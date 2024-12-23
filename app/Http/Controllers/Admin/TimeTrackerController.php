<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeTrackerController extends Controller
{

    public function index()
    {
        $projects = Project::all();
        return inertia('admin/time-tracker/Index', compact('projects'));
    }

    public function start(Request $request)
    {
        $title = $request->input('title', '');
        $projectId = $request->input('project_id');
        $entry = new TimeEntry([
            'user_id' => Auth::user()->id,
            'project_id' => $projectId ? $projectId : null,
            'title' => $title ? $title : '',
            'start_time' => now(),
        ]);
        $entry->save();
        return response()->json($entry);
    }

    public function stop(Request $request)
    {
        $entry = TimeEntry::find($request->input('id'));
        if (!$entry || $entry->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Tidak ditemukan'], 404);
        }

        // cek apakah sesi sedang berjalan
        if ($entry->end_time) {
            return response()->json(['message' => 'Sesi sudah berakhir'], 400);
        }

        $entry->end_time = now();
        $entry->duration = Carbon::parse($entry->start_time)->diffInSeconds($entry->end_time);
        $entry->save();

        return response()->json($entry);
    }

    public function update(Request $request)
    {
        $entry = TimeEntry::find($request->input('id'));
        if (!$entry || $entry->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Tidak ditemukan'], 404);
        }

        if ($entry->end_time) {
            return response()->json(['message' => 'Sesi sudah berakhir'], 400);
        }

        $entry->fill($request->all(['project_id', 'title']));
        $entry->project_id = $entry->project_id ? $entry->project_id : null;
        $entry->title = $entry->title ? $entry->title : '';
        $entry->save();
        return response()->json($entry);
    }

    public function sync(Request $request)
    {
        $entry = TimeEntry::find($request->input('id'));
        if (!$entry || $entry->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Tidak ditemukan'], 404);
        }

        if ($entry->end_time) {
            return response()->json(['message' => 'Sesi sudah berakhir'], 400);
        }

        $entry->duration = (int)Carbon::parse($entry->start_time)->diffInSeconds(now());

        return response()->json($entry);
    }

    public function cancel(Request $request)
    {
        $entry = TimeEntry::find($request->input('id'));

        if (!$entry || $entry->user_id != Auth::user()->id) {
            return response()->json(['message' => 'Tidak ditemukan'], 404);
        }

        if ($entry->end_time) {
            return response()->json(['message' => 'Sesi sudah berakhir'], 400);
        }

        $entry->delete();
        return $entry;
    }
}
