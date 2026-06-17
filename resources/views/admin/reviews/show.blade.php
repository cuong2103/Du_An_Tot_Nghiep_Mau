<x-layouts.admin title="Chi tiết Đánh giá">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Chi tiết Đánh giá #{{ $review->id }}</h2>
            <p class="text-gray-500 mt-1">Thông tin chi tiết về phản hồi của bệnh nhân</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reviews.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại
            </a>
            <form action="{{ route('admin.reviews.toggle-visibility', $review->id) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <button type="submit" class="{{ $review->is_visible ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} border border-transparent px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    @if($review->is_visible)
                        <i class="fa-solid fa-eye-slash mr-2"></i> Ẩn đánh giá
                    @else
                        <i class="fa-solid fa-eye mr-2"></i> Hiện đánh giá
                    @endif
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-800 p-4 rounded-lg mb-6 flex items-center border border-green-200">
            <i class="fa-solid fa-circle-check text-green-500 mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Review Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Nội dung đánh giá</h3>
                    @if($review->is_visible)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Trạng thái: Đang hiện</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Trạng thái: Đã ẩn</span>
                    @endif
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-12 w-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl font-bold">
                            {{ mb_strtoupper(mb_substr($review->patientProfile->full_name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 text-lg">{{ $review->patientProfile->full_name ?? 'Bệnh nhân ẩn danh' }}</div>
                            <div class="text-gray-500 text-sm">{{ $review->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="ml-auto flex text-yellow-400 text-xl">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fa-solid fa-star"></i>
                                @else
                                    <i class="fa-regular fa-star text-gray-200"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
                        @if($review->comment)
                            <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $review->comment }}</p>
                        @else
                            <p class="text-gray-400 italic">Bệnh nhân không để lại bình luận.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <h3 class="font-semibold text-gray-900">Thông tin khám bệnh</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Mã Lịch Hẹn</div>
                        <div class="font-medium text-blue-600 hover:underline">
                            <a href="{{ route('admin.appointments.show', $review->appointment_id) }}">
                                {{ $review->appointment->appointment_code ?? '#' . $review->appointment_id }}
                            </a>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Bác sĩ khám</div>
                        <div class="flex items-center gap-2">
                            <div class="font-medium text-gray-900">{{ $review->doctorProfile->user->name ?? '—' }}</div>
                            <a href="#" class="text-gray-400 hover:text-blue-600"><i class="fa-solid fa-up-right-from-square text-xs"></i></a>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Chuyên khoa</div>
                        <div class="text-gray-900">{{ $review->specialty->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Ngày khám</div>
                        <div class="text-gray-900">{{ $review->appointment->appointment_date ? \Carbon\Carbon::parse($review->appointment->appointment_date)->format('d/m/Y') : '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
