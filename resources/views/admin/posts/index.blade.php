<x-layouts.admin title="Quản lý Bài viết">
    <!-- Header & Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Quản lý Bài viết</h2>
            <p class="text-gray-500 mt-1">Nội dung truyền thông, dịch vụ và hướng dẫn</p>
        </div>
        <div>
            <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Viết bài mới
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
        <form action="{{ route('admin.posts.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tiêu đề..." class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
            </div>
            <div>
                <select name="post_type" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả loại bài</option>
                    <option value="news" {{ request('post_type') === 'news' ? 'selected' : '' }}>Tin tức</option>
                    <option value="service" {{ request('post_type') === 'service' ? 'selected' : '' }}>Dịch vụ</option>
                    <option value="guide" {{ request('post_type') === 'guide' ? 'selected' : '' }}>Hướng dẫn</option>
                    <option value="announcement" {{ request('post_type') === 'announcement' ? 'selected' : '' }}>Thông báo</option>
                </select>
            </div>
            <div>
                <select name="specialty_id" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả chuyên khoa</option>
                    @foreach($specialties as $sp)
                        <option value="{{ $sp->id }}" {{ request('specialty_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="is_published" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('is_published') === '1' ? 'selected' : '' }}>Đã đăng</option>
                    <option value="0" {{ request('is_published') === '0' ? 'selected' : '' }}>Bản nháp</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full sm:w-auto bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Lọc</button>
                <a href="{{ route('admin.posts.index') }}" class="w-full sm:w-auto text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Đặt lại</a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Ảnh</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chuyên khoa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tác giả</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lượt xem</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đăng</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($posts as $post)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($post->thumbnail_url)
                                <img src="{{ $post->thumbnail_url }}" alt="" class="h-12 w-12 rounded object-cover border border-gray-200" onerror="this.outerHTML='<div class=\'h-12 w-12 bg-gray-100 flex items-center justify-center rounded border border-gray-200\'><i class=\'fa-solid fa-image text-gray-400\'></i></div>'">
                            @else
                                <div class="h-12 w-12 bg-gray-100 flex items-center justify-center rounded border border-gray-200">
                                    <i class="fa-solid fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900 line-clamp-2" title="{{ $post->title }}">{{ $post->title }}</div>
                            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-link text-gray-400"></i>
                                <span class="truncate max-w-xs">{{ $post->slug }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $typeColors = [
                                    'news' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'service' => 'bg-green-100 text-green-800 border-green-200',
                                    'guide' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'announcement' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                ];
                                $typeLabels = [
                                    'news' => 'Tin tức',
                                    'service' => 'Dịch vụ',
                                    'guide' => 'Hướng dẫn',
                                    'announcement' => 'Thông báo',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $typeColors[$post->post_type] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                {{ $typeLabels[$post->post_type] ?? 'Khác' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $post->specialty->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $post->author->full_name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            <i class="fa-regular fa-eye mr-1"></i> {{ number_format($post->view_count) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($post->is_published)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    Đã đăng
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    Bản nháp
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                
                                <form action="{{ route('admin.posts.toggle-publish', $post->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-gray-500 hover:text-gray-800 transition-colors" title="{{ $post->is_published ? 'Ẩn bài viết' : 'Đăng bài viết' }}">
                                        <i class="fa-solid {{ $post->is_published ? 'fa-eye-slash text-gray-500' : 'fa-eye text-green-500' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        onclick="return confirm('Bạn có chắc muốn xoá bài viết này? Hành động này không thể hoàn tác.')"
                                        class="text-red-600 hover:text-red-900 transition-colors"
                                        title="Xoá">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-newspaper text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Chưa có bài viết nào</h3>
                                <p class="text-sm mt-1 text-gray-500">Bắt đầu bằng việc nhấn Thêm bài viết.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($posts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-white">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
