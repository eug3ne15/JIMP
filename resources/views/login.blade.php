<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    @vite(['resources/css/app.css'])
</head>
<body class="login-page">
    <h1>Login</h1>
    @include('partials.flash-notification')
    @if ($errors->any())
        <div>
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
            <input type="email" id="email" name="email" placeholder="john@example.com"required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="******" required>
        </div>
        <div>
            <a href="{{ route('password.request') }}">Forgot Password?</a><br>
            <p>Don't have an account? <a href="{{ route('register') }}">Register Now</a></p>
            <button type="submit">Login</button>
        </div>
    </form>
</body>
</html>
