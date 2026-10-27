@extends('layouts.app')
@section('title', 'Tambah ' . $specType . ' Spec')
@section('breadcrumb', 'Data Master > ' . $specType . ' Specs > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah {{ $specType }} Spec</h1><a href="{{ route('master.'.strtolower($specType).'_specs') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body"><form action="{{ route($storeRoute) }}" method="POST">@csrf
    <div class="form-group"><label>Spec *</label><input type="text" name="spec" class="form-control" required></div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
</form></div></div>
@endsection
