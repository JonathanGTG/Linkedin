<a class="learning-card" href="{{ $item['url'] }}">
  <div class="thumb">
    @if(!empty($item['thumbnail']))
      <img src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}" loading="lazy"
           onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <div class="thumb-fallback" style="display:none">{{ strtoupper(substr($item['title'], 0, 2)) }}</div>
    @else
      <div class="thumb-fallback">{{ strtoupper(substr($item['title'], 0, 2)) }}</div>
    @endif
    @if(!empty($item['duration']))
      <span class="duration">{{ $item['duration'] }}</span>
    @endif
  </div>
  <div>
    <div class="card-kind">{{ $item['type'] }}</div>
    <div class="card-title">{{ $item['title'] }}</div>
    <div class="card-meta">
      By: {{ $item['author'] }}
      @if(!empty($item['date']))
        &middot; {{ $item['date'] }}
      @endif
    </div>
    @if(!empty($item['description']))
      <div class="card-desc">{{ $item['description'] }}</div>
    @endif
    @if(!empty($item['learners']))
      <div class="learners">{{ number_format($item['learners']) }} learners</div>
    @endif
  </div>
</a>
