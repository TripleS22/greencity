<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Barcode Labels</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size:11px; }

    .location-section { page-break-after:always; }
    .location-section:last-child { page-break-after:avoid; }

    .barcode-grid {
        display:flex; flex-wrap:wrap; gap:0;
    }
    .barcode-item {
        width:33.33%; padding:8px; text-align:center;
        border:1px dashed #ccc; page-break-inside:avoid;
    }
    .barcode-item .code {
        font-size:14px; font-weight:bold; font-family:'Courier New', monospace;
        letter-spacing:4px; margin-top:6px;
    }
    .barcode-item .asset-info {
        font-size:11px; color:#555; margin-top:2px;
    }
    .barcode-item img { max-width:55px; height:55px; margin-bottom:4px; }
</style>
</head>
<body>
@foreach($assets as $locationName => $locationAssets)
    <div class="location-section">
        <div class="barcode-grid">
            @foreach($locationAssets as $asset)
                <div class="barcode-item">
                    @php
                       $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->margin(0)->generate($asset->code_asset);
                       $base64Qr = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
                    @endphp
                    <img src="{{ $base64Qr }}" alt="qrcode">
                    <div class="code">{{ implode(' ', str_split($asset->code_asset)) }}</div>
                    <div class="asset-info">{{ $asset->deviceModel->name ?? 'Tanpa Model' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
</body>
</html>
