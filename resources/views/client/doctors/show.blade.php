<x-layouts.client title="{{ $doctor->full_name }}">
    
    <div class="bg-blue-600 pb-24 pt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex text-blue-100 text-sm mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="/" class="hover:text-white transition-colors"><i class="fa-solid fa-house mr-2"></i>Trang chủ</a>
                    </li>
                    <li><div class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i><a href="#" class="hover:text-white transition-colors">Bác sĩ</a></div></li>
                    <li aria-current="page"><div class="flex items-center"><i class="fa-solid fa-chevron-right text-xs mx-2"></i><span class="text-white font-medium">{{ $doctor->full_name }}</span></div></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 mb-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Doctor Info -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Profile Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 flex flex-col sm:flex-row gap-8">
                    <div class="flex-shrink-0 mx-auto sm:mx-0">
                        <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?q=80&w=400&auto=format&fit=crop" alt="{{ $doctor->full_name }}" class="w-32 h-32 sm:w-48 sm:h-48 rounded-full object-cover border-4 border-blue-50 shadow-md">
                    </div>
                    <div>
                        <div class="inline-block bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide mb-3">
                            {{ $doctor->doctorProfile?->specialties->first()->name ?? 'Khoa Khám Bệnh' }}
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $doctor->full_name }}</h1>
                        <p class="text-gray-600 mb-4">{{ $doctor->doctorProfile->short_description ?? 'Bác sĩ chuyên khoa giàu kinh nghiệm.' }}</p>
                        
                        <div class="flex flex-wrap gap-4 text-sm">
                            <div class="flex items-center text-gray-700 bg-gray-50 px-3 py-2 rounded-lg">
                                <i class="fa-solid fa-star text-yellow-400 mr-2"></i> <strong>5.0</strong> (120 đánh giá)
                            </div>
                            <div class="flex items-center text-gray-700 bg-gray-50 px-3 py-2 rounded-lg">
                                <i class="fa-solid fa-money-bill-wave text-green-500 mr-2"></i> <strong>{{ number_format($doctor->doctorProfile->consultation_fee ?? 200000) }}đ</strong> / lượt
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Info Tabs -->
                <div x-data="{ tab: 'info' }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex border-b border-gray-100">
                        <button @click="tab = 'info'" class="flex-1 py-4 text-sm font-semibold text-center border-b-2 transition-colors" :class="tab === 'info' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">Thông tin chi tiết</button>
                        <button @click="tab = 'reviews'" class="flex-1 py-4 text-sm font-semibold text-center border-b-2 transition-colors" :class="tab === 'reviews' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">Phản hồi của bệnh nhân</button>
                    </div>
                    
                    <div class="p-6 sm:p-8">
                        <div x-show="tab === 'info'" class="prose prose-blue max-w-none">
                            @if($doctor->doctorProfile && $doctor->doctorProfile->detailed_description)
                                {!! nl2br(e($doctor->doctorProfile->detailed_description)) !!}
                            @else
                                <p>Đang cập nhật thông tin chi tiết về bác sĩ.</p>
                            @endif
                        </div>
                        
                        <div x-show="tab === 'reviews'" style="display: none;">
                            <!-- Placeholder for reviews -->
                            <div class="text-center py-8 text-gray-500">
                                <i class="fa-regular fa-comments text-4xl mb-3 text-gray-300"></i>
                                <p>Chưa có đánh giá nào cho bác sĩ này.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Booking Box -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 sticky top-24 overflow-hidden">
                    <div class="bg-blue-600 text-white p-4 text-center">
                        <h3 class="font-bold text-lg"><i class="fa-regular fa-calendar-check mr-2"></i> Lịch Khám Trống</h3>
                    </div>
                    
                    <div class="p-5" x-data="{ selectedDate: '{{ count($groupedSchedules) > 0 ? array_key_first($groupedSchedules) : '' }}', selectedSlot: null }">
                        <div class="text-center py-6">
                            <p class="text-gray-600 mb-6">Bạn muốn đặt lịch khám với {{ $doctor->full_title ?? $doctor->full_name }}?</p>
                            <a href="{{ route('booking.index', ['doctor_id' => $doctor->id, 'specialty_id' => $doctor->doctorProfile->specialties->first()->id ?? null]) }}" 
                               class="inline-block w-full py-3.5 rounded-xl text-white font-bold text-lg transition-all shadow-md bg-blue-600 hover:bg-blue-700">
                                Đặt Lịch Ngay <i class="fa-solid fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</x-layouts.client>
