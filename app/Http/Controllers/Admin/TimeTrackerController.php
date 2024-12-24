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

    public function checkLastSession(Request $request)
    {
        $user = Auth::user();
        $entry = TimeEntry::query()
            ->where('user_id', $user->id)
            ->where('end_time', null)
            ->orderBy('id', 'desc')
            ->first();

        if ($entry) {
            $entry->duration = (int)Carbon::parse($entry->start_time)->diffInSeconds($entry->end_time);
        }

        return response()->json($entry);
    }

    public function data(Request $request)
    {
        $user = Auth::user();
        $orderBy = $request->get('order_by', 'name');
        $orderType = $request->get('order_type', 'asc');
        $filter = $request->get('filter', []);

        $q = TimeEntry::with(['project']);
        $q->where('user_id', $user->id)
            ->where('end_time', '<>', null);
        $q->orderBy($orderBy, $orderType);

        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('title', 'like', '%' . $filter['search'] . '%');
            });
        }

        if (!empty($filter['project_id']) && $filter['project_id'] != 'all') {
            $q->where('project_id', '=', $filter['project_id']);
        }


        if (!empty($filter['period']) && $filter['period'] != 'all') {
            if ($filter['period'] == 'today') {
                $q->whereDate('start_time', Carbon::today())->get();
            } else if ($filter['period'] == 'yesterday') {
                $q->whereDate('start_time', Carbon::yesterday())->get();
            } else if ($filter['period'] == 'this_week') {
                $q->whereBetween('start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            } else if ($filter['period'] == 'prev_week') {
                $q->whereBetween('start_time', [Carbon::now()->subWeek()->startOfWeek(),  Carbon::now()->subWeek()->endOfWeek()])->get();
            } else if ($filter['period'] == 'this_month') {
                $q->whereBetween('start_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
            } else if ($filter['period'] == 'prev_month') {
                $q->whereBetween('start_time', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->get();
            }
        }

        $items = $q->paginate($request->get('per_page', 10))->withQueryString();
        return response()->json($items);
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
