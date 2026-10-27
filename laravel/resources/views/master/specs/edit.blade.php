@extends('layouts.app')
@section('title', 'Edit ' . $specType . ' Spec')
@section('breadcrumb', 'Data Master > ' . $specType . ' Specs > Edit')
@section('content')
<div class="page-header"><h1><i class="fas fa-edit"></i> Edit {{ $specType }} Spec</h1><a href="{{ route('master.'.strtolower($specType).'_specs') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="card"><div class="card-body"><form action="{{ route($updateRoute, $item) }}" method="POST">@csrf @method('PUT')
    <div class="form-group"><label>Spec *</label><input type="text" name="spec" class="form-control" value="{{ $item->spec }}" required></div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
</form></div></div>
@endsection
