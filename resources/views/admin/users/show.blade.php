<x-layouts.admin title="Chi tiết người dùng">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors mb-2 inline-block">
                <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại danh sách
            </a>
            <h2 class="text-2xl font-bold text-gray-900">Chi tiết người dùng</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-3 border border-green-200">
            <i class="fa-solid fa-circle-check text-green-500"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 flex items-center gap-3 border border-red-200">
            <i class="fa-solid fa-circle-exclamation text-red-500"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Cột trái (2/3) -->
        <div class="w-full lg:w-2/3 space-y-6">
            <!-- Card Thông tin cơ bản -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-5 pb-6 border-b border-gray-100">
                        <div class="h-16 w-16 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-2xl shadow-md shrink-0">
                            {{ $user->avatar_initials }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $user->full_name }}</h3>
                            <div class="flex items-center gap-3 mt-2">
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-red-100 text-red-800 border-red-200',
                                        'doctor' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'receptionist' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'patient' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    ];
                                    $roleClass = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $roleClass }}">
                                    {{ $user->display_role }}
                                </span>

                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <i class="fa-solid fa-circle text-[8px] mr-1.5 text-green-500"></i> Hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <i class="fa-solid fa-circle text-[8px] mr-1.5 text-red-500"></i> Đã khoá
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 pt-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-phone w-4 text-center text-gray-400"></i> Số điện thoại
                            </p>
                            <p class="mt-1 font-medium text-gray-900">{{ $user->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-envelope w-4 text-center text-gray-400"></i> Email
                            </p>
                            <p class="mt-1 font-medium text-gray-900">{{ $user->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <i class="fa-regular fa-id-card w-4 text-center text-gray-400"></i> CCCD/CMND
                            </p>
                            <p class="mt-1 font-medium text-gray-900">{{ $user->id_card ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-calendar-plus w-4 text-center text-gray-400"></i> Ngày tạo
                            </p>
                            <p class="mt-1 font-medium text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-sm font-medium text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-right-to-bracket w-4 text-center text-gray-400"></i> Đăng nhập lần cuối
                            </p>
                            <p class="mt-1 font-medium text-gray-900">
                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Chưa đăng nhập' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phân quyền riêng: Doctor -->
            @if($user->role === 'doctor' && $user->doctorProfile)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900"><i class="fa-solid fa-user-doctor mr-2 text-indigo-500"></i> Hồ sơ bác sĩ</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Mã BS</p>
                            <p class="mt-1 font-medium text-gray-900">{{ $user->doctorProfile->doctor_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Học hàm / Cấp độ</p>
                            <p class="mt-1 font-medium text-gray-900">
                                {{ $user->doctorProfile->academic_title ?? '—' }} / {{ $user->doctorProfile->level }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kinh nghiệm</p>
                            <p class="mt-1 font-medium text-gray-900">
                                {{ $user->doctorProfile->experience_years ? $user->doctorProfile->experience_years . ' năm' : '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Chứng chỉ hành nghề</p>
                            <p class="mt-1 font-medium text-gray-900">{{ $user->doctorProfile->license_number ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-500 mb-2">Chuyên khoa</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->doctorProfile->specialties as $specialty)
                                @if($specialty->pivot->is_primary)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        {{ $specialty->name }} (Chính)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $specialty->name }}
                                    </span>
                                @endif
                            @empty
                                <span class="text-sm text-gray-400 italic">Chưa gắn chuyên khoa</span>
                            @endforelse
                        </div>
                    </div>

                    @if($user->doctorProfile->bio)
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Tiểu sử / Giới thiệu</p>
                        <div class="p-4 bg-gray-50 rounded-lg text-sm text-gray-700 whitespace-pre-line border border-gray-100">
                            {{ $user->doctorProfile->bio }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Phân quyền riêng: Receptionist/Admin -->
            @if(in_array($user->role, ['receptionist', 'admin']) && $user->staffProfile)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900"><i class="fa-solid fa-id-badge mr-2 text-blue-500"></i> Thông tin nhân viên</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Mã NV</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $user->staffProfile->employee_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Chức vụ</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $user->staffProfile->position }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Phòng ban</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $user->staffProfile->department ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">SĐT nội bộ</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $user->staffProfile->internal_phone ?? '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Ngày vào làm</p>
                        <p class="mt-1 font-medium text-gray-900">
                            {{ $user->staffProfile->start_date ? $user->staffProfile->start_date->format('d/m/Y') : '—' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Phân quyền riêng: Patient -->
            @if($user->role === 'patient')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900"><i class="fa-solid fa-file-medical mr-2 text-gray-500"></i> Hồ sơ bệnh nhân</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $user->patientProfiles->count() }} hồ sơ</span>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-100">
                        @forelse($user->patientProfiles as $profile)
                            <li class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-semibold text-gray-900">{{ $profile->full_name }}</h4>
                                            @if($profile->is_self)
                                                <span class="bg-green-100 text-green-700 text-[10px] uppercase font-bold px-2 py-0.5 rounded border border-green-200">Bản thân</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-2 py-0.5 rounded border border-gray-200">Người thân</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $profile->gender === 'male' ? 'Nam' : ($profile->gender === 'female' ? 'Nữ' : 'Khác') }} • 
                                            Sinh ngày: {{ $profile->date_of_birth->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Chi tiết</button>
                                </div>
                            </li>
                        @empty
                            <li class="p-6 text-center text-gray-500 italic">Chưa có hồ sơ bệnh nhân nào.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <!-- Cột phải (1/3) -->
        <div class="w-full lg:w-1/3 space-y-6">
            <!-- Thao tác nhanh -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900"><i class="fa-solid fa-bolt mr-2 text-yellow-500"></i> Thao tác nhanh</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500 mb-1">Trạng thái hiện tại</p>
                        @if($user->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-200 w-full justify-center">
                                <i class="fa-solid fa-circle-check mr-2"></i> Tài khoản đang hoạt động
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-red-50 text-red-700 border border-red-200 w-full justify-center">
                                <i class="fa-solid fa-lock mr-2"></i> Tài khoản đã bị khoá
                            </span>
                        @endif
                    </div>

                    <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                            onclick="return confirm('Bạn có chắc muốn {{ $user->is_active ? 'khoá' : 'mở khoá' }} tài khoản này?')"
                            {{ $user->id === Auth::id() ? 'disabled' : '' }}
                            class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition-colors
                                {{ $user->id === Auth::id() ? 'bg-gray-300 cursor-not-allowed' : ($user->is_active ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500') }}
                                focus:outline-none focus:ring-2 focus:ring-offset-2">
                            @if($user->is_active)
                                <i class="fa-solid fa-lock mr-2"></i> Khoá tài khoản
                            @else
                                <i class="fa-solid fa-unlock mr-2"></i> Mở khoá tài khoản
                            @endif
                        </button>
                    </form>
                    @if($user->id === Auth::id())
                        <p class="text-xs text-center text-gray-500 mt-2">Không thể tự khoá tài khoản của chính mình.</p>
                    @endif
                </div>
            </div>

            <!-- Hoạt động gần đây -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900"><i class="fa-solid fa-clock-rotate-left mr-2 text-gray-500"></i> Hoạt động gần đây</h3>
                </div>
                <div class="p-6">
                    @if($systemLogs->count() > 0)
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($systemLogs as $idx => $log)
                                    <li>
                                        <div class="relative pb-8">
                                            @if($idx !== $systemLogs->count() - 1)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    @php
                                                        $actionColor = match($log->action) {
                                                            'USER_LOGIN' => 'text-blue-500 bg-white ring-8 ring-white',
                                                            'USER_LOGOUT' => 'text-gray-400 bg-white ring-8 ring-white',
                                                            'USER_LOCKED' => 'text-red-500 bg-white ring-8 ring-white',
                                                            'USER_UNLOCKED' => 'text-green-500 bg-white ring-8 ring-white',
                                                            default => 'text-gray-400 bg-white ring-8 ring-white'
                                                        };
                                                    @endphp
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center {{ $actionColor }}">
                                                        <i class="fa-solid fa-circle-dot text-sm"></i>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-900 font-medium">{{ $log->action }}</p>
                                                        @if($log->description)
                                                            <p class="text-xs text-gray-500 mt-0.5">{{ $log->description }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-xs whitespace-nowrap text-gray-500">
                                                        <time datetime="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-list-ul text-gray-400 text-lg"></i>
                            </div>
                            <p class="text-sm text-gray-500 italic">Chưa có hoạt động nào được ghi nhận.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
