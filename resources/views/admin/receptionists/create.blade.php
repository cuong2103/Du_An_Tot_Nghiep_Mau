<x-layouts.admin title="Thêm Lễ tân mới">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Thêm Lễ tân mới</h2>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.receptionists.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Thông tin cơ bản</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập <span class="text-red-500">*</span></label>
                <input type="text" name="username" value="{{ old('username') }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg outline-none font-mono">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                <input type="password" name="password" required minlength="6" class="block w-full py-2 px-3 border border-gray-300 rounded-lg outline-none">
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Thông tin công việc</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chức danh <span class="text-red-500">*</span></label>
                <input type="text" name="position" value="{{ old('position', 'Lễ tân') }}" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                <input type="text" name="department" value="{{ old('department', 'Phòng Khám Bệnh') }}" class="block w-full py-2 px-3 border border-gray-300 rounded-lg outline-none">
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.receptionists.index') }}" class="px-6 py-2 bg-gray-100 rounded-lg text-sm">Huỷ</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm">Lưu lễ tân</button>
        </div>
    </form>
</x-layouts.admin>
