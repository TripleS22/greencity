<x-filament-panels::page>
<style>
.imp-wrap { font-family: inherit; }

/* Steps */
.imp-steps { display:flex; align-items:center; gap:0; margin-bottom:24px; flex-wrap:wrap; gap:4px; }
.imp-step  { display:flex; align-items:center; gap:8px; }
.imp-step-num {
    width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:700; flex-shrink:0;
}
.imp-step-num.active  { background:#f59e0b; color:#fff; box-shadow:0 0 0 3px rgba(245,158,11,0.2); }
.imp-step-num.done    { background:#10b981; color:#fff; }
.imp-step-num.pending { background:#e5e7eb; color:#9ca3af; }
.imp-step-label { font-size:13px; font-weight:600; }
.imp-step-label.active  { color:#f59e0b; }
.imp-step-label.done    { color:#10b981; }
.imp-step-label.pending { color:#9ca3af; }
.imp-step-line { width:40px; height:2px; background:#e5e7eb; margin:0 6px; }
.imp-step-line.done { background:#10b981; }

/* Card */
.imp-card {
    background:#fff; border-radius:12px; border:1px solid #e5e7eb;
    box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:24px; margin-bottom:20px;
}
.dark .imp-card { background:#1f2937; border-color:#374151; }

/* Info box */
.imp-info {
    background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px;
    padding:14px 16px; display:flex; gap:12px; margin-bottom:20px;
}
.imp-info-icon { font-size:18px; flex-shrink:0; margin-top:1px; }
.imp-info p { margin:0; font-size:13px; color:#1d4ed8; line-height:1.5; }
.imp-info p strong { color:#1e40af; }

/* Form elements */
.imp-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.dark .imp-label { color:#d1d5db; }
.imp-file-wrap {
    border:2px dashed #d1d5db; border-radius:10px; padding:20px;
    text-align:center; cursor:pointer; transition:border-color .2s;
    background:#f9fafb;
}
.imp-file-wrap:hover { border-color:#f59e0b; background:#fffbeb; }
.dark .imp-file-wrap { background:#1f2937; border-color:#4b5563; }
.imp-file-input {
    display:block; width:100%; font-size:14px; color:#6b7280;
    cursor:pointer; outline:none;
}
.imp-error { color:#dc2626; font-size:12px; font-weight:600; margin-top:4px; }

/* Buttons */
.imp-btn {
    display:inline-flex; align-items:center; gap:8px;
    padding:10px 20px; border-radius:8px; font-size:14px; font-weight:600;
    cursor:pointer; border:none; transition:all .2s; text-decoration:none;
}
.imp-btn-primary { background:#f59e0b; color:#fff; }
.imp-btn-primary:hover { background:#d97706; }
.imp-btn-success { background:#10b981; color:#fff; }
.imp-btn-success:hover { background:#059669; }
.imp-btn-gray { background:#f3f4f6; color:#374151; border:1px solid #d1d5db; }
.imp-btn-gray:hover { background:#e5e7eb; }
.imp-btn:disabled { opacity:.6; cursor:not-allowed; }

/* Table */
.imp-table-wrap { overflow-x:auto; border-radius:8px; border:1px solid #e5e7eb; margin-bottom:20px; }
.imp-table { width:100%; border-collapse:collapse; font-size:12px; }
.imp-table th { background:#f9fafb; padding:8px 12px; text-align:left; font-weight:700; color:#6b7280; border-bottom:1px solid #e5e7eb; white-space:nowrap; }
.imp-table td { padding:8px 12px; color:#374151; border-bottom:1px solid #f3f4f6; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.imp-table tr:last-child td { border-bottom:none; }
.imp-table tr:hover td { background:#f9fafb; }
.dark .imp-table th { background:#1f2937; color:#9ca3af; border-color:#374151; }
.dark .imp-table td { color:#d1d5db; border-color:#374151; }

/* Mapping row */
.imp-map-row {
    display:grid; grid-template-columns:1fr 1fr; gap:12px; align-items:center;
    padding:12px 0; border-bottom:1px solid #f3f4f6;
}
.imp-map-row:last-child { border-bottom:none; }
.imp-map-left { display:flex; align-items:center; gap:8px; }
.imp-badge {
    padding:2px 8px; border-radius:20px; font-size:11px; font-weight:700;
    flex-shrink:0;
}
.imp-badge-required { background:#fee2e2; color:#dc2626; }
.imp-badge-optional { background:#f3f4f6; color:#6b7280; }
.imp-map-name { font-size:13px; font-weight:600; color:#111827; }
.dark .imp-map-name { color:#f3f4f6; }
.imp-map-db  { font-size:11px; color:#9ca3af; font-family:monospace; }
.imp-select {
    width:100%; padding:8px 12px; border-radius:8px; border:1px solid #d1d5db;
    font-size:13px; background:#fff; color:#111827; outline:none;
    transition:border-color .2s;
    -webkit-appearance:auto;
}
.imp-select:focus { border-color:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,0.1); }
.imp-select.mapped { border-color:#10b981; background:#f0fdf4; }
.dark .imp-select { background:#374151; border-color:#4b5563; color:#f3f4f6; }

/* Result */
.imp-result-icon {
    width:80px; height:80px; border-radius:50%; display:flex; align-items:center;
    justify-content:center; margin:0 auto 16px; font-size:40px;
}
.imp-result-icon.success { background:#d1fae5; }
.imp-result-icon.warn    { background:#fef3c7; }
.imp-stats { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; }
.imp-stat { border-radius:12px; padding:20px; text-align:center; }
.imp-stat.success { background:#d1fae5; border:1px solid #a7f3d0; }
.imp-stat.warn    { background:#fef3c7; border:1px solid #fde68a; }
.imp-stat-num { font-size:40px; font-weight:800; }
.imp-stat.success .imp-stat-num { color:#065f46; }
.imp-stat.warn    .imp-stat-num { color:#92400e; }
.imp-stat-label { font-size:13px; font-weight:600; margin-top:4px; }
.imp-stat.success .imp-stat-label { color:#047857; }
.imp-stat.warn    .imp-stat-label { color:#b45309; }
.imp-err-box { background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:14px 16px; }
.imp-err-box h4 { margin:0 0 8px; font-size:13px; font-weight:700; color:#dc2626; }
.imp-err-box li { font-size:12px; color:#b91c1c; margin-bottom:4px; list-style:disc; margin-left:16px; }
.imp-actions { display:flex; gap:12px; flex-wrap:wrap; }
.imp-actions a, .imp-actions button { flex:1; justify-content:center; }
</style>

<div class="imp-wrap">

    {{-- STEP INDICATOR --}}
    <div class="imp-steps">
        @php
            $stepOrder = ['upload', 'mapping', 'result'];
            $currentIdx = array_search($step, $stepOrder);
        @endphp

        @foreach (['upload' => 'Upload File', 'mapping' => 'Mapping Kolom', 'result' => 'Hasil Import'] as $key => $label)
            @php
                $idx     = array_search($key, $stepOrder);
                $isActive = $key === $step;
                $isDone   = $idx < $currentIdx;
            @endphp
            <div class="imp-step">
                <div class="imp-step-num {{ $isActive ? 'active' : ($isDone ? 'done' : 'pending') }}">
                    @if($isDone) ✓ @else {{ $idx + 1 }} @endif
                </div>
                <span class="imp-step-label {{ $isActive ? 'active' : ($isDone ? 'done' : 'pending') }}">{{ $label }}</span>
            </div>
            @if (!$loop->last)
                <div class="imp-step-line {{ $idx < $currentIdx ? 'done' : '' }}"></div>
            @endif
        @endforeach
    </div>

    {{-- ========================================================== --}}
    {{-- STEP 1: UPLOAD                                              --}}
    {{-- ========================================================== --}}
    @if ($step === 'upload')
    <div class="imp-card">
        <h2 style="font-size:18px;font-weight:700;margin:0 0 6px;">Upload File Excel</h2>
        <p style="font-size:13px;color:#6b7280;margin:0 0 20px;">
            Upload file <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong>. Baris pertama harus berisi nama kolom (header).
        </p>

        <div class="imp-info">
            <span class="imp-info-icon">ℹ️</span>
            <div>
                <p><strong>Nama kolom di Excel boleh berbeda!</strong></p>
                <p>Setelah upload, Anda bisa memetakan kolom Excel ke field yang tepat. Misalnya kolom "Nama Part" bisa dipetakan ke field "Item Sparepart".</p>
            </div>
        </div>

        @if ($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                @foreach ($errors->all() as $err)
                    <p style="margin:0;font-size:13px;color:#dc2626;font-weight:600;">⚠ {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('inventory.import.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="imp-label" for="excel_file">Pilih File Excel</label>
                <div class="imp-file-wrap">
                    <div style="font-size:32px;margin-bottom:8px;">📂</div>
                    <input
                        type="file"
                        id="excel_file"
                        name="excel_file"
                        accept=".xlsx,.xls,.csv"
                        class="imp-file-input"
                        onchange="document.getElementById('file-name-display').textContent = this.files[0]?.name || 'Belum ada file dipilih'"
                    />
                    <p id="file-name-display" style="font-size:12px;color:#9ca3af;margin:8px 0 0;">Belum ada file dipilih</p>
                </div>
                @error('excel_file')
                    <p class="imp-error">⚠ {{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="imp-btn imp-btn-primary">
                📤 Upload & Lanjut ke Mapping
            </button>
        </form>
    </div>
    @endif

    {{-- ========================================================== --}}
    {{-- STEP 2: MAPPING                                             --}}
    {{-- ========================================================== --}}
    @if ($step === 'mapping')
    <div>
        {{-- Preview --}}
        @if (!empty($previewRows))
        <div class="imp-card">
            <h3 style="font-size:15px;font-weight:700;margin:0 0 14px;">👁 Preview Data (3 Baris Pertama)</h3>
            <div class="imp-table-wrap">
                <table class="imp-table">
                    <thead>
                        <tr>
                            @foreach ($excelHeaders as $hdr)
                                <th>{{ $hdr }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($previewRows as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td title="{{ $cell }}">{{ $cell ?: '-' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Mapping --}}
        <div class="imp-card">
            <h2 style="font-size:18px;font-weight:700;margin:0 0 6px;">🔗 Mapping Kolom</h2>
            <p style="font-size:13px;color:#6b7280;margin:0 0 20px;">
                Pilih kolom dari file Excel yang sesuai dengan setiap field. Pilih <strong>"-- Lewati --"</strong> jika tidak ada.
            </p>

            @if ($errors->has('general'))
                <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                    <p style="margin:0;font-size:13px;color:#dc2626;font-weight:600;">⚠ {{ $errors->first('general') }}</p>
                </div>
            @endif

            <form action="{{ route('inventory.import.process') }}" method="POST" id="import-form">
                @csrf

                @foreach ($dbColumns as $dbCol => $label)
                    @php
                        $isRequired   = str_contains($label, '*');
                        $cleanLabel   = str_replace(' *', '', $label);
                        $autoVal      = $autoMapping[$dbCol] ?? '';
                    @endphp
                    <div class="imp-map-row">
                        <div class="imp-map-left">
                            @if($isRequired)
                                <span class="imp-badge imp-badge-required">Wajib</span>
                            @else
                                <span class="imp-badge imp-badge-optional">Opsional</span>
                            @endif
                            <div>
                                <div class="imp-map-name">{{ $cleanLabel }}</div>
                                <div class="imp-map-db">{{ $dbCol }}</div>
                            </div>
                        </div>
                        <div>
                            <select
                                name="mapping[{{ $dbCol }}]"
                                class="imp-select {{ $autoVal ? 'mapped' : '' }}"
                                onchange="this.className = this.value && this.value !== '__skip__' ? 'imp-select mapped' : 'imp-select'"
                            >
                                <option value="__skip__">-- Lewati / Tidak ada --</option>
                                @foreach ($excelHeaders as $header)
                                    <option value="{{ $header }}" {{ $autoVal === $header ? 'selected' : '' }}>
                                        {{ $header }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach

                <div style="margin-top:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding-top:16px;border-top:1px solid #e5e7eb;">
                    <form action="{{ route('inventory.import.clear') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="imp-btn imp-btn-gray">← Ulangi Upload</button>
                    </form>
                    <button type="submit" class="imp-btn imp-btn-success" onclick="this.disabled=true; this.innerHTML='⏳ Mengimport...'; this.form.submit();">
                        🚀 Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ========================================================== --}}
    {{-- STEP 3: RESULT                                              --}}
    {{-- ========================================================== --}}
    @if ($step === 'result')
    @php $imported = $result['imported'] ?? 0; $skipped = $result['skipped'] ?? 0; $errs = $result['errors'] ?? []; @endphp
    <div class="imp-card" style="text-align:center;">
        <div class="imp-result-icon {{ $imported > 0 ? 'success' : 'warn' }}">
            {{ $imported > 0 ? '✅' : '⚠️' }}
        </div>
        <h2 style="font-size:22px;font-weight:800;margin:0 0 6px;">
            {{ $imported > 0 ? 'Import Berhasil!' : 'Tidak Ada Data Terimport' }}
        </h2>
        <p style="font-size:13px;color:#6b7280;margin:0 0 24px;">
            {{ $imported > 0 ? "Semua baris valid berhasil dimasukkan ke database." : "Periksa kembali mapping kolom atau isi file Excel." }}
        </p>

        <div class="imp-stats">
            <div class="imp-stat success">
                <div class="imp-stat-num">{{ $imported }}</div>
                <div class="imp-stat-label">Data Berhasil Diimport</div>
            </div>
            <div class="imp-stat warn">
                <div class="imp-stat-num">{{ $skipped }}</div>
                <div class="imp-stat-label">Data Dilewati / Gagal</div>
            </div>
        </div>

        @if (!empty($errs))
        <div class="imp-err-box" style="text-align:left;margin-bottom:20px;">
            <h4>Detail Error:</h4>
            <ul>
                @foreach (array_slice($errs, 0, 10) as $err)
                    <li>{{ $err }}</li>
                @endforeach
                @if (count($errs) > 10)
                    <li style="color:#9ca3af;">...dan {{ count($errs) - 10 }} error lainnya.</li>
                @endif
            </ul>
        </div>
        @endif

        <div class="imp-actions">
            <a href="{{ \App\Filament\Resources\Inventories\InventoryResource::getUrl('index') }}" class="imp-btn imp-btn-primary">
                📋 Lihat Daftar Inventory
            </a>
            {{-- Reset session result then go back to upload --}}
            <form action="{{ route('inventory.import.upload') }}" method="GET" style="flex:1;">
                <button type="button" onclick="clearAndRestart()" class="imp-btn imp-btn-gray" style="width:100%;">
                    📂 Import File Lain
                </button>
            </form>
        </div>
    </div>

    <script>
    function clearAndRestart() {
        fetch('/admin/inventory-import/clear', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' } })
            .catch(() => {})
            .finally(() => { window.location.href = '{{ route('filament.admin.resources.inventories.import') }}'; });
    }
    </script>
    @endif

</div>
</x-filament-panels::page>
