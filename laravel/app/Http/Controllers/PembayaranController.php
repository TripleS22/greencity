<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with('warga');

        // Filter by Nama Warga
        if ($request->filled('nama')) {
            $query->whereHas('warga', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama . '%');
            });
        }

        // Filter by Unit
        if ($request->filled('unit')) {
            $query->whereHas('warga', function ($q) use ($request) {
                $q->where('unit', 'like', '%' . $request->unit . '%');
            });
        }

        // Filter by Bulan & Tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->where('bulan', 'like', $request->bulan . ' ' . $request->tahun . '%');
        } elseif ($request->filled('bulan')) {
            $query->where('bulan', 'like', $request->bulan . '%');
        } elseif ($request->filled('tahun')) {
            $query->where('bulan', 'like', '%' . $request->tahun . '%');
        }

        $pembayaran = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $warga = Warga::where('aktif', true)->get();
        $iuranNominal = \App\Models\Setting::getByKey('iuran_nominal', '180000');
        return view('pembayaran.index', compact('pembayaran', 'warga', 'iuranNominal'));
    }

    // Admin: Catat Pembayaran (Langsung Lunas)
    public function store(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|array',
            'warga_id.*' => 'exists:warga,id',
            'bulan' => 'required|array', // Multi-select bulan
            'bulan.*' => 'string',
            'year' => 'required|string',
        ]);

        $iuranNominal = \App\Models\Setting::getByKey('iuran_nominal', '180000');
        $year = $request->year;
        $skipped = [];

        \DB::transaction(function () use ($request, $iuranNominal, $year, &$skipped) {
            foreach ($request->warga_id as $wId) {
                foreach ($request->bulan as $bulan) {
                    $bulanFull = $bulan . ' ' . $year;

                    // Cek duplikat: sudah ada pembayaran lunas/pending untuk warga+bulan ini?
                    $exists = Pembayaran::where('warga_id', $wId)
                        ->where('bulan', $bulanFull)
                        ->whereIn('status', ['lunas', 'pending'])
                        ->exists();

                    if ($exists) {
                        $warga = Warga::find($wId);
                        $skipped[] = ($warga->nama ?? 'ID:'.$wId) . ' (' . $bulanFull . ')';
                        continue;
                    }

                    $orderId = 'IURAN-' . strtoupper(Str::random(8)) . '-' . (time() + rand(1, 100));
                    
                    Pembayaran::create([
                        'warga_id' => $wId,
                        'bulan' => $bulanFull,
                        'jumlah' => $iuranNominal,
                        'status' => 'lunas',
                        'metode_bayar' => 'manual',
                        'order_id' => $orderId,
                        'tanggal_bayar' => now(),
                        'catatan' => 'Dicatat oleh Admin',
                    ]);
                }
            }
        });

        if (!empty($skipped)) {
            return redirect()->back()->with('error', 'Beberapa pembayaran sudah ada dan dilewati: ' . implode(', ', $skipped));
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil dicatat.');
    }

    // Warga: Bayar Mandiri (via QRIS Doitpay)
    public function publicStore(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|exists:warga,id',
            'bulan'    => 'required|array',
            'bulan.*'  => 'string',
        ]);

        $warga        = Warga::findOrFail($request->warga_id);
        $iuranNominal = \App\Models\Setting::getByKey('iuran_nominal', '180000');

        try {
            $createdIds = [];

            \DB::transaction(function () use ($request, $iuranNominal, &$createdIds) {
                foreach ($request->bulan as $bulan) {
                    // Reuse existing pending record if available
                    $payment = Pembayaran::where('warga_id', $request->warga_id)
                        ->where('bulan', $bulan)
                        ->where('status', 'pending')
                        ->first();

                    if (!$payment) {
                        // Skip if already lunas
                        if (Pembayaran::where('warga_id', $request->warga_id)->where('bulan', $bulan)->where('status', 'lunas')->exists()) {
                            continue;
                        }

                        $payment = Pembayaran::create([
                            'warga_id' => $request->warga_id,
                            'bulan'    => $bulan,
                            'jumlah'   => $iuranNominal,
                            'status'   => 'pending',
                            'order_id' => 'QRIS-' . strtoupper(Str::random(8)) . '-' . time() . rand(10, 99),
                        ]);
                    }
                    $createdIds[] = $payment->id;
                }
            });

            if (empty($createdIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semua bulan yang dipilih sudah lunas.',
                ], 400);
            }

            // Build a signed multi-payment URL
            $idsStr = implode(',', $createdIds);
            $token  = hash_hmac('sha256', $idsStr, config('app.key'));

            $redirectUrl = route('pembayaran.bayarMulti', [
                'ids'   => $idsStr,
                'token' => $token,
            ]);

            return response()->json([
                'success'      => true,
                'redirect_url' => $redirectUrl,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bayar(Pembayaran $pembayaran)
    {
        if (empty($pembayaran->order_id)) {
            $pembayaran->update([
                'order_id' => 'QRIS-' . strtoupper(Str::random(8)) . '-' . time()
            ]);
        }

        // Fix: Treat payment individually as requested, avoid grouping.
        $allPayments = collect([$pembayaran]);
        $totalAmount = $pembayaran->jumlah;
        $months = [$pembayaran->bulan];

        if ($pembayaran->isLunas()) {
            return redirect()->route('dashboard')->with('info', 'Pembayaran sudah lunas.');
        }

        return view('pembayaran.bayar', compact('pembayaran', 'allPayments', 'totalAmount', 'months'));
    }

    /**
     * Halaman bayar QRIS untuk beberapa pembayaran sekaligus (multi-bulan)
     * Bisa diakses publik — IDs dikirim via query string terenkripsi
     */
    public function bayarMulti(Request $request)
    {
        $request->validate([
            'ids'   => 'required|string',
            'token' => 'required|string',
        ]);

        // Decode the signed token to get the IDs (security check)
        $expectedToken = hash_hmac('sha256', $request->ids, config('app.key'));
        if (!hash_equals($expectedToken, $request->token)) {
            abort(403, 'Token tidak valid.');
        }

        $ids = array_filter(explode(',', $request->ids), 'is_numeric');
        $payments = Pembayaran::with('warga')->whereIn('id', $ids)->get();

        if ($payments->isEmpty()) {
            abort(404, 'Data pembayaran tidak ditemukan.');
        }

        // Pastikan semua payment milik warga yang sama
        $wargaIds = $payments->pluck('warga_id')->unique();
        if ($wargaIds->count() > 1) {
            abort(422, 'Pembayaran massal QRIS hanya bisa untuk 1 warga sekaligus.');
        }

        // Filter hanya yang belum lunas
        $unpaidPayments = $payments->where('status', '!=', 'lunas');
        if ($unpaidPayments->isEmpty()) {
            return redirect()->route('dashboard')->with('info', 'Semua pembayaran sudah lunas.');
        }

        // Pastikan setiap record punya order_id
        foreach ($unpaidPayments as $p) {
            if (empty($p->order_id)) {
                $p->update(['order_id' => 'QRIS-' . strtoupper(Str::random(8)) . '-' . time()]);
            }
        }

        $totalAmount = $unpaidPayments->sum('jumlah');
        $months      = $unpaidPayments->pluck('bulan')->toArray();

        // Gunakan payment pertama sebagai primary record (untuk compatibility dengan bayar.blade)
        $primaryPayment = $unpaidPayments->first();
        $allPayments    = $unpaidPayments;

        return view('pembayaran.bayar', compact('primaryPayment', 'allPayments', 'totalAmount', 'months'));
    }

    /**
     * Admin: Bulk bayar via QRIS — siapkan records dan redirect ke bayar-multi
     */
    public function bulkBayarQris(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:pembayaran,id',
        ]);

        $payments = Pembayaran::with('warga')->whereIn('id', $request->ids)->get();

        // Cek semua milik 1 warga
        $wargaIds = $payments->pluck('warga_id')->unique();
        if ($wargaIds->count() > 1) {
            return redirect()->back()->with('error', 'Bayar QRIS massal hanya bisa untuk 1 warga sekaligus. Pilih pembayaran dari warga yang sama.');
        }

        // Hanya yang belum lunas
        $unpaid = $payments->where('status', '!=', 'lunas');
        if ($unpaid->isEmpty()) {
            return redirect()->back()->with('error', 'Semua pembayaran yang dipilih sudah lunas.');
        }

        $idsStr = $unpaid->pluck('id')->join(',');
        $token  = hash_hmac('sha256', $idsStr, config('app.key'));

        return redirect()->route('pembayaran.bayarMulti', [
            'ids'   => $idsStr,
            'token' => $token,
        ]);
    }

    public function bayarManual(Pembayaran $pembayaran)
    {
        $pembayaran->update([
            'status' => 'lunas',
            'metode_bayar' => 'manual',
            'tanggal_bayar' => now(),
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dicatat secara manual.');
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'bulan' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'status' => 'required|in:lunas,belum_lunas,pending',
            'metode_bayar' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // Cek duplikat jika bulan berubah
        if ($request->bulan !== $pembayaran->bulan) {
            $exists = Pembayaran::where('warga_id', $pembayaran->warga_id)
                ->where('bulan', $request->bulan)
                ->where('id', '!=', $pembayaran->id)
                ->whereIn('status', ['lunas', 'pending'])
                ->exists();

            if ($exists) {
                return redirect()->back()->withErrors(['bulan' => 'Pembayaran untuk bulan ' . $request->bulan . ' sudah ada.']);
            }
        }

        $pembayaran->update($request->all());

        return redirect()->back()->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        $pembayaran->delete();
        return redirect()->back()->with('success', 'Data pembayaran berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pembayaran,id',
        ]);

        Pembayaran::whereIn('id', $request->ids)->delete();
        return redirect()->back()->with('success', count($request->ids) . ' data pembayaran berhasil dihapus.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pembayaran,id',
            'status' => 'required|in:lunas,belum_lunas,pending',
        ]);

        $updateData = ['status' => $request->status];
        if ($request->status === 'lunas') {
            $updateData['metode_bayar'] = 'manual';
            $updateData['tanggal_bayar'] = now();
        }

        Pembayaran::whereIn('id', $request->ids)->update($updateData);
        return redirect()->back()->with('success', count($request->ids) . ' data berhasil diperbarui ke status ' . $request->status . '.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pembayaran,id',
            'status' => 'required|in:lunas,belum_lunas,pending',
            'metode_bayar' => 'nullable|string',
            'jumlah' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'bulan_nama' => 'nullable|string',
            'bulan_tahun' => 'nullable|string',
            'tanggal_bayar' => 'nullable|date',
        ]);

        $updateData = ['status' => $request->status];
        
        if ($request->filled('metode_bayar')) {
            $updateData['metode_bayar'] = $request->metode_bayar;
        }
        if ($request->filled('jumlah')) {
            $updateData['jumlah'] = $request->jumlah;
        }
        if ($request->has('catatan')) {
            $updateData['catatan'] = $request->catatan;
        }
        if ($request->filled('bulan_nama') && $request->filled('bulan_tahun')) {
            $newBulan = $request->bulan_nama . ' ' . $request->bulan_tahun;
            
            // Cek duplikat: untuk setiap record yang diedit, pastikan warga belum punya pembayaran untuk bulan baru
            $records = Pembayaran::whereIn('id', $request->ids)->get();
            $conflicts = [];
            foreach ($records as $rec) {
                if ($rec->bulan === $newBulan) continue; // bulan sama, skip
                $exists = Pembayaran::where('warga_id', $rec->warga_id)
                    ->where('bulan', $newBulan)
                    ->where('id', '!=', $rec->id)
                    ->whereIn('status', ['lunas', 'pending'])
                    ->exists();
                if ($exists) {
                    $conflicts[] = ($rec->warga->nama ?? 'ID:'.$rec->warga_id);
                }
            }
            if (!empty($conflicts)) {
                return redirect()->back()->withErrors(['bulan' => 'Pembayaran ' . $newBulan . ' sudah ada untuk: ' . implode(', ', array_unique($conflicts))]);
            }
            
            $updateData['bulan'] = $newBulan;
        }
        if ($request->filled('tanggal_bayar')) {
            $updateData['tanggal_bayar'] = $request->tanggal_bayar;
        } elseif ($request->status === 'lunas' && !$request->filled('tanggal_bayar')) {
            $updateData['tanggal_bayar'] = now();
        }

        Pembayaran::whereIn('id', $request->ids)->update($updateData);
        return redirect()->back()->with('success', count($request->ids) . ' data pembayaran berhasil diperbarui.');
    }

    public function getStatus(\App\Models\Warga $warga, $year)
    {
        $paidMonths = Pembayaran::where('warga_id', $warga->id)
            ->where('bulan', 'like', "% $year")
            ->where('status', 'lunas')
            ->pluck('bulan')
            ->toArray();

        // Strip the year from the month string (e.g., "Januari 2026" -> "Januari")
        $paidMonthNames = array_map(function($bulan) use ($year) {
            return trim(str_replace($year, '', $bulan));
        }, $paidMonths);

        return response()->json([
            'success' => true,
            'paid_months' => $paidMonthNames
        ]);
    }
}
