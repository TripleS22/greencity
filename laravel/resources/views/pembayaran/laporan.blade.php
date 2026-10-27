@extends('layouts.app')

@section('title', 'Laporan Pembayaran - Iuran Warga')

@section('content')
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.5rem; font-weight:700; color:var(--text);">Laporan Pembayaran</h1>
        <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:0.25rem;">Periode: {{ $bulan }}</p>
    </div>
    <button class="btn btn-outline" onclick="window.print()">
        <i data-lucide="printer" style="width:16px;height:16px"></i>
        Cetak
    </button>
</div>

<div class="card">
    @php
        $totalTagihan = $pembayaran->sum('jumlah');
        $totalLunas = $pembayaran->where('status', 'lunas')->sum('jumlah');
        $totalBelum = $pembayaran->where('status', '!=', 'lunas')->sum('jumlah');
    @endphp
    
    <div class="stats-grid" style="margin-bottom:1.5rem;">
        <div style="background:var(--primary-lightest); border-radius:12px; padding:1rem; text-align:center;">
            <div style="color:var(--text-secondary); font-size:0.82rem;">Total Tagihan</div>
            <div style="font-weight:700; font-size:1.2rem; color:var(--text);">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
        </div>
        <div style="background:var(--primary-lighter); border-radius:12px; padding:1rem; text-align:center;">
            <div style="color:var(--primary); font-size:0.82rem;">Sudah Lunas</div>
            <div style="font-weight:700; font-size:1.2rem; color:var(--primary);">Rp {{ number_format($totalLunas, 0, ',', '.') }}</div>
        </div>
        <div style="background:var(--red-light); border-radius:12px; padding:1rem; text-align:center;">
            <div style="color:var(--red); font-size:0.82rem;">Belum Lunas</div>
            <div style="font-weight:700; font-size:1.2rem; color:var(--red);">Rp {{ number_format($totalBelum, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Unit</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal Bayar</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pembayaran as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight:500;">{{ $p->warga->nama ?? '-' }}</td>
                    <td style="color:var(--text-secondary);">{{ $p->warga->unit ?? '-' }}</td>
                    <td style="font-weight:600;">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    <td>
                        @if($p->status === 'lunas')
                            <span class="badge badge-success">Lunas</span>
                        @else
                            <span class="badge badge-danger">Belum Lunas</span>
                        @endif
                    </td>
                    <td style="color:var(--text-secondary);">{{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d/m/Y') : '-' }}</td>
                    <td style="color:var(--text-secondary); text-transform:capitalize;">{{ $p->metode_bayar ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
