<x-layouts.admin title="Thêm lễ tân mới">
    <!-- Breadcrumbs & Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                            <a href="{{ route('admin.receptionists.index') }}" class="hover:text-blue-600 transition-colors">Lễ tân</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                            <span class="text-gray-900 font-medium">Thêm mới</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">Thêm lễ tân mới</h2>
        </div>
        <div>
            <a href="{{ route('admin.receptionists.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <form action="{{ route('admin.receptionists.store') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Thông tin đăng nhập -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900"><i class="fa-solid fa-right-to-bracket text-blue-500 mr-2"></i> Thông tin đăng nhập</h3>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên đầy đủ <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                        @error('full_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="Dùng để đăng nhập" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập (Username) <span class="text-red-500">*</span></label>
                            <input type="text" name="username" value="{{ old('username') }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5" x-data="{ showPw: false }">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input :type="showPw ? 'text' : 'password'" name="password" required minlength="8" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                <button type="button" @click="showPw = !showPw" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <i class="fa-solid" :class="showPw ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                            <input :type="showPw ? 'text' : 'password'" name="password_confirmation" required minlength="8" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin nhân viên -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900"><i class="fa-solid fa-address-card text-blue-500 mr-2"></i> Thông tin nhân viên</h3>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã nhân viên <span class="text-red-500">*</span></label>
                            <input type="text" name="employee_code" value="{{ old('employee_code') }}" required placeholder="VD: LT001" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none uppercase">
                            @error('employee_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chức vụ <span class="text-red-500">*</span></label>
                            <input type="text" name="position" value="{{ old('position') }}" required placeholder="VD: Lễ tân, Trưởng ca lễ tân" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                            <input type="text" name="department" value="{{ old('department') }}" placeholder="VD: Khoa Khám bệnh" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            @error('department')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SĐT nội bộ</label>
                            <input type="text" name="internal_phone" value="{{ old('internal_phone') }}" placeholder="VD: 101" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            @error('internal_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày vào làm</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.receptionists.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Huỷ
            </a>
            <button type="submit" :disabled="loading" class="px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors disabled:opacity-70 flex items-center gap-2">
                <span x-show="!loading"><i class="fa-solid fa-save mr-1"></i> Lưu lễ tân</span>
                <span x-show="loading" style="display: none;"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang xử lý...</span>
            </button>
        </div>
    </form>
</x-layouts.admin>
