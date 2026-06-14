<x-layouts.admin title="Quản lý Phòng khám">
    <div x-data="{ 
        open: false, 
        mode: 'create', 
        form: { id: null, name: '', room_number: '', building: '', floor: '', room_type: '', capacity: '', is_active: true, specialty_ids: [] },
        openCreate() {
            this.mode = 'create';
            this.form = { id: null, name: '', room_number: '', building: '', floor: '', room_type: '', capacity: '', is_active: true, specialty_ids: [] };
            this.open = true;
        },
        openEdit(room) {
            this.mode = 'edit';
            this.form = { 
                id: room.id, 
                name: room.name, 
                room_number: room.room_number || '', 
                building: room.building || '', 
                floor: room.floor || '', 
                room_type: room.room_type, 
                capacity: room.capacity || '', 
                is_active: room.is_active,
                specialty_ids: room.specialties ? room.specialties.map(s => s.id.toString()) : []
            };
            this.open = true;
        }
    }" @keydown.escape.window="open = false">
        
        <!-- Header & Alert -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Quản lý Phòng khám</h2>
                <p class="text-gray-500 mt-1">Danh sách phòng ban, khu vực khám chữa bệnh</p>
            </div>
            <div>
                <button @click="openCreate()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Thêm phòng mới
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-3 border border-green-200">
                <i class="fa-solid fa-circle-check text-green-500"></i>
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 flex items-center gap-3 border border-red-200">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('admin.rooms.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/3">
                    <select name="building" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả toà nhà</option>
                        <option value="Nhà K1" {{ request('building') == 'Nhà K1' ? 'selected' : '' }}>Nhà K1</option>
                        <option value="Nhà K2" {{ request('building') == 'Nhà K2' ? 'selected' : '' }}>Nhà K2</option>
                        <option value="Nhà K3" {{ request('building') == 'Nhà K3' ? 'selected' : '' }}>Nhà K3</option>
                    </select>
                </div>
                <div class="w-full sm:w-1/3">
                    <select name="room_type" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả loại phòng</option>
                        <option value="examination" {{ request('room_type') == 'examination' ? 'selected' : '' }}>Phòng khám</option>
                        <option value="diagnostic" {{ request('room_type') == 'diagnostic' ? 'selected' : '' }}>Cận lâm sàng</option>
                        <option value="surgery" {{ request('room_type') == 'surgery' ? 'selected' : '' }}>Phẫu thuật</option>
                        <option value="other" {{ request('room_type') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
                <div class="w-full sm:w-1/3">
                    <select name="status" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tạm đóng</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Lọc
                    </button>
                    <a href="{{ route('admin.rooms.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Đặt lại
                    </a>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên phòng</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toà - Tầng</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại phòng</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chuyên khoa</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sức chứa</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rooms as $room)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $room->name }}</div>
                                @if($room->room_number)
                                    <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ $room->room_number }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($room->building || $room->floor)
                                    {{ $room->building ?? '—' }} - {{ $room->floor ? 'Tầng ' . $room->floor : '—' }}
                                @else
                                    <span class="text-gray-400 italic">Chưa có</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $typeColors = [
                                        'examination' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'diagnostic' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'surgery' => 'bg-red-100 text-red-800 border-red-200',
                                        'other' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    ];
                                    $typeNames = [
                                        'examination' => 'Phòng khám',
                                        'diagnostic' => 'Cận lâm sàng',
                                        'surgery' => 'Phẫu thuật',
                                        'other' => 'Khác',
                                    ];
                                    $typeClass = $typeColors[$room->room_type] ?? $typeColors['other'];
                                    $typeName = $typeNames[$room->room_type] ?? 'Khác';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $typeClass }}">
                                    {{ $typeName }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @php $count = 0; @endphp
                                    @foreach($room->specialties as $sp)
                                        @if($count < 3)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 border border-green-200">
                                                {{ $sp->name }}
                                            </span>
                                        @endif
                                        @php $count++; @endphp
                                    @endforeach
                                    @if($count > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            +{{ $count - 3 }}
                                        </span>
                                    @endif
                                    @if($count == 0)
                                        <span class="text-xs text-gray-400 italic">Tất cả</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                {{ $room->capacity ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($room->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        Tạm đóng
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openEdit({{ $room->toJson() }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    
                                    <form action="{{ route('admin.rooms.toggle-active', $room->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-gray-500 hover:text-gray-800 transition-colors" title="{{ $room->is_active ? 'Tạm đóng' : 'Mở lại' }}">
                                            <i class="fa-solid {{ $room->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            onclick="return confirm('Bạn có chắc muốn xoá phòng này?')"
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
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fa-solid fa-door-open text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">Chưa có phòng khám nào</h3>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($rooms->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white">
                {{ $rooms->links() }}
            </div>
            @endif
        </div>

        <!-- Modal Thêm/Sửa Phòng -->
        <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="open = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="open" 
                    x-transition:enter="ease-out duration-300" 
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="ease-in duration-200" 
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                    
                    <form x-bind:action="mode === 'create' ? '{{ route('admin.rooms.store') }}' : '{{ url('admin/rooms') }}/' + form.id" method="POST">
                        @csrf
                        <template x-if="mode === 'edit'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900" id="modal-title" x-text="mode === 'create' ? 'Thêm phòng mới' : 'Chỉnh sửa phòng'"></h3>
                        </div>
                        
                        <div class="px-4 py-5 sm:p-6 space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên phòng <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="form.name" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="VD: Phòng khám Nội 1">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số phòng</label>
                                    <input type="text" name="room_number" x-model="form.room_number"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="VD: P101">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại phòng <span class="text-red-500">*</span></label>
                                    <select name="room_type" x-model="form.room_type" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                        <option value="">-- Chọn loại phòng --</option>
                                        <option value="examination">Phòng khám</option>
                                        <option value="diagnostic">Cận lâm sàng</option>
                                        <option value="surgery">Phẫu thuật</option>
                                        <option value="other">Khác</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Toà nhà</label>
                                    <select name="building" x-model="form.building" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                        <option value="">Không xác định</option>
                                        <option value="Nhà K1">Nhà K1</option>
                                        <option value="Nhà K2">Nhà K2</option>
                                        <option value="Nhà K3">Nhà K3</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tầng</label>
                                    <input type="text" name="floor" x-model="form.floor"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="VD: 1">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sức chứa (người)</label>
                                    <input type="number" name="capacity" x-model="form.capacity" min="1" max="200"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none" placeholder="VD: 5">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                                    <label class="flex items-center cursor-pointer mt-2">
                                        <div class="relative">
                                            <input type="checkbox" name="is_active" x-model="form.is_active" class="sr-only" value="1">
                                            <div class="block bg-gray-200 w-10 h-6 rounded-full transition-colors" :class="form.is_active ? 'bg-blue-600' : 'bg-gray-200'"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform" :class="form.is_active ? 'transform translate-x-4' : ''"></div>
                                        </div>
                                        <div class="ml-3 text-sm font-medium" :class="form.is_active ? 'text-green-600' : 'text-gray-500'" x-text="form.is_active ? 'Đang hoạt động' : 'Tạm đóng'"></div>
                                    </label>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Chuyên khoa phục vụ <span class="text-xs text-gray-400 font-normal">(Bỏ trống nếu phục vụ chung)</span></label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($specialties as $sp)
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="specialty_ids[]" value="{{ $sp->id }}" x-model="form.specialty_ids"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">{{ $sp->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                <i class="fa-solid fa-save mr-2 mt-0.5"></i> Lưu phòng
                            </button>
                            <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Huỷ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
