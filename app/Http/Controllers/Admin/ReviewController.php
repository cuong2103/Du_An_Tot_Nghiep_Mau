<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\DoctorProfile;
use App\Models\Specialty;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Hiển thị danh sách các đánh giá
     */
    public function index(Request $request)
    {
        $query = Review::with(['patientProfile.user', 'doctorProfile.user', 'specialty']);

        // Filter by Rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        // Filter by Visibility
        if ($request->filled('is_visible')) {
            $query->where('is_visible', $request->input('is_visible'));
        }

        // Filter by Doctor
        if ($request->filled('doctor_profile_id')) {
            $query->where('doctor_profile_id', $request->input('doctor_profile_id'));
        }

        // Filter by Specialty
        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->input('specialty_id'));
        }

        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $doctors = DoctorProfile::with('user')->get();
        $specialties = Specialty::all();

        return view('admin.reviews.index', compact('reviews', 'doctors', 'specialties'));
    }

    /**
     * Xem chi tiết đánh giá
     */
    public function show($id)
    {
        $review = Review::with([
            'patientProfile.user', 
            'doctorProfile.user', 
            'specialty', 
            'appointment.patientProfile',
            'appointment.doctor'
        ])->findOrFail($id);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Bật/Tắt hiển thị đánh giá
     */
    public function toggleVisibility($id)
    {
        $review = Review::findOrFail($id);
        $review->is_visible = !$review->is_visible;
        $review->save();

        return back()->with('success', 'Đã cập nhật trạng thái hiển thị của đánh giá.');
    }

    /**
     * Xóa đánh giá
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Đã xóa đánh giá thành công.');
    }
}
