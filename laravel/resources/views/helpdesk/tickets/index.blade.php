@extends('layouts.app')
@section('title', 'Tickets')
@section('breadcrumb', 'Helpdesk > Tickets')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-ticket-alt"></i> Tickets</h1>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Ticket</a>
</div>

{{-- ── Link Publik ──────────────────────────────────────────── --}}
<div class="card mb-4" style="border-left: 4px solid #3a7d34;">
    <div class="card-header" style="background:#f0f7ee; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-share-alt" style="color:#3a7d34"></i>
        <strong style="color:#245520">Link Publik — Bagikan ke User</strong>
        <small class="text-muted" style="margin-left:4px">Tanpa login</small>
    </div>
    <div class="card-body" style="padding: 14px 20px;">
        <div style="display:flex; gap:20px; flex-wrap:wrap;">

            {{-- Submit Tiket --}}
            <div style="flex:1; min-width:280px;">
                <label style="font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:5px">
                    <i class="fas fa-plus-circle" style="color:#3a7d34"></i> Form Buat Tiket Baru
                </label>
                <div style="display:flex;gap:6px;align-items:center">
                    <input type="text" id="pub-link-submit" readonly value="{{ route('public.ticket') }}"
                        style="flex:1;padding:7px 10px;border:1.5px solid #c5dfc0;border-radius:6px;font-size:12px;font-family:monospace;background:#f8fdf7;color:#333;cursor:pointer"
                        onclick="this.select()">
                    <button type="button" onclick="copyPubLink('pub-link-submit', this)"
                        style="padding:7px 12px;background:#3a7d34;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:12px;white-space:nowrap">
                        <i class="fas fa-copy"></i> Salin
                    </button>
                    <a href="{{ route('public.ticket') }}" target="_blank"
                        style="padding:7px 10px;background:#e8f5e9;color:#3a7d34;border:1.5px solid #c5dfc0;border-radius:6px;font-size:12px;text-decoration:none;white-space:nowrap">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>

            {{-- Lacak Tiket --}}
            <div style="flex:1; min-width:280px;">
                <label style="font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:5px">
                    <i class="fas fa-search" style="color:#3a7d34"></i> Lacak Status Tiket
                </label>
                <div style="display:flex;gap:6px;align-items:center">
                    <input type="text" id="pub-link-track" readonly value="{{ route('public.ticket.track') }}"
                        style="flex:1;padding:7px 10px;border:1.5px solid #c5dfc0;border-radius:6px;font-size:12px;font-family:monospace;background:#f8fdf7;color:#333;cursor:pointer"
                        onclick="this.select()">
                    <button type="button" onclick="copyPubLink('pub-link-track', this)"
                        style="padding:7px 12px;background:#3a7d34;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:12px;white-space:nowrap">
                        <i class="fas fa-copy"></i> Salin
                    </button>
                    <a href="{{ route('public.ticket.track') }}" target="_blank"
                        style="padding:7px 10px;background:#e8f5e9;color:#3a7d34;border:1.5px solid #c5dfc0;border-radius:6px;font-size:12px;text-decoration:none;white-space:nowrap">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>Daftar Ticket</span>
        <form method="GET" style="display:flex;gap:8px">
            <select name="status" class="form-control" style="width:auto" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
                <option value="progress" {{ request('status')=='progress'?'selected':'' }}>Progress</option>
                <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
            </select>
            <select name="priority" class="form-control" style="width:auto" onchange="this.form.submit()">
                <option value="">All Priority</option>
                <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
                <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
                <option value="urgent" {{ request('priority')=='urgent'?'selected':'' }}>Urgent</option>
            </select>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead><tr><th>#</th><th>Title</th><th>Requester</th><th>Category</th><th>Location</th><th>Status</th><th>Priority</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($tickets as $t)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $t->title }}</strong></td>
                    <td>{{ $t->requester->name ?? $t->requester_name ?? '-' }}</td>
                    <td>
                        <strong>{{ ucfirst($t->category) }}</strong>
                        @if($t->category === 'procurement')
                            <div style="margin-top:4px">
                                <select class="form-control form-control-sm inline-update" data-id="{{ $t->id }}" data-field="approval_status" style="width: auto; display:inline-block; padding: 2px 8px; font-size: 11px; font-weight: 500; height:auto; border-radius:12px;
                                    {{ $t->approval_status == 'pending' ? 'background:#fff3cd; color:#856404; border-color:#ffeeba;' : 
                                       ($t->approval_status == 'approved' ? 'background:#d4edda; color:#155724; border-color:#c3e6cb;' : 
                                       'background:#f8d7da; color:#721c24; border-color:#f5c6cb;') }}">
                                    <option value="pending" {{ $t->approval_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $t->approval_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $t->approval_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                        @endif
                    </td>
                    <td>{{ $t->location->name ?? '-' }}</td>
                    <td>
                        <select class="form-control form-control-sm inline-update" data-id="{{ $t->id }}" data-field="status" style="width: auto; min-width: 100px; display:inline-block; padding: 2px 8px; font-size: 13px; font-weight: 500;">
                            <option value="open" {{ $t->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="progress" {{ $t->status == 'progress' ? 'selected' : '' }}>Progress</option>
                            <option value="closed" {{ $t->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control form-control-sm inline-update" data-id="{{ $t->id }}" data-field="priority" style="width: auto; min-width: 100px; display:inline-block; padding: 2px 8px; font-size: 13px; font-weight: 500;">
                            <option value="low" {{ $t->priority == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $t->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $t->priority == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $t->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </td>
                    <td>{{ $t->request_date->format('d M Y') }}</td>
                    <td style="white-space:nowrap">
                        <a href="{{ route('tickets.show', $t) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('tickets.edit', $t) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('tickets.destroy', $t) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:#999;padding:30px">Belum ada ticket</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $tickets->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyPubLink(inputId, btn) {
        var input = document.getElementById(inputId);
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(function () {
            var orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
            btn.style.background = '#2ecc71';
            setTimeout(function () { btn.innerHTML = orig; btn.style.background = '#3a7d34'; }, 2000);
        }).catch(function() {
            document.execCommand('copy');
        });
    }

    $(document).ready(function() {
        // Simpan value lama saat di-focus agar bisa di-revert jika gagal
        $('.inline-update').on('focus', function() {
            $(this).data('original', $(this).val());
        });

        $('.inline-update').on('change', function() {
            let select = $(this);
            let id = select.data('id');
            let field = select.data('field');
            let value = select.val();
            let originalValue = select.data('original');

            // Disable sementara selagi loading
            select.prop('disabled', true);
            
            fetch(`/tickets/${id}/inline-update`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ field: field, value: value })
            })
            .then(response => response.json())
            .then(data => {
                select.prop('disabled', false);
                if (data.success) {
                    // Update data original ke value baru
                    select.data('original', value);
                    // Hijau sebentar tandanya sukses
                    let originalBg = select.css('background-color');
                    select.css('transition', 'background-color 0.3s');
                    select.css('background-color', '#d4edda');
                    setTimeout(() => select.css('background-color', originalBg), 1000);
                } else {
                    alert('Gagal mengupdate: ' + (data.message || 'Error'));
                    select.val(originalValue);
                }
            })
            .catch(error => {
                select.prop('disabled', false);
                alert('Terjadi kesalahan jaringan.');
                select.val(originalValue);
            });
        });
    });
</script>
@endpush
