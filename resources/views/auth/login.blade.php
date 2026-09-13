<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sewa Mobil Medan</title>
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
        .brand .logo {
            font-size: 40px;
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
            <div class="logo">🚗</div>
            <h1>SewaMobilMedan</h1>
            <p>Masuk untuk mengelola rental mobil</p>
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
                <input type="password" id="password" name="password"
                       class="form-control"
                       placeholder="Masukkan password"
                       required>
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