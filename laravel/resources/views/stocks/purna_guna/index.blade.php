@extends('layouts.app')
@section('title', 'Asset Purna Guna')
@section('breadcrumb', 'Stock > Asset Purna Guna')
@section('content')
<div class="page-header"><h1><i class="fas fa-recycle"></i> Asset Purna Guna</h1><a href="{{ route('stocks.purna_guna.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table>
    <thead><tr><th>#</th><th>Name</th><th>Qty</th><th>Price Range</th><th>Total Price</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($items as $i)
    <tr><td>{{ $loop->iteration }}</td><td><strong>{{ $i->name }}</strong></td><td>{{ $i->qty }}</td><td>{{ $i->price_range ?? '-' }}</td><td>{{ number_format($i->total_price,0,',','.') }}</td>
        <td><span class="badge badge-{{ $i->status }}">{{ ucfirst($i->status) }}</span></td>
        <td style="white-space:nowrap"><a href="{{ route('stocks.purna_guna.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
        <form action="{{ route('stocks.purna_guna.destroy', $i) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form></td></tr>
    @empty<tr><td colspan="7" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>@endforelse
    </tbody></table></div><div class="pagination-wrapper">{{ $items->links() }}</div></div></div>
@endsection
