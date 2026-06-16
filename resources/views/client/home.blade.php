<x-layouts.client title="CareBook - Đặt Lịch Khám Dễ Dàng">
    
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-50 to-indigo-50 py-20 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <svg class="absolute right-0 top-0 h-full w-1/2 transform translate-x-1/3 -translate-y-1/4 text-blue-100/50" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold mb-6">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span> Nền tảng y tế số 1
                    </span>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-6">
                        Chăm sóc sức khỏe <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">trong tầm tay bạn</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Đặt lịch khám nhanh chóng với các bác sĩ chuyên khoa hàng đầu. Theo dõi hồ sơ bệnh án và nhận tư vấn sức khỏe mọi lúc, mọi nơi.
                    </p>
                    
                    <!-- Search Bar -->
                    <div class="bg-white p-2 rounded-2xl shadow-lg border border-gray-100 flex flex-col md:flex-row gap-2 max-w-xl mb-6">
                        <div class="flex-1 flex items-center px-4 py-2 bg-gray-50 rounded-xl">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 mr-3"></i>
                            <input type="text" placeholder="Tìm bác sĩ, chuyên khoa..." class="bg-transparent border-none focus:ring-0 text-sm w-full outline-none">
                        </div>
                        <button class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-3 rounded-xl font-medium transition-all shadow-md whitespace-nowrap">Tìm kiếm</button>
                    </div>

                    <!-- Call to action buttons -->
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('booking.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                            <i class="fa-solid fa-calendar-plus text-lg"></i> Đặt Lịch Khám Ngay
                        </a>
                        <a href="#" class="bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 px-8 py-3.5 rounded-xl font-bold transition-all shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-phone-volume text-blue-600 text-lg"></i> Tư Vấn Khám
                        </a>
                    </div>
                    
                    <div class="mt-8 flex items-center gap-6 text-sm text-gray-500 font-medium">
                        <div class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-green-500 text-lg"></i> Nhanh chóng</div>
                        <div class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-green-500 text-lg"></i> Tiện lợi</div>
                        <div class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-green-500 text-lg"></i> An toàn</div>
                    </div>
                </div>
                
                <div class="hidden md:block relative">
                    <!-- Decorative elements -->
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                    
                    <div class="relative bg-white p-6 rounded-3xl shadow-xl border border-gray-100 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                        <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?q=80&w=800&auto=format&fit=crop" alt="Doctor" class="rounded-2xl w-full object-cover h-[400px]">
                        
                        <!-- Floating Card -->
                        <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-lg border border-gray-100 flex items-center gap-4 animate-bounce-slow">
                            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Lịch hẹn trống</p>
                                <p class="font-bold text-gray-900">Hôm nay: 15 Slot</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Specialties Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Chuyên Khoa Phổ Biến</h2>
                <p class="text-gray-500">Tìm kiếm bác sĩ phù hợp theo từng chuyên khoa y tế. Đội ngũ bác sĩ giàu kinh nghiệm luôn sẵn sàng chăm sóc bạn.</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @foreach($specialties as $specialty)
                <a href="#" class="group bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-100 transition-all text-center block relative overflow-hidden">
                    <div class="absolute inset-0 bg-blue-50 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 mx-auto bg-blue-50 group-hover:bg-white text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-4 transition-colors">
                            <i class="{{ $specialty->icon ?? 'fa-solid fa-stethoscope' }}"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $specialty->name }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Top Doctors Section -->
    <section id="doctors-section" class="py-20 bg-gray-50 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Bác Sĩ Nổi Bật</h2>
                    <p class="text-gray-500">Đặt lịch với các bác sĩ chuyên khoa giỏi nhất, được hàng ngàn bệnh nhân tin tưởng.</p>
                </div>
                <a href="#" class="hidden md:inline-flex items-center gap-2 text-blue-600 font-semibold hover:text-blue-800 transition-colors">
                    Xem tất cả <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($doctors as $doctor)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-shadow group flex flex-col">
                    <div class="h-48 overflow-hidden relative">
                        <!-- Placeholder doctor image -->
                        <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?q=80&w=400&auto=format&fit=crop" alt="{{ $doctor->full_name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg text-xs font-bold text-yellow-500 flex items-center gap-1 shadow-sm">
                            <i class="fa-solid fa-star"></i> 5.0
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <div class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">
                            {{ $doctor->doctorProfile?->specialties->first()->name ?? 'Khoa Khám Bệnh' }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1">{{ $doctor->full_name }}</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $doctor->doctorProfile->short_description ?? 'Bác sĩ chuyên khoa giàu kinh nghiệm.' }}</p>
                        
                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div class="text-sm font-bold text-gray-900">{{ number_format($doctor->doctorProfile->consultation_fee ?? 200000) }}đ</div>
                            <a href="{{ route('client.doctors.show', $doctor->id) }}" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center gap-1">
                                <i class="fa-solid fa-calendar-check"></i> Đặt lịch
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="#" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:text-blue-800 transition-colors">
                    Xem tất cả Bác sĩ <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Quy trình Đặt lịch</h2>
                <p class="text-gray-500">Chỉ với 3 bước đơn giản, bạn đã có thể chủ động thời gian thăm khám tại CareBook.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                <!-- Connecting line -->
                <div class="hidden md:block absolute top-12 left-[15%] right-[15%] h-0.5 bg-gray-100 z-0"></div>
                
                <div class="relative z-10 text-center">
                    <div class="w-24 h-24 mx-auto bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-3xl mb-6 shadow-sm border-4 border-white">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">1. Chọn Bác sĩ</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Tìm kiếm và chọn bác sĩ hoặc chuyên khoa phù hợp với tình trạng sức khỏe của bạn.</p>
                </div>
                
                <div class="relative z-10 text-center">
                    <div class="w-24 h-24 mx-auto bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-3xl mb-6 shadow-sm border-4 border-white">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">2. Chọn Thời gian</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Lựa chọn ngày và khung giờ trống thực tế của bác sĩ mà bạn có thể sắp xếp đến khám.</p>
                </div>
                
                <div class="relative z-10 text-center">
                    <div class="w-24 h-24 mx-auto bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-3xl mb-6 shadow-sm border-4 border-white">
                        <i class="fa-solid fa-hospital-user"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">3. Đến Khám</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Đến cơ sở y tế theo đúng thời gian đã hẹn và thanh toán tại quầy lễ tân.</p>
                </div>
            </div>
        </div>
    </section>

</x-layouts.client>
