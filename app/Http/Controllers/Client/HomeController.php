<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $specialties = Specialty::where('is_active', true)->get();
        $doctors = User::where('role', 'doctor')
            ->where('is_active', true)
            ->with(['doctorProfile.specialties'])
            ->limit(8)
            ->get();

        return view('client.home', compact('specialties', 'doctors'));
    }
}
