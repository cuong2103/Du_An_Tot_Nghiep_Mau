<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use App\Services\WorkScheduleService;

class DoctorController extends Controller
{
    protected $workScheduleService;

    public function __construct(WorkScheduleService $workScheduleService)
    {
        $this->workScheduleService = $workScheduleService;
    }

    public function show($id)
    {
        $doctor = User::where('role', 'doctor')
            ->where('is_active', true)
            ->with(['doctorProfile.specialties'])
            ->findOrFail($id);

        if (!$doctor->doctorProfile) {
            abort(404, 'Bác sĩ chưa có hồ sơ.');
        }

        $doctorProfileId = $doctor->doctorProfile->id;
        $groupedSchedules = [];
        
        // Lấy lịch 14 ngày tới
        for ($i = 0; $i < 14; $i++) {
            $date = Carbon::today()->addDays($i)->format('Y-m-d');
            $slots = $this->workScheduleService->getAvailableSlots($doctorProfileId, $date);
            
            if (!empty($slots)) {
                $groupedSchedules[$date] = $slots; // slots is an array of 'H:i' strings
            }
        }

        return view('client.doctors.show', compact('doctor', 'groupedSchedules'));
    }
}
