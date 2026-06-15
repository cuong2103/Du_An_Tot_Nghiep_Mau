<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientProfile;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profiles = PatientProfile::where('owner_id', $user->id)->get();

        return view('client.account.index', compact('user', 'profiles'));
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|max:5',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        PatientProfile::create([
            'owner_id' => Auth::id(),
            'full_name' => $request->full_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'blood_type' => $request->blood_type,
            'medical_history' => $request->medical_history,
            'allergies' => $request->allergies,
        ]);

        return redirect()->back()->with('success', 'Đã thêm hồ sơ người bệnh thành công.');
    }
}
