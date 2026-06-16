<x-layouts.client title="Đặt lịch thành công">
    <div class="max-w-xl mx-auto px-4 sm:px-6 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center relative overflow-hidden">
            <!-- Decorative background elements -->
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-green-50 to-white -z-10"></div>
            
            <!-- Icon thành công -->
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                <i class="fa-solid fa-check text-green-500 text-5xl"></i>
                <!-- Sparkles -->
                <div class="absolute -top-2 -right-2 text-green-400 text-xl"><i class="fa-solid fa-sparkles"></i></div>
                <div class="absolute bottom-2 -left-3 text-green-300 text-sm"><i class="fa-solid fa-star"></i></div>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Đặt lịch thành công!</h1>
            <p class="text-gray-500 mb-8 text-lg">Lịch khám của bạn đã được hệ thống ghi nhận.</p>

            <!-- Mã lịch hẹn nổi bật -->
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-8 relative">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-white px-3 text-xs font-bold text-blue-600 uppercase tracking-wider rounded-full border border-blue-100">
                    Mã lịch hẹn
                </div>
                <p class="text-3xl font-bold text-blue-700 font-mono tracking-widest">{{ $appointment->appointment_code }}</p>
                <p class="text-sm text-blue-600/70 mt-2">Vui lòng lưu lại mã này hoặc chụp màn hình để check-in tại quầy lễ tân.</p>
            </div>

            <!-- Thông tin chi tiết -->
            <div class="bg-gray-50 rounded-2xl p-6 text-left space-y-4 mb-8">
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 border-dashed">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fa-regular fa-user w-4 text-center"></i> Bệnh nhân</span>
                    <span class="font-bold text-gray-900">{{ $appointment->patientProfile->full_name }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 border-dashed">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fa-solid fa-user-doctor w-4 text-center"></i> Bác sĩ</span>
                    <span class="font-bold text-gray-900">{{ $appointment->doctorProfile->full_title }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 border-dashed">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fa-solid fa-stethoscope w-4 text-center"></i> Chuyên khoa</span>
                    <span class="font-bold text-gray-900">{{ $appointment->specialty->name }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 border-dashed">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fa-solid fa-door-open w-4 text-center"></i> Phòng khám</span>
                    <span class="font-bold text-gray-900">{{ $appointment->room->name ?? 'Phòng ' . $appointment->room->room_number }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 border-dashed">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fa-regular fa-calendar w-4 text-center"></i> Ngày khám</span>
                    <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fa-regular fa-clock w-4 text-center"></i> Giờ khám</span>
                    <span class="font-bold text-blue-600 text-lg">{{ substr($appointment->appointment_time, 0, 5) }}</span>
                </div>
            </div>

            <!-- Hướng dẫn đi khám -->
            <div class="text-left space-y-3 mb-10 bg-orange-50 border border-orange-100 p-5 rounded-2xl">
                <p class="font-bold text-orange-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list"></i> Hướng dẫn đến khám:
                </p>
                <div class="flex items-start gap-3 text-sm text-orange-800/80">
                    <div class="w-6 h-6 rounded-full bg-orange-200 text-orange-700 flex items-center justify-center font-bold shrink-0 mt-0.5">1</div>
                    <p>Có mặt tại quầy lễ tân trước giờ hẹn <strong>15 phút</strong> để làm thủ tục.</p>
                </div>
                <div class="flex items-start gap-3 text-sm text-orange-800/80">
                    <div class="w-6 h-6 rounded-full bg-orange-200 text-orange-700 flex items-center justify-center font-bold shrink-0 mt-0.5">2</div>
                    <p>Mang theo CCCD/CMND và thẻ BHYT (nếu có).</p>
                </div>
                <div class="flex items-start gap-3 text-sm text-orange-800/80">
                    <div class="w-6 h-6 rounded-full bg-orange-200 text-orange-700 flex items-center justify-center font-bold shrink-0 mt-0.5">3</div>
                    <p>Báo mã lịch hẹn <strong>{{ $appointment->appointment_code }}</strong> cho nhân viên lễ tân khi check-in.</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('client.appointments.index') }}"
                   class="px-8 py-3.5 border-2 border-blue-600 text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-list-check"></i> Xem lịch hẹn của tôi
                </a>
                <a href="{{ route('booking.index') }}"
                   class="px-8 py-3.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Đặt lịch khác
                </a>
            </div>
        </div>
    </div>

    <!-- Clear temporary booking state if exists -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            sessionStorage.removeItem('bookingState');
        });
    </script>
</x-layouts.client>
