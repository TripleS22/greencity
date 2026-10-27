<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['requester', 'location'])->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);

        $tickets = $query->paginate(15)->withQueryString();
        return view('helpdesk.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('helpdesk.tickets.create', compact('locations', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:maintenance,procurement',
            'request_date' => 'required|date',
            'schedule_date' => 'nullable|date',
            'status' => 'required|in:open,progress,closed',
            'requester_id' => 'nullable|exists:users,id',
            'location_id' => 'nullable|exists:locations,id',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tickets', 'public');
        }

        $validated['priority'] = 'medium';
        $validated['ticket_code'] = 'TCK-' . strtoupper(\Illuminate\Support\Str::random(6));

        if ($validated['category'] === 'procurement') {
            $validated['approval_status'] = 'pending';
        }

        // Link ke maintenance_id saat ini jika ada (demi pencatatan history pelaporan jika memang dibutuhkan)
        if ($validated['category'] === 'maintenance' && !empty($validated['location_id'])) {
            $maintenance = \App\Models\Maintenance::where('location_id', $validated['location_id'])->first();
            if ($maintenance) {
                $validated['maintenance_id'] = $maintenance->id;
            }
        }

        Ticket::create($validated);
        return redirect()->route('tickets.index')->with('success', 'Ticket berhasil dibuat!');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['requester', 'location']);
        return view('helpdesk.tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $locations = Location::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('helpdesk.tickets.edit', compact('ticket', 'locations', 'users'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:maintenance,procurement',
            'request_date' => 'required|date',
            'schedule_date' => 'nullable|date',
            'status' => 'required|in:open,progress,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'requester_id' => 'nullable|exists:users,id',
            'location_id' => 'nullable|exists:locations,id',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('photo')) {
            if ($ticket->photo) Storage::disk('public')->delete($ticket->photo);
            $validated['photo'] = $request->file('photo')->store('tickets', 'public');
        }

        if ($validated['category'] === 'procurement' && !$ticket->approval_status) {
            $validated['approval_status'] = 'pending';
        } elseif ($validated['category'] === 'maintenance' && !empty($validated['location_id']) && !$ticket->maintenance_id) {
            $maintenance = \App\Models\Maintenance::where('location_id', $validated['location_id'])->first();
            if ($maintenance) {
                $validated['maintenance_id'] = $maintenance->id;
            }
        }

        $ticket->update($validated);
        return redirect()->route('tickets.index')->with('success', 'Ticket berhasil diupdate!');
    }

    public function destroy(Ticket $ticket)
    {
        if ($ticket->photo) Storage::disk('public')->delete($ticket->photo);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket berhasil dihapus!');
    }

    public function resolve(Request $request, Ticket $ticket)
    {
        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $ticket->update([
            'resolution_notes' => $request->resolution_notes,
            'resolved_at' => now(),
            'status' => 'closed',
        ]);

        // [LOGIKA BARU]: Jika tiket perbaikan/maintenance ini SELESAI ditangani,
        // Maka jadwal maintenance berikutnya untuk lokasi tersebut HARUS DIUNDUR 3 bulan ke depan.
        if ($ticket->category === 'maintenance' && $ticket->location_id) {
            $maintenance = \App\Models\Maintenance::where('location_id', $ticket->location_id)->first();
            if ($maintenance) {
                $maintenance->update(['date' => now()->addMonths(3)]);
            } else {
                \App\Models\Maintenance::create([
                    'location_id' => $ticket->location_id,
                    'category' => 'all',
                    'description' => 'Scheduled Maintenance (Auto-Generated)',
                    'date' => now()->addMonths(3)
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket berhasil ditandai sebagai selesai, jadwal pemeliharaan lokasi ini aman diundur 3 bulan ke depan!');
    }

    public function inlineUpdate(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'field' => 'required|in:status,priority,approval_status',
            'value' => 'required|string',
        ]);

        if ($validated['field'] === 'status') {
            $request->validate(['value' => 'in:open,progress,closed']);
        } elseif ($validated['field'] === 'priority') {
            $request->validate(['value' => 'in:low,medium,high,urgent']);
        } elseif ($validated['field'] === 'approval_status') {
            $request->validate(['value' => 'in:pending,approved,rejected']);
        }

        $ticket->update([$validated['field'] => $validated['value']]);

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $validated['field'])) . ' berhasil diupdate'
        ]);
    }
}
