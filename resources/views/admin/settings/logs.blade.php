<x-layouts.admin title="Nhật ký hệ thống">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Nhật ký hệ thống (System Logs)</h2>
            <p class="text-gray-500 mt-1">Theo dõi hoạt động của người dùng trong hệ thống</p>
        </div>
        <div>
            <a href="{{ route('admin.settings.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Quay lại Cài đặt
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.settings.logs') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <select name="module" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả Module</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="text" name="action_search" value="{{ request('action_search') }}" placeholder="Tìm theo hành động (VD: CREATED)..." class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none font-mono">
            </div>
            <div>
                <select name="user_id" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả người thực hiện</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="block w-full py-2 px-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-xs outline-none" title="Từ ngày">
                <span class="text-gray-400">-</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="block w-full py-2 px-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-xs outline-none" title="Đến ngày">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-gray-900 hover:bg-gray-800 text-white px-2 py-2 rounded-lg text-sm font-medium transition-colors">Lọc</button>
                <a href="{{ route('admin.settings.logs') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-2 rounded-lg text-sm font-medium transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người thực hiện</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($log->user)
                                <div class="font-bold text-gray-900">{{ $log->user->full_name }}</div>
                            @else
                                <div class="font-bold text-gray-500 italic">Hệ thống</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $color = $log->action_color;
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold font-mono bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $log->module }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-800 line-clamp-2" title="{{ $log->description }}">
                                {{ Str::limit($log->description, 80) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs text-gray-500 font-mono">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-clipboard-list text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Chưa có nhật ký nào</h3>
                                <p class="text-sm mt-1 text-gray-500">Hệ thống chưa ghi nhận hoạt động nào khớp với bộ lọc.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-white">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
