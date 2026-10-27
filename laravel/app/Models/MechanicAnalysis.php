<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class MechanicAnalysis extends Model
{
    use HasFactory;

    protected $table = 'mechanic_analyses';

    protected static function booted()
    {
        static::created(function ($mechanicAnalysis) {


            $population = Population::where('unit_code', $mechanicAnalysis->unit_code)->first();
            
            $date = Carbon::parse($mechanicAnalysis->tanggal);
            $week = $date->weekOfYear;

            DBR::create([
                'mechanic_analysis_id' => $mechanicAnalysis->id,
                'date' => $mechanicAnalysis->tanggal,
                'week' => $week,
                'unit_code' => $mechanicAnalysis->unit_code,
                'brand' => $population?->brand ?? '-',
                'model' => $population?->model ?? '-',
                'eqp_type' => $population?->eqp_type ?? '-',
                'nama_mechanic_satu' => $mechanicAnalysis->nama_mechanic_satu ?? '-',
                'nama_mechanic_dua' => $mechanicAnalysis->nama_mechanic_dua ?? '-',
                'problem_reported' => $mechanicAnalysis->unit_driver_problem ?? '-',
                'shift_down' => '-',
                'location_unit' => $mechanicAnalysis->location_unit ?? $population?->loc_unit ?? '-',
                'category_bd' => '-',
                'date_bd' => $mechanicAnalysis->tanggal,
                'time_start_bd' => Carbon::parse($mechanicAnalysis->updated_at)->format('H:i:s'),
                'smu_down' => $mechanicAnalysis->smu_down ?? '0',
                'time_start' => '00:00:00',
                'time_finish' => '00:00:00',
                'smu_ready' => '0',
                'duration_db' => '00:00:00',
                'project' => $population?->project ?? '-',
                'status_code' => '-',
                'mtc_type' => '-',
                'status_bd' => '-',
                'activity' => $population?->activity ?? '-',
                'unit_status' => $population?->unit_status ?? '-',
                'remarks' => '-',
                'count_mtc_type' => '0',
                'task_down' => '-',
                'nama_driver' => $mechanicAnalysis->nama_driver ?? '-',
                'component' => '-',
                'qty' => '0',
                'unit' => '-',
                'purchase_price' => 0,
                'price_per_pcs' => 0,
                'amount' => 0,
                'service_charge' => 0,
                'total' => 0,
                'sales_sparepart' => 0,
                'sales_service' => 0,
            ]);
        });

        static::updated(function ($mechanicAnalysis) {
            $dbr = DBR::where('mechanic_analysis_id', $mechanicAnalysis->id)->first();
            if ($dbr) {
                $dbr->update([
                    'nama_mechanic_satu' => $mechanicAnalysis->nama_mechanic_satu ?? $dbr->nama_mechanic_satu,
                    'nama_mechanic_dua' => $mechanicAnalysis->nama_mechanic_dua ?? $dbr->nama_mechanic_dua,
                    'smu_down' => $mechanicAnalysis->smu_down ?? $dbr->smu_down,
                    'location_unit' => $mechanicAnalysis->location_unit ?? $dbr->location_unit,
                    'problem_reported' => $mechanicAnalysis->unit_driver_problem ?? $dbr->problem_reported,
                    'nama_driver' => $mechanicAnalysis->nama_driver ?? $dbr->nama_driver,
                ]);
            }
        });
    }

    protected $fillable = [
        'tanggal',
        'unit_code',
        'nama_mechanic_satu',
        'nama_mechanic_dua',
        'unit_driver_problem',
        'nama_driver',
        'smu_down',
        'location_unit',
        'hasil_analisis',
        'saran_perbaikan',
        'status_analysis',
        'is_approved',
    ];

    /**
     * Get the unit associated with the analysis.
     */
    public function unit()
    {
        return $this->belongsTo(Population::class, 'unit_code', 'unit_code');
    }

    /**
     * Get the spare parts to be replaced for this analysis.
     */
    /**
     * Get the spare part requests for this analysis.
     */
    public function sparePartRequests()
    {
        return $this->hasMany(SparePartRequest::class, 'mechanic_analysis_id');
    }

    public function mechanicReport()
    {
        return $this->hasOne(MechanicReport::class);
    }
}
