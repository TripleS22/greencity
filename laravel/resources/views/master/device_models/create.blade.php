@extends('layouts.app')
@section('title', 'Tambah Device Model')
@section('breadcrumb', 'Data Master > Device Models > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah Device Model</h1><a href="{{ route('master.device_models') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('master.device_models.store') }}" method="POST">@csrf
        <div class="form-group"><label>Brand *</label><select name="brand_id" class="form-control" required><option value="">-- Pilih --</option>@foreach($brands as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
        <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@endsection
