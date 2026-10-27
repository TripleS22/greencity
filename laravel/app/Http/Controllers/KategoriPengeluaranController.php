<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;

class KategoriPengeluaranController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_pengeluaran,nama',
        ]);

        KategoriPengeluaran::create([
            'nama' => $request->nama_kategori,
            'deskripsi' => 'Dibuat secara manual',
        ]);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }
}
