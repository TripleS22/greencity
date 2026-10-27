@extends('layouts.app')

@section('title', 'Dashboard - Iuran Warga')

@section('styles')
<style>
    .saldo-card-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    .saldo-card {
        margin: 0 !important;
        width: 100%;
        max-width: 480px;
    }
    .quick-actions {
        display: flex;
        gap: 1rem;
        width: 100%;
        max-width: 480px;
    }
    .action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 1.25rem 2rem;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: var(--text);
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.2s;
        box-shadow: var(--shadow-sm);
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-light);
    }
    .action-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .action-btn.payment .action-icon {
        background: var(--primary-lighter);
        color: var(--primary);
    }
    .action-btn.expense .action-icon {
        background: var(--orange-light);
        color: var(--orange);
    }
    .matrix-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: var(--bg);
        border-bottom: 2px solid var(--border);
    }
    .month-grid {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .month-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 10px;
        background: var(--bg);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        border: 1px solid var(--border);
        min-width: 60px;
        justify-content: center;
        opacity: 0.7;
    }
    .month-badge.lunas {
        background: #d1fae5;
        color: #065f46;
        border-color: #a7f3d0;
        opacity: 1;
    }
    .month-badge.pending {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
        opacity: 1;
    }
    .month-badge.current {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px var(--primary-lighter);
        opacity: 1;
        /* Remove background: var(--white) to let .lunas/.pending show through */
    }
    .month-badge:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        opacity: 1;
    }
    .month-badge .icon-check {
        width: 14px;
        height: 14px;
    }
    @media (max-width: 768px) {
        .month-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 4px;
        }
        .month-badge {
            font-size: 0.65rem;
            padding: 4px 2px;
            min-width: unset;
            border-radius: 6px;
        }
        .month-badge .icon-check {
            width: 10px;
            height: 10px;
        }
    }
    /* Buku Kas Bulanan */
    .bk-month {
        border-bottom: 1px dashed var(--border);
    }
    .bk-month:last-child {
        border-bottom: none;
    }
    .bk-summary {
        display: grid;
        grid-template-columns: 90px 1fr auto auto 92px 20px;
        align-items: center;
        gap: 1.25rem;
        padding: 1rem 1.5rem;
        cursor: pointer;
        list-style: none;
        transition: background 0.2s;
    }
    .bk-summary::-webkit-details-marker {
        display: none;
    }
    .bk-summary:hover,
    .bk-month[open] .bk-summary {
        background: var(--bg);
    }
    .bk-bar {
        height: 8px;
        background: var(--border-light);
        border-radius: 40px;
        overflow: hidden;
    }
    .bk-bar > div {
        height: 100%;
        border-radius: 40px;
        transition: width 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .bk-num-label {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-muted);
    }
    .bk-num-value {
        font-weight: 800;
        font-size: 0.95rem;
        white-space: nowrap;
    }
    .bk-badge {
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 5px 0;
        border-radius: 40px;
        border: 1.5px solid;
        text-align: center;
    }
    .bk-badge.surplus {
        color: var(--primary);
        border-color: var(--primary-light);
        background: var(--primary-lightest);
    }
    .bk-badge.defisit {
        color: #dc2626;
        border-color: rgba(220, 38, 38, 0.35);
        background: #fef2f2;
    }
    .bk-chevron {
        width: 18px;
        height: 18px;
        color: var(--text-muted);
        transition: transform 0.2s;
    }
    .bk-month[open] .bk-chevron {
        transform: rotate(180deg);
    }
    .bk-body {
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 2rem;
        padding: 1.25rem 1.5rem 1.5rem;
        border-top: 1px solid var(--border);
    }
    .bk-section-title {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }
    .bk-kat-row {
        display: grid;
        grid-template-columns: 10px 92px 1fr auto;
        align-items: center;
        gap: 10px;
        margin-bottom: 0.85rem;
    }
    .bk-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
    }
    .bk-kat-nama {
        font-size: 0.82rem;
        font-weight: 600;
    }
    .bk-kat-bar {
        height: 6px;
        background: var(--border-light);
        border-radius: 40px;
        overflow: hidden;
    }
    .bk-kat-bar > div {
        height: 100%;
        border-radius: 40px;
    }
    .bk-kat-total {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-secondary);
        white-space: nowrap;
    }
    .bk-trx-head,
    .bk-trx-row {
        display: grid;
        grid-template-columns: 60px 1fr auto;
        gap: 12px;
        align-items: start;
    }
    .bk-trx-head {
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: var(--text-muted);
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border);
    }
    .bk-trx-row {
        padding: 10px 0;
        border-bottom: 1px dashed var(--border-light);
    }
    .bk-trx-row:last-child {
        border-bottom: none;
    }
    .bk-trx-tgl {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }
    .bk-trx-ket {
        font-size: 0.85rem;
        font-weight: 600;
    }
    .bk-trx-badge {
        display: inline-block;
        margin-top: 4px;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
    }
    .bk-trx-jml {
        font-size: 0.85rem;
        font-weight: 700;
        color: #dc2626;
        text-align: right;
        white-space: nowrap;
    }
    @media (max-width: 768px) {
        .bk-summary {
            grid-template-columns: 1fr auto auto;
            gap: 0.5rem 1rem;
        }
        .bk-bar,
        .bk-chevron {
            display: none;
        }
        .bk-badge {
            padding: 5px 10px;
        }
        .bk-body {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            padding: 1rem;
        }
    }
    @media (max-width: 768px) {
        .saldo-card-container {
            margin-bottom: 1.5rem;
            gap: 1rem;
        }
        .saldo-card {
            max-width: 100%;
        }
        .quick-actions {
            max-width: 100%;
        }
        .action-btn {
            padding: 1rem 1.25rem;
            font-size: 0.95rem;
            gap: 10px;
        }
        .action-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
        }
        .expense-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
        .expense-row {
            padding: 0.85rem 1rem;
        }
    }
    @media (max-width: 480px) {
        .action-btn {
            padding: 0.85rem 1rem;
            font-size: 0.88rem;
        }
        .action-icon {
            width: 38px;
            height: 38px;
        }
        .expense-name {
            font-size: 0.88rem;
        }
        .expense-amount {
            font-size: 0.88rem;
        }
    }
