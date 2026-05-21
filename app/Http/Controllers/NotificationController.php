<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        $role = auth()->user()->role?->role_name;
        $view = $role === 'Orang Tua' ? 'notifications.mobile' : 'notifications.index';

        return view($view, compact('notifications'));
    }

    public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $url = $notification->data['url'] ?? route('dashboard');
        $notification->delete();

        return redirect($url);
    }

    public function destroy(string $id)
    {
        auth()->user()->notifications()->findOrFail($id)->delete();

        return back()->with('success', 'Notifikasi dihapus.');
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()->delete();

        return back();
    }
}
