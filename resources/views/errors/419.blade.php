<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
    <title>Sesi Kedaluwarsa - Mengalihkan...</title>
    <script>
        window.location.href = "{{ route('login') }}";
    </script>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        .card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
        }
        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.65rem 1.25rem;
            background-color: #0284c7;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="margin: 0 0 0.5rem 0; font-size: 1.25rem;">Sesi Anda Telah Berakhir</h2>
        <p style="color: #94a3b8; font-size: 0.875rem; margin: 0;">Sedang mengalihkan Anda kembali ke halaman login...</p>
        <a href="{{ route('login') }}" class="btn">Klik di Sini Jika Tidak Otomatis Beralih</a>
    </div>
</body>
</html>
