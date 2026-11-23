<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Kios Sembako Azwa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-500: #22c55e;
            --green-600: #16a34a;
            --green-700: #15803d;
            --gray-100:  #f3f4f6;
            --gray-200:  #e5e7eb;
            --gray-300:  #d1d5db;
            --gray-400:  #9ca3af;
            --gray-500:  #6b7280;
            --gray-700:  #374151;
            --gray-800:  #1f2937;
            --gray-900:  #111827;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0fdf4;
            background-image:
                linear-gradient(rgba(34,197,94,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34,197,94,0.06) 1px, transparent 1px);
            background-size: 40px 40px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .login-card {
            background: white;
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 8px 40px rgba(0,0,0,0.10), 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(34,197,94,0.1);
        }

        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            background: var(--green-600);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            box-shadow: 0 4px 14px rgba(22,163,74,0.35);
        }

        .brand-name {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1.2;
            margin-bottom: 0.15rem;
        }

        .brand-name span {
            color: var(--green-600);
        }

        .brand-tagline {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-500);
            margin-bottom: 0.5rem;
        }

        .input-wrap {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 0.875rem;
            border: 1.5px solid var(--gray-200);
            border-radius: 0.5rem;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: var(--gray-800);
            background: var(--gray-100);
            transition: all 0.2s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--green-500);
            background: white;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .form-control::placeholder { color: var(--gray-400); }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            font-size: 1rem;
            display: flex;
            align-items: center;
            padding: 0;
            transition: color 0.15s;
        }

        .password-toggle:hover { color: var(--gray-700); }

        .error-msg {
            font-size: 0.78rem;
            color: #ef4444;
            margin-top: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: var(--green-600);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: all 0.2s ease;
            letter-spacing: 0.02em;
        }

        .btn-login:hover {
            background: var(--green-700);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(22,163,74,0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-hint {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--gray-100);
        }

        .hint-title {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.625rem;
        }

        .hint-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            padding: 0.3rem 0;
            color: var(--gray-600);
        }

        .hint-row code {
            background: var(--gray-100);
            padding: 0.1rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            color: var(--green-700);
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon">KSA</div>
            <div class="brand-name">Kios Sembako <span>Azwa</span></div>
            <div class="brand-tagline">Sistem Manajemen Inventori</div>
        </div>

        <form method="POST" action="{{ route('login.process') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-wrap">
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}"
                        value="{{ old('username') }}"
                        placeholder="ID"
                        autocomplete="username"
                        autofocus
                        required
                    >
                </div>
                @error('username')
                <div class="error-msg">
                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="bi bi-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">Masuk ke Sistem</button>
        </form>


    </div>
</div>

<script>
    function togglePassword() {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            pwd.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
</script>

</body>
</html>