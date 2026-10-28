<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:2|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4|max:10|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        auth()->login($user);

        return match ($user->role) {
            'hr' => redirect()->route('hr.dashboard'),
            'supervisor' => redirect()->route('supervisor.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard.intern'),
        };
    }

    /**
     * Authenticate user credentials, generate and send email OTP, then redirect to OTP verification.
     */
    public function login(Request $request, OtpService $otpService): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Clear any previous active session so OTP verification is not blocked by guest middleware
        if (Auth::check()) {
            Auth::logout();
        }

        // Credentials are valid: Send OTP code to user's email for verification
        $otpService->sendOtp($user);

        // Store temporary OTP session state
        $request->session()->put('otp_user_id', $user->id);
        $request->session()->put('otp_email', $user->email);

        return redirect()->route('otp.verify.form')
            ->with('status', 'We have sent a 6-digit verification code to your email. Please enter it below to complete your login.');
    }
}
