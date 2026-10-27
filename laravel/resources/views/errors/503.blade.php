<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Diperbarui - Green City Resort 2</title>
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
            color: var(--primary);
            box-shadow: 0 10px 25px rgba(22, 101, 52, 0.08);
            margin-bottom: 2rem;
            position: relative;
        }

        .pulse {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: var(--radius);
            background: var(--primary-light);
            opacity: 0.2;
            z-index: -1;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.3; }
            100% { transform: scale(1.4); opacity: 0; }
        }

        h1 {
            font-size: 2rem;
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

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: 1px solid rgba(22, 101, 52, 0.1);
        }

        .dot {
            width: 8px;
            height: 8px;
            background: var(--primary-light);
            border-radius: 50%;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
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
            <div class="pulse"></div>
            <i data-lucide="wrench" style="width:48px; height:48px;"></i>
        </div>
        
        <h1>Sedang Pemeliharaan</h1>
        <p>Kami sedang melakukan pemeliharaan rutin untuk meningkatkan layanan Iuran Warga. Mohon tunggu sebentar, kami akan segera kembali!</p>
        
        <div class="status-badge">
            <div class="dot"></div>
            Progress: Pembersihan Data & Optimasi
        </div>
    </div>

    <footer>
        &copy; {{ date('Y') }} Green City Resort 2
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
