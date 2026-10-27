<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        // Filter periode; default bulan & tahun berjalan
        $filterYear = $request->input('year', now()->year);
        $filterMonth = $request->has('month') ? $request->input('month') : now()->format('m');

        $query = Pengeluaran::with('kategori')->whereYear('tanggal', $filterYear);

        if (!empty($filterMonth)) {
            $query->whereMonth('tanggal', $filterMonth);
        }

        // Total seluruh data terfilter (bukan hanya halaman paginasi aktif)
        $totalFiltered = (clone $query)->sum('jumlah');

        $pengeluaran = $query->orderBy('tanggal', 'desc')
            ->paginate(20)
            ->withQueryString();

        $kategoris = KategoriPengeluaran::all();
        $availableYears = range(2025, now()->year + 1);

        return view('pengeluaran.index', compact(
            'pengeluaran',
            'kategoris',
            'totalFiltered',
            'filterMonth',
            'filterYear',
            'availableYears'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_pengeluaran,id',
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
        ]);

        Pengeluaran::create($request->all());

        return redirect()->back()->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_pengeluaran,id',
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
        ]);

        $pengeluaran->update($request->all());

        return redirect()->back()->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();
        return redirect()->back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
