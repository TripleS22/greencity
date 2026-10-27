@extends('layouts.app')
@section('title', 'Project Detail')
@section('breadcrumb', 'Project > ' . $project->name)

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h1><i class="fas fa-project-diagram"></i> {{ $project->name }}</h1>
    <a href="{{ route('projects.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Kanban</a>
</div>

<div class="row">
    <!-- Project Info -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <strong>Informasi Project</strong>
            </div>
            <div class="card-body">
                <p><strong>Status:</strong> <span class="badge badge-open">{{ $project->status }}</span></p>
                <p><strong>Stage:</strong> {{ ucfirst($project->stage ?? 'Backlog') }}</p>
                <p><strong>Due Date:</strong> {{ $project->due_date?->format('d M Y') ?? 'Not Set' }}</p>
                <p><strong>Assignee:</strong> {{ $project->user->name ?? '-' }}</p>
                <p><strong>Group:</strong> {{ $project->group->name ?? '-' }}</p>
                
                <hr>
                <strong>Deskripsi:</strong>
                <p style="white-space:pre-wrap; margin-top:10px;">{{ $project->description ?? 'Tidak ada deskripsi.' }}</p>
            </div>
        </div>
    </div>

    <!-- Checklists -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <strong><i class="fas fa-list-check"></i> Project Checklists (Sub-Tasks)</strong>
                <span id="progress-text" style="font-weight:bold; color:var(--sidebar-bg);">Progress: {{ $project->progress }}%</span>
            </div>
            
            <div class="card-body">
                <!-- Progress Bar -->
                <div class="progress" style="height: 15px; margin-bottom: 20px;">
                    <div id="progress-bar" class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $project->progress }}%" 
                         aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>

                <!-- Setup Add New Task Form -->
                <form id="form-add-checklist" style="display:flex; gap:10px; margin-bottom:20px;">
                    <input type="text" id="checklist-title" class="form-control" placeholder="Tuliskan sub-task baru di sini..." required>
                    <button type="submit" class="btn btn-primary" style="white-space:nowrap;"><i class="fas fa-plus"></i> Tambah</button>
                </form>

                <!-- Checklists List -->
                <ul class="list-group" id="checklist-container">
                    @forelse($project->checklists as $cl)
                    <li class="list-group-item" style="display:flex; justify-content:space-between; align-items:center;" id="checklist-{{ $cl->id }}">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" class="checklist-toggle" data-id="{{ $cl->id }}" 
                                   style="width:18px; height:18px; cursor:pointer;" 
                                   {{ $cl->is_completed ? 'checked' : '' }}>
                            
                            <span class="checklist-title {{ $cl->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                                {{ $cl->title }}
                            </span>
                        </div>
                        <button class="btn btn-sm btn-danger btn-delete-checklist" data-id="{{ $cl->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted" id="empty-checklist">Belum ada sub-task...</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const projectId = {{ $project->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
        const container = document.getElementById('checklist-container');
        const emptyState = document.getElementById('empty-checklist');
        
        function updateProgressUI(progress) {
            document.getElementById('progress-bar').style.width = progress + '%';
            document.getElementById('progress-text').innerText = 'Progress: ' + progress + '%';
        }

        // --- 1. Add Checklist ---
        document.getElementById('form-add-checklist').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('checklist-title');
            const title = input.value;
            if(!title.trim()) return;

            fetch(`/projects/${projectId}/checklists`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title: title })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    input.value = '';
                    if(emptyState) emptyState.remove();
                    
                    const cl = data.checklist;
                    const li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.style.cssText = 'display:flex; justify-content:space-between; align-items:center;';
                    li.id = 'checklist-' + cl.id;
                    li.innerHTML = `
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" class="checklist-toggle" data-id="${cl.id}" style="width:18px; height:18px; cursor:pointer;">
                            <span class="checklist-title">${cl.title}</span>
                        </div>
                        <button class="btn btn-sm btn-danger btn-delete-checklist" data-id="${cl.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    container.appendChild(li);
                    updateProgressUI(data.progress);
                }
            });
        });

        // --- 2. Toggle Completion & Delete (Event Delegation) ---
        container.addEventListener('click', function(e) {
            // Toggle
            if (e.target.classList.contains('checklist-toggle')) {
                const checkbox = e.target;
                const id = checkbox.getAttribute('data-id');
                const isCompleted = checkbox.checked;
                const textSpan = checkbox.nextElementSibling;
                
                // Visual update immediately
                if (isCompleted) {
                    textSpan.classList.add('text-decoration-line-through', 'text-muted');
                } else {
                    textSpan.classList.remove('text-decoration-line-through', 'text-muted');
                }

                fetch(`/project-checklists/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ is_completed: isCompleted })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) updateProgressUI(data.progress);
                });
            }

            // Delete
            if (e.target.closest('.btn-delete-checklist')) {
                const btn = e.target.closest('.btn-delete-checklist');
                
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Hapus sub-task ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const id = btn.getAttribute('data-id');
                        
                        fetch(`/project-checklists/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('checklist-' + id).remove();
                                updateProgressUI(data.progress);
                                if(container.children.length === 0) {
                                    container.innerHTML = '<li class="list-group-item text-center text-muted" id="empty-checklist">Belum ada sub-task...</li>';
                                }
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endpush