</style>
@endsection

@section('content')
{{-- Hero Section --}}
<div class="hero">
    <div style="margin-bottom: 1rem;">
        <img src="{{ asset('logogcr.png') }}" alt="Logo" style="height:80px; width:auto; object-fit:contain;">
    </div>
    <h1>Warga Bijak, Lingkungan Nyaman</h1>
    <p>Transparansi iuran lingkungan untuk masa depan yang lebih hijau</p>
</div>

{{-- Global Filter & Saldo --}}
<div class="saldo-card-container">
    <div style="width: 100%; max-width: 480px; display: flex; flex-direction: column; gap: 0.75rem; align-items: center;">
        <form action="{{ route('dashboard') }}" method="GET" style="display:flex; gap: 0.5rem; align-items: center; background: var(--bg); padding: 6px 12px; border-radius: 50px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); flex-wrap: wrap; justify-content: center;">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-left: 4px;">Tampilkan Data:</label>
            <select name="year" onchange="this.form.submit()" style="border: none; background: transparent; font-weight: 800; color: var(--primary); cursor: pointer; outline: none; padding-right: 20px;">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endforeach
            </select>
        </form>

        <div class="saldo-card" title="Pemasukan + Saldo Awal - Pengeluaran (Semua Tahun)">
            <div class="saldo-label">
                Saldo Saat Ini
                <span style="font-size: 0.65rem; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 4px; margin-left: 4px; font-weight: normal;">
                    Akumulasi
                </span>
            </div>
            <div class="saldo-amount">Rp {{ number_format($totalSaldoAllTime, 0, ',', '.') }}</div>
        </div>
        
        <div class="quick-actions" style="justify-content: center;">
            <button onclick="document.getElementById('modalBayarMandiri').classList.add('active')" class="action-btn payment" style="cursor:pointer; border:none; max-width: 380px; justify-content: center;">
                <div class="action-icon">
                    <i data-lucide="credit-card" style="width:26px;height:26px"></i>
                </div>
                <span>Bayar Iuran</span>
            </button>
        </div>
    </div>
</div>

