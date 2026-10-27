@extends('layouts.app')

@section('title', 'Pemasukan Lain - Iuran Warga')

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
        <h1 style="font-size:1.5rem; font-weight:700; color:var(--text);">Pemasukan Lain-lain</h1>
        <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:0.25rem;">Catat pemasukan di luar iuran warga (Bunga Bank, Sewa, dll)</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('modalTambahPemasukan').classList.add('active')">
        <i data-lucide="plus" style="width:16px;height:16px"></i>
        Tambah Pemasukan
    </button>
</div>

<div class="card" style="background: linear-gradient(135deg, #f0fdf4, #f8fafc); margin-bottom:1.5rem; border: 1px solid rgba(34, 197, 94, 0.15);">
    <div style="display:flex; align-items:center; gap:1rem;">
        <div class="stat-icon green">
            <i data-lucide="trending-up" style="width:24px;height:24px"></i>
        </div>
        <div>
            <div style="font-size:0.82rem; color:var(--text-secondary);">Total Pemasukan Lain</div>
            <div style="font-size:1.4rem; font-weight:700; color:var(--text);">Rp {{ number_format(\App\Models\PemasukanLain::sum('jumlah'), 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemasukanLain as $p)
                <tr>
                    <td style="color:var(--text-muted);">{{ $loop->iteration + ($pemasukanLain->currentPage() - 1) * $pemasukanLain->perPage() }}</td>
                    <td style="color:var(--text-secondary);">{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td style="font-weight:500;">{{ $p->keterangan }}</td>
                    <td style="font-weight:600; color:var(--primary);">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button type="button" class="btn-icon-action btn-edit-premium" onclick="editPemasukan({{ json_encode($p) }})" title="Edit Data">
                                <i data-lucide="edit-2" style="width:16px;height:16px"></i>
                            </button>
                            
                            <form action="{{ route('pemasukan_lain.destroy', $p) }}" method="POST" class="delete-form" style="display:contents;">
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
                    <td colspan="5" class="text-center" style="padding:2rem; color:var(--text-muted);">
                        Belum ada data pemasukan lain
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pemasukanLain->hasPages())
        <div class="pagination">
            {{ $pemasukanLain->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah Pemasukan --}}
<div class="modal-overlay" id="modalTambahPemasukan">
    <div class="modal">
        <div class="modal-title">Catat Pemasukan Lain</div>
        <form action="{{ route('pemasukan_lain.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" class="form-input" required placeholder="Contoh: Bunga Bank Februari">
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah (Rp)</label>
                <input type="number" name="jumlah" class="form-input" required min="0" placeholder="0">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-input" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambahPemasukan').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Pemasukan --}}
<div class="modal-overlay" id="modalEditPemasukan">
    <div class="modal">
        <div class="modal-title">Edit Pemasukan Lain</div>
        <form id="formEditPemasukan" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" id="editKeterangan" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah (Rp)</label>
                <input type="number" name="jumlah" id="editJumlah" class="form-input" required min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="editTanggal" class="form-input" required>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEditPemasukan').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>

function editPemasukan(data) {
    document.getElementById('editKeterangan').value = data.keterangan;
    document.getElementById('editJumlah').value = parseFloat(data.jumlah);
    document.getElementById('editTanggal').value = data.tanggal.split('T')[0];
    document.getElementById('formEditPemasukan').action = '/pemasukan-lain/' + data.id;
    document.getElementById('modalEditPemasukan').classList.add('active');
}

// Delete Confirmation Premium (SweetAlert2)
document.querySelectorAll('.delete-btn-premium').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('form');
        Swal.fire({
            title: 'Hapus Catatan?',
            text: "Data pemasukan ini akan dihapus permanen.",
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
