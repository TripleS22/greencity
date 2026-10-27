@extends('layouts.app')
@section('title', 'Tambah Maintenance')
@section('breadcrumb', 'Helpdesk > Maintenance > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah Maintenance</h1><a href="{{ route('maintenances.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('maintenances.store') }}" method="POST" enctype="multipart/form-data">@csrf
        <div class="form-row">
            <div class="form-group"><label>Location</label><select name="location_id" class="form-control"><option value="">-- Pilih --</option>@foreach($locations as $l)<option value="{{ $l->id }}" {{ old('location_id')==$l->id?'selected':'' }}>{{ $l->name }}</option>@endforeach</select></div>
            <div class="form-group"><label>Category</label><select name="category" class="form-control">@foreach(['hardware','software','network','cctv','all'] as $c)<option value="{{ $c }}" {{ old('category')==$c?'selected':'' }}>{{ ucfirst($c) }}</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label>Date *</label><input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required></div>
        <div class="form-group"><label>Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control">{{ old('description') }}</textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@endsection
