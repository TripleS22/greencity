@extends('layouts.app')
@section('title', 'Maintenance')
@section('breadcrumb', 'Helpdesk > Maintenance')
@section('content')
<div class="page-header">
    <h1><i class="fas fa-tools"></i> Maintenance Schedule</h1>
    <div>
        <form action="{{ route('maintenances.generate') }}" method="POST" style="display:inline;" onsubmit="return confirm('Sistem akan mengecek dan membuat jadwal otomatis untuk lokasi yang belum memiliki jadwal di masa depan. Lanjutkan?')">
            @csrf
            <button class="btn btn-success mr-2" title="Otomatis buat jadwal baru untuk lokasi yang belum punya"><i class="fas fa-magic"></i> Generate Jadwal</button>
        </form>
        <a href="{{ route('maintenances.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Manual</a>
    </div>
</div>
<div class="card"><div class="card-body"><div class="table-responsive"><table>
    <thead><tr><th>#</th><th>Location</th><th>Category</th><th>Date</th><th>Linked Tickets</th><th>Description</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($maintenances as $m)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $m->location->name ?? '-' }}</td>
        <td><span class="badge badge-open">{{ ucfirst($m->category) }}</span></td>
        <td>{{ $m->date->format('d M Y') }}</td>
        <td>
            @if($m->tickets->count() > 0)
                <div style="font-size: 12px; margin-bottom: 4px;"><strong>{{ $m->tickets->count() }} Ticket(s):</strong></div>
                <ul style="margin: 0; padding-left: 15px; font-size: 12px;">
                    @foreach($m->tickets as $tk)
                        <li><a href="{{ route('tickets.show', $tk) }}">{{ $tk->title }}</a></li>
                    @endforeach
                </ul>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>{{ Str::limit($m->description, 50) }}</td>
        <td style="white-space:nowrap">
            <a href="{{ route('maintenances.show', $m) }}" class="btn btn-info btn-sm" title="Lihat Detail & Tiket"><i class="fas fa-eye"></i></a>
            <a href="{{ route('maintenances.edit', $m) }}" class="btn btn-warning btn-sm" title="Edit Jadwal"><i class="fas fa-edit"></i></a>
            <form action="{{ route('maintenances.destroy', $m) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" title="Hapus Jadwal"><i class="fas fa-trash"></i></button></form>
        </td>
    </tr>
    @empty<tr><td colspan="7" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>@endforelse
    </tbody>
</table></div><div class="pagination-wrapper">{{ $maintenances->links() }}</div></div></div>
@endsection
