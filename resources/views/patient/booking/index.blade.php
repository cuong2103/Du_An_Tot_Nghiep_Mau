<x-layouts.client title="Đặt lịch khám">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="bookingApp()">
        <!-- Progress indicator -->
        <div class="flex items-center justify-center gap-0 mb-8">
            <div class="flex items-center" :class="step >= 1 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm"
                     :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">1</div>
                <span class="ml-2 text-sm font-medium hidden sm:block">Chuyên khoa</span>
            </div>
            <div class="w-16 h-0.5 mx-2" :class="step >= 2 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            <div class="flex items-center" :class="step >= 2 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm"
                     :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">2</div>
                <span class="ml-2 text-sm font-medium hidden sm:block">Bác sĩ & Thời gian</span>
            </div>
            <div class="w-16 h-0.5 mx-2" :class="step >= 3 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            <div class="flex items-center" :class="step >= 3 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm"
                     :class="step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">3</div>
                <span class="ml-2 text-sm font-medium hidden sm:block">Thông tin</span>
            </div>
            <div class="w-16 h-0.5 mx-2" :class="step >= 4 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            <div class="flex items-center" :class="step >= 4 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm"
                     :class="step >= 4 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">4</div>
                <span class="ml-2 text-sm font-medium hidden sm:block">Xác nhận</span>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-exclamation mt-1"></i>
                <div>
                    <span class="block sm:inline font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <!-- BƯỚC 1 -->
            <div x-show="step === 1" x-transition>
                <h2 class="text-xl font-bold mb-6">Chọn chuyên khoa</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="specialty in specialties" :key="specialty.id">
                        <button @click="selectSpecialty(specialty)"
                                class="p-4 border-2 rounded-xl text-left hover:border-blue-600 hover:bg-blue-50 transition-all"
                                :class="selectedSpecialty?.id === specialty.id ? 'border-blue-600 bg-blue-50' : 'border-gray-200'">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                                <i class="fa-solid fa-stethoscope text-blue-600 text-xl"></i>
                            </div>
                            <p class="font-semibold text-gray-800" x-text="specialty.name"></p>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2" x-text="specialty.description"></p>
                        </button>
                    </template>
                </div>
            </div>

            <!-- BƯỚC 2 -->
            <div x-show="step === 2" x-transition style="display: none;">
                <div class="flex items-center gap-3 mb-6">
                    <button @click="step = 1" class="text-blue-600 hover:underline">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Đổi chuyên khoa
                    </button>
                    <span class="text-gray-400">/</span>
                    <span class="font-semibold" x-text="selectedSpecialty?.name"></span>
                </div>

                <div x-show="loadingDoctors" class="text-center py-12">
                    <i class="fa-solid fa-spinner fa-spin text-blue-600 text-3xl"></i>
                    <p class="mt-2 text-gray-500">Đang tải danh sách bác sĩ...</p>
                </div>

                <div x-show="!loadingDoctors">
                    <h3 class="font-semibold mb-3">Chọn bác sĩ</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <template x-for="doctor in doctors" :key="doctor.id">
                            <div @click="selectDoctor(doctor)"
                                 class="p-4 border-2 rounded-xl cursor-pointer transition-all"
                                 :class="selectedDoctor?.id === doctor.id ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-blue-700 font-bold text-sm" x-text="doctor.full_title.split(' ').slice(-2).map(w => w[0]).join('').toUpperCase()"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-800" x-text="doctor.full_title"></p>
                                        <p class="text-sm text-blue-600" x-text="doctor.level"></p>
                                        <p class="text-sm text-gray-500 mt-1" x-text="doctor.experience_years ? doctor.experience_years + ' năm kinh nghiệm' : ''"></p>
                                    </div>
                                    <i x-show="selectedDoctor?.id === doctor.id"
                                       class="fa-solid fa-circle-check text-blue-600 text-xl flex-shrink-0"></i>
                                </div>
                            </div>
                        </template>

                        <div x-show="doctors.length === 0 && !loadingDoctors" class="col-span-2 text-center py-8 text-gray-500">
                            <i class="fa-solid fa-user-doctor text-4xl mb-3 block"></i>
                            Chưa có bác sĩ nào cho chuyên khoa này.
                        </div>
                    </div>
                </div>

                <div x-show="selectedDoctor" class="mt-6">
                    <h3 class="font-semibold mb-3">Chọn ngày khám</h3>
                    <input type="date"
                           x-model="selectedDate"
                           :min="minDate"
                           :max="maxDate"
                           @change="loadSlots()"
                           class="border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none w-full sm:w-auto text-gray-700">
                    <p class="text-sm text-gray-500 mt-2" x-show="selectedDate" x-text="'Ngày ' + new Date(selectedDate).toLocaleDateString('vi-VN') + ' (' + getDayName(selectedDate) + ')'"></p>
                </div>

                <div x-show="selectedDate" class="mt-6">
                    <h3 class="font-semibold mb-3">Chọn giờ khám</h3>

                    <div x-show="loadingSlots" class="text-center py-4">
                        <i class="fa-solid fa-spinner fa-spin text-blue-600"></i> Đang tải slot...
                    </div>

                    <div x-show="!loadingSlots && slots.length > 0" class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                        <template x-for="slot in slots" :key="slot.time">
                            <button @click="slot.available && (selectedSlot = slot.time)"
                                    :disabled="!slot.available"
                                    class="py-2 px-3 rounded-xl text-sm font-medium border transition-all"
                                    :class="{
                                        'bg-blue-600 text-white border-blue-600 shadow-md': selectedSlot === slot.time,
                                        'bg-white border-gray-200 text-gray-700 hover:border-blue-300 hover:bg-blue-50': slot.available && selectedSlot !== slot.time,
                                        'bg-gray-50 border-gray-100 text-gray-400 cursor-not-allowed line-through': !slot.available
                                    }"
                                    x-text="slot.time">
                            </button>
                        </template>
                    </div>

                    <div x-show="!loadingSlots && slots.length === 0" class="text-center py-6 text-gray-500 bg-gray-50 rounded-xl border border-gray-100">
                        <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block"></i>
                        Không có lịch khám vào ngày này. Vui lòng chọn ngày khác.
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button @click="step = 1" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Quay lại
                    </button>
                    <button @click="canGoStep3() && (step = 3)"
                            :disabled="!canGoStep3()"
                            class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md transition-all">
                        Tiếp tục <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- BƯỚC 3 -->
            <div x-show="step === 3" x-transition style="display: none;">
                <h2 class="text-xl font-bold mb-6">Thông tin đặt lịch</h2>

                <div class="mb-8" x-show="isAuth">
                    <h3 class="font-semibold text-gray-800 mb-4">Đặt lịch cho ai? <span class="text-red-500">*</span></h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <template x-for="profile in profiles" :key="profile.id">
                            <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all"
                                   :class="selectedProfileId === profile.id ? 'border-blue-600 bg-blue-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="patient_profile_id" :value="profile.id"
                                       x-model="selectedProfileId" class="sr-only">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-user text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900" x-text="profile.full_name"></p>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        <span x-text="profile.is_self ? 'Bản thân' : 'Người thân'"></span>
                                        <span x-show="profile.date_of_birth" x-text="' • ' + (new Date().getFullYear() - new Date(profile.date_of_birth).getFullYear()) + ' tuổi'"></span>
                                    </p>
                                </div>
                                <i x-show="selectedProfileId === profile.id"
                                   class="fa-solid fa-circle-check text-blue-600 text-xl"></i>
                            </label>
                        </template>

                        <a href="{{ route('client.account.index') }}"
                           class="flex items-center gap-3 p-4 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-600 hover:bg-blue-50 transition-all text-center justify-center sm:justify-start">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fa-solid fa-plus text-gray-500"></i>
                            </div>
                            <span class="text-gray-600 font-medium">Thêm hồ sơ mới</span>
                        </a>
                    </div>

                    <div x-show="profiles.length === 0" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 flex items-center">
                        <i class="fa-solid fa-circle-info text-blue-600 mr-3 text-lg"></i>
                        <div>
                            Bạn chưa có hồ sơ bệnh nhân. <a href="{{ route('client.account.index') }}" class="underline font-semibold">Tạo hồ sơ ngay</a>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block font-semibold text-gray-800 mb-2">Lý do khám / Triệu chứng <span class="text-red-500">*</span></label>
                    <textarea x-model="reason"
                              rows="4"
                              placeholder="Mô tả triệu chứng, lý do đến khám... (tối thiểu 10 ký tự)"
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 resize-none outline-none text-gray-700"></textarea>
                    <p class="text-sm text-gray-400 mt-1" :class="reason.length < 10 ? 'text-red-500' : ''" x-text="reason.length + ' ký tự'"></p>
                </div>

                <div class="mt-8 flex justify-between">
                    <button @click="step = 2" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Quay lại
                    </button>
                    
                    <template x-if="isAuth">
                        <button @click="canSubmit() && (step = 4)"
                                :disabled="!canSubmit()"
                                class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md transition-all">
                            Xem xác nhận <i class="fa-solid fa-arrow-right ml-2"></i>
                        </button>
                    </template>
                    
                    <template x-if="!isAuth">
                        <button @click="saveStateAndLogin()"
                                :disabled="reason.length < 10"
                                class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-xl hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md transition-all">
                            Đăng nhập để tiếp tục <i class="fa-solid fa-arrow-right ml-2"></i>
                        </button>
                    </template>
                </div>
            </div>

            <!-- BƯỚC 4 -->
            <div x-show="step === 4" x-transition style="display: none;">
                <h2 class="text-xl font-bold mb-6 text-center">Xác nhận thông tin đặt lịch</h2>

                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 sm:p-8 mb-8 max-w-2xl mx-auto space-y-5">
                    <div class="flex items-start gap-4 pb-5 border-b border-blue-200/60">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fa-solid fa-stethoscope text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Chuyên khoa</p>
                            <p class="font-bold text-gray-900 text-lg" x-text="selectedSpecialty?.name"></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 pb-5 border-b border-blue-200/60">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fa-solid fa-user-doctor text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Bác sĩ</p>
                            <p class="font-bold text-gray-900 text-lg" x-text="selectedDoctor?.full_title"></p>
                            <p class="text-sm text-blue-600" x-text="selectedDoctor?.level"></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 pb-5 border-b border-blue-200/60">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fa-solid fa-calendar-check text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Ngày & Giờ khám</p>
                            <p class="font-bold text-blue-600 text-lg" x-text="getDayName(selectedDate) + ', ' + new Date(selectedDate).toLocaleDateString('vi-VN') + ' lúc ' + selectedSlot"></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 pb-5 border-b border-blue-200/60">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fa-solid fa-user text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Bệnh nhân</p>
                            <p class="font-bold text-gray-900 text-lg" x-text="profiles.find(p => p.id === selectedProfileId)?.full_name"></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fa-solid fa-clipboard text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Lý do khám</p>
                            <p class="font-medium text-gray-800" x-text="reason"></p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('booking.store') }}" id="booking-form">
                    @csrf
                    <input type="hidden" name="specialty_id" :value="selectedSpecialty?.id">
                    <input type="hidden" name="doctor_profile_id" :value="selectedDoctor?.id">
                    <input type="hidden" name="patient_profile_id" :value="selectedProfileId">
                    <input type="hidden" name="appointment_date" :value="selectedDate">
                    <input type="hidden" name="appointment_time" :value="selectedSlot">
                    <input type="hidden" name="reason" :value="reason">
                </form>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl mb-8 text-sm text-blue-800 max-w-2xl mx-auto flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-blue-600 mt-0.5 text-lg"></i>
                    <div>
                        Vui lòng đến trước giờ khám ít nhất <strong>15 phút</strong> để làm thủ tục. Nếu cần thay đổi hoặc huỷ lịch, vui lòng thao tác trước giờ khám 2 tiếng.
                    </div>
                </div>

                <div class="flex justify-center gap-4">
                    <button @click="step = 3" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Quay lại
                    </button>
                    <button @click="document.getElementById('booking-form').submit()"
                            x-data="{loading: false}"
                            @click="loading = true"
                            :disabled="loading"
                            class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 disabled:opacity-70 shadow-lg hover:shadow-xl transition-all">
                        <i x-show="!loading" class="fa-solid fa-calendar-check mr-2"></i>
                        <i x-show="loading" class="fa-solid fa-spinner fa-spin mr-2" style="display: none;"></i>
                        <span x-text="loading ? 'Đang đặt lịch...' : 'Xác nhận đặt lịch'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bookingApp() {
            return {
                isAuth: {{ auth()->check() ? 'true' : 'false' }},
                step: 1,
                // Bước 1
                selectedSpecialty: null,
                specialties: {!! $specialties->toJson() !!},

                // Bước 2
                doctors: [],
                selectedDoctor: null,
                selectedDate: '',
                slots: [],
                selectedSlot: null,
                loadingDoctors: false,
                loadingSlots: false,
                minDate: '{{ now()->format('Y-m-d') }}',
                maxDate: '{{ now()->addMonths(2)->format('Y-m-d') }}',

                // Bước 3
                profiles: {!! auth()->check() ? auth()->user()->patientProfiles->toJson() : '[]' !!},
                selectedProfileId: null,
                reason: '',

                init() {
                    // Watch for changes to save state
                    this.$watch('selectedSpecialty', val => this.saveState());
                    this.$watch('selectedDoctor', val => this.saveState());
                    this.$watch('selectedDate', val => this.saveState());
                    this.$watch('selectedSlot', val => this.saveState());
                    this.$watch('reason', val => this.saveState());

                    // Restore state from sessionStorage if available
                    const savedState = sessionStorage.getItem('bookingState');
                    if (savedState) {
                        try {
                            const state = JSON.parse(savedState);
                            this.restoreState(state);
                            return; // Stop here, restoreState handles preselecting logic
                        } catch(e) {
                            console.error('Lỗi khi khôi phục tiến trình đặt lịch', e);
                        }
                    }

                    // Normal flow (deep link from URL)
                    let preselectedSpecialtyId = {{ $selectedSpecialtyId ?? 'null' }};
                    let preselectedDoctorId = {{ $selectedDoctorId ?? 'null' }};
                    
                    if (preselectedSpecialtyId) {
                        let specialty = this.specialties.find(s => s.id === preselectedSpecialtyId);
                        if (specialty) {
                            this.selectSpecialty(specialty);
                        }
                    }
                },

                async restoreState(state) {
                    if (state.selectedSpecialtyId) {
                        let specialty = this.specialties.find(s => s.id === state.selectedSpecialtyId);
                        if (specialty) {
                            this.selectedSpecialty = specialty;
                            await this.loadDoctors(specialty.id);
                            
                            if (state.selectedDoctorId) {
                                let doc = this.doctors.find(d => d.id === state.selectedDoctorId);
                                if (doc) {
                                    this.selectedDoctor = doc;
                                    this.selectedDate = state.selectedDate || '';
                                    if (this.selectedDate) {
                                        await this.loadSlots();
                                        if (state.selectedSlot) {
                                            this.selectedSlot = state.selectedSlot;
                                            if (this.canGoStep3()) {
                                                this.step = 3;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (state.reason) {
                        this.reason = state.reason;
                    }
                },

                saveState() {
                    const state = {
                        selectedSpecialtyId: this.selectedSpecialty?.id,
                        selectedDoctorId: this.selectedDoctor?.id,
                        selectedDate: this.selectedDate,
                        selectedSlot: this.selectedSlot,
                        reason: this.reason
                    };
                    sessionStorage.setItem('bookingState', JSON.stringify(state));
                },

                saveStateAndLogin() {
                    this.saveState();
                    window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent('/dat-lich');
                },

                selectSpecialty(specialty) {
                    this.selectedSpecialty = specialty;
                    this.selectedDoctor = null;
                    this.doctors = [];
                    this.selectedDate = '';
                    this.slots = [];
                    this.selectedSlot = null;
                    this.loadDoctors(specialty.id);
                    this.step = 2;
                },

                async loadDoctors(specialtyId) {
                    this.loadingDoctors = true;
                    try {
                        const res = await fetch('/dat-lich/bac-si?specialty_id=' + specialtyId);
                        const data = await res.json();
                        this.doctors = data.doctors;
                        
                        // Auto select doctor if preselected
                        let preselectedDoctorId = {{ $selectedDoctorId ?? 'null' }};
                        if (preselectedDoctorId && this.doctors.length > 0) {
                            let doc = this.doctors.find(d => d.id === preselectedDoctorId);
                            if (doc) this.selectDoctor(doc);
                        }
                    } catch (error) {
                        console.error('Lỗi khi tải danh sách bác sĩ:', error);
                    } finally {
                        this.loadingDoctors = false;
                    }
                },

                selectDoctor(doctor) {
                    this.selectedDoctor = doctor;
                    this.selectedDate = '';
                    this.slots = [];
                    this.selectedSlot = null;
                },

                async loadSlots() {
                    if (!this.selectedDoctor || !this.selectedDate) return;
                    this.loadingSlots = true;
                    this.slots = [];
                    this.selectedSlot = null;
                    try {
                        const res = await fetch('/dat-lich/slots?doctor_id=' + this.selectedDoctor.id + '&date=' + this.selectedDate);
                        const data = await res.json();
                        this.slots = data.slots;
                    } catch (error) {
                        console.error('Lỗi khi tải slot:', error);
                    } finally {
                        this.loadingSlots = false;
                    }
                },

                canGoStep3() {
                    return this.selectedDoctor && this.selectedDate && this.selectedSlot;
                },

                canSubmit() {
                    return this.selectedProfileId && this.reason.length >= 10;
                },

                getDayName(dateStr) {
                    const days = ['Chủ Nhật','Thứ Hai','Thứ Ba','Thứ Tư','Thứ Năm','Thứ Sáu','Thứ Bảy'];
                    return days[new Date(dateStr).getDay()];
                }
            }
        }
    </script>
</x-layouts.client>
