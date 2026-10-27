@extends('layouts.app')
@section('title', 'Edit System Asset')
@section('breadcrumb', 'Project > System Asset > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit System Asset</h1><a href="{{ route('system_assets.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('system_assets.update', $item) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
        <div class="form-row"><div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" value="{{ $item->name }}" required></div><div class="form-group"><label>URL</label><input type="text" name="url" class="form-control" value="{{ $item->url }}"></div></div>
        <div class="form-row"><div class="form-group"><label>GitHub URL</label><input type="text" name="github_url" class="form-control" value="{{ $item->github_url }}"></div><div class="form-group"><label>Version</label><input type="text" name="version" class="form-control" value="{{ $item->version }}"></div></div>
        <div class="form-row"><div class="form-group"><label>Tech Stack</label><input type="text" name="tech_stack" class="form-control" value="{{ $item->tech_stack }}"></div><div class="form-group"><label>Hoster</label><input type="text" name="hoster" class="form-control" value="{{ $item->hoster }}"></div></div>
        <div class="form-row"><div class="form-group"><label>ZIP File</label><input type="file" name="zip_file" class="form-control"></div><div class="form-group"><label>DB Backup</label><input type="file" name="db_backup" class="form-control"></div></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
    </form>
</div></div>
@endsection
