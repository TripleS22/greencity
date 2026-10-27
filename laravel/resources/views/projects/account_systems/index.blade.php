@extends('layouts.app')
@section('title', 'Account Systems')
@section('breadcrumb', 'Project > Account System')
@section('content')
<div class="page-header"><h1><i class="fas fa-user-shield"></i> Account Systems</h1><a href="{{ route('account_systems.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table>
    <thead><tr><th>#</th><th>Email</th><th>Platform URL</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($items as $i)
    <tr><td>{{ $loop->iteration }}</td><td>{{ $i->email }}</td><td>{{ $i->platform_url ?? '-' }}</td>
        <td style="white-space:nowrap"><a href="{{ route('account_systems.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
        <form action="{{ route('account_systems.destroy', $i) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form></td></tr>
    @empty<tr><td colspan="4" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>@endforelse
    </tbody></table></div><div class="pagination-wrapper">{{ $items->links() }}</div></div></div>
@endsection
