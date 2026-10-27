@extends('layouts.app')
@section('title', 'Activity Log')
@section('breadcrumb', 'Administration > Activity Log')
@section('content')
<div class="page-header"><h1><i class="fas fa-clipboard-list"></i> Activity Log</h1></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table>
    <thead><tr><th>#</th><th>Date</th><th>User</th><th>Action</th><th>Model</th><th>Changes</th></tr></thead>
    <tbody>
    @forelse($activities as $a)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td style="white-space:nowrap">{{ $a->created_at->format('d M Y H:i') }}</td>
        <td>{{ $a->causer?->name ?? 'System' }}</td>
        <td><span class="badge badge-open">{{ $a->description }}</span></td>
        <td>{{ class_basename($a->subject_type ?? '') }}</td>
        <td><small style="color:#666">{{ Str::limit(json_encode($a->properties), 80) }}</small></td>
    </tr>
    @empty<tr><td colspan="6" style="text-align:center;color:#999;padding:30px">Belum ada activity</td></tr>@endforelse
    </tbody></table></div><div class="pagination-wrapper">{{ $activities->links() }}</div></div></div>
@endsection
