@extends('layouts.app')
@section('title', 'Edit Project')
@section('breadcrumb', 'Project > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Project</h1><a href="{{ route('projects.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body">
    <form action="{{ route('projects.update', $project) }}" method="POST">@csrf @method('PUT')
        <div class="form-row">
            <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required></div>
            <div class="form-group"><label>Status</label><select name="status" class="form-control">@foreach(['active','completed','pending','cancelled'] as $s)<option value="{{ $s }}" {{ old('status',$project->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Stage</label>
                <select name="stage" class="form-control">
                    <option value="update" {{ old('stage', $project->stage) == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="planning" {{ old('stage', $project->stage) == 'planning' ? 'selected' : '' }}>Planning</option>
                    <option value="design" {{ old('stage', $project->stage) == 'design' ? 'selected' : '' }}>Design</option>
                    <option value="develop" {{ old('stage', $project->stage) == 'develop' ? 'selected' : '' }}>Develop</option>
                    <option value="testing" {{ old('stage', $project->stage) == 'testing' ? 'selected' : '' }}>Testing</option>
                    <option value="deployment" {{ old('stage', $project->stage) == 'deployment' ? 'selected' : '' }}>Deployment</option>
                    <option value="review" {{ old('stage', $project->stage) == 'review' ? 'selected' : '' }}>Review</option>
                    <option value="launch" {{ old('stage', $project->stage) == 'launch' ? 'selected' : '' }}>Launch</option>
                    <option value="done" {{ old('stage', $project->stage) == 'done' ? 'selected' : '' }}>Done</option>
                </select>
            </div>
            <div class="form-group"><label>Due Date</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>User</label><select name="user_id" class="form-control"><option value="">-- Pilih --</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('user_id',$project->user_id)==$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach</select></div>
            <div class="form-group"><label>Group</label><select name="group_id" class="form-control"><option value="">-- Pilih --</option>@foreach($groups as $g)<option value="{{ $g->id }}" {{ old('group_id',$project->group_id)==$g->id?'selected':'' }}>{{ $g->name }}</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label>Task</label><textarea name="task" class="form-control">{{ old('task', $project->task) }}</textarea></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control">{{ old('description', $project->description) }}</textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
    </form>
</div></div>
@endsection
