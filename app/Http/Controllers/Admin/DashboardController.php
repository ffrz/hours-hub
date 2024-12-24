<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return inertia('admin/dashboard/Index', [
            'data' => [],
            'projects' => Project::orderBy('name', 'asc')->get()
        ]);
    }

    public function data(Request $request)
    {

        $user_id = Auth::user()->id;
        $project_id = intval($request->get('project_id'));
        $period = (string)$request->get('period');

        return response()->json([
            'total_duration' => TimeEntry::getTotalDuration($user_id, $project_id, $period),
            'top_project' => TimeEntry::getTopProject($user_id, $period),
            'top_entries' => TimeEntry::getTopEntries($user_id, $period)
        ]);
    }
}
