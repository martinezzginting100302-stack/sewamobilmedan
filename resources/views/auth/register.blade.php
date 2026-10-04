<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sewa Mobil Medan</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <meta property="og:image" content="{{ asset('logo.png') }}">
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
            max-width: 430px;
            background: #fff;
            border-radius: 12px;
            padding: 36px 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .35);
        }
        .brand { text-align: center; margin-bottom: 26px; }
        .brand .logo-img { width: 180px; max-width: 100%; height: auto; border-radius: 12px; }
        .brand h1 { margin: 10px 0 4px; font-size: 22px; color: #0f172a; }
        .brand p { margin: 0; color: #64748b; font-size: 14px; }
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
            <p>Solusi Perjalanan Anda — Buat akun baru</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       class="form-control"
                       placeholder="Nama Anda"
                       autofocus required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       class="form-control"
                       placeholder="nama@contoh.com"
                       required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password"
                           class="form-control"
                           placeholder="Minimal 8 karakter"
                           required>
                    <button type="button" class="toggle-password" data-target="password"
                            aria-label="Tampilkan password" title="Tampilkan/sembunyikan password">👁️</button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="password-wrap">
                    <input type="password" id="password_confirmation"
                           name="password_confirmation"
                           class="form-control"
                           placeholder="Ulangi password"
                           required>
                    <button type="button" class="toggle-password" data-target="password_confirmation"
                            aria-label="Tampilkan password" title="Tampilkan/sembunyikan password">👁️</button>
                </div>
            </div>

            <button type="submit" class="btn">Daftar</button>
        </form>

        <div class="alt">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>
    <script>
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