<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AppointmentService;
use App\Models\Appointment;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use Illuminate\Support\Facades\Auth;
use Exception;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Lock a time slot temporarily while patient fills out the booking form.
     */
    public function lockSlot(Request $request)
    {
        $request->validate([
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        $locked = $this->appointmentService->lockSlot(
            $request->doctor_profile_id,
            $request->appointment_date,
            $request->appointment_time
        );

        if (!$locked) {
            return response()->json([
                'success' => false,
                'message' => 'Khung giờ này đang có người khác đặt. Vui lòng chọn khung giờ khác hoặc thử lại sau.'
            ], 409); // Conflict
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã giữ chỗ khung giờ thành công trong 10 phút. Vui lòng hoàn tất thông tin.'
        ]);
    }

    /**
     * Submit booking and create appointment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'specialty_id' => 'required|exists:specialties,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'patient_profile_id' => 'required|exists:patient_profiles,id',
            'reason' => 'required|string|max:1000',
        ]);

        $patient = PatientProfile::findOrFail($request->patient_profile_id);
        $doctor = DoctorProfile::findOrFail($request->doctor_profile_id);

        // Check No-show rules dynamically
        $userId = Auth::id() ?? $patient->owner_id;
        if ($userId) {
            $absentCount = Appointment::where('booked_by_user_id', $userId)
                ->where('status', 'absent')
                ->where('appointment_date', '>=', now()->subDays(30))
                ->count();

            if ($absentCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản của bạn tạm thời bị giới hạn đặt lịch do đã vắng mặt quá 3 lần trong 30 ngày qua.'
                ], 403);
            }
        }

        try {
            $appointment = $this->appointmentService->createAppointment([
                'doctor_profile_id' => $doctor->id,
                'specialty_id' => $request->specialty_id,
                'patient_profile_id' => $patient->id,
                'booked_by_user_id' => $userId,
                'doctor_level' => $doctor->level,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'reason' => $request->reason,
                'source' => 'web',
                // other optional fields like room_id can be assigned later or auto-assigned
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đặt lịch thành công!',
                'data' => $appointment
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Patient cancels their own appointment.
     */
    public function cancel(Request $request, $id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('booked_by_user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $cancelledAppt = $this->appointmentService->cancelAppointment($appointment, $request->reason);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã hủy lịch hẹn thành công.',
                'data' => $cancelledAppt
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
