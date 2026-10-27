@extends('layouts.app')
@section('title', 'Edit Account')
@section('breadcrumb', 'Project > Account System > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Account</h1><a href="{{ route('account_systems.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('account_systems.update', $item) }}" method="POST">@csrf @method('PUT')
        <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" value="{{ $item->email }}" required></div>
        <div class="form-group"><label>Password (kosongkan jika tidak diubah)</label><input type="text" name="password" class="form-control"></div>
        <div class="form-group"><label>Platform URL</label><input type="text" name="platform_url" class="form-control" value="{{ $item->platform_url }}"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
    </form>
</div></div>
@endsection
