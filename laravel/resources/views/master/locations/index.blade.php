@extends('layouts.app')
@section('title', 'Locations')
@section('breadcrumb', 'Data Master > Locations')

@push('styles')
<style>
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
        margin-bottom: 16px;
        padding: 14px 16px;
        background: #f8f9fa;
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }
    .filter-bar .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 160px;
    }
    .filter-bar .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .filter-bar .filter-group input {
        padding: 7px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
    }
    .filter-bar .filter-group input:focus {
        border-color: var(--sidebar-bg);
        box-shadow: 0 0 0 3px rgba(58,125,52,.12);
    }
    .filter-bar .filter-actions {
        display: flex;
        gap: 8px;
        align-items: flex-end;
        padding-bottom: 1px;
    }
    .result-info {
        font-size: 12px;
        color: #888;
        margin-bottom: 8px;
    }
    .result-info span {
        font-weight: 600;
        color: var(--sidebar-bg);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-map-marker-alt"></i> Locations</h1>
    <a href="{{ route('master.locations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah
    </a>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <div class="filter-group">
        <label><i class="fas fa-search" style="margin-right:4px"></i>Cari Nama</label>
        <input type="text" id="filter-name" placeholder="Ketik nama lokasi..." autocomplete="off">
    </div>
    <div class="filter-group">
        <label><i class="fas fa-map-pin" style="margin-right:4px"></i>Cari Alamat</label>
        <input type="text" id="filter-address" placeholder="Ketik alamat..." autocomplete="off">
    </div>
    <div class="filter-actions">
        <button class="btn btn-secondary" id="btn-reset-filter" title="Reset Filter">
            <i class="fas fa-undo"></i> Reset
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="result-info">Menampilkan <span id="visible-count">{{ $items->total() }}</span> dari <span>{{ $items->total() }}</span> data</div>
        <div class="table-responsive">
            <table id="locations-table" class="datatable">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $i->name }}</strong></td>
                        <td>{{ $i->address ?? '-' }}</td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('master.locations.show', $i) }}" class="btn btn-info btn-sm" title="Lihat Detail & Riwayat Maintenance"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('master.locations.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('master.locations.destroy', $i) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus lokasi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // DataTable sudah diinisialisasi secara global di layout
    // Ambil instance DataTable yang sudah dibuat
    var table = $('#locations-table').DataTable();

    // --- Filter per kolom menggunakan input custom ---
    // Kolom: 0=#, 1=Name, 2=Address, 3=Actions

    $('#filter-name').on('keyup', function () {
        table.column(1).search(this.value).draw();
        updateVisibleCount(table);
    });

    $('#filter-address').on('keyup', function () {
        table.column(2).search(this.value).draw();
        updateVisibleCount(table);
    });

    $('#btn-reset-filter').on('click', function () {
        $('#filter-name').val('');
        $('#filter-address').val('');
        table.search('').columns().search('').draw();
        updateVisibleCount(table);
    });

    function updateVisibleCount(dt) {
        var info = dt.page.info();
        $('#visible-count').text(info.recordsDisplay);
    }

    // Update count setelah draw
    table.on('draw', function () {
        updateVisibleCount(table);
    });
});
</script>
@endpush
