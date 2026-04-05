<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        $role = $user->role?->role_name ?? 'Tidak ada role';
        // dd($user, $role);

        return view('dashboard', compact('user', 'role'));
    }
}
