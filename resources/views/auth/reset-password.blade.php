<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a New Password</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <h1>Choose a new password</h1>
    @include('partials.flash-notification')
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        <label for="password">New password:</label>
        <input type="password" id="password" name="password" required>
        <label for="password_confirmation">Confirm new password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
        <button type="submit">Reset password</button>
    </form>
</body>
</html>
