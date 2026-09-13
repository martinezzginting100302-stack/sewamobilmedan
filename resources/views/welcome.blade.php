<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SewaMobilMedan</title>
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
        .logo { font-size: 60px; }
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
        <div class="logo">🚗</div>
        <h1>SewaMobilMedan</h1>
        <p>Sistem Rental Mobil Medan</p>
        <a class="btn" href="{{ route('dashboard') }}">Buka Dashboard</a>
    </div>
</body>
</html>