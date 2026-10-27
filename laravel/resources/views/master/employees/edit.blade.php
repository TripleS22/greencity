@extends('layouts.app')
@section('title', 'Edit Employee')
@section('breadcrumb', 'Data Master > Employees > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit Employee</h1><a href="{{ route('master.employees') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body"><form action="{{ route('master.employees.update', $employee) }}" method="POST">@csrf @method('PUT')
    <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" value="{{ $employee->name }}" required></div>
    <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" class="form-control" value="{{ $employee->jabatan }}"></div>
    <div class="form-group"><label>Group</label><select name="group_id" class="form-control"><option value="">-- Pilih Group --</option>@foreach($groups as $g)<option value="{{ $g->id }}" {{ $employee->group_id==$g->id?'selected':'' }}>{{ $g->name }}</option>@endforeach</select></div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
</form></div></div>
@endsection
