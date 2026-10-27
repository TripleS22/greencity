@extends('layouts.app')
@section('title', 'Edit E-Money')
@section('breadcrumb', 'Stock > E-Money > Edit')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-credit-card"></i> Edit E-Money</h1>
</div>

<div class="card" style="max-width:600px;margin:0 auto">
    <div class="card-body">
        <form action="{{ route('stocks.emoneys.update', $emoney) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Tanggal Diberikan</label>
                <input type="date" name="tanggal_diberikan" class="form-control" value="{{ old('tanggal_diberikan', $emoney->tanggal_diberikan ? $emoney->tanggal_diberikan->format('Y-m-d') : '') }}">
            </div>
            <div class="form-group">
                <label>Nomor Body <span style="color:red">*</span></label>
                <input type="text" name="nomor_body" class="form-control" value="{{ old('nomor_body', $emoney->nomor_body) }}" required>
            </div>
            <div class="form-group">
                <label>Nomor Kartu <span style="color:red">*</span></label>
                <input type="text" name="nomor_kartu" class="form-control" value="{{ old('nomor_kartu', $emoney->nomor_kartu) }}" required>
            </div>
            <div class="form-group">
                <label>Masa Aktif <span style="color:red">*</span></label>
                <input type="month" name="masa_aktif" class="form-control" value="{{ old('masa_aktif', $emoney->masa_aktif ? $emoney->masa_aktif->format('Y-m') : '') }}" required>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control">{{ old('keterangan', $emoney->keterangan) }}</textarea>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('stocks.emoneys.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
