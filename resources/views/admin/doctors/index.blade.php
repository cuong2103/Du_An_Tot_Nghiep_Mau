<x-layouts.admin title="Quản lý Bác sĩ">
    <!-- Header & Alert -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Quản lý Bác sĩ</h2>
            <p class="text-gray-500 mt-1">Danh sách bác sĩ và chuyên gia của CareBook</p>
        </div>
        <div>
            <a href="{{ route('admin.doctors.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Thêm bác sĩ mới
            </a>
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

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.doctors.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" 
                        placeholder="Tìm theo tên hoặc mã bác sĩ...">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <select name="specialty_id" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả chuyên khoa</option>
                    @foreach($specialties as $sp)
                        <option value="{{ $sp->id }}" {{ request('specialty_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-36">
                <select name="level" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả cấp độ</option>
                    @foreach(['BS', 'BSCK1', 'BSCK2', 'ThS', 'TS', 'PGS', 'GS'] as $lvl)
                        <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-40">
                <select name="status" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Đã khoá</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Lọc
                </button>
                <a href="{{ route('admin.doctors.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Đặt lại
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bác sĩ</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cấp độ</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chuyên khoa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kinh nghiệm</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($doctors as $doctor)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Bác sĩ -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                        {{ $doctor->user->avatar_initials }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $doctor->full_title }}</div>
                                    <div class="text-xs text-gray-500">{{ $doctor->doctor_code }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Cấp độ -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                {{ $doctor->level }}
                            </span>
                        </td>

                        <!-- Chuyên khoa -->
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($doctor->specialties as $sp)
                                    @if($sp->pivot->is_primary)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 border border-green-200">
                                            {{ $sp->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ $sp->name }}
                                        </span>
                                    @endif
                                @empty
                                    <span class="text-xs text-gray-400 italic">Chưa có</span>
                                @endforelse
                            </div>
                        </td>

                        <!-- Kinh nghiệm -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $doctor->experience_years ? $doctor->experience_years . ' năm' : '—' }}
                        </td>

                        <!-- Trạng thái -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($doctor->user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    Hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    Đã khoá
                                </span>
                            @endif
                        </td>

                        <!-- Thao tác -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Sửa">
                                    <i class="fa-solid fa-pen"></i> Sửa
                                </a>
                                
                                @php
                                    $hasActiveAppointments = \App\Models\Appointment::where('doctor_profile_id', $doctor->id)
                                        ->whereIn('status', ['pending', 'checked_in', 'examining'])
                                        ->exists();
                                @endphp

                                <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        onclick="return confirm('Bạn có chắc muốn xoá/khoá bác sĩ này?')"
                                        {{ $hasActiveAppointments ? 'disabled' : '' }}
                                        class="{{ $hasActiveAppointments ? 'text-gray-300 cursor-not-allowed' : 'text-red-600 hover:text-red-900' }} transition-colors"
                                        title="{{ $hasActiveAppointments ? 'Bác sĩ đang có lịch khám' : 'Xoá bác sĩ' }}">
                                        <i class="fa-solid fa-trash"></i> Xoá
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-user-doctor text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Chưa có bác sĩ nào</h3>
                                <p class="text-sm mt-1 text-gray-500">Hệ thống chưa ghi nhận hồ sơ bác sĩ nào phù hợp.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($doctors->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-white">
            {{ $doctors->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
