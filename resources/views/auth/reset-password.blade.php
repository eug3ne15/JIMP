<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a New Password | Judiciary Internship Management Portal</title>
    @vite(['resources/css/app.css'])
</head>
<body class="login-page">
    <h1>Choose a new password</h1>
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

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        {{-- Recognized Account Display (No email input needed) --}}
        <div class="reset-account-box">
            <span class="reset-account-label">Resetting password for:</span>
            <div class="reset-account-pill">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <span class="reset-account-email">{{ $email }}</span>
                @if (!empty($userName))
                    <span class="reset-account-name">({{ $userName }})</span>
                @endif
            </div>
            <small class="reset-security-note">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Account verified from database via secure reset token.
            </small>
        </div>

        <div>
            <label for="password">New password:</label>
            <input type="password" id="password" name="password" placeholder="Enter new password" required autofocus>
        </div>

        <div>
            <label for="password_confirmation">Confirm new password:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required>
        </div>

        <div>
            <button type="submit">Reset password</button>
            <p style="margin-top: 0.75rem; font-size: 0.95rem; justify-content: center;">
                <a href="{{ route('login') }}">Back to login</a>
            </p>
        </div>
    </form>
</body>
</html>
