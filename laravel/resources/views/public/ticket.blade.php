<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Ticket - IT Helpdesk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2e6429 0%, #3a7d34 50%, #245520 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 580px;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #3a7d34, #245520);
            color: #fff;
            padding: 32px 40px 24px;
            text-align: center;
        }
        .header i { font-size: 40px; margin-bottom: 12px; opacity: 0.9; }
        .header h1 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
        .header p { font-size: 13px; opacity: 0.8; }
        .btn-track-link {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 16px; padding: 8px 18px;
            background: rgba(255,255,255,0.15);
            color: white; border: 1.5px solid rgba(255,255,255,0.4);
            border-radius: 20px; font-size: 12px; font-weight: 600;
            text-decoration: none; font-family: inherit;
            transition: all .2s; backdrop-filter: blur(4px);
        }
        .btn-track-link:hover { background: rgba(255,255,255,0.3); transform: translateY(-1px); }
        .body { padding: 32px 40px; }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #333; margin-bottom: 6px;
        }
        .form-group label span { color: #e74c3c; }
        .form-control {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid #ddd; border-radius: 8px;
            font-size: 13px; font-family: inherit;
            transition: border-color .2s, box-shadow .2s;
            background: #fafafa;
        }
        .form-control:focus {
            outline: none; border-color: #3a7d34;
            box-shadow: 0 0 0 3px rgba(58,125,52,.12);
            background: #fff;
        }
        textarea.form-control { resize: vertical; min-height: 90px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #3a7d34, #245520);
            color: #fff; border: none; border-radius: 8px;
            font-size: 15px; font-weight: 600; cursor: pointer;
            font-family: inherit; transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(58,125,52,.4); }
        .alert-success {
            background: #d4edda; color: #155724;
            border: 1px solid #c3e6cb; border-radius: 8px;
            padding: 14px 18px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
            font-size: 13px;
        }
        .alert-error {
            background: #f8d7da; color: #721c24;
            border: 1px solid #f5c6cb; border-radius: 8px;
            padding: 14px 18px; margin-bottom: 20px;
            font-size: 13px;
        }
        .alert-error ul { padding-left: 16px; margin: 4px 0 0; }
        .footer-note {
            text-align: center; font-size: 12px; color: #999;
            padding: 16px 40px 24px;
            border-top: 1px solid #f0f0f0;
        }
        @media(max-width: 500px) {
            .header { padding: 24px 20px; }
            .body { padding: 24px 20px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-headset"></i>
            <h1>IT Helpdesk Ticket</h1>
            <p>Laporkan masalah IT kamu di sini. Tim kami akan segera merespons.</p>
            <a href="{{ route('public.ticket.track') }}" class="btn-track-link">
                <i class="fas fa-search"></i> Sudah punya tiket? Cek status di sini
            </a>
        </div>
        <div class="body">

            @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle" style="font-size:18px"></i>
                <div>
                    <strong>Ticket berhasil dikirim!</strong><br>
                    {{ session('success') }}
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="alert-error">
                <strong><i class="fas fa-exclamation-circle"></i> Ada kesalahan:</strong>
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ route('public.ticket.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Nama Pelapor <span>*</span></label>
                    <input type="text" name="requester_name" class="form-control"
                        placeholder="Nama lengkap kamu"
                        value="{{ old('requester_name') }}" required>
                </div>
                <div class="form-group">
                    <label>Judul Masalah <span>*</span></label>
                    <input type="text" name="title" class="form-control"
                        placeholder="Contoh: Laptop tidak bisa nyala"
                        value="{{ old('title') }}" required>
                </div>
                <div class="form-group">
                    <label>Kategori Masalah <span>*</span></label>
                    <select name="category" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="maintenance" {{ old('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="procurement" {{ old('category') == 'procurement' ? 'selected' : '' }}>Procurement (Pengadaan)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lokasi <span>*</span></label>
                    <select name="location_id" class="form-control" required>
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $l)
                        <option value="{{ $l->id }}" {{ old('location_id') == $l->id ? 'selected' : '' }}>
                            {{ $l->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Deskripsi Masalah</label>
                    <textarea name="description" class="form-control"
                        placeholder="Jelaskan masalah secara detail...">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Foto (opsional)</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Kirim Laporan
                </button>
            </form>
        </div>
        <div class="footer-note">
            <i class="fas fa-shield-alt"></i>
            Laporan kamu akan langsung diterima oleh tim IT.
            &nbsp;·&nbsp;
            <a href="{{ route('public.ticket.track') }}" style="color:#3a7d34; text-decoration:none; font-weight:600;">
                <i class="fas fa-search"></i> Cek status tiket
            </a>
        </div>
    </div>
</body>
</html>
