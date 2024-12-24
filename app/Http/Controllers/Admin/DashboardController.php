<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return inertia('admin/dashboard/Index', [
            'data' => [
            ],
            'projects' => Project::orderBy('name', 'asc')->get()
        ]);
    }

}
