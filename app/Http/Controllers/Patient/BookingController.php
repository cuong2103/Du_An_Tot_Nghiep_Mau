<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Models\DoctorProfile;
use App\Services\BookingService;
use App\Models\PatientProfile;
use App\Models\Appointment;
use Exception;

class BookingController extends Controller
{
    public function index()
    {
        $specialties = Specialty::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // Lấy specialty_id từ query string nếu có (deep link)
        $selectedSpecialtyId = request('specialty_id');
        $selectedDoctorId = request('doctor_id');

        return view('patient.booking.index', compact(
            'specialties', 'selectedSpecialtyId', 'selectedDoctorId'
        ));
    }

    public function getDoctors(Request $request)
    {
        $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
        ]);

        $doctors = DoctorProfile::with(['user', 'specialties', 'workSchedules'])
            ->whereHas('specialties', fn($q) =>
                $q->where('specialties.id', $request->specialty_id)
            )
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->get()
            ->map(fn($doctor) => [
                'id'               => $doctor->id,
                'full_title'       => $doctor->full_title,
                'academic_title'   => $doctor->academic_title,
                'level'            => $doctor->level,
                'experience_years' => $doctor->experience_years,
                'expertise'        => $doctor->expertise,
                'bio'              => $doctor->bio,
                'work_days'        => $doctor->workSchedules
                    ->where('is_active', true)
                    ->pluck('day_of_week')
                    ->toArray(),
            ]);

        return response()->json(['doctors' => $doctors]);
    }

    public function getSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctor_profiles,id',
            'date'      => 'required|date|after_or_equal:today',
        ]);

        $bookingService = new BookingService();
        $slots = $bookingService->getAvailableSlots($request->doctor_id, $request->date);

        return response()->json(['slots' => $slots]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'specialty_id'       => 'required|exists:specialties,id',
            'doctor_profile_id'  => 'required|exists:doctor_profiles,id',
            'patient_profile_id' => 'required|exists:patient_profiles,id',
            'appointment_date'   => 'required|date|after_or_equal:today',
            'appointment_time'   => 'required',
            'reason'             => 'required|string|min:10|max:1000',
        ], [
            'specialty_id.required'       => 'Vui lòng chọn chuyên khoa.',
            'doctor_profile_id.required'  => 'Vui lòng chọn bác sĩ.',
            'patient_profile_id.required' => 'Vui lòng chọn hồ sơ bệnh nhân.',
            'appointment_date.required'   => 'Vui lòng chọn ngày khám.',
            'appointment_date.after_or_equal' => 'Ngày khám phải từ hôm nay trở đi.',
            'appointment_time.required'   => 'Vui lòng chọn giờ khám.',
            'reason.required'             => 'Vui lòng nhập lý do khám.',
            'reason.min'                  => 'Lý do khám tối thiểu 10 ký tự.',
        ]);

        // Kiểm tra patient_profile thuộc về user đang đăng nhập
        $profile = PatientProfile::where('id', $request->patient_profile_id)
            ->where('owner_id', auth()->id())
            ->firstOrFail();

        try {
            $bookingService = new BookingService();
            $appointment = $bookingService->createAppointment(
                $request->only([
                    'specialty_id', 'doctor_profile_id', 'patient_profile_id',
                    'appointment_date', 'appointment_time', 'reason'
                ]),
                auth()->user()
            );

            return redirect()->route('booking.success', $appointment->appointment_code)
                ->with('success', 'Đặt lịch khám thành công!');

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function success(string $code)
    {
        $appointment = Appointment::with([
            'patientProfile',
            'doctorProfile.user',
            'specialty',
            'room',
        ])->where('appointment_code', $code)
          ->where('booked_by_user_id', auth()->id())
          ->firstOrFail();

        return view('patient.booking.success', compact('appointment'));
    }
}
