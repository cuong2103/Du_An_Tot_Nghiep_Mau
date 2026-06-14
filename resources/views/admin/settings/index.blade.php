<x-layouts.admin title="Cài đặt hệ thống">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Cài đặt hệ thống</h2>
            <p class="text-gray-500 mt-1">Quản lý cấu hình chung và tham số hệ thống</p>
        </div>
        <div>
            <a href="{{ route('admin.settings.logs') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-2 hover:underline">
                Xem nhật ký hệ thống <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Alert -->
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

    <div x-data="{ tab: 'general' }">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="tab = 'general'" :class="tab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors">
                    <i class="fa-solid fa-building"></i> Chung
                </button>
                <button @click="tab = 'contact'" :class="tab === 'contact' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors">
                    <i class="fa-solid fa-address-book"></i> Liên hệ
                </button>
                <button @click="tab = 'system'" :class="tab === 'system' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors">
                    <i class="fa-solid fa-sliders"></i> Cấu hình Đặt lịch
                </button>
            </nav>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <!-- TAB: CHUNG -->
                <div x-show="tab === 'general'" class="p-6 space-y-6">
                    <div class="max-w-2xl">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin cơ bản</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên hệ thống</label>
                                <input type="text" name="settings[site.name]" value="{{ $settings['site.name'] ?? 'CareBook' }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tagline (Khẩu hiệu)</label>
                                <input type="text" name="settings[site.tagline]" value="{{ $settings['site.tagline'] ?? '' }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">URL Logo hệ thống</label>
                                <input type="url" name="settings[site.logo_url]" value="{{ $settings['site.logo_url'] ?? '' }}" placeholder="https://..." class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none font-mono">
                            </div>

                            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Chế độ bảo trì</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Kích hoạt để tạm dừng truy cập từ người dùng (chỉ Admin mới có thể đăng nhập).</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="settings[site.maintenance_mode]" value="0">
                                    <input type="checkbox" name="settings[site.maintenance_mode]" value="1" {{ ($settings['site.maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: LIÊN HỆ -->
                <div x-show="tab === 'contact'" class="p-6 space-y-6" style="display: none;">
                    <div class="max-w-2xl">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin liên hệ & hiển thị</h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hotline CSKH</label>
                                    <input type="text" name="settings[contact.hotline]" value="{{ $settings['contact.hotline'] ?? '' }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email hỗ trợ</label>
                                    <input type="email" name="settings[contact.email]" value="{{ $settings['contact.email'] ?? '' }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giờ làm việc</label>
                                <input type="text" name="settings[contact.working_hours]" value="{{ $settings['contact.working_hours'] ?? '07:00 - 17:00' }}" placeholder="VD: Thứ 2 - Thứ 7 (07:00 - 17:00)" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ phòng khám / Cơ sở</label>
                                <textarea name="settings[contact.address]" rows="3" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">{{ $settings['contact.address'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: HỆ THỐNG / ĐẶT LỊCH -->
                <div x-show="tab === 'system'" class="p-6 space-y-6" style="display: none;">
                    <div class="max-w-2xl">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Tham số đặt lịch khám</h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="w-1/2 pt-1">
                                    <label class="block text-sm font-medium text-gray-900">Số ngày tối đa đặt trước</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Bệnh nhân chỉ có thể chọn ngày trong khoảng này.</p>
                                </div>
                                <div class="w-1/2 relative">
                                    <input type="number" name="settings[booking.max_days_ahead]" value="{{ $settings['booking.max_days_ahead'] ?? '30' }}" min="1" class="block w-full py-2 px-3 pr-12 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-sm text-gray-500">ngày</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="w-1/2 pt-1">
                                    <label class="block text-sm font-medium text-gray-900">Thời gian tối thiểu báo huỷ</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Bệnh nhân không thể tự huỷ nếu còn cách giờ khám ít hơn mức này.</p>
                                </div>
                                <div class="w-1/2 relative">
                                    <input type="number" name="settings[booking.cancel_before_hours]" value="{{ $settings['booking.cancel_before_hours'] ?? '2' }}" min="0" class="block w-full py-2 px-3 pr-10 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-sm text-gray-500">giờ</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="w-1/2 pt-1">
                                    <label class="block text-sm font-medium text-gray-900">Thời gian giữ chỗ tạm (Slot hold)</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Thời gian slot bị khoá trong khi bệnh nhân đang điền form đặt lịch.</p>
                                </div>
                                <div class="w-1/2 relative">
                                    <input type="number" name="settings[booking.slot_hold_minutes]" value="{{ $settings['booking.slot_hold_minutes'] ?? '15' }}" min="1" class="block w-full py-2 px-3 pr-12 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-sm text-gray-500">phút</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Submit -->
            <div class="flex justify-start">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2" x-data="{ loading: false }" @click="loading = true; setTimeout(() => loading = false, 2000)">
                    <span x-show="!loading"><i class="fa-solid fa-floppy-disk"></i> Lưu cài đặt</span>
                    <span x-show="loading"><i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...</span>
                </button>
            </div>
            
        </form>
    </div>
</x-layouts.admin>
