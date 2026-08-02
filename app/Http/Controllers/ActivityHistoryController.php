<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer', 'subject');

        // Filter by causer name
        if ($request->filled('causer')) {
            $query->whereHasMorph('causer', '*', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->causer . '%');
            });
        }

        // Filter by event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by model/subject type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%' . $request->subject_type . '%');
        }

        // Filter by start date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filter by end date
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.activity-history.index', compact('activities'));
    }

    public function show($id)
    {
        $activity = Activity::with('causer', 'subject')->findOrFail($id);

        return view('admin.activity-history.show', compact('activity'));
    }
}
