@extends('layouts.app')
@section('title', 'Bagikan E-Money (Bulk)')
@section('breadcrumb', 'Stock > E-Money > Bagikan (Multiple)')

@push('styles')
<style>
    .item-list {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 15px;
        margin-bottom: 20px;
        max-height: 200px;
        overflow-y: auto;
    }
    .item-list ul {
        margin: 0;
        padding-left: 20px;
    }
    .item-list li {
        margin-bottom: 5px;
        font-size: 13px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-hand-holding-usd"></i> Bagikan E-Money (Multiple)</h1>
</div>

<div class="card" style="max-width:700px;margin:0 auto">
    <div class="card-body">
        
        <div class="alert alert-info">
            Anda akan membagikan <strong>{{ $emoneys->count() }}</strong> kartu E-Money ke pengguna.
        </div>

        <div class="item-list">
            <ul>
                @foreach($emoneys as $emoney)
                    <li><strong>No. Body:</strong> {{ $emoney->nomor_body }} &mdash; <strong>No. Kartu:</strong> {{ $emoney->nomor_kartu }}</li>
                @endforeach
            </ul>
        </div>

        <form action="{{ route('stocks.emoneys.bulk_out.process') }}" method="POST">
            @csrf
            
            {{-- Hidden input array for all selected IDs --}}
            @foreach($emoneys as $emoney)
                <input type="hidden" name="ids[]" value="{{ $emoney->id }}">
            @endforeach

            <div class="form-group">
                <label>Tanggal Diberikan <span style="color:red">*</span></label>
                <input type="date" name="tanggal_diberikan" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            
            <div class="form-group">
                <label>Penerima / Keterangan Tambahan <span style="color:red">*</span></label>
                <textarea name="keterangan" class="form-control" placeholder="Contoh: Diberikan kepada karyawan pabrik batch 1" required></textarea>
                <small class="form-text text-muted" style="display:block;margin-top:5px;font-size:11px;">
                    Catatan ini akan ditambahkan *(append)* ke masing-masing kartu E-Money tanpa menghapus catatan lamanya.
                </small>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px">
                <button type="submit" class="btn btn-primary"><i class="fas fa-check-double"></i> Konfirmasi Bagikan Semua</button>
                <a href="{{ route('stocks.emoneys.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
