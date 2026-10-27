@extends('layouts.app')
@section('title', 'Tambah Stock')
@section('breadcrumb', 'Stock > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah Stock</h1><a href="{{ route('stocks.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('stocks.store') }}" method="POST">@csrf
        <div class="form-row"><div class="form-group"><label>Item Name *</label><input type="text" name="item_name" class="form-control" required></div><div class="form-group"><label>Unit Type (Pcs, Box, Liter dll)</label><input type="text" name="unit_type" class="form-control" placeholder="Contoh: Pcs"></div></div>
        <div class="form-row"><div class="form-group"><label>Unit Price</label><input type="number" name="unit_price" class="form-control" value="0" min="0"></div><div class="form-group"><label>Stock In</label><input type="number" name="stock_in" class="form-control" value="0" min="0"></div></div>
        <div class="form-row"><div class="form-group"><label>Stock Out</label><input type="number" name="stock_out" class="form-control" value="0" min="0"></div><div class="form-group"><label>Minimum Limit</label><input type="number" name="minimum_limit" class="form-control" value="0" min="0"></div></div>
        <div class="form-row"><div class="form-group"><label>Location</label><select name="location_id" class="form-control"><option value="">-- Pilih --</option>@foreach($locations as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach</select></div>
        <div class="form-group"><label>User</label><select name="user_id" class="form-control"><option value="">-- Pilih --</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@endsection
