<x-layouts.admin title="Quản lý Thông báo">
<div class="space-y-6" x-data="{ showFilters: false }">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý Thông báo</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý và theo dõi các thông báo trong hệ thống</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showFilters = !showFilters" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                <i class="fa-solid fa-filter mr-2"></i>Bộ lọc
            </button>
            <a href="{{ route('admin.notifications.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i>Tạo thông báo mới
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div x-show="showFilters" x-collapse>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <form action="{{ route('admin.notifications.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại thông báo</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Tất cả</option>
                        <option value="appointment" {{ request('type') === 'appointment' ? 'selected' : '' }}>Lịch hẹn</option>
                        <option value="result" {{ request('type') === 'result' ? 'selected' : '' }}>Kết quả</option>
                        <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>Hệ thống</option>
                        <option value="reminder" {{ request('type') === 'reminder' ? 'selected' : '' }}>Nhắc nhở</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kênh gửi</label>
                    <select name="channel" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Tất cả</option>
                        <option value="in_web" {{ request('channel') === 'in_web' ? 'selected' : '' }}>Web</option>
                        <option value="email" {{ request('channel') === 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái gửi</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Tất cả</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Đã gửi</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chưa gửi / Lên lịch</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 transition-colors text-sm font-medium">
                        Lọc kết quả
                    </button>
                    @if(request()->anyFilled(['type', 'channel', 'status']))
                    <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors text-sm font-medium text-center">
                        Xóa
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
        <span class="font-medium">Thành công!</span> {{ session('success') }}
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người nhận</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề / Nội dung</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Phân loại</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notifications as $notification)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                    {{ $notification->user->avatar_initials ?? substr($notification->user->full_name ?? '?', 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $notification->user->full_name ?? 'Không xác định' }}</div>
                                    <div class="text-xs text-gray-500">{{ $notification->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 line-clamp-1" title="{{ $notification->title }}">{{ $notification->title }}</div>
                            <div class="text-xs text-gray-500 line-clamp-1 mt-0.5" title="{{ $notification->content }}">{{ $notification->content }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $typeColors = [
                                    'appointment' => 'blue',
                                    'result' => 'green',
                                    'system' => 'gray',
                                    'reminder' => 'yellow'
                                ];
                                $color = $typeColors[$notification->type] ?? 'gray';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 border border-{{ $color }}-200">
                                {{ $notification->type_label }}
                            </span>
                            <div class="mt-1">
                                @if($notification->channel === 'email')
                                    <span class="text-xs text-gray-500"><i class="fa-regular fa-envelope"></i> Email</span>
                                @elseif($notification->channel === 'in_web')
                                    <span class="text-xs text-gray-500"><i class="fa-regular fa-bell"></i> Web</span>
                                @else
                                    <span class="text-xs text-gray-500"><i class="fa-brands fa-whatsapp"></i> Zalo</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($notification->is_sent)
                                <span class="inline-flex items-center text-green-600 text-sm font-medium">
                                    <i class="fa-solid fa-check-circle mr-1.5"></i> Đã gửi
                                </span>
                            @else
                                <span class="inline-flex items-center text-yellow-600 text-sm font-medium">
                                    <i class="fa-solid fa-clock mr-1.5"></i> 
                                    {{ $notification->scheduled_at ? 'Chờ gửi (Hẹn giờ)' : 'Chưa gửi' }}
                                </span>
                            @endif
                            @if($notification->channel === 'in_web')
                                <div class="mt-1 text-xs">
                                    {!! $notification->is_read ? '<span class="text-green-500">Đã xem</span>' : '<span class="text-gray-400">Chưa xem</span>' !!}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            <div>{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                            @if($notification->scheduled_at)
                                <div class="text-xs text-blue-600 mt-1">Lịch: {{ $notification->scheduled_at->format('d/m/Y H:i') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                @if($notification->is_sent)
                                <form action="{{ route('admin.notifications.resend', $notification) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn gửi lại thông báo này?');">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900" title="Gửi lại">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="inline-block" onsubmit="return confirm('Xoá thông báo này? Hành động này không thể hoàn tác.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="mb-3 text-gray-300"><i class="fa-solid fa-bell-slash text-4xl"></i></div>
                            <p>Không tìm thấy thông báo nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

</div>
</x-layouts.admin>
