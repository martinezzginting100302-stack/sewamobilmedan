<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, private, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Logout Berhasil - Sewa Mobil Medan</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
            text-align: center;
            max-width: 440px;
        }
        h1 { margin: 0 0 12px; font-size: 24px; color: #1e293b; }
        p { margin: 0 0 24px; color: #475569; line-height: 1.5; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }
        .btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Logout Berhasil</h1>
        <p>Sesi Anda telah berakhir. Silakan login kembali untuk melanjutkan.</p>
        <a href="{{ route('login') }}" class="btn" id="loginBtn">Login Kembali</a>
    </div>
    <script>
        setTimeout(function() {
            window.location.replace('{{ route('login') }}');
        }, 1500);
        document.getElementById('loginBtn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.replace('{{ route('login') }}');
        });
    </script>
</body>
</html>
