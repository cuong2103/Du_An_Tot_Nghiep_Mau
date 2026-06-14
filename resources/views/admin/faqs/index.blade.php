<x-layouts.admin title="Quản lý FAQ">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Quản lý FAQ</h2>
            <p class="text-gray-500 mt-1">Câu hỏi thường gặp và giải đáp</p>
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
    @if($errors->any())
        <div class="bg-red-50 text-red-800 p-4 rounded-lg mb-6 border border-red-200">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ 
        isEditing: false, 
        editId: null, 
        formAction: '{{ route('admin.faqs.store') }}',
        formMethod: 'POST',
        formData: {
            question: '',
            answer: '',
            specialty_id: '',
            keywords: '',
            is_active: true
        },
        fillForm(faq) {
            this.isEditing = true;
            this.editId = faq.id;
            this.formAction = `/admin/faqs/${faq.id}`;
            this.formMethod = 'PUT';
            this.formData.question = faq.question;
            this.formData.answer = faq.answer;
            this.formData.specialty_id = faq.specialty_id || '';
            this.formData.keywords = faq.keywords || '';
            this.formData.is_active = faq.is_active == 1;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        resetForm() {
            this.isEditing = false;
            this.editId = null;
            this.formAction = '{{ route('admin.faqs.store') }}';
            this.formMethod = 'POST';
            this.formData.question = '';
            this.formData.answer = '';
            this.formData.specialty_id = '';
            this.formData.keywords = '';
            this.formData.is_active = true;
        }
    }" class="flex flex-col lg:flex-row gap-6">

        <!-- Cột trái (2/3): Bảng dữ liệu -->
        <div class="w-full lg:w-2/3">
            
            <!-- Filter Form -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
                <form action="{{ route('admin.faqs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo câu hỏi..." class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
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
                        <select name="is_active" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Đã ẩn</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 bg-gray-900 hover:bg-gray-800 text-white px-2 py-2 rounded-lg text-sm font-medium transition-colors">Lọc</button>
                        <a href="{{ route('admin.faqs.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-2 rounded-lg text-sm font-medium transition-colors">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Câu hỏi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chuyên khoa</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lượt xem</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($faqs as $faq)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900 line-clamp-2" title="{{ $faq->question }}">
                                        {{ Str::limit($faq->question, 80) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($faq->specialty)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 border border-green-200">
                                            {{ $faq->specialty->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                            Chung
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    <i class="fa-regular fa-eye mr-1"></i> {{ number_format($faq->view_count) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($faq->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Hiển thị
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Đã ẩn
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3">
                                        <button type="button" @click="fillForm({{ json_encode($faq) }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Sửa">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        
                                        <form action="{{ route('admin.faqs.toggle-active', $faq->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-gray-500 hover:text-gray-800 transition-colors" title="{{ $faq->is_active ? 'Ẩn' : 'Hiện' }}">
                                                <i class="fa-solid {{ $faq->is_active ? 'fa-toggle-on text-green-500' : 'fa-toggle-off text-gray-400' }} text-lg"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                onclick="return confirm('Bạn có chắc muốn xoá câu hỏi này? Hành động này không thể hoàn tác.')"
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
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-circle-question text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900">Chưa có câu hỏi nào</h3>
                                        <p class="text-sm mt-1 text-gray-500">Thêm câu hỏi mới ở form bên cạnh.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($faqs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-white">
                    {{ $faqs->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Cột phải (1/3): Form Thêm/Sửa -->
        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900" x-text="isEditing ? 'Sửa Câu hỏi' : 'Thêm Câu hỏi mới'"></h3>
                    <button type="button" x-show="isEditing" @click="resetForm()" class="text-xs text-blue-600 font-medium hover:underline">Huỷ sửa</button>
                </div>
                
                <form :action="formAction" method="POST" class="p-6">
                    @csrf
                    <template x-if="formMethod === 'PUT'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Câu hỏi <span class="text-red-500">*</span></label>
                            <textarea name="question" x-model="formData.question" required rows="2" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="Ví dụ: Làm sao để đặt lịch khám?"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Câu trả lời <span class="text-red-500">*</span></label>
                            <textarea name="answer" x-model="formData.answer" required rows="6" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="Chi tiết câu trả lời..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chuyên khoa</label>
                            <select name="specialty_id" x-model="formData.specialty_id" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                <option value="">Chung (không thuộc chuyên khoa)</option>
                                @foreach($specialties as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Từ khoá (Keywords)</label>
                            <input type="text" name="keywords" x-model="formData.keywords" placeholder="VD: dat lich, kham benh, huong dan" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <div class="text-[10px] text-gray-500 mt-1">Phân tách bằng dấu phẩy. Dùng để search tốt hơn.</div>
                        </div>

                        <div class="flex items-center justify-between py-2 border-t border-gray-100 mt-2">
                            <label class="text-sm font-medium text-gray-700">Trạng thái hiển thị</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" x-model="formData.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="resetForm()" class="flex-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 py-2 rounded-lg text-sm font-medium transition-colors">
                            Đặt lại
                        </button>
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition-colors shadow-sm relative" x-data="{ loading: false }" @click="loading = true; setTimeout(() => loading = false, 2000)">
                            <span x-show="!loading"><i class="fa-solid fa-floppy-disk mr-1"></i> Lưu lại</span>
                            <span x-show="loading"><i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.admin>
