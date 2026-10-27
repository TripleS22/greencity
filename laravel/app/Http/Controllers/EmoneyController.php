<?php

namespace App\Http\Controllers;

use App\Models\Emoney;
use Illuminate\Http\Request;

class EmoneyController extends Controller
{
    public function index()
    {
        $items = Emoney::latest()->paginate(9999);
        return view('stocks.emoneys.index', compact('items'));
    }

    public function create()
    {
        return view('stocks.emoneys.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_diberikan' => 'nullable|date',
            'nomor_body' => 'required|string|max:255',
            'nomor_kartu' => 'required|string|max:255',
            'masa_aktif' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $validated['status'] = $request->tanggal_diberikan ? 'Dibagikan' : 'Tersedia';

        Emoney::create($validated);
        return redirect()->route('stocks.emoneys.index')->with('success', 'E-Money berhasil ditambahkan!');
    }

    public function edit(Emoney $emoney)
    {
        return view('stocks.emoneys.edit', compact('emoney'));
    }

    public function update(Request $request, Emoney $emoney)
    {
        $validated = $request->validate([
            'tanggal_diberikan' => 'nullable|date',
            'nomor_body' => 'required|string|max:255',
            'nomor_kartu' => 'required|string|max:255',
            'masa_aktif' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $validated['status'] = $request->tanggal_diberikan ? 'Dibagikan' : 'Tersedia';

        $emoney->update($validated);
        return redirect()->route('stocks.emoneys.index')->with('success', 'E-Money berhasil diupdate!');
    }

    public function destroy(Emoney $emoney)
    {
        $emoney->delete();
        return redirect()->route('stocks.emoneys.index')->with('success', 'E-Money berhasil dihapus!');
    }

    public function out(Emoney $emoney)
    {
        abort_if($emoney->status !== 'Tersedia', 400, 'E-Money ini sudah dibagikan.');
        return view('stocks.emoneys.out', compact('emoney'));
    }

    public function outStore(Request $request, Emoney $emoney)
    {
        abort_if($emoney->status !== 'Tersedia', 400, 'E-Money ini sudah dibagikan.');

        $validated = $request->validate([
            'tanggal_diberikan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $emoney->update([
            'tanggal_diberikan' => $validated['tanggal_diberikan'],
            'keterangan' => $validated['keterangan'] ?? $emoney->keterangan,
            'status' => 'Dibagikan',
        ]);

        return redirect()->route('stocks.emoneys.index')->with('success', 'Status kartu E-Money berhasil diubah menjadi Dibagikan!');
    }

    public function bulkOut(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal 1 E-Money untuk dibagikan.');
        }

        // Ambil data yang statusnya masih Tersedia
        $emoneys = Emoney::whereIn('id', $ids)->where('status', 'Tersedia')->get();

        if ($emoneys->isEmpty()) {
            return back()->with('error', 'Semua E-Money yang Anda pilih sudah dibagikan.');
        }

        return view('stocks.emoneys.bulk_out', compact('emoneys'));
    }

    public function bulkOutProcess(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:emoneys,id',
            'tanggal_diberikan' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        $ids = $validated['ids'];

        // Update semua ID sekaligus
        Emoney::whereIn('id', $ids)->where('status', 'Tersedia')->update([
            'tanggal_diberikan' => $validated['tanggal_diberikan'],
            'keterangan' => \Illuminate\Support\Facades\DB::raw("CONCAT(COALESCE(keterangan, ''), IF(keterangan IS NULL OR keterangan = '', '', '\n'), '{$validated['keterangan']}')"),
            'status' => 'Dibagikan',
        ]);

        // Catat activity log manual untuk masing-masing
        $emoneys = Emoney::whereIn('id', $ids)->get();
        foreach ($emoneys as $emoney) {
            activity()
               ->performedOn($emoney)
               ->log('Bagikan (Bulk)');
        }

        return redirect()->route('stocks.emoneys.index')->with('success', count($ids) . ' E-Money berhasil dibagikan sekaligus!');
    }
}
