<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Quản lý Phòng khám']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Quản lý Phòng khám']); ?>
    <div>
        <!-- Header & Alert -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Quản lý Phòng khám</h2>
                <p class="text-gray-500 mt-1">Danh sách phòng ban, khu vực khám chữa bệnh</p>
            </div>
            <div>
                <a href="<?php echo e(route('admin.rooms.create')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Thêm phòng mới
                </a>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-3 border border-green-200">
                <i class="fa-solid fa-circle-check text-green-500"></i>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        
        <?php if(session('error')): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 flex items-center gap-3 border border-red-200">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200">
                <ul class="list-disc pl-5 text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Filter Form -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form action="<?php echo e(route('admin.rooms.index')); ?>" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/3">
                    <select name="building" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả toà nhà</option>
                        <option value="Nhà K1" <?php echo e(request('building') == 'Nhà K1' ? 'selected' : ''); ?>>Nhà K1</option>
                        <option value="Nhà K2" <?php echo e(request('building') == 'Nhà K2' ? 'selected' : ''); ?>>Nhà K2</option>
                        <option value="Nhà K3" <?php echo e(request('building') == 'Nhà K3' ? 'selected' : ''); ?>>Nhà K3</option>
                    </select>
                </div>
                <div class="w-full sm:w-1/3">
                    <select name="room_type" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả loại phòng</option>
                        <option value="examination" <?php echo e(request('room_type') == 'examination' ? 'selected' : ''); ?>>Phòng khám</option>
                        <option value="diagnostic" <?php echo e(request('room_type') == 'diagnostic' ? 'selected' : ''); ?>>Cận lâm sàng</option>
                        <option value="surgery" <?php echo e(request('room_type') == 'surgery' ? 'selected' : ''); ?>>Phẫu thuật</option>
                        <option value="other" <?php echo e(request('room_type') == 'other' ? 'selected' : ''); ?>>Khác</option>
                    </select>
                </div>
                <div class="w-full sm:w-1/3">
                    <select name="status" class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" <?php echo e(request('status') === '1' ? 'selected' : ''); ?>>Đang hoạt động</option>
                        <option value="0" <?php echo e(request('status') === '0' ? 'selected' : ''); ?>>Tạm đóng</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Lọc
                    </button>
                    <a href="<?php echo e(route('admin.rooms.index')); ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
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
                        <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="<?php echo e(route('admin.rooms.show', $room->id)); ?>" class="font-bold text-blue-600 hover:text-blue-800 transition-colors"><?php echo e($room->name); ?></a>
                                <?php if($room->room_number): ?>
                                    <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        <?php echo e($room->room_number); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php if($room->building || $room->floor): ?>
                                    <?php echo e($room->building ?? '—'); ?> - <?php echo e($room->floor ? 'Tầng ' . $room->floor : '—'); ?>

                                <?php else: ?>
                                    <span class="text-gray-400 italic">Chưa có</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
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
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?php echo e($typeClass); ?>">
                                    <?php echo e($typeName); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <?php $count = 0; ?>
                                    <?php $__currentLoopData = $room->specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($count < 3): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 border border-green-200">
                                                <?php echo e($sp->name); ?>

                                            </span>
                                        <?php endif; ?>
                                        <?php $count++; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($count > 3): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            +<?php echo e($count - 3); ?>

                                        </span>
                                    <?php endif; ?>
                                    <?php if($count == 0): ?>
                                        <span class="text-xs text-gray-400 italic">Tất cả</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                <?php echo e($room->capacity ?? '—'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if($room->is_active): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        Tạm đóng
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="<?php echo e(route('admin.rooms.show', $room->id)); ?>" class="text-teal-600 hover:text-teal-900 transition-colors" title="Xem chi tiết">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.rooms.edit', $room->id)); ?>" class="text-blue-600 hover:text-blue-900 transition-colors" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    
                                    <form action="<?php echo e(route('admin.rooms.toggle-active', $room->id)); ?>" method="POST" class="inline-block">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="text-gray-500 hover:text-gray-800 transition-colors" title="<?php echo e($room->is_active ? 'Tạm đóng' : 'Mở lại'); ?>">
                                            <i class="fa-solid <?php echo e($room->is_active ? 'fa-eye-slash' : 'fa-eye'); ?>"></i>
                                        </button>
                                    </form>

                                    <form action="<?php echo e(route('admin.rooms.destroy', $room->id)); ?>" method="POST" class="inline-block">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
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
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
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
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($rooms->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-100 bg-white">
                <?php echo e($rooms->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH F:\Du_An_Tot_Nghiep_Mau\resources\views/admin/rooms/index.blade.php ENDPATH**/ ?>