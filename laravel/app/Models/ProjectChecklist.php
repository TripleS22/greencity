<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
