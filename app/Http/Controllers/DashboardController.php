<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Si c'est un utilisateur Boulangerie, redirection vers son dashboard dédié
        if ($user->isBakeryUser()) {
            return redirect()->route('dashboard.boulangerie');
        }

        return view('dashboard');
    }

    public function settings()
    {
        return view('settings.index');
    }

    public function superAdminOverview()
    {
        return view('superadmin.overview.index');
    }


}
