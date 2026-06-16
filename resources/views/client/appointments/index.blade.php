<x-layouts.client title="Lịch hẹn khám">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Lịch Hẹn & Lịch Sử Khám</h1>

            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-3 border border-green-100">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-3 border border-red-100">
                    <i class="fa-solid fa-circle-exclamation text-xl"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Left Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                        <div class="p-4">
                            <nav class="space-y-1">
                                <a href="{{ route('client.account.index') }}" class="text-gray-700 hover:bg-gray-50 flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors">
                                    <i class="fa-solid fa-users w-5 text-center"></i> Hồ sơ người bệnh
                                </a>
                                <a href="{{ route('client.appointments.index') }}" class="bg-blue-50 text-blue-700 flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors">
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
                <div class="lg:col-span-3 space-y-6">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <div x-data="{ tab: 'upcoming' }">
                            <!-- Tabs -->
                            <div class="flex border-b border-gray-100 mb-6">
                                <button @click="tab = 'upcoming'" class="pb-3 px-4 text-sm font-semibold transition-colors border-b-2" :class="tab === 'upcoming' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'">Sắp tới / Chờ khám</button>
                                <button @click="tab = 'past'" class="pb-3 px-4 text-sm font-semibold transition-colors border-b-2" :class="tab === 'past' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'">Lịch sử đã khám / Hủy</button>
                            </div>
                            
                            <!-- UPCOMING APPOINTMENTS -->
                            <div x-show="tab === 'upcoming'">
                                @php
                                    $upcoming = $appointments->filter(function($app) {
                                        return in_array($app->status, ['pending', 'confirmed']);
                                    });
                                @endphp
                                
                                @if($upcoming->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($upcoming as $app)
                                            <div class="border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition-colors">
                                                <div class="flex flex-col md:flex-row gap-4 justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-3 mb-2">
                                                            <span class="font-bold text-lg text-gray-900">{{ \Carbon\Carbon::parse($app->appointment_date)->format('d/m/Y') }}</span>
                                                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-sm font-bold">{{ \Carbon\Carbon::parse($app->appointment_time)->format('H:i') }}</span>
                                                            
                                                            @if($app->status === 'pending')
                                                                <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-xs font-semibold">Chờ xác nhận</span>
                                                            @else
                                                                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold">Đã xác nhận</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-gray-600 mb-1"><i class="fa-regular fa-user w-5"></i> Bệnh nhân: <span class="font-medium text-gray-900">{{ $app->patientProfile->full_name }}</span></p>
                                                        <p class="text-sm text-gray-600 mb-1"><i class="fa-solid fa-user-doctor w-5"></i> Bác sĩ: <span class="font-medium text-gray-900">{{ $app->doctorProfile->user->full_name ?? 'Bác sĩ' }}</span></p>
                                                        <p class="text-sm text-gray-500 mt-2 italic">Lý do: {{ $app->reason }}</p>
                                                    </div>
                                                    <div class="flex flex-col justify-between items-end">
                                                        <div class="text-sm font-bold text-gray-900 mb-4">{{ number_format($app->doctorProfile->consultation_fee ?? 200000) }}đ</div>
                                                        
                                                        <form action="{{ route('client.appointments.cancel', $app->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch khám này không?');">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border border-red-200">
                                                                Hủy lịch
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-10">
                                        <i class="fa-regular fa-calendar-xmark text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500">Bạn không có lịch hẹn nào sắp tới.</p>
                                        <a href="/" class="text-blue-600 font-medium hover:underline mt-2 inline-block">Đặt lịch ngay</a>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- PAST APPOINTMENTS -->
                            <div x-show="tab === 'past'" style="display: none;">
                                @php
                                    $past = $appointments->filter(function($app) {
                                        return in_array($app->status, ['completed', 'cancelled', 'no_show']);
                                    });
                                @endphp
                                
                                @if($past->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($past as $app)
                                            <div class="border border-gray-200 rounded-xl p-5 bg-gray-50/50 opacity-75 hover:opacity-100 transition-opacity">
                                                <div class="flex flex-col md:flex-row gap-4 justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-3 mb-2">
                                                            <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($app->appointment_date)->format('d/m/Y') }}</span>
                                                            <span class="text-gray-600 text-sm">{{ \Carbon\Carbon::parse($app->appointment_time)->format('H:i') }}</span>
                                                            
                                                            @if($app->status === 'completed')
                                                                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold">Đã hoàn thành</span>
                                                            @elseif($app->status === 'cancelled')
                                                                <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-xs font-semibold">Đã hủy</span>
                                                            @else
                                                                <span class="bg-gray-200 text-gray-800 px-2 py-0.5 rounded text-xs font-semibold">Bỏ khám</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-gray-600 mb-1"><i class="fa-regular fa-user w-5"></i> Bệnh nhân: <span class="font-medium text-gray-900">{{ $app->patientProfile->full_name }}</span></p>
                                                        <p class="text-sm text-gray-600"><i class="fa-solid fa-user-doctor w-5"></i> Bác sĩ: <span class="font-medium text-gray-900">{{ $app->doctorProfile->user->full_name ?? 'Bác sĩ' }}</span></p>
                                                    </div>
                                                    
                                                    @if($app->status === 'completed')
                                                        <div class="flex items-end">
                                                            <button class="text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-blue-200">
                                                                Xem Bệnh án & Đơn thuốc
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-10">
                                        <p class="text-gray-500">Chưa có lịch sử khám bệnh.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.client>
