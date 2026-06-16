<x-layouts.admin title="Cài đặt Hệ thống">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Cài đặt Hệ thống</h2>
        <p class="text-gray-500 mt-1">Cấu hình các thông số cốt lõi và quy định hoạt động của Phòng khám.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 text-green-800 rounded-lg p-4 flex items-center border border-green-200">
            <i class="fa-solid fa-circle-check text-green-500 mr-3 text-lg"></i>
            <span class="flex-1 text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div x-data="{ activeTab: 'general' }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Tabs Navigation -->
        <div class="flex border-b border-gray-100 overflow-x-auto hide-scrollbar">
            <button @click="activeTab = 'general'" 
                class="px-6 py-4 text-sm font-medium transition-colors border-b-2 whitespace-nowrap"
                :class="activeTab === 'general' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                <i class="fa-solid fa-house-chimney-medical mr-2"></i> Thông tin Phòng khám
            </button>
            <button @click="activeTab = 'booking'" 
                class="px-6 py-4 text-sm font-medium transition-colors border-b-2 whitespace-nowrap"
                :class="activeTab === 'booking' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                <i class="fa-regular fa-calendar-check mr-2"></i> Quy định Đặt lịch
            </button>
            <button @click="activeTab = 'social'" 
                class="px-6 py-4 text-sm font-medium transition-colors border-b-2 whitespace-nowrap"
                :class="activeTab === 'social' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                <i class="fa-solid fa-share-nodes mr-2"></i> Mạng xã hội & Tích hợp
            </button>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- TAB 1: GENERAL -->
            <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Cột 1 -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Phòng khám <span class="text-red-500">*</span></label>
                            <input type="text" name="settings[clinic_name]" value="{{ $settings['clinic_name'] ?? 'Carebook Clinic' }}" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Liên hệ</label>
                            <input type="email" name="settings[contact_email]" value="{{ $settings['contact_email'] ?? '' }}" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="contact@carebook.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại Hotline</label>
                            <input type="text" name="settings[contact_phone]" value="{{ $settings['contact_phone'] ?? '' }}" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="1900 1234">
                        </div>
                    </div>
                    
                    <!-- Cột 2 -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ Cơ sở</label>
                            <textarea name="settings[address]" rows="3" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Ví dụ: Số 123 Đường ABC, Quận XYZ, TP.HCM">{{ $settings['address'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Khung giờ hoạt động hiển thị</label>
                            <input type="text" name="settings[working_hours]" value="{{ $settings['working_hours'] ?? 'T2 - T6: 08:00 - 17:00' }}" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="T2 - T6: 08:00 - 17:00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo Phòng khám</label>
                            <div class="flex items-center gap-4">
                                @if(isset($settings['logo']) && $settings['logo'] !== '')
                                    <div class="w-16 h-16 rounded-lg border border-gray-200 overflow-hidden bg-gray-50 flex-shrink-0 flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                    </div>
                                @endif
                                <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Định dạng khuyên dùng: PNG nền trong suốt, kích thước tỷ lệ 1:1 hoặc 3:1.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: BOOKING RULES -->
            <div x-show="activeTab === 'booking'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="space-y-6">
                    <div class="bg-blue-50/50 p-5 rounded-xl border border-blue-100">
                        <div class="flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fa-solid fa-users-viewfinder text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <label class="block text-base font-bold text-gray-900 mb-1">Sức chứa mỗi ca khám (Max Patients/Slot)</label>
                                <p class="text-xs text-gray-500 mb-3">Số lượng bệnh nhân tối đa có thể đặt cùng một khung giờ (Ví dụ: 8h00 - 8h30 có tối đa 5 người).</p>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="settings[max_appointment_per_slot]" value="{{ $settings['max_appointment_per_slot'] ?? 5 }}" min="1" max="50" class="block w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-center">
                                    <span class="text-sm font-medium text-gray-600">bệnh nhân / 1 slot</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <div class="flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fa-solid fa-hourglass-start text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <label class="block text-base font-bold text-gray-900 mb-1">Thời gian đặt trước tối thiểu (Advance Booking)</label>
                                <p class="text-xs text-gray-500 mb-3">Bệnh nhân phải đặt lịch trước giờ khám ít nhất bao nhiêu tiếng để phòng khám kịp chuẩn bị.</p>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="settings[booking_advance_hours]" value="{{ $settings['booking_advance_hours'] ?? 2 }}" min="0" max="72" class="block w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-center">
                                    <span class="text-sm font-medium text-gray-600">tiếng</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-50/50 p-5 rounded-xl border border-orange-100">
                        <div class="flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fa-solid fa-ban text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <label class="block text-base font-bold text-gray-900 mb-1">Thời gian cho phép hủy lịch (Cancellation Policy)</label>
                                <p class="text-xs text-gray-500 mb-3">Bệnh nhân chỉ được phép tự hủy lịch hẹn trên web trước giờ khám bao nhiêu tiếng.</p>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="settings[cancel_before_hours]" value="{{ $settings['cancel_before_hours'] ?? 12 }}" min="0" max="168" class="block w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-center">
                                    <span class="text-sm font-medium text-gray-600">tiếng</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: SOCIAL -->
            <div x-show="activeTab === 'social'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fa-brands fa-facebook text-blue-600 mr-1"></i> Đường dẫn Fanpage Facebook</label>
                        <input type="url" name="settings[facebook_url]" value="{{ $settings['facebook_url'] ?? '' }}" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="https://facebook.com/carebook">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fa-solid fa-comment-dots text-blue-400 mr-1"></i> Zalo ZOA / Nhóm Zalo</label>
                        <input type="url" name="settings[zalo_url]" value="{{ $settings['zalo_url'] ?? '' }}" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="https://zalo.me/...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fa-solid fa-map-location-dot text-green-600 mr-1"></i> Google Maps Embed Link (Tùy chọn)</label>
                        <textarea name="settings[google_maps_iframe]" rows="3" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder='<iframe src="..."></iframe>'>{{ $settings['google_maps_iframe'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-5 border-t border-gray-100">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-save"></i> Lưu toàn bộ cấu hình
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
