<div class="li-course-card">
  <a href="<?php echo e(route('course.show', $course)); ?>" class="li-card-thumb-link">
    <div class="li-card-thumb">
      <?php if(!empty($course->thumbnail)): ?>
        <img src="<?php echo e($course->thumbnail); ?>" alt="<?php echo e($course->title); ?>" loading="lazy"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="li-card-thumb-fallback" style="display:none"><?php echo e(strtoupper(substr($course->title,0,2))); ?></div>
      <?php else: ?>
        <?php
          $colors = [
            ['#1d4ed8','#3b82f6'], ['#166534','#16a34a'], ['#7c2d12','#ea580c'],
            ['#581c87','#9333ea'], ['#0f172a','#334155'], ['#0e7490','#06b6d4'],
          ];
          $pair = $colors[crc32($course->id ?? $course->title) % count($colors)];
        ?>
        <div class="li-card-thumb-fallback" style="background:linear-gradient(135deg,<?php echo e($pair[0]); ?> 0%,<?php echo e($pair[1]); ?> 100%)">
          <?php echo e(strtoupper(substr($course->title,0,2))); ?>

        </div>
      <?php endif; ?>
      <?php if($course->durasi_format): ?>
        <span class="li-card-duration"><?php echo e($course->durasi_format); ?></span>
      <?php endif; ?>
    </div>
  </a>
  <div class="li-card-body">
    <div class="li-card-type">Course</div>
    <a href="<?php echo e(route('course.show', $course)); ?>" class="li-card-title-link">
      <div class="li-card-title"><?php echo e($course->title); ?></div>
    </a>
    <div class="li-card-by">By: <?php echo e($course->instructor_name ?: 'LinkedIn'); ?></div>
  </div>
</div><?php /**PATH D:\LinkedinLearning\resources\views/home/_course_card.blade.php ENDPATH**/ ?>