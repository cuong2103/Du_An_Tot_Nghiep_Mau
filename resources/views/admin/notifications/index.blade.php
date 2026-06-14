<x-layouts.admin title="Quản lý Thông báo">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Quản lý Thông báo</h2>
            <p class="text-gray-500 mt-1">Lịch sử gửi thông báo đến người dùng</p>
        </div>
        <div>
            <a href="{{ route('admin.notifications.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i> Gửi thông báo mới
            </a>
        </div>
    </div>

    <!-- Alert -->
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

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.notifications.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tiêu đề hoặc tên người nhận..." class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
            </div>
            <div>
                <select name="type" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả loại thông báo</option>
                    <option value="appointment" {{ request('type') === 'appointment' ? 'selected' : '' }}>Lịch hẹn</option>
                    <option value="result" {{ request('type') === 'result' ? 'selected' : '' }}>Kết quả</option>
                    <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>Hệ thống</option>
                    <option value="reminder" {{ request('type') === 'reminder' ? 'selected' : '' }}>Nhắc nhở</option>
                </select>
            </div>
            <div>
                <select name="channel" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả kênh gửi</option>
                    <option value="in_web" {{ request('channel') === 'in_web' ? 'selected' : '' }}>Trong web</option>
                    <option value="email" {{ request('channel') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="zalo" {{ request('channel') === 'zalo' ? 'selected' : '' }}>Zalo</option>
                </select>
            </div>
            <div>
                <select name="is_read" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Đã đọc</option>
                    <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Chưa đọc</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full sm:w-auto bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Lọc</button>
                <a href="{{ route('admin.notifications.index') }}" class="w-full sm:w-auto text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Đặt lại</a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề / Nội dung</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người nhận</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kênh</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Đã gửi lúc</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notifications as $noti)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900 line-clamp-1" title="{{ $noti->title }}">{{ $noti->title }}</div>
                            <div class="text-xs text-gray-500 mt-1 line-clamp-1 truncate max-w-xs" title="{{ $noti->content }}">{{ Str::limit($noti->content, 60) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">{{ $noti->user->full_name ?? '—' }}</div>
                            <div class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-phone mr-1"></i>{{ $noti->user->phone ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $typeColors = [
                                    'appointment' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'result' => 'bg-green-100 text-green-800 border-green-200',
                                    'system' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'reminder' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border {{ $typeColors[$noti->type] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                {{ $noti->type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-mono text-gray-600">
                            {{ $noti->channel_label }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($noti->is_read)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-green-50 text-green-700 border border-green-200">
                                    Đã đọc
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-orange-50 text-orange-700 border border-orange-200">
                                    Chưa đọc
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 font-mono">
                            {{ $noti->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form action="{{ route('admin.notifications.destroy', $noti->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    onclick="return confirm('Bạn có chắc muốn xoá thông báo này?')"
                                    class="text-red-600 hover:text-red-900 transition-colors"
                                    title="Xoá">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-bell-slash text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Chưa có thông báo nào</h3>
                                <p class="text-sm mt-1 text-gray-500">Bắt đầu gửi thông báo mới cho người dùng.</p>
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
</x-layouts.admin>
