@extends("layouts.app")
@section("title",($typeLabel ?? 'Business')." | LinkedIn Learning")
@section("content")
@php
    $selectedType = $selectedType ?? request('type', 'B');
    $typeLabel = $typeLabel ?? ['B' => 'Business', 'T' => 'Technology', 'C' => 'Creative'][$selectedType] ?? 'Business';
    $typeTabs = ['B' => 'Business', 'T' => 'Technology', 'C' => 'Creative'];
    $typeUrl = fn ($type) => route('browse', ['type' => $type]);
    $searchUrl = function ($term) use ($selectedType) {
        return route('browse', ['type' => $selectedType, 'q' => $term]);
    };
    $categoryLink = function ($cat) use ($selectedType) {
        $key = is_object($cat) ? ($cat->slug ?: $cat->id) : (string) $cat;
        return route('browse.category', ['category' => $key, 'type' => $selectedType]);
    };

    $pathCourses = $courses->take(4)->values();
    $featuredCategories = $categories->take(3)->values();
    $topicGroups = $topicGroups ?? collect();
    $topicChips = $topicChips ?? collect();
    $linkedinTopics = collect($linkedinTopics ?? []);
    $roleGuides = collect($roleGuides ?? []);
    $extraRoleGuides = collect($extraRoleGuides ?? []);
    $software = ['Microsoft Excel', 'Power BI', 'Microsoft Copilot', 'ChatGPT', 'SAP ERP', 'LinkedIn', 'Salesforce', 'PowerPoint', 'SharePoint', 'Outlook', 'Microsoft Project', 'Microsoft Teams', 'Microsoft Word', 'Microsoft 365', 'Google Analytics', 'Google Workspace', 'Dynamics', 'Microsoft Access'];
    $showResults = request('q') || request('level') || request('durasi') || request('skill') || isset($category);
    $selectedSort = request('sort', 'popular');
    $sortUrl = fn ($sort) => request()->fullUrlWithQuery(['sort' => $sort, 'page' => null]);
@endphp

