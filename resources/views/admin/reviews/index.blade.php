<x-layouts.admin title="Quản lý Đánh giá & Phản hồi">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Quản lý Đánh giá</h2>
            <p class="text-gray-500 mt-1">Kiểm duyệt các phản hồi từ bệnh nhân</p>
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="bg-green-50 text-green-800 p-4 rounded-lg mb-6 flex items-center justify-between border border-green-200">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-green-500"></i>
                {{ session('success') }}
            </div>
            <button @click="show=false" class="text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- FILTER FORM -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Số sao</label>
                    <select name="rating" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Sao</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Sao</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Sao</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Sao</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Sao</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Trạng thái</label>
                    <select name="is_visible" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('is_visible') === '1' ? 'selected' : '' }}>Đang hiện</option>
                        <option value="0" {{ request('is_visible') === '0' ? 'selected' : '' }}>Đã bị ẩn</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bác sĩ</label>
                    <select name="doctor_profile_id" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả Bác sĩ</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ request('doctor_profile_id') == $doc->id ? 'selected' : '' }}>{{ $doc->full_title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Chuyên khoa</label>
                    <select name="specialty_id" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả Chuyên khoa</option>
                        @foreach($specialties as $sp)
                            <option value="{{ $sp->id }}" {{ request('specialty_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Đến ngày</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                </div>
                <div class="col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-900 hover:bg-gray-800 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                        Lọc
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg text-sm font-medium transition-colors">
                        Đặt lại
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đánh giá</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Nội dung</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thông tin khám</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người viết</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reviews as $review)
                    <tr class="hover:bg-gray-50 transition-colors {{ !$review->is_visible ? 'bg-red-50/50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex text-yellow-400 text-sm">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fa-solid fa-star"></i>
                                    @else
                                        <i class="fa-regular fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $review->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-900 line-clamp-2" title="{{ $review->comment }}">
                                {{ $review->comment ?? '(Không có nội dung)' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">BS. {{ $review->doctorProfile->user->name ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $review->specialty->name ?? '—' }}</div>
                            <a href="{{ route('admin.appointments.show', $review->appointment_id) }}" class="text-[10px] text-blue-600 hover:underline mt-1 block">Lịch hẹn #{{ $review->appointment_id }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $review->patientProfile->full_name ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($review->is_visible)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Đang hiện</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Đã ẩn</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.reviews.show', $review->id) }}" class="text-gray-400 hover:text-blue-600 transition-colors" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.reviews.toggle-visibility', $review->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="{{ $review->is_visible ? 'text-gray-400 hover:text-orange-600' : 'text-orange-500 hover:text-orange-700' }} transition-colors" title="{{ $review->is_visible ? 'Ẩn đánh giá' : 'Hiện đánh giá' }}">
                                        @if($review->is_visible)
                                            <i class="fa-regular fa-eye-slash"></i>
                                        @else
                                            <i class="fa-regular fa-eye"></i>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline-block" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN đánh giá này không? Thay vào đó bạn nên ẨN nó đi.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
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
                                    <i class="fa-solid fa-star-half-stroke text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Không tìm thấy đánh giá nào</h3>
                                <p class="text-sm mt-1 text-gray-500">Thử thay đổi bộ lọc tìm kiếm.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reviews->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-white">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
