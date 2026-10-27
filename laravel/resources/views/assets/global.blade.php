@extends('layouts.app')
@section('title', 'Global Asset View')
@section('breadcrumb', 'Asset > Global View')

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
        min-width: 150px;
    }
    .filter-bar .filter-group label {
        font-size: 11px;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .filter-bar .filter-group input,
    .filter-bar .filter-group select {
        padding: 7px 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        background: #fff;
    }
    .filter-bar .filter-group input:focus,
    .filter-bar .filter-group select:focus {
        border-color: var(--sidebar-bg);
        box-shadow: 0 0 0 3px rgba(58,125,52,.12);
    }
    .filter-bar .filter-actions {
        display: flex;
        gap: 8px;
        align-items: flex-end;
        padding-bottom: 1px;
        flex-shrink: 0;
    }
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 12px;
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #e8f5e2;
        border: 1px solid #b8dbb0;
        color: #2e6429;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 500;
    }
    .filter-chip a {
        color: #2e6429;
        text-decoration: none;
        font-weight: 700;
        line-height: 1;
        margin-left: 2px;
    }
    .filter-chip a:hover { color: #e74c3c; }

    /* Barcode toolbar */
    .barcode-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: #f0f7ee;
        border: 1px solid #c5dfc0;
        border-radius: 8px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .barcode-toolbar .select-info {
        font-size: 13px;
        color: #3a7d34;
        font-weight: 600;
        flex: 1;
    }
    .barcode-toolbar .btn-group { display: flex; gap: 6px; }
    .btn-58mm { background:#e67e22; color:#fff; border:none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    .btn-58mm:hover { background:#d35400; }
    .btn-a4 { background:#2980b9; color:#fff; border:none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    .btn-a4:hover { background:#1a6ea0; }
    .btn-58mm:disabled, .btn-a4:disabled { opacity: 0.5; cursor: not-allowed; }

    .cb-asset, #select-all { cursor:pointer; width:15px; height:15px; accent-color:#3a7d34; }
    th.th-cb, td.td-cb { width:36px; text-align:center; }

    .result-count {
        font-size: 12px;
        color: #888;
        margin-bottom: 8px;
    }
    .result-count strong { color: var(--sidebar-bg); }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-globe"></i> Global Asset View</h1>
    <div style="display:flex;gap:8px;align-items:center">
        <span style="font-size:13px;color:#666">Total: <strong>{{ $assets->total() }}</strong> asset dipaginate</span>
    </div>
</div>

{{-- ── Filter Bar ──────────────────────────────────── --}}
<form method="GET" action="{{ route('assets.global') }}" id="filter-form">
    <div class="filter-bar">

        {{-- Search teks --}}
        <div class="filter-group" style="min-width:200px;flex:2">
            <label><i class="fas fa-search"></i> Cari (Code / Brand / Model / Pengguna)</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                   placeholder="Ketik untuk mencari..." autocomplete="off">
        </div>

        {{-- Tipe Asset --}}
        <div class="filter-group">
            <label><i class="fas fa-layer-group"></i> Tipe Asset</label>
            <select name="type">
                <option value="">-- Semua Tipe --</option>
                @foreach(\App\Models\Asset::TYPES as $key => $label)
                <option value="{{ $key }}" {{ ($filters['type'] ?? '') === $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Lokasi --}}
        <div class="filter-group">
            <label><i class="fas fa-map-marker-alt"></i> Lokasi</label>
            <select name="location_id">
                <option value="">-- Semua Lokasi --</option>
                @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ ($filters['location_id'] ?? '') == $loc->id ? 'selected' : '' }}>
                    {{ $loc->name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Group --}}
        <div class="filter-group">
            <label><i class="fas fa-users"></i> Group</label>
            <select name="group_id">
                <option value="">-- Semua Group --</option>
                @foreach($groups as $grp)
                <option value="{{ $grp->id }}" {{ ($filters['group_id'] ?? '') == $grp->id ? 'selected' : '' }}>
                    {{ $grp->name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Tombol --}}
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('assets.global') }}" class="btn btn-secondary">
                <i class="fas fa-undo"></i> Reset
            </a>
        </div>
    </div>
</form>

{{-- ── Active Filter Chips ─────────────────────────── --}}
@php
    $activeFilters = array_filter($filters ?? []);
    $typeLabels = \App\Models\Asset::TYPES;
@endphp

@if(count($activeFilters))
<div class="active-filters">
    <span style="font-size:12px;font-weight:600;color:#555;align-self:center">Filter aktif:</span>

    @if(!empty($filters['search']))
    <span class="filter-chip">
        <i class="fas fa-search"></i> "{{ $filters['search'] }}"
        <a href="{{ route('assets.global', array_merge($filters, ['search'=>''])) }}">×</a>
    </span>
    @endif

    @if(!empty($filters['type']))
    <span class="filter-chip">
        <i class="fas fa-layer-group"></i> {{ $typeLabels[$filters['type']] ?? $filters['type'] }}
        <a href="{{ route('assets.global', array_merge($filters, ['type'=>''])) }}">×</a>
    </span>
    @endif

    @if(!empty($filters['location_id']))
    @php $locFilter = collect($locations)->firstWhere('id', $filters['location_id']); @endphp
    @if($locFilter)
    <span class="filter-chip">
        <i class="fas fa-map-marker-alt"></i> {{ $locFilter->name }}
        <a href="{{ route('assets.global', array_merge($filters, ['location_id'=>''])) }}">×</a>
    </span>
    @endif
    @endif

    @if(!empty($filters['group_id']))
    @php $grpFilter = collect($groups)->firstWhere('id', $filters['group_id']); @endphp
    @if($grpFilter)
    <span class="filter-chip">
        <i class="fas fa-users"></i> {{ $grpFilter->name }}
        <a href="{{ route('assets.global', array_merge($filters, ['group_id'=>''])) }}">×</a>
    </span>
    @endif
    @endif
