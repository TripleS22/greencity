<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;
use App\Models\Pembayaran;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class IuranSeeder extends Seeder
{
    public function run(): void
    {
        // Create Warga
        $wargaData = [
            ['nama' => 'Bambang Subroto', 'unit' => 'Blok A-12', 'telepon' => '081234567890', 'email' => 'bambang@email.com', 'aktif' => true],
            ['nama' => 'Siti Nurhaliza', 'unit' => 'Blok B-105', 'telepon' => '081234567891', 'email' => 'siti@email.com', 'aktif' => true],
            ['nama' => 'Ahmad Rahman', 'unit' => 'Blok C-18', 'telepon' => '081234567892', 'email' => 'ahmad@email.com', 'aktif' => true],
            ['nama' => 'Dwi Hartanto', 'unit' => 'Blok A-GN', 'telepon' => '081234567893', 'email' => 'dwi@email.com', 'aktif' => true],
            ['nama' => 'Rina Mariah', 'unit' => 'Blok D-22', 'telepon' => '081234567894', 'email' => 'rina@email.com', 'aktif' => true],
            ['nama' => 'Budi Santoso', 'unit' => 'Blok B-15', 'telepon' => '081234567895', 'email' => 'budi@email.com', 'aktif' => true],
        ];

        foreach ($wargaData as $data) {
            Warga::create($data);
        }

        // Create Pembayaran
        $bulan = 'Januari 2026';
        $pembayaranData = [
            ['warga_id' => 1, 'bulan' => $bulan, 'jumlah' => 100000, 'status' => 'lunas', 'tanggal_bayar' => Carbon::parse('2026-01-05')],
            ['warga_id' => 2, 'bulan' => $bulan, 'jumlah' => 100000, 'status' => 'lunas', 'tanggal_bayar' => Carbon::parse('2026-01-07')],
            ['warga_id' => 3, 'bulan' => $bulan, 'jumlah' => 100000, 'status' => 'belum_lunas'],
            ['warga_id' => 4, 'bulan' => $bulan, 'jumlah' => 100000, 'status' => 'lunas', 'tanggal_bayar' => Carbon::parse('2026-01-10')],
            ['warga_id' => 5, 'bulan' => $bulan, 'jumlah' => 100000, 'status' => 'belum_lunas'],
            ['warga_id' => 6, 'bulan' => $bulan, 'jumlah' => 100000, 'status' => 'lunas', 'tanggal_bayar' => Carbon::parse('2026-01-15')],
        ];

        // Add historical payments for several months
        $months = [
            'Juli 2025', 'Agustus 2025', 'September 2025', 'Oktober 2025',
            'November 2025', 'Desember 2025', 'Januari 2026'
        ];

        $monthlyAmounts = [
            'Juli 2025' => ['income' => 4500000, 'expense' => 3200000],
            'Agustus 2025' => ['income' => 4800000, 'expense' => 3500000],
            'September 2025' => ['income' => 5000000, 'expense' => 3800000],
            'Oktober 2025' => ['income' => 5200000, 'expense' => 3600000],
            'November 2025' => ['income' => 4900000, 'expense' => 3400000],
            'Desember 2025' => ['income' => 5100000, 'expense' => 3900000],
            'Januari 2026' => ['income' => 4000000, 'expense' => 3400000],
        ];

        // Create historical pembayaran
        foreach ($months as $month) {
            if ($month === 'Januari 2026') {
                // Use the detailed data above
                foreach ($pembayaranData as $data) {
                    Pembayaran::create($data);
                }
            } else {
                foreach ([1, 2, 3, 4, 5, 6] as $wargaId) {
                    $isLunas = rand(0, 10) > 2; // 80% chance paid
                    Pembayaran::create([
                        'warga_id' => $wargaId,
                        'bulan' => $month,
                        'jumlah' => 100000,
                        'status' => $isLunas ? 'lunas' : 'belum_lunas',
                        'tanggal_bayar' => $isLunas ? Carbon::parse('2025-' . (array_search($month, $months) + 7) . '-' . rand(1, 25)) : null,
                    ]);
                }
            }
        }

        // Create Kategori Pengeluaran
        $kategoris = [
            ['nama' => 'Kebersihan', 'deskripsi' => 'Biaya kebersihan lingkungan'],
            ['nama' => 'Keamanan', 'deskripsi' => 'Biaya keamanan dan satpam'],
            ['nama' => 'Listrik', 'deskripsi' => 'Biaya listrik fasilitas umum'],
            ['nama' => 'Pemeliharaan', 'deskripsi' => 'Biaya pemeliharaan fasilitas'],
            ['nama' => 'Taman', 'deskripsi' => 'Biaya perawatan taman'],
            ['nama' => 'Air', 'deskripsi' => 'Biaya air'],
        ];

        foreach ($kategoris as $kategori) {
            KategoriPengeluaran::create($kategori);
        }

        // Create Pengeluaran for current month
        $pengeluaranData = [
            ['kategori_id' => 1, 'keterangan' => 'Gaji petugas kebersihan', 'jumlah' => 2500000, 'tanggal' => '2026-01-05'],
            ['kategori_id' => 2, 'keterangan' => 'Gaji satpam', 'jumlah' => 2000000, 'tanggal' => '2026-01-05'],
            ['kategori_id' => 3, 'keterangan' => 'Tagihan listrik Januari', 'jumlah' => 1500000, 'tanggal' => '2026-01-10'],
            ['kategori_id' => 4, 'keterangan' => 'Perbaikan pagar', 'jumlah' => 800000, 'tanggal' => '2026-01-15'],
            ['kategori_id' => 5, 'keterangan' => 'Perawatan taman', 'jumlah' => 350000, 'tanggal' => '2026-01-20'],
            ['kategori_id' => 6, 'keterangan' => 'Tagihan air', 'jumlah' => 250000, 'tanggal' => '2026-01-22'],
        ];

        foreach ($pengeluaranData as $data) {
            Pengeluaran::create($data);
        }

        // Create historical pengeluaran
        foreach ($months as $idx => $month) {
            if ($month === 'Januari 2026') continue;
            
            $monthNum = $idx + 7;
            $year = $monthNum <= 12 ? 2025 : 2026;
            $monthNum = $monthNum <= 12 ? $monthNum : $monthNum - 12;

            foreach ([1, 2, 3, 4, 5, 6] as $katId) {
                $baseAmounts = [2500000, 2000000, 1500000, 800000, 350000, 250000];
                $variation = rand(-200000, 200000);
                Pengeluaran::create([
                    'kategori_id' => $katId,
                    'keterangan' => $kategoris[$katId - 1]['nama'] . ' bulan ' . $month,
                    'jumlah' => max(100000, $baseAmounts[$katId - 1] + $variation),
                    'tanggal' => Carbon::create($year, $monthNum, rand(1, 25)),
                ]);
            }
        }
    }
}
