<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return inertia('admin/project/Index', [
            'clients' => Client::orderBy('name', 'asc')->get(['id', 'name']),
        ]);
    }

    public function list(Request $request)
    {
        $projects = Project::query()
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
        return response()->json($projects);
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'name');
        $orderType = $request->get('order_type', 'asc');
        $filter = $request->get('filter', []);

        $q = Project::with(['client']);
        $q->orderBy($orderBy, $orderType);

        if (!empty($filter['status']) && ($filter['status'] == 'active' || $filter['status'] == 'inactive')) {
            $q->where('active', '=', $filter['status'] == 'active' ? true : false);
        }

        if (!empty($filter['client_id']) && $filter['client_id'] != 'all') {
            $q->where('client_id', '=', $filter['client_id']);
        }

        if (!empty($filter['search'])) {
            $q->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter['search'] . '%');
            });
        }

        $projects = $q->paginate($request->get('per_page', 10))->withQueryString();
        return response()->json($projects);
    }

    public function editor($id = 0)
    {
        $project = $id ? Project::findOrFail($id) : new Project(['active' => true]);
        $clients = Client::orderBy('name', 'asc')->get(['id', 'name']);
        return inertia('admin/project/Editor', [
            'data' => $project,
            'clients' => $clients,
        ]);
    }

    public function save(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
        ];
        $messages = [
            'name.required' => 'Nama proyek harus diisi.',
            'name.max' => 'Nama proyek maksimal 255 karakter.',
            'name.unique' => 'Nama proyek sudah digunakan.',
        ];
        $project = null;
        $message = '';
        $fields = ['name', 'client_id', 'active', 'notes'];
        if (!$request->id) {
            $rules['name'] = "required|max:255|unique:projects,name,NULL,id";
            $request->validate($rules, $messages);
            $project = new Project();
            $message = 'Proyek baru telah dibuat.';
        } else {
            $rules['name'] = "required|max:255|unique:projects,name,{$request->id},id";
            $request->validate($rules, $messages);
            $project = Project::findOrFail($request->id);
            $message = "Proyek {$project->name} telah diperbarui.";
        }
        $data = $request->only($fields);
        $data['client_id'] = $data['client_id'] ?? null;
        $data['active'] = $data['active'] ?? true;
        $data['notes'] = $data['notes'] ?? '';
        $project->fill($data);
        $project->save();

        return redirect(route('admin.project.index'))->with('success', $message);
    }

    public function delete($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'message' => "Proyek {$project->name} telah dihapus!"
        ]);
    }
}
