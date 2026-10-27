@extends('layouts.app')

@section('title', 'Pembayaran Iuran - Iuran Warga')

@section('content')
<style>
    .dropdown { position: relative; display: inline-block; }
    .dropdown-menu { 
        display: none; 
        position: absolute; 
        right: 0; 
        top: 100%;
        background-color: var(--white); 
        min-width: 160px; 
        box-shadow: var(--shadow-lg); 
        border: 1px solid var(--border); 
        border-radius: 12px; 
        z-index: 100; 
        padding: 6px;
        margin-top: 4px;
        animation: slideUp 0.2s ease;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .dropdown.active .dropdown-menu { display: block; }
    .dropdown-item { 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        padding: 10px 12px; 
        color: var(--text); 
        text-decoration: none; 
        font-size: 0.85rem; 
        font-weight: 500;
        cursor: pointer; 
        border: none; 
        background: none; 
        width: 100%; 
        text-align: left; 
        border-radius: 8px;
        transition: all 0.2s;
    }
    .dropdown-item:hover { background-color: var(--primary-lightest); color: var(--primary); }
    .dropdown-item i { color: var(--text-secondary); }
    .dropdown-item:hover i { color: var(--primary); }
    .dropdown-item.text-danger:hover { background-color: var(--red-light); color: var(--red); }
    .dropdown-item.text-danger:hover i { color: var(--red); }
    
    /* Dropdown Up Transformation */
    .dropdown.dropdown-up .dropdown-menu {
        top: auto;
        bottom: 100%;
        margin-top: 0;
        margin-bottom: 8px;
    }

    /* Prevent clipping in table wrapper */
    .table-wrapper {
        overflow-x: auto;
        padding-bottom: 120px; /* Increased buffer */
        margin-bottom: -120px;
    }
    
    /* Ensure the table itself doesn't hide overflow if not scrolling */
    table {
        border-collapse: separate; /* Required for some overflow behaviors */
        border-spacing: 0;
    }
    
    .btn-icon-circle {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); background: var(--white);
        color: var(--text-secondary); cursor: pointer; transition: all 0.2s;
    }
    .btn-icon-circle:hover { background: var(--primary-lightest); border-color: var(--primary-light); color: var(--primary); }

    /* Bulk Action Bar */
    .bulk-bar {
        position: fixed;
        bottom: -120px;
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: #fff;
        padding: 12px 24px;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        display: flex;
        align-items: center;
        gap: 16px;
        z-index: 100;
        transition: bottom 0.3s ease, visibility 0.3s;
        max-width: 95vw;
        flex-wrap: wrap;
        justify-content: center;
        visibility: hidden;
    }
    .bulk-bar.active {
        bottom: 24px;
        visibility: visible;
    }
    .bulk-bar .bulk-count {
        font-weight: 700;
        font-size: 0.92rem;
        white-space: nowrap;
    }
    .bulk-bar .bulk-btn {
        padding: 8px 16px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .bulk-btn-danger {
        background: #ef4444;
        color: #fff;
    }
    .bulk-btn-danger:hover {
        background: #dc2626;
    }
    .bulk-btn-success {
        background: #22c55e;
        color: #fff;
    }
    .bulk-btn-success:hover {
        background: #16a34a;
    }
    .bulk-btn-outline {
        background: transparent;
        color: #94a3b8;
        border: 1px solid #475569;
    }
    .bulk-btn-outline:hover {
        background: #334155;
        color: #fff;
    }
    .bulk-checkbox {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
        cursor: pointer;
    }
    @media (max-width: 768px) {
        #modalBulkEdit .modal {
            max-width: 100% !important;
        }
        #modalBulkEdit .modal div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        .bulk-bar {
            padding: 10px 16px;
            gap: 8px;
            border-radius: 12px;
        }
        .bulk-bar .bulk-btn {
            padding: 8px 12px;
            font-size: 0.8rem;
        }
        .bulk-bar .bulk-count {
            font-size: 0.82rem;
        }
    }
</style>

<div class="page-header-responsive">
    <div>
        <h1 style="font-size:1.5rem; font-weight:700; color:var(--text);">Pembayaran Iuran</h1>
        <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:0.25rem;">Kelola tagihan dan pembayaran iuran warga</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('modalTambahPembayaran').classList.add('active')">
        <i data-lucide="plus" style="width:16px;height:16px"></i>
        Catat Pembayaran
    </button>
