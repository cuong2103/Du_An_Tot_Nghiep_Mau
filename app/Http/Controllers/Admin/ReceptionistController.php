<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StaffProfile;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;

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
                         ->orWhere('position', 'like', '%'.$request->search.'%')
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

        // Lấy danh sách phòng ban distinct để filter
        $departments = \App\Models\StaffProfile::whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.receptionists.index', compact('receptionists', 'stats', 'departments'));
    }

    public function create()
    {
        return view('admin.receptionists.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:100',
            'phone'          => 'required|string|max:15|unique:users,phone',
            'username'       => 'required|string|max:50|unique:users,username',
            'id_card'        => 'nullable|string|max:20|unique:users,id_card',
            'email'          => 'nullable|email|max:150|unique:users,email',
            'password'       => 'required|string|min:8|confirmed',
            'employee_code'  => 'required|string|max:20|unique:staff_profiles,employee_code',
            'position'       => 'required|string|max:100',
            'department'     => 'nullable|string|max:150',
            'internal_phone' => 'nullable|string|max:15',
            'start_date'     => 'nullable|date|before_or_equal:today',
        ], [
            'full_name.required'     => 'Vui lòng nhập họ tên.',
            'phone.required'         => 'Vui lòng nhập số điện thoại.',
            'phone.unique'           => 'Số điện thoại đã được sử dụng.',
            'username.required'      => 'Vui lòng nhập tên đăng nhập.',
            'username.unique'        => 'Tên đăng nhập đã tồn tại.',
            'id_card.unique'         => 'Số CCCD đã được sử dụng.',
            'email.unique'           => 'Email đã được sử dụng.',
            'password.required'      => 'Vui lòng nhập mật khẩu.',
            'password.min'           => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed'     => 'Xác nhận mật khẩu không khớp.',
            'employee_code.required' => 'Vui lòng nhập mã nhân viên.',
            'employee_code.unique'   => 'Mã nhân viên đã tồn tại.',
            'position.required'      => 'Vui lòng nhập chức vụ.',
            'start_date.before_or_equal' => 'Ngày vào làm không được là ngày trong tương lai.',
        ]);

        DB::transaction(function() use ($validated) {
            $user = User::create([
                'full_name'  => $validated['full_name'],
                'phone'      => $validated['phone'],
                'username'   => $validated['username'],
                'id_card'    => $validated['id_card'] ?? null,
                'email'      => $validated['email'] ?? null,
                'password'   => bcrypt($validated['password']),
                'role'       => 'receptionist',
                'is_active'  => true,
            ]);

            StaffProfile::create([
                'user_id'        => $user->id,
                'employee_code'  => $validated['employee_code'],
                'position'       => $validated['position'],
                'department'     => $validated['department'] ?? null,
                'internal_phone' => $validated['internal_phone'] ?? null,
                'start_date'     => $validated['start_date'] ?? null,
                'is_active'      => true,
            ]);

            SystemLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'RECEPTIONIST_CREATED',
                'module'      => 'receptionists',
                'ref_type'    => 'users',
                'ref_id'      => $user->id,
                'description' => 'Thêm lễ tân mới: ' . $validated['full_name'],
                'ip_address'  => request()->ip(),
            ]);
        });

        return redirect()->route('admin.receptionists.index')
            ->with('success', 'Thêm lễ tân thành công.');
    }

    public function show($id)
    {
        $receptionist = User::with('staffProfile')
            ->where('role', 'receptionist')
            ->findOrFail($id);

        // Thống kê check-in hôm nay và tháng này
        $checkInStats = [
            'today' => \App\Models\Appointment::where('measured_by', $id)
                        ->whereDate('checked_in_at', today())->count(),
            'month' => \App\Models\Appointment::where('measured_by', $id)
                        ->whereMonth('checked_in_at', now()->month)
                        ->whereYear('checked_in_at', now()->year)->count(),
            'total' => \App\Models\Appointment::where('measured_by', $id)->count(),
        ];

        $logs = SystemLog::where('user_id', $id)
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.receptionists.show', compact('receptionist', 'checkInStats', 'logs'));
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
        $receptionist = User::with('staffProfile')
            ->where('role', 'receptionist')
            ->findOrFail($id);

        $staffProfileId = $receptionist->staffProfile?->id;

        $validated = $request->validate([
            'full_name'      => 'required|string|max:100',
            'phone'          => "required|string|max:15|unique:users,phone,$id",
            'username'       => "required|string|max:50|unique:users,username,$id",
            'id_card'        => "nullable|string|max:20|unique:users,id_card,$id",
            'email'          => "nullable|email|max:150|unique:users,email,$id",
            'password'       => 'nullable|string|min:8|confirmed',
            'employee_code'  => "required|string|max:20|unique:staff_profiles,employee_code,$staffProfileId",
            'position'       => 'required|string|max:100',
            'department'     => 'nullable|string|max:150',
            'internal_phone' => 'nullable|string|max:15',
            'start_date'     => 'nullable|date|before_or_equal:today',
        ], [
            'full_name.required'     => 'Vui lòng nhập họ tên.',
            'phone.required'         => 'Vui lòng nhập số điện thoại.',
            'phone.unique'           => 'Số điện thoại đã được sử dụng.',
            'username.required'      => 'Vui lòng nhập tên đăng nhập.',
            'username.unique'        => 'Tên đăng nhập đã tồn tại.',
            'id_card.unique'         => 'Số CCCD đã được sử dụng.',
            'email.unique'           => 'Email đã được sử dụng.',
            'password.min'           => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed'     => 'Xác nhận mật khẩu không khớp.',
            'employee_code.required' => 'Vui lòng nhập mã nhân viên.',
            'employee_code.unique'   => 'Mã nhân viên đã tồn tại.',
            'position.required'      => 'Vui lòng nhập chức vụ.',
            'start_date.before_or_equal' => 'Ngày vào làm không được là ngày trong tương lai.',
        ]);

        DB::transaction(function() use ($receptionist, $validated) {
            $userData = [
                'full_name' => $validated['full_name'],
                'phone'     => $validated['phone'],
                'username'  => $validated['username'],
                'id_card'   => $validated['id_card'] ?? null,
                'email'     => $validated['email'] ?? null,
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = bcrypt($validated['password']);
            }
            $receptionist->update($userData);

            $receptionist->staffProfile()->updateOrCreate(
                ['user_id' => $receptionist->id],
                [
                    'employee_code'  => $validated['employee_code'],
                    'position'       => $validated['position'],
                    'department'     => $validated['department'] ?? null,
                    'internal_phone' => $validated['internal_phone'] ?? null,
                    'start_date'     => $validated['start_date'] ?? null,
                ]
            );

            SystemLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'RECEPTIONIST_UPDATED',
                'module'      => 'receptionists',
                'ref_type'    => 'users',
                'ref_id'      => $receptionist->id,
                'description' => 'Cập nhật thông tin lễ tân: ' . $validated['full_name'],
                'ip_address'  => request()->ip(),
            ]);
        });

        return redirect()->route('admin.receptionists.edit', $id)
            ->with('success', 'Cập nhật thông tin lễ tân thành công.');
    }

    public function toggleActive($id)
    {
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
