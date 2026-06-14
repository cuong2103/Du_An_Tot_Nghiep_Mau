<x-layouts.admin title="Gửi thông báo mới">
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
            <a href="{{ route('admin.notifications.index') }}" class="hover:text-blue-600 transition-colors">Thông báo</a>
            <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
            <span class="text-gray-800 font-medium">Gửi mới</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Gửi thông báo mới</h2>
    </div>

    <!-- Error Summary -->
    @if($errors->any())
        <div class="bg-red-50 text-red-800 p-4 rounded-lg mb-6 border border-red-200">
            <div class="flex items-center gap-2 mb-2 font-bold">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                Có lỗi xảy ra, vui lòng kiểm tra lại:
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-800 p-4 rounded-lg mb-6 flex items-center gap-3 border border-red-200">
            <i class="fa-solid fa-circle-exclamation text-red-500"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-4xl mx-auto" x-data="{
        target: 'single',
        title: '{{ old('title') }}',
        content: '{{ old('content') }}',
        type: '{{ old('type', 'system') }}',
        channel: '{{ old('channel', 'in_web') }}',
        confirmSubmit(e) {
            if (this.target === 'all') {
                if(!confirm('CẢNH BÁO: Bạn đang chuẩn bị gửi thông báo này cho TẤT CẢ người dùng trong hệ thống. Bạn có chắc chắn muốn tiếp tục không?')) {
                    e.preventDefault();
                }
            } else {
                if(!confirm('Bạn có chắc chắn muốn gửi thông báo này?')) {
                    e.preventDefault();
                }
            }
        }
    }">
        <form action="{{ route('admin.notifications.store') }}" method="POST" @submit="confirmSubmit">
            @csrf
            
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Cột Form chính -->
                <div class="w-full md:w-2/3 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Người nhận</h3>
                        
                        <div class="space-y-4">
                            <div class="flex flex-col sm:flex-row gap-4 sm:items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="target" value="single" x-model="target" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-900 font-medium">Một người cụ thể</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="target" value="role" x-model="target" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-900 font-medium">Theo vai trò</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="target" value="all" x-model="target" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-900 font-medium text-red-600">Tất cả người dùng</span>
                                </label>
                            </div>

                            <div x-show="target === 'single'" class="pt-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chọn người dùng <span class="text-red-500">*</span></label>
                                <select name="user_id" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                    <option value="">-- Tìm và chọn người dùng --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }} - {{ $user->phone }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="target === 'role'" class="pt-2" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chọn vai trò nhận <span class="text-red-500">*</span></label>
                                <select name="role" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                    <option value="">-- Chọn vai trò --</option>
                                    <option value="patient" {{ old('role') === 'patient' ? 'selected' : '' }}>Bệnh nhân (Patient)</option>
                                    <option value="doctor" {{ old('role') === 'doctor' ? 'selected' : '' }}>Bác sĩ (Doctor)</option>
                                    <option value="receptionist" {{ old('role') === 'receptionist' ? 'selected' : '' }}>Lễ tân (Receptionist)</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                                </select>
                            </div>

                            <div x-show="target === 'all'" class="pt-2" style="display: none;">
                                <div class="bg-yellow-50 text-yellow-800 p-4 rounded-lg border border-yellow-200 flex items-start gap-3">
                                    <i class="fa-solid fa-triangle-exclamation text-yellow-600 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-bold">Lưu ý cực kỳ quan trọng</p>
                                        <p class="text-xs mt-1">Thông báo này sẽ được gửi đến <strong>TẤT CẢ</strong> người dùng đang hoạt động trong hệ thống. Hành động này không thể hoàn tác. Hãy chắc chắn nội dung phù hợp cho mọi đối tượng trước khi gửi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Nội dung thông báo</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề thông báo <span class="text-red-500">*</span></label>
                                <input type="text" name="title" x-model="title" required placeholder="Nhập tiêu đề..." class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm font-medium outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung <span class="text-red-500">*</span></label>
                                <textarea name="content" x-model="content" required rows="5" placeholder="Nội dung chi tiết của thông báo..." class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none"></textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại thông báo <span class="text-red-500">*</span></label>
                                    <select name="type" x-model="type" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                        <option value="system">Thông báo hệ thống</option>
                                        <option value="appointment">Cập nhật Lịch hẹn</option>
                                        <option value="result">Trả Kết quả</option>
                                        <option value="reminder">Nhắc nhở</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kênh gửi <span class="text-red-500">*</span></label>
                                    <select name="channel" x-model="channel" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                        <option value="in_web">Trong web (Notification Bell)</option>
                                        <option value="email">Qua Email</option>
                                        <option value="zalo">Qua Zalo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột Preview (1/3) -->
                <div class="w-full md:w-1/3 space-y-6">
                    <div class="bg-gray-100 rounded-xl border border-gray-200 p-4 sticky top-6">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 text-center">Bản xem trước (Preview)</h3>
                        
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 relative overflow-hidden">
                            <div class="flex gap-3">
                                <div class="shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-lg"
                                     :class="{
                                         'bg-blue-100 text-blue-600': type === 'appointment',
                                         'bg-green-100 text-green-600': type === 'result',
                                         'bg-gray-100 text-gray-600': type === 'system',
                                         'bg-yellow-100 text-yellow-600': type === 'reminder'
                                     }">
                                    <i class="fa-solid fa-calendar-check" x-show="type === 'appointment'"></i>
                                    <i class="fa-solid fa-file-medical" x-show="type === 'result'" style="display: none;"></i>
                                    <i class="fa-solid fa-bell" x-show="type === 'system'" style="display: none;"></i>
                                    <i class="fa-solid fa-clock" x-show="type === 'reminder'" style="display: none;"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 leading-tight" x-text="title || 'Tiêu đề thông báo...'"></p>
                                    <p class="text-xs text-gray-600 mt-1 line-clamp-3" x-text="content || 'Nội dung chi tiết sẽ hiển thị ở đây...'"></p>
                                    <div class="text-[10px] text-gray-400 mt-2 font-mono flex items-center gap-1">
                                        <i class="fa-regular fa-clock"></i> Vừa xong
                                    </div>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4 h-2 w-2 bg-blue-500 rounded-full"></div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-500">
                                Gửi qua kênh: <span class="font-bold text-gray-700" x-text="channel === 'in_web' ? 'Trong web' : (channel === 'email' ? 'Email' : 'Zalo')"></span>
                            </p>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200 flex items-center gap-3">
                            <a href="{{ route('admin.notifications.index') }}" class="flex-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-center py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm">
                                Huỷ bỏ
                            </a>
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> Gửi đi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</x-layouts.admin>
