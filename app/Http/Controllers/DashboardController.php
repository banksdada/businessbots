<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $business = $request->user()->activeBusiness();

        return view('dashboard.index', [
            'business' => $business,
        ]);
    }

    public function settings(Request $request): View
    {
        $business = $request->user()->activeBusiness();

        return view('dashboard.settings', [
            'business' => $business,
        ]);
    }
}
