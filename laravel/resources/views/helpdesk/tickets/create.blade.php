@extends('layouts.app')
@section('title', 'Tambah Ticket')
@section('breadcrumb', 'Helpdesk > Tickets > Tambah')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Tambah Ticket</h1>
    <a href="{{ route('tickets.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>
<div class="card"><div class="card-body">
    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group"><label>Title *</label><input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
            <div class="form-group"><label>Category *</label><select name="category" class="form-control" required><option value="maintenance" {{ old('category')=='maintenance'?'selected':'' }}>Maintenance</option><option value="procurement" {{ old('category')=='procurement'?'selected':'' }}>Procurement</option></select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Request Date *</label><input type="date" name="request_date" class="form-control" value="{{ old('request_date', date('Y-m-d')) }}" required></div>
            <div class="form-group"><label>Schedule Date</label><input type="date" name="schedule_date" class="form-control" value="{{ old('schedule_date') }}"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Requester</label>
                <select name="requester_id" class="form-control"><option value="">-- Pilih --</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('requester_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach</select>
            </div>
            <div class="form-group"><label>Location</label>
                <select name="location_id" class="form-control"><option value="">-- Pilih --</option>@foreach($locations as $l)<option value="{{ $l->id }}" {{ old('location_id')==$l->id?'selected':'' }}>{{ $l->name }}</option>@endforeach</select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Status</label>
                <select name="status" class="form-control">
                    <option value="open" {{ old('status')=='open'?'selected':'' }}>Open</option>
                    <option value="progress" {{ old('status')=='progress'?'selected':'' }}>Progress</option>
                    <option value="closed" {{ old('status')=='closed'?'selected':'' }}>Closed</option>
                </select>
            </div>
            <div class="form-group"><label>Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
        </div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control">{{ old('description') }}</textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@endsection
