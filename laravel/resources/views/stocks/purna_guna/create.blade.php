@extends('layouts.app')
@section('title', 'Tambah Asset Purna Guna')
@section('breadcrumb', 'Stock > Asset Purna Guna > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah Asset Purna Guna</h1><a href="{{ route('stocks.purna_guna') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('stocks.purna_guna.store') }}" method="POST">@csrf
        <div class="form-row"><div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div><div class="form-group"><label>Qty</label><input type="number" name="qty" class="form-control" value="0" min="0"></div></div>
        <div class="form-row"><div class="form-group"><label>Price Range</label><input type="text" name="price_range" class="form-control"></div><div class="form-group"><label>Total Price</label><input type="number" name="total_price" class="form-control" value="0" min="0"></div></div>
        <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="warehouse">Warehouse</option><option value="sold">Sold</option><option value="disposed">Disposed</option></select></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@endsection
