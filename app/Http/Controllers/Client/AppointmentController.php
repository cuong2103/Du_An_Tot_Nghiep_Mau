<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Models\PatientProfile;
use App\Models\Appointment;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // Danh sách lịch hẹn của bệnh nhân
    public function index()
    {
        $appointments = Appointment::with(['doctorProfile.user', 'patientProfile'])
            ->where('booked_by_user_id', Auth::id())
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return view('client.appointments.index', compact('appointments'));
    }

    // Hiển thị form đặt lịch
    public function create(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $date = $request->input('date');
        $time = $request->input('time');

        if (!$doctorId || !$date || !$time) {
            return redirect()->back()->with('error', 'Vui lòng chọn ngày và giờ khám.');
        }

        $doctor = User::with('doctorProfile.specialties')->findOrFail($doctorId);
        $doctorProfileId = $doctor->doctorProfile->id;

        // Lấy danh sách hồ sơ bệnh nhân của user hiện tại
        $patientProfiles = [];
        if (Auth::check()) {
            $patientProfiles = PatientProfile::where('owner_id', Auth::id())->get();
        }

        return view('client.appointments.create', compact('doctor', 'date', 'time', 'patientProfiles'));
    }

    // Xử lý lưu lịch khám
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'patient_profile_id' => 'required|exists:patient_profiles,id',
            'reason' => 'required|string|max:500',
        ]);

        $doctor = User::with('doctorProfile.specialties')->findOrFail($request->input('doctor_id'));
        $doctorProfileId = $doctor->doctorProfile->id;
        $specialtyId = $doctor->doctorProfile->specialties->first()->id ?? null;
        $doctorLevel = $doctor->doctorProfile->level;

        // Lấy room_id từ WorkSchedule
        $dayOfWeek = \Carbon\Carbon::parse($request->input('date'))->dayOfWeek + 1;
        $schedule = \App\Models\WorkSchedule::where('doctor_profile_id', $doctorProfileId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule || !$schedule->room_id) {
            // Thử tìm bất kỳ schedule nào của bác sĩ này nếu lịch hằng ngày bị lỗi
            $fallbackSchedule = \App\Models\WorkSchedule::where('doctor_profile_id', $doctorProfileId)
                ->whereNotNull('room_id')
                ->first();
                
            if (!$fallbackSchedule) {
                return redirect()->back()->with('error', 'Bác sĩ chưa được cấu hình Phòng khám. Vui lòng liên hệ Admin.');
            }
            $roomId = $fallbackSchedule->room_id;
        } else {
            $roomId = $schedule->room_id;
        }

        // Tạo cuộc hẹn
        $appointment = Appointment::create([
            'appointment_code' => 'APT' . strtoupper(uniqid()),
            'booked_by_user_id' => Auth::id(),
            'doctor_profile_id' => $doctorProfileId,
            'specialty_id' => $specialtyId,
            'doctor_level' => $doctorLevel,
            'room_id' => $roomId,
            'patient_profile_id' => $request->input('patient_profile_id'),
            'appointment_date' => $request->input('date'),
            'appointment_time' => $request->input('time'),
            'status' => 'pending',
            'source' => 'web',
            'reason' => $request->input('reason'),
        ]);

        return redirect()->route('client.appointments.success', $appointment->id);
    }

    // Trang báo đặt thành công
    public function success($id)
    {
        $appointment = Appointment::with(['doctorProfile.user', 'patientProfile'])->findOrFail($id);
        
        // Đảm bảo chỉ người tạo mới xem được
        if ($appointment->booked_by_user_id !== Auth::id()) {
            abort(403);
        }

        return view('client.appointments.success', compact('appointment'));
    }

    // Hủy lịch
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        if ($appointment->booked_by_user_id !== Auth::id()) {
            abort(403);
        }

        if ($appointment->status === 'cancelled' || $appointment->status === 'completed') {
            return redirect()->back()->with('error', 'Không thể hủy lịch hẹn này.');
        }

        // Kiểm tra thời gian (chỉ cho hủy trước X tiếng)
        $cancelBeforeHours = SystemSetting::where('key', 'cancel_before_hours')->value('value') ?? 24;
        $appointmentTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
        
        if (now()->diffInHours($appointmentTime, false) < $cancelBeforeHours) {
            return redirect()->back()->with('error', "Bạn chỉ được hủy lịch trước $cancelBeforeHours tiếng.");
        }

        $appointment->update([
            'status' => 'cancelled',
        ]);

        return redirect()->back()->with('success', 'Đã hủy lịch hẹn thành công.');
    }
}
