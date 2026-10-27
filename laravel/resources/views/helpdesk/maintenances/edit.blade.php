@extends('layouts.app')
@section('title', 'Edit Maintenance')
@section('breadcrumb', 'Helpdesk > Maintenance > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Maintenance</h1><a href="{{ route('maintenances.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('maintenances.update', $maintenance) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
        <div class="form-row">
            <div class="form-group"><label>Location</label><select name="location_id" class="form-control"><option value="">-- Pilih --</option>@foreach($locations as $l)<option value="{{ $l->id }}" {{ old('location_id',$maintenance->location_id)==$l->id?'selected':'' }}>{{ $l->name }}</option>@endforeach</select></div>
            <div class="form-group"><label>Category</label><select name="category" class="form-control">@foreach(['hardware','software','network','cctv','all'] as $c)<option value="{{ $c }}" {{ old('category',$maintenance->category)==$c?'selected':'' }}>{{ ucfirst($c) }}</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label>Date *</label><input type="date" name="date" class="form-control" value="{{ old('date', $maintenance->date->format('Y-m-d')) }}" required></div>
        <div class="form-group"><label>Photo</label>@if($maintenance->photo)<div style="margin-bottom:8px"><img src="{{ asset('storage/'.$maintenance->photo) }}" style="max-width:200px;border-radius:6px"></div>@endif<input type="file" name="photo" class="form-control" accept="image/*"></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control">{{ old('description', $maintenance->description) }}</textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
    </form>
</div></div>
@endsection
