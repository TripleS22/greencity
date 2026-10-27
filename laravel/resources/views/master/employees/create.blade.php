@extends('layouts.app')
@section('title', 'Tambah Employee')
@section('breadcrumb', 'Data Master > Employees > Tambah')
@section('content')
<div class="page-header"><h1><i class="fas fa-plus-circle"></i> Tambah Employee</h1><a href="{{ route('master.employees') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body"><form action="{{ route('master.employees.store') }}" method="POST">@csrf
    <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div>
    <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" class="form-control"></div>
    <div class="form-group"><label>Group</label><select name="group_id" class="form-control"><option value="">-- Pilih Group --</option>@foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach</select></div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
</form></div></div>
@endsection
