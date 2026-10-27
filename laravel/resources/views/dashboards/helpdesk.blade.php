@extends('layouts.app')
@section('title', 'Helpdesk Dashboard')
@section('breadcrumb', 'Helpdesk > Dashboard')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-headset"></i> Helpdesk Dashboard</h1>
</div>

{{-- Ticket Stat Cards --}}
<div class="stat-grid">
    <div class="stat-card bg-blue">
        <div class="icon"><i class="fas fa-ticket-alt"></i></div>
        <div class="count">{{ $ticketStats['total'] }}</div>
        <div class="label">Total Tickets</div>
    </div>
    <div class="stat-card bg-orange">
        <div class="icon"><i class="fas fa-envelope-open"></i></div>
        <div class="count">{{ $ticketStats['open'] }}</div>
        <div class="label">Open</div>
    </div>
    <div class="stat-card bg-teal">
        <div class="icon"><i class="fas fa-spinner"></i></div>
        <div class="count">{{ $ticketStats['progress'] }}</div>
        <div class="label">In Progress</div>
    </div>
    <div class="stat-card bg-green">
        <div class="icon"><i class="fas fa-check-circle"></i></div>
        <div class="count">{{ $ticketStats['closed'] }}</div>
        <div class="label">Closed</div>
    </div>
    <div class="stat-card bg-red">
        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="count">{{ $ticketStats['late'] }}</div>
        <div class="label">Overdue</div>
    </div>
    <div class="stat-card bg-purple">
        <div class="icon"><i class="fas fa-tools"></i></div>
        <div class="count">{{ $maintenanceCount }}</div>
        <div class="label">Total Maintenance</div>
    </div>
</div>

{{-- Charts --}}
<div class="chart-grid">
    <div class="card">
        <div class="card-header">Tickets by Priority</div>
        <div class="card-body"><div class="chart-container"><canvas id="priorityChart"></canvas></div></div>
    </div>
    <div class="card">
        <div class="card-header">Tickets by Month (6 bulan terakhir)</div>
        <div class="card-body"><div class="chart-container"><canvas id="monthChart"></canvas></div></div>
    </div>
</div>

{{-- Two columns: Recent Tickets + Upcoming Maintenance --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <div class="card">
        <div class="card-header">Ticket Terbaru</div>
        <div class="card-body">
            <table>
                <thead><tr><th>#</th><th>Title</th><th>Status</th><th>Priority</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($recentTickets as $t)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="{{ route('tickets.show', $t) }}" style="color:var(--sidebar-bg);font-weight:500">{{ Str::limit($t->title, 35) }}</a></td>
                        <td><span class="badge badge-{{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
                        <td><span class="badge badge-{{ $t->priority }}">{{ ucfirst($t->priority) }}</span></td>
                        <td>{{ $t->request_date ? \Carbon\Carbon::parse($t->request_date)->format('d M') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">Upcoming Maintenance</div>
        <div class="card-body">
            <table>
                <thead><tr><th>#</th><th>Category</th><th>Location</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($upcomingMaintenance as $m)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><span class="badge badge-open">{{ ucfirst($m->category) }}</span></td>
                        <td>{{ $m->location->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($m->date)->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#999;padding:20px">Tidak ada jadwal maintenance mendatang</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Priority Donut
new Chart(document.getElementById('priorityChart'), {
    type:'doughnut',
    data:{
        labels:['Low','Medium','High','Urgent'],
        datasets:[{ data:[{{ $ticketsByPriority['low'] }},{{ $ticketsByPriority['medium'] }},{{ $ticketsByPriority['high'] }},{{ $ticketsByPriority['urgent'] }}],
            backgroundColor:['#27ae60','#f39c12','#e74c3c','#8e44ad'], borderWidth:1 }]
    },
    options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'right', labels:{ font:{size:11} } } } }
});

// Monthly Bar Chart
new Chart(document.getElementById('monthChart'), {
    type:'bar',
    data:{
        labels: {!! json_encode(array_keys($ticketsByMonth)) !!},
        datasets:[
            { label:'Open', data:{!! json_encode(array_column($ticketsByMonth,'open')) !!}, backgroundColor:'#f39c12' },
            { label:'Progress', data:{!! json_encode(array_column($ticketsByMonth,'progress')) !!}, backgroundColor:'#17a2b8' },
            { label:'Closed', data:{!! json_encode(array_column($ticketsByMonth,'closed')) !!}, backgroundColor:'#27ae60' },
        ]
    },
    options:{ responsive:true, maintainAspectRatio:false, scales:{ x:{stacked:true}, y:{stacked:true, beginAtZero:true} }, plugins:{ legend:{ labels:{ font:{size:11} } } } }
});
</script>
@endpush
