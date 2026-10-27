@extends('layouts.app')
@section('title', 'Tambah System Asset')
@section('breadcrumb', 'Project > System Asset > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah System Asset</h1><a href="{{ route('system_assets.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('system_assets.store') }}" method="POST" enctype="multipart/form-data">@csrf
        <div class="form-row"><div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div><div class="form-group"><label>URL</label><input type="text" name="url" class="form-control"></div></div>
        <div class="form-row"><div class="form-group"><label>GitHub URL</label><input type="text" name="github_url" class="form-control"></div><div class="form-group"><label>Version</label><input type="text" name="version" class="form-control"></div></div>
        <div class="form-row"><div class="form-group"><label>Tech Stack</label><input type="text" name="tech_stack" class="form-control"></div><div class="form-group"><label>Hoster</label><input type="text" name="hoster" class="form-control"></div></div>
        <div class="form-row"><div class="form-group"><label>ZIP File</label><input type="file" name="zip_file" class="form-control"></div><div class="form-group"><label>DB Backup</label><input type="file" name="db_backup" class="form-control"></div></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@endsection
