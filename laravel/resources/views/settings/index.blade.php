@extends('layouts.app')

@section('title', 'Pengaturan - Iuran Warga')

@section('content')
<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem; font-weight:700; color:var(--text);">Pengaturan Sistem</h1>
    <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:0.25rem;">Kelola biaya iuran dan konfigurasi aplikasi lainnya.</p>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="banknote" style="width:16px;height:16px;color:var(--primary)"></i>
                Nominal Iuran Bulanan (Rp)
            </label>
            <input type="number" name="iuran_nominal" class="form-input" value="{{ $iuranNominal }}" required min="0" step="1000">
            <p style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.5rem;">
                Nilai ini akan digunakan sebagai biaya default saat mencatat pembayaran admin maupun bayar mandiri warga.
            </p>
        </div>

        <div class="form-group" style="margin-top:1.5rem;">
            <label class="form-label" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="wallet" style="width:16px;height:16px;color:var(--primary)"></i>
                Total Saldo Kas Saat Ini (Rp)
            </label>
            <input type="number" name="total_saldo" class="form-input" value="{{ $totalSaldo }}" required step="1000">
            <p style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.5rem;">
                Jika Anda mengubah nilai ini, sistem akan otomatis menyesuaikan Saldo Awal agar total kas sesuai dengan angka yang Anda masukkan.
            </p>
        </div>

        <div class="form-group" style="margin-top:1.5rem; display:none;">
            <label class="form-label" style="display:flex; align-items:center; gap:8px;">
                <i data-lucide="piggy-bank" style="width:16px;height:16px;color:var(--primary)"></i>
                Saldo Awal (Hidden)
            </label>
            <input type="number" name="saldo_awal" id="saldo_awal_input" class="form-input" value="{{ $saldoAwal }}" step="1000">
        </div>

        <div style="margin-top:2rem; border-top:1px solid var(--border); padding-top:1.5rem;">
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                <i data-lucide="save" style="width:18px;height:18px"></i>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
