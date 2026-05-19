@extends('layouts.app')
@section('title', 'My Career Journey — LinkedIn Learning')

@section('content')
<style>
  .page-title { font-size:24px; font-weight:700; color:#1d1d1d; margin-bottom:4px; }
  .page-sub   { font-size:15px; color:#666; margin-bottom:28px; }

  /* ── HERO CARD ── */
  .journey-hero {
    background: linear-gradient(135deg, #0a3d6b, #004182);
    border-radius: 8px; color:#fff; padding:32px; margin-bottom:24px;
    display:flex; justify-content:space-between; align-items:center; gap:24px;
  }
  .journey-hero-left h2 { font-size:22px; font-weight:700; margin-bottom:6px; }
  .journey-hero-left p  { font-size:14px; color:rgba(255,255,255,.75); margin-bottom:16px; }
  .role-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.12); border-radius:20px; padding:5px 14px; font-size:13px; }
  .arrow-icon { font-size:16px; }
  .goal-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.2); border-radius:20px; padding:5px 14px; font-size:13px; font-weight:600; }

  /* Progress ring */
  .progress-ring-wrap { position:relative; width:90px; height:90px; flex-shrink:0; }
  .progress-ring-wrap svg { transform:rotate(-90deg); }
  .ring-bg   { fill:none; stroke:rgba(255,255,255,.2); stroke-width:6; }
  .ring-fill { fill:none; stroke:#fff; stroke-width:6; stroke-linecap:round; transition:stroke-dashoffset .5s; }
  .ring-label { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; }
  .ring-pct   { font-size:20px; font-weight:700; display:block; }
  .ring-sub   { font-size:11px; color:rgba(255,255,255,.7); }

  /* ── SET GOAL CARD ── */
  .set-goal-card { background:#fff; border:2px dashed #d0e8ff; border-radius:8px; padding:40px; text-align:center; margin-bottom:24px; }
  .set-goal-card h3 { font-size:18px; font-weight:700; color:#1d1d1d; margin-bottom:8px; }
  .set-goal-card p  { font-size:14px; color:#666; margin-bottom:24px; }

  /* ── FORM ── */
  .form-card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:24px; margin-bottom:20px; }
  .form-card h3 { font-size:16px; font-weight:700; color:#1d1d1d; margin-bottom:16px; }
  .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px; }
  .form-group { margin-bottom:14px; }
  .form-group label { display:block; font-size:13px; font-weight:600; color:#444; margin-bottom:5px; }
  .form-group input, .form-group select, .form-group textarea {
    width:100%; padding:9px 12px; border:1px solid #ccc; border-radius:4px;
    font-size:14px; font-family:inherit; outline:none; color:#1d1d1d;
  }
  .form-group input:focus, .form-group select:focus { border-color:#0a66c2; box-shadow:0 0 0 2px #e8f3ff; }
  .btn-primary { height:38px; padding:0 20px; background:#1d1d1d; color:#fff; border:none; border-radius:24px; font-size:14px; font-weight:600; cursor:pointer; font-family:inherit; }
  .btn-primary:hover { background:#333; }
  .btn-outline-sm { height:32px; padding:0 14px; background:transparent; color:#555; border:1px solid #ccc; border-radius:24px; font-size:13px; cursor:pointer; font-family:inherit; }
  .btn-outline-sm:hover { border-color:#1d1d1d; color:#1d1d1d; }
  .btn-danger-sm { height:32px; padding:0 14px; background:transparent; color:#c0392b; border:1px solid #e0e0e0; border-radius:24px; font-size:13px; cursor:pointer; font-family:inherit; }
  .btn-danger-sm:hover { background:#fff0f0; border-color:#c0392b; }

  /* ── LEARNING PLAN ── */
  .plan-wrap { display:grid; grid-template-columns:1fr 300px; gap:24px; }

  .module-card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; margin-bottom:16px; overflow:hidden; }
  .module-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f0f0f0; cursor:pointer; }
  .module-header:hover { background:#f9f9f9; }
  .module-title { font-size:15px; font-weight:700; color:#1d1d1d; }
  .module-desc  { font-size:13px; color:#666; margin-top:2px; }
  .module-actions { display:flex; gap:8px; align-items:center; flex-shrink:0; }
  .module-body { padding:16px 20px; }

  .course-item { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f5f5f5; }
  .course-item:last-child { border-bottom:none; }
  .course-thumb { width:52px; height:36px; background:linear-gradient(135deg,#0a66c2,#004182); border-radius:4px; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,.25); font-size:14px; font-weight:900; flex-shrink:0; }
  .course-info  { flex:1; }
  .course-name  { font-size:14px; font-weight:600; color:#1d1d1d; }
  .course-meta2 { font-size:12px; color:#888; margin-top:2px; }
  .done-badge   { font-size:12px; color:#2e7d32; font-weight:600; background:#e8f5e9; padding:2px 8px; border-radius:10px; flex-shrink:0; }
  .todo-badge   { font-size:12px; color:#888; background:#f5f5f5; padding:2px 8px; border-radius:10px; flex-shrink:0; }

  /* Add course form inside module */
  .add-course-form { display:flex; gap:8px; margin-top:14px; padding-top:14px; border-top:1px dashed #e0e0e0; }
  .add-course-form select { flex:1; padding:8px 10px; border:1px solid #ccc; border-radius:4px; font-size:13px; font-family:inherit; }

  /* Sidebar */
  .sidebar-tip { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:20px; }
  .sidebar-tip h3 { font-size:15px; font-weight:700; color:#1d1d1d; margin-bottom:12px; }
  .tip-item { display:flex; gap:10px; margin-bottom:12px; font-size:13px; color:#444; line-height:1.5; }
  .tip-icon { font-size:18px; flex-shrink:0; }

  /* Flash */
  .flash { background:#e8f5e9; border:1px solid #a5d6a7; color:#2e7d32; border-radius:4px; padding:10px 14px; margin-bottom:16px; font-size:14px; }

  /* Toggle collapse */
  .module-body.collapsed { display:none; }
  .chevron { transition:transform .2s; }
  .chevron.open { transform:rotate(180deg); }
</style>

<h1 class="page-title">My Career Journey</h1>
<p class="page-sub">Pantau progres belajar dan rencanakan jalur karir Anda.</p>

@if(session('success'))
<div class="flash">{{ session('success') }}</div>
@endif

@if(!$goal)
{{-- Belum set goal --}}
<div class="set-goal-card">
  <h3>🎯 Mulai perjalanan karir Anda</h3>
  <p>Tentukan tujuan karir Anda dan buat rencana belajar yang terstruktur.</p>

  <div class="form-card" style="text-align:left;max-width:500px;margin:0 auto">
    <h3>Set Career Goal</h3>
    <form method="POST" action="{{ route('journey.goal') }}">
      @csrf
      <div class="form-group">
        <label>Role Saat Ini</label>
        <input type="text" name="role_saat_ini" placeholder="cth: Multimedia Coordinator" value="{{ old('role_saat_ini') }}">
      </div>
      <div class="form-group">
        <label>Goal Karir Anda *</label>
        <input type="text" name="goal_title" placeholder="cth: Become a Web Developer" required value="{{ old('goal_title') }}">
      </div>
      <button type="submit" class="btn-primary">Simpan Goal</button>
    </form>
  </div>
</div>

@else
{{-- HERO: progress overview --}}
@php
  $pct   = $goal->progress_percent;
  $circ  = 2 * M_PI * 36;
  $offset= $circ - ($pct / 100) * $circ;
@endphp
<div class="journey-hero">
  <div class="journey-hero-left">
    <h2>My Career Plan</h2>
    <p>Lacak progres belajar Anda menuju tujuan karir.</p>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
      @if($goal->role_saat_ini)
      <span class="role-badge">{{ $goal->role_saat_ini }}</span>
      <span class="arrow-icon">→</span>
      @endif
      <span class="goal-badge">🎯 {{ $goal->goal_title }}</span>
    </div>
  </div>
  <div style="text-align:center">
    <div class="progress-ring-wrap">
      <svg width="90" height="90" viewBox="0 0 90 90">
        <circle class="ring-bg"   cx="45" cy="45" r="36"/>
        <circle class="ring-fill" cx="45" cy="45" r="36"
          stroke-dasharray="{{ $circ }}"
          stroke-dashoffset="{{ $offset }}"/>
      </svg>
      <div class="ring-label">
        <span class="ring-pct">{{ $pct }}%</span>
        <span class="ring-sub">selesai</span>
      </div>
    </div>
  </div>
</div>

{{-- PLAN --}}
<div class="plan-wrap">
  <div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <h2 style="font-size:18px;font-weight:700;color:#1d1d1d">My Learning Plan</h2>
      <button onclick="document.getElementById('add-module-form').classList.toggle('hidden')" class="btn-outline-sm">+ Tambah Modul</button>
    </div>

    {{-- Form tambah modul --}}
    <div id="add-module-form" class="form-card hidden" style="margin-bottom:20px">
      <h3>Tambah Modul Baru</h3>
      <form method="POST" action="{{ route('journey.module') }}">
        @csrf
        <div class="form-group">
          <label>Judul Modul</label>
          <input type="text" name="judul_modul" placeholder="cth: Building Blocks of Web Development" required>
        </div>
        <div class="form-group">
          <label>Deskripsi (opsional)</label>
          <textarea name="deskripsi" rows="2" placeholder="Deskripsi singkat modul ini..."></textarea>
        </div>
        <button type="submit" class="btn-primary">Simpan Modul</button>
      </form>
    </div>

    {{-- Modul list --}}
    @forelse($goal->modules as $i => $module)
    @php
      $moduleCourses = $module->planCourses()->with('course')->get();
      $totalM  = $moduleCourses->count();
      $selesaiM= $moduleCourses->filter(fn($pc) =>
        \App\Models\Enrollment::where('user_id', auth()->id())
          ->where('course_id', $pc->course_id)
          ->where('status','completed')->exists()
      )->count();
    @endphp
    <div class="module-card">
      <div class="module-header" onclick="toggleModule({{ $module->id }})">
        <div>
          <div class="module-title">{{ $module->judul_modul }}</div>
          @if($module->deskripsi)
          <div class="module-desc">{{ Str::limit($module->deskripsi, 80) }}</div>
          @endif
        </div>
        <div class="module-actions">
          <span style="font-size:13px;color:#888">{{ $selesaiM }}/{{ $totalM }} selesai</span>
          <form method="POST" action="{{ route('journey.module.delete', $module) }}" onsubmit="return confirm('Hapus modul ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger-sm">Hapus</button>
          </form>
          <span class="chevron" id="chevron-{{ $module->id }}">▼</span>
        </div>
      </div>

      <div class="module-body" id="module-body-{{ $module->id }}">
        {{-- Course list --}}
        @forelse($moduleCourses as $planCourse)
        @php
          $c = $planCourse->course;
          $isDone = \App\Models\Enrollment::where('user_id', auth()->id())
            ->where('course_id', $c->id)->where('status','completed')->exists();
        @endphp
        <div class="course-item">
          <div class="course-thumb">{{ strtoupper(substr($c->title,0,1)) }}</div>
          <div class="course-info">
            <div class="course-name">{{ $c->title }}</div>
            <div class="course-meta2">{{ $c->instructor_name }} · {{ $c->durasi_format }}</div>
          </div>
          @if($isDone)
            <span class="done-badge">✓ Selesai</span>
          @else
            <span class="todo-badge">Belum</span>
          @endif
          <a href="{{ route('course.show', $c) }}" class="btn-outline-sm" style="text-decoration:none">Buka</a>
        </div>
        @empty
        <p style="font-size:13px;color:#888;text-align:center;padding:12px 0">Belum ada course. Tambahkan di bawah.</p>
        @endforelse

        {{-- Tambah course ke modul --}}
        <form method="POST" action="{{ route('journey.module.course', $module) }}" class="add-course-form">
          @csrf
          <select name="course_id" required>
            <option value="">Pilih course untuk ditambahkan...</option>
            @foreach($allCourses as $c)
            <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->durasi_format }})</option>
            @endforeach
          </select>
          <button type="submit" class="btn-primary" style="height:36px;white-space:nowrap">+ Tambah</button>
        </form>
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;color:#888">
      <p style="font-size:16px;margin-bottom:8px">📋 Belum ada modul</p>
      <p style="font-size:14px">Klik "Tambah Modul" untuk memulai rencana belajar Anda.</p>
    </div>
    @endforelse
  </div>

  {{-- SIDEBAR --}}
  <div>
    {{-- Edit Goal --}}
    <div class="form-card">
      <h3>Edit Goal</h3>
      <form method="POST" action="{{ route('journey.goal') }}">
        @csrf
        <div class="form-group">
          <label>Role Saat Ini</label>
          <input type="text" name="role_saat_ini" value="{{ $goal->role_saat_ini }}" placeholder="Role sekarang">
        </div>
        <div class="form-group">
          <label>Goal Karir</label>
          <input type="text" name="goal_title" value="{{ $goal->goal_title }}" required>
        </div>
        <button type="submit" class="btn-primary">Update</button>
      </form>
    </div>

    {{-- Tips --}}
    <div class="sidebar-tip">
      <h3>💡 Tips Belajar</h3>
      <div class="tip-item"><span class="tip-icon">📅</span> Luangkan 30 menit setiap hari untuk konsistensi.</div>
      <div class="tip-item"><span class="tip-icon">🎯</span> Fokus satu modul sebelum pindah ke berikutnya.</div>
      <div class="tip-item"><span class="tip-icon">✅</span> Tandai video selesai untuk memantau progres.</div>
      <div class="tip-item"><span class="tip-icon">🔁</span> Review materi yang sulit lebih dari sekali.</div>
    </div>
  </div>
</div>
@endif

<style>.hidden { display:none !important; }</style>
@push('scripts')
<script>
function toggleModule(id) {
  const body    = document.getElementById('module-body-' + id);
  const chevron = document.getElementById('chevron-' + id);
  body.classList.toggle('collapsed');
  chevron.classList.toggle('open');
}
</script>
@endpush
@endsection