<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link to user email.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Display password reset view with the email recognized from the database.
     */
    public function showResetForm(Request $request, string $token): View|RedirectResponse
    {
        $email = $this->findEmailForToken($token, $request->query('email'));

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => __('This password reset link is invalid or has expired. Please request a new one.')]);
        }

        $user = User::where('email', $email)->first();

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
            'userName' => $user?->name,
        ]);
    }

    /**
     * Reset password ensuring the email is recognized directly from the database token.
     */
    public function reset(Request $request): RedirectResponse
    {
        $token = (string) $request->input('token');

        // Recognize the account email strictly from the database token to prevent changing another person's password
        $email = $this->findEmailForToken($token, $request->input('email'));

        if (! $email) {
            return back()->withErrors(['email' => [__('This password reset link is invalid or has expired. Please request a new one.')]]);
        }

        // Force the verified database email into the request parameters
        $request->merge(['email' => $email]);

        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:4', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Find the email associated with the given reset token from the database.
     */
    protected function findEmailForToken(string $token, ?string $hintEmail = null): ?string
    {
        if (empty($token)) {
            return null;
        }

        $expireMinutes = (int) config('auth.passwords.users.expire', 60);
        $cutoff = now()->subMinutes($expireMinutes);

        // 1. If hint email is provided (query parameter or session), check that record first
        if ($hintEmail) {
            $record = DB::table('password_reset_tokens')
                ->where('email', $hintEmail)
                ->where('created_at', '>=', $cutoff)
                ->first();

            if ($record && ($record->token === $token || Hash::check($token, $record->token))) {
                return $record->email;
            }
        }

        // 2. Otherwise scan active unexpired tokens to match the hashed or direct token
        $records = DB::table('password_reset_tokens')
            ->where('created_at', '>=', $cutoff)
            ->get();

        foreach ($records as $record) {
            if ($record->token === $token || Hash::check($token, $record->token)) {
                return $record->email;
            }
        }

        return null;
    }
}
