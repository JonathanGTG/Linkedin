@extends("layouts.app")
@section("title","Certifications — LinkedIn Learning")
@section("content")
<style>
  .cert-page{background:#fff;border-radius:10px}
  .cert-hero{padding:28px 26px 24px;border-bottom:1px solid #e5e7eb}
  .page-title{font-size:40px;font-weight:800;color:#111827;letter-spacing:-.02em;margin:0 0 10px}
  .page-sub{font-size:16px;color:#4b5563;margin:0;max-width:900px;line-height:1.65}

  .section-wrap{background:#f3f2ef;padding:22px 0}
  .section-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:22px 22px 18px;margin:0 0 18px}
  .section-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:14px}
  .section-title{font-size:20px;font-weight:800;color:#111827;margin:0 0 8px}
  .section-desc{font-size:14px;color:#4b5563;margin:0;line-height:1.6;max-width:820px}
  .show-all{font-size:16px;font-weight:700;color:#0a66c2;text-decoration:none;white-space:nowrap;padding:8px 10px;border-radius:8px}
  .show-all:hover{background:#e8f3ff}

  .provider-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-top:14px}
  @media (max-width: 920px){.provider-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media (max-width: 560px){.provider-grid{grid-template-columns:repeat(1,minmax(0,1fr))}}

  .provider-tile{display:flex;align-items:center;gap:14px;border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;background:#fff;text-decoration:none}
  .provider-tile:hover{background:#f9fafb;border-color:#d1d5db}
  .provider-logo{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex:0 0 40px;background:#fff;border:1px solid #e5e7eb;overflow:hidden}
  .provider-logo svg{width:26px;height:26px;display:block}
  .provider-initial{font-weight:900;color:#111827;font-size:16px}

  .provider-name{font-size:16px;font-weight:800;color:#111827}
</style>

<div class="cert-page">
  <div class="cert-hero">
    <h1 class="page-title">Certifications</h1>
    <p class="page-sub">Earn a professional certificate from top brands on LinkedIn Learning or prepare for off-platform certifications and CEUs with prep courses and assessment options available for over 175 different credentials.</p>
  </div>

  <div class="section-wrap">
    @foreach($sections as $section)
      <div class="section-card">
        <div class="section-head">
          <div>
            <h2 class="section-title">{{ $section['title'] }}</h2>
            <p class="section-desc">{{ $section['desc'] }}</p>
          </div>
          <a class="show-all" href="{{ route('certifications.type', $section['type_slug']) }}">Show all</a>
        </div>

        @if($section['providers']->count() > 0)
          <div class="provider-grid">
            @foreach($section['providers']->take(6) as $provider)
              @php
                $abbr = strtoupper(substr($provider['name'], 0, 1));
                $logo = null;
                if ($provider['slug'] === 'microsoft') {
                  $logo = '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="9" height="9" fill="#F25022"/><rect x="13" y="2" width="9" height="9" fill="#7FBA00"/><rect x="2" y="13" width="9" height="9" fill="#00A4EF"/><rect x="13" y="13" width="9" height="9" fill="#FFB900"/></svg>';
                } elseif ($provider['slug'] === 'github') {
                  $logo = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="#111827"/><text x="12" y="15" text-anchor="middle" font-size="9" font-weight="900" fill="#fff" font-family="Inter, -apple-system, BlinkMacSystemFont, Segoe UI, system-ui, sans-serif">GH</text></svg>';
                } elseif ($provider['slug'] === 'adobe') {
                  $logo = '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="4" fill="#FF0000"/><path d="M7 18 11.3 6h1.4L17 18h-2.1l-1-2.8H10l-1 2.8H7Zm3.7-4.6h2.6L12 9.7l-1.3 3.7Z" fill="#fff"/></svg>';
                }
              @endphp
              <a class="provider-tile" href="{{ route('certifications.provider', [$section['type_slug'], $provider['slug']]) }}">
                <span class="provider-logo">
                  @if($logo)
                    {!! $logo !!}
                  @else
                    <span class="provider-initial">{{ $abbr }}</span>
                  @endif
                </span>
                <span>
                  <div class="provider-name">{{ $provider['name'] }}</div>
                </span>
              </a>
            @endforeach
          </div>
        @else
          <div style="padding:18px 0;color:#6b7280;font-size:14px;">Belum ada konten tersedia.</div>
        @endif
      </div>
    @endforeach
  </div>
</div>

@endsection
