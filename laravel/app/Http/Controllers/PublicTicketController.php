<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicTicketController extends Controller
{
    public function create()
    {
        $locations = Location::orderBy('name')->get();
        return view('public.ticket', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requester_name' => 'required|string|max:255',
            'category'       => 'required|in:maintenance,procurement',
            'title'          => 'required|string|max:255',
            'location_id'    => 'required|exists:locations,id',
            'description'    => 'nullable|string',
            'photo'          => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tickets', 'public');
        }

        // Set ticket defaults for public submission
        // Set ticket defaults for public submission
        $validated['ticket_code']  = 'TCK-' . strtoupper(Str::random(6));
        $validated['status']       = 'open';
        $validated['priority']     = 'medium'; // Default priority for public
        $validated['request_date'] = now()->toDateString();
        $validated['requester_id'] = null; // no logged-in user

        if ($validated['category'] === 'procurement') {
            $validated['approval_status'] = 'pending';
        } elseif ($validated['category'] === 'maintenance' && !empty($validated['location_id'])) {
            $maintenance = \App\Models\Maintenance::where('location_id', $validated['location_id'])
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date', 'asc')
                ->first();

            if ($maintenance) {
                $ticketDate = \Carbon\Carbon::parse($validated['request_date']);
                $maintenanceDate = \Carbon\Carbon::parse($maintenance->date);

                if ($ticketDate->lte($maintenanceDate)) {
                    $maintenance->update(['date' => $ticketDate]);
                    $validated['maintenance_id'] = $maintenance->id;
                }
            }
        }

        // Store requester name in description prefix if no dedicated column
        $validated['description'] = '[Pelapor: ' . $validated['requester_name'] . '] ' . ($validated['description'] ?? '');
        unset($validated['requester_name']);

        $ticket = Ticket::create($validated);

        // Redirect ke halaman lacak tiket agar user langsung melihat status tanpa perlu ketik ulang
        return redirect()->route('public.ticket.track', ['ticket_code' => $ticket->ticket_code])
            ->with('just_created', true);
    }

    public function track(Request $request)
    {
        $ticket = null;
        $queuePosition = null;

        if ($code = $request->query('ticket_code')) {
            $ticket = Ticket::with('location')->where('ticket_code', $code)->first();

            if ($ticket && in_array($ticket->status, ['open', 'progress'])) {
                // Hitung posisi antrian: semua tiket open yg dibuat lebih dulu
                $queuePosition = Ticket::where('status', 'open')
                    ->where('created_at', '<=', $ticket->created_at)
                    ->where('id', '<=', $ticket->id)
                    ->count();
            }

            if (!$ticket) {
                session()->flash('error', 'Tiket tidak ditemukan.');
            }
        }

        $justCreated = session('just_created', false);

        return view('public.track', compact('ticket', 'queuePosition', 'justCreated'));
    }

    public function trackProcess(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
        ]);

        $ticket = Ticket::with('location')->where('ticket_code', $request->ticket_code)->first();

        if (!$ticket) {
            return back()->with('error', 'Tiket tidak ditemukan.');
        }

        // Redirect ke GET supaya URL bisa di-bookmark dan di-refresh tanpa resubmit form
        return redirect()->route('public.ticket.track', ['ticket_code' => $ticket->ticket_code]);
    }
}
