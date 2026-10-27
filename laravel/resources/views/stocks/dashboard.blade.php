@extends('layouts.app')
@section('title', 'Stock Dashboard')
@section('breadcrumb', 'Stock > Dashboard')
@section('content')
<div class="page-header"><h1><i class="fas fa-chart-bar"></i> Stock Dashboard</h1></div>
<div class="stat-grid" style="grid-template-columns:repeat(auto-fill,minmax(240px,1fr))">
    <div class="stat-card bg-blue"><i class="fas fa-boxes icon"></i><div class="count">{{ $totalItems }}</div><div class="label">Total Items</div></div>
    <div class="stat-card bg-green"><i class="fas fa-money-bill-wave icon"></i><div class="count">{{ number_format($totalValue,0,',','.') }}</div><div class="label">Total Value (Rp)</div></div>
    <div class="stat-card bg-red"><i class="fas fa-exclamation-triangle icon"></i><div class="count">{{ $lowStock->count() }}</div><div class="label">Low Stock Items</div></div>
</div>
@if($lowStock->count())
<div class="card"><div class="card-header" style="background:#f8d7da;color:#721c24"><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</div><div class="card-body"><div class="table-responsive"><table>
    <thead><tr><th>Item</th><th>Stock</th><th>Minimum</th><th>Location</th></tr></thead>
    <tbody>@foreach($lowStock as $s)<tr><td><strong>{{ $s->item_name }}</strong></td><td style="color:#e74c3c;font-weight:700">{{ $s->total_stock }}</td><td>{{ $s->minimum_limit }}</td><td>{{ $s->location->name ?? '-' }}</td></tr>@endforeach</tbody>
</table></div></div></div>
@endif
@if($topMoving->count())
<div class="card"><div class="card-header"><i class="fas fa-fire"></i> Top Moving Items</div><div class="card-body"><div class="table-responsive"><table>
    <thead><tr><th>#</th><th>Item</th><th>Total Out</th></tr></thead>
    <tbody>@foreach($topMoving as $t)<tr><td>{{ $loop->iteration }}</td><td><strong>{{ $t->stock->item_name ?? '-' }}</strong></td><td>{{ $t->total_qty }}</td></tr>@endforeach</tbody>
</table></div></div></div>
@endif
@endsection
