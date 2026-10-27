@extends('layouts.app')
@section('title', 'Detail Ticket')
@section('breadcrumb', 'Helpdesk > Tickets > Detail')
@section('content')
<div class="page-header"><h1><i class="fas fa-ticket-alt"></i> {{ $ticket->title }}</h1>
    <a href="{{ route('tickets.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-header">Detail Ticket #{{ $ticket->id }}</div><div class="card-body">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13px">
        <div><strong>Title:</strong> {{ $ticket->title }}</div>
        <div><strong>Status:</strong> <span class="badge badge-{{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span></div>
        <div><strong>Priority:</strong> <span class="badge badge-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span></div>
        <div><strong>Requester:</strong> {{ $ticket->requester->name ?? $ticket->requester_name ?? '-' }}</div>
        <div><strong>Location:</strong> {{ $ticket->location->name ?? '-' }}</div>
        <div><strong>Request Date:</strong> {{ $ticket->request_date->format('d M Y') }}</div>
        <div><strong>Schedule Date:</strong> {{ $ticket->schedule_date?->format('d M Y') ?? '-' }}</div>
    </div>
    </div>
    @if($ticket->description)<div style="margin-top:16px"><strong>Description:</strong><p style="margin-top:4px;color:#555">{{ $ticket->description }}</p></div>@endif
    @if($ticket->photo)<div style="margin-top:16px"><strong>Photo:</strong><br><img src="{{ asset('storage/'.$ticket->photo) }}" style="max-width:400px;border-radius:8px;margin-top:8px"></div>@endif
</div></div>

{{-- Resolution Section --}}
<div class="card mt-4">
    <div class="card-header bg-{{ $ticket->status === 'closed' ? 'success' : 'light' }} {{ $ticket->status === 'closed' ? 'text-white' : '' }}">
        <i class="fas fa-clipboard-check"></i> Penyelesaian Ticket
    </div>
    <div class="card-body">
        @if($ticket->status === 'closed')
            <div class="alert alert-success">
                <strong>Ticket ini telah diselesaikan pada:</strong> {{ $ticket->resolved_at?->format('d M Y H:i') ?? '-' }}
            </div>
            @if($ticket->resolution_notes)
                <div class="mt-3">
                    <strong>Catatan Penyelesaian:</strong>
                    <div class="p-3 bg-light border rounded mt-2">
                        {!! nl2br(e($ticket->resolution_notes)) !!}
                    </div>
                </div>
            @else
                <p class="text-muted mt-2"><em>Tidak ada catatan penyelesaian yang dilampirkan.</em></p>
            @endif
        @else
            <form action="{{ route('tickets.resolve', $ticket) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label><strong>Catatan Penyelesaian (Resolution Notes) *</strong></label>
                    <textarea name="resolution_notes" class="form-control" rows="4" required placeholder="Jelaskan secara detail bagaimana masalah ini diselesaikan..."></textarea>
                    <small class="form-text text-muted">Mengisi dan menyimpan ini otomatis akan mengubah status ticket menjadi Closed.</small>
                </div>
                <button type="submit" class="btn btn-success mt-2">
                    <i class="fas fa-check-double"></i> Tandai Selesai (Closed)
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
