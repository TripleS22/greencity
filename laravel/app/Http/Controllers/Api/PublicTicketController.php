<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicTicketController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'requester_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:10240',
        ]);

        $ticketData = [
            'title' => $validated['title'],
            'request_date' => $validated['date'],
            'requester_name' => $validated['requester_name'],
            'description' => $validated['description'] ?? null,
            'status' => 'open',
            'priority' => 'medium',
        ];

        // Try to match location by name
        $location = \App\Models\Location::where('name', 'like', '%' . $validated['location'] . '%')->first();
        if ($location) $ticketData['location_id'] = $location->id;

        if ($request->hasFile('photo')) {
            $ticketData['photo'] = $request->file('photo')->store('tickets', 'public');
        }

        $ticket = Ticket::create($ticketData);

        return response()->json([
            'success' => true,
            'message' => 'Ticket berhasil dibuat!',
            'ticket_id' => $ticket->id,
        ], 201);
    }
}
