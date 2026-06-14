<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $todayAppointmentsCount = Appointment::whereDate('appointment_date', today())->count();
        $activeDoctorsCount = User::where('role', 'doctor')->where('is_active', true)->count();
        $pendingAppointmentsCount = Appointment::where('status', 'pending')->count();

        $todayAppointments = Appointment::with(['patientProfile', 'doctorProfile.user'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'todayAppointmentsCount',
            'activeDoctorsCount',
            'pendingAppointmentsCount',
            'todayAppointments'
        ));
    }
}
