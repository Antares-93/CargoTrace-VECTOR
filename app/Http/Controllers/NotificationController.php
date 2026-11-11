<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Notification $notification, Request $request): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        if ($notification->shipment_id) {
            return redirect()->route('shipments.show', $notification->shipment_id);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        Notification::where('user_id', $request->user()->id)->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
