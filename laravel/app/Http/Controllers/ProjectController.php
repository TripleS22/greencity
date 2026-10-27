<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SystemAsset;
use App\Models\AccountSystem;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    // ── Projects ────────────────────────────────
    public function index()
    {
        $allProjects = Project::with(['user', 'group', 'checklists'])->latest()->get();
        // Standardize columns into a collection (9 stages)
        $projects = [
            'update' => $allProjects->where('stage', 'update')->values(),
            'planning' => $allProjects->where('stage', 'planning')->values(),
            'design' => $allProjects->where('stage', 'design')->values(),
            'develop' => $allProjects->where('stage', 'develop')->values(),
            'testing' => $allProjects->where('stage', 'testing')->values(),
            'deployment' => $allProjects->where('stage', 'deployment')->values(),
            'review' => $allProjects->where('stage', 'review')->values(),
            'launch' => $allProjects->where('stage', 'launch')->values(),
            'done' => $allProjects->where('stage', 'done')->values(),
        ];
        // Put uncategorized projects into 'update' (the first stage)
        $validStages = ['update', 'planning', 'design', 'develop', 'testing', 'deployment', 'review', 'launch', 'done'];
        $uncategorized = $allProjects->whereNotIn('stage', $validStages)->values();
        $projects['update'] = $projects['update']->merge($uncategorized);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $groups = Group::orderBy('name')->get();
        return view('projects.create', compact('users', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'task' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|string',
            'stage' => 'nullable|string',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'group_id' => 'nullable|exists:groups,id',
        ]);
        Project::create($validated);
        return redirect()->route('projects.index')->with('success', 'Project berhasil dibuat!');
    }

    public function show(Project $project)
    {
        $project->load(['user', 'group', 'checklists']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $users = User::orderBy('name')->get();
        $groups = Group::orderBy('name')->get();
        return view('projects.edit', compact('project', 'users', 'groups'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'task' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|string',
            'stage' => 'nullable|string',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'group_id' => 'nullable|exists:groups,id',
        ]);
        $project->update($validated);
        return redirect()->route('projects.index')->with('success', 'Project berhasil diupdate!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project berhasil dihapus!');
    }

    // ── Kanban & Checklists AJAX Endpoints ──────
    public function updateStage(Request $request, Project $project)
    {
        $validated = $request->validate([
            'stage' => 'required|string',
        ]);
        $project->update(['stage' => $validated['stage']]);
        return response()->json(['success' => true]);
    }

    public function storeChecklist(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);
        $checklist = $project->checklists()->create($validated);
        return response()->json(['success' => true, 'checklist' => $checklist, 'progress' => $project->progress]);
    }

    public function updateChecklist(Request $request, \App\Models\ProjectChecklist $checklist)
    {
        $validated = $request->validate([
            'is_completed' => 'required|boolean',
        ]);
        $checklist->update(['is_completed' => $validated['is_completed']]);
        return response()->json(['success' => true, 'progress' => $checklist->project->progress]);
    }

    public function destroyChecklist(\App\Models\ProjectChecklist $checklist)
    {
        $project = $checklist->project;
        $checklist->delete();
        return response()->json(['success' => true, 'progress' => $project->progress]);
    }

    // ── System Assets ───────────────────────────
    public function systemAssets()
    {
        $items = SystemAsset::latest()->paginate(15);
        return view('projects.system_assets.index', compact('items'));
    }

    public function systemAssetsCreate() { return view('projects.system_assets.create'); }

    public function systemAssetsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string',
            'zip_file' => 'nullable|file|max:10240',
            'db_backup' => 'nullable|file|max:10240',
            'github_url' => 'nullable|string',
            'version' => 'nullable|string',
            'tech_stack' => 'nullable|string',
            'hoster' => 'nullable|string',
        ]);
        if ($request->hasFile('zip_file')) $validated['zip_file'] = $request->file('zip_file')->store('system_assets', 'public');
        if ($request->hasFile('db_backup')) $validated['db_backup'] = $request->file('db_backup')->store('system_assets', 'public');
        SystemAsset::create($validated);
        return redirect()->route('system_assets.index')->with('success', 'System Asset berhasil ditambahkan!');
    }

    public function systemAssetsEdit(SystemAsset $systemAsset)
    {
        return view('projects.system_assets.edit', ['item' => $systemAsset]);
    }

    public function systemAssetsUpdate(Request $request, SystemAsset $systemAsset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string',
            'zip_file' => 'nullable|file|max:10240',
            'db_backup' => 'nullable|file|max:10240',
            'github_url' => 'nullable|string',
            'version' => 'nullable|string',
            'tech_stack' => 'nullable|string',
            'hoster' => 'nullable|string',
        ]);
        if ($request->hasFile('zip_file')) {
            if ($systemAsset->zip_file) Storage::disk('public')->delete($systemAsset->zip_file);
            $validated['zip_file'] = $request->file('zip_file')->store('system_assets', 'public');
        }
        if ($request->hasFile('db_backup')) {
            if ($systemAsset->db_backup) Storage::disk('public')->delete($systemAsset->db_backup);
            $validated['db_backup'] = $request->file('db_backup')->store('system_assets', 'public');
        }
        $systemAsset->update($validated);
        return redirect()->route('system_assets.index')->with('success', 'System Asset berhasil diupdate!');
    }

    public function systemAssetsDestroy(SystemAsset $systemAsset)
    {
        if ($systemAsset->zip_file) Storage::disk('public')->delete($systemAsset->zip_file);
        if ($systemAsset->db_backup) Storage::disk('public')->delete($systemAsset->db_backup);
        $systemAsset->delete();
        return redirect()->route('system_assets.index')->with('success', 'System Asset berhasil dihapus!');
    }

    // ── Account Systems ─────────────────────────
    public function accountSystems()
    {
        $items = AccountSystem::latest()->paginate(15);
        return view('projects.account_systems.index', compact('items'));
    }

    public function accountSystemsCreate() { return view('projects.account_systems.create'); }

    public function accountSystemsStore(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'platform_url' => 'nullable|string',
        ]);
        AccountSystem::create($validated);
        return redirect()->route('account_systems.index')->with('success', 'Account berhasil ditambahkan!');
    }

    public function accountSystemsEdit(AccountSystem $accountSystem) { return view('projects.account_systems.edit', ['item' => $accountSystem]); }

    public function accountSystemsUpdate(Request $request, AccountSystem $accountSystem)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'nullable|string',
            'platform_url' => 'nullable|string',
        ]);
        if (empty($validated['password'])) unset($validated['password']);
        $accountSystem->update($validated);
        return redirect()->route('account_systems.index')->with('success', 'Account berhasil diupdate!');
    }

    public function accountSystemsDestroy(AccountSystem $accountSystem)
    {
        $accountSystem->delete();
        return redirect()->route('account_systems.index')->with('success', 'Account berhasil dihapus!');
    }
}
