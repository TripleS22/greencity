<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPengeluaran;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Kebersihan', 'deskripsi' => 'Biaya kebersihan lingkungan (Sampah, dll)'],
            ['nama' => 'Keamanan', 'deskripsi' => 'Biaya keamanan dan honor satpam/hansip'],
            ['nama' => 'Listrik', 'deskripsi' => 'Biaya listrik fasilitas umum (Lampu jalan, pos)'],
            ['nama' => 'Pemeliharaan', 'deskripsi' => 'Biaya perbaikan/pemeliharaan fasilitas (Gapura, jalan, portal)'],
            ['nama' => 'Taman', 'deskripsi' => 'Biaya perawatan taman dan penghijauan'],
            ['nama' => 'Air', 'deskripsi' => 'Biaya air bersih fasilitas umum'],
            ['nama' => 'Sosial', 'deskripsi' => 'Biaya kegiatan sosial (Takziah, hajatan, bantuan)'],
            ['nama' => 'Lain-lain', 'deskripsi' => 'Pengeluaran lain yang tidak terduga'],
        ];

        foreach ($kategoris as $kategori) {
            KategoriPengeluaran::firstOrCreate(['nama' => $kategori['nama']], $kategori);
        }
    }
}
