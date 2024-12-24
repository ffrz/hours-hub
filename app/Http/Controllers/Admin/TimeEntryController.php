<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeEntryController extends Controller
{

    public function index()
    {
        $users = User::query()->orderBy('name', 'asc')->get();
        $projects = Project::query()->orderBy('name', 'asc')->get();
        return inertia('admin/time-entry/Index', compact('projects', 'users'));
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'name');
        $orderType = $request->get('order_type', 'asc');
        $filter = $request->get('filter', []);

        $q = TimeEntry::with(['user', 'project']);
        $q->orderBy($orderBy, $orderType);

        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('title', 'like', '%' . $filter['search'] . '%');
            });
        }

        if (!empty($filter['user_id']) && $filter['user_id'] != 'all') {
            $q->where('user_id', '=', $filter['user_id']);
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

    public function editor($id = 0)
    {
        $users = User::query()->orderBy('name', 'asc')->get();
        $projects = Project::query()->orderBy('name', 'asc')->get();
        $entry = $id ? TimeEntry::findOrFail($id) : new TimeEntry(['start_time' => Carbon::now()->toDateTimeString()]);
        return inertia('admin/time-entry/Editor', [
            'data' => $entry,
            'users' => $users,
            'projects' => $projects,
        ]);
    }

    public function save(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'start_time' => 'required',
        ];
        $messages = [
            'user_id.required' => 'Pilih pengguna.',
            'start_time.required' => 'Waktu mulai harus diisi.',
        ];
        $message = '';
        $fields = ['title', 'user_id', 'project_id', 'start_time', 'end_time', 'notes'];
        if (!$request->id) {
            $request->validate($rules, $messages);
            $entry = new TimeEntry();
            $message = 'Entri baru telah dibuat.';
        } else {
            $request->validate($rules, $messages);
            $entry = TimeEntry::findOrFail($request->id);
            $message = "Entri {$entry->name} telah diperbarui.";
        }

        $data = $request->only($fields);
        $data['title'] = $data['title'] ?? '';
        $data['project_id'] = $data['project_id'] ?? null;
        $data['start_time'] = $data['start_time'] ?? null;
        $data['end_time'] = $data['end_time'] ?? null;
        if ($data['start_time'] && $data['end_time']) {
            $data['start_time'] = Carbon::parse($data['start_time']);
            $data['end_time'] = Carbon::parse($data['end_time']);
            $data['duration'] = $data['start_time']->diffInSeconds($data['end_time']);
        }
        $data['notes'] = $data['notes'] ?? '';

        $entry->fill($data);
        $entry->save();

        return redirect(route('admin.time-entry.index'))->with('success', $message);
    }

    public function delete(Request $request, $id)
    {
        $entry = TimeEntry::findOrFail($id);
        $entry->delete();
        return response()->json([
            'message' => "Entri {$entry->title} telah dihapus!"
        ]);
    }
}
