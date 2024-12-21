<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        return inertia('admin/client/Index');
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'name');
        $orderType = $request->get('order_type', 'asc');
        $filter = $request->get('filter', []);

        $q = Client::query();
        $q->orderBy($orderBy, $orderType);

        if (!empty($filter['status']) && ($filter['status'] == 'active' || $filter['status'] == 'inactive')) {
            $q->where('active', '=', $filter['status'] == 'active' ? true : false);
        }

        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('email', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('phone', 'like', '%' . $filter['search'] . '%');
            });
        }

        $clients = $q->paginate($request->get('per_page', 10))->withQueryString();
        return response()->json($clients);
    }

    public function editor($id = 0)
    {
        $client = $id ? Client::findOrFail($id) : new Client();
        return inertia('admin/client/Editor', [
            'data' => $client,
        ]);
    }

    public function save(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'email' => 'email|min:3|max:100',
        ];
        $messages = [
            'name.required' => 'Nama klien harus diisi.',
            'name.max' => 'Nama klien maksimal 255 karakter.',
            'name.unique' => 'Nama klien sudah digunakan.',
        ];
        $client = null;
        $message = '';
        $fields = ['name', 'phone', 'email', 'address', 'active', 'notes'];
        if (!$request->id) {
            $rules['name'] = "required|max:255|unique:clients,name,NULL,id";
            $request->validate($rules, $messages);
            $client = new Client();
            $message = 'Klien baru telah dibuat.';
        } else {
            $rules['name'] = "required|max:255|unique:clients,name,{$request->id},id";
            $request->validate($rules, $messages);
            $client = Client::findOrFail($request->id);
            $message = "Klien {$client->name} telah diperbarui.";
        }
        $data = $request->only($fields);
        $data['phone'] = $data['phone'] ?? '';
        $data['email'] = $data['email'] ?? '';
        $data['address'] = $data['address'] ?? '';
        $data['notes'] = $data['notes'] ?? '';
        $client->fill($data);
        $client->save();

        return redirect(route('admin.client.index'))->with('success', $message);
    }

    public function delete($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json([
            'message' => "Klien {$client->name} telah dihapus!"
        ]);
    }
}
