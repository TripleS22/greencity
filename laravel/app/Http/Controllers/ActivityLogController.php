<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = Activity::latest()->paginate(25);
        return view('admin.activity_log', compact('activities'));
    }
}
