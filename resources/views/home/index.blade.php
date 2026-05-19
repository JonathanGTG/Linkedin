@extends("layouts.app")
@section("title", "Home - LinkedIn Learning")
@section("content")

@php
  $typeCodes = ['Business' => 'B', 'Technology' => 'T', 'Creative' => 'C'];
  $primaryProgress = $inProgress->first();
@endphp

<style>
  body .main-content{min-width:0;overflow-x:hidden}
  .home-page{width:100%;max-width:1180px;margin:0 auto;color:#1d2226}
  .home-hero{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(300px,.75fr);gap:20px;margin-bottom:18px}
  .hero-panel,.resume-panel,.home-section{background:#fff;border:1px solid #dedede;border-radius:8px}
  .hero-panel{padding:28px;min-height:258px;display:flex;flex-direction:column;justify-content:space-between;overflow:hidden;position:relative}
  .hero-panel:after{content:"";position:absolute;right:-80px;bottom:-90px;width:280px;height:230px;background:#e8f3ff;border-radius:50%;z-index:0}
  .hero-content{position:relative;z-index:1;max-width:720px}
  .eyebrow{font-size:14px;color:#56687a;margin-bottom:8px}
  .hero-title{font-size:38px;line-height:1.15;font-weight:800;color:#111827;margin:0 0 12px;letter-spacing:0}
  .hero-copy{font-size:16px;line-height:1.55;color:#3f3f46;margin:0 0 22px;max-width:680px}
  .hero-actions{display:flex;gap:12px;flex-wrap:wrap}
  .primary-btn,.outline-btn{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 18px;border-radius:999px;font-size:15px;font-weight:800;text-decoration:none}
  .primary-btn{background:#0a66c2;color:#fff;border:1px solid #0a66c2}
  .primary-btn:hover{background:#004182}
  .outline-btn{background:#fff;color:#0a66c2;border:1px solid #0a66c2}
  .outline-btn:hover{background:#e8f3ff}
  .stats-row{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:22px;position:relative;z-index:1}
  .stat-card{display:flex;flex-direction:column;gap:4px;background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px;padding:14px;text-decoration:none;color:#1d2226}
  .stat-number{font-size:24px;font-weight:900;color:#111827}
  .stat-label{font-size:13px;font-weight:800;color:#0a66c2}

  .resume-panel{padding:20px;display:flex;flex-direction:column;gap:14px}
  .resume-title{font-size:18px;font-weight:800;margin:0;color:#111827}
  .resume-card{display:block;text-decoration:none;color:inherit}
  .resume-thumb{position:relative;border-radius:7px;overflow:hidden;aspect-ratio:16/9;background:linear-gradient(135deg,#263238,#0a66c2);margin-bottom:12px}
  .resume-thumb img{width:100%;height:100%;object-fit:cover;display:block}
  .resume-fallback{height:100%;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.78);font-size:34px;font-weight:900}
  .resume-course{font-size:16px;font-weight:800;line-height:1.32;color:#111827;margin-bottom:4px}
  .resume-meta{font-size:13px;color:#56687a}
  .progress-wrap{height:4px;background:#e5e7eb;border-radius:999px;overflow:hidden;margin:12px 0 8px}
  .progress-bar{height:100%;background:#0a66c2}
  .empty-resume{border:1px dashed #cfd6dd;border-radius:8px;padding:18px;background:#f8fafc;color:#4b5563;line-height:1.5}

  .home-section{padding:22px;margin-bottom:14px}
  .section-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px}
  .section-title{font-family:'Source Serif 4',Georgia,serif;font-size:22px;line-height:1.2;font-weight:800;color:#111827;margin:0}
  .section-sub{font-size:13px;color:#56687a;margin-top:4px;line-height:1.45}
  .see-all{font-size:14px;font-weight:800;color:#0a66c2;text-decoration:none;white-space:nowrap}
  .see-all:hover{text-decoration:underline}

  .quick-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
  .quick-card{border:1px solid #e0e0e0;border-radius:8px;padding:16px;text-decoration:none;color:#1d2226;background:#fff;min-height:118px}
  .quick-card:hover{box-shadow:0 2px 8px rgba(0,0,0,.08)}
  .quick-title{font-size:18px;font-weight:900;margin-bottom:7px}
  .quick-desc{font-size:13px;color:#56687a;line-height:1.45;margin-bottom:13px}
  .quick-link{font-size:13px;font-weight:800;color:#0a66c2}

  .role-columns{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
  .role-column{border:1px solid #e5e7eb;border-radius:8px;padding:16px;background:#f8fafc}
  .role-column h3{font-size:16px;font-weight:900;margin:0 0 12px;color:#111827}
  .role-list{display:flex;flex-direction:column;gap:8px}
  .role-link{color:#0a66c2;font-size:14px;font-weight:800;text-decoration:none}
  .role-link:hover{text-decoration:underline}

  .topic-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
  .topic-card{border:1px solid #e5e7eb;border-radius:8px;padding:16px;background:#fff}
  .topic-card h3{font-size:16px;font-weight:900;margin:0 0 10px}
  .topic-list{display:flex;flex-wrap:wrap;gap:8px}
  .topic-chip,.skill-chip{display:inline-flex;align-items:center;min-height:32px;border:1px solid #cfd6dd;border-radius:999px;padding:0 12px;color:#1d2226;background:#fff;font-size:13px;font-weight:700;text-decoration:none}
  .topic-chip:hover,.skill-chip:hover{background:#f3f2ef}
  .skill-cloud{display:flex;flex-wrap:wrap;gap:10px}

  .li-carousel-section{margin-bottom:14px}
  .li-carousel-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
  .li-carousel-title{font-family:'Source Serif 4',Georgia,serif;font-size:22px;font-weight:800;color:#111827}
  .li-carousel-nav{display:flex;align-items:center;gap:8px}
  .li-carousel-btn{width:36px;height:36px;border-radius:50%;border:1.5px solid #c0c0c0;background:white;display:flex;align-items:center;justify-content:center;cursor:pointer}
  .li-carousel-btn:hover{border-color:#1d2226;box-shadow:0 2px 8px rgba(0,0,0,.1)}
  .li-carousel-overflow{overflow:hidden;width:100%}
  .li-carousel-track{display:flex;gap:14px;overflow-x:scroll;scroll-behavior:smooth;scrollbar-width:none;-ms-overflow-style:none;padding-bottom:2px}
  .li-carousel-track::-webkit-scrollbar{display:none}
  .li-course-card{flex:0 0 218px;min-width:0;display:flex;flex-direction:column}
  .li-card-thumb-link{display:block;text-decoration:none}
  .li-card-thumb{position:relative;width:100%;aspect-ratio:16/9;border-radius:6px;overflow:hidden;background:#e5e7eb}
  .li-card-thumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .2s}
  .li-card-thumb-link:hover .li-card-thumb img{transform:scale(1.03)}
  .li-card-thumb-fallback{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;color:rgba(255,255,255,.72);letter-spacing:1px}
  .li-card-duration{position:absolute;bottom:7px;right:7px;background:rgba(0,0,0,.75);color:#fff;font-size:11px;font-weight:700;padding:2px 6px;border-radius:3px;pointer-events:none}
  .li-card-body{padding:10px 2px 0;display:flex;flex-direction:column;gap:3px}
  .li-card-type{font-size:12px;color:#56687a}
  .li-card-title-link{text-decoration:none}
  .li-card-title{font-size:14px;font-weight:800;color:#1d2226;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .li-card-title-link:hover .li-card-title{color:#0a66c2}
  .li-card-by{font-size:12px;color:#56687a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

  @media(max-width:980px){
    .home-hero{grid-template-columns:1fr}
    .quick-grid,.role-columns,.topic-grid{grid-template-columns:1fr}
  }
  @media(max-width:680px){
    .hero-panel,.resume-panel,.home-section{padding:18px}
    .hero-title{font-size:30px}
    .stats-row{grid-template-columns:1fr}
    .li-course-card{flex-basis:178px}
  }
</style>

<div class="home-page">
  <section class="home-hero">
    <div class="hero-panel">
      <div class="hero-content">
        <div class="eyebrow">LinkedIn Learning</div>
        <h1 class="hero-title">{{ auth()->user()->name }}, grow your skills and advance your career</h1>
        <p class="hero-copy">Explore {{ number_format($contentStats->sum('count')) }} courses from your local LinkedIn Learning database, organized into Business, Technology, and Creative learning paths.</p>
        <div class="hero-actions">
          <a class="primary-btn" href="{{ route('browse') }}">Explore courses</a>
          <a class="outline-btn" href="{{ route('journey.index') }}">Create my learning plan</a>
        </div>
      </div>
      <div class="stats-row">
        @foreach($contentStats as $stat)
          <a class="stat-card" href="{{ $stat['url'] }}">
            <span class="stat-number">{{ number_format($stat['count']) }}</span>
            <span class="stat-label">{{ $stat['label'] }} courses</span>
          </a>
        @endforeach
      </div>
    </div>

    <aside class="resume-panel">
      <h2 class="resume-title">Pick up where you left off</h2>
      @if($primaryProgress)
        @php($course = $primaryProgress->course)
        <a class="resume-card" href="{{ route('course.show', $course) }}">
          <div class="resume-thumb">
            @if($course->thumbnail)
              <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
              <div class="resume-fallback" style="display:none">{{ strtoupper(substr($course->title,0,2)) }}</div>
            @else
              <div class="resume-fallback">{{ strtoupper(substr($course->title,0,2)) }}</div>
            @endif
          </div>
          <div class="resume-course">{{ $course->title }}</div>
          <div class="resume-meta">By: {{ $course->instructor_name ?: 'LinkedIn Learning' }}</div>
          <div class="progress-wrap"><div class="progress-bar" style="width:{{ $primaryProgress->progress_percent }}%"></div></div>
          <div class="resume-meta">{{ $primaryProgress->progress_percent }}% complete</div>
        </a>
        <a class="primary-btn" href="{{ route('course.show', $course) }}">Continue learning</a>
      @else
        <div class="empty-resume">
          Start a course to see your progress here. Your saved and in-progress courses will stay close at hand.
        </div>
        <a class="primary-btn" href="{{ route('browse') }}">Find a course</a>
      @endif
    </aside>
  </section>

  <section class="home-section">
    <div class="section-head">
      <div>
        <h2 class="section-title">Explore by category</h2>
        <div class="section-sub">These categories use the Business, Technology, and Creative course groups from your imported database.</div>
      </div>
    </div>
    <div class="quick-grid">
      @foreach($contentStats as $stat)
        <a class="quick-card" href="{{ $stat['url'] }}">
          <div class="quick-title">{{ $stat['label'] }}</div>
          <div class="quick-desc">{{ number_format($stat['count']) }} available courses, with topics and learning paths matched to this category.</div>
          <div class="quick-link">Browse {{ $stat['label'] }}</div>
        </a>
      @endforeach
    </div>
  </section>

  <section class="home-section">
    <div class="section-head">
      <div>
        <h2 class="section-title">Role guides for your next move</h2>
        <div class="section-sub">Role guide pages now map to skills and pull matching courses from the database.</div>
      </div>
      <a class="see-all" href="{{ route('browse') }}">View all</a>
    </div>
    <div class="role-columns">
      @foreach($roleGuides as $label => $roles)
        <div class="role-column">
          <h3>{{ $label }}</h3>
          <div class="role-list">
            @foreach($roles as $role)
              <a class="role-link" href="{{ $role['url'] }}">{{ $role['title'] }}</a>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
  </section>

  @if($recommended->count() > 0)
    <section class="home-section li-carousel-section">
      <div class="li-carousel-header">
        <div>
          <div class="li-carousel-title">Recommended for you</div>
          <div class="section-sub">Based on your saved and in-progress learning when available.</div>
        </div>
        <div class="li-carousel-nav">
          <button class="li-carousel-btn" type="button" onclick="scrollCarousel('recommended-track',-1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
          <button class="li-carousel-btn" type="button" onclick="scrollCarousel('recommended-track',1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
      </div>
      <div class="li-carousel-overflow"><div class="li-carousel-track" id="recommended-track">
        @foreach($recommended as $course)@include("home._course_card", compact("course"))@endforeach
      </div></div>
    </section>
  @endif

  <section class="home-section li-carousel-section">
    <div class="li-carousel-header">
      <div class="li-carousel-title">Top picks for {{ auth()->user()->name }}</div>
      <div class="li-carousel-nav">
        <button class="li-carousel-btn" type="button" onclick="scrollCarousel('toppicks-track',-1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
        <button class="li-carousel-btn" type="button" onclick="scrollCarousel('toppicks-track',1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
      </div>
    </div>
    <div class="li-carousel-overflow"><div class="li-carousel-track" id="toppicks-track">
      @foreach($topPicks as $course)@include("home._course_card", compact("course"))@endforeach
    </div></div>
  </section>

  <section class="home-section">
    <div class="section-head">
      <div>
        <h2 class="section-title">Trending skills</h2>
        <div class="section-sub">Skills are ranked from course-skill relationships in the database.</div>
      </div>
    </div>
    <div class="skill-cloud">
      @foreach($trendingSkills as $skill)
        <a class="skill-chip" href="{{ route('browse', ['q' => $skill->name]) }}">{{ $skill->name }}</a>
      @endforeach
    </div>
  </section>

  <section class="home-section">
    <div class="section-head">
      <div>
        <h2 class="section-title">Popular topics</h2>
        <div class="section-sub">The strongest topics from each content area.</div>
      </div>
    </div>
    <div class="topic-grid">
      @foreach($featuredCategories as $label => $categories)
        <div class="topic-card">
          <h3>{{ $label }}</h3>
          <div class="topic-list">
            @foreach($categories as $category)
              <a class="topic-chip" href="{{ route('browse.category', ['category' => $category->slug ?: $category->id, 'type' => $typeCodes[$label] ?? 'B']) }}">
                {{ $category->name }}
              </a>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
  </section>

  <section class="home-section li-carousel-section">
    <div class="li-carousel-header">
      <div>
        <div class="li-carousel-title">New and recently released</div>
        <div class="section-sub">Sorted from the latest release dates in your imported course data.</div>
      </div>
      <div class="li-carousel-nav">
        <button class="li-carousel-btn" type="button" onclick="scrollCarousel('recent-track',-1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
        <button class="li-carousel-btn" type="button" onclick="scrollCarousel('recent-track',1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
      </div>
    </div>
    <div class="li-carousel-overflow"><div class="li-carousel-track" id="recent-track">
      @foreach(($recentCourses->count() ? $recentCourses : $shortCourses) as $course)@include("home._course_card", compact("course"))@endforeach
    </div></div>
  </section>

  <section class="home-section li-carousel-section">
    <div class="li-carousel-header">
      <div class="li-carousel-title">30 minutes or less</div>
      <div class="li-carousel-nav">
        <button class="li-carousel-btn" type="button" onclick="scrollCarousel('short-track',-1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
        <button class="li-carousel-btn" type="button" onclick="scrollCarousel('short-track',1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
      </div>
    </div>
    <div class="li-carousel-overflow"><div class="li-carousel-track" id="short-track">
      @foreach($shortCourses as $course)@include("home._course_card", compact("course"))@endforeach
    </div></div>
  </section>

  @if($saved->count() > 0)
    <section class="home-section li-carousel-section">
      <div class="li-carousel-header">
        <div class="li-carousel-title">Saved courses</div>
        <div class="li-carousel-nav">
          <button class="li-carousel-btn" type="button" onclick="scrollCarousel('saved-track',-1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
          <button class="li-carousel-btn" type="button" onclick="scrollCarousel('saved-track',1)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
      </div>
      <div class="li-carousel-overflow"><div class="li-carousel-track" id="saved-track">
        @foreach($saved as $enrollment)@include("home._course_card", ["course" => $enrollment->course])@endforeach
      </div></div>
    </section>
  @endif
</div>

@push('scripts')
<script>
function scrollCarousel(trackId, dir) {
  const track = document.getElementById(trackId);
  if (!track) return;
  const card = track.querySelector('.li-course-card');
  const step = ((card?.offsetWidth ?? 218) + 14) * 3;
  track.scrollBy({ left: dir * step, behavior: 'smooth' });
}
</script>
@endpush
@endsection
