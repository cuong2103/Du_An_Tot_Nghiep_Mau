<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DoctorProfile;
use App\Models\Specialty;
use App\Models\Room;
use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = DoctorProfile::with(['user', 'specialties'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('full_name', 'like', "%{$search}%");
                })->orWhere('doctor_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('specialty_id')) {
            $query->whereHas('specialties', function ($q) use ($request) {
                $q->where('specialties.id', $request->specialty_id);
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('user', function ($q) use ($status) {
                $q->where('is_active', $status);
            });
        }

        $doctors = $query->paginate(15)->withQueryString();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        return view('admin.doctors.index', compact('doctors', 'specialties'));
    }

    public function create()
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $rooms = Room::where('is_active', true)->orderBy('name')->get();

        return view('admin.doctors.create', compact('specialties', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8',
            'doctor_code' => 'required|string|max:20|unique:doctor_profiles,doctor_code',
            'academic_title' => 'nullable|string|max:100',
            'level' => 'required|in:BS,BSCK1,BSCK2,ThS,TS,PGS,GS',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'license_number' => 'nullable|string|max:50|unique:doctor_profiles,license_number',
            'bio' => 'nullable|string',
            'expertise' => 'nullable|string',
            'specialty_ids' => 'required|array|min:1',
            'specialty_ids.*' => 'exists:specialties,id',
            'primary_specialty_id' => 'required|exists:specialties,id',
        ], [
            'required' => 'Vui lòng nhập/chọn trường này.',
            'unique' => 'Dữ liệu đã tồn tại trong hệ thống.',
            'min' => 'Giá trị quá ngắn/nhỏ.',
            'max' => 'Giá trị quá dài/lớn.',
            'email' => 'Email không hợp lệ.',
            'in' => 'Giá trị không hợp lệ.',
            'exists' => 'Dữ liệu không tồn tại.',
        ]);

        if (!in_array($request->primary_specialty_id, $request->specialty_ids)) {
            return back()->withInput()->withErrors(['primary_specialty_id' => 'Chuyên khoa chính phải nằm trong danh sách chuyên khoa đã chọn.']);
        }

        DB::transaction(function () use ($request) {
            $user = User::create([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'username' => 'bs_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', \Illuminate\Support\Str::ascii($request->full_name))) . '_' . time(),
                'password' => Hash::make($request->password),
                'role' => 'doctor',
                'is_active' => true,
            ]);

            $doctorProfile = DoctorProfile::create([
                'user_id' => $user->id,
                'doctor_code' => $request->doctor_code,
                'academic_title' => $request->academic_title,
                'level' => $request->level,
                'experience_years' => $request->experience_years,
                'license_number' => $request->license_number,
                'bio' => $request->bio,
                'expertise' => $request->expertise,
            ]);

            $syncData = [];
            foreach ($request->specialty_ids as $id) {
                $syncData[$id] = ['is_primary' => $id == $request->primary_specialty_id];
            }
            $doctorProfile->specialties()->sync($syncData);

            SystemLog::create([
                'user_id' => Auth::id(),
                'action' => 'DOCTOR_CREATED',
                'module' => 'doctor_management',
                'ref_type' => 'doctor_profile',
                'ref_id' => $doctorProfile->id,
                'description' => 'Thêm bác sĩ mới: ' . $user->full_name,
                'ip_address' => request()->ip()
            ]);
        });

        return redirect()->route('admin.doctors.index')->with('success', 'Thêm bác sĩ thành công.');
    }

    public function edit($id)
    {
        $doctor = DoctorProfile::with(['user', 'specialties'])->findOrFail($id);
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.doctors.edit', compact('doctor', 'specialties'));
    }

    public function update(Request $request, $id)
    {
        $doctor = DoctorProfile::with('user')->findOrFail($id);
        $user = $doctor->user;

        $request->validate([
            'full_name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'doctor_code' => ['required', 'string', 'max:20', Rule::unique('doctor_profiles')->ignore($doctor->id)],
            'academic_title' => 'nullable|string|max:100',
            'level' => 'required|in:BS,BSCK1,BSCK2,ThS,TS,PGS,GS',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'license_number' => ['nullable', 'string', 'max:50', Rule::unique('doctor_profiles')->ignore($doctor->id)],
            'bio' => 'nullable|string',
            'expertise' => 'nullable|string',
            'specialty_ids' => 'required|array|min:1',
            'specialty_ids.*' => 'exists:specialties,id',
            'primary_specialty_id' => 'required|exists:specialties,id',
        ], [
            'required' => 'Vui lòng nhập/chọn trường này.',
            'unique' => 'Dữ liệu đã tồn tại trong hệ thống.',
            'min' => 'Giá trị quá ngắn/nhỏ.',
            'max' => 'Giá trị quá dài/lớn.',
            'email' => 'Email không hợp lệ.',
            'in' => 'Giá trị không hợp lệ.',
            'exists' => 'Dữ liệu không tồn tại.',
        ]);

        if (!in_array($request->primary_specialty_id, $request->specialty_ids)) {
            return back()->withInput()->withErrors(['primary_specialty_id' => 'Chuyên khoa chính phải nằm trong danh sách chuyên khoa đã chọn.']);
        }

        DB::transaction(function () use ($request, $doctor, $user) {
            $user->full_name = $request->full_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $doctor->update([
                'doctor_code' => $request->doctor_code,
                'academic_title' => $request->academic_title,
                'level' => $request->level,
                'experience_years' => $request->experience_years,
                'license_number' => $request->license_number,
                'bio' => $request->bio,
                'expertise' => $request->expertise,
            ]);

            $syncData = [];
            foreach ($request->specialty_ids as $id) {
                $syncData[$id] = ['is_primary' => $id == $request->primary_specialty_id];
            }
            $doctor->specialties()->sync($syncData);

            SystemLog::create([
                'user_id' => Auth::id(),
                'action' => 'DOCTOR_UPDATED',
                'module' => 'doctor_management',
                'ref_type' => 'doctor_profile',
                'ref_id' => $doctor->id,
                'description' => 'Cập nhật bác sĩ: ' . $user->full_name,
                'ip_address' => request()->ip()
            ]);
        });

        return redirect()->route('admin.doctors.edit', $doctor->id)->with('success', 'Cập nhật bác sĩ thành công.');
    }

    public function destroy($id)
    {
        $doctor = DoctorProfile::findOrFail($id);

        $hasActiveAppointments = \App\Models\Appointment::where('doctor_profile_id', $doctor->id)
            ->whereIn('status', ['pending', 'checked_in', 'examining'])
            ->exists();

        if ($hasActiveAppointments) {
            return back()->with('error', 'Không thể xoá bác sĩ này vì đang có lịch hẹn chờ khám hoặc đang khám.');
        }

        DB::transaction(function () use ($doctor) {
            $user = $doctor->user;
            $user->is_active = false;
            $user->save();

            SystemLog::create([
                'user_id' => Auth::id(),
                'action' => 'DOCTOR_DEACTIVATED',
                'module' => 'doctor_management',
                'ref_type' => 'doctor_profile',
                'ref_id' => $doctor->id,
                'description' => 'Khoá bác sĩ: ' . $user->full_name,
                'ip_address' => request()->ip()
            ]);
        });

        return redirect()->route('admin.doctors.index')->with('success', 'Đã khoá/xoá bác sĩ thành công.');
    }
}
