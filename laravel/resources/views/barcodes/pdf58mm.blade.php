<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Barcode Labels 58mm</title>
<style>
    /*
     * Ukuran kertas thermal 58mm.
     * Lebar cetak efektif ±52mm (setelah margin sisi).
     */
    @page {
        margin: 3mm;
        size: 58mm auto;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        width: 52mm;
        background: #fff;
        color: #000;
    }


    /* ── Satu label ────────────────────── */
    .label {
        width: 52mm;
        padding: 2mm 0;
        margin-bottom: 4mm; /* Jarak antar label */
        page-break-inside: avoid;
    }

    /* Layout tabel untuk kompatibilitas tinggi di DomPDF */
    .label-table {
        width: 100%;
        border-collapse: collapse;
    }

    .label-table td {
        vertical-align: middle;
    }

    .td-qr {
        width: 22mm;
        text-align: left;
    }

    .qr-wrapper {
        position: relative;
        width: 20mm;
        height: 20mm;
    }

    .qr-wrapper img {
        width: 20mm;
        height: 20mm;
        display: block;
    }

    /* Info teks di sebelah kanan QR */
    .td-info {
        padding-left: 2mm;
        text-align: left;
    }

    .company-name {
        font-size: 11pt;
        font-weight: normal;
        margin-bottom: 1.5mm;
        letter-spacing: 0.5pt;
    }

    .asset-code {
        font-size: 9pt;
        font-weight: normal;
        margin-bottom: 1mm;
    }

    .model-name {
        font-size: 8pt;
        font-weight: normal;
    }

    .location-section {
        page-break-after: always;
    }
    .location-section:last-child {
        page-break-after: avoid;
    }
</style>
</head>
<body>
@foreach($assets as $locationName => $locationAssets)
<div class="location-section">

    @foreach($locationAssets as $asset)
    @php
        /* QR Code */
        $qrSvg  = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(76)->margin(0)->generate($asset->code_asset);
        $base64Qr = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
    @endphp

    <div class="label">
        <table class="label-table">
            <tr>
                <td class="td-qr">
                    <div class="qr-wrapper">
                        <!-- Pseudo-viewfinder bracket overlay with inline SVG -->
                        <svg style="position:absolute; top:0; left:0; width:20mm; height:20mm; z-index:1;" viewBox="0 0 100 100">
                            <!-- Top Left -->
                            <path d="M 25 2 L 10 2 C 5 2 2 5 2 10 L 2 25" fill="none" stroke="#000" stroke-width="4" stroke-linecap="round"/>
                            <!-- Top Right -->
                            <path d="M 75 2 L 90 2 C 95 2 98 5 98 10 L 98 25" fill="none" stroke="#000" stroke-width="4" stroke-linecap="round"/>
                            <!-- Bottom Left -->
                            <path d="M 25 98 L 10 98 C 5 98 2 95 2 90 L 2 75" fill="none" stroke="#000" stroke-width="4" stroke-linecap="round"/>
                            <!-- Bottom Right -->
                            <path d="M 75 98 L 90 98 C 95 98 98 95 98 90 L 98 75" fill="none" stroke="#000" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                        <img src="{{ $base64Qr }}" style="position:absolute; top:2.5mm; left:2.5mm; width:15mm; height:15mm; z-index:0;" alt="QR">
                    </div>
                </td>
                <td class="td-info">
                    <div class="company-name">PT NHC</div>
                    <div class="asset-code">{{ $asset->code_asset }}</div>
                    <div class="model-name">{{ $asset->deviceModel->name ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</div>
@endforeach
</body>
</html>
