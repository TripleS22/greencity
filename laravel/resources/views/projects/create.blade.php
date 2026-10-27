@extends('layouts.app')
@section('title', 'Tambah Project')
@section('breadcrumb', 'Project > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah Project</h1><a href="{{ route('projects.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('projects.store') }}" method="POST">@csrf
        <div class="form-row">
            <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="active">Active</option><option value="completed">Completed</option><option value="pending">Pending</option><option value="cancelled">Cancelled</option></select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Stage</label>
                <select name="stage" class="form-control">
                    <option value="update" {{ old('stage') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="planning" {{ old('stage') == 'planning' ? 'selected' : '' }}>Planning</option>
                    <option value="design" {{ old('stage') == 'design' ? 'selected' : '' }}>Design</option>
                    <option value="develop" {{ old('stage') == 'develop' ? 'selected' : '' }}>Develop</option>
                    <option value="testing" {{ old('stage') == 'testing' ? 'selected' : '' }}>Testing</option>
                    <option value="deployment" {{ old('stage') == 'deployment' ? 'selected' : '' }}>Deployment</option>
                    <option value="review" {{ old('stage') == 'review' ? 'selected' : '' }}>Review</option>
                    <option value="launch" {{ old('stage') == 'launch' ? 'selected' : '' }}>Launch</option>
                    <option value="done" {{ old('stage') == 'done' ? 'selected' : '' }}>Done</option>
                </select>
            </div>
            <div class="form-group"><label>Due Date</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>User</label><select name="user_id" class="form-control"><option value="">-- Pilih --</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
            <div class="form-group"><label>Group</label><select name="group_id" class="form-control"><option value="">-- Pilih --</option>@foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label>Task</label><textarea name="task" class="form-control">{{ old('task') }}</textarea></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control">{{ old('description') }}</textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@endsection
