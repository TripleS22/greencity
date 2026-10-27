@extends('layouts.app')
@section('title', 'Employees')
@section('breadcrumb', 'Data Master > Employees')

@push('styles')
<style>
.filter-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;margin-bottom:16px;padding:14px 16px;background:#f8f9fa;border:1px solid var(--border-color);border-radius:8px;}
.filter-bar .filter-group{display:flex;flex-direction:column;gap:4px;flex:1;min-width:160px;}
.filter-bar .filter-group label{font-size:12px;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.5px;}
.filter-bar .filter-group input,.filter-bar .filter-group select{padding:7px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-family:inherit;transition:border-color .15s,box-shadow .15s;outline:none;}
.filter-bar .filter-group input:focus,.filter-bar .filter-group select:focus{border-color:var(--sidebar-bg);box-shadow:0 0 0 3px rgba(58,125,52,.12);}
.filter-bar .filter-actions{display:flex;gap:8px;align-items:flex-end;padding-bottom:1px;}
.result-info{font-size:12px;color:#888;margin-bottom:8px;}
.result-info span{font-weight:600;color:var(--sidebar-bg);}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-users"></i> Employees (Pengguna Barang)</h1>
    <a href="{{ route('master.employees.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a>
</div>

<div class="filter-bar">
    <div class="filter-group">
        <label><i class="fas fa-search" style="margin-right:4px"></i>Cari Nama</label>
        <input type="text" id="filter-name" placeholder="Ketik nama employee..." autocomplete="off">
    </div>
    <div class="filter-group">
        <label><i class="fas fa-briefcase" style="margin-right:4px"></i>Cari Jabatan</label>
        <input type="text" id="filter-jabatan" placeholder="Ketik jabatan..." autocomplete="off">
    </div>
    <div class="filter-group">
        <label><i class="fas fa-layer-group" style="margin-right:4px"></i>Filter Group</label>
        <select id="filter-group">
            <option value="">-- Semua Group --</option>
            @foreach(\App\Models\Group::orderBy('name')->get() as $group)
            <option value="{{ $group->name }}">{{ $group->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-actions">
        <button class="btn btn-secondary" id="btn-reset-filter"><i class="fas fa-undo"></i> Reset</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="result-info">Menampilkan <span id="visible-count">{{ $items->total() }}</span> dari <span>{{ $items->total() }}</span> data</div>
        <div class="table-responsive">
            <table id="employees-table" class="datatable">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Name</th>
                        <th>Jabatan</th>
                        <th>Group</th>
                        <th style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $i->name }}</strong></td>
                        <td>{{ $i->jabatan ?? '-' }}</td>
                        <td>{{ $i->group->name ?? '-' }}</td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('master.employees.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('master.employees.destroy', $i) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus employee ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>
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
    var table = $('#employees-table').DataTable();

    $('#filter-name').on('keyup', function () {
        table.column(1).search(this.value).draw();
        $('#visible-count').text(table.page.info().recordsDisplay);
    });

    $('#filter-jabatan').on('keyup', function () {
        table.column(2).search(this.value).draw();
        $('#visible-count').text(table.page.info().recordsDisplay);
    });

    $('#filter-group').on('change', function () {
        table.column(3).search(this.value).draw();
        $('#visible-count').text(table.page.info().recordsDisplay);
    });

    $('#btn-reset-filter').on('click', function () {
        $('#filter-name, #filter-jabatan').val('');
        $('#filter-group').val('');
        table.search('').columns().search('').draw();
        $('#visible-count').text(table.page.info().recordsDisplay);
    });

    table.on('draw', function () {
        $('#visible-count').text(table.page.info().recordsDisplay);
    });
});
</script>
@endpush
