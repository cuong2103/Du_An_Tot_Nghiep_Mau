<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\ScheduleOverride;
use App\Models\WorkSchedule;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingService
{
    /**
     * Lấy danh sách slot available cho bác sĩ theo ngày
     */
    public function getAvailableSlots(int $doctorProfileId, string $date): array
    {
        $carbon = Carbon::parse($date);
        // Carbon ISO: 1=Mon,7=Sun → DB: 1=Sun,2=Mon,...,7=Sat
        $dbDayOfWeek = $carbon->dayOfWeek === 0 ? 1 : $carbon->dayOfWeek + 1;

        // Kiểm tra override
        $override = ScheduleOverride::where('doctor_profile_id', $doctorProfileId)
            ->whereDate('override_date', $date)
            ->first();

        if ($override && $override->type === 'close') {
            return [];
        }

        // Lấy work_schedule
        $schedule = WorkSchedule::where('doctor_profile_id', $doctorProfileId)
            ->where('day_of_week', $dbDayOfWeek)
            ->where('is_active', true)
            ->first();

        // Nếu có override extra thì dùng giờ override
        if ($override && $override->type === 'extra') {
            $startTime = $override->start_time;
            $endTime = $override->end_time;
            $slotDuration = $schedule?->slot_duration_minutes ?? 15;
            $maxSlots = $schedule?->max_slots ?? 30;
        } elseif ($schedule) {
            $startTime = $schedule->start_time;
            $endTime = $schedule->end_time;
            $slotDuration = $schedule->slot_duration_minutes;
            $maxSlots = $schedule->max_slots;
        } else {
            return []; // Không có lịch ngày này
        }

        // Generate slots
        $slots = [];
        $current = Carbon::parse($date . ' ' . $startTime);
        $end = Carbon::parse($date . ' ' . $endTime);
        $now = now();

        while ($current->lt($end) && count($slots) < $maxSlots) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($slotDuration);
        }

        // Lấy appointments đã đặt trong ngày
        $bookedSlots = Appointment::where('doctor_profile_id', $doctorProfileId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'absent'])
            ->pluck('appointment_time')
            ->map(fn($t) => substr($t, 0, 5))
            ->toArray();

        // Build result
        $result = [];
        foreach ($slots as $slot) {
            $slotDateTime = Carbon::parse($date . ' ' . $slot);
            $result[] = [
                'time'      => $slot,
                'available' => !in_array($slot, $bookedSlots) && $slotDateTime->gt($now),
            ];
        }

        return $result;
    }

    /**
     * Tạo mã lịch hẹn unique
     */
    public function generateAppointmentCode(string $date): string
    {
        $dateStr = Carbon::parse($date)->format('Ymd');
        $prefix = 'APT' . $dateStr;

        $lastCode = Appointment::where('appointment_code', 'like', $prefix . '%')
            ->orderBy('appointment_code', 'desc')
            ->value('appointment_code');

        $sequence = $lastCode ? (int)substr($lastCode, -4) + 1 : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Tạo lịch hẹn
     */
    public function createAppointment(array $data, User $bookedBy): Appointment
    {
        // Double-check slot còn trống
        $slots = $this->getAvailableSlots($data['doctor_profile_id'], $data['appointment_date']);
        $availableSlot = collect($slots)->firstWhere('time', substr($data['appointment_time'], 0, 5));

        if (!$availableSlot || !$availableSlot['available']) {
            throw new Exception('Slot giờ này đã hết. Vui lòng chọn giờ khác.');
        }

        return DB::transaction(function() use ($data, $bookedBy) {
            // Lấy room_id từ work_schedule
            $dbDayOfWeek = Carbon::parse($data['appointment_date'])->dayOfWeek === 0
                ? 1
                : Carbon::parse($data['appointment_date'])->dayOfWeek + 1;

            $schedule = WorkSchedule::where('doctor_profile_id', $data['doctor_profile_id'])
                ->where('day_of_week', $dbDayOfWeek)
                ->where('is_active', true)
                ->first();

            $appointment = Appointment::create([
                'appointment_code'   => $this->generateAppointmentCode($data['appointment_date']),
                'patient_profile_id' => $data['patient_profile_id'],
                'booked_by_user_id'  => $bookedBy->id,
                'specialty_id'       => $data['specialty_id'],
                'doctor_profile_id'  => $data['doctor_profile_id'],
                'room_id'            => $schedule?->room_id ?? $data['room_id'] ?? 1,
                'appointment_date'   => $data['appointment_date'],
                'appointment_time'   => $data['appointment_time'] . ':00',
                'reason'             => $data['reason'],
                'status'             => 'pending',
                'source'             => 'web',
            ]);

            // Ghi log
            AppointmentLog::create([
                'appointment_id' => $appointment->id,
                'changed_by'     => $bookedBy->id,
                'old_status'     => null,
                'new_status'     => 'pending',
                'action'         => 'APPOINTMENT_CREATED',
                'reason'         => 'Bệnh nhân đặt lịch qua website',
            ]);

            // Tự động tạo thông báo cho bệnh nhân và bác sĩ
            $appointment->load(['patientProfile.user', 'doctor.user']);
            $patientUserId = $appointment->patientProfile->user_id ?? $bookedBy->id;
            $patientUser = User::find($patientUserId);
            
            $aptDateStr = Carbon::parse($appointment->appointment_date)->format('d/m/Y');
            $aptTimeStr = substr($appointment->appointment_time, 0, 5);
            
            $patientTitle = "Đặt lịch hẹn thành công";
            $patientContent = "Lịch hẹn mã {$appointment->appointment_code} của bạn đã được xác nhận vào ngày {$aptDateStr} lúc {$aptTimeStr}.";

            // Thông báo Web cho bệnh nhân
            \App\Models\Notification::create([
                'user_id' => $patientUserId,
                'title' => $patientTitle,
                'content' => $patientContent,
                'type' => 'appointment',
                'channel' => 'in_web',
                'is_sent' => false,
                'ref_type' => 'appointment',
                'ref_id' => $appointment->id,
            ]);

            // Thông báo Email cho bệnh nhân
            if ($patientUser && $patientUser->email) {
                \App\Models\Notification::create([
                    'user_id' => $patientUserId,
                    'title' => $patientTitle,
                    'content' => $patientContent,
                    'type' => 'appointment',
                    'channel' => 'email',
                    'is_sent' => false,
                    'ref_type' => 'appointment',
                    'ref_id' => $appointment->id,
                ]);
            }

            // Thông báo cho Bác sĩ
            $doctorUser = $appointment->doctor->user ?? null;
            if ($doctorUser) {
                $patientName = $appointment->patientProfile->full_name ?? '—';
                $doctorTitle = "Có lịch hẹn mới";
                $doctorContent = "Bạn có lịch hẹn mới mã {$appointment->appointment_code} với bệnh nhân {$patientName} vào ngày {$aptDateStr} lúc {$aptTimeStr}.";
                
                \App\Models\Notification::create([
                    'user_id' => $doctorUser->id,
                    'title' => $doctorTitle,
                    'content' => $doctorContent,
                    'type' => 'appointment',
                    'channel' => 'in_web',
                    'is_sent' => false,
                    'ref_type' => 'appointment',
                    'ref_id' => $appointment->id,
                ]);
                
                if ($doctorUser->email) {
                    \App\Models\Notification::create([
                        'user_id' => $doctorUser->id,
                        'title' => $doctorTitle,
                        'content' => $doctorContent,
                        'type' => 'appointment',
                        'channel' => 'email',
                        'is_sent' => false,
                        'ref_type' => 'appointment',
                        'ref_id' => $appointment->id,
                    ]);
                }
            }

            return $appointment;
        });
    }
}
