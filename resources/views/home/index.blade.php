@extends("layouts.app")
@section("title", "Home - LinkedIn Learning")
@section("content")

<style>
  .li-carousel-section { margin-bottom: 32px; }
  .li-carousel-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
  .li-carousel-title { font-size:18px; font-weight:700; color:#1d2226; font-family:'Source Serif 4',Georgia,serif; }
  .li-carousel-nav { display:flex; align-items:center; gap:8px; }
  .li-carousel-btn { width:36px; height:36px; border-radius:50%; border:1.5px solid #c0c0c0; background:white; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:border-color .15s,box-shadow .15s; flex-shrink:0; }
  .li-carousel-btn:hover { border-color:#1d2226; box-shadow:0 2px 8px rgba(0,0,0,.1); }
  .li-carousel-btn svg { pointer-events:none; }
  .li-carousel-overflow { overflow:hidden; width:100%; }
  .li-carousel-track { display:flex; gap:14px; overflow-x:scroll; scroll-behavior:smooth; scrollbar-width:none; -ms-overflow-style:none; padding-bottom:2px; }
  .li-carousel-track::-webkit-scrollbar { display:none; }
  .li-course-card { flex:0 0 210px; min-width:0; display:flex; flex-direction:column; }
  .li-card-thumb-link { display:block; text-decoration:none; }
  .li-card-thumb { position:relative; width:100%; aspect-ratio:16/9; border-radius:6px; overflow:hidden; background:#e5e7eb; }
  .li-card-thumb img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .2s; }
  .li-card-thumb-link:hover .li-card-thumb img { transform:scale(1.03); }
  .li-card-thumb-fallback { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:900; color:rgba(255,255,255,.6); letter-spacing:1px; }
  .li-card-duration { position:absolute; bottom:7px; right:7px; background:rgba(0,0,0,.75); color:#fff; font-size:11px; font-weight:700; padding:2px 6px; border-radius:3px; pointer-events:none; }
  .li-card-body { padding:10px 2px 0; display:flex; flex-direction:column; gap:3px; }
  .li-card-type { font-size:12px; color:#56687a; }
  .li-card-title-link { text-decoration:none; }
  .li-card-title { font-size:14px; font-weight:700; color:#1d2226; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
  .li-card-title-link:hover .li-card-title { color:#0a66c2; }
  .li-card-by { font-size:12px; color:#56687a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .li-progress-card { flex:0 0 210px; min-width:0; background:white; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; display:flex; flex-direction:column; }
  .li-progress-thumb { position:relative; width:100%; aspect-ratio:16/9; }
  .li-progress-thumb-bg { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:900; color:rgba(255,255,255,.4); }
  .li-progress-bar-wrap { height:3px; background:#e5e7eb; flex-shrink:0; }
  .li-progress-bar { height:100%; background:#0a66c2; }
  .li-progress-body { padding:10px 12px 12px; display:flex; flex-direction:column; gap:3px; flex:1; }
  .li-progress-title { font-size:13px; font-weight:700; color:#1d2226; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
  .li-progress-meta { font-size:12px; color:#56687a; }
  .li-progress-foot { display:flex; align-items:center; justify-content:space-between; margin-top:8px; }
  .li-progress-pct { font-size:11px; color:#56687a; }
  .li-progress-cta { font-size:12px; font-weight:700; color:#0a66c2; text-decoration:none; }
  .li-progress-cta:hover { text-decoration:underline; }
  .li-greeting { margin-bottom:28px; }
  .li-greeting h1 { font-family:'Source Serif 4',Georgia,serif; font-size:24px; font-weight:700; color:#1d2226; }
  .li-greeting p { font-size:14px; color:#56687a; margin-top:4px; }
  @media(max-width:640px){.li-course-card,.li-progress-card{flex:0 0 172px;}.li-carousel-title{font-size:16px;}}
</style>

<div class="li-greeting">
  <h1>Welcome back, {{ auth()->user()->name }}!</h1>
  <p>Continue where you left off.</p>
</div>

@if($inProgress->count() > 0)
<section class="li-carousel-section">
  <div class="li-carousel-header">
    <div class="li-carousel-title">Continue Learning</div>
    <div class="li-carousel-nav">
      <button class="li-carousel-btn" onclick="scrollCarousel('inprogress-track',-1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
      <button class="li-carousel-btn" onclick="scrollCarousel('inprogress-track',1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
    </div>
  </div>
  <div class="li-carousel-overflow"><div class="li-carousel-track" id="inprogress-track">
    @foreach($inProgress as $enrollment)
    @php $colors=[['#1d4ed8','#3b82f6'],['#166534','#16a34a'],['#7c2d12','#ea580c'],['#581c87','#9333ea'],['#0f172a','#334155'],['#0e7490','#06b6d4']];$pair=$colors[crc32($enrollment->course->id??$enrollment->course->title)%count($colors)]; @endphp
    <div class="li-progress-card">
      <div class="li-progress-thumb">
        @if(!empty($enrollment->course->thumbnail))
          <img src="{{ $enrollment->course->thumbnail }}" style="width:100%;height:100%;object-fit:cover;display:block" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
          <div class="li-progress-thumb-bg" style="display:none;background:linear-gradient(135deg,{{ $pair[0] }} 0%,{{ $pair[1] }} 100%)">{{ strtoupper(substr($enrollment->course->title,0,2)) }}</div>
        @else
          <div class="li-progress-thumb-bg" style="background:linear-gradient(135deg,{{ $pair[0] }} 0%,{{ $pair[1] }} 100%)">{{ strtoupper(substr($enrollment->course->title,0,2)) }}</div>
        @endif
      </div>
      <div class="li-progress-bar-wrap"><div class="li-progress-bar" style="width:{{ $enrollment->progress_percent }}%"></div></div>
      <div class="li-progress-body">
        <div class="li-progress-title">{{ $enrollment->course->title }}</div>
        <div class="li-progress-meta">{{ $enrollment->course->instructor_name }}</div>
        <div class="li-progress-foot">
          <span class="li-progress-pct">{{ $enrollment->progress_percent }}% complete</span>
          <a href="{{ route('course.show', $enrollment->course) }}" class="li-progress-cta">Continue →</a>
        </div>
      </div>
    </div>
    @endforeach
  </div></div>
</section>
@endif

<section class="li-carousel-section">
  <div class="li-carousel-header">
    <div class="li-carousel-title">Top picks for {{ auth()->user()->name }}</div>
    <div class="li-carousel-nav">
      <button class="li-carousel-btn" onclick="scrollCarousel('toppicks-track',-1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
      <button class="li-carousel-btn" onclick="scrollCarousel('toppicks-track',1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
    </div>
  </div>
  <div class="li-carousel-overflow"><div class="li-carousel-track" id="toppicks-track">
    @foreach($topPicks as $course)@include("home._course_card",compact("course"))@endforeach
  </div></div>
</section>

<section class="li-carousel-section">
  <div class="li-carousel-header">
    <div class="li-carousel-title">30 minutes or less</div>
    <div class="li-carousel-nav">
      <button class="li-carousel-btn" onclick="scrollCarousel('short-track',-1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
      <button class="li-carousel-btn" onclick="scrollCarousel('short-track',1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
    </div>
  </div>
  <div class="li-carousel-overflow"><div class="li-carousel-track" id="short-track">
    @foreach($shortCourses as $course)@include("home._course_card",compact("course"))@endforeach
  </div></div>
</section>

@if($saved->count() > 0)
<section class="li-carousel-section">
  <div class="li-carousel-header">
    <div class="li-carousel-title">Saved</div>
    <div class="li-carousel-nav">
      <button class="li-carousel-btn" onclick="scrollCarousel('saved-track',-1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
      <button class="li-carousel-btn" onclick="scrollCarousel('saved-track',1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
    </div>
  </div>
  <div class="li-carousel-overflow"><div class="li-carousel-track" id="saved-track">
    @foreach($saved as $enrollment)@include("home._course_card",["course"=>$enrollment->course])@endforeach
  </div></div>
</section>
@endif

@push('scripts')
<script>
function scrollCarousel(trackId, dir) {
  const track = document.getElementById(trackId);
  if (!track) return;
  const card = track.querySelector('.li-course-card, .li-progress-card');
  const step = ((card?.offsetWidth ?? 210) + 14) * 3;
  track.scrollBy({ left: dir * step, behavior: 'smooth' });
}
</script>
@endpush
@endsection