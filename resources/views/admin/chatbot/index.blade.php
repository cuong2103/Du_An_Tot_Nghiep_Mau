<x-layouts.admin title="Quản lý Chatbot">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Quản lý Chatbot AI</h2>
        <p class="text-gray-500 mt-1">Cấu hình Intent (Ý định) và Responses (Phản hồi) cho trợ lý ảo</p>
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
    @if(session('error'))
        <div class="bg-red-50 text-red-800 p-4 rounded-lg mb-6 flex items-center gap-3 border border-red-200">
            <i class="fa-solid fa-circle-exclamation text-red-500"></i>
            {{ session('error') }}
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

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="h-12 w-12 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xl mr-4 shrink-0">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Tổng phiên chat</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSessions) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="h-12 w-12 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-xl mr-4 shrink-0">
                <i class="fa-solid fa-message"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Tổng tin nhắn</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalMessages) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="h-12 w-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center text-xl mr-4 shrink-0">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Phiên chat hôm nay</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($sessionsToday) }}</p>
            </div>
        </div>
    </div>

    <!-- Main Layout: 2 Cột -->
    <div x-data="{ 
        intentModal: false,
        responseModal: false,
        intentEditing: false,
        responseEditing: false,
        intentFormAction: '{{ route('admin.chatbot.intents.store') }}',
        responseFormAction: '{{ route('admin.chatbot.responses.store') }}',
        intentData: { id: null, name: '', description: '', examples: '', action: 'faq_lookup', is_active: true },
        responseData: { id: null, intent_id: '{{ request('intent_id', '') }}', content: '', priority: 1, is_active: true },

        openIntent(intent = null) {
            this.intentEditing = !!intent;
            if(intent) {
                this.intentFormAction = `/admin/chatbot/intents/${intent.id}`;
                this.intentData.id = intent.id;
                this.intentData.name = intent.intent_name;
                this.intentData.description = intent.description;
                this.intentData.examples = intent.example_phrases || '';
                this.intentData.action = intent.action;
                this.intentData.is_active = intent.is_active == 1;
            } else {
                this.intentFormAction = '{{ route('admin.chatbot.intents.store') }}';
                this.intentData = { id: null, name: '', description: '', examples: '', action: 'faq_lookup', is_active: true };
            }
            this.intentModal = true;
        },
        
        openResponse(response = null) {
            this.responseEditing = !!response;
            if(response) {
                this.responseFormAction = `/admin/chatbot/responses/${response.id}`;
                this.responseData.id = response.id;
                this.responseData.intent_id = response.intent_id;
                this.responseData.content = response.content;
                this.responseData.priority = response.priority;
                this.responseData.is_active = response.is_active == 1;
            } else {
                this.responseFormAction = '{{ route('admin.chatbot.responses.store') }}';
                this.responseData = { id: null, intent_id: '{{ request('intent_id', '') }}', content: '', priority: 1, is_active: true };
            }
            this.responseModal = true;
        }
    }">

        <div class="flex flex-col xl:flex-row gap-6">
            
            <!-- CỘT TRÁI: INTENTS -->
            <div class="w-full xl:w-1/2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-brain text-blue-500"></i>
                            <h3 class="text-base font-bold text-gray-900">Intents (Ý định)</h3>
                        </div>
                        <button type="button" @click="openIntent()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-plus"></i> Thêm Intent
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên / Mô tả</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Responses</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($intents as $intent)
                                <tr class="hover:bg-gray-50 {{ request('intent_id') == $intent->id ? 'bg-blue-50/50' : '' }}">
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-bold font-mono text-gray-900">{{ $intent->intent_name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 truncate max-w-[150px]" title="{{ $intent->description }}">{{ $intent->description }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $actionColors = [
                                                'faq_lookup' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'guide_booking' => 'bg-green-100 text-green-800 border-green-200',
                                                'introduce_specialty' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                'transfer_staff' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            ];
                                            $actionLabels = [
                                                'faq_lookup' => 'Tra FAQ',
                                                'guide_booking' => 'HD Đặt lịch',
                                                'introduce_specialty' => 'GT Chuyên khoa',
                                                'transfer_staff' => 'Chuyển nhân viên',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border {{ $actionColors[$intent->action] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $actionLabels[$intent->action] ?? $intent->action }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.chatbot.index', ['intent_id' => $intent->id]) }}" class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $intent->responses_count > 0 ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : 'bg-gray-100 text-gray-500' }} text-xs font-bold transition-colors">
                                            {{ $intent->responses_count }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <form action="{{ route('admin.chatbot.intents.toggle-active', $intent->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-gray-500 hover:text-gray-800" title="{{ $intent->is_active ? 'Đang bật' : 'Đang tắt' }}">
                                                <i class="fa-solid {{ $intent->is_active ? 'fa-toggle-on text-green-500' : 'fa-toggle-off text-gray-400' }} text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <button type="button" @click="openIntent({{ json_encode($intent) }})" class="text-blue-600 hover:text-blue-900 transition-colors p-1 mr-1">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.chatbot.intents.destroy', $intent->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                onclick="return confirm('Bạn có chắc muốn xoá Intent này?')"
                                                class="{{ $intent->responses_count > 0 ? 'text-gray-300 cursor-not-allowed' : 'text-red-600 hover:text-red-900' }} p-1 transition-colors"
                                                {{ $intent->responses_count > 0 ? 'disabled title=Phải_xoá_hết_response_trước' : 'title=Xoá' }}>
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">Chưa có Intent nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: RESPONSES -->
            <div class="w-full xl:w-1/2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-comment-dots text-green-500"></i>
                            <h3 class="text-base font-bold text-gray-900">Responses (Phản hồi)</h3>
                        </div>
                        <div class="flex items-center gap-3">
                            @if(request('intent_id'))
                                @php $filteredIntent = $intents->firstWhere('id', request('intent_id')); @endphp
                                <span class="text-xs text-gray-500 hidden sm:inline-block">Lọc theo: <span class="font-bold text-gray-900">{{ $filteredIntent->intent_name ?? '' }}</span></span>
                                <a href="{{ route('admin.chatbot.index') }}" class="text-xs text-red-500 hover:text-red-700 font-medium">Bỏ lọc</a>
                            @endif
                            <button type="button" @click="openResponse()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-plus"></i> Thêm Phản hồi
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intent / Nội dung</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ưu tiên</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Dùng</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($responses as $resp)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="text-[10px] text-blue-600 font-mono mb-0.5">{{ $resp->intent->intent_name ?? '—' }}</div>
                                        <div class="text-sm text-gray-900 line-clamp-2" title="{{ $resp->content }}">{{ Str::limit($resp->content, 100) }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-gray-100 text-gray-600 text-xs font-mono font-bold">{{ $resp->priority }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs font-medium text-gray-500">
                                        {{ number_format($resp->use_count) }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <form action="{{ route('admin.chatbot.responses.toggle-active', $resp->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-gray-500 hover:text-gray-800" title="{{ $resp->is_active ? 'Đang bật' : 'Đang tắt' }}">
                                                <i class="fa-solid {{ $resp->is_active ? 'fa-toggle-on text-green-500' : 'fa-toggle-off text-gray-400' }} text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <button type="button" @click="openResponse({{ json_encode($resp) }})" class="text-blue-600 hover:text-blue-900 transition-colors p-1 mr-1">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.chatbot.responses.destroy', $resp->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                onclick="return confirm('Bạn có chắc muốn xoá phản hồi này?')"
                                                class="text-red-600 hover:text-red-900 p-1 transition-colors">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">Chưa có Phản hồi nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($responses->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100 bg-white">
                        {{ $responses->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- MODAL INTENT -->
        <div x-show="intentModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="intentModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="intentModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="intentModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="intentFormAction" method="POST">
                        @csrf
                        <template x-if="intentEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fa-solid fa-brain text-blue-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title" x-text="intentEditing ? 'Sửa Ý định (Intent)' : 'Thêm Ý định mới'"></h3>
                                    <div class="mt-4 space-y-4 text-left">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Intent <span class="text-red-500">*</span></label>
                                            <input type="text" name="intent_name" x-model="intentData.name" required placeholder="VD: ask_price, book_appointment" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả <span class="text-red-500">*</span></label>
                                            <input type="text" name="description" x-model="intentData.description" required placeholder="VD: Hỏi về giá dịch vụ" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Hành động hệ thống <span class="text-red-500">*</span></label>
                                            <select name="action" x-model="intentData.action" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                                <option value="faq_lookup">Tra cứu FAQ thông thường</option>
                                                <option value="guide_booking">Hướng dẫn đặt lịch khám</option>
                                                <option value="introduce_specialty">Giới thiệu chuyên khoa</option>
                                                <option value="transfer_staff">Chuyển cho nhân viên (Live chat)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Các mẫu câu ví dụ (Example phrases)</label>
                                            <textarea name="example_phrases" x-model="intentData.examples" rows="3" placeholder="Giá khám bao nhiêu?&#10;Khám tổng quát hết bao nhiêu tiền?" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none"></textarea>
                                            <p class="text-[10px] text-gray-500 mt-1">Mỗi dòng một câu. Dùng để huấn luyện AI nhận diện tốt hơn.</p>
                                        </div>
                                        <div class="flex items-center mt-2">
                                            <input type="checkbox" name="is_active" value="1" x-model="intentData.is_active" id="intent_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="intent_active" class="ml-2 block text-sm text-gray-900 cursor-pointer">Kích hoạt Intent này</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Lưu Ý định
                            </button>
                            <button type="button" @click="intentModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Huỷ bỏ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL RESPONSE -->
        <div x-show="responseModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="responseModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="responseModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="responseModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="responseFormAction" method="POST">
                        @csrf
                        <template x-if="responseEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fa-regular fa-comment-dots text-green-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title" x-text="responseEditing ? 'Sửa Phản hồi (Response)' : 'Thêm Phản hồi mới'"></h3>
                                    <div class="mt-4 space-y-4 text-left">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Thuộc Intent <span class="text-red-500">*</span></label>
                                            <select name="intent_id" x-model="responseData.intent_id" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white font-mono">
                                                <option value="">-- Chọn Intent --</option>
                                                @foreach($intents as $intent)
                                                    <option value="{{ $intent->id }}">{{ $intent->intent_name }} ({{ $intent->description }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung phản hồi <span class="text-red-500">*</span></label>
                                            <textarea name="content" x-model="responseData.content" required rows="4" placeholder="Nhập nội dung bot sẽ trả lời..." class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Độ ưu tiên</label>
                                            <input type="number" name="priority" x-model="responseData.priority" min="1" max="10" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none w-24">
                                            <p class="text-[10px] text-gray-500 mt-1">1 là cao nhất. Dùng khi 1 Intent có nhiều câu trả lời ngẫu nhiên.</p>
                                        </div>
                                        <div class="flex items-center mt-2">
                                            <input type="checkbox" name="is_active" value="1" x-model="responseData.is_active" id="response_active" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                            <label for="response_active" class="ml-2 block text-sm text-gray-900 cursor-pointer">Sử dụng phản hồi này</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Lưu Phản hồi
                            </button>
                            <button type="button" @click="responseModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Huỷ bỏ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.admin>
