@extends('layouts.app')
@section('title', 'Edit Brand')
@section('breadcrumb', 'Data Master > Brands > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Brand</h1><a href="{{ route('master.brands') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('master.brands.update', $brand) }}" method="POST">@csrf @method('PUT')
        <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" value="{{ $brand->name }}" required></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
    </form>
</div></div>
@endsection
