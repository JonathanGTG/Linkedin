<a class="learning-card" href="<?php echo e($item['url']); ?>">
  <div class="thumb">
    <?php if(!empty($item['thumbnail'])): ?>
      <img src="<?php echo e($item['thumbnail']); ?>" alt="<?php echo e($item['title']); ?>" loading="lazy"
           onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <div class="thumb-fallback" style="display:none"><?php echo e(strtoupper(substr($item['title'], 0, 2))); ?></div>
    <?php else: ?>
      <div class="thumb-fallback"><?php echo e(strtoupper(substr($item['title'], 0, 2))); ?></div>
    <?php endif; ?>
    <?php if(!empty($item['duration'])): ?>
      <span class="duration"><?php echo e($item['duration']); ?></span>
    <?php endif; ?>
  </div>
  <div>
    <div class="card-kind"><?php echo e($item['type']); ?></div>
    <div class="card-title"><?php echo e($item['title']); ?></div>
    <div class="card-meta">
      By: <?php echo e($item['author']); ?>

      <?php if(!empty($item['date'])): ?>
        &middot; <?php echo e($item['date']); ?>

      <?php endif; ?>
    </div>
    <?php if(!empty($item['description'])): ?>
      <div class="card-desc"><?php echo e($item['description']); ?></div>
    <?php endif; ?>
    <?php if(!empty($item['learners'])): ?>
      <div class="learners"><?php echo e(number_format($item['learners'])); ?> learners</div>
    <?php endif; ?>
  </div>
</a>
<?php /**PATH D:\LinkedinLearning\resources\views/roles/partials/learning-card.blade.php ENDPATH**/ ?>