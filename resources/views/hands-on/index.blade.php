@extends('layouts.app')

@section('title', 'Hands-On Tech - LinkedIn Learning')

@section('content')
<style>
  .hot-shell {
    max-width: 1180px;
    margin: 0 auto;
    color: #1f2328;
  }

  .hot-hero {
    background: #fff;
    border: 1px solid #d8dee4;
    border-radius: 8px;
    padding: 28px;
    margin-bottom: 16px;
  }

  .hot-kicker {
    margin: 0 0 10px;
    color: #0a66c2;
    font-size: 14px;
    font-weight: 850;
  }

  .hot-title {
    margin: 0;
    font-size: clamp(30px, 4vw, 44px);
    line-height: 1.06;
    font-weight: 850;
    letter-spacing: 0;
  }

  .hot-desc {
    margin: 14px 0 0;
    max-width: 900px;
    color: #59636e;
    font-size: 16px;
    line-height: 1.65;
  }

  .hot-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-bottom: 16px;
  }

  .hot-search {
    flex: 1 1 280px;
    display: flex;
    gap: 10px;
  }

  .hot-search input,
  .hot-select {
    min-height: 40px;
    border: 1px solid #c9cfd6;
    border-radius: 999px;
    background: #fff;
    padding: 0 14px;
    color: #24292f;
    font: inherit;
    font-size: 14px;
    outline: none;
  }

  .hot-search input {
    width: 100%;
  }

  .hot-search input:focus,
  .hot-select:focus {
    border-color: #0a66c2;
    box-shadow: 0 0 0 3px rgba(10, 102, 194, .12);
  }

  .hot-button,
  .hot-reset {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    border-radius: 999px;
    padding: 0 16px;
    font-size: 14px;
    font-weight: 850;
    text-decoration: none;
    cursor: pointer;
  }

  .hot-button {
    border: 1px solid #0a66c2;
    color: #fff;
    background: #0a66c2;
  }

  .hot-reset {
    border: 1px solid #c9cfd6;
    color: #24292f;
    background: #fff;
  }

  .hot-button:hover {
    background: #004182;
  }

  .hot-reset:hover {
    background: #f6f8fa;
  }

  .hot-results {
    background: #fff;
    border: 1px solid #d8dee4;
    border-radius: 8px;
    overflow: hidden;
  }

  .hot-results-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 20px;
    border-bottom: 1px solid #d8dee4;
  }

  .hot-results-head h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 850;
  }

  .hot-count {
    color: #59636e;
    font-size: 14px;
    white-space: nowrap;
  }

  .hot-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    padding: 20px;
  }

  .hot-card {
    display: flex;
    flex-direction: column;
    min-width: 0;
    min-height: 310px;
    border: 1px solid #d8dee4;
    border-radius: 8px;
    overflow: hidden;
    color: #24292f;
    background: #fff;
    text-decoration: none;
  }

  .hot-card:hover {
    border-color: #8c959f;
    box-shadow: 0 8px 24px rgba(140, 149, 159, .14);
  }

  .hot-thumb {
    position: relative;
    min-height: 130px;
    color: #fff;
    background:
      linear-gradient(135deg, rgba(10, 102, 194, .92), rgba(36, 41, 47, .88)),
      repeating-linear-gradient(45deg, rgba(255,255,255,.18) 0 2px, transparent 2px 10px);
  }

  .hot-thumb img {
    width: 100%;
    height: 130px;
    object-fit: cover;
    display: block;
  }

  .hot-thumb-fallback {
    height: 130px;
    display: flex;
    align-items: flex-end;
    padding: 12px;
    font-size: 34px;
    font-weight: 850;
  }

  .hot-duration {
    position: absolute;
    right: 10px;
    bottom: 10px;
    display: inline-flex;
    align-items: center;
    min-height: 24px;
    border-radius: 4px;
    padding: 2px 8px;
    color: #fff;
    background: rgba(0, 0, 0, .72);
    font-size: 12px;
    font-weight: 850;
  }

  .hot-body {
    display: flex;
    flex-direction: column;
    gap: 9px;
    flex: 1;
    padding: 14px;
  }

  .hot-label {
    color: #59636e;
    font-size: 12px;
    font-weight: 850;
    text-transform: uppercase;
  }

  .hot-card-title {
    margin: 0;
    font-size: 16px;
    line-height: 1.3;
    font-weight: 850;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .hot-byline,
  .hot-meta {
    margin: 0;
    color: #59636e;
    font-size: 13px;
    line-height: 1.45;
  }

  .hot-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: auto;
  }

  .hot-chip {
    display: inline-flex;
    align-items: center;
    min-height: 26px;
    border-radius: 999px;
    padding: 0 10px;
    color: #24292f;
    background: #f6f8fa;
    border: 1px solid #eaeef2;
    font-size: 12px;
    font-weight: 800;
  }

  .hot-empty {
    padding: 44px 20px;
    color: #59636e;
    text-align: center;
  }

  .hot-pagination {
    padding: 16px 20px;
    border-top: 1px solid #eaeef2;
  }

  @media (max-width: 1100px) {
    .hot-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }
  }

  @media (max-width: 820px) {
    .hot-hero {
      padding: 20px;
    }

    .hot-grid {
      grid-template-columns: 1fr;
      padding: 14px;
    }

    .hot-results-head {
      align-items: flex-start;
      flex-direction: column;
    }

    .hot-count {
      white-space: normal;
    }
  }
