@extends('layouts.app')
@section('title', 'Edit Group')
@section('breadcrumb', 'Data Master > Groups > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Group</h1><a href="{{ route('master.groups') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body"><form action="{{ route('master.groups.update', $group) }}" method="POST">@csrf @method('PUT')
    <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" value="{{ $group->name }}" required></div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
</form></div></div>
@endsection