</div>
@endif

{{-- ── Barcode Toolbar ─────────────────────────────── --}}
<div class="barcode-toolbar" id="barcode-toolbar">
    <div class="select-info">
        <i class="fas fa-check-square"></i>
        Pilih asset lalu klik tombol Print Barcode
        &mdash; <strong><span id="selected-count">0</span></strong> dipilih di halaman ini
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-secondary btn-sm" id="btn-select-all-toggle">
            <i class="fas fa-check-double"></i> Pilih Semua
        </button>
        <button type="button" class="btn btn-a4 btn-sm" id="btn-print-a4" disabled>
            <i class="fas fa-print"></i> Print A4
        </button>
        <button type="button" class="btn btn-58mm btn-sm" id="btn-print-58mm" disabled>
            <i class="fas fa-receipt"></i> Print 58mm
        </button>
    </div>
</div>

{{-- Hidden form A4 --}}
<form id="form-barcode-a4" action="{{ route('barcodes.generate') }}" method="POST" target="_blank" style="display:none">
    @csrf
    <div id="inputs-a4"></div>
</form>
{{-- Hidden form 58mm --}}
<form id="form-barcode-58mm" action="{{ route('barcodes.generate58mm') }}" method="POST" target="_blank" style="display:none">
    @csrf
    <div id="inputs-58mm"></div>
</form>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th class="th-cb"><input type="checkbox" id="select-all" title="Pilih semua baris ini"></th>
                        <th>#</th>
                        <th>Type</th>
                        <th>Code</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Location</th>
                        <th>Pengguna</th>
                        <th>Group</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td class="td-cb">
                            <input type="checkbox" class="cb-asset" value="{{ $asset->id }}" title="{{ $asset->code_asset }}">
                        </td>
                        <td>{{ $loop->iteration + $assets->firstItem() - 1 }}</td>
                        <td><span class="badge badge-open">{{ \App\Models\Asset::TYPES[$asset->asset_type] ?? $asset->asset_type }}</span></td>
                        <td><strong style="font-family:monospace;letter-spacing:1px">{{ $asset->code_asset }}</strong></td>
                        <td>{{ $asset->brand->name ?? '-' }}</td>
                        <td>{{ $asset->deviceModel->name ?? '-' }}</td>
                        <td>{{ $asset->location->name ?? '-' }}</td>
                        <td>{{ $asset->employee->name ?? '-' }}</td>
                        <td>{{ $asset->group->name ?? '-' }}</td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('assets.show', $asset) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center;color:#999;padding:30px">
                        @if(count($activeFilters))
                            Tidak ada asset yang cocok dengan kriteria filter.
                        @else
                            Belum ada data asset.
                        @endif
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $assets->withQueryString()->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // ── Checkbox logic ──────────────────────────────────
    function getChecked() {
        return $('.cb-asset:checked');
    }

    function updateToolbar() {
        var count = getChecked().length;
        $('#selected-count').text(count);
        var disabled = count === 0;
        $('#btn-print-a4, #btn-print-58mm').prop('disabled', disabled);
    }

    $(document).on('change', '.cb-asset', updateToolbar);

    // Select all toggle
    var allSelected = false;
    $('#btn-select-all-toggle').on('click', function () {
        allSelected = !allSelected;
        $('.cb-asset').prop('checked', allSelected);
        $('#select-all').prop('checked', allSelected);
        $(this).html(allSelected
            ? '<i class="fas fa-times"></i> Batal Semua'
            : '<i class="fas fa-check-double"></i> Pilih Semua');
        updateToolbar();
    });

    // Header checkbox
    $('#select-all').on('change', function () {
        $('.cb-asset').prop('checked', this.checked);
        updateToolbar();
    });

    // ── Build & submit form ─────────────────────────────
    function buildAndSubmit(formId, inputsId) {
        var ids = getChecked().map(function () { return this.value; }).get();
        if (ids.length === 0) {
            alert('Pilih minimal 1 asset terlebih dahulu.');
            return;
        }
        var $inputs = $('#' + inputsId).empty();
        ids.forEach(function (id) {
            $inputs.append('<input type="hidden" name="ids[]" value="' + id + '">');
        });
        $('#' + formId)[0].submit();
    }

    $('#btn-print-a4').on('click', function () {
        buildAndSubmit('form-barcode-a4', 'inputs-a4');
    });

    $('#btn-print-58mm').on('click', function () {
        buildAndSubmit('form-barcode-58mm', 'inputs-58mm');
    });

    // Init count
    updateToolbar();
});
</script>
@endpush
