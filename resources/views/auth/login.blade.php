<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;900&display=swap" rel="stylesheet"> <!-- Tambah weight 900 -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            overflow: hidden;
        }

        .bg-image {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('images/bg-4.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem; /* Dikurangi dari 2.5rem */
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.18);
            width: 100%;
            max-width: 360px; /* Dikurangi dari 420px */
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem; /* Dikurangi dari 2rem */
        }

        .logo img {
            height: 70px; /* Dikurangi dari 80px */
            width: auto;
            margin-bottom: 0.75rem; /* Dikurangi dari 1rem */
        }

        .logo-text {
            text-align: center;
            position: relative;
            padding: 0.5rem 0;
        }

        /* Updated styles for the system name */
        .logo-subtitle {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.6rem;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 0 0 10px rgba(0, 221, 235, 0.5);
            position: relative;
            line-height: 1.2;
        }

        .logo-subtitle span {
            display: block;
        }

        .logo-subtitle .system-text {
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 4px;
            color: #00ddeb;
            margin-top: 0.25rem;
            text-shadow: 0 0 8px rgba(0, 221, 235, 0.3);
        }

        .input-label {
            display: block;
            margin-bottom: 0.4rem; /* Dikurangi dari 0.5rem */
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            color: #ffffff;
        }

        .text-input {
            width: 100%;
            padding: 0.65rem; /* Dikurangi dari 0.85rem */
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            margin-bottom: 1rem; /* Dikurangi dari 1.25rem */
            font-family: 'Poppins', sans-serif;
        }

        .text-input:focus {
            outline: none;
            border-color: #00ddeb;
            box-shadow: 0 0 0 3px rgba(0, 221, 235, 0.3);
        }

        .primary-button {
            background: linear-gradient(90deg, #007bff, #00ddeb);
            color: white;
            padding: 0.65rem; /* Dikurangi dari 0.85rem */
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            transition: transform 0.2s ease;
        }

        .primary-button:hover {
            transform: translateY(-2px);
            background: linear-gradient(90deg, #0056b3, #00b7c3);
        }

        .error-card {
            background: rgba(229, 62, 62, 0.1);
            border-left: 4px solid #e53e3e;
            border-radius: 6px;
            padding: 0.75rem; /* Dikurangi dari 1rem */
            margin-bottom: 1rem; /* Dikurangi dari 1.5rem */
            color: #fff;
        }

        .error-card-title {
            font-family: 'Poppins', sans-serif;
            color: #e53e3e;
            font-weight: 600;
            font-size: 0.85rem; /* Dikurangi dari 0.9rem */
            margin-bottom: 0.4rem; /* Dikurangi dari 0.5rem */
            display: flex;
            align-items: center;
        }

        .error-icon {
            margin-right: 0.5rem;
            width: 16px;
            height: 16px;
        }

        .error-message {
            font-family: 'Poppins', sans-serif;
            color: #ffcccc;
            font-size: 0.8rem; /* Dikurangi dari 0.875rem */
        }

        .input-error {
            color: #e53e3e;
            font-size: 0.8rem; /* Dikurangi dari 0.875rem */
            margin-top: 0.2rem; /* Dikurangi dari 0.25rem */
        }

        .has-error {
            border-color: #e53e3e;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1rem; /* Dikurangi dari 1.5rem */
        }

        .remember-me input {
            margin-right: 0.5rem;
        }

        .remember-me span {
            font-family: 'Poppins', sans-serif;
            font-size: 0.8rem; /* Dikurangi dari 0.875rem */
            color: #ffffff;
        }

        .success-card {
            background: rgba(56, 161, 105, 0.1);
            border-left: 4px solid #38a169;
            border-radius: 6px;
            padding: 0.75rem; /* Dikurangi dari 1rem */
            margin-bottom: 1rem; /* Dikurangi dari 1.5rem */
        }

        .success-card-title {
            font-family: 'Poppins', sans-serif;
            color: #38a169;
            font-weight: 600;
            font-size: 0.85rem; /* Dikurangi dari 0.9rem */
            margin-bottom: 0.4rem; /* Dikurangi dari 0.5rem */
            display: flex;
            align-items: center;
        }

        .success-icon {
            margin-right: 0.5rem;
            width: 16px;
            height: 16px;
        }

        .success-message {
            font-family: 'Poppins', sans-serif;
            color: #c6f6d5;
            font-size: 0.8rem; /* Dikurangi dari 0.875rem */
        }
    </style>
</head>
<body class="bg-image">
    <div class="login-container">
        <!-- Logo -->
        <div class="logo-container">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Warehouse">
            </div>
            <div class="logo-text">
                <p class="logo-subtitle">
                    <span>Step Stock</span>
                    <span class="system-text">SYSTEM</span>
                </p>
            </div>
        </div>

        <!-- Session Status (Success) -->
        @if (session('status'))
            <div class="success-card">
                <div class="success-card-title">
                    <svg class="success-icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Berhasil
                </div>
                <div class="success-message">{{ session('status') }}</div>
            </div>
        @endif

        <!-- Form Error Messages (if any) -->
        @if ($errors->any())
            <div class="error-card">
                <div class="error-card-title">
                    <svg class="error-icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Login Gagal
                </div>
                <div class="error-message">Username atau password salah. Silakan coba lagi.</div>
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username -->
            <div>
                <label for="username" class="input-label">Username</label>
                <input id="username" class="text-input {{ $errors->has('username') ? 'has-error' : '' }}" type="text" name="username" value="{{ old('username') }}" required autofocus>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="input-label">Password</label>
                <input id="password" class="text-input {{ $errors->has('password') ? 'has-error' : '' }}" type="password" name="password" required>
            </div>

            <!-- Remember Me -->
            <div class="remember-me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Remember me</span>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="primary-button">Login</button>
            </div>
        </form>
    </div>
</body>
</html>