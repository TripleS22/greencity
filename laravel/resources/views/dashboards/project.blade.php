@extends('layouts.app')
@section('title', 'Project Dashboard')
@section('breadcrumb', 'Project > Dashboard')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-project-diagram"></i> Project Dashboard</h1>
</div>

{{-- Stat Cards --}}
<div class="stat-grid">
    <div class="stat-card bg-blue">
        <div class="icon"><i class="fas fa-folder-open"></i></div>
        <div class="count">{{ $projectStats['total'] }}</div>
        <div class="label">Total Projects</div>
    </div>
    <div class="stat-card bg-green">
        <div class="icon"><i class="fas fa-play-circle"></i></div>
        <div class="count">{{ $projectStats['active'] }}</div>
        <div class="label">Active</div>
    </div>
    <div class="stat-card bg-orange">
        <div class="icon"><i class="fas fa-clock"></i></div>
        <div class="count">{{ $projectStats['pending'] }}</div>
        <div class="label">Pending</div>
    </div>
    <div class="stat-card bg-teal">
        <div class="icon"><i class="fas fa-check-double"></i></div>
        <div class="count">{{ $projectStats['completed'] }}</div>
        <div class="label">Completed</div>
    </div>
    <div class="stat-card bg-purple">
        <div class="icon"><i class="fas fa-server"></i></div>
        <div class="count">{{ $systemAssetCount }}</div>
        <div class="label">System Assets</div>
    </div>
    <div class="stat-card bg-dark">
        <div class="icon"><i class="fas fa-user-shield"></i></div>
        <div class="count">{{ $accountSystemCount }}</div>
        <div class="label">Account Systems</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    {{-- Recent Projects --}}
    <div class="card">
        <div class="card-header">Project Terbaru</div>
        <div class="card-body">
            <table>
                <thead><tr><th>#</th><th>Name</th><th>Status</th><th>Stage</th><th>Due Date</th></tr></thead>
                <tbody>
                    @forelse($recentProjects as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ Str::limit($p->name, 30) }}</strong></td>
                        <td>
                            @php $sc = ['active'=>'badge-progress','pending'=>'badge-open','completed'=>'badge-closed']; @endphp
                            <span class="badge {{ $sc[$p->status] ?? 'badge-open' }}">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td>{{ $p->stage ?? '-' }}</td>
                        <td>{{ $p->due_date ? \Carbon\Carbon::parse($p->due_date)->format('d M Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:#999;padding:20px">Belum ada project</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Overdue Projects --}}
    <div class="card">
        <div class="card-header" style="color:#e74c3c"><i class="fas fa-exclamation-triangle"></i> Overdue Projects</div>
        <div class="card-body">
            <table>
                <thead><tr><th>#</th><th>Name</th><th>Status</th><th>Due Date</th><th>Overdue</th></tr></thead>
                <tbody>
                    @forelse($overdueProjects as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ Str::limit($p->name, 25) }}</strong></td>
                        <td><span class="badge badge-open">{{ ucfirst($p->status) }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($p->due_date)->format('d M Y') }}</td>
                        <td style="color:#e74c3c;font-weight:600">{{ \Carbon\Carbon::parse($p->due_date)->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:#27ae60;padding:20px"><i class="fas fa-check-circle"></i> Tidak ada project yang overdue 🎉</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
