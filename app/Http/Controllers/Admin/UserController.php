<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'patient')->with(['patientProfiles'])->latest();

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::where('role', 'patient')->count(),
            'active' => User::where('role', 'patient')->where('is_active', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show($id)
    {
        $user = User::where('role', 'patient')->with(['patientProfiles'])->findOrFail($id);

        $systemLogs = SystemLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.users.show', compact('user', 'systemLogs'));
    }

    public function toggleActive($id)
    {
        $user = User::where('role', 'patient')->findOrFail($id);

        $user->is_active = !$user->is_active;
        $user->save();

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => $user->is_active ? 'USER_UNLOCKED' : 'USER_LOCKED',
            'module' => 'user_management',
            'ref_type' => 'user',
            'ref_id' => $user->id,
            'description' => ($user->is_active ? 'Mở khoá' : 'Khoá') . ' bệnh nhân: ' . $user->full_name,
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', 'Đã ' . ($user->is_active ? 'mở khoá' : 'khoá') . ' tài khoản bệnh nhân thành công.');
    }
}
