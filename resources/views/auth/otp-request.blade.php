<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email OTP Login | Judiciary Internship Management Portal</title>
    @vite(['resources/css/app.css'])
</head>
<body class="login-page">
    <h1>Log in with OTP</h1>
    @include('partials.flash-notification')
    @if ($errors->any())
        <div class="hr-errors" style="max-width: 420px; width: 100%; margin-bottom: 1rem;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('otp.send') }}">
        @csrf

        <div class="otp-intro-card">
            <div class="otp-intro-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </div>
            <div>
                <strong style="color: #14532d; font-size: 0.95rem;">Passwordless Email Login</strong>
                <p style="margin: 0.2rem 0 0; font-size: 0.85rem; color: #475569; line-height: 1.35;">
                    Enter your registered email address and we'll send a 6-digit verification code to log in securely without typing your password.
                </p>
            </div>
        </div>

        <div>
            <label for="email">Registered Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@judiciary.go.ke" required autofocus>
        </div>

        <button type="submit" style="margin-top: 0.5rem;">Send Verification Code</button>

        <div style="text-align: center; margin-top: 0.75rem;">
            <a href="{{ route('login') }}" style="font-size: 0.9rem;">Back to Password Login</a>
        </div>
    </form>
</body>
</html>
