<x-layouts.client title="Xác nhận Đặt Lịch">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Xác nhận Đặt Lịch Khám</h1>

            @if(session('error'))
                <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-xl"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('client.appointments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="time" value="{{ $time }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Left: Booking Info -->
                    <div class="md:col-span-2 space-y-6">
                        
                        <!-- Select Patient Profile -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm"><i class="fa-solid fa-users"></i></div>
                                1. Chọn hồ sơ người bệnh
                            </h2>
                            
                            @if(count($patientProfiles) > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    @foreach($patientProfiles as $profile)
                                        <label class="relative flex cursor-pointer rounded-xl border bg-white p-4 shadow-sm focus:outline-none hover:border-blue-300 has-[:checked]:border-blue-600 has-[:checked]:ring-1 has-[:checked]:ring-blue-600">
                                            <input type="radio" name="patient_profile_id" value="{{ $profile->id }}" class="sr-only" required>
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span class="block text-sm font-medium text-gray-900">{{ $profile->full_name }}</span>
                                                    <span class="mt-1 flex items-center text-sm text-gray-500">
                                                        {{ $profile->gender == 'male' ? 'Nam' : ($profile->gender == 'female' ? 'Nữ' : 'Khác') }} - {{ \Carbon\Carbon::parse($profile->date_of_birth)->age }} tuổi
                                                    </span>
                                                </span>
                                            </span>
                                            <i class="fa-solid fa-circle-check text-blue-600 text-xl absolute right-4 top-4 opacity-0 transition-opacity peer-checked:opacity-100"></i>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="text-sm text-blue-600"><a href="#" class="hover:underline">+ Tạo hồ sơ mới</a></p>
                            @else
                                <div class="text-center py-6 bg-yellow-50 rounded-xl border border-yellow-100">
                                    <i class="fa-solid fa-user-xmark text-3xl text-yellow-500 mb-3"></i>
                                    <p class="text-gray-700 font-medium mb-3">Bạn chưa có hồ sơ người bệnh nào.</p>
                                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors inline-block">Tạo hồ sơ ngay</a>
                                </div>
                            @endif
                        </div>

                        <!-- Reason -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm"><i class="fa-solid fa-stethoscope"></i></div>
                                2. Lý do khám bệnh
                            </h2>
                            <div>
                                <textarea name="reason" rows="4" required placeholder="Vui lòng mô tả chi tiết triệu chứng, biểu hiện bệnh để bác sĩ có thể nắm bắt sơ bộ tình trạng của bạn..." class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm"></textarea>
                            </div>
                        </div>

                    </div>

                    <!-- Right: Summary -->
                    <div class="md:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm border border-blue-100 sticky top-24 overflow-hidden">
                            <div class="bg-blue-50 p-6 border-b border-blue-100">
                                <h3 class="font-bold text-gray-900 mb-4">Thông tin Cuộc hẹn</h3>
                                
                                <div class="flex items-center gap-3 mb-4">
                                    <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?q=80&w=100&auto=format&fit=crop" class="w-12 h-12 rounded-full object-cover">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $doctor->full_name }}</p>
                                        <p class="text-xs text-blue-600">{{ $doctor->doctorProfile?->specialties->first()->name ?? 'Khoa Khám Bệnh' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6 space-y-4 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500"><i class="fa-regular fa-calendar mr-2"></i>Ngày khám:</span>
                                    <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500"><i class="fa-regular fa-clock mr-2"></i>Giờ khám:</span>
                                    <span class="font-bold text-blue-600">{{ $time }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500"><i class="fa-solid fa-money-bill-wave mr-2"></i>Chi phí khám:</span>
                                    <span class="font-bold text-gray-900">{{ number_format($doctor->doctorProfile->consultation_fee ?? 200000) }}đ</span>
                                </div>
                            </div>
                            
                            <div class="p-6 bg-gray-50 border-t border-gray-100">
                                <div class="flex items-start gap-2 mb-4">
                                    <input type="checkbox" required id="agree" class="mt-1 border-gray-300 rounded text-blue-600 focus:ring-blue-500">
                                    <label for="agree" class="text-xs text-gray-500">Tôi cam kết đi khám đúng giờ và tuân thủ quy định của phòng khám. Tôi hiểu rằng tôi sẽ thanh toán tại quầy lễ tân.</label>
                                </div>
                                
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition-colors shadow-md">
                                    Xác Nhận Đặt Lịch
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        .peer-checked\:opacity-100:checked + span + i { opacity: 1; }
        /* A little hack for Tailwind has-[:checked] support if using CDN */
        input:checked ~ span { border-color: #2563eb; }
        input:checked ~ i { opacity: 1; }
    </style>
</x-layouts.client>
