@extends('layouts.app')
@section('title', 'Detail Asset')
@section('breadcrumb', 'Asset > Detail')

@section('content')
<div class="page-header">
    <h1><i class="{{ \App\Models\Asset::TYPE_ICONS[$asset->asset_type] }}"></i> {{ $asset->code_asset }}</h1>
    <div>
        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
        <a href="{{ route('assets.type', $asset->asset_type) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="card">
    <div class="card-header">Detail Asset - {{ \App\Models\Asset::TYPES[$asset->asset_type] }}</div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13px">
            <div><strong>Code Asset:</strong> {{ $asset->code_asset }}</div>
            <div><strong>Type:</strong> {{ \App\Models\Asset::TYPES[$asset->asset_type] }}</div>
            <div><strong>Brand:</strong> {{ $asset->brand->name ?? '-' }}</div>
            <div><strong>Model:</strong> {{ $asset->deviceModel->name ?? '-' }}</div>
            <div><strong>Location:</strong> {{ $asset->location->name ?? '-' }}</div>
            <div><strong>Group:</strong> {{ $asset->group->name ?? '-' }}</div>
            <div><strong>Pengguna:</strong> {{ $asset->employee->name ?? '-' }}</div>
            <div><strong>Jabatan:</strong> {{ $asset->employee->jabatan ?? '-' }}</div>
            <div><strong>RAM:</strong> {{ $asset->ram->spec ?? '-' }}</div>
            <div><strong>ROM:</strong> {{ $asset->rom->spec ?? '-' }}</div>
            <div><strong>CPU:</strong> {{ $asset->cpu->spec ?? '-' }}</div>
            <div><strong>VGA:</strong> {{ $asset->vga->spec ?? '-' }}</div>
            <div><strong>Created:</strong> {{ $asset->created_at->format('d M Y H:i') }}</div>
            @if($asset->asset_type === 'cctv')
            <div><strong><i class="fas fa-key"></i> Kode Akses:</strong>
                @if($asset->access_code)
                    <code style="background:#f0f0f0;padding:2px 8px;border-radius:4px;font-size:13px">{{ $asset->access_code }}</code>
                @else
                    -
                @endif
            </div>
            @endif
        </div>
        @if($asset->description)
        <div style="margin-top:16px">
            <strong>Description:</strong>
            <p style="margin-top:4px;color:#555">{{ $asset->description }}</p>
        </div>
        @endif
        @if($asset->photo)
        <div style="margin-top:16px">
            <strong>Photo:</strong><br>
            <img src="{{ asset('storage/'.$asset->photo) }}" alt="Photo" style="max-width:400px;border-radius:8px;margin-top:8px">
        </div>
        @endif
    </div>
</div>
@endsection
