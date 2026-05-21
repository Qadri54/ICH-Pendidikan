<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $url = $notification->data['url'] ?? route('dashboard');
        $notification->delete();

        return redirect($url);
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()->delete();

        return back();
    }
}
