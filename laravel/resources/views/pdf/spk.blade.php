<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Kerja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .spk-info {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f2f2f2;
            padding: 5px 10px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table td {
            padding: 5px;
            vertical-align: top;
        }
        .label {
            width: 30%;
            font-weight: bold;
        }
        .value {
            width: 70%;
        }
        .border-table {
            border: 1px solid #000;
        }
        .border-table th, .border-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .border-table th {
            background-color: #f2f2f2;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            width: 25%;
            text-align: center;
            border: 1px solid #000;
            padding: 10px;
        }
        .signature-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    @foreach($records as $record)
        <div style="page-break-after: always;">
            <div class="header">
                <h1>Surat Perintah Kerja (SPK)</h1>
                <p>Nomor: {{ $record['spk_number'] }}</p>
            </div>

            <div class="section-title">INFORMASI UNIT</div>
            <table>
                <tr>
                    <td class="label">Code Unit</td>
                    <td class="value">: {{ $record['unit_code'] }}</td>
                </tr>
                <tr>
                    <td class="label">Location</td>
                    <td class="value">: {{ $record['location_unit'] }}</td>
                </tr>
                <tr>
                    <td class="label">Nomor Polisi</td>
                    <td class="value">: {{ $record['plat_no'] ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">SMU KM</td>
                    <td class="value">: {{ $record['smu_down'] }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="value">: {{ \Carbon\Carbon::parse($record['tanggal'])->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Driver</td>
                    <td class="value">: {{ $record['nama_driver'] ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Masalah Dilaporkan</td>
                    <td class="value">: {{ $record['unit_driver_problem'] }}</td>
                </tr>
            </table>

            <div class="section-title">ANALISIS MECHANIC</div>
            <table>
                <tr>
                    <td class="label">Mechanic Utama (PIC)</td>
                    <td class="value">: {{ $record['nama_mechanic_satu'] ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Mechanic Pendamping</td>
                    <td class="value">: {{ $record['nama_mechanic_dua'] ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Hasil Analisis</td>
                    <td class="value">: {{ $record['hasil_analisis'] ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Saran Perbaikan</td>
                    <td class="value">: {{ $record['saran_perbaikan'] ?: '-' }}</td>
                </tr>
            </table>

            <div class="section-title">DAFTAR SPAREPART YANG DIGUNAKAN</div>
            <table class="border-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Part</th>
                        <th style="width: 100px;">Qty</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach($record['parts'] as $part)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $part['name'] }}</td>
                            <td>{{ $part['qty'] }}</td>
                            <td>{{ $part['status'] }}</td>
                        </tr>
                    @endforeach
                    @for($j = 0; $j < 5; $j++)
                        <tr>
                            <td style="height: 25px;">{{ $i++ }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <div class="signature-section">
                <table style="border: none;">
                    <tr>
                        <td class="signature-box">
                            <div style="height: 60px;"></div>
                            <div class="signature-label">Dibuat Oleh<br>(Admin)</div>
                        </td>
                        <td class="signature-box">
                            <div style="height: 60px;"></div>
                            <div class="signature-label">Diajukan Oleh<br>(Driver)</div>
                        </td>
                        <td class="signature-box">
                            <div style="height: 60px;"></div>
                            <div class="signature-label">Dikerjakan Oleh<br>(Mekanik)</div>
                        </td>
                        <td class="signature-box" style="margin-right: 0;">
                            <div style="height: 60px;"></div>
                            <div class="signature-label">Mengetahui<br>(______________)</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
    @endforeach
</body>
</html>
