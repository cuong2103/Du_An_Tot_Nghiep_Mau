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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đối tượng</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Chi tiết</th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($log->ref_type && $log->ref_id)
                                {{ $log->ref_type }} #{{ $log->ref_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($log->old_data || $log->new_data)
                                <button type="button" x-data x-on:click="$dispatch('open-log-modal', {
                                    old: {{ json_encode($log->old_data ?? new stdClass) }},
                                    new: {{ json_encode($log->new_data ?? new stdClass) }}
                                })" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fa-solid fa-code"></i> Xem
                                </button>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
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

    <!-- JSON Diff Modal using Alpine -->
    <div x-data="{ open: false, oldData: {}, newData: {} }" 
         @open-log-modal.window="open = true; oldData = $event.detail.old; newData = $event.detail.new;"
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" @click="open = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="open" class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Chi tiết thay đổi dữ liệu</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-red-600 mb-2">Dữ liệu cũ (Old Data)</h4>
                        <pre class="bg-red-50 border border-red-100 p-4 rounded-lg text-xs overflow-auto max-h-96 font-mono" x-text="JSON.stringify(oldData, null, 2)"></pre>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-green-600 mb-2">Dữ liệu mới (New Data)</h4>
                        <pre class="bg-green-50 border border-green-100 p-4 rounded-lg text-xs overflow-auto max-h-96 font-mono" x-text="JSON.stringify(newData, null, 2)"></pre>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6">
                    <button @click="open = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-gray-900 border border-transparent rounded-lg shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:text-sm">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
