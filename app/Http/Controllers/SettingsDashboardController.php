<?php

namespace App\Http\Controllers;

class SettingsDashboardController extends Controller
{
    public function index()
    {
        return view('settings.dashboard');
    }
}
