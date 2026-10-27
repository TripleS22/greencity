@extends('layouts.app')
@section('title', 'System Assets')
@section('breadcrumb', 'Project > System Asset')
@section('content')
<div class="page-header"><h1><i class="fas fa-server"></i> System Assets</h1><a href="{{ route('system_assets.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table>
    <thead><tr><th>#</th><th>Name</th><th>URL</th><th>Version</th><th>Tech Stack</th><th>Hoster</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($items as $i)
    <tr><td>{{ $loop->iteration }}</td><td><strong>{{ $i->name }}</strong></td><td>{{ $i->url ?? '-' }}</td><td>{{ $i->version ?? '-' }}</td><td>{{ $i->tech_stack ?? '-' }}</td><td>{{ $i->hoster ?? '-' }}</td>
        <td style="white-space:nowrap"><a href="{{ route('system_assets.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
        <form action="{{ route('system_assets.destroy', $i) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form></td>
    </tr>
    @empty<tr><td colspan="7" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>@endforelse
    </tbody>
</table></div><div class="pagination-wrapper">{{ $items->links() }}</div></div></div>
@endsection
