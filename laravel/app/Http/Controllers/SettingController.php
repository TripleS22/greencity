<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Pembayaran;
use App\Models\PemasukanLain;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $iuranNominal = Setting::getByKey('iuran_nominal', '180000');
        $saldoAwal = Setting::getByKey('saldo_awal', '0');
        
        $totalPemasukan = Pembayaran::where('status', 'lunas')->sum('jumlah') + PemasukanLain::sum('jumlah');
        $totalPengeluaran = Pengeluaran::sum('jumlah');
        $totalSaldo = $saldoAwal + $totalPemasukan - $totalPengeluaran;

        return view('settings.index', compact('iuranNominal', 'saldoAwal', 'totalSaldo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'iuran_nominal' => 'required|numeric|min:0',
            'saldo_awal' => 'nullable|numeric',
            'total_saldo' => 'nullable|numeric',
        ]);

        Setting::updateOrCreate(
            ['key' => 'iuran_nominal'],
            ['value' => $request->iuran_nominal]
        );

        $saldoAwalToSave = $request->saldo_awal ?? 0;

        // If user submitted total_saldo, calculate back the required saldo_awal
        if ($request->filled('total_saldo')) {
            $totalPemasukan = Pembayaran::where('status', 'lunas')->sum('jumlah') + PemasukanLain::sum('jumlah');
            $totalPengeluaran = Pengeluaran::sum('jumlah');
            
            // total_saldo = saldo_awal + pemasukan - pengeluaran
            // saldo_awal = total_saldo - pemasukan + pengeluaran
            $saldoAwalToSave = $request->total_saldo - $totalPemasukan + $totalPengeluaran;
        }

        Setting::updateOrCreate(
            ['key' => 'saldo_awal'],
            ['value' => $saldoAwalToSave]
        );

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
