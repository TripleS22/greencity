@extends('layouts.app')
@section('title', 'Groups')
@section('breadcrumb', 'Data Master > Groups')
@section('content')
<div class="page-header"><h1><i class="fas fa-layer-group"></i> Groups</h1><a href="{{ route('master.groups.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table>
    <thead><tr><th>#</th><th>Name</th><th>Actions</th></tr></thead>
    <tbody>@forelse($items as $i)
    <tr><td>{{ $loop->iteration }}</td><td>{{ $i->name }}</td>
        <td style="white-space:nowrap"><a href="{{ route('master.groups.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
        <form action="{{ route('master.groups.destroy', $i) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form></td></tr>
    @empty<tr><td colspan="3" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>@endforelse</tbody></table></div><div class="pagination-wrapper">{{ $items->links() }}</div></div></div>
@endsection