</style>

<div class="hot-shell">
  <section class="hot-hero">
    <p class="hot-kicker">Hands-On Tech</p>
    <h1 class="hot-title">Hands-On courses from your catalog</h1>
    <p class="hot-desc">
      This page now uses the main course database and only shows published courses whose title contains "Hands-On".
      Each course opens inside this app using the existing course detail page.
    </p>
  </section>

  <form class="hot-filters" method="GET" action="{{ route('hands-on.index') }}">
    <div class="hot-search">
      <input type="search" name="q" value="{{ request('q') }}" placeholder="Search Hands-On courses">
      <button class="hot-button" type="submit">Search</button>
    </div>

    <select class="hot-select" name="level" onchange="this.form.submit()">
      <option value="">All levels</option>
      @foreach(['Beginner', 'Intermediate', 'Advanced'] as $level)
        <option value="{{ $level }}" @selected(request('level') === $level)>{{ $level }}</option>
      @endforeach
    </select>

    <select class="hot-select" name="durasi" onchange="this.form.submit()">
      <option value="">Any duration</option>
      <option value="short" @selected(request('durasi') === 'short')>30 min or less</option>
      <option value="medium" @selected(request('durasi') === 'medium')>31-60 min</option>
      <option value="long" @selected(request('durasi') === 'long')>Over 60 min</option>
    </select>

    <a class="hot-reset" href="{{ route('hands-on.index') }}">Reset</a>
  </form>

  <section class="hot-results">
    <div class="hot-results-head">
      <h2>Hands-On course results</h2>
      <div class="hot-count">{{ $courses->total() }} courses</div>
    </div>

    @if($courses->count() > 0)
      <div class="hot-grid">
        @foreach($courses as $course)
          <a class="hot-card" href="{{ route('course.show', $course) }}">
            <div class="hot-thumb">
              @if($course->thumbnail)
                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="hot-thumb-fallback" style="display:none">{{ strtoupper(substr($course->title, 0, 2)) }}</div>
              @else
                <div class="hot-thumb-fallback">{{ strtoupper(substr($course->title, 0, 2)) }}</div>
              @endif
              <span class="hot-duration">{{ $course->durasi_format }}</span>
            </div>

            <div class="hot-body">
              <div class="hot-label">{{ $course->category->name ?? 'Course' }}</div>
              <h3 class="hot-card-title">{{ $course->title }}</h3>
              <p class="hot-byline">By: {{ $course->instructor_name ?: 'LinkedIn Learning' }}</p>
              <div class="hot-meta">
                <span class="hot-chip">{{ $course->level ?: ($course->scraped_level ?: 'Beginner') }}</span>
                <span class="hot-chip">{{ number_format($course->display_learner_count) }} learners</span>
                @if($course->display_rating > 0)
                  <span class="hot-chip">{{ number_format($course->display_rating, 1) }} rating</span>
                @endif
              </div>
            </div>
          </a>
        @endforeach
      </div>

      @if($courses->hasPages())
        <div class="hot-pagination">{{ $courses->links() }}</div>
      @endif
    @else
      <div class="hot-empty">
        <strong>No Hands-On courses found.</strong>
        <div>Add courses with "Hands-On" in the title, or clear the current filters.</div>
      </div>
    @endif
  </section>
</div>
@endsection
