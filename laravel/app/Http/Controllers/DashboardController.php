<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Parameter year parsing (Moved to top to prevent undefined variable errors)
        $currentYear = $request->input('year', Carbon::now()->year);
        $availableYears = [Carbon::now()->year - 1, Carbon::now()->year, Carbon::now()->year + 1];
        if (!in_array(2025, $availableYears)) array_unshift($availableYears, 2025);
        if (!in_array(2026, $availableYears)) array_push($availableYears, 2026);
        $availableYears = array_unique($availableYears);
        sort($availableYears);

        // Total pemasukan for selected year (resident dues + other income)
        $totalIuran = Pembayaran::where('status', 'lunas')
            ->where('bulan', 'like', '% ' . $currentYear)
            ->sum('jumlah');
        $totalPemasukanLain = \App\Models\PemasukanLain::whereYear('tanggal', $currentYear)->sum('jumlah');
        $totalPemasukan = $totalIuran + $totalPemasukanLain;

        // Total pengeluaran for selected year
        $totalPengeluaran = Pengeluaran::whereYear('tanggal', $currentYear)->sum('jumlah');

        // Total saldo (all time balance remains relevant, but user asked for yearly view)
        // We'll show yearly totals in cards but keep the overall balance available if needed.
        $saldoAwal = \App\Models\Setting::getByKey('saldo_awal', '0');
        $totalSaldoAllTime = $saldoAwal + (Pembayaran::where('status', 'lunas')->sum('jumlah') + \App\Models\PemasukanLain::sum('jumlah')) - Pengeluaran::sum('jumlah');

        // Warga aktif (who paid this month)
        $currentMonth = Carbon::now()->format('F Y');
        // Map Indonesian month names
        $bulanIndonesia = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        $currentMonthId = $bulanIndonesia[Carbon::now()->format('F')] . ' ' . Carbon::now()->format('Y');
        
        $wargaAktif = Warga::where('aktif', true)->count();
        $sudahBayar = Pembayaran::where('bulan', $currentMonthId)
            ->where('status', 'lunas')
            ->count();


        // Status pembayaran warga (Matrix view)
        $query = Warga::where('aktif', true);

         // Parameters for matrix
        $sortColumn = in_array($request->sort, ['nama', 'unit']) ? $request->sort : 'unit';
        $sortDirection = $request->direction === 'desc' ? 'desc' : 'asc';

        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Filter by Unit Category (A, B, C)
        if ($request->filled('unit_category')) {
            $query->where('unit', 'like', $request->unit_category . '%');
        }

        // Apply same sorting logic as WargaController
        if ($sortColumn === 'unit') {
            $query->orderByRaw("REGEXP_REPLACE(unit, '[0-9]+', '') {$sortDirection}, CAST(REGEXP_REPLACE(unit, '[^0-9]', '') AS UNSIGNED) {$sortDirection}");
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $wargaList = $query->with(['pembayaran' => function($query) use ($currentYear) {
                $query->where('bulan', 'like', '% ' . $currentYear)
                      ->orderBy('created_at', 'asc');
            }])
            ->get();

        $daftarBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $monthsOfYear = [];
        foreach ($daftarBulan as $b) {
            $monthsOfYear[] = $b . ' ' . $currentYear;
        }

        // Keep the original variable for other parts of dashboard if needed, 
        // but we'll prioritize wargaList for the new widget.
        $pembayaranBulanIni = Pembayaran::with('warga')
            ->where('bulan', $currentMonthId)
            ->orderBy('created_at', 'desc')
            ->get();

        // If no records for current month, try Januari 2026 (seed data)
        if ($pembayaranBulanIni->isEmpty()) {
            $currentMonthId = 'Januari 2026';
            $pembayaranBulanIni = Pembayaran::with('warga')
                ->where('bulan', $currentMonthId)
                ->orderBy('created_at', 'desc')
                ->get();
            $sudahBayar = Pembayaran::where('bulan', $currentMonthId)
                ->where('status', 'lunas')
                ->count();
        }

        // Buku Kas Bulanan: masuk/keluar, rincian kategori & riwayat transaksi per bulan
        $bukuKas = [];
        for ($m = 1; $m <= 12; $m++) {
            $bulanStr = $daftarBulan[$m - 1] . ' ' . $currentYear;

            $masuk = Pembayaran::where('status', 'lunas')->where('bulan', $bulanStr)->sum('jumlah')
                + \App\Models\PemasukanLain::whereMonth('tanggal', $m)->whereYear('tanggal', $currentYear)->sum('jumlah');

            $transaksi = Pengeluaran::with('kategori')
                ->whereMonth('tanggal', $m)
                ->whereYear('tanggal', $currentYear)
                ->orderBy('tanggal')
                ->get();

            $perKategori = $transaksi->groupBy(function ($t) {
                    return $t->kategori->nama ?? 'Lain-lain';
                })
                ->map(function ($items) {
                    return (float) $items->sum('jumlah');
                })
                ->sortDesc();

            $bukuKas[] = [
                'nama' => $daftarBulan[$m - 1],
                'masuk' => (float) $masuk,
                'keluar' => (float) $transaksi->sum('jumlah'),
                'perKategori' => $perKategori,
                'transaksi' => $transaksi,
            ];
        }

        // Arus kas bulanan (Full year Jan-Dec)
        $arusKas = $this->getArusKasTahunan($currentYear);

        // Percentage changes (Year vs Previous Year)
        $prevYear = $currentYear - 1;
        $prevYearPemasukan = (Pembayaran::where('status', 'lunas')->where('bulan', 'like', '% ' . $prevYear)->sum('jumlah')) + (\App\Models\PemasukanLain::whereYear('tanggal', $prevYear)->sum('jumlah'));
        $prevYearPengeluaran = Pengeluaran::whereYear('tanggal', $prevYear)->sum('jumlah');

        $pemasukanChange = $prevYearPemasukan > 0 
            ? round(($totalPemasukan - $prevYearPemasukan) / $prevYearPemasukan * 100) 
            : 0;
        $pengeluaranChange = $prevYearPengeluaran > 0 
            ? round(($totalPengeluaran - $prevYearPengeluaran) / $prevYearPengeluaran * 100) 
            : 0;

        return view('dashboard', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'totalSaldoAllTime',
            'wargaAktif',
            'sudahBayar',
            'pembayaranBulanIni',
            'wargaList',
            'monthsOfYear',
            'currentYear',
            'availableYears',
            'bukuKas',
            'arusKas',
            'pemasukanChange',
            'pengeluaranChange',
            'currentMonthId',
            'saldoAwal'
        ));
    }

    private function getArusKasTahunan($year)
    {
        $months = [];
        $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $bulanFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        for ($m = 1; $m <= 12; $m++) {
            $bulanStr = $bulanFull[$m] . ' ' . $year;

            $pemasukanIuran = Pembayaran::where('status', 'lunas')
                ->where('bulan', $bulanStr)
                ->sum('jumlah');
                
            $pemasukanLain = \App\Models\PemasukanLain::whereMonth('tanggal', $m)
                ->whereYear('tanggal', $year)
                ->sum('jumlah');

            $pemasukan = $pemasukanIuran + $pemasukanLain;

            $pengeluaran = Pengeluaran::whereMonth('tanggal', $m)
                ->whereYear('tanggal', $year)
                ->sum('jumlah');

            $months[] = [
                'label' => $bulanNames[$m - 1],
                'pemasukan' => (float) $pemasukan,
                'pengeluaran' => (float) $pengeluaran,
            ];
        }

        return $months;
    }

    private function getPreviousMonthIncome()
    {
        $prevMonth = Carbon::now()->subMonth();
        $bulanFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanStr = $bulanFull[$prevMonth->month] . ' ' . $prevMonth->year;
        
        return (float) Pembayaran::where('status', 'lunas')
            ->where('bulan', $bulanStr)
            ->sum('jumlah');
    }

    private function getPreviousMonthExpense()
    {
        $prevMonth = Carbon::now()->subMonth();
        return (float) Pengeluaran::whereMonth('tanggal', $prevMonth->month)
            ->whereYear('tanggal', $prevMonth->year)
            ->sum('jumlah');
    }
}
