<x-layouts.admin title="Chi tiết Chuyên khoa">
    <div class="mb-6">
        <a href="{{ route('admin.specialties.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors mb-2 inline-block">
            <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại Danh sách
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Chi tiết Chuyên khoa</h2>
    </div>

    <!-- Thông tin tổng quan Chuyên khoa -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-col md:flex-row gap-6 items-start">
        <div class="flex-shrink-0">
            @if($specialty->image_url)
                <div class="h-32 w-32 rounded-xl overflow-hidden border border-gray-100 bg-gray-50 flex items-center justify-center">
                    <img src="{{ asset($specialty->image_url) }}" alt="{{ $specialty->name }}" class="h-full w-full object-cover">
                </div>
            @else
                <div class="h-32 w-32 rounded-xl bg-blue-50 text-blue-500 flex flex-col items-center justify-center border border-blue-100">
                    <i class="fa-solid fa-stethoscope text-4xl mb-2"></i>
                    <span class="text-xs font-medium">Chưa có ảnh</span>
                </div>
            @endif
        </div>
        
        <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-2xl font-bold text-gray-900">{{ $specialty->name }}</h3>
                @if($specialty->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                        <i class="fa-solid fa-check-circle mr-1"></i> Đang hoạt động
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                        <i class="fa-solid fa-times-circle mr-1"></i> Đã ẩn
                    </span>
                @endif
            </div>
            
            <p class="text-gray-600 mb-6">{{ $specialty->description ?? 'Chưa có mô tả cho chuyên khoa này.' }}</p>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Thứ tự hiển thị</div>
                    <div class="text-xl font-bold text-gray-900">{{ $specialty->display_order }}</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <div class="text-xs text-blue-600 font-medium uppercase tracking-wider mb-1">Số Bác sĩ</div>
                    <div class="text-xl font-bold text-blue-900">{{ $specialty->doctors->count() }}</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                    <div class="text-xs text-purple-600 font-medium uppercase tracking-wider mb-1">Số Phòng khám</div>
                    <div class="text-xl font-bold text-purple-900">{{ $specialty->rooms->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Danh sách Bác sĩ -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fa-solid fa-user-doctor text-blue-500 mr-2"></i> 
                    Danh sách Bác sĩ
                </h3>
                <a href="{{ route('admin.doctors.index') }}?specialty_id={{ $specialty->id }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Xem tất cả <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bác sĩ</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($specialty->doctors as $doctor)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mr-3">
                                        {{ $doctor->user->avatar_initials }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $doctor->full_title }}</div>
                                        <div class="text-xs text-gray-500">{{ $doctor->user->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($doctor->pivot->is_primary)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Chính
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        Phụ
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.doctors.show', $doctor->id) }}" class="text-blue-600 hover:text-blue-900"><i class="fa-solid fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">
                                Chưa có bác sĩ nào thuộc chuyên khoa này.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Danh sách Phòng khám -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fa-solid fa-door-open text-purple-500 mr-2"></i> 
                    Danh sách Phòng khám
                </h3>
                <a href="{{ route('admin.rooms.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Quản lý phòng <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Phòng</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số/Mã Phòng</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($specialty->rooms as $room)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="text-sm font-bold text-gray-900">{{ $room->name }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($room->room_number)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $room->room_number }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500 text-sm">
                                Chưa có phòng nào phục vụ chuyên khoa này.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
