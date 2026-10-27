@extends('layouts.app')
@section('title', 'Edit Stock')
@section('breadcrumb', 'Stock > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Stock</h1><a href="{{ route('stocks.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('stocks.update', $stock) }}" method="POST">@csrf @method('PUT')
        <div class="form-row"><div class="form-group"><label>Item Name *</label><input type="text" name="item_name" class="form-control" value="{{ $stock->item_name }}" required></div><div class="form-group"><label>Unit Type (Pcs, Box, Liter dll)</label><input type="text" name="unit_type" class="form-control" value="{{ $stock->unit_type }}" placeholder="Contoh: Pcs"></div></div>
        <div class="form-row"><div class="form-group"><label>Unit Price</label><input type="number" name="unit_price" class="form-control" value="{{ $stock->unit_price }}" min="0"></div><div class="form-group"><label>Stock In</label><input type="number" name="stock_in" class="form-control" value="{{ $stock->stock_in }}" min="0"></div></div>
        <div class="form-row"><div class="form-group"><label>Stock Out</label><input type="number" name="stock_out" class="form-control" value="{{ $stock->stock_out }}" min="0"></div><div class="form-group"><label>Minimum Limit</label><input type="number" name="minimum_limit" class="form-control" value="{{ $stock->minimum_limit }}" min="0"></div></div>
        <div class="form-row"><div class="form-group"><label>Location</label><select name="location_id" class="form-control"><option value="">-- Pilih --</option>@foreach($locations as $l)<option value="{{ $l->id }}" {{ $stock->location_id==$l->id?'selected':'' }}>{{ $l->name }}</option>@endforeach</select></div>
        <div class="form-group"><label>User</label><select name="user_id" class="form-control"><option value="">-- Pilih --</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ $stock->user_id==$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach</select></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
    </form>
</div></div>
@endsection
