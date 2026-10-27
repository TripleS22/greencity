@extends('layouts.app')
@section('title', 'Edit Location')
@section('breadcrumb', 'Data Master > Locations > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Location</h1><a href="{{ route('master.locations') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body"><form action="{{ route('master.locations.update', $location) }}" method="POST">@csrf @method('PUT')
    <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" value="{{ $location->name }}" required></div>
    <div class="form-group"><label>Address</label><input type="text" name="address" class="form-control" value="{{ $location->address }}"></div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
</form></div></div>
@endsection
