<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SewaMobilMedan - Solusi Perjalanan Anda</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .wrap { text-align: center; }
        .logo-img { width: 220px; max-width: 80vw; height: auto; border-radius: 16px; }
        h1 { margin: 12px 0 6px; }
        p { color: #94a3b8; margin: 0 0 24px; }
        a.btn {
            display: inline-block;
            padding: 12px 26px;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }
        a.btn:hover { background: #1d4ed8; }
    </style>
    <meta http-equiv="refresh" content="0; url={{ route('dashboard') }}">
</head>
<body>
    <div class="wrap">
        <img src="{{ asset('logo.png') }}" alt="SewaMobilMedan" class="logo-img">
        <h1>SewaMobilMedan</h1>
        <p>Solusi Perjalanan Anda</p>
        <a class="btn" href="{{ route('dashboard') }}">Buka Dashboard</a>
    </div>
</body>
</html>