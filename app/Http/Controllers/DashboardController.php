<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk peran Warga.
     */
    public function warga(): View
    {
        return view('dashboard.warga', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Dashboard untuk peran Admin RT.
     */
    public function adminRt(): View
    {
        return view('dashboard.admin-rt', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Dashboard untuk peran Admin RW.
     */
    public function adminRw(): View
    {
        return view('dashboard.admin-rw', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Dashboard untuk peran Petugas.
     */
    public function petugas(): View
    {
        return view('dashboard.petugas', [
            'user' => Auth::user(),
        ]);
    }
}
