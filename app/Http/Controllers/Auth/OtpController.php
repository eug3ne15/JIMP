<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Display the form to enter and verify the OTP code.
     */
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('otp_user_id');
        $email = $request->session()->get('otp_email');

        if (! $userId || ! ($user = User::find($userId))) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Please enter your email and password to log in.']);
        }

        return view('auth.otp-verify', [
            'email' => $email ?? $user->email,
        ]);
    }

    /**
     * Verify the submitted OTP and complete user login.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'code.regex' => 'The verification code must be exactly 6 digits.',
            'code.size' => 'The verification code must be exactly 6 digits.',
        ]);

        $userId = $request->session()->get('otp_user_id');

        if (! $userId || ! ($user = User::find($userId))) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Your login session has expired. Please enter your credentials again.']);
        }

        // Limit to 5 failed verification attempts per 10 minutes
        $throttleKey = "otp_verify_attempts_{$user->id}";
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'code' => "Too many failed attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (! $this->otpService->verifyOtp($user, $request->code)) {
            RateLimiter::hit($throttleKey, 600);

            throw ValidationException::withMessages([
                'code' => 'The verification code is invalid or has expired.',
            ]);
        }

        // Clear rate limiter and temporary session keys
        RateLimiter::clear($throttleKey);
        $request->session()->forget(['otp_user_id', 'otp_email']);

        Auth::login($user);
        $request->session()->regenerate();

        return match ($user->role) {
            'hr' => redirect()->intended(route('hr.dashboard')),
            'supervisor' => redirect()->intended(route('supervisor.dashboard')),
            'admin' => redirect()->intended(route('admin.dashboard')),
            default => redirect()->intended(route('dashboard.intern')),
        };
    }

    /**
     * Resend an OTP code to the user.
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('otp_user_id');

        if ($userId && ($user = User::find($userId))) {
            $throttleKey = "otp_resend_{$user->id}";
            if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
                $seconds = RateLimiter::availableIn($throttleKey);

                return back()->withErrors(['code' => "Please wait {$seconds} seconds before requesting another code."]);
            }

            RateLimiter::hit($throttleKey, 60);
            $this->otpService->sendOtp($user);

            return back()->with('status', 'A new 6-digit verification code has been sent to your email.');
        }

        return redirect()->route('login');
    }
}
