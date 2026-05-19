<?php $__env->startSection("title","My Library - LinkedIn Learning"); ?>
<?php $__env->startSection("content"); ?>
<div class="mb-6"><h1 class="text-2xl font-semibold text-gray-800">My Library</h1></div>
<div class="flex gap-6">
    <aside class="w-44 flex-shrink-0">
        <nav class="bg-white rounded-lg shadow-sm overflow-hidden">
            <?php $__currentLoopData = ["in-progress"=>"In Progress","saved"=>"Saved","completed"=>"Completed"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey=>$tabLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('library.tab',$tabKey)); ?>"
               class="flex items-center gap-2 px-4 py-3 text-sm border-l-2 transition <?php echo e($tab===$tabKey ? 'border-blue-700 bg-blue-50 text-blue-700 font-semibold' : 'border-transparent text-gray-600 hover:bg-gray-50'); ?>">
                <?php echo e($tabLabel); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </aside>
    <div class="flex-1">
        <?php if($enrollments->count()>0): ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 flex gap-4 hover:shadow-md transition">
                <div class="w-24 h-16 bg-gradient-to-br from-blue-800 to-blue-600 rounded flex-shrink-0 flex items-center justify-center">
                    <span class="text-white text-2xl font-black opacity-30"><?php echo e(strtoupper(substr($enrollment->course->title,0,1))); ?></span>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800 mb-0.5"><?php echo e($enrollment->course->title); ?></h3>
                    <p class="text-xs text-gray-500 mb-1"><?php echo e($enrollment->course->instructor_name); ?></p>
                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                        <span><?php echo e($enrollment->course->durasi_format); ?></span><span>•</span>
                        <span><?php echo e($enrollment->course->level); ?></span><span>•</span>
                        <span><?php echo e($enrollment->course->category->name); ?></span>
                    </div>
                    <?php if($tab==="in-progress"): ?>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                            <div class="bg-blue-600 h-1.5 rounded-full" style="width:<?php echo e($enrollment->progress_percent); ?>%"></div>
                        </div>
                        <span class="text-xs text-gray-500"><?php echo e($enrollment->progress_percent); ?>%</span>
                    </div>
                    <?php endif; ?>
                    <?php if($tab==="completed"): ?>
                    <span class="text-xs text-green-600 font-medium">✓ Selesai</span>
                    <?php endif; ?>
                </div>
                <div class="flex-shrink-0 flex items-center">
                    <?php if($tab==="saved"): ?>
                    <form method="POST" action="<?php echo e(route('library.enroll',$enrollment->course)); ?>"><?php echo csrf_field(); ?>
                        <button class="bg-blue-700 hover:bg-blue-800 text-white text-xs px-4 py-2 rounded transition">Mulai</button>
                    </form>
                    <?php elseif($tab==="in-progress"): ?>
                    <a href="#" class="bg-blue-700 hover:bg-blue-800 text-white text-xs px-4 py-2 rounded transition">Lanjutkan</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="text-center py-20 text-gray-400">
            <p class="text-5xl mb-4"><?php echo e($tab==="in-progress" ? "📖" : ($tab==="saved" ? "🔖" : "✅")); ?></p>
            <p class="font-medium text-gray-600">
                <?php if($tab==="in-progress"): ?> Belum ada course yang sedang dikerjakan
                <?php elseif($tab==="saved"): ?> Belum ada course yang disimpan
                <?php else: ?> Belum ada course yang selesai <?php endif; ?>
            </p>
            <a href="<?php echo e(route('browse')); ?>" class="mt-4 inline-block bg-blue-700 text-white text-sm px-5 py-2 rounded hover:bg-blue-800 transition">Browse Course</a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\LinkedinLearning\resources\views/library/index.blade.php ENDPATH**/ ?>