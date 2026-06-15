<x-layouts.admin title="Chỉnh sửa lễ tân — {{ $receptionist->full_name }}">
    <!-- Session Alerts -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" style="display: none;"
             class="bg-green-50 text-green-800 p-4 rounded-lg mb-6 flex items-center justify-between border border-green-200">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-green-500"></i>
                {{ session('success') }}
            </div>
            <button @click="show=false" class="text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

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
                            <span class="text-gray-900 font-medium">Chỉnh sửa</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">Chỉnh sửa lễ tân — {{ $receptionist->full_name }}</h2>
        </div>
        <div>
            <a href="{{ route('admin.receptionists.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột trái (2/3) Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.receptionists.update', $receptionist->id) }}" method="POST" x-data="{ loading: false }" @submit="loading = true" id="updateForm">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Thông tin đăng nhập -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-900"><i class="fa-solid fa-right-to-bracket text-blue-500 mr-2"></i> Thông tin đăng nhập</h3>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên đầy đủ <span class="text-red-500">*</span></label>
                                <input type="text" name="full_name" value="{{ old('full_name', $receptionist->full_name) }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                                    <input type="text" name="phone" value="{{ old('phone', $receptionist->phone) }}" required placeholder="Dùng để đăng nhập" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập (Username) <span class="text-red-500">*</span></label>
                                    <input type="text" name="username" value="{{ old('username', $receptionist->username) }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                    @error('username')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $receptionist->email) }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5" x-data="{ showPw: false }">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                                    <div class="relative">
                                        <input :type="showPw ? 'text' : 'password'" name="password" minlength="8" placeholder="Để trống nếu không đổi mật khẩu" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                        <button type="button" @click="showPw = !showPw" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <i class="fa-solid" :class="showPw ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                                    <input :type="showPw ? 'text' : 'password'" name="password_confirmation" minlength="8" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
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
                                    <input type="text" name="employee_code" value="{{ old('employee_code', $receptionist->staffProfile->employee_code ?? '') }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none uppercase">
                                    @error('employee_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chức vụ <span class="text-red-500">*</span></label>
                                    <input type="text" name="position" value="{{ old('position', $receptionist->staffProfile->position ?? '') }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                    @error('position')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                                    <input type="text" name="department" value="{{ old('department', $receptionist->staffProfile->department ?? '') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                    @error('department')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">SĐT nội bộ</label>
                                    <input type="text" name="internal_phone" value="{{ old('internal_phone', $receptionist->staffProfile->internal_phone ?? '') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                    @error('internal_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày vào làm</label>
                                <input type="date" name="start_date" value="{{ old('start_date', $receptionist->staffProfile->start_date ? \Carbon\Carbon::parse($receptionist->staffProfile->start_date)->format('Y-m-d') : '') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Cột phải (1/3) Hiện trạng & Thao tác -->
        <div class="space-y-6">
            <!-- Thao tác bảo mật -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-4"><i class="fa-solid fa-bolt text-yellow-500 mr-2"></i> Trạng thái hoạt động</h3>
                
                <div class="mb-5 flex items-center justify-between p-3 rounded-lg {{ $receptionist->is_active ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
                    <span class="text-sm font-medium {{ $receptionist->is_active ? 'text-green-800' : 'text-red-800' }}">Tài khoản</span>
                    @if($receptionist->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Hoạt động</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Đã khoá</span>
                    @endif
                </div>

                <form action="{{ route('admin.receptionists.toggle-active', $receptionist->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @if(auth()->id() == $receptionist->id)
                        <button type="button" disabled class="w-full bg-gray-100 text-gray-400 cursor-not-allowed px-4 py-2.5 rounded-lg text-sm font-medium border border-gray-200 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-lock"></i> Không thể khoá chính mình
                        </button>
                    @else
                        @if($receptionist->is_active)
                            <button type="submit" onclick="return confirm('Bạn có chắc muốn khoá tài khoản lễ tân này?')" class="w-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                <i class="fa-solid fa-lock"></i> Khoá tài khoản này
                            </button>
                        @else
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white shadow-sm border border-transparent px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                <i class="fa-solid fa-lock-open"></i> Mở khoá tài khoản
                            </button>
                        @endif
                    @endif
                </form>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="text-sm text-gray-500 mb-2 flex justify-between">
                        <span>Ngày tạo:</span>
                        <span class="font-medium text-gray-900">{{ $receptionist->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="text-sm text-gray-500 flex justify-between">
                        <span>Đăng nhập cuối:</span>
                        <span class="font-medium text-gray-900">{{ $receptionist->last_login_at?->diffForHumans() ?? 'Chưa đăng nhập' }}</span>
                    </div>
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col gap-3">
                <button type="submit" form="updateForm" class="w-full px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i> Lưu cập nhật
                </button>
                <a href="{{ route('admin.receptionists.index') }}" class="w-full text-center px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Huỷ bỏ
                </a>
            </div>
        </div>
    </div>
</x-layouts.admin>
