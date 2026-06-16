<x-layouts.client title="Đặt lịch thành công">
    <div class="bg-gray-50 py-16 min-h-screen flex items-center justify-center">
        <div class="max-w-xl w-full mx-auto px-4">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden text-center relative border border-gray-100">
                <!-- Top Decoration -->
                <div class="h-32 bg-gradient-to-r from-green-400 to-emerald-500 w-full absolute top-0 left-0 z-0"></div>
                
                <div class="relative z-10 pt-16 px-8 pb-12">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto shadow-lg mb-6">
                        <i class="fa-solid fa-check text-5xl text-green-500"></i>
                    </div>
                    
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Đặt Lịch Thành Công!</h1>
                    <p class="text-gray-500 mb-8">Cuộc hẹn của bạn đã được ghi nhận vào hệ thống. Xin cảm ơn vì đã tin tưởng CareBook.</p>
                    
                    <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-left border border-gray-100 shadow-inner">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="text-gray-500 mb-1">Mã cuộc hẹn</div>
                            <div class="font-bold text-gray-900 text-right">#{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</div>
                            
                            <div class="text-gray-500 mb-1">Bệnh nhân</div>
                            <div class="font-bold text-gray-900 text-right">{{ $appointment->patientProfile->full_name }}</div>
                            
                            <div class="text-gray-500 mb-1">Bác sĩ khám</div>
                            <div class="font-bold text-gray-900 text-right">{{ $appointment->doctorProfile->user->full_name ?? 'Bác sĩ' }}</div>
                            
                            <div class="text-gray-500 mb-1">Thời gian</div>
                            <div class="font-bold text-blue-600 text-right">
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                            </div>
                            
                            <div class="text-gray-500 mb-1">Trạng thái</div>
                            <div class="text-right">
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Chờ xác nhận</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('client.appointments.index') }}" class="bg-white border-2 border-gray-200 hover:border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-bold transition-colors">
                            Quản lý lịch hẹn
                        </a>
                        <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-colors shadow-md">
                            Về trang chủ
                        </a>
                    </div>
                </div>
            </div>
            
            <p class="text-center text-sm text-gray-400 mt-8">
                Có câu hỏi? <a href="#" class="text-blue-600 hover:underline">Liên hệ tổng đài hỗ trợ</a>
            </p>
        </div>
    </div>
</x-layouts.client>
