<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - Green City Resort 2</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.292.0/dist/umd/lucide.min.js"></script>
    <style>
        :root {
            --primary: #166534;
            --primary-light: #22c55e;
            --bg: #f8faf9;
            --white: #ffffff;
            --text: #1a1a2e;
            --text-secondary: #6b7280;
            --radius: 24px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }

        .container {
            max-width: 500px;
            width: 100%;
        }

        .illustration {
            width: 120px;
            height: 120px;
            background: var(--white);
            border-radius: var(--radius);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: var(--primary);
            color: var(--white);
            text-decoration: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
            background: var(--primary-dark);
            box-shadow: 0 8px 20px rgba(22, 101, 52, 0.3);
        }

        footer {
            position: fixed;
            bottom: 2rem;
            font-size: 0.82rem;
            color: var(--text-secondary);
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="illustration">
            <i data-lucide="map-pin-off" style="width:48px; height:48px;"></i>
        </div>
        
        <h1>404</h1>
        <p>Aduh! Halaman yang Anda cari tidak dapat ditemukan. Mungkin sudah dipindahkan atau dihapus.</p>
        
        <a href="/" class="btn">
            <i data-lucide="arrow-left" style="width:18px; height:18px;"></i>
            Kembali ke Dashboard
        </a>
    </div>

    <footer>
        &copy; {{ date('Y') }} Green City Resort 2
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
