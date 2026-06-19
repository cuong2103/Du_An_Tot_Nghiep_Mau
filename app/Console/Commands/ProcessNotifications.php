<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Notification;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ProcessNotifications extends Command
{
    protected $signature = 'notifications:process';
    protected $description = 'Process scheduled notifications and appointment reminders';

    public function handle()
    {
        $this->info('Bắt đầu quét và tạo thông báo nhắc nhở lịch hẹn...');
        $this->processAppointmentReminders();
        $this->info('Hoàn tất quét lịch hẹn.');
        
        // ĐÃ XÓA: $this->sendPendingNotifications();
        // Lý do: Việc gửi email (bao gồm cả email hẹn giờ) giờ đây đã được xử lý tối ưu
        // thông qua Queue trong file ProcessScheduledNotifications.php và SendEmailNotificationJob.
    }

    private function processAppointmentReminders()
    {
        // Get appointments happening today
        $appointments = Appointment::whereDate('appointment_date', Carbon::today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->get();

        foreach ($appointments as $appointment) {
            if (!$appointment->appointment_time) continue;

            $appointmentDateTime = Carbon::parse($appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->appointment_time);
            $minutesUntil = now()->diffInMinutes($appointmentDateTime, false);

            if ($minutesUntil > 0 && $minutesUntil <= 60) {
                // Check if 1 hour reminder was sent
                $has1HourReminder = Notification::where('ref_type', 'appointment')
                    ->where('ref_id', $appointment->id)
                    ->where('type', 'reminder')
                    ->where('title', 'like', '%1 tiếng%')
                    ->exists();
                    
                // Check if 30 min reminder was sent
                $has30MinReminder = Notification::where('ref_type', 'appointment')
                    ->where('ref_id', $appointment->id)
                    ->where('type', 'reminder')
                    ->where('title', 'like', '%30 phút%')
                    ->exists();

                if (!$has1HourReminder && $minutesUntil <= 60 && $minutesUntil > 30) {
                    // Send 60 min reminder
                    $this->createReminder($appointment, '1 tiếng');
                } elseif (!$has30MinReminder && $minutesUntil <= 30) {
                    // Send 30 min reminder
                    $this->createReminder($appointment, '30 phút');
                }
            }
        }
    }

    private function createReminder($appointment, $timeString)
    {
        $patient = $appointment->patientProfile->user ?? null;
        $doctor = $appointment->doctor->user ?? null;

        $title = "Nhắc nhở: Lịch hẹn sắp diễn ra sau {$timeString}";
        $content = "Bạn có một lịch hẹn với mã {$appointment->appointment_code} sẽ diễn ra sau {$timeString}.\nThời gian: " . Carbon::parse($appointment->appointment_time)->format('H:i');

        // To Patient
        if ($patient) {
            Notification::create([
                'user_id' => $patient->id,
                'title' => $title,
                'content' => $content,
                'type' => 'reminder',
                'channel' => 'in_web',
                'is_sent' => true, // In_web notifications don't need actual sending, they are just saved
                'ref_type' => 'appointment',
                'ref_id' => $appointment->id,
            ]);
            
            if ($patient->email) {
                Notification::create([
                    'user_id' => $patient->id,
                    'title' => $title,
                    'content' => $content,
                    'type' => 'reminder',
                    'channel' => 'email',
                    'is_sent' => false,
                    'ref_type' => 'appointment',
                    'ref_id' => $appointment->id,
                ]);
            }
        }

        // To Doctor
        if ($doctor) {
            Notification::create([
                'user_id' => $doctor->id,
                'title' => $title,
                'content' => "Bệnh nhân " . ($appointment->patientProfile->full_name ?? '—') . " có lịch hẹn sau {$timeString}.",
                'type' => 'reminder',
                'channel' => 'in_web',
                'is_sent' => true,
                'ref_type' => 'appointment',
                'ref_id' => $appointment->id,
            ]);
        }
    }
}
