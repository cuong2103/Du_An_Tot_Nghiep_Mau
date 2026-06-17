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
        $this->processAppointmentReminders();
        $this->sendPendingNotifications();
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
                'is_sent' => false,
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
                'is_sent' => false,
                'ref_type' => 'appointment',
                'ref_id' => $appointment->id,
            ]);
        }
    }

    private function sendPendingNotifications()
    {
        $notifications = Notification::where('is_sent', false)
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            })
            ->with('user')
            ->get();

        foreach ($notifications as $notification) {
            try {
                if ($notification->channel === 'email' && $notification->user && $notification->user->email) {
                    Mail::to($notification->user->email)->send(new NotificationMail($notification));
                }
                
                $notification->update(['is_sent' => true]);
            } catch (\Exception $e) {
                \Log::error("Failed to send notification {$notification->id}: " . $e->getMessage());
            }
        }
    }
}
