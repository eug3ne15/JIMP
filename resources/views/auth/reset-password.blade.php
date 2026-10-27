<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a New Password</title>
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