</div>

<div class="card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
    <form action="{{ route('pembayaran.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: flex-end;">
        <div class="form-group">
            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem;">Unit</label>
            <input type="text" name="unit" value="{{ request('unit') }}" class="form-input" placeholder="Cari unit..." style="height: 40px;">
        </div>
        <div class="form-group">
            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem;">Nama Warga</label>
            <input type="text" name="nama" value="{{ request('nama') }}" class="form-input" placeholder="Cari nama..." style="height: 40px;">
        </div>
        <div class="form-group">
            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem;">Bulan</label>
            <select name="bulan" class="form-input" style="height: 40px;">
                <option value="">Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $m)
                    <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem;">Tahun</label>
            <select name="tahun" class="form-input" style="height: 40px;">
                <option value="">Semua Tahun</option>
                @php $currentYear = date('Y'); @endphp
                @for($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="height: 40px; flex: 1;">
                <i data-lucide="filter" style="width:16px;height:16px"></i>
                Filter
            </button>
            <a href="{{ route('pembayaran.index') }}" class="btn btn-outline" style="height: 40px; flex: 1; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                <i data-lucide="rotate-ccw" style="width:16px;height:16px"></i>
                Reset
            </a>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:40px; text-align:center;">
                        <input type="checkbox" class="bulk-checkbox" id="selectAll" title="Pilih Semua">
                    </th>
                    <th>#</th>
                    <th>Unit</th>
                    <th>Nama Warga</th>
                    <th>Bulan</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Metode</th>
                    <th>Tanggal Bayar</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayaran as $p)
                <tr>
                    <td style="text-align:center;">
                        <input type="checkbox" class="bulk-checkbox row-checkbox" value="{{ $p->id }}">
                    </td>
                    <td style="color:var(--text-muted);">{{ $loop->iteration }}</td>
                    <td style="color:var(--text-secondary);">{{ $p->warga->unit ?? '-' }}</td>
                    <td style="font-weight:500;">{{ $p->warga->nama ?? '-' }}</td>
                    <td style="color:var(--text-secondary);">{{ $p->bulan }}</td>
                    <td style="font-weight:600;">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    <td>
                        @if($p->status === 'lunas')
                            <span class="badge badge-success">Lunas</span>
                        @elseif($p->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-danger">Belum Lunas</span>
                        @endif
                    </td>
                    <td style="color:var(--text-secondary); text-transform:capitalize;">{{ $p->metode_bayar ?? '-' }}</td>
                    <td style="color:var(--text-secondary);">
                        {{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d/m/Y') : '-' }}
                    </td>
                    <td style="text-align: right;">
                        <div class="dropdown {{ ($loop->remaining < 3 && $loop->count > 5) ? 'dropdown-up' : '' }}">
                            <button type="button" class="btn-icon-circle dropdown-trigger" title="Opsi">
                                <i data-lucide="more-horizontal" style="width:18px;height:18px"></i>
                            </button>
                            <div class="dropdown-menu">
                                @if($p->status !== 'lunas')
                                    <a href="{{ route('pembayaran.bayar', $p) }}" class="dropdown-item">
                                        <i data-lucide="qr-code" style="width:16px;height:16px"></i> Bayar Online (QRIS)
                                    </a>
                                    <form action="{{ route('pembayaran.manual', $p) }}" method="POST" style="display:contents;">
                                        @csrf
                                        <button type="submit" class="dropdown-item" onclick="return confirm('Konfirmasi pembayaran manual untuk {{ $p->warga->nama }}?')">
                                            <i data-lucide="check-circle" style="width:16px;height:16px"></i> Bayar Manual
                                        </button>
                                    </form>
                                @endif

                                <button type="button" class="dropdown-item btn-edit-pembayaran" 
                                        data-id="{{ $p->id }}"
                                        data-warga="{{ $p->warga->nama }}"
                                        data-bulan="{{ $p->bulan }}"
                                        data-jumlah="{{ (int)$p->jumlah }}"
                                        data-status="{{ $p->status }}"
                                        data-metode="{{ $p->metode_bayar }}"
                                        data-catatan="{{ $p->catatan }}">
                                    <i data-lucide="edit-2" style="width:16px;height:16px"></i> Edit Data
                                </button>

                                <div style="height: 1px; background: var(--border-light); margin: 4px 0;"></div>

                                <form action="{{ route('pembayaran.destroy', $p) }}" method="POST" class="delete-form" style="display:contents;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger delete-btn-premium">
                                        <i data-lucide="trash-2" style="width:16px;height:16px"></i> Hapus Catatan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding:2rem; color:var(--text-muted);">
                        Belum ada data pembayaran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pembayaran->hasPages())
        <div class="pagination">
            {{ $pembayaran->links() }}
        </div>
    @endif
</div>

{{-- Floating Bulk Action Bar --}}
<div class="bulk-bar" id="bulkBar">
    <span class="bulk-count"><span id="bulkCount">0</span> dipilih</span>
    <button type="button" class="bulk-btn" id="bulkQrisBtn" style="background:#7c3aed;color:#fff;" title="Bayar semua yang dipilih via QRIS (harus 1 warga yang sama)">
        <i data-lucide="qr-code" style="width:16px;height:16px"></i>
        Bayar QRIS
    </button>
    <button type="button" class="bulk-btn" id="bulkEditBtn" style="background:#3b82f6;color:#fff;">
        <i data-lucide="edit-2" style="width:16px;height:16px"></i>
        Edit
    </button>
    <button type="button" class="bulk-btn bulk-btn-danger" id="bulkDeleteBtn">
        <i data-lucide="trash-2" style="width:16px;height:16px"></i>
        Hapus
    </button>
    <button type="button" class="bulk-btn bulk-btn-outline" id="bulkCancelBtn">
        Batal
    </button>
</div>

{{-- Hidden Bulk Forms --}}
<form id="bulkDeleteForm" method="POST" action="{{ route('pembayaran.bulkDelete') }}" style="display:none;">
    @csrf
    <div id="bulkDeleteIds"></div>
</form>
<form id="bulkStatusForm" method="POST" action="{{ route('pembayaran.bulkUpdateStatus') }}" style="display:none;">
    @csrf
    <input type="hidden" name="status" id="bulkStatusValue">
    <div id="bulkStatusIds"></div>
</form>
<form id="bulkQrisForm" method="POST" action="{{ route('pembayaran.bulkBayarQris') }}" style="display:none;">
    @csrf
    <div id="bulkQrisIds"></div>
</form>

{{-- Modal Bulk Edit --}}
<div class="modal-overlay" id="modalBulkEdit">
    <div class="modal" style="max-width: 480px;">
        <div class="modal-title" style="display:flex; align-items:center; gap:8px; margin-bottom:1.25rem;">
            <i data-lucide="edit-3" style="width:20px;height:20px;color:var(--primary)"></i>
            Edit Massal — <span id="bulkEditCount" style="color:var(--primary)">0</span> data
        </div>
        <form id="formBulkEdit" method="POST" action="{{ route('pembayaran.bulkUpdate') }}">
            @csrf
            <div id="bulkEditIdsContainer"></div>
            <div class="form-group">
                <label class="form-label">Bulan & Tahun <span style="font-weight:400;color:var(--text-muted);font-size:0.78rem;">— kosongkan jika tidak diubah</span></label>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <select name="bulan_nama" class="form-input">
                        <option value="">— Jangan ubah —</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                    <select name="bulan_tahun" class="form-input">
                        <option value="">— Jangan ubah —</option>
                        @php $cy = date('Y'); @endphp
                        @for($y = $cy - 2; $y <= $cy + 2; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input" required>
                        <option value="lunas">Lunas</option>
                        <option value="belum_lunas">Belum Lunas</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Metode</label>
                    <select name="metode_bayar" class="form-input">
                        <option value="">— Jangan ubah —</option>
                        <option value="manual">Manual</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris_doitpay">QRIS Doitpay</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah (Rp) <span style="font-weight:400;color:var(--text-muted);font-size:0.78rem;">— kosongkan jika tidak diubah</span></label>
                <input type="number" name="jumlah" class="form-input" min="0" step="1000" placeholder="Kosongkan jika tidak diubah">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Bayar <span style="font-weight:400;color:var(--text-muted);font-size:0.78rem;">— kosongkan jika tidak diubah</span></label>
                <input type="date" name="tanggal_bayar" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-input" rows="2" placeholder="Opsional"></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalBulkEdit').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Pembayaran --}}
<div class="modal-overlay" id="modalTambahPembayaran">
    <div class="modal" style="max-width: 550px;">
        <div class="modal-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem; position: relative;">
            <h2 class="modal-title" style="margin: 0; font-size: 1.25rem;">Catat Pembayaran</h2>
            <button class="btn-icon" onclick="document.getElementById('modalTambahPembayaran').classList.remove('active')" style="position: absolute; right: -10px; top: -10px; background: var(--bg); border: 1px solid var(--border); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-sm); transition: all 0.2s;">
                <i data-lucide="x" style="width:18px;height:18px; color: var(--text-secondary);"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div style="background: var(--primary-lightest); border: 1px dashed var(--primary-lighter); padding: 12px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
                <div style="background: var(--primary); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="info" style="width:14px;height:14px;"></i>
                </div>
                <span style="font-size:0.85rem; color:var(--primary); font-weight:700;">
                    Iuran Flat: Rp {{ number_format($iuranNominal, 0, ',', '.') }} / bulan
                </span>
            </div>

            <form action="{{ route('pembayaran.store') }}" method="POST">
                @csrf
                {{-- Resident Selection --}}
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.5rem;">
                        <span style="font-weight: 700;">Pilih Warga</span>
                        <label style="font-size:0.75rem; color:var(--primary); cursor:pointer; font-weight:600; display: flex; align-items: center; gap: 4px;">
                            <input type="checkbox" id="selectAllWarga" style="width: 14px; height: 14px; accent-color: var(--primary);"> Pilih Semua
                        </label>
                    </label>
                    
                    <div style="position: relative; margin-bottom: 0.75rem;">
                        <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted);"></i>
                        <input type="text" id="searchWargaModal" class="form-input" placeholder="Cari nama atau unit..." 
                               style="padding-left: 36px; font-size: 0.85rem; height: 40px; border-radius: 8px;">
                    </div>

                    <div id="wargaListContainer" style="max-height: 180px; overflow-y: auto; border: 1px solid var(--border); border-radius: 12px; padding: 6px; background: var(--bg);">
                        @foreach($warga as $w)
                            <div class="warga-item" data-id="{{ $w->id }}" data-name="{{ strtolower($w->nama) }}" data-unit="{{ strtolower($w->unit) }}" style="padding: 6px; border-radius: 8px; transition: all 0.2s; border-bottom: 1px solid var(--border-light);">
                                <label style="display:flex; align-items:center; cursor:pointer; width:100%; margin:0; gap: 12px; padding: 4px;">
                                    <input type="checkbox" name="warga_id[]" value="{{ $w->id }}" class="warga-checkbox" style="width: 16px; height: 16px; accent-color: var(--primary);">
                                    <div style="display:flex; flex-direction:column;">
                                        <span style="font-size:0.9rem; font-weight:600; color: var(--text);">{{ $w->nama }}</span>
                                        <span style="font-size:0.75rem; color:var(--text-secondary);">Unit: {{ $w->unit }}</span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                        <div id="noWargaFound" style="display:none; text-align:center; padding: 20px; color: var(--text-secondary); font-size: 0.85rem;">
                             Warga tidak ditemukan
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                        <label class="form-label" style="font-weight: 700; margin:0;">Pilih Bulan</label>
                        <select id="adminYear" name="year" class="form-input" style="width:100px; height:32px; font-size:0.8rem; padding:0 8px;">
                            @php $currentYear = date('Y'); @endphp
                            <option value="{{ $currentYear - 1 }}">{{ $currentYear - 1 }}</option>
                            <option value="{{ $currentYear }}" selected>{{ $currentYear }}</option>
                            <option value="{{ $currentYear + 1 }}">{{ $currentYear + 1 }}</option>
                        </select>
                    </div>
                    <div id="adminMonthLoading" style="display:none; text-align:center; padding:5px; font-size:0.75rem; color:var(--text-secondary);">
                        <i class="spin" data-lucide="loader-2" style="width:14px;height:14px;vertical-align:middle;"></i> Mengecek tunggakan...
                    </div>
                    <div id="adminMonthContainer" style="max-height: 160px; overflow-y: auto; border: 1px solid var(--border); border-radius: 12px; padding: 12px; background: var(--bg); display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        @php
                            $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        @endphp
                        @foreach($bulanList as $b)
                            <label class="admin-month-label" data-month="{{ $b }}" style="display:flex; align-items:center; justify-content:space-between; gap:10px; font-size:0.85rem; cursor:pointer; padding: 4px; border-radius: 6px; border: 1px solid transparent;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox" name="bulan[]" value="{{ $b }}" class="admin-month-check" style="width: 15px; height: 15px; accent-color: var(--primary);">
                                    <span style="font-weight: 500;">{{ $b }}</span>
                                </div>
                                <span class="admin-paid-badge" style="display:none; font-size:0.6rem; background:var(--primary); color:white; padding:2px 6px; border-radius:40px; font-weight:700;">LUNAS</span>
                            </label>
                        @endforeach
                    </div>
                    <p id="multiWargaNote" style="display:none; font-size:0.75rem; color:var(--text-secondary); margin-top:0.5rem; font-style:italic;">
                        * Status lunas tidak ditampilkan jika memilih lebih dari 1 warga.
                    </p>
                </div>

                <div class="form-actions" style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambahPembayaran').classList.remove('active')" style="padding: 10px 20px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 700;">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Pembayaran --}}
<div class="modal-overlay" id="modalEditPembayaran">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem; position: relative;">
            <h2 class="modal-title" style="margin: 0; font-size: 1.25rem;">Edit Pembayaran</h2>
            <button class="btn-icon" onclick="document.getElementById('modalEditPembayaran').classList.remove('active')" style="position: absolute; right: -10px; top: -10px; background: var(--bg); border: 1px solid var(--border); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-sm);">
                <i data-lucide="x" style="width:18px;height:18px; color: var(--text-secondary);"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div id="editWargaName" style="font-weight: 700; color: var(--primary); margin-bottom: 1rem; font-size: 1.1rem;"></div>
            
            <form id="formEditPembayaran" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label">Bulan</label>
                    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:10px;">
                        <select id="edit_month_select" class="form-input" required>
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $month)
                                <option value="{{ $month }}">{{ $month }}</option>
                            @endforeach
                        </select>
                        <select id="edit_year_select" class="form-input" required>
                             @php $currYear = date('Y'); @endphp
                             @for($y = $currYear - 3; $y <= $currYear + 2; $y++)
                                <option value="{{ $y }}" {{ $y == $currYear ? 'selected' : '' }}>{{ $y }}</option>
                             @endfor
                        </select>
                    </div>
                    <input type="hidden" name="bulan" id="edit_bulan_hidden">
                </div>
                
                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label">Jumlah (Rp)</label>
                    <input type="number" name="jumlah" id="edit_jumlah" class="form-input" required>
                </div>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:1rem;">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-input">
                            <option value="lunas">Lunas</option>
                            <option value="pending">Pending</option>
                            <option value="belum_lunas">Belum Lunas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Metode</label>
                        <select name="metode_bayar" id="edit_metode" class="form-input">
                            <option value="manual">Manual</option>
                            <option value="transfer">Transfer</option>
                            <option value="qris_doitpay">QRIS Doitpay</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" id="edit_catatan" class="form-input" style="height:80px;"></textarea>
                </div>

                <div class="form-actions" style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEditPembayaran').classList.remove('active')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Resident Search in Modal
const searchInput = document.getElementById('searchWargaModal');
const wargaItems = document.querySelectorAll('.warga-item');
const noWargaFound = document.getElementById('noWargaFound');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        let found = false;
        
        wargaItems.forEach(item => {
            const name = item.getAttribute('data-name');
            const unit = item.getAttribute('data-unit');
            
            // Unit matches if it STARTS with the term (prefix match)
            const isUnitPrefixMatch = unit.startsWith(term);
            
            // Name matching: 
            // If term is only 1 char, only match if it's the START of a word in the name
            // (prevents "a" from matching "Pak [Name]" just because of the 'a' in 'pak')
            let isNameMatch = false;
            if (term.length === 1) {
                isNameMatch = name.startsWith(term) || name.split(' ').some(word => word.startsWith(term));
            } else {
                isNameMatch = name.includes(term);
            }
            
            // Smart boundary: if term ends in a number, don't match if unit has more numbers
            // e.g. "a1" matches "a1" but NOT "a10"
            let smartUnitMatch = isUnitPrefixMatch;
            if (isUnitPrefixMatch && /\d$/.test(term)) {
                const nextChar = unit.charAt(term.length);
                if (/\d/.test(nextChar)) {
                    smartUnitMatch = false;
                }
            }
            
            if (isNameMatch || smartUnitMatch) {
                item.style.display = 'block';
                found = true;
            } else {
                item.style.display = 'none';
            }
        });
        
        noWargaFound.style.display = found ? 'none' : 'block';
    });
}

