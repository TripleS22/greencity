@extends('layouts.app')

@section('title', 'Pengeluaran - Iuran Warga')

@section('content')
<style>
    .btn-icon-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
        background: var(--white);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-edit-premium:hover {
        background: var(--primary-lightest);
        border-color: var(--primary-light);
        color: var(--primary);
    }
    .btn-delete-premium:hover {
        background: var(--red-light);
        border-color: rgba(239, 68, 68, 0.3);
        color: var(--red);
    }
</style>

<div class="page-header-responsive">
    <div>
        <h1 style="font-size:1.5rem; font-weight:700; color:var(--text);">Data Pengeluaran</h1>
        <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:0.25rem;">Kelola catatan pengeluaran lingkungan</p>
    </div>
    <div style="display:flex; gap: 0.5rem; flex-wrap: wrap;">
        <button class="btn btn-outline" style="flex:1; justify-content:center;" onclick="document.getElementById('modalTambahKategori').classList.add('active')">
            <i data-lucide="folder-plus" style="width:16px;height:16px"></i>
            Kategori
        </button>
        <button class="btn btn-primary" style="flex:1; justify-content:center;" onclick="document.getElementById('modalTambahPengeluaran').classList.add('active')">
            <i data-lucide="plus" style="width:16px;height:16px"></i>
            Tambah Pengeluaran
        </button>
    </div>
</div>

@php
    $bulanNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $periodeLabel = !empty($filterMonth) ? $bulanNames[(int) $filterMonth] . ' ' . $filterYear : 'Tahun ' . $filterYear;
@endphp

<div class="card" style="background: linear-gradient(135deg, #fef2f2, #fff7ed); margin-bottom:1.5rem; border: 1px solid rgba(249, 115, 22, 0.15);">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:1rem;">
            <div class="stat-icon orange">
                <i data-lucide="receipt" style="width:24px;height:24px"></i>
            </div>
            <div>
                <div style="font-size:0.82rem; color:var(--text-secondary);">Total Pengeluaran {{ $periodeLabel }}</div>
                <div style="font-size:1.4rem; font-weight:700; color:var(--text);">Rp {{ number_format($totalFiltered, 0, ',', '.') }}</div>
            </div>
        </div>

        <form action="{{ route('pengeluaran.index') }}" method="GET" style="display:flex; gap:0.5rem; margin:0; flex-wrap:wrap;">
            <select name="month" class="form-input" style="width:auto; height:38px; font-size:0.88rem; background-color:var(--white); cursor:pointer;" onchange="this.form.submit()">
                <option value="">Semua Bulan</option>
                @foreach($bulanNames as $num => $name)
                    <option value="{{ sprintf('%02d', $num) }}" {{ (int) $filterMonth === $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" class="form-input" style="width:auto; height:38px; font-size:0.88rem; background-color:var(--white); cursor:pointer;" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ (int) $filterYear === $y ? 'selected' : '' }}>{{ $y }}</option>
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
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengeluaran as $exp)
                <tr>
                    <td style="color:var(--text-muted);">{{ $loop->iteration }}</td>
                    <td style="color:var(--text-secondary);">{{ $exp->tanggal->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-warning">{{ $exp->kategori->nama ?? '-' }}</span>
                    </td>
                    <td style="font-weight:500;">{{ $exp->keterangan }}</td>
                    <td style="font-weight:600; color:var(--red);">Rp {{ number_format($exp->jumlah, 0, ',', '.') }}</td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button type="button" class="btn-icon-action btn-edit-premium" onclick="editPengeluaran({{ json_encode($exp) }})" title="Edit Data">
                                <i data-lucide="edit-2" style="width:16px;height:16px"></i>
                            </button>
                            
                            <form action="{{ route('pengeluaran.destroy', $exp) }}" method="POST" class="delete-form" style="display:contents;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-icon-action btn-delete-premium delete-btn-premium" title="Hapus Catatan">
                                    <i data-lucide="trash-2" style="width:16px;height:16px"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding:2rem; color:var(--text-muted);">
                        Belum ada data pengeluaran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengeluaran->hasPages())
        <div class="pagination">
            {{ $pengeluaran->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah Pengeluaran --}}
<div class="modal-overlay" id="modalTambahPengeluaran">
    <div class="modal">
        <div class="modal-title">Tambah Pengeluaran</div>
        <form action="{{ route('pengeluaran.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="kategori_id" class="form-input" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" class="form-input" required placeholder="Keterangan pengeluaran">
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah (Rp)</label>
                <input type="number" name="jumlah" class="form-input" required min="1000" step="1000" placeholder="100000">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-input" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambahPengeluaran').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Pengeluaran --}}
<div class="modal-overlay" id="modalEditPengeluaran">
    <div class="modal">
        <div class="modal-title">Edit Pengeluaran</div>
        <form id="formEditPengeluaran" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="kategori_id" id="editKategori" class="form-input" required>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" id="editKeterangan" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah (Rp)</label>
                <input type="number" name="jumlah" id="editJumlah" class="form-input" required min="1000">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="editTanggal" class="form-input" required>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEditPengeluaran').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Kategori Baru --}}
<div class="modal-overlay" id="modalTambahKategori">
    <div class="modal" style="max-width: 400px;">
        <div class="modal-title">Buat Kategori Baru</div>
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-input" required placeholder="Contoh: Beli Semen">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem">Kategori ini nantinya akan muncul selamanya di sistem untuk dipilih.</p>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambahKategori').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>

function editPengeluaran(data) {
    document.getElementById('editKategori').value = data.kategori_id;
    document.getElementById('editKeterangan').value = data.keterangan;
    document.getElementById('editJumlah').value = data.jumlah;
    document.getElementById('editTanggal').value = data.tanggal;
    document.getElementById('formEditPengeluaran').action = '/pengeluaran/' + data.id;
    document.getElementById('modalEditPengeluaran').classList.add('active');
}

// Delete Confirmation Premium (SweetAlert2)
document.querySelectorAll('.delete-btn-premium').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('form');
        Swal.fire({
            title: 'Hapus Catatan?',
            text: "Data pengeluaran ini akan dihapus permanen.",
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

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>
@endsection
