<x-layouts.admin title="Lịch sử Thông báo">
    <div>
        <!-- Header & Alert -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Quản lý Thông báo</h2>
                <p class="text-gray-500 mt-1">Lịch sử các thông báo đã gửi cho hệ thống và người dùng</p>
            </div>
            <div>
                <a href="{{ route('admin.notifications.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Gửi thông báo mới
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 text-green-800 rounded-lg p-4 flex items-center border border-green-200" x-data="{ show: true }" x-show="show">
                <i class="fa-solid fa-circle-check text-green-500 mr-3 text-lg"></i>
                <span class="flex-1 text-sm font-medium">{{ session('success') }}</span>
                <button @click="show = false" class="text-green-600 hover:text-green-900">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <!-- Filter & Search -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form method="GET" action="{{ route('admin.notifications.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề, tên hoặc SĐT người nhận..."
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Loại thông báo</label>
                    <select name="type" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả</option>
                        <option value="appointment" {{ request('type') == 'appointment' ? 'selected' : '' }}>Lịch hẹn</option>
                        <option value="result" {{ request('type') == 'result' ? 'selected' : '' }}>Kết quả</option>
                        <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Hệ thống</option>
                        <option value="reminder" {{ request('type') == 'reminder' ? 'selected' : '' }}>Nhắc nhở</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Trạng thái đọc</label>
                    <select name="is_read" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Đã đọc</option>
                        <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Chưa đọc</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Lọc
                    </button>
                    @if(request()->anyFilled(['search', 'type', 'channel', 'is_read']))
                        <a href="{{ route('admin.notifications.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm font-medium transition-colors" title="Xóa bộ lọc">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                            <th class="px-6 py-4">Người nhận</th>
                            <th class="px-6 py-4">Nội dung</th>
                            <th class="px-6 py-4">Phân loại & Kênh</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($notifications as $noti)
                        <tr class="hover:bg-gray-50 transition-colors {{ !$noti->is_read ? 'bg-blue-50/30' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ $noti->user ? $noti->user->avatar_initials : '?' }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $noti->user ? $noti->user->full_name : 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $noti->user ? $noti->user->phone : '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 text-sm mb-1 line-clamp-1" title="{{ $noti->title }}">{{ $noti->title }}</div>
                                <div class="text-xs text-gray-500 line-clamp-2" title="{{ $noti->content }}">{{ $noti->content }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1.5 items-start">
                                    @if($noti->type === 'appointment')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800"><i class="fa-regular fa-calendar-check mr-1"></i> {{ $noti->type_label }}</span>
                                    @elseif($noti->type === 'result')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-800"><i class="fa-solid fa-file-medical mr-1"></i> {{ $noti->type_label }}</span>
                                    @elseif($noti->type === 'reminder')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-orange-100 text-orange-800"><i class="fa-regular fa-bell mr-1"></i> {{ $noti->type_label }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-800"><i class="fa-solid fa-gear mr-1"></i> {{ $noti->type_label }}</span>
                                    @endif

                                    @if($noti->channel === 'email')
                                        <span class="text-xs text-gray-500"><i class="fa-regular fa-envelope mr-1"></i> Email</span>
                                    @elseif($noti->channel === 'zalo')
                                        <span class="text-xs text-gray-500"><i class="fa-regular fa-comment mr-1"></i> Zalo</span>
                                    @else
                                        <span class="text-xs text-gray-500"><i class="fa-solid fa-globe mr-1"></i> Trong Web</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($noti->is_read)
                                    <span class="text-gray-400 text-xs flex flex-col items-center gap-1">
                                        <i class="fa-solid fa-check-double text-blue-500 text-sm"></i>
                                        Đã đọc
                                    </span>
                                @else
                                    <span class="text-gray-500 text-xs flex flex-col items-center gap-1">
                                        <i class="fa-solid fa-check text-gray-300 text-sm"></i>
                                        Chưa đọc
                                    </span>
                                @endif
                                <div class="text-[10px] text-gray-400 mt-1">{{ $noti->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('admin.notifications.destroy', $noti->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        onclick="return confirm('Bạn có chắc muốn xoá lịch sử thông báo này không?')"
                                        class="text-red-600 hover:text-red-900 transition-colors p-2"
                                        title="Xoá thông báo">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fa-regular fa-bell-slash text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">Chưa có thông báo nào</h3>
                                    <p class="mt-1 text-sm text-gray-500">Bắt đầu tương tác với người dùng bằng cách gửi thông báo.</p>
                                    <a href="{{ route('admin.notifications.create') }}" class="mt-4 bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Gửi thông báo ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