// Select All Warga
const selectAllCheckbox = document.getElementById('selectAllWarga');
const wargaCheckboxes = document.querySelectorAll('.warga-checkbox');
const adminYearSelect = document.getElementById('adminYear');
const adminMonthLoading = document.getElementById('adminMonthLoading');
const adminMonthContainer = document.getElementById('adminMonthContainer');
const multiWargaNote = document.getElementById('multiWargaNote');

async function checkAdminStatus() {
    const checkedWarga = document.querySelectorAll('.warga-checkbox:checked');
    
    // Reset all labels first
    document.querySelectorAll('.admin-month-label').forEach(label => {
        label.style.background = 'transparent';
        label.style.borderColor = 'transparent';
        label.style.cursor = 'pointer';
        label.querySelector('.admin-month-check').style.display = 'block';
        label.querySelector('.admin-paid-badge').style.display = 'none';
    });
    multiWargaNote.style.display = 'none';

    if (checkedWarga.length === 1) {
        const wargaId = checkedWarga[0].value;
        const year = adminYearSelect.value;
        
        adminMonthLoading.style.display = 'block';
        adminMonthContainer.style.opacity = '0.5';
        adminMonthContainer.style.pointerEvents = 'none';
        
        try {
            const response = await fetch(`/pembayaran/status/${wargaId}/${year}`);
            const data = await response.json();
            
            if (data.success) {
                const paidMonths = data.paid_months;
                document.querySelectorAll('.admin-month-label').forEach(label => {
                    const month = label.dataset.month;
                    const checkbox = label.querySelector('.admin-month-check');
                    const badge = label.querySelector('.admin-paid-badge');
                    
                    if (paidMonths.includes(month)) {
                        label.style.background = 'var(--primary-lightest)';
                        label.style.borderColor = 'var(--primary-lighter)';
                        label.style.cursor = 'not-allowed';
                        checkbox.checked = false;
                        checkbox.style.display = 'none';
                        badge.style.display = 'block';
                    }
                });
            }
        } catch (e) {
            console.error(e);
        } finally {
            adminMonthLoading.style.display = 'none';
            adminMonthContainer.style.opacity = '1';
            adminMonthContainer.style.pointerEvents = 'auto';
        }
    } else if (checkedWarga.length > 1) {
        multiWargaNote.style.display = 'block';
    }
}

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        wargaCheckboxes.forEach(cb => {
            if (cb.closest('.warga-item').style.display !== 'none') {
                cb.checked = isChecked;
            }
        });
        checkAdminStatus();
    });
}

