<?php

namespace App\Console\Commands;

use App\Models\DBR;
use App\Models\PeriodicService;
use App\Models\Population;
use App\Models\Project;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckPeriodicService extends Command
{
    protected $signature = 'app:check-periodic-service';
    protected $description = 'Check unit SMU accumulation and send notifications if service is due';

    public function handle()
    {
        $this->info('Starting periodic service check...');

        // 1. Sync Unique Projects from Populations (ensure projects exist)
        $uniqueProjects = Population::whereNotNull('project')
            ->where('project', '!=', '')
            ->distinct()
            ->pluck('project');

        foreach ($uniqueProjects as $projectName) {
            Project::firstOrCreate(
                ['name' => $projectName],
                ['distance_km' => 0]
            );
        }
        $this->info('Projects synced from populations.');

        // 2. Sync populations to periodic_services (ensure records exist)
        $populations = Population::where('active', 1)->get();
        foreach ($populations as $unit) {
            PeriodicService::firstOrCreate(
                ['unit_code' => $unit->unit_code],
                [
                    'service_interval_km' => 8000,
                    'average_daily_ritase' => 0,
                ]
            );
        }

        // 3. Check each periodic service record
        $services = PeriodicService::with('population')->get();
        $adminUsers = User::all(); // Assuming all registered users are admins who should receive notifications

        foreach ($services as $service) {
            // Get latest DBR as Anchor
            $latestDbr = DBR::where('unit_code', $service->unit_code)
                ->latest('date')
                ->first();
            
            // Baseline Service SMU (Reset Point)
            $baselineSmu = $service->last_service_smu ?? (DBR::where('unit_code', $service->unit_code)->oldest('date')->first()?->smu_ready ?? 0);
            
            // Calculate Current Estimated SMU
            $currentSmu = 0;
            if ($latestDbr) {
                $baseSmu = $latestDbr->smu_ready;
                $daysDiff = Carbon::parse($latestDbr->date)->startOfDay()->diffInDays(Carbon::today());
                
                $addedSmu = 0;
                $projectPop = $service->population->project ?? '';
                $project = Project::where('name', $projectPop)->first();
                
                if ($project && $service->average_daily_ritase > 0) {
                    $dailyKm = $service->average_daily_ritase * 2 * $project->distance_km;
                    $addedSmu = $daysDiff * $dailyKm;
                }
                
                $currentSmu = $baseSmu + $addedSmu;
            } else {
                // If no DBR, maybe rely on manual last_service_smu or 0
                $currentSmu = $service->last_service_smu ?? 0;
            }

            // Calculate Accumulated KM
            $accumulatedKm = max(0, $currentSmu - $baselineSmu);
            $threshold = $service->service_interval_km ?? 8000;
            $remaining = $threshold - $accumulatedKm;

            // Check if threshold reached or close (e.g. within 500KM)
            // Or strictly when Overdue? Let's notify when 1000KM remaining (Warning) and Overdue (Danger)
            $shouldNotify = false;
            $status = 'info';
            $message = '';

            if ($remaining <= 0) {
                $shouldNotify = true;
                $status = 'danger';
                $message = "Unit {$service->unit_code} SUDAH LEWAT target servis! (Overdue: " . number_format(abs($remaining)) . " KM)";
            } elseif ($remaining <= 1000) {
                $shouldNotify = true;
                $status = 'warning';
                $message = "Unit {$service->unit_code} mendekati servis (Sisa: " . number_format($remaining) . " KM)";
            }

            if ($shouldNotify) {
                // Throttle notifications: Only notify if never notified OR notified > 3 days ago OR status changed (logic simplified to time-based for now)
                
                // For simplicity: If last notified > 3 days ago, notify again.
                if (!$service->last_notified_at || $service->last_notified_at->diffInDays(now()) >= 3) {
                    $this->info("Sending Notification for {$service->unit_code}: $message");
                    
                    Notification::make()
                        ->title('Peringatan Servis Berkala')
                        ->body($message)
                        ->status($status) // danger, warning, success, info
                        ->icon('heroicon-o-exclamation-triangle')
                        ->actions([
                            Action::make('view')
                                ->label('Lihat Monitoring')
                                ->url('/admin/monitoring-periodik-service')
                                ->button(),
                        ])
                        ->sendToDatabase($adminUsers);
                    
                    $service->update(['last_notified_at' => now()]);
                }
            }
        }

        $this->info('Periodic service check completed.');
    }
}
