<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with('user')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }
        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($uq) use ($request) {
                      $uq->where('full_name', 'like', '%' . $request->search . '%')
                         ->orWhere('phone', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $notifications = $query->paginate(20)->withQueryString();

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('full_name')->get();
        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target' => 'required|in:single,role,all',
            'user_id' => 'required_if:target,single|nullable|exists:users,id',
            'role' => 'required_if:target,role|nullable|in:patient,doctor,receptionist,admin',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:appointment,result,system,reminder',
            'channel' => 'required|in:in_web,email,zalo',
        ]);

        $userIds = [];

        if ($request->target === 'single') {
            $userIds[] = $request->user_id;
        } elseif ($request->target === 'role') {
            $userIds = User::where('is_active', true)->where('role', $request->role)->pluck('id')->toArray();
        } elseif ($request->target === 'all') {
            $userIds = User::where('is_active', true)->pluck('id')->toArray();
        }

        if (empty($userIds)) {
            return back()->with('error', 'Không tìm thấy người dùng phù hợp để gửi thông báo.')->withInput();
        }

        $now = now();
        $notifications = [];
        foreach ($userIds as $id) {
            $notifications[] = [
                'user_id' => $id,
                'title' => $request->title,
                'content' => $request->content,
                'type' => $request->type,
                'channel' => $request->channel,
                'is_read' => false,
                'created_at' => $now,
            ];
        }

        DB::transaction(function () use ($notifications, $userIds) {
            // Batch insert in chunks to avoid query length limits
            foreach (array_chunk($notifications, 500) as $chunk) {
                Notification::insert($chunk);
            }

            SystemLog::create([
                'user_id' => Auth::id(),
                'action' => 'NOTIFICATION_SENT',
                'module' => 'notifications',
                'description' => 'Gửi thông báo đến ' . count($userIds) . ' người dùng',
                'ip_address' => request()->ip()
            ]);
        });

        return redirect()->route('admin.notifications.index')->with('success', 'Đã gửi thông báo đến ' . count($userIds) . ' người dùng.');
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Đã xoá thông báo thành công.');
    }
}
