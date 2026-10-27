<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Warga::withCount(['pembayaran as lunas_count' => function ($q) {
            $q->where('status', 'lunas');
        }]);

        // Search by nama or unit
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        // Filter by blok prefix (A, B, C)
        if ($request->filled('blok')) {
            $query->where('unit', 'like', $request->blok . '%');
        }

        // Sort (natural sort for unit: A1, A2, ..., A9, A10 instead of A1, A10, A2)
        $sortColumn = in_array($request->sort, ['nama', 'unit']) ? $request->sort : 'nama';
        $sortDirection = $request->direction === 'desc' ? 'desc' : 'asc';

        if ($sortColumn === 'unit') {
            // Sort by letter prefix first (A, B, C), then by numeric part as integer
            $query->orderByRaw("REGEXP_REPLACE(unit, '[0-9]+', '') {$sortDirection}, CAST(REGEXP_REPLACE(unit, '[^0-9]', '') AS UNSIGNED) {$sortDirection}");
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $warga = $query->paginate(20)->appends($request->query());

        // Get distinct blok prefixes for filter dropdown
        $blokList = Warga::selectRaw("REGEXP_REPLACE(unit, '[0-9]+', '') as prefix")
            ->distinct()
            ->orderBy('prefix')
            ->pluck('prefix');

        return view('warga.index', compact('warga', 'blokList'));
    }

    public function show(Request $request, Warga $warga)
    {
        $currentYear = $request->input('year', date('Y'));
        
        $query = $warga->pembayaran();
        
        if ($request->filled('year')) {
            $query->where('bulan', 'like', '% ' . $currentYear);
        }
        
        $pembayaran = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        // available years
        $availableYears = [date('Y') - 1, date('Y'), date('Y') + 1];
        if (!in_array(2025, $availableYears)) array_unshift($availableYears, 2025);
        if (!in_array(2026, $availableYears)) array_push($availableYears, 2026);
        $availableYears = array_unique($availableYears);
        sort($availableYears);
        
        return view('warga.show', compact('warga', 'pembayaran', 'currentYear', 'availableYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'unit' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
        ]);

        Warga::create($request->all());

        return redirect()->back()->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function update(Request $request, Warga $warga)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'unit' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $warga->update($request->all());

        return redirect()->back()->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(Warga $warga)
    {
        $warga->delete();
        return redirect()->back()->with('success', 'Data warga berhasil dihapus.');
    }
}
