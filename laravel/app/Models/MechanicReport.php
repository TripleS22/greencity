<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechanicReport extends Model
{
    use HasFactory;

    protected $table = 'mechanic_reports';

    protected static function booted()
    {
        static::created(function ($mechanicReport) {

        });

        static::saving(function ($mechanicReport) {
            // Validation: SMU ready must be provided if time_finish is set
            if ($mechanicReport->time_finish && empty($mechanicReport->smu_ready)) {
                throw new \Illuminate\Validation\ValidationException(\Illuminate\Support\Facades\Validator::make([], []), [
                    'smu_ready' => ["SMU Ready (Km Akhir) must be filled when finish time is set."]
                ]);
            }

            // Validation: SMU ready must be >= SMU down from analysis
            if ($mechanicReport->mechanicAnalysis && $mechanicReport->smu_ready) {
                $smuDown = (float) $mechanicReport->mechanicAnalysis->smu_down;
                $smuReady = (float) $mechanicReport->smu_ready;
                
                if ($smuReady < $smuDown) {
                    throw new \Illuminate\Validation\ValidationException(\Illuminate\Support\Facades\Validator::make([], []), [
                        'smu_ready' => ["SMU Ready ({$smuReady}) cannot be less than SMU Down ({$smuDown})."]
                    ]);
                }
            }
        });

        static::saved(function ($mechanicReport) {
            if ($mechanicReport->mechanicAnalysis && $mechanicReport->mechanicAnalysis->unit_code) {
                $dbr = DBR::where('mechanic_analysis_id', $mechanicReport->mechanic_analysis_id)->first();
                
                // Fallback to unit_code and date if no direct ID link exists (for legacy records)
                if (!$dbr) {
                    $dbr = DBR::where('unit_code', $mechanicReport->unit_code)
                        ->where('date', $mechanicReport->mechanicAnalysis->tanggal)
                        ->first();
                }

                if ($dbr) {
                    $updateData = array_filter([
                        'time_start' => $mechanicReport->time_start,
                        'time_finish' => $mechanicReport->time_finish,
                        'smu_ready' => $mechanicReport->smu_ready,
                        'activity' => $mechanicReport->activity,
                        'duration_db' => $dbr->duration_db, // Use the accessor result
                        'sales_service' => $mechanicReport->sales_service,
                    ], fn($value) => !is_null($value));

                    if ($mechanicReport->status_approval === 'Approved') {
                        $updateData['remarks'] = null;
                    }

                    if (!empty($updateData)) {
                        $dbr->update($updateData);
                    }
                }
            }
        });
    }

    protected $fillable = [
        'mechanic_analysis_id',
        'unit_code',
        'activity',
        'time_start',
        'time_finish',
        'smu_ready',
        'documentation',
        'status_approval',
        'admin_response',
        'sales_service',
        'is_paused',
        'pause_reason',
        'accumulated_seconds',
        'paused_at',
        'resumed_at',
    ];

    protected $casts = [
        'documentation' => 'array',
        'is_paused' => 'boolean',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
    ];

    /**
     * Get the unit associated with the report.
     */
    public function unit()
    {
        return $this->belongsTo(Population::class, 'unit_code', 'unit_code');
    }

    public function mechanicAnalysis()
    {
        return $this->belongsTo(MechanicAnalysis::class);
    }

    public function getDurationAttribute()
    {
        if (!$this->time_start || !$this->time_finish) {
            return null;
        }

        try {
            // Since MechanicReport doesn't have a date, we might need to reference MechanicAnalysis date
            // or assume if finish < start, it's next day. 
            // Better to use the dates from DBR if available, but for this model's accessor:
            $start = \Carbon\Carbon::parse($this->time_start);
            $finish = \Carbon\Carbon::parse($this->time_finish);

            if ($finish->lt($start)) {
                $finish->addDay();
            }

            $diff = $start->diff($finish);
            
            $parts = [];
            if ($diff->d > 0) $parts[] = $diff->d . ' Hari';
            if ($diff->h > 0) $parts[] = $diff->h . ' Jam';
            if ($diff->i > 0) $parts[] = $diff->i . ' Menit';
            if ($diff->s > 0 && empty($parts)) $parts[] = $diff->s . ' Detik';

            return empty($parts) ? '0 Menit' : implode(' ', $parts);
        } catch (\Exception $e) {
            return null;
        }
    }
}
