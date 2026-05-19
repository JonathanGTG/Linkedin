@extends("layouts.app")
@section("title",$meta['title']." — Certifications")
@section("content")
<style>
  .page-wrap{background:#f3f2ef;border-radius:12px;padding:18px}
  .filter-row{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:18px}
  .pill{display:inline-flex;align-items:center;gap:8px;border:1px solid #d1d5db;background:#fff;border-radius:999px;padding:8px 14px;font-size:14px;font-weight:700;color:#111827;cursor:pointer}
  .pill:hover{background:#f9fafb}
  .reset{margin-left:10px;font-size:14px;font-weight:700;color:#0a66c2;text-decoration:none}
  .reset:hover{text-decoration:underline}

  .breadcrumb{font-size:14px;color:#6b7280;margin:6px 0 10px}
  .breadcrumb a{color:#111827;text-decoration:none}
  .breadcrumb a:hover{text-decoration:underline}

  .type-hero{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px 22px;margin-bottom:16px}
  .title{font-size:34px;font-weight:900;color:#111827;margin:0 0 10px;letter-spacing:-.02em}
  .desc{font-size:15px;color:#4b5563;margin:0;max-width:980px;line-height:1.65}

  .results-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px}
  .results-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid #e5e7eb}
  .results-count{font-size:14px;color:#374151}
  .sort{display:inline-flex;align-items:center;gap:8px;font-size:14px;font-weight:800;color:#111827}
  .icon{width:16px;height:16px;display:block}

  .list{padding:8px 0}
  .row{display:flex;gap:16px;padding:16px 18px;border-top:1px solid #eef2f7}
  .row:first-child{border-top:none}
  .thumb{width:220px;height:124px;border-radius:12px;background:linear-gradient(135deg,#e5e7eb,#f3f4f6);flex:0 0 220px;border:1px solid #e5e7eb}
  .meta{flex:1}
  .kicker{font-size:13px;color:#6b7280;margin-bottom:4px}
  .name{font-size:20px;font-weight:900;color:#111827;line-height:1.25;margin-bottom:6px}
  .sub{font-size:14px;color:#374151;margin-bottom:8px}
  .sub b{font-weight:900}
  .blurb{font-size:14px;color:#4b5563;line-height:1.6;max-width:860px}
  .detail-line{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0 10px}
  .detail-pill{display:inline-flex;align-items:center;border:1px solid #e5e7eb;background:#f9fafb;border-radius:999px;padding:4px 9px;font-size:12px;font-weight:700;color:#374151}
  .skills{font-size:13px;color:#6b7280;line-height:1.5;margin-top:8px;max-width:860px}
  .actions{display:flex;align-items:flex-start;gap:10px}
  .more{width:36px;height:36px;border-radius:999px;border:1px solid transparent;background:transparent;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;color:#111827}
  .more:hover{background:#f3f4f6}
  .save{border:1px solid #0a66c2;color:#0a66c2;background:#fff;border-radius:999px;padding:8px 14px;font-size:14px;font-weight:800;cursor:pointer}
  .save:hover{background:#e8f3ff}
  .open{border:1px solid #0a66c2;color:#fff;background:#0a66c2;border-radius:999px;padding:8px 14px;font-size:14px;font-weight:800;text-decoration:none;white-space:nowrap}
  .open:hover{background:#004182}
</style>

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
    <a class="reset" href="{{ route('certifications.index') }}">Reset</a>
  </div>

  <div class="breadcrumb">
    <a href="{{ route('certifications.index') }}">Browse</a> / {{ $meta['title'] }}
  </div>

  <div class="type-hero">
    <h1 class="title">{{ $meta['title'] }}</h1>
    <p class="desc">{{ $meta['desc'] }}</p>
  </div>

  <div class="results-card">
    <div class="results-head">
      <div class="results-count">{{ $certifications->count() }} Results</div>
      <div class="sort">
        <span>Best Match</span>
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
      </div>
    </div>

    <div class="list">
      @foreach($certifications as $cert)
        @php($duration = $cert->durasi)
        <div class="row">
          <div class="thumb"></div>
          <div class="meta">
            <div class="kicker">{{ $cert->type_label }}</div>
            <div class="name">{{ $cert->title }}</div>
            <div class="sub">
              @if($cert->durasi)
                <b>{{ $duration }}</b> ·
              @endif
              Provided by <b>{{ $cert->provider }}</b>
            </div>
            <div class="detail-line">
              @if($cert->level)
                <span class="detail-pill">{{ $cert->level }}</span>
              @endif
              @if($cert->tanggal_rilis)
                <span class="detail-pill">{{ $cert->tanggal_rilis }}</span>
              @endif
              @if($cert->rating)
                <span class="detail-pill">{{ $cert->rating }} rating</span>
              @endif
              @if($cert->jumlah_learner)
                <span class="detail-pill">{{ number_format($cert->jumlah_learner) }} learners</span>
              @endif
            </div>
            <div class="blurb">{{ $cert->description }}</div>
            @if($cert->skills)
              <div class="skills">{{ $cert->skills }}</div>
            @endif
          </div>
          <div class="actions">
            <button class="more" type="button" aria-label="More actions">
              <svg class="icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="6" cy="12" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="18" cy="12" r="1.7"/></svg>
            </button>
            @if($cert->source_url)
              <a class="open" href="{{ $cert->source_url }}" target="_blank" rel="noopener">Open</a>
            @else
              <button class="save" type="button">Save</button>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
