<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WorkScheduleService;
use Exception;

class WorkScheduleController extends Controller
{
    protected $workScheduleService;

    public function __construct(WorkScheduleService $workScheduleService)
    {
        $this->workScheduleService = $workScheduleService;
    }

    /**
     * Fetch available time slots for a specific doctor on a specific date.
     *
     * @param Request $request
     * @param int $doctorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSlots(Request $request, $doctorId)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        try {
            $slots = $this->workScheduleService->getAvailableSlots($doctorId, $request->date);

            return response()->json([
                'success' => true,
                'data' => $slots,
                'message' => empty($slots) ? 'Không có ca khám nào trống trong ngày này.' : 'Lấy danh sách ca khám thành công.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tính toán lịch khám.',
                'error' => $e->getMessage() // In production, hide the exact error
            ], 500);
        }
    }
}
