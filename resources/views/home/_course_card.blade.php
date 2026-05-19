<div class="li-course-card">
  <a href="{{ route('course.show', $course) }}" class="li-card-thumb-link">
    <div class="li-card-thumb">
      @if(!empty($course->thumbnail))
        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" loading="lazy"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="li-card-thumb-fallback" style="display:none">{{ strtoupper(substr($course->title,0,2)) }}</div>
      @else
        @php
          $colors = [
            ['#1d4ed8','#3b82f6'], ['#166534','#16a34a'], ['#7c2d12','#ea580c'],
            ['#581c87','#9333ea'], ['#0f172a','#334155'], ['#0e7490','#06b6d4'],
          ];
          $pair = $colors[crc32($course->id ?? $course->title) % count($colors)];
        @endphp
        <div class="li-card-thumb-fallback" style="background:linear-gradient(135deg,{{ $pair[0] }} 0%,{{ $pair[1] }} 100%)">
          {{ strtoupper(substr($course->title,0,2)) }}
        </div>
      @endif
      @if($course->durasi_format)
        <span class="li-card-duration">{{ $course->durasi_format }}</span>
      @endif
    </div>
  </a>
  <div class="li-card-body">
    <div class="li-card-type">Course</div>
    <a href="{{ route('course.show', $course) }}" class="li-card-title-link">
      <div class="li-card-title">{{ $course->title }}</div>
    </a>
    <div class="li-card-by">By: {{ $course->instructor_name ?: 'LinkedIn' }}</div>
  </div>
</div>