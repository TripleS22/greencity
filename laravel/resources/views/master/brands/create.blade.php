@extends('layouts.app')
@section('title', 'Tambah Brand')
@section('breadcrumb', 'Data Master > Brands > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah Brand</h1><a href="{{ route('master.brands') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('master.brands.store') }}" method="POST">@csrf
        <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@endsection
