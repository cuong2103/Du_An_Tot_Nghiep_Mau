<x-layouts.admin title="Gửi Thông báo">
    <!-- TomSelect CSS cho tính năng Search Dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
    <style>
        /* Tùy chỉnh nhẹ giao diện TomSelect cho hợp với Tailwind */
        .ts-control { border-radius: 0.5rem; padding: 0.5rem 0.75rem; border-color: #d1d5db; box-shadow: none; }
        .ts-control.focus { border-color: #3b82f6; box-shadow: 0 0 0 1px #3b82f6; }
    </style>

    <div class="mb-6">
        <a href="{{ route('admin.notifications.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors mb-2 inline-block">
            <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại Lịch sử
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Phát Thông báo mới</h2>
        <p class="text-gray-500 mt-1">Gửi tin nhắn trực tiếp đến người dùng qua hệ thống, email hoặc các kênh khác.</p>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-50 text-red-800 rounded-lg p-4 flex items-center border border-red-200">
            <i class="fa-solid fa-triangle-exclamation text-red-500 mr-3 text-lg"></i>
            <span class="flex-1 text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ targetType: '{{ old('target', 'single') }}' }">
        <form action="{{ route('admin.notifications.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Cột Trái (Cấu hình Đối tượng & Kênh) -->
                <div class="lg:col-span-5 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">1. Chọn đối tượng nhận</h3>
                    
                    <!-- Radio Buttons chọn Target -->
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors" :class="targetType === 'single' ? 'border-blue-500 bg-blue-50/50' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="target" value="single" x-model="targetType" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Một cá nhân cụ thể</span>
                                <span class="block text-xs text-gray-500">Chọn chính xác một người dùng trong hệ thống</span>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors" :class="targetType === 'role' ? 'border-blue-500 bg-blue-50/50' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="target" value="role" x-model="targetType" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Nhóm đối tượng (Role)</span>
                                <span class="block text-xs text-gray-500">Gửi hàng loạt cho toàn bộ Bệnh nhân hoặc Bác sĩ...</span>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors" :class="targetType === 'all' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="target" value="all" x-model="targetType" class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Tất cả người dùng (Broadcasting)</span>
                                <span class="block text-xs text-red-500">Gửi cho toàn bộ người dùng có tài khoản trên hệ thống</span>
                            </div>
                        </label>
                    </div>

                    <!-- Khu vực chọn chi tiết tùy theo Target -->
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 min-h-[100px]">
                        
                        <!-- Select User (TomSelect) -->
                        <div x-show="targetType === 'single'" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm Người dùng <span class="text-red-500">*</span></label>
                            <select id="user-select" name="user_id" class="w-full" placeholder="Nhập tên, số điện thoại hoặc email...">
                                <option value="">Chọn người dùng...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }} ({{ $user->phone ?? 'Chưa có SĐT' }}) - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Select Role -->
                        <div x-show="targetType === 'role'" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Nhóm (Role) <span class="text-red-500">*</span></label>
                            <select name="role" class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                <option value="">-- Chọn nhóm đối tượng --</option>
                                <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>Bệnh nhân (Khách hàng)</option>
                                <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Bác sĩ</option>
                                <option value="receptionist" {{ old('role') == 'receptionist' ? 'selected' : '' }}>Lễ tân</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                            </select>
                            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Cảnh báo Spam -->
                        <div x-show="targetType === 'all'" style="display: none;">
                            <div class="flex items-start">
                                <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5 mr-2"></i>
                                <div>
                                    <h4 class="text-sm font-bold text-red-800">Cảnh báo Gửi hàng loạt</h4>
                                    <p class="text-xs text-red-600 mt-1">Thông báo này sẽ được phát đi cho <strong>Hàng ngàn người dùng</strong>. Hãy kiểm tra thật kỹ lỗi chính tả và nội dung trước khi gửi để tránh trở thành tin nhắn rác (Spam).</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2 mt-8">2. Kênh & Phân loại</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kênh phát <span class="text-red-500">*</span></label>
                            <select name="channel" class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                <option value="in_web" {{ old('channel') == 'in_web' ? 'selected' : '' }}>Trong Website (Thông báo chuông)</option>
                                <option value="email" {{ old('channel') == 'email' ? 'selected' : '' }}>Gửi Email</option>
                                <!-- Tạm ẩn Zalo trên UI vì mô tả yêu cầu, nhưng vẫn giữ ở đây dưới dạng comment nếu sau này cần -->
                                <!-- <option value="zalo" {{ old('channel') == 'zalo' ? 'selected' : '' }}>Tin nhắn Zalo ZNS</option> -->
                            </select>
                            @error('channel') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại thông báo <span class="text-red-500">*</span></label>
                            <select name="type" class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>Hệ thống chung</option>
                                <option value="reminder" {{ old('type') == 'reminder' ? 'selected' : '' }}>Nhắc nhở chung</option>
                                <option value="appointment" {{ old('type') == 'appointment' ? 'selected' : '' }}>Liên quan lịch khám</option>
                                <option value="result" {{ old('type') == 'result' ? 'selected' : '' }}>Kết quả y tế</option>
                            </select>
                            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Cột Phải (Nội dung Thông báo) -->
                <div class="lg:col-span-7 space-y-6">
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">3. Soạn thảo Nội dung</h3>
                    
                    <div class="bg-blue-50/30 p-5 rounded-xl border border-blue-100">
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-900 mb-1">Tiêu đề thông báo <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-base font-bold outline-none shadow-sm" placeholder="Nhập tiêu đề ngắn gọn...">
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Nội dung chi tiết <span class="text-red-500">*</span></label>
                            <textarea name="content" rows="12" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none shadow-sm" placeholder="Viết nội dung thông báo đầy đủ ở đây...">{{ old('content') }}</textarea>
                            <p class="text-xs text-gray-500 mt-2 flex items-center">
                                <i class="fa-solid fa-circle-info mr-1"></i> Nội dung không hỗ trợ chèn ảnh để đảm bảo tốc độ gửi nhanh chóng trên mọi thiết bị.
                            </p>
                            @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Khu vực Submit -->
                    <div class="mt-8 pt-5 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            <span x-show="targetType === 'all'" class="text-red-600 font-medium animate-pulse"><i class="fa-solid fa-circle-exclamation mr-1"></i> Sẽ gửi cho hàng ngàn người</span>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.notifications.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Hủy bỏ
                            </a>
                            <button type="submit" 
                                class="px-6 py-2.5 text-sm font-medium text-white rounded-lg transition-colors flex items-center gap-2 shadow-sm"
                                :class="targetType === 'all' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'"
                                @click="if(targetType === 'all') return confirm('CẢNH BÁO: Bạn đang chọn phát thông báo cho TOÀN BỘ HỆ THỐNG. Bạn có chắc chắn muốn thực hiện hành động này không?'); return true;"
                            >
                                <i class="fa-solid fa-paper-plane"></i> <span x-text="targetType === 'all' ? 'Xác nhận Phát thông báo (Broadcast)' : 'Gửi Thông báo'">Gửi Thông báo</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Khởi tạo TomSelect bằng JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo TomSelect cho hộp chọn User
            // Cho phép tìm kiếm, tự động thu gọn, không cho tạo mới
            new TomSelect('#user-select', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                maxOptions: 50, // Chỉ hiển thị tối đa 50 kết quả lúc tìm kiếm để đỡ lag
                placeholder: "Gõ Tên, SĐT hoặc Email để tìm..."
            });
        });
    </script>
</x-layouts.admin>
