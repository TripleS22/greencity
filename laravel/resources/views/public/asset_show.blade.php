<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Information | IHAMS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary:#4a8a3c; --bg:#f4f7f6; --text:#333; --card:#fff; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Roboto,sans-serif; background:var(--bg); color:var(--text); line-height:1.6; display:flex; flex-direction:column; min-height:100vh; }
        .header { background:var(--primary); color:#fff; padding:15px 20px; text-align:center; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
        .header h1 { font-size:1.2rem; margin:0; font-weight:600; }
        .container { flex:1; max-width:600px; width:100%; margin:20px auto; padding:0 20px; }

        .card { background:var(--card); border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:20px; }
        .card-header { padding:20px; text-align:center; border-bottom:1px solid #eee; background:#fafafa; }
        .asset-icon { font-size:3rem; color:var(--primary); margin-bottom:10px; }
        .asset-code { font-size:1.5rem; font-weight:bold; letter-spacing:2px; font-family:monospace; color:#222; }
        .asset-type { display:inline-block; padding:4px 12px; background:#e8f5e9; color:var(--primary); border-radius:20px; font-size:0.85rem; font-weight:600; margin-top:10px; }

        /* ── Photo Container ──────────── */
        .photo-container {
            width: 100%;
            background: #111;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            max-height: 320px;
            cursor: zoom-in;
        }
        .photo-container img {
            width: 100%;
            height: 100%;
            max-height: 320px;
            object-fit: contain;
            display: block;
            transition: transform 0.2s ease;
        }
        .photo-container:hover img { transform: scale(1.02); }
        .photo-zoom-hint {
            position: absolute;
            bottom: 8px;
            right: 10px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 20px;
            pointer-events: none;
        }
        .photo-placeholder {
            color: #555;
            font-size: 3rem;
            padding: 48px 0;
        }

        /* ── Lightbox ──────────────────── */
        #lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.92);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        #lightbox.open { display: flex; }
        #lightbox img {
            max-width: 100%;
            max-height: 90vh;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
            object-fit: contain;
        }
        #lightbox-close {
            position: absolute;
            top: 16px;
            right: 20px;
            color: #fff;
            font-size: 1.8rem;
            cursor: pointer;
            line-height: 1;
            opacity: 0.85;
            background: none;
            border: none;
        }
        #lightbox-close:hover { opacity: 1; }

        /* ── Info List ─────────────────── */
        .info-list { padding:0; list-style:none; }
        .info-item { display:flex; padding:15px 20px; border-bottom:1px solid #f0f0f0; }
        .info-item:last-child { border-bottom:none; }
        .info-label { width:40%; color:#777; font-size:0.9rem; font-weight:500; }
        .info-value { width:60%; font-weight:600; color:#333; }

        /* ── Kode Akses badge ──────────── */
        .access-code-badge {
            display: inline-block;
            background: #1a1a2e;
            color: #00d4aa;
            font-family: 'Courier New', monospace;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 2px;
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid #00d4aa44;
        }

        .footer { text-align:center; padding:20px; color:#888; font-size:0.85rem; }
        .btn-search { display:block; width:100%; padding:15px; background:var(--primary); color:#fff; text-align:center; text-decoration:none; border-radius:8px; font-weight:bold; margin-top:20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1><i class="fas fa-server"></i> IHAMS Public Viewer</h1>
    </div>

    <div class="container">
        <div class="card">
            {{-- ── Photo ─────────────────────── --}}
            @if($asset->photo)
                <div class="photo-container" id="photo-trigger" title="Klik untuk lihat penuh">
                    <img src="{{ asset('storage/' . $asset->photo) }}" alt="Asset Photo" id="asset-photo">
                    <span class="photo-zoom-hint"><i class="fas fa-search-plus"></i> Lihat penuh</span>
                </div>
            @else
                <div class="photo-container" style="cursor:default;">
                    <i class="fas fa-image photo-placeholder"></i>
                </div>
            @endif

            <div class="card-header">
                <div class="asset-icon"><i class="{{ \App\Models\Asset::TYPE_ICONS[$asset->asset_type] ?? 'fas fa-box' }}"></i></div>
                <div class="asset-code">{{ $asset->code_asset }}</div>
                <div class="asset-type">{{ \App\Models\Asset::TYPES[$asset->asset_type] ?? ucfirst($asset->asset_type) }}</div>
            </div>

            <ul class="info-list">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-building fa-fw"></i> Lokasi</div>
                    <div class="info-value">{{ $asset->location->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-user fa-fw"></i> Pengguna</div>
                    <div class="info-value">{{ $asset->employee->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-users fa-fw"></i> Group</div>
                    <div class="info-value">{{ $asset->group->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-tag fa-fw"></i> Brand & Model</div>
                    <div class="info-value">{{ $asset->brand->name ?? '-' }} {{ $asset->deviceModel->name ?? '' }}</div>
                </div>

                @if($asset->ram || $asset->rom || $asset->cpu)
                <div class="info-item" style="background:#fafafa">
                    <div class="info-label"><i class="fas fa-microchip fa-fw"></i> Spesifikasi</div>
                    <div class="info-value" style="font-size:0.85rem; font-weight:normal">
                        @if($asset->cpu) <div><strong>CPU:</strong> {{ $asset->cpu->spec }}</div> @endif
                        @if($asset->ram) <div><strong>RAM:</strong> {{ $asset->ram->spec }}</div> @endif
                        @if($asset->rom) <div><strong>Storage:</strong> {{ $asset->rom->spec }}</div> @endif
                        @if($asset->vga) <div><strong>VGA:</strong> {{ $asset->vga->spec }}</div> @endif
                    </div>
                </div>
                @endif

                @if($asset->description)
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-align-left fa-fw"></i> Catatan</div>
                    <div class="info-value" style="font-weight:normal;font-size:0.9rem">{{ $asset->description }}</div>
                </div>
                @endif

                {{-- ── Kode Akses: hanya tampil untuk CCTV ──────────── --}}
                @if($asset->asset_type === 'cctv' && $asset->access_code)
                <div class="info-item" style="background:#f0fff8">
                    <div class="info-label"><i class="fas fa-key fa-fw" style="color:#00a884"></i> Kode Akses</div>
                    <div class="info-value">
                        <span class="access-code-badge">{{ $asset->access_code }}</span>
                    </div>
                </div>
                @endif
            </ul>
        </div>

        <a href="{{ route('public.asset.search') }}" class="btn-search"><i class="fas fa-search"></i> Scan Aset Lainnya</a>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} IHAMS - IT Asset Management
    </div>

    {{-- ── Lightbox ──────────────────────────────────── --}}
    @if($asset->photo)
    <div id="lightbox">
        <button id="lightbox-close" onclick="closeLightbox()" title="Tutup">&times;</button>
        <img src="{{ asset('storage/' . $asset->photo) }}" alt="Asset Photo Full">
    </div>

    <script>
        document.getElementById('photo-trigger').addEventListener('click', function () {
            document.getElementById('lightbox').classList.add('open');
        });
        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('open');
        }
        // Klik area gelap untuk tutup
        document.getElementById('lightbox').addEventListener('click', function (e) {
            if (e.target === this) closeLightbox();
        });
        // ESC untuk tutup
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeLightbox();
        });
    </script>
    @endif

</body>
</html>
