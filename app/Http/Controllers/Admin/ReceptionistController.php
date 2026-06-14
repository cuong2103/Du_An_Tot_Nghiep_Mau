<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StaffProfile;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class ReceptionistController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'receptionist')->with('staffProfile')->latest();

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

        $receptionists = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::where('role', 'receptionist')->count(),
            'active' => User::where('role', 'receptionist')->where('is_active', true)->count(),
        ];

        return view('admin.receptionists.index', compact('receptionists', 'stats'));
    }

    public function create()
    {
        return view('admin.receptionists.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15|unique:users,phone',
            'email' => 'nullable|email|max:100|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'position' => 'required|string|max:100',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'username' => $request->username,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'receptionist',
            'is_active' => $request->has('is_active'),
        ]);

        StaffProfile::create([
            'user_id' => $user->id,
            'employee_code' => 'LT' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
            'position' => $request->position ?? 'Lễ tân',
            'department' => $request->department,
            'internal_phone' => $request->internal_phone,
            'start_date' => $request->start_date,
        ]);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'RECEPTIONIST_CREATED',
            'module' => 'user_management',
            'description' => 'Thêm lễ tân: ' . $user->full_name,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.receptionists.index')->with('success', 'Thêm lễ tân thành công.');
    }

    public function show($id)
    {
        $user = User::where('role', 'receptionist')->with(['staffProfile'])->findOrFail($id);

        $systemLogs = SystemLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.receptionists.show', compact('user', 'systemLogs'));
    }

    public function toggleActive($id)
    {
        $user = User::where('role', 'receptionist')->findOrFail($id);

        $user->is_active = !$user->is_active;
        $user->save();

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => $user->is_active ? 'RECEPTIONIST_UNLOCKED' : 'RECEPTIONIST_LOCKED',
            'module' => 'user_management',
            'ref_type' => 'user',
            'ref_id' => $user->id,
            'description' => ($user->is_active ? 'Mở khoá' : 'Khoá') . ' lễ tân: ' . $user->full_name,
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', 'Đã ' . ($user->is_active ? 'mở khoá' : 'khoá') . ' tài khoản thành công.');
    }
}
