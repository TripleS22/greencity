<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Barcode Aset | IHAMS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary:#4a8a3c; --bg:#f4f7f6; --text:#333; --card:#fff; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Roboto,sans-serif; background:var(--bg); color:var(--text); line-height:1.6; display:flex; flex-direction:column; min-height:100vh; align-items:center; justify-content:center; }
        
        .container { max-width:400px; width:100%; padding:20px; }
        .card { background:var(--card); border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.08); overflow:hidden; padding:40px 30px; text-align:center; }
        
        .icon { font-size:4rem; color:var(--primary); margin-bottom:20px; }
        h1 { font-size:1.5rem; margin-bottom:10px; color:#2c3e50; }
        p { color:#777; margin-bottom:30px; font-size:0.95rem; }
        
        .form-group { margin-bottom:20px; text-align:left; }
        .form-control { width:100%; padding:14px; border:2px solid #e0e0e0; border-radius:8px; font-size:1.2rem; text-align:center; font-family:monospace; letter-spacing:2px; transition:border-color 0.3s; }
        .form-control:focus { outline:none; border-color:var(--primary); }
        
        .btn { display:block; width:100%; padding:14px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-weight:bold; font-size:1.1rem; cursor:pointer; transition:background 0.3s; }
        .btn:hover { background:#3a7d34; }
        
        .alert { padding:15px; background:#fadbd8; color:#c0392b; border-radius:8px; margin-bottom:20px; font-size:0.9rem; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <div class="icon"><i class="fas fa-qrcode"></i></div>
            <h1>Pencarian Aset</h1>
            <p>Scan QR code atau masukkan kode aset (9 digit) untuk melihat detail informasi.</p>

            @if(session('error'))
                <div class="alert"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
            @endif

            <div id="reader-container" style="display:none; margin-bottom:20px;">
                <div id="reader" style="width:100%"></div>
                <button type="button" class="btn btn-secondary mt-3" onclick="stopScanner()" style="background:#7f8c8d; margin-top:10px;"><i class="fas fa-times"></i> Tutup Kamera</button>
            </div>

            <form action="{{ url('public/asset/search') }}" method="POST" id="searchForm">
                @csrf
                <div class="form-group">
                    <input type="text" name="code_asset" id="code_asset" class="form-control" placeholder="000 000 000" value="{{ old('code_asset') }}" required autofocus autocomplete="off">
                </div>
                <button type="submit" class="btn" style="margin-bottom:10px;"><i class="fas fa-search"></i> Cari Aset</button>
                <button type="button" class="btn btn-outline" onclick="startScanner()" style="background:#fff; color:var(--primary); border:2px solid var(--primary);"><i class="fas fa-qrcode"></i> Scan QR dengan Kamera HP</button>
            </form>
        </div>
    </div>

    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5QrcodeScanner = null;

        function startScanner() {
            document.getElementById('reader-container').style.display = 'block';
            
            if (!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    { fps: 10, qrbox: {width: 250, height: 250}, formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ] },
                    /* verbose= */ false
                );
            }

            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        function stopScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().then(() => {
                    document.getElementById('reader-container').style.display = 'none';
                }).catch(err => console.error(err));
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanning
            stopScanner();
            
            // Fill input and submit form automatically
            document.getElementById('code_asset').value = decodedText;
            document.getElementById('searchForm').submit();
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning
            // console.warn(`Code scan error = ${error}`);
        }
    </script>
</body>
</html>
