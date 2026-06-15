<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffProfile;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ReceptionistController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total'  => User::where('role', 'receptionist')->count(),
            'active' => User::where('role', 'receptionist')->where('is_active', true)->count(),
            'locked' => User::where('role', 'receptionist')->where('is_active', false)->count(),
        ];

        $query = User::with('staffProfile')
            ->where('role', 'receptionist')
            ->latest('created_at');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%'.$request->search.'%')
                  ->orWhere('phone', 'like', '%'.$request->search.'%')
                  ->orWhereHas('staffProfile', fn($sq) =>
                      $sq->where('employee_code', 'like', '%'.$request->search.'%')
                  );
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('department')) {
            $query->whereHas('staffProfile', fn($sq) =>
                $sq->where('department', 'like', '%'.$request->department.'%')
            );
        }

        $receptionists = $query->paginate(15)->withQueryString();

        return view('admin.receptionists.index', compact('receptionists', 'stats'));
    }

    public function create()
    {
        return view('admin.receptionists.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'       => 'required|string|max:100',
            'phone'           => 'required|string|max:15|unique:users,phone',
            'username'        => 'required|string|max:50|unique:users,username',
            'email'           => 'nullable|email|max:150|unique:users,email',
            'password'        => 'required|string|min:8|confirmed',
            'employee_code'   => 'required|string|max:20|unique:staff_profiles,employee_code',
            'position'        => 'required|string|max:100',
            'department'      => 'nullable|string|max:150',
            'internal_phone'  => 'nullable|string|max:15',
            'start_date'      => 'nullable|date',
        ], [
            'required' => 'Trường :attribute không được để trống.',
            'unique'   => ':attribute đã tồn tại trong hệ thống.',
            'max'      => ':attribute không được vượt quá :max ký tự.',
            'min'      => ':attribute phải có ít nhất :min ký tự.',
            'confirmed'=> 'Xác nhận mật khẩu không khớp.',
            'email'    => 'Email không đúng định dạng.',
        ], [
            'full_name' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'username' => 'Tên đăng nhập',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'employee_code' => 'Mã nhân viên',
            'position' => 'Chức vụ',
            'department' => 'Phòng ban',
            'internal_phone' => 'SĐT nội bộ',
            'start_date' => 'Ngày vào làm',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
                'username'  => $request->username,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'receptionist',
                'is_active' => true,
            ]);

            StaffProfile::create([
                'user_id'        => $user->id,
                'employee_code'  => $request->employee_code,
                'position'       => $request->position,
                'department'     => $request->department,
                'internal_phone' => $request->internal_phone,
                'start_date'     => $request->start_date,
            ]);

            SystemLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'RECEPTIONIST_CREATED',
                'module'      => 'receptionists',
                'ref_type'    => 'users',
                'ref_id'      => $user->id,
                'description' => 'Thêm mới lễ tân: ' . $user->full_name,
                'ip_address'  => request()->ip(),
            ]);
        });

        return redirect()->route('admin.receptionists.index')->with('success', 'Đã thêm lễ tân mới thành công.');
    }

    public function edit($id)
    {
        $receptionist = User::with('staffProfile')
            ->where('role', 'receptionist')
            ->findOrFail($id);
            
        return view('admin.receptionists.edit', compact('receptionist'));
    }

    public function update(Request $request, $id)
    {
        $receptionist = User::with('staffProfile')->where('role', 'receptionist')->findOrFail($id);
        $staffProfileId = $receptionist->staffProfile->id ?? null;

        $request->validate([
            'full_name'       => 'required|string|max:100',
            'phone'           => ["required", "string", "max:15", Rule::unique('users', 'phone')->ignore($id)],
            'username'        => ["required", "string", "max:50", Rule::unique('users', 'username')->ignore($id)],
            'email'           => ["nullable", "email", "max:150", Rule::unique('users', 'email')->ignore($id)],
            'password'        => 'nullable|string|min:8|confirmed',
            'employee_code'   => ["required", "string", "max:20", Rule::unique('staff_profiles', 'employee_code')->ignore($staffProfileId)],
            'position'        => 'required|string|max:100',
            'department'      => 'nullable|string|max:150',
            'internal_phone'  => 'nullable|string|max:15',
            'start_date'      => 'nullable|date',
        ], [
            'required' => 'Trường :attribute không được để trống.',
            'unique'   => ':attribute đã tồn tại trong hệ thống.',
            'max'      => ':attribute không được vượt quá :max ký tự.',
            'min'      => ':attribute phải có ít nhất :min ký tự.',
            'confirmed'=> 'Xác nhận mật khẩu không khớp.',
            'email'    => 'Email không đúng định dạng.',
        ], [
            'full_name' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'username' => 'Tên đăng nhập',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'employee_code' => 'Mã nhân viên',
            'position' => 'Chức vụ',
            'department' => 'Phòng ban',
            'internal_phone' => 'SĐT nội bộ',
            'start_date' => 'Ngày vào làm',
        ]);

        DB::transaction(function () use ($request, $receptionist) {
            $userData = [
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
                'username'  => $request->username,
                'email'     => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $receptionist->update($userData);

            StaffProfile::updateOrCreate(
                ['user_id' => $receptionist->id],
                [
                    'employee_code'  => $request->employee_code,
                    'position'       => $request->position,
                    'department'     => $request->department,
                    'internal_phone' => $request->internal_phone,
                    'start_date'     => $request->start_date,
                ]
            );

            SystemLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'RECEPTIONIST_UPDATED',
                'module'      => 'receptionists',
                'ref_type'    => 'users',
                'ref_id'      => $receptionist->id,
                'description' => 'Cập nhật thông tin lễ tân: ' . $receptionist->full_name,
                'ip_address'  => request()->ip(),
            ]);
        });

        return redirect()->route('admin.receptionists.edit', $receptionist->id)->with('success', 'Đã cập nhật thông tin lễ tân thành công.');
    }

    public function toggleActive($id)
    {
        if ($id == auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không thể khoá tài khoản của chính mình.');
        }

        $receptionist = User::where('role', 'receptionist')->findOrFail($id);
        $receptionist->update(['is_active' => !$receptionist->is_active]);

        SystemLog::create([
            'user_id'     => auth()->id(),
            'action'      => $receptionist->is_active ? 'RECEPTIONIST_UNLOCKED' : 'RECEPTIONIST_LOCKED',
            'module'      => 'receptionists',
            'ref_type'    => 'users',
            'ref_id'      => $receptionist->id,
            'description' => ($receptionist->is_active ? 'Mở khoá' : 'Khoá') . ' lễ tân: ' . $receptionist->full_name,
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->back()->with('success',
            $receptionist->is_active ? 'Đã mở khoá tài khoản lễ tân.' : 'Đã khoá tài khoản lễ tân.'
        );
    }
}
