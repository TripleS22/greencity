@extends('layouts.app')
@section('title', 'Projects')
@section('breadcrumb', 'Project > Kanban Board')

@push('styles')
<style>
    /* Light Theme Kanban with Sticky Note Cards */
    .kanban-board { display: flex; gap: 20px; overflow-x: auto; padding-bottom: 20px; align-items: stretch; height: calc(100vh - 200px); }
    
    /* Column styling: light gray background */
    .kanban-column { flex: 0 0 280px; background: #eef2f5; border-radius: 8px; padding: 15px; display: flex; flex-direction: column; border: 1px solid #dce2e8; }
    .kanban-header { font-weight: 700; color: #2c3e50; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 8px; }
    
    .kanban-cards { flex: 1; min-height: 100px; display: flex; flex-direction: column; gap: 15px; }
    
    /* Sticky Note Card Base */
    .kanban-card { 
        padding: 16px; 
        border-radius: 4px; /* Slight rounding, mostly square */
        box-shadow: 2px 3px 6px rgba(0,0,0,0.1); 
        cursor: grab; 
        position: relative; 
        transition: transform 0.2s, box-shadow 0.2s; 
        color: #fff; /* White text for contrast on colored notes */
    }
    .kanban-card:active { cursor: grabbing; transform: scale(0.98); box-shadow: 1px 2px 4px rgba(0,0,0,0.15); }
    
    /* Sticky Note Content */
    .kanban-card h4 { font-size: 15px; font-weight: 700; margin: 0 0 10px 0; color: rgba(255,255,255,0.95); line-height: 1.3;}
    .kanban-card .meta { font-size: 11px; color: rgba(255,255,255,0.85); margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    
    /* Progress bar tuned for sticky notes */
    .progress-bar-container { height: 4px; background: rgba(0,0,0,0.2); border-radius: 2px; overflow: hidden; margin-top: 8px; }
    .progress-bar-fill { height: 100%; background: #fff; border-radius: 2px; transition: width 0.3s; }
    
    /* Actions: slightly dimmed so they don't distract */
    .kanban-card .actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 12px; }
    .kanban-card .actions a, .kanban-card .actions button { cursor: pointer; color: rgba(255,255,255,0.6); border: none; background: transparent; padding: 0; font-size: 13px; transition: color 0.2s; }
    .kanban-card .actions a:hover, .kanban-card .actions button:hover { color: #fff; }
    
    /* Note specific colors mimicking the user's reference */
    .note-gray { background: #7f8c8d; }
    .note-gold { background: #d4ac0d; }
    .note-blue { background: #2980b9; }
    .note-teal { background: #16a085; }
    .note-salmon { background: #e74c3c; } /* Replacing pure red with salmon/red */
    .note-green { background: #27ae60; }
</style>
@endpush

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
    <h1><i class="fas fa-project-diagram"></i> IT Development Board</h1>
    <a href="{{ route('projects.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Project</a>
</div>

<div class="kanban-board">
    @php
        $columns = [
            'update'     => ['label' => 'Update', 'noteClass' => 'note-gray'],
            'planning'   => ['label' => 'Planning', 'noteClass' => 'note-gold'],
            'design'     => ['label' => 'Design', 'noteClass' => 'note-teal'],
            'develop'    => ['label' => 'Develop', 'noteClass' => 'note-blue'],
            'testing'    => ['label' => 'Testing', 'noteClass' => 'note-blue'],
            'deployment' => ['label' => 'Deployment', 'noteClass' => 'note-salmon'],
            'review'     => ['label' => 'Review', 'noteClass' => 'note-salmon'],
            'launch'     => ['label' => 'Launch', 'noteClass' => 'note-salmon'],
            'done'       => ['label' => 'Done', 'noteClass' => 'note-green'],
        ];
    @endphp

    @foreach($columns as $key => $col)
    <div class="kanban-column" data-stage="{{ $key }}">
        <div class="kanban-header">
            <span>{{ $col['label'] }}</span>
            <span class="badge" style="background:#ddd; color:#333">{{ count($projects[$key] ?? []) }}</span>
        </div>
        
        <div class="kanban-cards" id="stage-{{ $key }}">
            @foreach($projects[$key] ?? [] as $p)
            <div class="kanban-card {{ $col['noteClass'] }}" data-id="{{ $p->id }}" data-original-class="{{ $col['noteClass'] }}">
                <a href="{{ route('projects.show', $p->id) }}" style="text-decoration:none; color:inherit; display:block;">
                    <h4>{{ $p->name }}</h4>
                    <div class="meta"><i class="fas fa-user-circle"></i> {{ $p->user->name ?? 'Unassigned' }}</div>
                    @if($p->due_date)
                    <div class="meta"><i class="far fa-calendar-alt"></i> {{ $p->due_date->format('d M Y') }}</div>
                    @endif
                    
                    <div class="meta" style="justify-content:space-between; margin-top:10px;">
                        <span><i class="fas fa-tasks"></i> Progress</span>
                        <strong>{{ $p->progress }}%</strong>
                    </div>
                    <div class="progress-bar-container"><div class="progress-bar-fill" style="width: {{ $p->progress }}%"></div></div>
                </a>
                
                <div class="actions">
                    <a href="{{ route('projects.edit', $p) }}" title="Edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('projects.destroy', $p) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button type="submit" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const columns = document.querySelectorAll('.kanban-cards');
        columns.forEach(col => {
            new Sortable(col, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    const itemEl = evt.item;  // dragged HTMLElement
                    const toEl = evt.to;      // target list
                    
                    const projectId = itemEl.getAttribute('data-id');
                    const newStage = toEl.closest('.kanban-column').getAttribute('data-stage');
                    
                    // Call backend API to update stage
                    fetch(`/projects/${projectId}/stage`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ stage: newStage })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update badge counts dynamically
                            updateBadgeCounts();
                            
                            // Remove old note color class
                            const currentClass = Array.from(itemEl.classList).find(c => c.startsWith('note-'));
                            if (currentClass) {
                                itemEl.classList.remove(currentClass);
                            }
                            
                            // Determine new color class from the column's first child card (if any)
                            // or ideally from a data-attribute on the column. But for simplicity, we map it:
                            const colorMap = {
                                'update': 'note-gray', 'planning': 'note-gold', 
                                'design': 'note-teal', 'develop': 'note-blue', 'testing': 'note-blue',
                                'deployment': 'note-salmon', 'review': 'note-salmon', 'launch': 'note-salmon',
                                'done': 'note-green'
                            };
                            itemEl.classList.add(colorMap[newStage]);
                            
                        } else {
                            alert('Gagal memindahkan kartu.');
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                    });
                },
            });
        });

        function updateBadgeCounts() {
            document.querySelectorAll('.kanban-column').forEach(col => {
                const count = col.querySelectorAll('.kanban-card').length;
                col.querySelector('.badge').innerText = count;
            });
        }
    });
</script>
<style>
    .sortable-ghost { opacity: 0.4; background: #e0e0e0; }
</style>
@endpush
