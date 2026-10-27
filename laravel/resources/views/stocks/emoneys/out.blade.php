@extends('layouts.app')
@section('title', 'Bagikan E-Money')
@section('breadcrumb', 'Stock > E-Money > Bagikan')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-hand-holding-usd"></i> Bagikan E-Money</h1>
</div>

<div class="card" style="max-width:600px;margin:0 auto">
    <div class="card-body">
        
        <div style="background:#f8f9fa; border:1px solid #ddd; padding:15px; border-radius:8px; margin-bottom:20px;">
            <p style="margin:0 0 5px 0;"><strong>Nomor Body:</strong> {{ $emoney->nomor_body }}</p>
            <p style="margin:0 0 5px 0;"><strong>Nomor Kartu:</strong> {{ $emoney->nomor_kartu }}</p>
            <p style="margin:0;"><strong>Keterangan Awal:</strong> {{ $emoney->keterangan ?? '-' }}</p>
        </div>

        <form action="{{ route('stocks.emoneys.out.store', $emoney) }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Tanggal Diberikan <span style="color:red">*</span></label>
                <input type="date" name="tanggal_diberikan" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            
            <div class="form-group">
                <label>Penerima / Keterangan Tambahan Diberikan <span style="color:red">*</span></label>
                <textarea name="keterangan" class="form-control" placeholder="Contoh: Diberikan kepada Bpk. Sigit (Divisi IT)" required>{{ old('keterangan', $emoney->keterangan) }}</textarea>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px">
                <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Konfirmasi Bagikan</button>
                <a href="{{ route('stocks.emoneys.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