{{-- Modal Bayar Mandiri --}}
<div class="modal-overlay" id="modalBayarMandiri">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem; position: relative;">
            <h2 style="font-size:1.25rem; font-weight:700; margin: 0;">Bayar Iuran Mandiri</h2>
            <button class="btn-icon" onclick="document.getElementById('modalBayarMandiri').classList.remove('active'); resetBtnBayar();" style="position: absolute; right: -10px; top: -10px; background: var(--bg); border: 1px solid var(--border); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-sm);">
                <i data-lucide="x" style="width:18px;height:18px; color: var(--text-secondary);"></i>
            </button>
        </div>
        
        <div id="step1">
            <div class="form-group">
                <label class="form-label">Cari Nama atau Unit Anda</label>
                <div style="position:relative;">
                    <i data-lucide="search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:18px; height:18px; color:var(--text-secondary);"></i>
                    <input type="text" id="wargaSearch" class="form-input" placeholder="Contoh: A1 atau Nama Anda..." style="padding-left:40px;">
                </div>
                <div id="wargaSearchResults" style="margin-top:10px; max-height:200px; overflow-y:auto; border:1px solid var(--border); border-radius:12px; display:none;">
                    @foreach($wargaList as $w)
                        <div class="warga-option" data-id="{{ $w->id }}" data-name="{{ $w->nama }}" data-unit="{{ $w->unit }}" 
                             style="padding:12px; cursor:pointer; border-bottom:1px solid var(--border); transition:background 0.2s;">
                            <div style="font-weight:600; font-size:0.95rem;">{{ $w->nama }}</div>
                            <div style="font-size:0.8rem; color:var(--text-secondary);">Unit: {{ $w->unit }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="step2" style="display:none;">
            <div style="background:var(--primary-lightest); padding:12px; border-radius:12px; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div id="selectedWargaName" style="font-weight:700; color:var(--primary);"></div>
                    <div id="selectedWargaUnit" style="font-size:0.85rem; color:var(--primary);"></div>
                </div>
                <div class="form-group" style="margin:0; width:120px;">
                    <select id="mandiriYear" class="form-input" style="height:36px; font-size:0.85rem; padding:0 8px;">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Pilih Bulan Pembayaran</label>
                <div id="monthLoadingStatus" style="display:none; text-align:center; padding:10px; font-size:0.85rem; color:var(--text-secondary);">
                    <i class="spin" data-lucide="loader-2" style="width:16px;height:16px;vertical-align:middle;"></i> Mengecek status...
                </div>
                <div id="monthSelectionContainer" style="display:grid; grid-template-columns:1fr 1fr; gap:8px; max-height:200px; overflow-y:auto; padding:5px;">
                    @php
                        $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    @endphp
                    @foreach($months as $m)
                        <label class="month-label" data-month="{{ $m }}" style="display:flex; align-items:center; justify-content:space-between; gap:8px; padding:8px; border:1px solid var(--border); border-radius:8px; cursor:auto; font-size:0.85rem; transition: all 0.2s; background: var(--bg);">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <input type="checkbox" name="mandiri_bulan[]" value="{{ $m }}" class="month-check" style="cursor:pointer;">
                                <span>{{ $m }}</span>
                            </div>
                            <span class="paid-badge" style="display:none; font-size:0.65rem; background:var(--primary); color:white; padding:2px 6px; border-radius:40px; font-weight:700;">LUNAS</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="margin-top:1.5rem; padding:15px; background:var(--bg); border-radius:12px; border: 1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">Total Bayar</div>
                    <div id="totalBayarMandiri" style="font-size:1.1rem; font-weight:800; color:var(--primary);">Rp 0</div>
                </div>
                <button id="btnProsesBayar" class="btn btn-primary" disabled>
                    Bayar Sekarang
                </button>
            </div>
            
            <button type="button" onclick="resetMandiri()" style="margin-top:1rem; background:none; border:none; color:var(--text-secondary); cursor:pointer; font-size:0.85rem;">
                <i data-lucide="arrow-left" style="width:14px;height:14px; vertical-align:middle;"></i> Kembali ke pencarian
            </button>
        </div>
    </div>
</div>

{{-- Stats Grid --}}
<div class="stats-grid">
    {{-- Total Pemasukan --}}
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon green">
                <i data-lucide="trending-up" style="width:24px;height:24px"></i>
            </div>
            <span class="stat-badge {{ $pemasukanChange >= 0 ? 'positive' : 'negative' }}">
                {{ $pemasukanChange >= 0 ? '+' : '' }}{{ $pemasukanChange }}%
            </span>
        </div>
        <div class="stat-label">Pemasukan Tahun {{ $currentYear }}</div>
        <div class="stat-value">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
    </div>

    {{-- Total Pengeluaran --}}
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon orange">
                <i data-lucide="clipboard-list" style="width:24px;height:24px"></i>
            </div>
            <span class="stat-badge {{ $pengeluaranChange <= 0 ? 'positive' : 'negative' }}">
                {{ $pengeluaranChange >= 0 ? '+' : '' }}{{ $pengeluaranChange }}%
            </span>
        </div>
        <div class="stat-label">Pengeluaran Tahun {{ $currentYear }}</div>
        <div class="stat-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
    </div>

    {{-- Warga Aktif --}}
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon blue">
                <i data-lucide="users" style="width:24px;height:24px"></i>
            </div>
        </div>
        <div class="stat-label">Warga Aktif</div>
        <div class="stat-value">{{ $wargaAktif }}</div>
        <div class="stat-sub">{{ $sudahBayar }} sudah bayar</div>
    </div>
</div>

{{-- Arus Kas Bulanan --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Arus Kas Tahunan ({{ $currentYear }})</h2>
    </div>
    <div class="chart-container">
        <canvas id="arusKasChart"></canvas>
    </div>
    <div class="chart-legend">
        <div class="legend-item">
            <div class="legend-dot" style="background: var(--primary)"></div>
            Pemasukan
        </div>
        <div class="legend-item">
            <div class="legend-dot" style="background: #f97316"></div>
            Pengeluaran
        </div>
    </div>
</div>

{{-- Status Pembayaran Warga --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="border-bottom: 1px solid var(--border); padding: 1.25rem 1.5rem;">
        <h2 class="card-title" style="margin:0;">Status Iuran Warga</h2>
    </div>
    
    {{-- Unified Filter Bar --}}
    <div style="background: var(--bg); border-bottom: 1px solid var(--border); padding: 1rem 1.5rem;">
        <form action="{{ route('dashboard') }}" method="GET" id="matrixFilterForm" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <div style="position: relative; flex: 1; min-width: 120px;">
                <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-secondary);"></i>
                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama warga..." class="form-input" style="padding-left: 32px; height: 38px; font-size: 0.88rem;">
            </div>
            
            <select name="unit_category" class="form-input" style="width: auto; min-width: 90px; height: 38px; font-size: 0.88rem; background-color: var(--white);" onchange="this.form.submit()">
                <option value="">Semua Blok</option>
                <option value="A" {{ request('unit_category') == 'A' ? 'selected' : '' }}>Blok A</option>
                <option value="B" {{ request('unit_category') == 'B' ? 'selected' : '' }}>Blok B</option>
                <option value="C" {{ request('unit_category') == 'C' ? 'selected' : '' }}>Blok C</option>
            </select>

            <select name="sort" class="form-input" style="width: auto; min-width: 90px; height: 38px; font-size: 0.88rem; background-color: var(--white);" onchange="this.form.submit()">
                <option value="unit" {{ request('sort', 'unit') == 'unit' ? 'selected' : '' }}>Urut Unit</option>
                <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Urut Nama</option>
            </select>

            <select name="direction" class="form-input" style="width: auto; min-width: 80px; height: 38px; font-size: 0.88rem; background-color: var(--white);" onchange="this.form.submit()">
                <option value="asc" {{ request('direction', 'asc') == 'asc' ? 'selected' : '' }}>A → Z</option>
                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Z → A</option>
            </select>

            <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 15px; font-size: 0.85rem;">
                Terapkan
            </button>
            
            @if(request()->anyFilled(['nama', 'unit_category', 'sort', 'direction']))
                <a href="{{ route('dashboard') }}" class="btn btn-outline" style="height: 38px; padding: 0 15px; font-size: 0.85rem;">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="table-wrapper" style="margin: 0; max-height: 500px; overflow-y: auto; position: relative;">
        <table class="matrix-table" style="border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th class="text-center">Unit</th>
                    <th>Nama</th>
                    <th>Status Iuran {{ $currentYear }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wargaList as $w)
                <tr>
                    <td class="text-center" style="color: var(--text-secondary); min-width: 80px;">{{ $w->unit }}</td>
                    <td style="font-weight:700; color: var(--text); min-width: 150px;">{{ $w->nama }}</td>
                    <td>
                        <div class="month-grid">
                            @foreach($monthsOfYear as $mId)
                                @php
                                    $pRecords = $w->pembayaran->where('bulan', $mId);
                                    $isLunas = $pRecords->where('status', 'lunas')->isNotEmpty();
                                    $isPending = !$isLunas && $pRecords->where('status', 'pending')->isNotEmpty();
                                    $monthName = explode(' ', $mId)[0];
                                    $isCurrent = $mId === $currentMonthId;
                                @endphp
                                <div class="month-badge {{ $isLunas ? 'lunas' : ($isPending ? 'pending' : '') }} {{ $isCurrent ? 'current' : '' }}" 
                                     title="{{ $mId }}: {{ $isLunas ? 'Lunas' : ($isPending ? 'Pending' : 'Belum Bayar') }}">
                                    {{ substr($monthName, 0, 3) }}
                                    @if($isLunas)
                                        <i data-lucide="check-circle-2" class="icon-check"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center" style="padding: 3rem; color: var(--text-muted);">
                        <i data-lucide="info" style="width:24px;height:24px;margin-bottom:0.5rem;opacity:0.5;"></i><br>
                        Data tidak ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Buku Kas Bulanan --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="border-bottom: 1px solid var(--border); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="card-title" style="margin:0;">Buku Kas Bulanan ({{ $currentYear }})</h2>
            <p style="font-size:0.82rem; color:var(--text-secondary); margin-top:4px;">Rincian transaksi tiap bulan</p>
        </div>
        <div style="display:flex; gap:1rem; align-items:center; font-size:0.78rem; color:var(--text-secondary);">
            <span style="display:flex; align-items:center; gap:6px;"><span style="width:10px; height:10px; border-radius:3px; background:var(--primary);"></span> Surplus</span>
            <span style="display:flex; align-items:center; gap:6px;"><span style="width:10px; height:10px; border-radius:3px; background:#dc2626;"></span> Defisit</span>
        </div>
    </div>

    @php
        $bkPalette = ['#166534','#3b82f6','#f97316','#eab308','#8b5cf6','#ec4899'];
        $bkBulanShort = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    @endphp

    @foreach($bukuKas as $bk)
        @php
            $isSurplus = $bk['masuk'] >= $bk['keluar'];
            $ratio = $bk['masuk'] > 0 ? min($bk['keluar'] / $bk['masuk'], 1) : ($bk['keluar'] > 0 ? 1 : 0);
            $maxKat = $bk['perKategori']->max() ?: 1;
            $katColors = [];
            foreach ($bk['perKategori']->keys() as $ki => $kn) {
                $katColors[$kn] = $bkPalette[$ki % 6];
            }
        @endphp
        <details class="bk-month">
            <summary class="bk-summary">
                <div>
                    <div style="font-weight:800; font-size:1rem;">{{ $bk['nama'] }}</div>
                    <div style="font-size:0.78rem; color:var(--text-secondary);">{{ $currentYear }}</div>
                </div>
                <div class="bk-bar"><div style="width:{{ round($ratio * 100) }}%; background:{{ $isSurplus ? 'var(--primary)' : '#dc2626' }};"></div></div>
                <div>
                    <div class="bk-num-label">Masuk</div>
                    <div class="bk-num-value" style="color:var(--primary);">Rp {{ number_format($bk['masuk'], 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="bk-num-label">Keluar</div>
                    <div class="bk-num-value" style="color:#dc2626;">Rp {{ number_format($bk['keluar'], 0, ',', '.') }}</div>
                </div>
                <span class="bk-badge {{ $isSurplus ? 'surplus' : 'defisit' }}">{{ $isSurplus ? 'SURPLUS' : 'DEFISIT' }}</span>
                <i data-lucide="chevron-down" class="bk-chevron"></i>
            </summary>
            <div class="bk-body">
                <div>
                    <div class="bk-section-title">Rincian per Kategori</div>
                    @forelse($bk['perKategori'] as $katNama => $katTotal)
                        <div class="bk-kat-row">
                            <span class="bk-dot" style="background:{{ $katColors[$katNama] }};"></span>
                            <span class="bk-kat-nama">{{ $katNama }}</span>
                            <div class="bk-kat-bar"><div style="width:{{ round($katTotal / $maxKat * 100) }}%; background:{{ $katColors[$katNama] }};"></div></div>
                            <span class="bk-kat-total">Rp {{ number_format($katTotal, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div style="font-size:0.85rem; color:var(--text-muted);">Belum ada pengeluaran bulan ini</div>
                    @endforelse
                </div>
                <div>
                    <div class="bk-section-title">Riwayat Transaksi</div>
                    @if($bk['transaksi']->isNotEmpty())
                        <div class="bk-trx-head">
                            <span>Tanggal</span>
                            <span>Keterangan</span>
                            <span style="text-align:right;">Jumlah</span>
                        </div>
                        @foreach($bk['transaksi'] as $trx)
                            @php $trxKat = $trx->kategori->nama ?? 'Lain-lain'; @endphp
                            <div class="bk-trx-row">
                                <span class="bk-trx-tgl">{{ str_pad($trx->tanggal->day, 2, '0', STR_PAD_LEFT) }} {{ $bkBulanShort[$trx->tanggal->month - 1] }}</span>
                                <div>
                                    <div class="bk-trx-ket">{{ $trx->keterangan }}</div>
                                    <span class="bk-trx-badge" style="background:{{ ($katColors[$trxKat] ?? '#6b7280') . '1a' }}; color:{{ $katColors[$trxKat] ?? '#6b7280' }};">{{ $trxKat }}</span>
                                </div>
                                <span class="bk-trx-jml">-Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @else
                        <div style="font-size:0.85rem; color:var(--text-muted);">Belum ada transaksi bulan ini</div>
                    @endif
                </div>
            </div>
        </details>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('arusKasChart').getContext('2d');
    const arusKas = @json($arusKas);

    const labels = arusKas.map(d => d.label);
    const pemasukan = arusKas.map(d => d.pemasukan);
    const pengeluaran = arusKas.map(d => d.pengeluaran);

    // Create gradient
    const gradientPemasukan = ctx.createLinearGradient(0, 0, 0, 280);
    gradientPemasukan.addColorStop(0, 'rgba(22, 101, 52, 0.3)');
    gradientPemasukan.addColorStop(1, 'rgba(22, 101, 52, 0.02)');

    const gradientPengeluaran = ctx.createLinearGradient(0, 0, 0, 280);
    gradientPengeluaran.addColorStop(0, 'rgba(249, 115, 22, 0.3)');
    gradientPengeluaran.addColorStop(1, 'rgba(249, 115, 22, 0.02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: pemasukan,
                    borderColor: '#166534',
                    backgroundColor: gradientPemasukan,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#166534',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Pengeluaran',
                    data: pengeluaran,
                    borderColor: '#f97316',
                    backgroundColor: gradientPengeluaran,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#f97316',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1a1a2e',
                    bodyColor: '#6b7280',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 10,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 12, family: 'Plus Jakarta Sans' },
                    },
                    border: { display: false },
                },
                y: {
                    grid: {
                        color: 'rgba(0,0,0,0.04)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11, family: 'Plus Jakarta Sans' },
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000).toFixed(0) + 'jt';
                            if (value >= 1000) return (value / 1000).toFixed(0) + 'rb';
                            return value;
                        }
                    },
                    border: { display: false },
                },
            },
        },
    });

    // --- Buku Kas Bulanan: accordion eksklusif dengan animasi smooth ---
    const bkMonths = document.querySelectorAll('details.bk-month');
    const BK_DURASI = 200;

    function bkAnimateOpen(det) {
        const body = det.querySelector('.bk-body');
        if (!body || det.dataset.animating) return;
        det.dataset.animating = '1';
        det.open = true;

        const tinggiAkhir = body.offsetHeight;
        const gaya = getComputedStyle(body);
        const padAtas = gaya.paddingTop;
        const padBawah = gaya.paddingBottom;

        body.style.overflow = 'hidden';
        body.style.height = '0px';
        body.style.paddingTop = '0px';
        body.style.paddingBottom = '0px';

        requestAnimationFrame(() => {
            body.style.transition = `height ${BK_DURASI}ms ease, padding-top ${BK_DURASI}ms ease, padding-bottom ${BK_DURASI}ms ease`;
            body.style.height = tinggiAkhir + 'px';
            body.style.paddingTop = padAtas;
            body.style.paddingBottom = padBawah;
        });

        setTimeout(() => {
            body.style.height = body.style.paddingTop = body.style.paddingBottom = '';
            body.style.transition = body.style.overflow = '';
            delete det.dataset.animating;
        }, BK_DURASI + 50);
    }

    function bkAnimateClose(det) {
        const body = det.querySelector('.bk-body');
        if (!body || det.dataset.animating) return;
        det.dataset.animating = '1';

        body.style.overflow = 'hidden';
        body.style.height = body.offsetHeight + 'px';

        requestAnimationFrame(() => {
            body.style.transition = `height ${BK_DURASI}ms ease, padding-top ${BK_DURASI}ms ease, padding-bottom ${BK_DURASI}ms ease`;
            body.style.height = '0px';
            body.style.paddingTop = '0px';
            body.style.paddingBottom = '0px';
        });

        setTimeout(() => {
            det.open = false;
            body.style.height = body.style.paddingTop = body.style.paddingBottom = '';
            body.style.transition = body.style.overflow = '';
            delete det.dataset.animating;
        }, BK_DURASI + 50);
    }

    // Jaga baris yang diklik tetap di posisi layar yang sama selama animasi
    function bkKunciPosisi(el) {
        const posAwal = el.getBoundingClientRect().top;
        const mulai = performance.now();
        function langkah(t) {
            const geser = el.getBoundingClientRect().top - posAwal;
            if (geser !== 0) window.scrollBy(0, geser);
            if (t - mulai < BK_DURASI + 100) requestAnimationFrame(langkah);
        }
        requestAnimationFrame(langkah);
    }

    bkMonths.forEach(det => {
        const summary = det.querySelector('summary');
        summary.addEventListener('click', function(e) {
            e.preventDefault();
            if (det.dataset.animating) return;

            bkKunciPosisi(summary);

            if (det.open) {
                bkAnimateClose(det);
                return;
            }

            bkMonths.forEach(other => {
                if (other !== det && other.open) bkAnimateClose(other);
            });
            bkAnimateOpen(det);
        });
    });

    // --- Logic Bayar Mandiri ---
    const searchInput = document.getElementById('wargaSearch');
    const resultsContainer = document.getElementById('wargaSearchResults');
    const options = document.querySelectorAll('.warga-option');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const monthChecks = document.querySelectorAll('.month-check');
    const totalDisplay = document.getElementById('totalBayarMandiri');
    const btnBayar = document.getElementById('btnProsesBayar');
    
    const mandiriYearSelect = document.getElementById('mandiriYear');
    const monthLoadingStatus = document.getElementById('monthLoadingStatus');
    const monthSelectionContainer = document.getElementById('monthSelectionContainer');
    
    let selectedWargaId = null;
    const iuranNominal = {{ \App\Models\Setting::getByKey('iuran_nominal', 180000) }};

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            let hasResults = false;
            
            if (term.length > 0) {
                options.forEach(opt => {
                    const name = opt.dataset.name.toLowerCase();
                    const unit = opt.dataset.unit.toLowerCase();
                    if (name.includes(term) || unit.includes(term)) {
                        opt.style.display = 'block';
                        hasResults = true;
                    } else {
                        opt.style.display = 'none';
                    }
                });
                resultsContainer.style.display = hasResults ? 'block' : 'none';
            } else {
                resultsContainer.style.display = 'none';
            }
        });

        options.forEach(opt => {
            opt.addEventListener('click', function() {
                selectedWargaId = this.dataset.id;
                document.getElementById('selectedWargaName').innerText = this.dataset.name;
                document.getElementById('selectedWargaUnit').innerText = 'Unit: ' + this.dataset.unit;
                step1.style.display = 'none';
                step2.style.display = 'block';
                fetchStatus();
            });
        });
    }

    async function fetchStatus() {
        if (!selectedWargaId) return;
        
        const year = mandiriYearSelect.value;
        monthLoadingStatus.style.display = 'block';
        monthSelectionContainer.style.opacity = '0.5';
        monthSelectionContainer.style.pointerEvents = 'none';
        
        try {
            const response = await fetch(`/pembayaran/status/${selectedWargaId}/${year}`);
            const data = await response.json();
            
            if (data.success) {
                const paidMonths = data.paid_months;
                
                document.querySelectorAll('.month-label').forEach(label => {
                    const month = label.dataset.month;
                    const checkbox = label.querySelector('.month-check');
                    const badge = label.querySelector('.paid-badge');
                    
                    checkbox.checked = false;
                    
                    if (paidMonths.includes(month)) {
                        label.style.background = 'var(--primary-lightest)';
                        label.style.borderColor = 'var(--primary-lighter)';
                        label.style.cursor = 'not-allowed';
                        label.style.pointerEvents = 'none'; // Prevent any clicks
                        checkbox.style.display = 'none';
                        checkbox.disabled = true; // Extra safety
                        badge.style.display = 'block';
                    } else {
                        label.style.background = 'var(--bg)';
                        label.style.borderColor = 'var(--border)';
                        label.style.cursor = 'pointer';
                        label.style.pointerEvents = 'auto';
                        checkbox.style.display = 'block';
                        checkbox.disabled = false;
                        badge.style.display = 'none';
                    }
                });
            }
        } catch (e) {
            console.error('Failed to fetch status', e);
        } finally {
            monthLoadingStatus.style.display = 'none';
            monthSelectionContainer.style.opacity = '1';
            monthSelectionContainer.style.pointerEvents = 'auto';
            updateTotal();
            lucide.createIcons();
        }
    }

    if (mandiriYearSelect) {
        mandiriYearSelect.addEventListener('change', fetchStatus);
    }

    function updateTotal() {
        const checkedCount = document.querySelectorAll('.month-check:checked').length;
        const total = checkedCount * iuranNominal;
        totalDisplay.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        btnBayar.disabled = checkedCount === 0;
    }

    monthChecks.forEach(ch => ch.addEventListener('change', updateTotal));

    window.resetMandiri = function() {
        selectedWargaId = null;
        step1.style.display = 'block';
        step2.style.display = 'none';
        searchInput.value = '';
        monthChecks.forEach(ch => ch.checked = false);
        updateTotal();
    }

    window.resetBtnBayar = function() {
        if (btnBayar) {
            btnBayar.disabled = true; // disabled sampai bulan dipilih lagi
            btnBayar.innerText = 'Bayar Sekarang';
        }
    }

    if (btnBayar) {
        btnBayar.addEventListener('click', async function() {
            const year = mandiriYearSelect.value;
            const selectedMonths = Array.from(document.querySelectorAll('.month-check:checked')).map(cb => cb.value + ' ' + year);
            
            btnBayar.disabled = true;
            btnBayar.innerHTML = '<i class="spin" data-lucide="loader-2"></i> Loading...';
            lucide.createIcons();

            try {
                const response = await fetch("{{ route('pembayaran.publicStore') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        warga_id: selectedWargaId,
                        bulan: selectedMonths
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                    btnBayar.disabled = false;
                    btnBayar.innerText = 'Bayar Sekarang';
                    lucide.createIcons();
                }
            } catch (error) {
                Swal.fire('Error', 'Terjadi kesalahan teknis. Coba lagi nanti.', 'error');
                btnBayar.disabled = false;
                btnBayar.innerText = 'Bayar Sekarang';
                lucide.createIcons();
            }
        });
    }

    // ─── Fix: Reset tombol Loading jika user kembali via browser Back (bfcache) ───
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // Halaman diambil dari bfcache (back/forward navigation)
            if (btnBayar) {
                btnBayar.disabled = false;
                btnBayar.innerText = 'Bayar Sekarang';
            }
        }
    });

    // Reset tombol saat modal ditutup
    const modalBayarMandiri = document.getElementById('modalBayarMandiri');
    if (modalBayarMandiri) {
        modalBayarMandiri.addEventListener('click', function(e) {
            if (e.target === this) {
                // Klik di luar modal
                if (btnBayar) {
                    btnBayar.disabled = false;
                    btnBayar.innerText = 'Bayar Sekarang';
                }
            }
        });
    }
});
</script>
@endsection
