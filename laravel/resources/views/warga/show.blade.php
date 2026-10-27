@extends('layouts.app')

@section('title', 'Riwayat Pembayaran - ' . $warga->nama)

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
        animation: slideUp 0.15s ease;
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
    
    .btn-icon-circle {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); background: var(--white);
        color: var(--text-secondary); cursor: pointer; transition: all 0.2s;
    }
    .btn-icon-circle:hover { background: var(--primary-lightest); border-color: var(--primary-light); color: var(--primary); }

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
        padding-bottom: 100px; /* Buffer for dropdown */
        margin-bottom: -100px;
    }
    
    table {
        border-collapse: separate;
        border-spacing: 0;
    }
</style>

<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('warga.index') }}" style="text-decoration: none; color: var(--text-secondary); font-size: 0.9rem; display: flex; align-items: center; gap: 4px; margin-bottom: 1rem;">
        <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
        Kembali ke Data Warga
    </a>
    <div style="display:flex; align-items:flex-end; justify-content:space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size:1.5rem; font-weight:700; color:var(--text);">Riwayat Pembayaran</h1>
            <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:0.25rem;">
                {{ $warga->nama }} (Unit: {{ $warga->unit }})
            </p>
        </div>
        
        <form action="{{ route('warga.show', $warga) }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center; background: var(--white); padding: 8px 16px; border-radius: 50px; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">Tahun:</label>
            <select name="year" onchange="this.form.submit()" style="border: none; background: transparent; font-weight: 800; color: var(--primary); cursor: pointer; outline: none; padding-right: 15px;">
                <option value="">Semua Tahun</option>
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
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
                    <td style="font-weight:500;">{{ $p->bulan }}</td>
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
                        <div class="dropdown">
                            <button type="button" class="btn-icon-circle dropdown-trigger" title="Opsi">
                                <i data-lucide="more-horizontal" style="width:18px;height:18px"></i>
                            </button>
                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item btn-edit-pembayaran" 
                                        data-id="{{ $p->id }}"
                                        data-warga="{{ $warga->nama }}"
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
                                        <i data-lucide="trash-2" style="width:16px;height:16px"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding:2rem; color:var(--text-muted);">
                        Belum ada riwayat pembayaran
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
                            <option value="midtrans">Midtrans</option>
                            <option value="transfer">Transfer</option>
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
// JS Dropdown
document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const parent = this.parentElement;
        const menu = parent.querySelector('.dropdown-menu');
        const isActive = parent.classList.contains('active');
        
        document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active', 'dropdown-up'));
        
        if (!isActive) {
            // Check if there's enough space below
            const rect = this.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            const menuHeight = 120; // Estimated height
            
            if (spaceBelow < menuHeight) {
                parent.classList.add('dropdown-up');
            }
            
            parent.classList.add('active');
        }
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
        const bulanStr = this.dataset.bulan; // e.g. "Januari 2026"
        const parts = bulanStr.split(' ');
        
        if (parts.length === 2) {
            document.getElementById('edit_month_select').value = parts[0];
            document.getElementById('edit_year_select').value = parts[1];
            document.getElementById('edit_bulan_hidden').value = bulanStr;
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

// Delete Confirmation
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
            borderRadius: '16px'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
