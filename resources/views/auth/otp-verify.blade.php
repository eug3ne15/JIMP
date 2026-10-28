<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Verification Code | Judiciary Internship Management Portal</title>
    @vite(['resources/css/app.css'])
</head>
<body class="login-page">
    <h1>Verify Code</h1>
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

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf

        <div class="otp-intro-card">
            <div class="otp-intro-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div>
                <strong style="color: #14532d; font-size: 0.95rem;">Two-Factor Authentication</strong>
                <p style="margin: 0.2rem 0 0; font-size: 0.85rem; color: #475569; line-height: 1.35;">
                    Your password was verified! We sent a 6-digit verification code to
                    @if (!empty($email))
                        <strong>{{ $email }}</strong>
                    @else
                        your email address
                    @endif. Please enter it below to complete your login.
                </p>
            </div>
        </div>

        <div>
            <label for="code">6-Digit Verification Code:</label>
            <input
                type="text"
                id="code"
                name="code"
                class="otp-input-box"
                maxlength="6"
                pattern="[0-9]{6}"
                inputmode="numeric"
                autocomplete="one-time-code"
                placeholder="123456"
                required
                autofocus
            >
            <small style="color: #64748b; font-size: 0.78rem;">Code expires in 10 minutes</small>
        </div>

        <button type="submit" style="margin-top: 0.5rem;">Verify &amp; Complete Login</button>
    </form>

    <div style="margin-top: 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; font-size: 0.88rem;">
        <form method="POST" action="{{ route('otp.resend') }}" style="background: none; border: none; box-shadow: none; padding: 0; width: auto; margin: 0;">
            @csrf
            <span>Didn't receive the code? </span>
            <button type="submit" style="background: none; border: none; color: var(--judiciary-green, #025532); text-decoration: underline; cursor: pointer; padding: 0; font: inherit; font-weight: 600;">
                Resend Code
            </button>
        </form>
        <a href="{{ route('login') }}" style="color: #64748b;">Back to Login</a>
    </div>
</body>
</html>
