<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverReport extends Model
{
    use HasFactory;

    protected $table = 'driver_reports';

    protected static function booted()
    {
        static::created(function ($driverReport) {
            $role = $driverReport->report_role ?? 'Driver';
            $title = ($role === 'Driver') ? 'Laporan Driver Baru' : 'Laporan Manager/Asmen Baru';
            
            \Filament\Actions\Action::make('test'); // Dummy call to ensure class exists for autoloader if needed (optional)

            \Filament\Notifications\Notification::make()
                ->title($title)
                ->body("Unit: {$driverReport->unit_code}\nKeluhan: {$driverReport->unit_driver_problem}")
                ->warning() // Yellow for "Needs Attention"
                ->actions([
                    \Filament\Actions\Action::make('review')
                        ->label('Review')
                        ->url("/admin/driver-reports/{$driverReport->id}/edit")
                        ->button(),
                ])
                ->sendToDatabase(\App\Models\User::all());
        });

        static::updated(function ($driverReport) {
            if ($driverReport->is_approved && !$driverReport->wasChanged('is_approved')) {
                // Already approved, do nothing or handle re-approval logic if needed
            }

            if ($driverReport->is_approved && $driverReport->wasChanged('is_approved')) {
                // Check if an analysis already exists to prevent duplicate
                $exists = MechanicAnalysis::where('unit_code', $driverReport->unit_code)
                    ->where('tanggal', $driverReport->tanggal)
                    ->where('unit_driver_problem', $driverReport->unit_driver_problem)
                    ->exists();

                if (!$exists) {
                    MechanicAnalysis::create([
                        'tanggal' => now(), // Or use $driverReport->tanggal if strictly same day
                        'unit_code' => $driverReport->unit_code,
                        'nama_driver' => $driverReport->nama_driver,
                        'unit_driver_problem' => $driverReport->unit_driver_problem,
                        'status_analysis' => 'Pending', // Set status to Pending
                        'nama_mechanic_satu' => '-', // Placeholder
                    ]);
                }
            }
        });
    }

    protected $fillable = [
        'tanggal',
        'unit_code',
        'nama_driver',
        'nama_pelapor',
        'unit_driver_problem',

        'is_approved',
        'report_role',
    ];

    /**
     * Get the unit associated with the report.
     */
    public function unit()
    {
        return $this->belongsTo(Population::class, 'unit_code', 'unit_code');
    }
}
