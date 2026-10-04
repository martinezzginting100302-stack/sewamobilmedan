<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sewa Mobil Medan</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="SewaMobilMedan - Rental Mobil Medan">
    <meta property="og:description" content="Sewa mobil di Medan cepat, mudah, dan terpercaya.">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 20px;
        }
        .card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 12px;
            padding: 36px 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .35);
        }
        .brand {
            text-align: center;
            margin-bottom: 26px;
        }
        .brand .logo-img {
            width: 180px;
            max-width: 100%;
            height: auto;
            border-radius: 12px;
        }
        .brand h1 {
            margin: 10px 0 4px;
            font-size: 22px;
            color: #0f172a;
        }
        .brand p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 18px;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 18px;
        }
        .alert-error ul { margin: 0; padding-left: 18px; }
        .alert-error li { margin: 3px 0; }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
        }
        .form-control {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
        }
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            background: #2563eb;
            color: #fff;
            transition: background .15s;
        }
        .btn:hover { background: #1d4ed8; }
        .remember-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            font-size: 13.5px;
            color: #475569;
        }
        .remember-row a { color: #2563eb; text-decoration: none; }
        .password-wrap { position: relative; }
        .password-wrap .form-control { padding-right: 46px; }
        .toggle-password {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            padding: 6px 8px;
            border-radius: 6px;
            color: #64748b;
        }
        .toggle-password:hover { background: #f1f5f9; color: #0f172a; }
        .alt {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #64748b;
        }
        .alt a { color: #2563eb; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <img src="{{ asset('logo.png') }}" alt="SewaMobilMedan" class="logo-img">
            <h1>SewaMobilMedan</h1>
            <p>Solusi Perjalanan Anda — Masuk untuk mengelola rental mobil</p>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       class="form-control"
                       placeholder="nama@contoh.com"
                       autofocus required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password"
                           class="form-control"
                           placeholder="Masukkan password"
                           required>
                    <button type="button" class="toggle-password" data-target="password"
                            aria-label="Tampilkan password" title="Tampilkan/sembunyikan password">👁️</button>
                </div>
            </div>

            <div class="remember-row">
                <label style="display:flex;align-items:center;gap:6px;font-weight:400;">
                    <input type="checkbox" name="remember" value="1">
                    Ingat saya
                </label>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Daftar Akun</a>
                @endif
            </div>

            <button type="submit" class="btn">Masuk</button>
        </form>

        <div class="alt">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
    <script>
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var input = document.getElementById(btn.getAttribute('data-target'));
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.textContent = show ? '🙈' : '👁️';
                btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });
    </script>
</body>
</html>
