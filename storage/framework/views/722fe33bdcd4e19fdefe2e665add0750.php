<?php ($title = $role['title'].' Role Guide - LinkedIn Learning'); ?>

<?php $__env->startSection('title', $role['title'].' Role Guide - LinkedIn Learning'); ?>

<?php $__env->startSection('content'); ?>
<style>
  .role-page{max-width:1180px;margin:0 auto;color:#1f2328}
  .role-hero{display:grid;grid-template-columns:minmax(0,1fr) 470px;gap:34px;align-items:start;background:#fff;border:1px solid #e1e5e9;border-radius:8px;padding:30px}
  .role-eyebrow{font-size:14px;color:#666;margin-bottom:10px}
  .role-title{font-size:42px;line-height:1.12;font-weight:800;color:#111827;margin:0 0 18px;letter-spacing:0}
  .role-summary{font-size:17px;line-height:1.55;color:#3f3f46;margin:0 0 22px}
  .role-actions{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
  .primary-action{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 18px;border-radius:999px;background:#0a66c2;color:#fff;font-size:15px;font-weight:800;text-decoration:none;border:1px solid #0a66c2}
  .primary-action:hover{background:#004182}
  .secondary-action{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 16px;border-radius:999px;background:#fff;color:#0a66c2;font-size:15px;font-weight:800;text-decoration:none;border:1px solid #0a66c2}
  .secondary-action:hover{background:#e8f3ff}
  .hero-media{position:relative;overflow:hidden;border-radius:8px;border:1px solid #d8dee4;background:#e5e7eb;aspect-ratio:16/10}
  .hero-media img{width:100%;height:100%;object-fit:cover;display:block}
  .play-button{position:absolute;inset:0;margin:auto;width:66px;height:66px;border-radius:50%;border:0;background:rgba(0,0,0,.62);color:#fff;display:flex;align-items:center;justify-content:center}
  .play-button svg{width:30px;height:30px;margin-left:4px}

  .role-section{margin-top:24px;background:#fff;border:1px solid #e1e5e9;border-radius:8px;padding:24px}
  .section-head{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:18px}
  .section-title{font-size:24px;line-height:1.25;font-weight:800;color:#111827;margin:0}
  .section-note{font-size:14px;color:#666;margin:7px 0 0}
  .text-link{font-size:15px;font-weight:800;color:#0a66c2;text-decoration:none;white-space:nowrap}
  .text-link:hover{text-decoration:underline}
  .skill-count{font-size:16px;font-weight:800;color:#111827;margin:14px 0 12px}
  .skill-cloud{display:flex;flex-wrap:wrap;gap:10px}
  .skill-pill{display:inline-flex;align-items:center;min-height:35px;border:1px solid #cfd6dd;border-radius:999px;background:#fff;color:#1f2328;padding:0 16px;font-size:14px;font-weight:700;text-decoration:none}
  .skill-pill:hover{background:#f3f2ef;border-color:#a8b0b8}
  .skill-pill.active{background:#057642;color:#fff;border-color:#057642}
  .profile-note{display:flex;align-items:flex-start;gap:10px;color:#5f6368;font-size:14px;line-height:1.45;margin-top:18px}
  .info-dot{width:22px;height:22px;border-radius:4px;background:#64748b;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:900;flex:0 0 22px}

  .rail-head{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:18px}
  .rail-controls{display:flex;gap:10px}
  .round-icon{width:40px;height:40px;border-radius:50%;border:1px solid #cfd6dd;background:#fff;color:#111827;display:inline-flex;align-items:center;justify-content:center}
  .round-icon svg{width:20px;height:20px}
  .card-rail{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
  .learning-card{display:grid;grid-template-columns:180px minmax(0,1fr);gap:16px;padding:0;text-decoration:none;color:inherit;border-radius:8px}
  .learning-card:hover .card-title{text-decoration:underline}
  .thumb{position:relative;min-height:106px;border-radius:6px;background:linear-gradient(135deg,#38434f,#0a66c2);overflow:hidden;border:1px solid #d8dee4}
  .thumb img{width:100%;height:100%;object-fit:cover;display:block}
  .thumb-fallback{width:100%;height:100%;min-height:106px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:28px;background:linear-gradient(135deg,#263238,#0a66c2)}
  .duration{position:absolute;right:8px;bottom:8px;background:rgba(17,24,39,.82);color:#fff;border-radius:3px;padding:2px 6px;font-size:12px;font-weight:700}
  .card-kind{font-size:13px;color:#5f6368;margin-bottom:3px}
  .card-title{font-size:18px;line-height:1.28;font-weight:800;color:#111827;margin-bottom:7px}
  .card-meta{font-size:13px;color:#4b5563;margin-bottom:8px}
  .card-desc{font-size:14px;color:#4b5563;line-height:1.45;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .learners{font-size:13px;color:#5f6368;margin-top:9px}

  .skill-tabs{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:22px}
  .cta-band{margin-top:24px;background:#f3f2ef;border-radius:8px;padding:22px;display:flex;align-items:center;justify-content:space-between;gap:18px}
  .cta-band p{margin:0;color:#374151;font-size:15px;line-height:1.5;max-width:650px}

  @media (max-width:1040px){
    .role-hero{grid-template-columns:1fr}
    .hero-media{max-width:680px}
    .card-rail{grid-template-columns:1fr}
  }
  @media (max-width:680px){
    .role-page{margin:0}
    .role-hero,.role-section{padding:18px;border-radius:6px}
    .role-title{font-size:34px}
    .learning-card{grid-template-columns:1fr}
    .cta-band{align-items:flex-start;flex-direction:column}
  }
</style>

<div class="role-page">
  <section class="role-hero" aria-label="Role guide overview">
    <div>
      <div class="role-eyebrow"><?php echo e($role['eyebrow']); ?></div>
      <h1 class="role-title"><?php echo e($role['title']); ?></h1>
      <p class="role-summary"><?php echo e($role['summary']); ?></p>
      <div class="role-actions">
        <a class="primary-action" href="<?php echo e(route('journey.index')); ?>">Create my learning plan</a>
        <a class="secondary-action" href="<?php echo e(route('browse', ['type' => 'B', 'q' => $role['browse_query']])); ?>">Browse related courses</a>
      </div>
    </div>
    <div class="hero-media" aria-label="<?php echo e($role['title']); ?> preview">
      <img src="<?php echo e($role['hero_image']); ?>" alt="<?php echo e($role['title']); ?> preview">
      <button class="play-button" type="button" aria-label="Play role overview">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
      </button>
    </div>
  </section>

  <section class="role-section" aria-label="Role guide resources">
    <div class="section-head">
      <div>
        <h2 class="section-title">Top <?php echo e($role['title']); ?> skills</h2>
        <p class="section-note">Provided by LinkedIn.</p>
      </div>
      <a class="text-link" href="<?php echo e(route('browse', ['type' => 'B', 'q' => $role['browse_query']])); ?>">Follow skills</a>
    </div>
    <div class="skill-count"><?php echo e(count($skills)); ?> skills to develop</div>
    <div class="skill-cloud">
      <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a class="skill-pill" href="<?php echo e(route('roles.show', ['role' => $role['slug'], 'skill' => $skill])); ?>"><?php echo e($skill); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="profile-note">
      <span class="info-dot">i</span>
      <span>You can manage role-related learning from your career journey and use these skills to discover relevant courses.</span>
    </div>
  </section>

  <section class="role-section">
    <div class="rail-head">
      <h2 class="section-title">Understand the basics for this role with short videos</h2>
      <div class="rail-controls" aria-hidden="true">
        <span class="round-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 18-6-6 6-6"/></svg></span>
        <span class="round-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 18 6-6-6-6"/></svg></span>
      </div>
    </div>
    <div class="card-rail">
      <?php $__currentLoopData = $shortVideos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('roles.partials.learning-card', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </section>

  <section class="role-section">
    <div class="rail-head">
      <h2 class="section-title">Build skills for this role</h2>
      <div class="rail-controls" aria-hidden="true">
        <span class="round-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 18-6-6 6-6"/></svg></span>
        <span class="round-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 18 6-6-6-6"/></svg></span>
      </div>
    </div>
    <div class="skill-tabs" aria-label="Skill filters">
      <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a class="skill-pill <?php echo e($skill === $activeSkill ? 'active' : ''); ?>" href="<?php echo e(route('roles.show', ['role' => $role['slug'], 'skill' => $skill])); ?>"><?php echo e($skill); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="card-rail">
      <?php $__currentLoopData = $skillCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('roles.partials.learning-card', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="cta-band">
      <p>Interested in becoming a <?php echo e($role['title']); ?>? Start by creating a learning plan and build the top skills with guided modules from your own course catalog.</p>
      <a class="primary-action" href="<?php echo e(route('journey.index')); ?>">Create my learning plan</a>
    </div>
  </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\LinkedinLearning\resources\views/roles/marketing-manager.blade.php ENDPATH**/ ?>