@extends('layouts.app')
@section('title', $typeLabel)
@section('breadcrumb', 'Asset > ' . $typeLabel)

@push('styles')
<style>
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
    .barcode-toolbar .btn-group {
        display: flex;
        gap: 6px;
    }
    .barcode-toolbar .btn-58mm {
        background: #e67e22;
        color: #fff;
        border: none;
    }
    .barcode-toolbar .btn-58mm:hover { background: #d35400; }
    .barcode-toolbar .btn-a4 {
        background: #2980b9;
        color: #fff;
        border: none;
    }
    .barcode-toolbar .btn-a4:hover { background: #1a6ea0; }

    .cb-asset { cursor: pointer; width: 15px; height: 15px; accent-color: #3a7d34; }
    #select-all { cursor: pointer; width: 15px; height: 15px; accent-color: #3a7d34; }
    th.th-cb, td.td-cb { width: 36px; text-align: center; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="{{ $typeIcon }}"></i> {{ $typeLabel }}</h1>
    <div style="display:flex;gap:8px">
        <a href="{{ route('assets.type.import', $type) }}" class="btn btn-secondary"><i class="fas fa-file-csv"></i> Import CSV</a>
        <a href="{{ route('assets.type.create', $type) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah {{ $typeLabel }}</a>
    </div>
</div>

{{-- Barcode Toolbar --}}
<div class="barcode-toolbar" id="barcode-toolbar">
    <div class="select-info">
        <i class="fas fa-check-square"></i>
        Pilih asset lalu klik tombol Print Barcode
        &mdash; <strong><span id="selected-count">0</span></strong> dipilih
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
            <table class="datatable">
                <thead>
                    <tr>
                        <th class="th-cb"><input type="checkbox" id="select-all" title="Pilih semua"></th>
                        <th>#</th>
                        <th>Code Asset</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Location</th>
                        <th>Pengguna</th>
                        <th>Group</th>
                        <th>Description</th>
                        @if($type === 'cctv')
                        <th><i class="fas fa-key"></i> Kode Akses</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td class="td-cb">
                            <input type="checkbox" class="cb-asset" value="{{ $asset->id }}" title="{{ $asset->code_asset }}">
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $asset->code_asset }}</strong></td>
                        <td>{{ $asset->brand->name ?? '-' }}</td>
                        <td>{{ $asset->deviceModel->name ?? '-' }}</td>
                        <td>{{ $asset->location->name ?? '-' }}</td>
                        <td>{{ $asset->employee->name ?? '-' }}</td>
                        <td>{{ $asset->group->name ?? '-' }}</td>
                        <td>{{ Str::limit($asset->description, 50) ?: '-' }}</td>
                        @if($type === 'cctv')
                        <td>
                            @if($asset->access_code)
                                <code style="font-size:12px">{{ $asset->access_code }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        @endif
                        <td style="white-space:nowrap">
                            <a href="{{ route('assets.show', $asset) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus asset ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ $type === 'cctv' ? 10 : 9 }}" style="text-align:center;color:#999;padding:30px">Belum ada data {{ $typeLabel }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $assets->links() }}</div>
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
