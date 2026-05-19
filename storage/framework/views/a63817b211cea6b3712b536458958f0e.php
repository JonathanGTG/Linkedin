<?php $__env->startSection("title","Hands-On Tech — LinkedIn Learning"); ?>
<?php $__env->startSection("content"); ?>
<style>
  .page-wrap{background:#f3f2ef;border-radius:12px;padding:18px}
  .filter-row{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:18px}
  .pill{display:inline-flex;align-items:center;gap:8px;border:1px solid #d1d5db;background:#fff;border-radius:999px;padding:8px 14px;font-size:14px;font-weight:700;color:#111827;cursor:pointer}
  .pill:hover{background:#f9fafb}
  .icon{width:16px;height:16px;display:block}
  .reset{margin-left:10px;font-size:14px;font-weight:700;color:#0a66c2;text-decoration:none}
  .reset:hover{text-decoration:underline}

  .breadcrumb{font-size:14px;color:#6b7280;margin:6px 0 10px}
  .breadcrumb a{color:#111827;text-decoration:none}
  .breadcrumb a:hover{text-decoration:underline}

  .hero{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px 22px;margin-bottom:16px}
  .title{font-size:34px;font-weight:900;color:#111827;margin:0 0 10px;letter-spacing:-.02em}
  .desc{font-size:15px;color:#4b5563;margin:0;max-width:980px;line-height:1.65}
  .topics{margin-top:18px}
  .topics-title{font-size:18px;font-weight:900;color:#111827;margin:0 0 10px}
  .topic-row{display:flex;flex-wrap:wrap;gap:10px}
  .topic{display:inline-flex;align-items:center;gap:8px;border:1px solid #d1d5db;background:#fff;border-radius:999px;padding:10px 14px;font-size:14px;font-weight:800;color:#111827;text-decoration:none}
  .topic:hover{background:#f9fafb}
  .topic.active{border-color:#111827;box-shadow:0 0 0 2px rgba(17,24,39,.06) inset}

  .results-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px}
  .results-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid #e5e7eb}
  .results-count{font-size:14px;color:#374151}
  .sort{display:inline-flex;align-items:center;gap:8px;font-size:14px;font-weight:800;color:#111827}

  .list{padding:8px 0}
  .row{display:flex;gap:16px;padding:16px 18px;border-top:1px solid #eef2f7}
  .row:first-child{border-top:none}
  .thumb{width:220px;height:124px;border-radius:12px;background:linear-gradient(135deg,#e5e7eb,#f3f4f6);flex:0 0 220px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:900;color:rgba(17,24,39,.15)}
  .thumb.practice{background:linear-gradient(135deg,#d1fae5,#ecfdf5);color:rgba(6,95,70,.2)}
  .thumb.project{background:linear-gradient(135deg,#fce7f3,#fff1f2);color:rgba(136,19,55,.18)}
  .thumb.challenge{background:linear-gradient(135deg,#fef3c7,#fffbeb);color:rgba(146,64,14,.18)}
  .meta{flex:1}
  .kicker{font-size:13px;color:#6b7280;margin-bottom:4px}
  .name{font-size:20px;font-weight:900;color:#111827;line-height:1.25;margin-bottom:6px}
  .sub{font-size:14px;color:#374151;margin-bottom:8px}
  .sub b{font-weight:900}
  .blurb{font-size:14px;color:#4b5563;line-height:1.6;max-width:860px}
  .actions{display:flex;align-items:flex-start;gap:10px}
  .more{width:36px;height:36px;border-radius:999px;border:1px solid transparent;background:transparent;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;color:#111827}
  .more:hover{background:#f3f4f6}
  .save{border:1px solid #0a66c2;color:#0a66c2;background:#fff;border-radius:999px;padding:8px 14px;font-size:14px;font-weight:800;cursor:pointer}
  .save:hover{background:#e8f3ff}
  .empty-state{text-align:center;padding:60px 0;color:#6b7280}

  @media (max-width: 820px){
    .row{flex-direction:column}
    .thumb{width:100%;flex:0 0 auto}
  }
</style>

<?php
  $type = request('type');
  $topicMap = [
    'challenge' => [
      'title' => 'Hands-On Practice with Code Challenges',
      'desc' => 'Asah kemampuan coding dengan latihan langsung dan umpan balik, untuk membantu Anda meningkatkan skill lebih cepat.',
    ],
    'practice' => [
      'title' => 'Hands-On Practice with Cybersecurity Labs',
      'desc' => 'Latihan langsung untuk membangun pemahaman praktis lewat skenario dan tantangan yang menyerupai kondisi nyata.',
    ],
    'project' => [
      'title' => 'Hands-On Practice with GitHub Codespaces',
      'desc' => 'Belajar dengan praktik di lingkungan cloud untuk membangun pengalaman yang lebih dekat dengan workflow developer modern.',
    ],
  ];
  $heroTitle = $type && isset($topicMap[$type]) ? $topicMap[$type]['title'] : 'Hands-On Tech';
  $heroDesc = $type && isset($topicMap[$type]) ? $topicMap[$type]['desc'] : 'Advance your tech skills through hands-on learning. Explore hands-on practice options, projects, and challenges.';
?>

<div class="page-wrap">
  <div class="filter-row">
    <button class="pill" type="button">
      <span>Type</span>
      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <button class="pill" type="button">
      <span>Hands-On Practice</span>
      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <button class="pill" type="button">
      <span>Time to Complete</span>
      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <button class="pill" type="button">
      <span>Level</span>
      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <button class="pill" type="button">
      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/>
        <circle cx="8" cy="6" r="2" fill="#111827" stroke="none"/><circle cx="15" cy="12" r="2" fill="#111827" stroke="none"/><circle cx="10" cy="18" r="2" fill="#111827" stroke="none"/>
      </svg>
      <span>All filters</span>
    </button>
    <a class="reset" href="<?php echo e(route('hands-on.index')); ?>">Reset</a>
  </div>

  <div class="breadcrumb">
    <a href="<?php echo e(route('hands-on.index')); ?>">Browse</a><?php if($type): ?> / <a href="<?php echo e(route('hands-on.index')); ?>">Hands-On Tech</a><?php endif; ?>
  </div>

  <div class="hero">
    <h1 class="title"><?php echo e($heroTitle); ?></h1>
    <p class="desc"><?php echo e($heroDesc); ?></p>

    <div class="topics">
      <h2 class="topics-title">Explore Hands-On Tech topics</h2>
      <div class="topic-row">
        <a class="topic <?php echo e($type === 'challenge' ? 'active' : ''); ?>" href="<?php echo e(route('hands-on.index', ['type' => 'challenge'])); ?>">Hands-On Practice with Code Challenges</a>
        <a class="topic <?php echo e($type === 'practice' ? 'active' : ''); ?>" href="<?php echo e(route('hands-on.index', ['type' => 'practice'])); ?>">Hands-On Practice with Cybersecurity Labs</a>
        <a class="topic <?php echo e($type === 'project' ? 'active' : ''); ?>" href="<?php echo e(route('hands-on.index', ['type' => 'project'])); ?>">Hands-On Practice with GitHub Codespaces</a>
      </div>
    </div>
  </div>

  <div class="results-card">
    <div class="results-head">
      <div class="results-count"><?php echo e($labs->total()); ?> Results for “<?php echo e($heroTitle); ?>”</div>
      <div class="sort">
        <span>Best Match</span>
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
      </div>
    </div>

    <?php if($labs->count() > 0): ?>
      <div class="list">
        <?php $__currentLoopData = $labs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="row">
            <div class="thumb <?php echo e($lab->type); ?>"><?php echo e(strtoupper(substr($lab->title,0,1))); ?></div>
            <div class="meta">
              <div class="kicker"><?php echo e($lab->type_label); ?></div>
              <div class="name"><?php echo e($lab->title); ?></div>
              <div class="sub">
                <b><?php echo e($lab->durasi_format); ?></b> · <?php echo e($lab->level); ?>

                <?php if($lab->teknologi): ?> · <?php echo e($lab->teknologi); ?> <?php endif; ?>
              </div>
              <?php if($lab->description): ?>
                <div class="blurb"><?php echo e($lab->description); ?></div>
              <?php endif; ?>
            </div>
            <div class="actions">
              <button class="more" type="button" aria-label="More actions">
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="6" cy="12" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="18" cy="12" r="1.7"/></svg>
              </button>
              <button class="save" type="button">Save</button>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div style="padding:16px 18px;border-top:1px solid #eef2f7"><?php echo e($labs->links()); ?></div>
    <?php else: ?>
      <div class="empty-state">
        <div style="font-size:40px;margin-bottom:12px">🧪</div>
        <div style="font-size:16px;font-weight:800;color:#374151">Belum ada lab tersedia</div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\LinkedinLearning\resources\views/hands-on/index.blade.php ENDPATH**/ ?>