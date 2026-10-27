@extends('layouts.app')
@section('title', 'Import ' . $typeLabel)
@section('breadcrumb', 'Asset > ' . $typeLabel . ' > Import CSV')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-file-csv"></i> Import Data {{ $typeLabel }}</h1>
    <a href="{{ route('assets.type', $type) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card" style="max-width:750px">
    <div class="card-body">

        @if(!isset($headers))
        {{-- ─── STEP 1: Upload file ─────────────────────────────────────── --}}
        <div style="background:#f0f9f0;border:1px solid #b7dfb7;border-radius:8px;padding:16px;margin-bottom:20px">
            <p style="font-size:13px;color:#444;margin:0">
                <i class="fas fa-info-circle" style="color:#3a7d34"></i>
                Upload file CSV dari Excel kamu. Setelah upload, kamu bisa <strong>memetakan kolom</strong> sesuai nama yang ada di file kamu.
                Tidak perlu mengganti nama kolom di Excel.
            </p>
        </div>

        <form action="{{ route('assets.type.import', $type) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label style="font-weight:600;font-size:14px">Pilih File CSV <span style="color:#e74c3c">*</span></label>
                <p style="font-size:12px;color:#888;margin-bottom:6px">Simpan file Excel sebagai CSV (UTF-8) terlebih dahulu</p>
                <input type="file" name="file" class="form-control" accept=".csv,.txt" required
                    style="padding:12px;border:2px dashed #3a7d34;border-radius:8px;background:#f9fff9;font-size:13px">
                @error('file')<div style="color:#e74c3c;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:15px">
                <i class="fas fa-arrow-right"></i> Lanjut: Petakan Kolom
            </button>
        </form>

        @else
        {{-- ─── STEP 2: Mapping kolom ──────────────────────────────────── --}}
        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px">
            <i class="fas fa-columns" style="color:#f39c12"></i>
            <strong>Petakan kolom CSV ke field sistem.</strong>
            Sistem mendeteksi <strong>{{ count($headers) }} kolom</strong> di file kamu. Pilih kolom yang sesuai untuk setiap field.
        </div>

        {{-- Preview baris pertama data --}}
        @if(isset($preview) && count($preview))
        <div style="margin-bottom:20px;overflow-x:auto">
            <p style="font-size:12px;font-weight:600;color:#666;margin-bottom:6px">Preview 3 baris data pertama:</p>
            <table style="font-size:11px;width:100%;border-collapse:collapse">
                <thead>
                    <tr>
                        @foreach($headers as $h)
                        <th style="padding:5px 8px;background:#e8f5e9;border:1px solid #c8e6c9;white-space:nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($preview, 0, 3) as $row)
                    <tr>
                        @foreach($row as $cell)
                        <td style="padding:4px 8px;border:1px solid #eee;white-space:nowrap;max-width:150px;overflow:hidden;text-overflow:ellipsis" title="{{ $cell }}">{{ Str::limit($cell, 30) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <form action="{{ route('assets.type.import.process', $type) }}" method="POST">
            @csrf
            <input type="hidden" name="tmp_file" value="{{ $tmpFile }}">

            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#f5f5f5">
                        <th style="padding:10px 12px;text-align:left;font-size:13px;border-bottom:2px solid #ddd;width:38%">Field Sistem</th>
                        <th style="padding:10px 12px;text-align:left;font-size:13px;border-bottom:2px solid #ddd">Kolom di CSV kamu</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    // Field definitions with keyword hints for fuzzy matching
                    $fields = [
                        'lokasi'      => ['label' => 'Lokasi / Cabang',        'required' => true,  'keywords' => ['lokasi','location','cabang','kantor','outlet','branch']],
                        'brand'       => ['label' => 'Brand / Manufaktur',     'required' => false, 'keywords' => ['brand','manufaktur','manufacturer','merk','vendor','pabrikan']],
                        'model'       => ['label' => 'Device Model / Nama',    'required' => false, 'keywords' => ['model','nama','name','device','tipe','type','seri']],
                        'processor'   => ['label' => 'Processor / CPU',        'required' => false, 'keywords' => ['processor','cpu','prosesor','komponen - proc','komponen proc']],
                        'memory_type' => ['label' => 'Memory / RAM',           'required' => false, 'keywords' => ['memory','ram','memori','komponen - mem','komponen mem']],
                        'hard_drive'  => ['label' => 'Storage / Hard Drive',   'required' => false, 'keywords' => ['hard_drive','storage','harddisk','hard drive','hdd','ssd','komponen - stor','disk']],
                        'user'        => ['label' => 'Pengguna / User',        'required' => false, 'keywords' => ['user','pengguna','pemakai','nama pengguna','penanggung','pegawai','karyawan']],
                        'grup'        => ['label' => 'Group / Divisi',         'required' => false, 'keywords' => ['grup','group','divisi','departemen','department','bagian','entitas']],
                        'keterangan'  => ['label' => 'Keterangan / Deskripsi', 'required' => false, 'keywords' => ['keterangan','deskripsi','description','catatan','note','status','kondisi']],
                    ];

                    // Fuzzy match: check if any keyword is contained in the header (or vice versa)
                    $autoSelect = function(string $fieldKey, array $keywords, array $headers): int|string {
                        foreach ($headers as $idx => $header) {
                            $h = strtolower(trim($header));
                            $hNorm = preg_replace('/[\s\-_]+/', ' ', $h);
                            // Exact match after normalization
                            $fNorm = preg_replace('/[\s\-_]+/', ' ', $fieldKey);
                            if ($hNorm === $fNorm) return $idx;
                            // Keyword match
                            foreach ($keywords as $kw) {
                                if (str_contains($hNorm, $kw) || str_contains($kw, $hNorm)) {
                                    return $idx;
                                }
                            }
                        }
                        return '';
                    };
                    @endphp

                    @foreach($fields as $fieldKey => $fieldInfo)
                    @php $autoIdx = $autoSelect($fieldKey, $fieldInfo['keywords'], $headers); @endphp
                    <tr style="border-bottom:1px solid #f0f0f0">
                        <td style="padding:10px 12px;font-size:13px">
                            {{ $fieldInfo['label'] }}
                            @if($fieldInfo['required'])<span style="color:#e74c3c;font-size:11px"> *Wajib</span>@endif
                            @if($autoIdx !== '')<span style="color:#3a7d34;font-size:11px;display:block">✓ auto-terdeteksi</span>@endif
                        </td>
                        <td style="padding:8px 12px">
                            <select name="mapping[{{ $fieldKey }}]" class="form-control" style="font-size:13px">
                                <option value="">— Lewati kolom ini —</option>
                                @foreach($headers as $idx => $header)
                                <option value="{{ $idx }}" {{ $idx == $autoIdx ? 'selected' : '' }}>
                                    {{ $header }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:20px;display:flex;gap:10px">
                <a href="{{ route('assets.type.import', $type) }}" class="btn btn-secondary" style="flex:1;text-align:center;padding:12px">
                    <i class="fas fa-redo"></i> Upload Ulang
                </a>
                <button type="submit" class="btn btn-primary" style="flex:2;padding:12px;font-size:15px">
                    <i class="fas fa-database"></i> Mulai Import Data
                </button>
            </div>
        </form>
        @endif

    </div>
</div>
@endsection