wargaCheckboxes.forEach(cb => {
    cb.addEventListener('change', checkAdminStatus);
});

if (adminYearSelect) {
    adminYearSelect.addEventListener('change', checkAdminStatus);
}

// JS Dropdown
document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const parent = this.parentElement;
        const isActive = parent.classList.contains('active');
        
        // Close all other dropdowns
        document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
        
        if (!isActive) parent.classList.add('active');
    });
});

document.addEventListener('click', function() {
    document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
});

// Edit Pembayaran
const modalEdit = document.getElementById('modalEditPembayaran');
const formEdit = document.getElementById('formEditPembayaran');
const editWargaName = document.getElementById('editWargaName');

document.querySelectorAll('.btn-edit-pembayaran').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.warga;
        const bulan = this.dataset.bulan; // e.g. "Januari 2026"
        const parts = bulan.split(' ');
        if (parts.length === 2) {
            document.getElementById('edit_month_select').value = parts[0];
            document.getElementById('edit_year_select').value = parts[1];
            document.getElementById('edit_bulan_hidden').value = bulan;
        }

        editWargaName.innerText = name;
        document.getElementById('edit_jumlah').value = this.dataset.jumlah;
        document.getElementById('edit_status').value = this.dataset.status;
        document.getElementById('edit_metode').value = this.dataset.metode || 'manual';
        document.getElementById('edit_catatan').value = this.dataset.catatan || '';

        formEdit.action = `/pembayaran/${id}`;
        modalEdit.classList.add('active');
    });
});

