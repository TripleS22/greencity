<?php

namespace App\Http\Controllers;

use App\Models\PemasukanLain;
use Illuminate\Http\Request;

class PemasukanLainController extends Controller
{
    public function index()
    {
        $pemasukanLain = PemasukanLain::orderBy('tanggal', 'desc')->paginate(10);
        return view('pemasukan_lain.index', compact('pemasukanLain'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
        ]);

        PemasukanLain::create($request->all());

        return redirect()->back()->with('success', 'Pemasukan lain berhasil dicatat.');
    }

    public function update(Request $request, PemasukanLain $pemasukanLain)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
        ]);

        $pemasukanLain->update($request->all());

        return redirect()->back()->with('success', 'Catatan pemasukan lain berhasil diperbarui.');
    }

    public function destroy(PemasukanLain $pemasukanLain)
    {
        $pemasukanLain->delete();
        return redirect()->back()->with('success', 'Catatan pemasukan lain berhasil dihapus.');
    }
}
