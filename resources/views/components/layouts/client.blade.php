<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Trang chủ' }} - Carebook Clinic</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen text-gray-800">

    <!-- Header Navigation -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold text-xl">
                            <i class="fa-solid fa-briefcase-medical"></i>
                        </div>
                        <span class="font-bold text-2xl text-gray-900 tracking-tight">Care<span class="text-blue-600">Book</span></span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">Trang chủ</a>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">Chuyên khoa</a>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">Bác sĩ</a>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">Hỏi đáp (FAQ)</a>
                </nav>

                <!-- Auth / User Menu -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#doctors-section" class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-5 py-2.5 rounded-full text-sm font-bold transition-all shadow-sm flex items-center gap-2 border border-blue-200">
                        <i class="fa-regular fa-calendar-check"></i> Đặt lịch khám
                    </a>
                    
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full text-sm font-medium transition-all shadow-sm hover:shadow-md">Đăng ký</a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                    {{ substr(Auth::user()->full_name, 0, 1) }}
                                </div>
                                <span>{{ Auth::user()->full_name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                            </button>
                            <!-- Dropdown -->
                            <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50" style="display: none;">
                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'receptionist' || Auth::user()->role === 'doctor')
                                    <a href="/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600"><i class="fa-solid fa-gauge w-5"></i> Trang Quản trị</a>
                                @endif
                                <a href="{{ route('client.account.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600"><i class="fa-solid fa-user w-5"></i> Hồ sơ của tôi</a>
                                <a href="{{ route('client.appointments.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600"><i class="fa-regular fa-calendar-check w-5"></i> Lịch hẹn khám</a>
                                <hr class="my-1 border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Đăng xuất</button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 hover:text-gray-900 focus:outline-none">
                        <i class="fa-solid fa-bars text-2xl" x-show="!mobileMenuOpen"></i>
                        <i class="fa-solid fa-xmark text-2xl" x-show="mobileMenuOpen" style="display: none;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" class="md:hidden bg-white border-t border-gray-100" style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="/" class="block px-3 py-2 text-base font-medium text-blue-600 bg-blue-50 rounded-md">Trang chủ</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-md">Chuyên khoa</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-md">Bác sĩ</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-md">Hỏi đáp (FAQ)</a>
            </div>
            @guest
                <div class="pt-4 pb-3 border-t border-gray-100 px-5 flex gap-3">
                    <a href="{{ route('login') }}" class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">Đăng ký</a>
                </div>
            @else
                <div class="pt-4 pb-3 border-t border-gray-100">
                    <div class="px-5 flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                            {{ substr(Auth::user()->full_name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ Auth::user()->full_name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->phone }}</div>
                        </div>
                    </div>
                    <div class="mt-3 px-2 space-y-1">
                        <a href="{{ route('client.account.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-md">Hồ sơ của tôi</a>
                        <a href="{{ route('client.appointments.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-md">Lịch hẹn khám</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-md">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 pt-16 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <a href="/" class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-lg">
                            <i class="fa-solid fa-briefcase-medical"></i>
                        </div>
                        <span class="font-bold text-xl text-gray-900 tracking-tight">Care<span class="text-blue-600">Book</span></span>
                    </a>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Nền tảng đặt lịch khám bệnh trực tuyến hàng đầu, kết nối bệnh nhân với các bác sĩ chuyên khoa giỏi nhất một cách nhanh chóng và dễ dàng.</p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-50 text-gray-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-50 text-gray-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-bold text-gray-900 mb-4">Dịch vụ</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm transition-colors">Khám Tổng quát</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm transition-colors">Khám Chuyên khoa</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm transition-colors">Tư vấn Sức khỏe</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm transition-colors">Gói Khám Bệnh</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-4">Hỗ trợ</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm transition-colors">Câu hỏi thường gặp (FAQ)</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm transition-colors">Hướng dẫn Đặt lịch</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm transition-colors">Chính sách Bảo mật</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm transition-colors">Quy định Hủy lịch</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-4">Liên hệ</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 text-blue-600"></i>
                            <span class="text-gray-500 text-sm">123 Đường Tôn Đức Thắng, Quận 1, TP. Hồ Chí Minh</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-phone text-blue-600"></i>
                            <span class="text-gray-500 text-sm font-medium">1900 123 456</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope text-blue-600"></i>
                            <span class="text-gray-500 text-sm">support@carebook.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} CareBook Clinic. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="text-sm text-gray-400 hover:text-gray-600">Điều khoản sử dụng</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-gray-600">Bảo mật</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
