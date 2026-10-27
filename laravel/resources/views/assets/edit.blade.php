@extends('layouts.app')
@section('title', 'Edit ' . $typeLabel)
@section('breadcrumb', 'Asset > ' . $typeLabel . ' > Edit')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-edit"></i> Edit {{ $typeLabel }}: {{ $asset->code_asset }}</h1>
    <a href="{{ route('assets.type', $type) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label>Code Asset</label>
                    <input type="text" name="code_asset" class="form-control" value="{{ old('code_asset', $asset->code_asset) }}" readonly style="background:#f8f9fa; cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label>Brand</label>
                    <select name="brand_id" id="brand_id" class="form-control">
                        <option value="">-- Pilih Brand --</option>
                        @foreach($brands as $b)<option value="{{ $b->id }}" {{ old('brand_id', $asset->brand_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Device Model</label>
                    <select name="device_model_id" id="device_model_id" class="form-control">
                        {{-- Pre-loaded models for current brand --}}
                        <option value="">-- Pilih Brand dulu --</option>
                        @foreach($deviceModels as $m)
                            @if($m->brand_id == old('brand_id', $asset->brand_id))
                                <option value="{{ $m->id }}" {{ old('device_model_id', $asset->device_model_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <select name="location_id" class="form-control">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $l)<option value="{{ $l->id }}" {{ old('location_id', $asset->location_id) == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Group</label>
                    <select name="group_id" id="group_id" class="form-control">
                        <option value="">-- Pilih Group --</option>
                        @foreach($groups as $g)<option value="{{ $g->id }}" {{ old('group_id', $asset->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Pengguna Barang</label>
                    <select name="employee_id" id="employee_id" class="form-control">
                        <option value="">-- Pilih Group dulu --</option>
                        {{-- Pre-loaded employees for current group on edit --}}
                        @foreach($employees as $e)
                            @if($e->group_id == old('group_id', $asset->group_id))
                                <option value="{{ $e->id }}" {{ old('employee_id', $asset->employee_id) == $e->id ? 'selected' : '' }}>{{ $e->name }} ({{ $e->jabatan ?? '-' }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>RAM</label>
                    <select name="ram_id" class="form-control">
                        <option value="">-- Pilih RAM --</option>
                        @foreach($ramSpecs as $r)<option value="{{ $r->id }}" {{ old('ram_id', $asset->ram_id) == $r->id ? 'selected' : '' }}>{{ $r->spec }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>ROM / Storage</label>
                    <select name="rom_id" class="form-control">
                        <option value="">-- Pilih ROM --</option>
                        @foreach($romSpecs as $r)<option value="{{ $r->id }}" {{ old('rom_id', $asset->rom_id) == $r->id ? 'selected' : '' }}>{{ $r->spec }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>CPU</label>
                    <select name="cpu_id" class="form-control">
                        <option value="">-- Pilih CPU --</option>
                        @foreach($cpuSpecs as $c)<option value="{{ $c->id }}" {{ old('cpu_id', $asset->cpu_id) == $c->id ? 'selected' : '' }}>{{ $c->spec }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>VGA</label>
                    <select name="vga_id" class="form-control">
                        <option value="">-- Pilih VGA --</option>
                        @foreach($vgaSpecs as $v)<option value="{{ $v->id }}" {{ old('vga_id', $asset->vga_id) == $v->id ? 'selected' : '' }}>{{ $v->spec }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Photo</label>
                @if($asset->photo)
                    <div style="margin-bottom:8px"><img src="{{ asset('storage/'.$asset->photo) }}" alt="Photo" style="max-width:200px;border-radius:6px"></div>
                @endif
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description', $asset->description) }}</textarea>
            </div>
            @if($type === 'cctv')
            <div class="form-group">
                <label><i class="fas fa-key"></i> Kode Akses <small class="text-muted">(opsional, misal: password DVR/NVR)</small></label>
                <input type="text" name="access_code" class="form-control" value="{{ old('access_code', $asset->access_code) }}" placeholder="Contoh: admin123">
            </div>
            @endif
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    const currentModelId    = {{ old('device_model_id', $asset->device_model_id) ?? 'null' }};
    const currentEmployeeId = {{ old('employee_id', $asset->employee_id) ?? 'null' }};

    function reinitSelect2(el) {
        try { $(el).select2('destroy'); } catch(e) {}
        $(el).select2({ width: '100%', allowClear: true, placeholder: $(el).data('placeholder') || '' });
    }

    function cascadeFetch($trigger, $target, apiUrl, emptyLabel, renderOption, currentId) {
        $trigger.on('change', function () {
            const id = $(this).val();
            if (!id) {
                $target.html(`<option value="">${emptyLabel}</option>`);
                reinitSelect2($target[0]);
                return;
            }
            try { $target.select2('destroy'); } catch(e) {}
            $target.html('<option value="">Memuat...</option>');

            fetch(apiUrl + id, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    let opts = '<option value="">-- Pilih --</option>';
                    data.forEach(item => {
                        const sel = (currentId && item.id == currentId) ? ' selected' : '';
                        opts += renderOption(item, sel);
                    });
                    $target.html(opts);
                    reinitSelect2($target[0]);
                })
                .catch(() => {
                    $target.html('<option value="">Gagal memuat data</option>');
                    reinitSelect2($target[0]);
                });
        });
    }

    // Brand → Device Model
    cascadeFetch(
        $('#brand_id'), $('#device_model_id'),
        '/api/models-by-brand/',
        '-- Pilih Brand dulu --',
        (item, sel) => `<option value="${item.id}"${sel}>${item.name}</option>`,
        currentModelId
    );

    // Group → Employee
    cascadeFetch(
        $('#group_id'), $('#employee_id'),
        '/api/employees-by-group/',
        '-- Pilih Group dulu --',
        (item, sel) => `<option value="${item.id}"${sel}>${item.name}${item.jabatan ? ' (' + item.jabatan + ')' : ''}</option>`,
        currentEmployeeId
    );

});
</script>
@endpush
