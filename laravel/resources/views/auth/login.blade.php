<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Iuran Warga</title>
    <link rel="icon" type="image/png" href="{{ asset('logogcr.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.292.0/dist/umd/lucide.min.js"></script>
    <style>
        :root {
            --primary: #166534;
            --primary-light: #22c55e;
            --primary-lighter: #dcfce7;
            --primary-lightest: #f0fdf4;
            --primary-dark: #14532d;
            --bg: #f8faf9;
            --white: #ffffff;
            --text: #1a1a2e;
            --text-secondary: #6b7280;
            --border: #e5e7eb;
            --red: #ef4444;
            --red-light: #fef2f2;
            --shadow-lg: 0 10px 40px -8px rgb(0 0 0 / 0.15);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(34,197,94,0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(22,101,52,0.06) 0%, transparent 50%);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        /* Brand */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin-bottom: 0.75rem;
            box-shadow: 0 8px 24px rgba(22,101,52,0.3);
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-6px); }
        }
        .brand h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: -0.5px;
        }
        .brand p {
            font-size: 0.88rem;
            color: var(--text-secondary);
            margin-top: 0.2rem;
        }

        /* Card */
        .login-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(34,197,94,0.1);
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-dark), var(--primary-light));
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.25rem;
        }
        .card-subtitle {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 1.75rem;
        }

        /* Alert */
        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-error {
            background: var(--red-light);
            color: var(--red);
            border: 1px solid rgba(239,68,68,0.2);
        }
        .alert-info {
            background: var(--primary-lightest);
            color: var(--primary);
            border: 1px solid rgba(34,197,94,0.2);
        }

        /* Form */
        .form-group { margin-bottom: 1.1rem; }
        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.4rem;
        }
        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--text);
            background: var(--white);
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(34,197,94,0.12);
        }
        .form-input.is-invalid {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }
        .invalid-feedback {
            font-size: 0.78rem;
            color: var(--red);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Toggle password */
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 2px;
            transition: color 0.2s;
        }
        .toggle-password:hover { color: var(--primary); }

        /* Remember */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.5rem;
        }
        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .remember-row label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            cursor: pointer;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(22,101,52,0.25);
            letter-spacing: 0.2px;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(22,101,52,0.35);
        }
        .btn-login:active { transform: translateY(0); }

        /* Back link */
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .back-link a {
            font-size: 0.85rem;
            color: var(--text-secondary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .back-link a:hover { color: var(--primary); }
    </style>
</head>
<body>
    <div class="login-wrapper">

        {{-- Brand --}}
        <div class="brand">
            <div style="margin-bottom: 1rem;">
                <img src="{{ asset('logogcr.png') }}" alt="Logo" style="height:70px; width:auto; object-fit:contain;">
            </div>
            <h1>Iuran Warga</h1>
            <p>Portal Admin RT</p>
        </div>

        {{-- Card --}}
        <div class="login-card">
            <div class="card-title">Selamat Datang 👋</div>
            <div class="card-subtitle">Masuk untuk mengelola warga dan iuran</div>

            {{-- Error / Info flash --}}
            @if (session('error'))
                <div class="alert alert-error">
                    <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->has('email') && !$errors->has('password'))
                <div class="alert alert-error">
                    <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0"></i>
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email Admin</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i data-lucide="mail" style="width:16px;height:16px"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="admin@iuranwarga.com"
                            required
                            autofocus
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i data-lucide="lock" style="width:16px;height:16px"></i>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="••••••••"
                            required
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()" title="Tampilkan password">
                            <i data-lucide="eye" id="eye-icon" style="width:16px;height:16px"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember --}}
                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Ingat saya di perangkat ini</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login">
                    <i data-lucide="log-in" style="width:18px;height:18px"></i>
                    Masuk sebagai Admin
                </button>
            </form>
        </div>

        {{-- Back --}}
        <div class="back-link">
            <a href="{{ route('dashboard') }}">
                <i data-lucide="arrow-left" style="width:15px;height:15px"></i>
                Kembali ke Dashboard
            </a>
        </div>

    </div>

    <script>
        lucide.createIcons();

        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
