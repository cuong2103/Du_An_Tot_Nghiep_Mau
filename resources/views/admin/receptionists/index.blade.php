<x-layouts.admin title="Quản lý Lễ tân">
    <!-- Session Alerts -->
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

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" style="display: none;"
             class="bg-red-50 text-red-800 p-4 rounded-lg mb-6 flex items-center justify-between border border-red-200">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                {{ session('error') }}
            </div>
            <button @click="show=false" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Quản lý Lễ tân</h2>
            <p class="text-gray-500 mt-1">Danh sách nhân viên hỗ trợ tiếp đón và đặt lịch</p>
        </div>
        <div>
            <a href="{{ route('admin.receptionists.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i> Thêm lễ tân mới
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user-tie text-xl text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Tổng lễ tân</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-check text-xl text-green-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Đang hoạt động</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-xmark text-xl text-red-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Đã khoá</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['locked']) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.receptionists.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="lg:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, SĐT hoặc mã NV..." class="block w-full pl-10 py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                </div>
            </div>
            
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-building text-gray-400"></i>
                    </div>
                    <input type="text" name="department" value="{{ request('department') }}" placeholder="Lọc theo phòng ban..." class="block w-full pl-10 py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                </div>
            </div>
            
            <div>
                <select name="status" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Đã khoá</option>
                </select>
            </div>
            
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                    Lọc
                </button>
                <a href="{{ route('admin.receptionists.index') }}" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Đặt lại
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lễ tân</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã NV</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chức vụ</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phòng ban</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SĐT đăng nhập</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày vào làm</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($receptionists as $receptionist)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($receptionists->currentPage() - 1) * $receptionists->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($receptionist->avatar_url)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset($receptionist->avatar_url) }}" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                                            {{ $receptionist->avatar_initials }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $receptionist->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $receptionist->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                            {{ $receptionist->staffProfile->employee_code ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $receptionist->staffProfile->position ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $receptionist->staffProfile->department ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                            <i class="fa-solid fa-phone text-gray-400 mr-1 text-xs"></i> {{ $receptionist->phone }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $receptionist->staffProfile->start_date ? \Carbon\Carbon::parse($receptionist->staffProfile->start_date)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($receptionist->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    Đã khoá
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.receptionists.edit', $receptionist->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Sửa">
                                    <i class="fa-solid fa-pen text-lg"></i>
                                </a>

                                <form action="{{ route('admin.receptionists.toggle-active', $receptionist->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    @if(auth()->id() == $receptionist->id)
                                        <button type="button" disabled class="text-gray-300 cursor-not-allowed" title="Không thể khoá tài khoản của chính mình">
                                            <i class="fa-solid fa-lock text-lg"></i>
                                        </button>
                                    @else
                                        @if($receptionist->is_active)
                                            <button type="submit" onclick="return confirm('Bạn có chắc muốn khoá tài khoản lễ tân này?')" class="text-yellow-500 hover:text-yellow-700 transition-colors" title="Khoá tài khoản">
                                                <i class="fa-solid fa-lock text-lg"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="text-green-500 hover:text-green-700 transition-colors" title="Mở khoá tài khoản">
                                                <i class="fa-solid fa-lock-open text-lg"></i>
                                            </button>
                                        @endif
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-user-slash text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Chưa có lễ tân nào trong hệ thống</h3>
                                <p class="text-sm mt-1 text-gray-500">Hãy thêm lễ tân đầu tiên để bắt đầu quản lý đặt khám.</p>
                                <a href="{{ route('admin.receptionists.create') }}" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Thêm lễ tân ngay
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($receptionists->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-white">
            {{ $receptionists->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
