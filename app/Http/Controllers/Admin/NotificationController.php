<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with('user')->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'sent') $query->where('is_sent', true);
            elseif ($status === 'pending') $query->where('is_sent', false);
        }

        $notifications = $query->paginate(20)->withQueryString();

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        // For sending manual notifications
        $users = User::select('id', 'full_name', 'email', 'role')->get();
        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:appointment,result,system,reminder',
            'channels' => 'required|array',
            'channels.*' => 'in:in_web,email',
            'scheduled_at' => 'nullable|date|after_or_equal:now',
        ]);

        foreach ($request->user_ids as $userId) {
            foreach ($request->channels as $channel) {
                Notification::create([
                    'user_id' => $userId,
                    'title' => $request->title,
                    'content' => $request->content,
                    'type' => $request->type,
                    'channel' => $channel,
                    'scheduled_at' => $request->scheduled_at,
                    'is_sent' => false,
                ]);
            }
        }

        return redirect()->route('admin.notifications.index')->with('success', 'Đã tạo thông báo thành công.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Đã xoá thông báo.');
    }

    public function resend(Notification $notification)
    {
        $notification->update(['is_sent' => false]);
        return back()->with('success', 'Đã đặt lại trạng thái để gửi lại thông báo.');
    }
}
