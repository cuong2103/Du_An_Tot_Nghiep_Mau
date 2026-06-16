<x-layouts.client title="Hồ sơ của tôi">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Quản lý Tài khoản</h1>

            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                        <div class="p-6 text-center border-b border-gray-100">
                            <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-3xl mx-auto mb-4">
                                {{ substr($user->full_name, 0, 1) }}
                            </div>
                            <h2 class="font-bold text-xl text-gray-900">{{ $user->full_name }}</h2>
                            <p class="text-gray-500 text-sm mt-1">{{ $user->phone }}</p>
                        </div>
                        
                        <div class="p-4">
                            <nav class="space-y-1">
                                <a href="{{ route('client.account.index') }}" class="bg-blue-50 text-blue-700 flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors">
                                    <i class="fa-solid fa-users w-5 text-center"></i> Hồ sơ người bệnh
                                </a>
                                <a href="{{ route('client.appointments.index') }}" class="text-gray-700 hover:bg-gray-50 flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors">
                                    <i class="fa-regular fa-calendar-check w-5 text-center"></i> Lịch sử khám bệnh
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t border-gray-100">
                                    @csrf
                                    <button type="submit" class="w-full text-left text-red-600 hover:bg-red-50 flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors">
                                        <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> Đăng xuất
                                    </button>
                                </form>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Existing Profiles -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Danh sách Hồ sơ Người bệnh</h2>
                        </div>
                        
                        @if(count($profiles) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($profiles as $profile)
                                    <div class="border border-gray-200 rounded-xl p-5 hover:border-blue-300 hover:shadow-md transition-all relative">
                                        <div class="absolute top-4 right-4 text-blue-600 bg-blue-50 w-8 h-8 rounded-full flex items-center justify-center">
                                            <i class="fa-regular fa-user"></i>
                                        </div>
                                        <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $profile->full_name }}</h3>
                                        <div class="text-sm text-gray-500 space-y-1 mb-4">
                                            <p><i class="fa-solid fa-cake-candles w-4"></i> {{ \Carbon\Carbon::parse($profile->date_of_birth)->format('d/m/Y') }} ({{ \Carbon\Carbon::parse($profile->date_of_birth)->age }} tuổi)</p>
                                            <p><i class="fa-solid fa-venus-mars w-4"></i> {{ $profile->gender == 'male' ? 'Nam' : ($profile->gender == 'female' ? 'Nữ' : 'Khác') }}</p>
                                            @if($profile->blood_type)
                                                <p><i class="fa-solid fa-droplet w-4 text-red-400"></i> Nhóm máu: {{ $profile->blood_type }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <i class="fa-solid fa-users-slash text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">Bạn chưa tạo hồ sơ người bệnh nào.</p>
                                <p class="text-sm text-gray-400 mt-1">Vui lòng tạo ít nhất 1 hồ sơ để có thể đặt lịch khám.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Add New Profile Form -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8" id="add-profile">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-user-plus text-blue-600"></i> Thêm Hồ sơ mới
                        </h2>
                        
                        <form action="{{ route('client.account.store-profile') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                                    <input type="text" name="full_name" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh <span class="text-red-500">*</span></label>
                                    <input type="date" name="date_of_birth" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính <span class="text-red-500">*</span></label>
                                    <select name="gender" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="male">Nam</option>
                                        <option value="female">Nữ</option>
                                        <option value="other">Khác</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nhóm máu</label>
                                    <select name="blood_type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">Không rõ</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                                    <input type="text" name="address" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Số nhà, đường, phường/xã...">
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiền sử bệnh lý</label>
                                    <textarea name="medical_history" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Tiểu đường, cao huyết áp... (nếu có)"></textarea>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors shadow-sm">
                                    Lưu Hồ Sơ
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.client>
