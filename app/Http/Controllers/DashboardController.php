<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        $role = $user->role?->role_name ?? '';

        // Arahkan ke view spesifik per role
        return match(true) {
            in_array($role, ['Guru', 'Guru Ngaji']) => view('guru.dashboard', compact('user')),
            default                                  => view('dashboard', compact('user', 'role')),
        };
    }
}
