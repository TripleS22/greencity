@extends('layouts.app')

@section('title', 'Data Warga - Iuran Warga')

@section('content')
<style>
    /* Dropdown logic removed as no longer needed in this view */
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
</style>

<div class="page-header-responsive">
    <div>
        <h1 style="font-size:1.5rem; font-weight:700; color:var(--text);">Data Warga</h1>
        <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:0.25rem;">Kelola data warga dan unit hunian</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('modalTambahWarga').classList.add('active')">
        <i data-lucide="plus" style="width:16px;height:16px"></i>
        Tambah Warga
    </button>
</div>

{{-- Filter & Sort Bar --}}
<div class="card" style="margin-bottom:1rem; padding:1rem 1.25rem;">
    <form method="GET" action="{{ route('warga.index') }}" id="filterForm">
        <div style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">
            {{-- Search --}}
            <div style="flex:1; min-width:200px;">
                <label class="form-label" style="font-size:0.78rem; margin-bottom:0.35rem; display:block;">
                    <i data-lucide="search" style="width:13px;height:13px;vertical-align:-2px;margin-right:3px;"></i>Cari
                </label>
                <input type="text" name="search" class="form-input" placeholder="Cari nama atau unit..."
                       value="{{ request('search') }}" style="padding:0.5rem 0.75rem; font-size:0.88rem;">
            </div>

            {{-- Block / Unit Filter --}}
            <div style="min-width:170px;">
                <label class="form-label" style="font-size:0.78rem; margin-bottom:0.35rem; display:block;">
                    <i data-lucide="building-2" style="width:13px;height:13px;vertical-align:-2px;margin-right:3px;"></i>Blok / Unit
                </label>
                <select name="blok" class="form-input" style="padding:0.5rem 0.75rem; font-size:0.88rem;">
                    <option value="">Semua Blok</option>
                    @foreach($blokList as $blok)
                        <option value="{{ $blok }}" {{ request('blok') == $blok ? 'selected' : '' }}>Blok {{ $blok }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sort --}}
            <div style="min-width:150px;">
                <label class="form-label" style="font-size:0.78rem; margin-bottom:0.35rem; display:block;">
                    <i data-lucide="arrow-up-down" style="width:13px;height:13px;vertical-align:-2px;margin-right:3px;"></i>Urutkan
                </label>
                <select name="sort" class="form-input" style="padding:0.5rem 0.75rem; font-size:0.88rem;">
                    <option value="nama" {{ request('sort', 'nama') == 'nama' ? 'selected' : '' }}>Nama</option>
                    <option value="unit" {{ request('sort') == 'unit' ? 'selected' : '' }}>Unit / Blok</option>
                </select>
            </div>

            {{-- Direction --}}
            <div style="min-width:130px;">
                <label class="form-label" style="font-size:0.78rem; margin-bottom:0.35rem; display:block;">
                    Arah
                </label>
                <select name="direction" class="form-input" style="padding:0.5rem 0.75rem; font-size:0.88rem;">
                    <option value="asc" {{ request('direction', 'asc') == 'asc' ? 'selected' : '' }}>A → Z</option>
                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Z → A</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div style="display:flex; gap:0.5rem; align-items:center; padding-bottom:1px;">
                <button type="submit" class="btn btn-primary" style="padding:0.5rem 1rem; font-size:0.88rem;">
                    <i data-lucide="filter" style="width:14px;height:14px;margin-right:4px;"></i>Terapkan
                </button>
                <a href="{{ route('warga.index') }}" class="btn btn-outline" style="padding:0.5rem 1rem; font-size:0.88rem;">
                    <i data-lucide="x" style="width:14px;height:14px;margin-right:4px;"></i>Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Unit</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Pembayaran Lunas</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warga as $w)
                <tr>
                    <td style="color:var(--text-muted);">{{ $warga->firstItem() + $loop->index }}</td>
                    <td style="color:var(--text-secondary);">{{ $w->unit }}</td>
                    <td style="font-weight:500;">{{ $w->nama }}</td>
                    <td>
                        @if($w->aktif)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Non-Aktif</span>
                        @endif
                    </td>
                    <td style="color:var(--text-secondary);">{{ $w->lunas_count ?? 0 }}x</td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                            <a href="{{ route('warga.show', $w) }}" class="btn-icon-circle" title="Lihat Riwayat" style="text-decoration: none;">
                                <i data-lucide="history" style="width:16px;height:16px"></i>
                            </a>
                            
                            <button type="button" class="btn-icon-circle" onclick="editWarga({{ json_encode($w) }})" title="Edit Profil">
                                <i data-lucide="edit-2" style="width:16px;height:16px"></i>
                            </button>
                            
                            <form action="{{ route('warga.destroy', $w) }}" method="POST" class="delete-form" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-icon-circle text-danger delete-btn-premium" title="Hapus Warga">
                                    <i data-lucide="trash-2" style="width:16px;height:16px"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding:2rem; color:var(--text-muted);">
                        Belum ada data warga
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($warga->hasPages())
        <div class="pagination">
            {{ $warga->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah Warga --}}
<div class="modal-overlay" id="modalTambahWarga">
    <div class="modal">
        <div class="modal-title">Tambah Warga Baru</div>
        <form action="{{ route('warga.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Unit / Blok</label>
                <input type="text" name="unit" class="form-input" required placeholder="Contoh: Blok A-12">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-input" required placeholder="Masukkan nama lengkap">
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambahWarga').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Warga --}}
<div class="modal-overlay" id="modalEditWarga">
    <div class="modal">
        <div class="modal-title">Edit Data Warga</div>
        <form id="formEditWarga" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Unit / Blok</label>
                <input type="text" name="unit" id="editUnit" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" id="editNama" class="form-input" required>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEditWarga').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Dropdown trigger removed as dropdowns are no longer used here

function editWarga(warga) {
    document.getElementById('editNama').value = warga.nama;
    document.getElementById('editUnit').value = warga.unit;
    document.getElementById('formEditWarga').action = '/warga/' + warga.id;
    document.getElementById('modalEditWarga').classList.add('active');
}

// Delete Confirmation Premium (SweetAlert2)
document.querySelectorAll('.delete-btn-premium').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('form');
        Swal.fire({
            title: 'Hapus Warga?',
            text: "Data warga ini akan dihapus permanen.",
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

// Close modal on click outside
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>
@endsection
