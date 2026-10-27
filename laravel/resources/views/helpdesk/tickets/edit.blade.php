@extends('layouts.app')
@section('title', 'Edit Ticket')
@section('breadcrumb', 'Helpdesk > Tickets > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Ticket</h1><a href="{{ route('tickets.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('tickets.update', $ticket) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="form-row">
            <div class="form-group"><label>Title *</label><input type="text" name="title" class="form-control" value="{{ old('title', $ticket->title) }}" required></div>
            <div class="form-group"><label>Category *</label><select name="category" class="form-control" required><option value="maintenance" {{ old('category', $ticket->category)=='maintenance'?'selected':'' }}>Maintenance</option><option value="procurement" {{ old('category', $ticket->category)=='procurement'?'selected':'' }}>Procurement</option></select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Request Date *</label><input type="date" name="request_date" class="form-control" value="{{ old('request_date', $ticket->request_date->format('Y-m-d')) }}" required></div>
            <div class="form-group"><label>Schedule Date</label><input type="date" name="schedule_date" class="form-control" value="{{ old('schedule_date', $ticket->schedule_date?->format('Y-m-d')) }}"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Requester</label><select name="requester_id" class="form-control"><option value="">-- Pilih --</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('requester_id',$ticket->requester_id)==$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach</select></div>
            <div class="form-group"><label>Status</label><select name="status" class="form-control">@foreach(['open','progress','closed'] as $s)<option value="{{ $s }}" {{ old('status',$ticket->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Priority</label><select name="priority" class="form-control">@foreach(['low','medium','high','urgent'] as $p)<option value="{{ $p }}" {{ old('priority',$ticket->priority)==$p?'selected':'' }}>{{ ucfirst($p) }}</option>@endforeach</select></div>
            <div class="form-group"><label>Location</label><select name="location_id" class="form-control"><option value="">-- Pilih --</option>@foreach($locations as $l)<option value="{{ $l->id }}" {{ old('location_id',$ticket->location_id)==$l->id?'selected':'' }}>{{ $l->name }}</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label>Photo</label>@if($ticket->photo)<div style="margin-bottom:8px"><img src="{{ asset('storage/'.$ticket->photo) }}" style="max-width:200px;border-radius:6px"></div>@endif<input type="file" name="photo" class="form-control" accept="image/*"></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control">{{ old('description', $ticket->description) }}</textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
    </form>
</div></div>
@endsection
