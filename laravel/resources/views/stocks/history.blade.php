@extends('layouts.app')
@section('title', 'Stock History')
@section('breadcrumb', 'Stock > History')
@section('content')
<div class="page-header"><h1><i class="fas fa-history"></i> Stock History</h1></div>

<!-- Add History Form -->
<div class="card"><div class="card-header"><i class="fas fa-plus"></i> Tambah History</div><div class="card-body">
    <form action="{{ route('stocks.history.store') }}" method="POST" id="history-form">@csrf
        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:15px;">
            <div class="form-group" style="margin:0;flex:1;min-width:150px"><label>Stock Item</label><select name="stock_id" class="form-control" required><option value="">-- Pilih --</option>@foreach(\App\Models\Stock::all() as $s)<option value="{{ $s->id }}">{{ $s->item_name }}</option>@endforeach</select></div>
            <div class="form-group" style="margin:0;width:100px"><label>Type</label><select name="type" id="history-type" class="form-control"><option value="in">In</option><option value="out">Out</option></select></div>
            <div class="form-group" style="margin:0;width:100px"><label>Qty</label><input type="number" name="quantity" id="history-qty" class="form-control" min="1" value="1" required></div>
            <div class="form-group" style="margin:0"><label>Date</label><input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
            <div class="form-group" style="margin:0;flex:1;min-width:150px"><label>Notes</label><input type="text" name="notes" class="form-control"></div>
            <button type="submit" class="btn btn-primary" style="height:38px"><i class="fas fa-save"></i> Simpan</button>
        </div>

        <!-- Asset Creation Section (Hidden by default) -->
        <div id="asset-creation-section" style="display:none; padding:15px; background:#f8f9fa; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">
            <div style="margin-bottom:15px;">
                <label style="display:inline-flex; align-items:center; cursor:pointer; font-weight:bold; color:var(--sidebar-bg);">
                    <input type="checkbox" name="is_create_asset" id="is_create_asset" value="1" style="width:16px;height:16px;margin-right:8px;">
                    Jadikan Asset Baru (Otomatis masuk ke Master Asset)
                </label>
            </div>
            
            <div id="asset-fields-container" style="display:none;">
                <div class="form-group" style="max-width:300px;">
                    <label>Tipe Asset Global (Berlaku untuk semua)</label>
                    <select name="asset_type" id="asset_type" class="form-control">
                        <option value="">-- Pilih Tipe Asset --</option>
                        @foreach($assetTypes ?? [] as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <hr style="border:0; border-top:1px solid #ddd; margin:15px 0;">
                <p style="font-size:12px; color:#555; margin-bottom:10px;"><i class="fas fa-info-circle"></i> Silakan tentukan detail untuk masing-masing item (berdasarkan Qty):</p>
                
                <div id="dynamic-asset-rows" style="display:grid; gap:15px;">
                    <!-- Rows will be injected here by JS -->
                </div>
            </div>
        </div>
    </form>
</div></div>

<div class="card"><div class="card-body"><div class="table-responsive"><table class="datatable">
    <thead><tr><th>#</th><th>Item</th><th>Type</th><th>Qty</th><th>Date</th><th>Notes</th></tr></thead>
    <tbody>
    @forelse($histories as $h)
    <tr><td>{{ $loop->iteration }}</td><td>{{ $h->stock->item_name ?? '-' }}</td>
        <td><span class="badge {{ $h->type=='in'?'badge-closed':'badge-high' }}">{{ strtoupper($h->type) }}</span></td>
        <td>{{ $h->quantity }} {{ $h->stock->unit_type ?? '' }}</td><td>{{ $h->date->format('d M Y') }}</td><td>{{ $h->notes ?? '-' }}</td></tr>
    @empty<tr><td colspan="6" style="text-align:center;color:#999;padding:30px">Belum ada data</td></tr>@endforelse
    </tbody>
</table></div></div></div>
@endsection

@push('scripts')
<script>
    // Asset references for dynamic rows
    const locations = @json($locations ?? []);
    const employees = @json($employees ?? []);
    const brands = @json($brands ?? []);
    const models = @json($deviceModels ?? []);

    function generateOptions(data, valueKey, labelKey) {
        return '<option value="">-- Pilih --</option>' + data.map(item => `<option value="${item[valueKey]}">${item[labelKey]}</option>`).join('');
    }

    function renderAssetRows() {
        const qty = parseInt($('#history-qty').val()) || 1;
        const container = $('#dynamic-asset-rows');
        container.empty();

        for (let i = 0; i < qty; i++) {
            const html = `
                <div style="background:#fff; padding:12px; border:1px solid #ccc; border-radius:6px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                    <div style="font-weight:bold; width:30px; text-align:center;">#${i+1}</div>
                    <div class="form-group" style="margin:0; flex:1; min-width:150px;">
                        <label style="font-size:11px;">Lokasi *</label>
                        <select name="assets_data[${i}][location_id]" class="form-control dynamic-select2" required>${generateOptions(locations, 'id', 'name')}</select>
                    </div>
                    <div class="form-group" style="margin:0; flex:1; min-width:150px;">
                        <label style="font-size:11px;">Pengguna (Employee)</label>
                        <select name="assets_data[${i}][employee_id]" class="form-control dynamic-select2">${generateOptions(employees, 'id', 'name')}</select>
                    </div>
                    <div class="form-group" style="margin:0; flex:1; min-width:120px;">
                        <label style="font-size:11px;">Brand</label>
                        <select name="assets_data[${i}][brand_id]" class="form-control dynamic-select2">${generateOptions(brands, 'id', 'name')}</select>
                    </div>
                    <div class="form-group" style="margin:0; flex:1; min-width:120px;">
                        <label style="font-size:11px;">Model</label>
                        <select name="assets_data[${i}][device_model_id]" class="form-control dynamic-select2">${generateOptions(models, 'id', 'name')}</select>
                    </div>
                </div>
            `;
            container.append(html);
        }

        // Initialize select2 on new dynamic selects
        $('.dynamic-select2').select2({ width: '100%', placeholder: '-- Pilih --', allowClear: true });
    }

    $(document).ready(function() {
        const typeSelect = $('#history-type');
        const assetSection = $('#asset-creation-section');
        const isCreateAsset = $('#is_create_asset');
        const assetFieldsContainer = $('#asset-fields-container');
        const assetTypeSelect = $('#asset_type');
        const qtyInput = $('#history-qty');

        // Show/hide entire asset section ONLY when Type is OUT
        typeSelect.on('change', function() {
            if ($(this).val() === 'out') {
                assetSection.slideDown();
            } else {
                assetSection.slideUp();
                isCreateAsset.prop('checked', false).trigger('change');
            }
        });

        // Show/hide detailed asset fields based on Checkbox
        isCreateAsset.on('change', function() {
            if ($(this).is(':checked')) {
                assetFieldsContainer.slideDown();
                assetTypeSelect.prop('required', true);
                renderAssetRows();
            } else {
                assetFieldsContainer.slideUp();
                assetTypeSelect.prop('required', false);
                $('#dynamic-asset-rows').empty();
            }
        });

        // Re-render rows when Quantity changes (if checkbox is active)
        qtyInput.on('input change', function() {
            if (isCreateAsset.is(':checked')) {
                renderAssetRows();
            }
        });
    });
</script>
@endpush
