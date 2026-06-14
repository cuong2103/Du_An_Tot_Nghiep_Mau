<x-layouts.admin title="Thêm bác sĩ mới">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('admin.doctors.index') }}" class="hover:text-blue-600 transition-colors">Bác sĩ</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-gray-900 font-medium">Thêm mới</span>
        </div>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.doctors.index') }}" class="text-gray-400 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Thêm bác sĩ mới</h2>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.doctors.store') }}" method="POST" x-data="{ loading: false, selectedSpecialties: {{ json_encode(old('specialty_ids', [])) }} }" @submit="loading = true">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Cột trái: Thông tin tài khoản -->
            <div class="xl:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900"><i class="fa-solid fa-user-lock mr-2 text-gray-500"></i> Thông tin đăng nhập</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="VD: Nguyễn Văn An">
                            @error('full_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="Dùng để đăng nhập">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="Email liên hệ (tuỳ chọn)">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ showPass: false }">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input :type="showPass ? 'text' : 'password'" id="password" name="password" required minlength="8"
                                    class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="Ít nhất 8 ký tự">
                                <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <i class="fa-solid" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Hồ sơ chuyên môn -->
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900"><i class="fa-solid fa-user-doctor mr-2 text-indigo-500"></i> Thông tin chuyên môn</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="doctor_code" class="block text-sm font-medium text-gray-700 mb-1">Mã bác sĩ <span class="text-red-500">*</span></label>
                                <input type="text" id="doctor_code" name="doctor_code" value="{{ old('doctor_code') }}" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none" placeholder="VD: BS001">
                                @error('doctor_code')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Cấp độ <span class="text-red-500">*</span></label>
                                <select id="level" name="level" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none bg-white">
                                    @foreach(['BS', 'BSCK1', 'BSCK2', 'ThS', 'TS', 'PGS', 'GS'] as $lvl)
                                        <option value="{{ $lvl }}" {{ old('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                    @endforeach
                                </select>
                                @error('level')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="academic_title" class="block text-sm font-medium text-gray-700 mb-1">Học hàm / Học vị</label>
                                <input type="text" id="academic_title" name="academic_title" value="{{ old('academic_title') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none" placeholder="VD: TS., PGS.TS.">
                                @error('academic_title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1">Số năm kinh nghiệm</label>
                                <div class="relative">
                                    <input type="number" id="experience_years" name="experience_years" value="{{ old('experience_years') }}" min="0" max="60"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none pr-12">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-500 text-sm">
                                        năm
                                    </div>
                                </div>
                                @error('experience_years')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">Số chứng chỉ hành nghề</label>
                                <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none" placeholder="Mã CCHN (nếu có)">
                                @error('license_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-6 mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Chuyên khoa đảm nhiệm <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-2">
                                @foreach($specialties as $sp)
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="specialty_ids[]" value="{{ $sp->id }}" x-model="selectedSpecialties"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $sp->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('specialty_ids')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t border-gray-100 pt-6 mb-6" x-show="selectedSpecialties.length > 0" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Chọn Chuyên khoa CHÍNH <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($specialties as $sp)
                                    <label class="inline-flex items-center cursor-pointer" x-show="selectedSpecialties.includes('{{ $sp->id }}')" style="display: none;">
                                        <input type="radio" name="primary_specialty_id" value="{{ $sp->id }}" {{ old('primary_specialty_id') == $sp->id ? 'checked' : '' }}
                                            class="border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700 font-medium">{{ $sp->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('primary_specialty_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t border-gray-100 pt-6 space-y-6">
                            <div>
                                <label for="expertise" class="block text-sm font-medium text-gray-700 mb-1">Lĩnh vực chuyên trị</label>
                                <textarea id="expertise" name="expertise" rows="3"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none" placeholder="Các mặt bệnh, kỹ thuật nổi bật...">{{ old('expertise') }}</textarea>
                                @error('expertise')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Giới thiệu bản thân (Bio)</label>
                                <textarea id="bio" name="bio" rows="4"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none" placeholder="Quá trình công tác, đào tạo...">{{ old('bio') }}</textarea>
                                @error('bio')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6">
                    <a href="{{ route('admin.doctors.index') }}" class="px-5 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        Huỷ
                    </a>
                    <button type="submit" :disabled="loading"
                        class="px-5 py-2.5 border border-transparent bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-2">
                        <span x-show="!loading"><i class="fa-solid fa-save mr-1"></i> Lưu bác sĩ</span>
                        <span x-show="loading" style="display: none;"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang xử lý...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>
