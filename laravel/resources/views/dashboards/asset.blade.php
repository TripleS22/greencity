@extends('layouts.app')
@section('title', 'Asset Dashboard')
@section('breadcrumb', 'Asset > Dashboard')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Asset Dashboard</h1>
    <span style="font-size:13px;color:#777">Total Asset: <strong style="color:var(--sidebar-bg)">{{ $totalAssets }}</strong></span>
</div>

{{-- Stat Cards --}}
<div class="stat-grid">
    @php
        $colors = ['bg-green','bg-blue','bg-teal','bg-orange','bg-red','bg-purple','bg-dark','bg-cyan','bg-green','bg-blue','bg-teal','bg-orange','bg-red'];
        $i = 0;
    @endphp
    @foreach($assetCounts as $type => $data)
        <a href="{{ route('assets.type', $type) }}" style="text-decoration:none">
            <div class="stat-card {{ $colors[$i++ % count($colors)] }}">
                <div class="icon"><i class="{{ $data['icon'] }}"></i></div>
                <div class="count">{{ $data['count'] }}</div>
                <div class="label">{{ $data['label'] }}</div>
            </div>
        </a>
    @endforeach
</div>

{{-- Charts --}}
<div class="chart-grid">
    <div class="card">
        <div class="card-header">Asset by Brand</div>
        <div class="card-body"><div class="chart-container"><canvas id="brandChart"></canvas></div></div>
    </div>
    <div class="card">
        <div class="card-header">Asset by Location</div>
        <div class="card-body"><div class="chart-container"><canvas id="locationChart"></canvas></div></div>
    </div>
    <div class="card">
        <div class="card-header">Asset by Group</div>
        <div class="card-body"><div class="chart-container"><canvas id="groupChart"></canvas></div></div>
    </div>
</div>

{{-- Recent Assets --}}
<div class="card">
    <div class="card-header">Asset Terbaru</div>
    <div class="card-body">
        <table>
            <thead><tr><th>#</th><th>Code</th><th>Type</th><th>Brand</th><th>Location</th><th>Pengguna</th><th>Created</th></tr></thead>
            <tbody>
                @foreach($recentAssets as $a)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $a->code_asset }}</strong></td>
                    <td><span class="badge badge-open">{{ \App\Models\Asset::TYPES[$a->asset_type] ?? $a->asset_type }}</span></td>
                    <td>{{ $a->brand->name ?? '-' }}</td>
                    <td>{{ $a->location->name ?? '-' }}</td>
                    <td>{{ $a->employee->name ?? '-' }}</td>
                    <td>{{ $a->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const palette = ['#4a8a3c','#2980b9','#e67e22','#e74c3c','#8e44ad','#17a2b8','#2c3e50','#f39c12','#1abc9c','#d35400'];

function donut(id, labels, data) {
    new Chart(document.getElementById(id), {
        type:'doughnut',
        data:{ labels, datasets:[{ data, backgroundColor:palette.slice(0,data.length), borderWidth:1 }] },
        options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'right', labels:{ font:{size:11} } } } }
    });
}

donut('brandChart', {!! json_encode($assetsByBrand->pluck('label')) !!}, {!! json_encode($assetsByBrand->pluck('value')) !!});
donut('locationChart', {!! json_encode($assetsByLocation->pluck('label')) !!}, {!! json_encode($assetsByLocation->pluck('value')) !!});
donut('groupChart', {!! json_encode($assetsByGroup->pluck('label')) !!}, {!! json_encode($assetsByGroup->pluck('value')) !!});
</script>
@endpush
