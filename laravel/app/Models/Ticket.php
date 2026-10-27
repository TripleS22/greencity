<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Ticket extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title', 'request_date', 'schedule_date', 'status', 'priority',
        'requester_id', 'requester_name', 'location_id', 'description', 'photo',
        'ticket_code', 'category', 'approval_status', 'maintenance_id', 'resolution_notes', 'resolved_at'
    ];

    protected $casts = [
        'request_date' => 'date',
        'schedule_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}
