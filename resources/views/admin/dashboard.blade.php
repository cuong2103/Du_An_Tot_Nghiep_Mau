<x-layouts.admin title="Dashboard">
    <!-- Greeting -->
    <div class="mb-8">
        @php
            $dayOfWeek = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'][now()->dayOfWeek];
            $todayString = $dayOfWeek . ', ' . now()->format('d/m/Y');
        @endphp
        <h2 class="text-2xl font-bold text-gray-900">Xin chào, {{ explode(' ', Auth::user()->full_name)[count(explode(' ', Auth::user()->full_name))-1] }}! 👋</h2>
        <p class="text-gray-500 mt-1">Hôm nay là {{ $todayString }}. Chúc bạn một ngày làm việc hiệu quả.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="h-12 w-12 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xl mr-4 shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Tổng người dùng</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="h-12 w-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center text-xl mr-4 shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Lịch hẹn hôm nay</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($todayAppointmentsCount) }}</p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="h-12 w-12 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-xl mr-4 shrink-0">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Bác sĩ hoạt động</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($activeDoctorsCount) }}</p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="h-12 w-12 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center text-xl mr-4 shrink-0">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Chờ xử lý</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($pendingAppointmentsCount) }}</p>
            </div>
        </div>
    </div>

    <!-- Today's Appointments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Lịch hẹn hôm nay</h3>
            <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">Xem tất cả &rarr;</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã LH</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bệnh nhân</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bác sĩ</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giờ khám</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($todayAppointments as $appt)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            {{ $appt->appointment_code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $appt->patientProfile->full_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $appt->doctorProfile->full_title ?? 'Chưa xếp' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <i class="fa-regular fa-clock text-gray-400 mr-1"></i> {{ substr($appt->appointment_time, 0, 5) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'checked_in' => 'bg-blue-100 text-blue-800',
                                    'examining' => 'bg-purple-100 text-purple-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'absent' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'Chờ khám',
                                    'checked_in' => 'Đã tiếp nhận',
                                    'examining' => 'Đang khám',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã huỷ',
                                    'absent' => 'Vắng mặt',
                                ];
                                $colorClass = $statusColors[$appt->status] ?? 'bg-gray-100 text-gray-800';
                                $label = $statusLabels[$appt->status] ?? $appt->status;
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                {{ $label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-regular fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                <p>Hôm nay chưa có lịch hẹn nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
