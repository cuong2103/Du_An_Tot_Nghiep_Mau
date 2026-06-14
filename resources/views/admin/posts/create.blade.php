<x-layouts.admin title="Thêm bài viết mới">
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
            <a href="{{ route('admin.posts.index') }}" class="hover:text-blue-600 transition-colors">Bài viết</a>
            <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
            <span class="text-gray-800 font-medium">Thêm mới</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Thêm bài viết mới</h2>
    </div>

    <!-- Error Summary -->
    @if($errors->any())
        <div class="bg-red-50 text-red-800 p-4 rounded-lg mb-6 border border-red-200">
            <div class="flex items-center gap-2 mb-2 font-bold">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                Có lỗi xảy ra, vui lòng kiểm tra lại:
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.posts.store') }}" method="POST" x-data="{
        title: '{{ old('title') }}',
        slug: '{{ old('slug') }}',
        thumbnailUrl: '{{ old('thumbnail_url') }}',
        generateSlug() {
            if(!this.slug && this.title) {
                // Basic slugification for preview
                let str = this.title.toLowerCase().trim();
                str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
                str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
                str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
                str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
                str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
                str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
                str = str.replace(/đ/g, 'd');
                str = str.replace(/[^a-z0-9\-]+/g, '-');
                str = str.replace(/\-+/g, '-');
                return str;
            }
            return this.slug;
        }
    }">
        @csrf
        
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Cột trái (2/3) -->
            <div class="w-full lg:w-2/3 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Nội dung bài viết</h3>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="title" required placeholder="Nhập tiêu đề..." class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-base font-medium outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đường dẫn (Slug)</label>
                            <input type="text" name="slug" x-model="slug" placeholder="Để trống để tự tạo từ tiêu đề" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none font-mono text-gray-600">
                            <p class="text-xs text-gray-500 mt-1">
                                URL xem trước: <span class="text-blue-600 font-mono">{{ url('/posts') }}/<span x-text="generateSlug() || '...' "></span></span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt ngắn gọn</label>
                            <textarea name="summary" rows="3" placeholder="Đoạn mô tả ngắn hiển thị ở trang chủ và SEO..." class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">{{ old('summary') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung bài viết <span class="text-red-500">*</span></label>
                            <div class="text-xs text-gray-500 mb-2 italic">* Bạn có thể sử dụng các thẻ HTML cơ bản (h2, p, strong, ul, li...) để trình bày văn bản.</div>
                            <textarea name="content" rows="15" required class="block w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none font-mono">{{ old('content') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải (1/3) -->
            <div class="w-full lg:w-1/3 space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Xuất bản</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <label class="text-sm font-medium text-gray-700">Trạng thái đăng bài</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                        <div class="text-xs text-gray-500 italic mt-[-8px]">Bật để xuất bản ngay, tắt để lưu nháp.</div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại bài viết <span class="text-red-500">*</span></label>
                            <select name="post_type" required class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                <option value="news" {{ old('post_type') === 'news' ? 'selected' : '' }}>Tin tức y tế</option>
                                <option value="service" {{ old('post_type') === 'service' ? 'selected' : '' }}>Dịch vụ khám chữa bệnh</option>
                                <option value="guide" {{ old('post_type') === 'guide' ? 'selected' : '' }}>Hướng dẫn quy trình</option>
                                <option value="announcement" {{ old('post_type') === 'announcement' ? 'selected' : '' }}>Thông báo hệ thống</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chuyên khoa liên quan</label>
                            <select name="specialty_id" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                <option value="">Không liên kết chuyên khoa</option>
                                @foreach($specialties as $sp)
                                    <option value="{{ $sp->id }}" {{ old('specialty_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-[10px] text-gray-500 mt-1">Bài viết sẽ hiển thị ở trang giới thiệu chuyên khoa này.</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Ảnh đại diện</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Ảnh thu nhỏ</label>
                        <input type="url" name="thumbnail_url" x-model="thumbnailUrl" placeholder="https://example.com/image.jpg" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none font-mono">
                        
                        <div class="mt-4 rounded-lg border border-dashed border-gray-300 p-2 flex items-center justify-center bg-gray-50 aspect-video relative overflow-hidden">
                            <template x-if="thumbnailUrl">
                                <img :src="thumbnailUrl" class="w-full h-full object-cover rounded" alt="Preview" @error="thumbnailUrl = ''">
                            </template>
                            <template x-if="!thumbnailUrl">
                                <div class="text-center text-gray-400">
                                    <i class="fa-regular fa-image text-4xl mb-2"></i>
                                    <div class="text-xs font-medium">Chưa có ảnh</div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.posts.index') }}" class="flex-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-center py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm">
                        Huỷ bỏ
                    </a>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu bài viết
                    </button>
                </div>
            </div>
            
        </div>
    </form>
</x-layouts.admin>
