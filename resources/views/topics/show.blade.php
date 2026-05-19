@extends('layouts.app')
@section('title', $meta['title'].' | LinkedIn Learning')
@section('content')
@php
  $selectedSort = request('sort', 'popular');
  $typeUrl = fn ($type) => route('browse', ['type' => $type]);
  $topicUrl = fn ($slug) => route('topics.show', $slug);
  $subtopicUrl = fn ($term) => request()->fullUrlWithQuery(['q' => $term, 'page' => null]);
  $sortUrl = fn ($sort) => request()->fullUrlWithQuery(['sort' => $sort, 'page' => null]);
  $ratingFor = fn ($course) => $course->display_rating ?: (float) $course->rating;
@endphp

<style>
  .topic-page {
    --li-blue: #0a66c2;
    --li-dark: #1d2226;
    --li-muted: #56687a;
    --li-bg: #f3f2ef;
    --li-border: #d6dce2;
    --li-soft: #eef3f8;
    --radius: 8px;
    max-width: 1128px;
    margin: 0 auto;
    color: var(--li-dark);
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }

  .topic-sub-nav {
    display: flex;
    align-items: flex-end;
    gap: 0;
    min-height: 48px;
    padding: 0 18px;
    margin-bottom: 12px;
    overflow-x: auto;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: var(--radius);
  }

  .topic-sub-link {
    padding: 0 20px 12px;
    color: var(--li-muted);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    white-space: nowrap;
    font-size: 14px;
    font-weight: 600;
  }

  .topic-sub-link.active {
    color: var(--li-dark);
    border-bottom-color: var(--li-dark);
    font-weight: 800;
  }

  .topic-section {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: var(--radius);
    padding: 24px;
    margin-bottom: 12px;
  }

  .topic-crumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--li-muted);
    font-size: 13px;
    margin-bottom: 14px;
  }

  .topic-crumbs a {
    color: var(--li-muted);
    text-decoration: none;
  }

  .topic-crumbs a:hover {
    color: var(--li-blue);
    text-decoration: underline;
  }

  .topic-hero-title {
    font-family: 'Source Serif 4', Georgia, serif;
    font-size: 34px;
    line-height: 1.14;
    font-weight: 700;
    margin: 0 0 10px;
  }

  .topic-hero-copy {
    max-width: 82ch;
    color: var(--li-muted);
    font-size: 15px;
    line-height: 1.6;
    margin: 0;
  }

  .topic-heading-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 16px;
  }

  .topic-title {
    font-family: 'Source Serif 4', Georgia, serif;
    font-size: 22px;
    line-height: 1.2;
    font-weight: 700;
  }

  .topic-muted {
    color: var(--li-muted);
    font-size: 13px;
    line-height: 1.45;
    margin-top: 4px;
  }

  .topic-chip-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .topic-chip {
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 6px 16px;
    border: 1.5px solid #b8c2cc;
    border-radius: 999px;
    color: var(--li-dark);
    background: #fff;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 800;
  }

  .topic-chip:hover,
  .topic-chip.active {
    color: var(--li-blue);
    border-color: var(--li-blue);
    background: #eef7ff;
  }

  .topic-feature-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
  }

  .topic-course-card {
    min-width: 0;
    overflow: hidden;
    color: inherit;
    text-decoration: none;
    border: 1px solid #e0e0e0;
    border-radius: var(--radius);
    background: #fff;
    transition: box-shadow .15s, transform .15s;
  }

  .topic-course-card:hover,
  .topic-result-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,.1);
    transform: translateY(-1px);
  }

  .topic-thumb {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
    background: #eef2f7;
  }

  .topic-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .topic-thumb-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: linear-gradient(135deg, #0a66c2 0%, #1d4ed8 100%);
    font-weight: 900;
    font-size: 24px;
  }

  .topic-duration {
    position: absolute;
    right: 8px;
    bottom: 8px;
    padding: 2px 7px;
    border-radius: 4px;
    background: rgba(0,0,0,.74);
    color: #fff;
    font-size: 11px;
    font-weight: 800;
  }

  .topic-card-body {
    padding: 12px;
  }

  .topic-card-kicker {
    color: var(--li-muted);
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 5px;
  }

  .topic-card-title {
    color: var(--li-dark);
    font-size: 14px;
    font-weight: 900;
    line-height: 1.35;
  }

  .topic-card-meta {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 8px;
    color: var(--li-muted);
    font-size: 12px;
  }

  .topic-filter-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .topic-filter-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .topic-select {
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 0 12px;
    border: 1.5px solid #b8c2cc;
    border-radius: 999px;
    background: #fff;
  }

  .topic-select select {
    border: 0;
    outline: 0;
    background: transparent;
    color: var(--li-dark);
    font-size: 13px;
    font-weight: 800;
  }

  .topic-result-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .topic-result-card {
    display: flex;
    gap: 16px;
    padding: 14px;
    color: inherit;
    text-decoration: none;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: var(--radius);
    transition: box-shadow .15s, transform .15s;
  }

  .topic-result-thumb {
    width: 176px;
    height: 99px;
    flex-shrink: 0;
    border-radius: 6px;
    overflow: hidden;
    background: #eef2f7;
  }

  .topic-result-body {
    min-width: 0;
    flex: 1;
  }

  .topic-result-title {
    color: var(--li-dark);
    font-size: 16px;
    line-height: 1.28;
    font-weight: 900;
    margin-bottom: 6px;
  }

  .topic-result-desc {
    color: var(--li-muted);
    line-height: 1.45;
    font-size: 13px;
    margin-bottom: 8px;
  }

  .topic-result-meta {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    color: var(--li-muted);
    font-size: 13px;
  }

  .topic-empty {
    border: 1px dashed #b8c2cc;
    border-radius: var(--radius);
    padding: 22px;
    color: var(--li-muted);
    background: #fafafa;
  }

  .topic-pager {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 18px;
  }

  .topic-pager a,
  .topic-reset-link,
  .topic-see-all {
    color: var(--li-blue);
    font-size: 13px;
    font-weight: 900;
    text-decoration: none;
  }

  .topic-pager a {
    color: var(--li-dark);
    padding: 8px 14px;
    border: 1.5px solid #b8c2cc;
    border-radius: 999px;
    background: #fff;
  }

  .topic-pager a.disabled {
    opacity: .45;
    pointer-events: none;
  }

  .topic-page-info {
    color: var(--li-muted);
    font-size: 13px;
  }

  @media (max-width: 980px) {
    .topic-feature-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 640px) {
    .topic-section {
      padding: 18px;
    }

    .topic-hero-title {
      font-size: 28px;
    }

    .topic-feature-grid {
      grid-template-columns: 1fr;
    }

    .topic-result-card {
      flex-direction: column;
    }

    .topic-result-thumb {
      width: 100%;
      height: auto;
      aspect-ratio: 16/9;
    }
  }
