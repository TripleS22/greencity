<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    protected $fillable = [
        'spk_number',
        'mechanic_analysis_id',
        'unit_code',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function mechanicAnalysis()
    {
        return $this->belongsTo(MechanicAnalysis::class);
    }
}
