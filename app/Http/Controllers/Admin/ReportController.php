<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return inertia('admin/report/Index');
    }
}
