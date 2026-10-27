@extends('layouts.app')
@section('title', 'Detail Maintenance')
@section('breadcrumb', 'Helpdesk > Maintenance > Detail')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-tools"></i> Detail Maintenance Schedule</h1>
    <a href="{{ route('maintenances.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Informasi Maintenance</strong>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:14px">
            <div><strong>Location:</strong> {{ $maintenance->location->name ?? '-' }}</div>
            <div><strong>Category:</strong> <span class="badge badge-open">{{ ucfirst($maintenance->category) }}</span></div>
            <div><strong>Scheduled Date:</strong> {{ \Carbon\Carbon::parse($maintenance->date)->format('d F Y') }}</div>
        </div>
        @if($maintenance->description)
            <div style="margin-top:16px;">
                <strong>Description:</strong>
                <p style="margin-top:4px;color:#555;padding:10px;background:#f9f9f9;border-radius:6px;border:1px solid #eee;">{{ $maintenance->description }}</p>
            </div>
        @endif
        @if($maintenance->photo)
            <div style="margin-top:16px">
                <strong>Photo / Lampiran:</strong><br>
                <img src="{{ asset('storage/'.$maintenance->photo) }}" style="max-width:400px;border-radius:8px;margin-top:8px;border:1px solid #ddd;">
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <strong><i class="fas fa-ticket-alt"></i> Riwayat Tiket Terkait (Linked Tickets)</strong>
    </div>
    <div class="card-body">
        @if($maintenance->tickets->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 13px;">
                    <thead style="background:#f4f6f9;">
                        <tr>
                            <th>#</th>
                            <th>Kode Tiket</th>
                            <th>Judul Masalah</th>
                            <th>Pelapor</th>
                            <th>Tgl Lapor</th>
                            <th>Status Tiket</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($maintenance->tickets as $ticket)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $ticket->ticket_code }}</strong></td>
                                <td>{{ Str::limit($ticket->title, 40) }}</td>
                                <td>{{ $ticket->requester->name ?? $ticket->requester_name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($ticket->request_date)->format('d M Y') }}</td>
                                <td>
                                    @if($ticket->status == 'open')
                                        <span class="badge" style="background:#f39c12;color:white;">Open</span>
                                    @elseif($ticket->status == 'progress')
                                        <span class="badge" style="background:#3498db;color:white;">In Progress</span>
                                    @else
                                        <span class="badge" style="background:#2ecc71;color:white;">Closed</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-info btn-sm" target="_blank" title="Lihat Detail Tiket">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2 text-muted" style="font-size: 12px;">
                <em>Keterangan: Menampilkan semua tiket yang otomatis digabungkan atau di-reschedule ke jadwal maintenance ini.</em>
            </div>
        @else
            <div class="alert alert-info" style="margin-bottom: 0;">
                <i class="fas fa-info-circle"></i> Belum ada tiket yang terhubung (linked) dengan jadwal maintenance ini.
            </div>
        @endif
    </div>
</div>
@endsection