</style>

<div class="topic-page">
  <nav class="topic-sub-nav" aria-label="Content categories">
    @foreach($typeTabs as $typeCode => $label)
      <a class="topic-sub-link {{ $meta['type'] === $typeCode ? 'active' : '' }}" href="{{ $typeUrl($typeCode) }}">{{ $label }}</a>
    @endforeach
  </nav>

  <section class="topic-section">
    <div class="topic-crumbs">
      <a href="{{ route('browse') }}">Browse</a>
      <span>/</span>
      <a href="{{ $typeUrl($meta['type']) }}">{{ $meta['type_label'] }}</a>
    </div>
    <h1 class="topic-hero-title">{{ $meta['title'] }}</h1>
    <p class="topic-hero-copy">{{ $meta['summary'] }}</p>
  </section>

  <section class="topic-section">
    <div class="topic-heading-row">
      <div>
        <div class="topic-title">Explore {{ $meta['title'] }} topics</div>
        <div class="topic-muted">Pilih subtopic untuk memfilter course lokal yang skill atau deskripsinya paling dekat.</div>
      </div>
      @if(request('q'))
        <a class="topic-reset-link" href="{{ $topicUrl($meta['slug']) }}">Reset</a>
      @endif
    </div>
    <div class="topic-chip-wrap">
      @foreach($subtopics as $subtopic)
        <a class="topic-chip {{ request('q') === $subtopic ? 'active' : '' }}" href="{{ $subtopicUrl($subtopic) }}">{{ $subtopic }}</a>
      @endforeach
    </div>
  </section>

  @if($featuredCourses->count())
    <section class="topic-section">
      <div class="topic-heading-row">
        <div>
          <div class="topic-title">Featured courses</div>
          <div class="topic-muted">Course paling relevan berdasarkan topic, skill, kategori, dan popularitas di database.</div>
        </div>
        <a class="topic-see-all" href="#all-courses">Show all</a>
      </div>
      <div class="topic-feature-grid">
        @foreach($featuredCourses as $course)
          <a class="topic-course-card" href="{{ route('course.show', $course) }}">
            <div class="topic-thumb">
              @if($course->thumbnail)
                <img src="{{ $course->thumbnail }}" alt="" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="topic-thumb-fallback" style="display:none">{{ strtoupper(substr($course->title, 0, 2)) }}</div>
              @else
                <div class="topic-thumb-fallback">{{ strtoupper(substr($course->title, 0, 2)) }}</div>
              @endif
              <span class="topic-duration">{{ $course->durasi_format }}</span>
            </div>
            <div class="topic-card-body">
              <div class="topic-card-kicker">Course</div>
              <div class="topic-card-title">{{ $course->title }}</div>
              <div class="topic-card-meta">
                <span>{{ $course->category->name ?? $meta['type_label'] }}</span>
                <span>{{ number_format($course->display_learner_count) }} learners</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </section>
  @endif

  <section class="topic-section" id="all-courses">
    <div class="topic-filter-row">
      <div>
        <div class="topic-title">{{ number_format($courses->total()) }} Results for {{ request('q') ?: $meta['title'] }}</div>
        <div class="topic-muted">Disesuaikan dengan content type {{ $meta['type_label'] }}.</div>
      </div>
      <div class="topic-filter-group">
        <div class="topic-select">
          <select onchange="location=this.value" aria-label="Duration">
            <option value="{{ request()->fullUrlWithQuery(['durasi' => null, 'page' => null]) }}" {{ request('durasi') ? '' : 'selected' }}>Time to complete</option>
            <option value="{{ request()->fullUrlWithQuery(['durasi' => 'short', 'page' => null]) }}" {{ request('durasi') === 'short' ? 'selected' : '' }}>Under 30 min</option>
            <option value="{{ request()->fullUrlWithQuery(['durasi' => 'medium', 'page' => null]) }}" {{ request('durasi') === 'medium' ? 'selected' : '' }}>30 min - 2 hr</option>
            <option value="{{ request()->fullUrlWithQuery(['durasi' => 'long', 'page' => null]) }}" {{ request('durasi') === 'long' ? 'selected' : '' }}>Over 2 hr</option>
          </select>
        </div>
        <div class="topic-select">
          <select onchange="location=this.value" aria-label="Level">
            <option value="{{ request()->fullUrlWithQuery(['level' => null, 'page' => null]) }}" {{ request('level') ? '' : 'selected' }}>Level</option>
            <option value="{{ request()->fullUrlWithQuery(['level' => 'Beginner', 'page' => null]) }}" {{ request('level') === 'Beginner' ? 'selected' : '' }}>Beginner</option>
            <option value="{{ request()->fullUrlWithQuery(['level' => 'Intermediate', 'page' => null]) }}" {{ request('level') === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
            <option value="{{ request()->fullUrlWithQuery(['level' => 'Advanced', 'page' => null]) }}" {{ request('level') === 'Advanced' ? 'selected' : '' }}>Advanced</option>
          </select>
        </div>
        <div class="topic-select">
          <select onchange="location=this.value" aria-label="Sort">
            <option value="{{ $sortUrl('popular') }}" {{ $selectedSort === 'popular' ? 'selected' : '' }}>Best Match</option>
            <option value="{{ $sortUrl('recent') }}" {{ $selectedSort === 'recent' ? 'selected' : '' }}>Most Recent</option>
            <option value="{{ $sortUrl('rating') }}" {{ $selectedSort === 'rating' ? 'selected' : '' }}>Top Rated</option>
          </select>
        </div>
      </div>
    </div>

    @if($courses->count())
      <div class="topic-result-list">
        @foreach($courses as $course)
          <a class="topic-result-card" href="{{ route('course.show', $course) }}">
            <div class="topic-result-thumb topic-thumb">
              @if($course->thumbnail)
                <img src="{{ $course->thumbnail }}" alt="" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="topic-thumb-fallback" style="display:none">{{ strtoupper(substr($course->title, 0, 2)) }}</div>
              @else
                <div class="topic-thumb-fallback">{{ strtoupper(substr($course->title, 0, 2)) }}</div>
              @endif
              <span class="topic-duration">{{ $course->durasi_format }}</span>
            </div>
            <div class="topic-result-body">
              <div class="topic-result-title">{{ $course->title }}</div>
              <div class="topic-result-desc">{{ \Illuminate\Support\Str::limit($course->description ?: 'Explore this course from your local LinkedIn Learning database.', 145) }}</div>
              <div class="topic-result-meta">
                <span>{{ $course->instructor_name ?: 'LinkedIn' }}</span>
                <span>{{ $course->level ?: ($course->scraped_level ?: 'Beginner') }}</span>
                <span>{{ $course->category->name ?? $meta['type_label'] }}</span>
                <span>{{ number_format($course->display_learner_count) }} learners</span>
                @if($ratingFor($course) > 0)
                  <span>{{ number_format($ratingFor($course), 1) }} rating</span>
                @endif
              </div>
            </div>
          </a>
        @endforeach
      </div>

      <div class="topic-pager" aria-label="Pagination">
        <a class="{{ $courses->onFirstPage() ? 'disabled' : '' }}" href="{{ $courses->previousPageUrl() ?: '#' }}">Previous</a>
        <div class="topic-page-info">Page {{ $courses->currentPage() }} / {{ $courses->lastPage() }}</div>
        <a class="{{ $courses->hasMorePages() ? '' : 'disabled' }}" href="{{ $courses->nextPageUrl() ?: '#' }}">Next</a>
      </div>
    @else
      <div class="topic-empty">
        Belum ada course yang cocok untuk filter ini. Coba subtopic lain, reset filter, atau cek kembali data course di database.
      </div>
    @endif
  </section>

  <section class="topic-section">
    <div class="topic-heading-row">
      <div>
        <div class="topic-title">Related {{ $meta['type_label'] }} topics</div>
        <div class="topic-muted">Topic lain mengikuti daftar LinkedIn Learning untuk kategori yang sama.</div>
      </div>
      <a class="topic-see-all" href="{{ $typeUrl($meta['type']) }}">Show all</a>
    </div>
    <div class="topic-chip-wrap">
      @foreach($relatedTopics as $topic)
        <a class="topic-chip" href="{{ $topicUrl($topic['slug']) }}">{{ $topic['title'] }}</a>
      @endforeach
    </div>
  </section>
</div>
@endsection
