<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateMaintenanceSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:generate {--redistribute : Hapus dan buat ulang jadwal masa depan secara merata}';

    protected $description = 'Generate scheduled maintenance for each location every 3 months';

    public function handle()
    {
        // === MENCEGAH JADWAL MENUMPUK: HAPUS DUPLIKAT TERLEBIH DAHULU ===
        // Karena sistem yang baru hanya mengizinkan 1 jadwal per lokasi
        $all_locations = \App\Models\Location::all();
        foreach ($all_locations as $loc) {
            $existing = \App\Models\Maintenance::where('location_id', $loc->id)->orderBy('date', 'desc')->get();
            if ($existing->count() > 1) {
                // Simpan jadwal yang paling jauh/terbaru saja, sisa riwayat akan digantikan oleh tiket keluhan
                $keep = $existing->first();
                \App\Models\Maintenance::where('location_id', $loc->id)->where('id', '!=', $keep->id)->delete();
            }
        }

        $locations = \App\Models\Location::orderBy('id')->get();
        $totalLocations = $locations->count();
        $generatedCount = 0;

        foreach ($locations as $index => $location) {
            $maintenance = \App\Models\Maintenance::where('location_id', $location->id)->first();

            // Cari tiket perbaikan/maintenance terakhir yang sudah selesai (sebagai riwayat pengerjaan asli)
            $lastTicket = \App\Models\Ticket::where('location_id', $location->id)
                ->where('status', 'closed')
                ->where('category', 'maintenance')
                ->orderBy('resolved_at', 'desc')
                ->first();

            $nextDate = null;

            if ($lastTicket && $lastTicket->resolved_at) {
                // Perhitungan selalu dimulai 3 bulan setelah perbaikan SEBENARNYA selesai dilakukan
                $nextDate = $lastTicket->resolved_at->copy()->addMonths(3);
                
                // Jika jadwal barusan sudah terlewat di masa lalu, maka jatuhkan jadwalnya +3 bulan dari hari ini
                if ($nextDate->isPast()) {
                    $nextDate = now()->addMonths(3);
                }
            } else {
                if ($maintenance) {
                    $nextDate = $maintenance->date;
                    // Jika jadwal lama belum tereksekusi dan sudah terlewat, anggap harus di-maintenance segera (tambah 3 bulan dari sblmnya atau dr skrg)
                    if (now()->startOfDay()->gt($nextDate)) {
                        $nextDate = now()->addMonths(3);
                    }
                } else {
                    // Jika lokasi benar-benar baru dan belum punya jadwal serta histori
                    $daysOffset = floor(($index / max(1, $totalLocations)) * 90);
                    $nextDate = now()->addDays($daysOffset);
                }
            }

            // Update atau buat 1 ROW SAJA per lokasi
            $m = \App\Models\Maintenance::updateOrCreate(
                ['location_id' => $location->id],
                [
                    'category' => 'all',
                    'description' => 'Scheduled Maintenance (Auto-Generated)',
                    'date' => $nextDate,
                ]
            );

            if ($m->wasRecentlyCreated || $m->wasChanged('date')) {
                $generatedCount++;
            }
        }

        $this->info("Pengecekan selesai. {$generatedCount} jadwal diperbarui atau dibuat ulang secara terpusat (1 jadwal per lokasi).");
    }
}
