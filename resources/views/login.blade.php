<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Judiciary Internship Management Portal</title>
    @vite(['resources/css/app.css'])
</head>
<body class="login-page">
    <h1>Login</h1>
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
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required autofocus>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="******" required>
        </div>

        <div>
            <a href="{{ route('password.request') }}">Forgot Password?</a><br>
            <p style="margin: 0.5rem 0 0.75rem 0; font-size: 0.95rem;">Don't have an account? <a href="{{ route('register') }}">Register Now</a></p>
            <button type="submit">Log in</button>
        </div>
    </form>
</body>
</html>
