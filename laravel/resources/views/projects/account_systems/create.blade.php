@extends('layouts.app')
@section('title', 'Tambah Account')
@section('breadcrumb', 'Project > Account System > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah Account</h1><a href="{{ route('account_systems.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('account_systems.store') }}" method="POST">@csrf
        <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" required></div>
        <div class="form-group"><label>Password *</label><input type="text" name="password" class="form-control" required></div>
        <div class="form-group"><label>Platform URL</label><input type="text" name="platform_url" class="form-control"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@endsection
