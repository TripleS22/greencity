@extends('layouts.app')
@section('title', 'Edit Asset Purna Guna')
@section('breadcrumb', 'Stock > Asset Purna Guna > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Asset Purna Guna</h1><a href="{{ route('stocks.purna_guna') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('stocks.purna_guna.update', $item) }}" method="POST">@csrf @method('PUT')
        <div class="form-row"><div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" value="{{ $item->name }}" required></div><div class="form-group"><label>Qty</label><input type="number" name="qty" class="form-control" value="{{ $item->qty }}" min="0"></div></div>
        <div class="form-row"><div class="form-group"><label>Price Range</label><input type="text" name="price_range" class="form-control" value="{{ $item->price_range }}"></div><div class="form-group"><label>Total Price</label><input type="number" name="total_price" class="form-control" value="{{ $item->total_price }}" min="0"></div></div>
        <div class="form-group"><label>Status</label><select name="status" class="form-control">@foreach(['warehouse','sold','disposed'] as $s)<option value="{{ $s }}" {{ $item->status==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
    </form>
</div></div>
@endsection
