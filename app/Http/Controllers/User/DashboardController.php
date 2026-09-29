<?php

namespace App\Http\Controllers\User;

use App\Data\User\DashboardData;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view(
            'user.dashboard.index',
            DashboardData::get()
        );
    }
}