<style>
  .content-page {
    --li-blue: #0a66c2;
    --li-dark: #1d2226;
    --li-bg: #f3f2ef;
    --li-white: #ffffff;
    --li-border: #dce6f1;
    --li-muted: #56687a;
    --li-hover: #004182;
    --li-gold: #c7a84e;
    --radius: 8px;
    max-width: 1128px;
    margin: 0 auto;
    color: var(--li-dark);
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-size: 14px;
  }

  .content-sub-nav {
    background: var(--li-white);
    border: 1px solid #e0e0e0;
    border-radius: var(--radius);
    min-height: 48px;
    display: flex;
    align-items: flex-end;
    padding: 0 18px;
    gap: 0;
    margin-bottom: 12px;
  }

  .content-sub-link {
    padding: 0 20px 12px;
    font-size: 14px;
    font-weight: 500;
    color: var(--li-muted);
    cursor: pointer;
    white-space: nowrap;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    transition: color .15s;
  }

  .content-sub-link:hover { color: var(--li-dark); }
  .content-sub-link.active { color: var(--li-dark); border-bottom-color: var(--li-dark); font-weight: 700; }

  .content-section {
    background: var(--li-white);
    border-radius: var(--radius);
    border: 1px solid #e0e0e0;
    padding: 24px;
    margin-bottom: 12px;
  }

  .content-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
  }

  .content-title {
    font-family: 'Source Serif 4', Georgia, serif;
    font-size: 20px;
    line-height: 1.2;
    font-weight: 700;
    color: var(--li-dark);
  }

  .content-sub {
    font-size: 13px;
    color: var(--li-muted);
    margin-top: 4px;
    line-height: 1.45;
  }

  .see-all {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--li-blue);
    text-decoration: none;
    white-space: nowrap;
  }

  .see-all:hover { text-decoration: underline; }

  .cat-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
  }

  .cat-card {
    border: 1px solid #e0e0e0;
    border-radius: var(--radius);
    padding: 20px 16px 20px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    cursor: pointer;
    transition: box-shadow .2s, transform .2s;
    background: white;
    color: inherit;
    text-decoration: none;
    min-height: 142px;
  }

  .cat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); transform: translateY(-1px); }
  .cat-card-info { flex: 1; min-width: 0; }
  .cat-card-title { font-size: 15px; font-weight: 700; color: var(--li-dark); line-height: 1.3; margin-bottom: 12px; }

  .btn-explore {
    display: inline-block;
    border: 1.5px solid #666;
    border-radius: 20px;
    padding: 5px 16px;
    font-size: 13px;
    font-weight: 700;
    color: var(--li-dark);
    background: transparent;
    transition: all .15s;
  }

  .cat-card:hover .btn-explore { border-color: var(--li-blue); color: var(--li-blue); }
  .cat-card-img { width: 96px; height: 96px; border-radius: 50%; overflow: hidden; flex-shrink: 0; background: #ddd; }

  .role-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
  }

  .role-card {
    background: #eef2f7;
    border-radius: 6px;
    padding: 14px 18px;
    font-size: 14px;
    font-weight: 700;
    color: var(--li-dark);
    cursor: pointer;
    transition: background .15s;
    text-decoration: none;
  }

  .role-card:hover { background: #dce6f1; }

  .show-more {
    text-align: center;
    margin-top: 18px;
    font-size: 14px;
    font-weight: 700;
    color: var(--li-dark);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }

  .paths-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 16px;
  }

  .paths-nav { display: flex; align-items: center; gap: 8px; }

  .path-nav-btn {
    width: 32px;
    height: 32px;
    border: 1.5px solid #bbb;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: border-color .15s;
  }

  .path-nav-btn:hover { border-color: var(--li-dark); }
  .path-nav-btn.disabled { opacity: 0.4; cursor: default; }

  .paths-scroll {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
  }

  .path-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: var(--radius);
    overflow: hidden;
    cursor: pointer;
    transition: box-shadow .2s, transform .2s;
    color: inherit;
    text-decoration: none;
  }

  .path-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.12); transform: translateY(-2px); }
  .path-thumb { position: relative; aspect-ratio: 16/9; overflow: hidden; background: #d4e8d4; }

  .path-thumb-bg {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .pt1 { background: linear-gradient(135deg, #e8f4ea 0%, #c5dfc6 100%); }
  .pt2 { background: linear-gradient(135deg, #b8e0e8 0%, #4db6c8 100%); }
  .pt3 { background: linear-gradient(135deg, #e8f0e8 0%, #c5d5c5 100%); }
  .pt4 { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); }

  .path-thumb-badge-star {
    position: absolute;
    bottom: 8px;
    left: 8px;
    background: var(--li-gold);
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .path-duration {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0,0,0,.72);
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
  }

  .path-body { padding: 12px; }
  .path-label { font-size: 11px; color: var(--li-muted); margin-bottom: 4px; }
  .path-title { font-size: 13.5px; font-weight: 800; color: var(--li-dark); line-height: 1.35; }
  .path-meta { color: var(--li-muted); font-size: 12px; margin-top: 8px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

  .topics-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 32px 48px;
  }

  .topic-col-title { font-size: 15px; font-weight: 800; color: var(--li-dark); margin-bottom: 10px; }
  .topic-col-list { list-style: none; margin: 0; padding: 0; }
  .topic-col-list li { margin-bottom: 6px; }

  .topic-col-list a,
  .topic-show-all {
    font-size: 13.5px;
    color: var(--li-muted);
    text-decoration: none;
    cursor: pointer;
    line-height: 1.5;
  }

  .topic-col-list a:hover { color: var(--li-blue); text-decoration: underline; }
  .topic-show-all { display: block; margin-top: 8px; font-weight: 700; color: var(--li-blue); }
  .topic-show-all:hover { text-decoration: underline; }
  .topic-link-card {
    display: block;
    min-height: 86px;
    padding: 16px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #fff;
    color: var(--li-dark);
    text-decoration: none;
  }
  .topic-link-card:hover { background: #f8fafc; border-color: #cfd6dd; }
  .topic-source {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    color: var(--li-muted);
    font-size: 13px;
    line-height: 1.4;
  }
  .topic-source span {
    display: inline-flex;
    align-items: center;
    min-height: 22px;
    padding: 0 8px;
    border-radius: 999px;
    background: #eef3f8;
    color: #0a66c2;
    font-size: 12px;
    font-weight: 800;
  }

  .chips-section {
    background: var(--li-white);
    border-top: 4px solid var(--li-bg);
    padding: 24px;
    margin-bottom: 12px;
    border-radius: var(--radius);
    border: 1px solid #e0e0e0;
  }

  .chips-title { font-size: 18px; font-weight: 800; color: var(--li-dark); margin-bottom: 16px; }
  .chips-wrap { display: flex; flex-wrap: wrap; gap: 10px; }

  .sw-chip {
    padding: 7px 18px;
    border: 1.5px solid #c0c0c0;
    border-radius: 20px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--li-dark);
    background: white;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    text-decoration: none;
  }

  .sw-chip:hover,
  .sw-chip.selected {
    border-color: var(--li-blue);
    color: var(--li-blue);
    background: #e8f3ff;
  }

  .course-result-note {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    color: var(--li-muted);
    font-size: 13px;
  }

  .crumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--li-muted);
    margin-bottom: 8px;
  }

  .crumbs a { color: var(--li-muted); text-decoration: none; }
  .crumbs a:hover { color: var(--li-blue); text-decoration: underline; }
  .hero-title { font-family: 'Source Serif 4', Georgia, serif; font-size: 32px; line-height: 1.15; font-weight: 700; color: var(--li-dark); }
  .hero-sub { margin-top: 8px; color: var(--li-muted); line-height: 1.55; max-width: 78ch; }

  .filter-bar { margin-top: 14px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .filter-pill { border: 1.5px solid #c0c0c0; border-radius: 999px; padding: 6px 12px; background: white; display: flex; align-items: center; gap: 8px; }
  .filter-pill select { border: 0; background: transparent; font-size: 13px; font-weight: 700; color: var(--li-dark); outline: none; }

  .results-wrap { display: flex; flex-direction: column; gap: 12px; }
  .results-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
  .results-count { font-size: 14px; font-weight: 700; color: var(--li-dark); }
  .sort-pill { border: 1.5px solid #c0c0c0; border-radius: 999px; padding: 6px 12px; background: white; display: inline-flex; align-items: center; gap: 8px; }
  .sort-pill select { border: 0; background: transparent; font-size: 13px; font-weight: 700; color: var(--li-dark); outline: none; }

  .result-card {
    display: flex;
    gap: 16px;
    padding: 14px;
    border: 1px solid #e0e0e0;
    border-radius: var(--radius);
    background: white;
    text-decoration: none;
    color: inherit;
    transition: box-shadow .15s, transform .15s;
  }
  .result-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); transform: translateY(-1px); }
  .result-thumb { width: 168px; height: 94px; border-radius: 6px; overflow: hidden; background: #eef2f7; flex-shrink: 0; }
  .result-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .result-thumb-fallback { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #6b7280; }
  .result-body { min-width: 0; flex: 1; }
  .result-title { font-size: 16px; font-weight: 800; line-height: 1.25; margin-bottom: 6px; }
  .result-meta { color: var(--li-muted); font-size: 13px; display: flex; gap: 10px; flex-wrap: wrap; }
  .pager { margin-top: 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .pager a {
    padding: 8px 14px;
    border: 1.5px solid #c0c0c0;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    color: var(--li-dark);
    background: white;
  }
  .pager a.disabled { opacity: .45; pointer-events: none; }
  .pager .page-info { font-size: 13px; color: var(--li-muted); }

  .content-foot {
    border-top: 1px solid var(--li-border);
    margin-top: 48px;
    padding: 24px 0 0;
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: center;
  }

  .content-foot a {
    font-size: 12px;
    color: var(--li-muted);
    text-decoration: none;
  }

  .content-foot a:hover { color: var(--li-blue); text-decoration: underline; }
  .foot-copy { font-size: 12px; color: var(--li-muted); margin-left: auto; }

  @media (max-width: 980px) {
    .cat-cards-grid,
    .role-grid,
    .topics-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }

    .paths-scroll { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }

  @media (max-width: 640px) {
    .content-section,
    .chips-section { padding: 18px; }

    .content-sub-nav { overflow-x: auto; }

    .cat-cards-grid,
    .role-grid,
    .paths-scroll,
    .topics-grid { grid-template-columns: 1fr; }

    .cat-card { min-height: auto; }
    .content-head,
    .paths-head { align-items: flex-start; flex-direction: column; }
    .foot-copy { margin-left: 0; }
  }
</style>

<div class="content-page">
  <nav class="content-sub-nav" aria-label="Content categories">
    @foreach($typeTabs as $typeCode => $label)
      <a class="content-sub-link {{ $selectedType === $typeCode ? 'active' : '' }}" href="{{ $typeUrl($typeCode) }}">{{ $label }}</a>
    @endforeach
  </nav>

  @if(isset($category))
    <section class="content-section">
      <div class="crumbs">
        <span>Browse</span>
        <span>/</span>
        <a href="{{ $typeUrl($selectedType) }}">{{ $typeLabel }}</a>
      </div>
      <div class="hero-title">{{ $category->name }}</div>
      <div class="hero-sub">
        Jelajahi course {{ $typeLabel }} untuk topik {{ $category->name }}.
      </div>

      <div class="filter-bar" aria-label="Filters">
        <div class="filter-pill">
          <select onchange="location=this.value" aria-label="Duration">
            <option value="{{ request()->fullUrlWithQuery(['durasi' => null, 'page' => null]) }}" {{ request('durasi') ? '' : 'selected' }}>Time to complete</option>
            <option value="{{ request()->fullUrlWithQuery(['durasi' => 'short', 'page' => null]) }}" {{ request('durasi') === 'short' ? 'selected' : '' }}>Under 30 min</option>
            <option value="{{ request()->fullUrlWithQuery(['durasi' => 'medium', 'page' => null]) }}" {{ request('durasi') === 'medium' ? 'selected' : '' }}>30 min – 2 hr</option>
            <option value="{{ request()->fullUrlWithQuery(['durasi' => 'long', 'page' => null]) }}" {{ request('durasi') === 'long' ? 'selected' : '' }}>Over 2 hr</option>
          </select>
        </div>
        <div class="filter-pill">
          <select onchange="location=this.value" aria-label="Level">
            <option value="{{ request()->fullUrlWithQuery(['level' => null, 'page' => null]) }}" {{ request('level') ? '' : 'selected' }}>Level</option>
            <option value="{{ request()->fullUrlWithQuery(['level' => 'Beginner', 'page' => null]) }}" {{ request('level') === 'Beginner' ? 'selected' : '' }}>Beginner</option>
            <option value="{{ request()->fullUrlWithQuery(['level' => 'Intermediate', 'page' => null]) }}" {{ request('level') === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
            <option value="{{ request()->fullUrlWithQuery(['level' => 'Advanced', 'page' => null]) }}" {{ request('level') === 'Advanced' ? 'selected' : '' }}>Advanced</option>
          </select>
        </div>
        <a class="see-all" href="{{ $categoryLink($category) }}">Reset</a>
      </div>
    </section>

    <section class="content-section">
      <div class="paths-head">
        <div class="content-title">Explore {{ $category->name }} topics</div>
        <a class="see-all" href="{{ $typeUrl($selectedType) }}">Show all</a>
      </div>
      <div class="chips-wrap">
        @if($topicChips->count())
          @foreach($topicChips as $chip)
            <a class="sw-chip {{ request('skill') === $chip->slug ? 'selected' : '' }}" href="{{ request()->fullUrlWithQuery(['skill' => $chip->slug, 'q' => null, 'page' => null]) }}">{{ $chip->name }}</a>
          @endforeach
        @else
          @foreach($categories->take(18) as $cat)
            <a class="sw-chip {{ $cat->id === $category->id ? 'selected' : '' }}" href="{{ $categoryLink($cat) }}">{{ $cat->name }}</a>
          @endforeach
        @endif
      </div>
    </section>
  @endif

  @if(!$showResults)
  <section class="content-section">
    <div class="content-head">
      <div>
        <div class="content-title">{{ $typeLabel }}</div>
        <div class="content-sub">Kategori dan learning path di bawah ini diambil dari course dengan ID Excel berakhiran {{ $selectedType }}.</div>
      </div>
      <a class="see-all" href="{{ $typeUrl($selectedType) }}">Show all</a>
    </div>

    <div class="cat-cards-grid">
      @forelse($featuredCategories as $index => $cat)
        <a class="cat-card" href="{{ $categoryLink($cat) }}">
          <div class="cat-card-info">
            <div class="cat-card-title">{{ $cat->name }}</div>
            <span class="btn-explore">Explore</span>
          </div>
          <div class="cat-card-img">
            <svg viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" width="96" height="96">
              <circle cx="48" cy="48" r="48" fill="{{ ['#e8d5c4', '#dce8f0', '#e8e8e8'][$index % 3] }}"/>
              <rect x="18" y="58" width="60" height="20" rx="5" fill="#f5f0e8" opacity=".85"/>
              <circle cx="38" cy="38" r="11" fill="{{ ['#d4a373', '#D97706', '#8B5E3C'][$index % 3] }}"/>
              <rect x="27" y="49" width="24" height="25" rx="7" fill="{{ ['#6b8f71', '#c25a00', '#334155'][$index % 3] }}"/>
              <rect x="50" y="34" width="26" height="25" rx="4" fill="#ffffff" opacity=".75"/>
              <line x1="55" y1="42" x2="70" y2="42" stroke="#9aa" stroke-width="2"/>
              <line x1="55" y1="48" x2="66" y2="48" stroke="#9aa" stroke-width="2"/>
            </svg>
          </div>
        </a>
      @empty
        <div class="course-result-note">Belum ada kategori untuk {{ $typeLabel }}.</div>
      @endforelse
    </div>
  </section>
  @endif

  @if(!$showResults)
  <section class="content-section">
    <div class="content-head" style="flex-direction:column;align-items:flex-start;gap:4px;">
      <div class="content-title">Role Guides</div>
      <div class="content-sub">Explore foundational content and tools to help you understand, learn, and improve at the skills involved in trending industry roles.</div>
    </div>
    <div class="role-grid">
      @foreach($roleGuides as $role)
        <a class="role-card" href="{{ route('roles.show', $role['slug']) }}">{{ $role['title'] }}</a>
      @endforeach
    </div>
    @if($extraRoleGuides->isNotEmpty())
      <div class="show-more" id="roleShowMore">
        Show more
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </div>
    @endif
  </section>
  @endif

  <section class="content-section">
    <div class="paths-head">
      <div>
        <div class="content-title">{{ $showResults ? 'Results' : 'Learning Paths' }}</div>
        @if($showResults)
          <div class="content-sub">
            Menampilkan {{ number_format($courses->total()) }} course
            @if(isset($category)) dalam {{ $category->name }} @endif
          </div>
        @endif
      </div>
      <div style="display:flex;align-items:center;gap:16px;">
        <a class="see-all" href="{{ $typeUrl($selectedType) }}">Show all</a>
        <div class="paths-nav">
          <div class="path-nav-btn disabled" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
          </div>
          <div class="path-nav-btn" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
          </div>
        </div>
      </div>
    </div>

    @if($showResults)
      @if($courses->count())
        <div class="results-head">
          <div class="results-count">{{ number_format($courses->total()) }} Results for "{{ isset($category) ? $category->name : (request('q') ?: $typeLabel) }}"</div>
          <div class="sort-pill">
            <select onchange="location=this.value" aria-label="Sort">
              <option value="{{ $sortUrl('popular') }}" {{ $selectedSort === 'popular' ? 'selected' : '' }}>Best Match</option>
              <option value="{{ $sortUrl('recent') }}" {{ $selectedSort === 'recent' ? 'selected' : '' }}>Most Recent</option>
              <option value="{{ $sortUrl('rating') }}" {{ $selectedSort === 'rating' ? 'selected' : '' }}>Top Rated</option>
            </select>
          </div>
        </div>

        <div class="results-wrap">
          @foreach($courses as $course)
            <a class="result-card" href="{{ route('course.show', $course) }}">
              <div class="result-thumb">
                @if($course->thumbnail)
                  <img src="{{ $course->thumbnail }}" alt="" onerror="this.style.display='none'">
                @else
                  <div class="result-thumb-fallback">{{ strtoupper(substr($course->title, 0, 1)) }}</div>
                @endif
              </div>
              <div class="result-body">
                <div class="result-title">{{ $course->title }}</div>
                <div class="result-meta">
                  <span>{{ $course->durasi_format }}</span>
                  <span>{{ $course->level ?: ($course->scraped_level ?: 'Beginner') }}</span>
                  <span>{{ $course->category->name ?? $typeLabel }}</span>
                  <span>{{ number_format($course->display_learner_count) }} learners</span>
                </div>
              </div>
            </a>
          @endforeach
        </div>

        <div class="pager" aria-label="Pagination">
          <a class="{{ $courses->onFirstPage() ? 'disabled' : '' }}" href="{{ $courses->previousPageUrl() ?: '#' }}">Previous</a>
          <div class="page-info">Page {{ $courses->currentPage() }} / {{ $courses->lastPage() }}</div>
          <a class="{{ $courses->hasMorePages() ? '' : 'disabled' }}" href="{{ $courses->nextPageUrl() ?: '#' }}">Next</a>
        </div>
      @else
        <div class="course-result-note">
          <span>Tidak ada course ditemukan. Coba ubah kata kunci pencarian.</span>
          <a class="see-all" href="{{ route('browse') }}">Reset</a>
        </div>
      @endif
    @else
      @if($pathCourses->count())
        <div class="paths-scroll">
          @foreach($pathCourses as $index => $course)
            <a class="path-card" href="{{ route('course.show', $course) }}">
              <div class="path-thumb">
                <div class="path-thumb-bg pt{{ ($index % 4) + 1 }}">
                  <svg viewBox="0 0 240 135" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <rect width="240" height="135" fill="{{ ['#d4e8d4', '#b8d8e0', '#d8e8d8', '#1a1a2e'][$index % 4] }}"/>
                    <rect x="18" y="78" width="204" height="42" rx="6" fill="{{ $index % 4 === 3 ? '#0a66c2' : '#ffffff' }}" opacity=".42"/>
                    <circle cx="86" cy="58" r="18" fill="{{ $index % 2 === 0 ? '#d4a373' : '#5bb5c5' }}"/>
                    <rect x="64" y="72" width="44" height="40" rx="12" fill="{{ $index % 4 === 3 ? '#c7a84e' : '#5a8a4a' }}"/>
                    <rect x="108" y="80" width="64" height="40" rx="4" fill="#334155"/>
                    <rect x="112" y="84" width="56" height="32" rx="2" fill="#1e3a5f"/>
                    <rect x="118" y="91" width="32" height="4" rx="2" fill="#fff" opacity=".55"/>
                    <rect x="118" y="101" width="42" height="4" rx="2" fill="#fff" opacity=".35"/>
                    <polygon points="30,20 55,8 80,20 80,50 55,62 30,50" fill="none" stroke="#c7a84e" stroke-width="3"/>
                  </svg>
                </div>
                @if($index === 0 || $index === 3)
                  <div class="path-thumb-badge-star">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="white"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                  </div>
                @endif
                <div class="path-duration">{{ $course->durasi_format }}</div>
              </div>
              <div class="path-body">
                <div class="path-label">{{ $course->category->name ?? 'Learning Path' }}</div>
                <div class="path-title">{{ $course->title }}</div>
                <div class="path-meta">
                  <span>{{ number_format($course->display_rating, 1) }} rating</span>
                  <span>{{ number_format($course->display_learner_count) }} learners</span>
                </div>
              </div>
            </a>
          @endforeach
        </div>
      @else
        <div class="course-result-note">
          <span>Tidak ada course ditemukan. Coba ubah kata kunci pencarian.</span>
          <a class="see-all" href="{{ route('browse') }}">Reset</a>
        </div>
      @endif
    @endif
  </section>

  @if(!$showResults)
  <section class="content-section">
    <div class="paths-head">
      <div class="content-title">{{ $typeLabel }} Topics</div>
      <a class="see-all" href="{{ $typeUrl($selectedType) }}">Show all</a>
    </div>
    <div class="content-sub" style="margin-bottom:16px;">Daftar topic mengikuti halaman LinkedIn Learning {{ $typeLabel }} dan diarahkan ke kategori atau pencarian course di database lokal.</div>
    <div class="topics-grid">
      @forelse($linkedinTopics as $topic)
        <a class="topic-link-card" href="{{ $topic['url'] }}">
          <div class="topic-col-title">{{ $topic['title'] }}</div>
          <div class="topic-source">
            {{ number_format($topic['count']) }} course match
            @if($topic['source'] === 'category')
              <span>Category</span>
            @else
              <span>Search</span>
            @endif
          </div>
        </a>
      @empty
        <div class="course-result-note">Belum ada topic untuk {{ $typeLabel }}.</div>
      @endforelse
    </div>
  </section>
  @endif

  <section class="chips-section">
    <div class="chips-title">Software</div>
    <div class="chips-wrap">
      @foreach($software as $chip)
        <a class="sw-chip {{ request('q') === $chip ? 'selected' : '' }}" href="{{ $searchUrl($chip) }}">{{ $chip }}</a>
      @endforeach
    </div>
  </section>

  <footer class="content-foot">
    <a href="#">About</a>
    <a href="#">Accessibility</a>
    <a href="#">Help Center</a>
    <a href="#">Privacy & Terms</a>
    <a href="#">Ad Choices</a>
    <a href="#">Advertising</a>
    <a href="#">Business Services</a>
    <span class="foot-copy">LinkedIn © 2025</span>
  </footer>
</div>

@push('scripts')
<script>
  document.querySelectorAll('.content-sub-link').forEach((item) => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.content-sub-link').forEach((entry) => entry.classList.remove('active'));
      item.classList.add('active');
    });
  });

  const showMore = document.getElementById('roleShowMore');
  showMore?.addEventListener('click', () => {
    const extra = @json($extraRoleGuides->values());
    const grid = document.querySelector('.role-grid');

    extra.forEach((role) => {
      const a = document.createElement('a');
      a.className = 'role-card';
      a.href = `/roles/${role.slug}`;
      a.textContent = role.title;
      grid.appendChild(a);
    });

    showMore.style.display = 'none';
  });
</script>
@endpush
@endsection
