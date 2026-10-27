<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with(['location', 'tickets'])
            ->where('date', '>', now()->endOfDay())
            ->orderBy('date', 'asc')
            ->paginate(15);
        return view('helpdesk.maintenances.index', compact('maintenances'));
    }

    public function generate()
    {
        Artisan::call('maintenance:generate');
        $output = Artisan::output();
        
        return redirect()->route('maintenances.index')->with('success', 'Jadwal maintenance baru berhasil di-*generate*! Cek tabel untuk melihat jadwal baru.');
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        return view('helpdesk.maintenances.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'nullable|exists:locations,id',
            'category' => 'required|in:hardware,software,network,cctv,all',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'photo' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('maintenances', 'public');
        }

        Maintenance::create($validated);
        return redirect()->route('maintenances.index')->with('success', 'Jadwal maintenance berhasil ditambahkan!');
    }

    public function show(Maintenance $maintenance)
    {
        $maintenance->load(['location', 'tickets' => function($query) {
            $query->latest();
        }]);
        return view('helpdesk.maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance)
    {
        $locations = Location::orderBy('name')->get();
        return view('helpdesk.maintenances.edit', compact('maintenance', 'locations'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'location_id' => 'nullable|exists:locations,id',
            'category' => 'required|in:hardware,software,network,cctv,all',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'photo' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('photo')) {
            if ($maintenance->photo) Storage::disk('public')->delete($maintenance->photo);
            $validated['photo'] = $request->file('photo')->store('maintenances', 'public');
        }

        $maintenance->update($validated);
        return redirect()->route('maintenances.index')->with('success', 'Jadwal maintenance berhasil diupdate!');
    }

    public function destroy(Maintenance $maintenance)
    {
        if ($maintenance->photo) Storage::disk('public')->delete($maintenance->photo);
        $maintenance->delete();
        return redirect()->route('maintenances.index')->with('success', 'Jadwal maintenance berhasil dihapus!');
    }
}
