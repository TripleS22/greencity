@extends('layouts.app')
@section('title', ($specType ?? 'Spec') . ' Specs')
@section('breadcrumb', 'Data Master > ' . ($specType ?? 'Spec') . ' Specs')

@push('styles')
<style>
.filter-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;margin-bottom:16px;padding:14px 16px;background:#f8f9fa;border:1px solid var(--border-color);border-radius:8px;}
.filter-bar .filter-group{display:flex;flex-direction:column;gap:4px;flex:1;min-width:160px;}
.filter-bar .filter-group label{font-size:12px;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.5px;}
.filter-bar .filter-group input{padding:7px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-family:inherit;transition:border-color .15s,box-shadow .15s;outline:none;}
.filter-bar .filter-group input:focus{border-color:var(--sidebar-bg);box-shadow:0 0 0 3px rgba(58,125,52,.12);}
.filter-bar .filter-actions{display:flex;gap:8px;align-items:flex-end;padding-bottom:1px;}
.result-info{font-size:12px;color:#888;margin-bottom:8px;}
.result-info span{font-weight:600;color:var(--sidebar-bg);}
</style>
@endpush

@section('content')
@php $specType = $specType ?? 'RAM'; $lcType = strtolower($specType); @endphp

<div class="page-header">
    <h1><i class="fas fa-memory"></i> {{ $specType }} Specs</h1>
    <a href="{{ route('master.'.$lcType.'_specs.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a>
</div>

<div class="filter-bar">
    <div class="filter-group">
        <label><i class="fas fa-search" style="margin-right:4px"></i>Cari {{ $specType }} Spec</label>
        <input type="text" id="filter-spec" placeholder="Ketik spesifikasi {{ $specType }}..." autocomplete="off">
    </div>
    <div class="filter-actions">
        <button class="btn btn-secondary" id="btn-reset-filter"><i class="fas fa-undo"></i> Reset</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="result-info">Menampilkan <span id="visible-count">{{ $items->count() }}</span> dari <span>{{ $items->count() }}</span> data</div>
        <div class="table-responsive">
            <table id="specs-table" class="datatable">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Spec</th>
                        <th style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $i->spec }}</strong></td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('master.'.$lcType.'_specs.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('master.'.$lcType.'_specs.destroy', $i) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus spec ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>
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
    var table = $('#specs-table').DataTable();

    $('#filter-spec').on('keyup', function () {
        table.column(1).search(this.value).draw();
        $('#visible-count').text(table.page.info().recordsDisplay);
    });

    $('#btn-reset-filter').on('click', function () {
        $('#filter-spec').val('');
        table.search('').columns().search('').draw();
        $('#visible-count').text(table.page.info().recordsDisplay);
    });

    table.on('draw', function () {
        $('#visible-count').text(table.page.info().recordsDisplay);
    });
});
</script>
@endpush