// Update hidden bulan before submit
formEdit.addEventListener('submit', function() {
    const m = document.getElementById('edit_month_select').value;
    const y = document.getElementById('edit_year_select').value;
    document.getElementById('edit_bulan_hidden').value = m + ' ' + y;
});

// Delete Confirmation Premium (SweetAlert2)
document.querySelectorAll('.delete-btn-premium').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('form');
        Swal.fire({
            title: 'Hapus Catatan?',
            text: "Data pembayaran ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            border: 'none',
            borderRadius: '16px'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

// Success Alert (SweetAlert2)
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false,
        borderRadius: '16px'
    });
@endif

// Error Alert (SweetAlert2)
@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        html: `<ul style="text-align:left; font-size:0.9rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
               </ul>`,
        borderRadius: '16px'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Perhatian',
        text: '{{ session('error') }}',
        timer: 4000,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        borderRadius: '16px'
    });
@endif

// Close modal on click outside
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});

// ===== Bulk Action Logic =====
const selectAll = document.getElementById('selectAll');
const rowCheckboxes = document.querySelectorAll('.row-checkbox');
const bulkBar = document.getElementById('bulkBar');
const bulkCount = document.getElementById('bulkCount');

function getSelectedIds() {
    return Array.from(rowCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
}

function updateBulkBar() {
    const selected = getSelectedIds();
    bulkCount.textContent = selected.length;
    if (selected.length > 0) {
        bulkBar.classList.add('active');
    } else {
        bulkBar.classList.remove('active');
    }
    // Update selectAll state
    if (rowCheckboxes.length > 0) {
        selectAll.checked = selected.length === rowCheckboxes.length;
        selectAll.indeterminate = selected.length > 0 && selected.length < rowCheckboxes.length;
    }
}

if (selectAll) {
    selectAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkBar();
    });
}

rowCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkBar);
});

// Cancel selection
document.getElementById('bulkCancelBtn').addEventListener('click', function() {
    rowCheckboxes.forEach(cb => cb.checked = false);
    if (selectAll) selectAll.checked = false;
    updateBulkBar();
});

// Bulk Delete
document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
    const ids = getSelectedIds();
    Swal.fire({
        title: 'Hapus ' + ids.length + ' Data?',
        text: 'Data pembayaran yang dipilih akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const container = document.getElementById('bulkDeleteIds');
            container.innerHTML = '';
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                container.appendChild(input);
            });
            document.getElementById('bulkDeleteForm').submit();
        }
    });
});

// Bulk Edit — open modal
document.getElementById('bulkEditBtn').addEventListener('click', function() {
    const ids = getSelectedIds();
    document.getElementById('bulkEditCount').textContent = ids.length;
    const container = document.getElementById('bulkEditIdsContainer');
    container.innerHTML = '';
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        container.appendChild(input);
    });
    document.getElementById('modalBulkEdit').classList.add('active');
    lucide.createIcons();
});

// Bulk Bayar QRIS
document.getElementById('bulkQrisBtn').addEventListener('click', function() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;

    Swal.fire({
        title: 'Bayar ' + ids.length + ' Tagihan via QRIS?',
        html: `<p style="font-size:0.9rem;color:#64748b;">Total pembayaran akan dijumlahkan dan dibuat 1 kode QRIS.<br><br>
               <strong>⚠️ Catatan:</strong> Semua tagihan yang dipilih harus milik <strong>1 warga yang sama</strong>.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fa fa-qrcode"></i> Buat QRIS Sekarang',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const container = document.getElementById('bulkQrisIds');
            container.innerHTML = '';
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                container.appendChild(input);
            });
            document.getElementById('bulkQrisForm').submit();
        }
    });
});
</script>
@endsection
