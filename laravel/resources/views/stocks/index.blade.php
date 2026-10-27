@extends('layouts.app')
@section('title', 'Stock Opname')
@section('breadcrumb', 'Stock > Stock Opname')
@section('content')
<div class="page-header"><h1><i class="fas fa-boxes"></i> Stock Opname</h1><a href="{{ route('stocks.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="datatable">
    <thead><tr><th>#</th><th>Item Name</th><th>Type</th><th>Unit Price</th><th>In</th><th>Out</th><th>Total</th><th>Min Limit</th><th>Location</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($stocks as $s)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td><strong>{{ $s->item_name }}</strong></td><td>{{ $s->unit_type ?? '-' }}</td><td>{{ number_format($s->unit_price,0,',','.') }}</td>
        <td style="color:green">{{ $s->stock_in }}</td><td style="color:red">{{ $s->stock_out }}</td>
        <td><strong class="{{ $s->total_stock <= $s->minimum_limit ? 'badge badge-urgent' : '' }}">{{ $s->total_stock }}</strong></td>
        <td>{{ $s->minimum_limit }}</td><td>{{ $s->location->name ?? '-' }}</td>
        <td style="white-space:nowrap">
            <a href="{{ route('stocks.show', $s) }}" class="btn btn-info btn-sm" title="Detail & History"><i class="fas fa-history"></i></a>
            <a href="{{ route('stocks.edit', $s) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
            <form action="{{ route('stocks.destroy', $s) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
        </td>
    </tr>
    @empty<tr><td colspan="10" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>@endforelse
    </tbody>
</table></div></div></div>
@endsection
