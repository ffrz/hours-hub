<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class TimeEntry extends Model
{
    protected $fillable = ['user_id', 'project_id', 'title', 'start_time', 'end_time', 'duration', 'notes'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getTotalDuration($user_id, $project_id, $period)
    {
        $q = DB::table('time_entries')
            ->where('user_id', $user_id);

        if (!empty($project_id) && $project_id != 'all') {
            $q->where('project_id', $project_id);
        }

        if (!empty($period) && $period != 'all') {
            if ($period == 'today') {
                $q->whereDate('start_time', '=', Carbon::today());
            } else if ($period == 'yesterday') {
                $q->whereDate('start_time', '=', Carbon::yesterday());
            } else if ($period == 'this_week') {
                $q->whereBetween('start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            } else if ($period == 'prev_week') {
                $q->whereBetween('start_time', [Carbon::now()->subWeek()->startOfWeek(),  Carbon::now()->subWeek()->endOfWeek()])->get();
            } else if ($period == 'this_month') {
                $q->whereBetween('start_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
            } else if ($period == 'prev_month') {
                $q->whereBetween('start_time', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->get();
            }
        }

        return $q->sum('duration');
    }

    public static function getTopProject($user_id, $period)
    {
        $q = DB::table('time_entries')
            ->leftJoin('projects', 'time_entries.project_id', '=', 'projects.id')
            ->select('project_id', 'projects.name as project_name', DB::raw('SUM(duration) as total_duration'))
            ->where('user_id', $user_id);

        if (!empty($period) && $period != 'all') {
            if ($period == 'today') {
                $q->whereDate('start_time', '=', Carbon::today());
            } else if ($period == 'yesterday') {
                $q->whereDate('start_time', '=', Carbon::yesterday());
            } else if ($period == 'this_week') {
                $q->whereBetween('start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            } else if ($period == 'prev_week') {
                $q->whereBetween('start_time', [Carbon::now()->subWeek()->startOfWeek(),  Carbon::now()->subWeek()->endOfWeek()])->get();
            } else if ($period == 'this_month') {
                $q->whereBetween('start_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
            } else if ($period == 'prev_month') {
                $q->whereBetween('start_time', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->get();
            }
        }

        return $q->groupBy('project_id')
            ->orderByDesc('total_duration')
            ->first();
    }

    public static function getTopEntries($user_id, $period)
    {
        $q = DB::table('time_entries')
            ->leftJoin('projects', 'time_entries.project_id', '=', 'projects.id')
            ->select(
                'time_entries.id as id',
                'time_entries.title as title',
                'time_entries.project_id',
                'projects.name as project_name',
                'time_entries.duration',
            )
            ->where('user_id', $user_id);

        if (!empty($period) && $period != 'all') {
            if ($period == 'today') {
                $q->whereDate('start_time', '=', Carbon::today());
            } else if ($period == 'yesterday') {
                $q->whereDate('start_time', '=', Carbon::yesterday());
            } else if ($period == 'this_week') {
                $q->whereBetween('start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            } else if ($period == 'prev_week') {
                $q->whereBetween('start_time', [Carbon::now()->subWeek()->startOfWeek(),  Carbon::now()->subWeek()->endOfWeek()])->get();
            } else if ($period == 'this_month') {
                $q->whereBetween('start_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
            } else if ($period == 'prev_month') {
                $q->whereBetween('start_time', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->get();
            }
        }

        return $q->orderByDesc('time_entries.duration')
            ->limit(10)
            ->get();
    }
}
