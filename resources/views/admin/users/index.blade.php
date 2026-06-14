<x-layouts.admin title="Quản lý Bệnh nhân">
    <!-- Header & Alert -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Quản lý Bệnh nhân</h2>
            <p class="text-gray-500 mt-1">Danh sách tất cả tài khoản bệnh nhân trong hệ thống CareBook</p>
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

    <!-- Mini Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-2 gap-4 mb-6 max-w-lg">
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col">
            <span class="text-gray-500 text-sm font-medium">Tổng số bệnh nhân</span>
            <span class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col">
            <span class="text-gray-500 text-sm font-medium">Đang hoạt động</span>
            <span class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['active']) }}</span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" 
                        placeholder="Tìm theo tên hoặc SĐT bệnh nhân...">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <select name="status" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Đã khoá</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Tìm kiếm
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liên hệ</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- User -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                        {{ $user->avatar_initials }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                    <div class="text-xs text-gray-500">ID: #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Contact -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->phone }}</div>
                            @if($user->email)
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            @else
                                <div class="text-xs text-gray-400 italic">Chưa có email</div>
                            @endif
                        </td>

                        <!-- Role -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $roleColors = [
                                    'admin' => 'bg-red-100 text-red-800 border border-red-200',
                                    'doctor' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                    'receptionist' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'patient' => 'bg-gray-100 text-gray-800 border border-gray-200',
                                ];
                                $roleClass = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800 border border-gray-200';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleClass }}">
                                {{ $user->display_role }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class="fa-solid fa-circle text-[8px] mr-1.5 text-green-500"></i> Hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    <i class="fa-solid fa-circle text-[8px] mr-1.5 text-red-500"></i> Đã khoá
                                </span>
                            @endif
                        </td>

                        <!-- Created At -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Xem chi tiết">
                                    <i class="fa-regular fa-eye"></i> Xem
                                </a>
                                
                                <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                        onclick="return confirm('Bạn có chắc muốn {{ $user->is_active ? 'khoá' : 'mở khoá' }} tài khoản này?')"
                                        {{ $user->id === Auth::id() ? 'disabled' : '' }}
                                        class="{{ $user->id === Auth::id() ? 'text-gray-300 cursor-not-allowed' : ($user->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900') }} transition-colors"
                                        title="{{ $user->is_active ? 'Khoá tài khoản' : 'Mở khoá tài khoản' }}">
                                        @if($user->is_active)
                                            <i class="fa-solid fa-lock"></i> Khoá
                                        @else
                                            <i class="fa-solid fa-unlock"></i> Mở
                                        @endif
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
                                    <i class="fa-solid fa-users-slash text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Không tìm thấy người dùng nào</h3>
                                <p class="text-sm mt-1 text-gray-500">Thử thay đổi bộ lọc hoặc từ khoá tìm kiếm.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-white">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
