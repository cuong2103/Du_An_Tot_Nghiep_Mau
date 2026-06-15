<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DoctorProfile;
use App\Models\Specialty;
use App\Models\Room;
use App\Models\User;
use App\Models\SystemLog;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total'   => DoctorProfile::count(),
            'active'  => DoctorProfile::whereHas('user', fn($q) => $q->where('is_active', true))->count(),
            'locked'  => DoctorProfile::whereHas('user', fn($q) => $q->where('is_active', false))->count(),
            'specialties_count' => Specialty::where('is_active', true)->count(),
        ];

        $query = DoctorProfile::with(['user', 'specialties'])
            ->whereHas('user') // chỉ lấy có user
            ->latest('created_at');

        // Filter search: tên hoặc mã bác sĩ
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('doctor_code', 'like', '%'.$request->search.'%')
                  ->orWhereHas('user', fn($uq) =>
                      $uq->where('full_name', 'like', '%'.$request->search.'%')
                         ->orWhere('phone', 'like', '%'.$request->search.'%')
                  );
            });
        }

        // Filter chuyên khoa
        if ($request->filled('specialty_id')) {
            $query->whereHas('specialties', fn($q) =>
                $q->where('specialties.id', $request->specialty_id)
            );
        }

        // Filter cấp độ
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter trạng thái
        if ($request->filled('status')) {
            $query->whereHas('user', fn($q) =>
                $q->where('is_active', $request->status)
            );
        }

        $doctors = $query->paginate(12)->withQueryString();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        return view('admin.doctors.index', compact('doctors', 'stats', 'specialties'));
    }

    public function create()
    {
        $specialties = Specialty::where('is_active', true)->orderBy('display_order')->get();
        $rooms = Room::where('is_active', true)->orderBy('name')->get();
        return view('admin.doctors.create', compact('specialties', 'rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Tài khoản
            'full_name'       => 'required|string|max:100',
            'phone'           => 'required|string|max:15|unique:users,phone',
            'username'        => 'required|string|max:50|unique:users,username',
            'email'           => 'nullable|email|max:150|unique:users,email',
            'password'        => 'required|string|min:8|confirmed',
            // Hồ sơ chuyên môn
            'doctor_code'     => 'required|string|max:20|unique:doctor_profiles,doctor_code',
            'academic_title'  => 'nullable|string|max:100',
            'level'           => 'required|in:BS,BSCK1,BSCK2,ThS,TS,PGS,GS',
            'expertise'       => 'nullable|string',
            'experience_years'=> 'nullable|integer|min:0|max:60',
            'license_number'  => 'nullable|string|max:50|unique:doctor_profiles,license_number',
            'bio'             => 'nullable|string',
            // Chuyên khoa
            'specialty_ids'         => 'required|array|min:1',
            'specialty_ids.*'       => 'exists:specialties,id',
            'primary_specialty_id'  => 'required|exists:specialties,id',
        ], [
            'full_name.required'      => 'Vui lòng nhập họ tên.',
            'phone.required'          => 'Vui lòng nhập số điện thoại.',
            'phone.unique'            => 'Số điện thoại đã được sử dụng.',
            'username.required'       => 'Vui lòng nhập tên đăng nhập.',
            'username.unique'         => 'Tên đăng nhập đã tồn tại.',
            'password.required'       => 'Vui lòng nhập mật khẩu.',
            'password.min'            => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed'      => 'Xác nhận mật khẩu không khớp.',
            'doctor_code.required'    => 'Vui lòng nhập mã bác sĩ.',
            'doctor_code.unique'      => 'Mã bác sĩ đã tồn tại.',
            'level.required'          => 'Vui lòng chọn cấp độ chuyên môn.',
            'specialty_ids.required'  => 'Vui lòng chọn ít nhất một chuyên khoa.',
            'primary_specialty_id.required' => 'Vui lòng chọn chuyên khoa chính.',
        ]);

        DB::transaction(function() use ($validated) {
            // Tạo User
            $user = User::create([
                'full_name'  => $validated['full_name'],
                'phone'      => $validated['phone'],
                'username'   => $validated['username'],
                'email'      => $validated['email'] ?? null,
                'password'   => bcrypt($validated['password']),
                'role'       => 'doctor',
                'is_active'  => true,
            ]);

            // Tạo DoctorProfile
            $doctor = DoctorProfile::create([
                'user_id'          => $user->id,
                'doctor_code'      => $validated['doctor_code'],
                'academic_title'   => $validated['academic_title'] ?? null,
                'level'            => $validated['level'],
                'expertise'        => $validated['expertise'] ?? null,
                'experience_years' => $validated['experience_years'] ?? null,
                'license_number'   => $validated['license_number'] ?? null,
                'bio'              => $validated['bio'] ?? null,
            ]);

            // Gán chuyên khoa
            $syncData = [];
            foreach ($validated['specialty_ids'] as $specialtyId) {
                $syncData[$specialtyId] = [
                    'is_primary' => ($specialtyId == $validated['primary_specialty_id']) ? 1 : 0
                ];
            }
            $doctor->specialties()->sync($syncData);

            // Ghi log
            SystemLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'DOCTOR_CREATED',
                'module'      => 'doctors',
                'ref_type'    => 'doctor_profiles',
                'ref_id'      => $doctor->id,
                'description' => 'Thêm bác sĩ mới: ' . $validated['full_name'],
                'ip_address'  => request()->ip(),
            ]);
        });

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Thêm bác sĩ thành công.');
    }

    public function show($id)
    {
        $doctor = DoctorProfile::with([
            'user',
            'specialties',
            'workSchedules.room',
        ])->findOrFail($id);

        // Thống kê lịch hẹn
        $appointmentStats = [
            'total'     => Appointment::where('doctor_profile_id', $id)->count(),
            'pending'   => Appointment::where('doctor_profile_id', $id)->where('status', 'pending')->count(),
            'completed' => Appointment::where('doctor_profile_id', $id)->where('status', 'completed')->count(),
            'today'     => Appointment::where('doctor_profile_id', $id)
                            ->whereDate('appointment_date', today())->count(),
        ];

        // 5 lịch hẹn gần nhất
        $recentAppointments = Appointment::with('patientProfile.user')
            ->where('doctor_profile_id', $id)
            ->latest('appointment_date')
            ->limit(5)
            ->get();

        $logs = SystemLog::where('ref_type', 'doctor_profiles')
            ->where('ref_id', $id)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.doctors.show', compact(
            'doctor', 'appointmentStats', 'recentAppointments', 'logs'
        ));
    }

    public function edit($id)
    {
        $doctor = DoctorProfile::with(['user', 'specialties'])->findOrFail($id);
        $specialties = Specialty::where('is_active', true)->orderBy('display_order')->get();
        $rooms = Room::where('is_active', true)->orderBy('name')->get();
        $selectedSpecialtyIds = $doctor->specialties->pluck('id')->toArray();
        $primarySpecialtyId = $doctor->specialties->where('pivot.is_primary', 1)->first()?->id;
        
        return view('admin.doctors.edit', compact(
            'doctor', 'specialties', 'rooms', 'selectedSpecialtyIds', 'primarySpecialtyId'
        ));
    }

    public function update(Request $request, $id)
    {
        $doctor = DoctorProfile::with('user')->findOrFail($id);

        $validated = $request->validate([
            'full_name'       => 'required|string|max:100',
            'phone'           => "required|string|max:15|unique:users,phone,{$doctor->user_id}",
            'username'        => "required|string|max:50|unique:users,username,{$doctor->user_id}",
            'email'           => "nullable|email|unique:users,email,{$doctor->user_id}",
            'password'        => 'nullable|string|min:8|confirmed',
            'doctor_code'     => "required|string|max:20|unique:doctor_profiles,doctor_code,$id",
            'academic_title'  => 'nullable|string|max:100',
            'level'           => 'required|in:BS,BSCK1,BSCK2,ThS,TS,PGS,GS',
            'expertise'       => 'nullable|string',
            'experience_years'=> 'nullable|integer|min:0|max:60',
            'license_number'  => "nullable|string|max:50|unique:doctor_profiles,license_number,$id",
            'bio'             => 'nullable|string',
            'specialty_ids'        => 'required|array|min:1',
            'specialty_ids.*'      => 'exists:specialties,id',
            'primary_specialty_id' => 'required|exists:specialties,id',
        ], [
            'full_name.required'      => 'Vui lòng nhập họ tên.',
            'phone.required'          => 'Vui lòng nhập số điện thoại.',
            'phone.unique'            => 'Số điện thoại đã được sử dụng.',
            'username.required'       => 'Vui lòng nhập tên đăng nhập.',
            'username.unique'         => 'Tên đăng nhập đã tồn tại.',
            'password.min'            => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed'      => 'Xác nhận mật khẩu không khớp.',
            'doctor_code.required'    => 'Vui lòng nhập mã bác sĩ.',
            'doctor_code.unique'      => 'Mã bác sĩ đã tồn tại.',
            'level.required'          => 'Vui lòng chọn cấp độ chuyên môn.',
            'specialty_ids.required'  => 'Vui lòng chọn ít nhất một chuyên khoa.',
            'primary_specialty_id.required' => 'Vui lòng chọn chuyên khoa chính.',
        ]);

        DB::transaction(function() use ($doctor, $validated) {
            // Update User
            $userData = [
                'full_name' => $validated['full_name'],
                'phone'     => $validated['phone'],
                'username'  => $validated['username'],
                'email'     => $validated['email'] ?? null,
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = bcrypt($validated['password']);
            }
            $doctor->user->update($userData);

            // Update DoctorProfile
            $doctor->update([
                'doctor_code'      => $validated['doctor_code'],
                'academic_title'   => $validated['academic_title'] ?? null,
                'level'            => $validated['level'],
                'expertise'        => $validated['expertise'] ?? null,
                'experience_years' => $validated['experience_years'] ?? null,
                'license_number'   => $validated['license_number'] ?? null,
                'bio'              => $validated['bio'] ?? null,
            ]);

            // Sync chuyên khoa
            $syncData = [];
            foreach ($validated['specialty_ids'] as $specialtyId) {
                $syncData[$specialtyId] = [
                    'is_primary' => ($specialtyId == $validated['primary_specialty_id']) ? 1 : 0
                ];
            }
            $doctor->specialties()->sync($syncData);

            SystemLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'DOCTOR_UPDATED',
                'module'      => 'doctors',
                'ref_type'    => 'doctor_profiles',
                'ref_id'      => $doctor->id,
                'description' => 'Cập nhật thông tin bác sĩ: ' . $validated['full_name'],
                'ip_address'  => request()->ip(),
            ]);
        });

        return redirect()->route('admin.doctors.edit', $id)
            ->with('success', 'Cập nhật thông tin bác sĩ thành công.');
    }

    public function toggleActive($id)
    {
        $doctor = DoctorProfile::with('user')->findOrFail($id);
        $doctor->user->update(['is_active' => !$doctor->user->is_active]);

        SystemLog::create([
            'user_id'     => auth()->id(),
            'action'      => $doctor->user->is_active ? 'DOCTOR_UNLOCKED' : 'DOCTOR_LOCKED',
            'module'      => 'doctors',
            'ref_type'    => 'doctor_profiles',
            'ref_id'      => $doctor->id,
            'description' => ($doctor->user->is_active ? 'Mở khoá' : 'Khoá') . ' bác sĩ: ' . $doctor->user->full_name,
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->back()->with('success',
            $doctor->user->is_active ? 'Đã mở khoá tài khoản bác sĩ.' : 'Đã khoá tài khoản bác sĩ.'
        );
    }
}
