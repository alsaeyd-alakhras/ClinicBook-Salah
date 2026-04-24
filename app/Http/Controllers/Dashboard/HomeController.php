<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard.bookings.index');
    }

    public function refreshDashboardCache()
    {
        return redirect()
            ->route('dashboard.bookings.index');
    }
}
