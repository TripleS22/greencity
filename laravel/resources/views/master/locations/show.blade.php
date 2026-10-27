@extends('layouts.app')
@section('title', 'Detail Location')
@section('breadcrumb', 'Data Master > Locations > Detail')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-map-marker-alt"></i> Detail Lokasi: {{ $location->name }}</h1>
    <a href="{{ route('master.locations') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title"><i class="fas fa-info-circle"></i> Informasi Detail</h5>
        <table class="table table-borderless mt-3" style="max-width: 600px;">
            <tr>
                <th style="width: 150px;">Nama Lokasi</th>
                <td>: <strong>{{ $location->name }}</strong></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>: {{ $location->address ?? '-' }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <strong><i class="fas fa-calendar-alt"></i> Status Jadwal Maintenance Saat Ini</strong>
    </div>
    <div class="card-body">
        @if($maintenances->count() > 0)
            @php $m = $maintenances->first(); @endphp
            <div class="d-flex align-items-center">
                <div class="mr-4">
                    <span class="text-muted d-block" style="font-size:12px;">Tanggal Jadwal</span>
                    <strong style="font-size:18px;">{{ \Carbon\Carbon::parse($m->date)->format('d M Y') }}</strong>
                </div>
                <div>
                    @if(now()->startOfDay()->gt(\Carbon\Carbon::parse($m->date)))
                        <span class="badge badge-danger px-3 py-2">Terlewat / Segera Laksanakan</span>
                    @else
                        <span class="badge badge-success px-3 py-2">Mendatang (Terjadwal)</span>
                    @endif
                </div>
            </div>
        @else
            <div class="alert alert-warning mb-0">Lokasi ini belum memiliki pendaftaran jadwal.</div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <strong><i class="fas fa-history"></i> Riwayat Pekerjaan & Tiket (History)</strong>
    </div>
    <div class="card-body">
        @if($tickets->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover mt-2">
                    <thead style="background:#f4f6f9;">
                        <tr>
                            <th>#</th>
                            <th>Kode Tiket / Judul</th>
                            <th>Kategori</th>
                            <th>Tanggal Laporan</th>
                            <th>Tgl Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $t)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $t->ticket_code }}</strong><br>
                                    <small>{{ $t->title }}</small>
                                </td>
                                <td>
                                    @if($t->category == 'maintenance')
                                        <span class="badge badge-info">Pemeliharaan</span>
                                    @else
                                        <span class="badge badge-warning">Pengadaan</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($t->request_date)->format('d M Y') }}</td>
                                <td>{{ $t->resolved_at ? \Carbon\Carbon::parse($t->resolved_at)->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($t->status == 'closed')
                                        <span class="badge badge-success">Selesai</span>
                                    @elseif($t->status == 'progress')
                                        <span class="badge badge-warning">Diproses</span>
                                    @else
                                        <span class="badge badge-open">Terbuka</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('tickets.show', $t) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">Belum ada riwayat perbaikan/tiket di lokasi ini sejauh ini.</div>
        @endif
    </div>
</div>
@endsection
