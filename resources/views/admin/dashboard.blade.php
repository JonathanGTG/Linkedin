@extends('layouts.app')
@section('title', 'Admin Dashboard — LinkedIn Learning')

@section('content')
<style>
  .admin-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
  .page-title   { font-size:24px; font-weight:700; color:#1d1d1d; }
  .admin-nav    { display:flex; gap:8px; margin-bottom:28px; flex-wrap:wrap; }
  .admin-nav a  { padding:8px 18px; border-radius:24px; font-size:14px; font-weight:600; text-decoration:none; border:1px solid #e0e0e0; color:#555; background:#fff; transition:all .15s; }
  .admin-nav a:hover  { border-color:#1d1d1d; color:#1d1d1d; }
  .admin-nav a.active { background:#1d1d1d; color:#fff; border-color:#1d1d1d; }

  /* Stats grid */
  .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
  .stat-card  { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:20px; }
  .stat-icon  { font-size:28px; margin-bottom:8px; }
  .stat-val   { font-size:32px; font-weight:700; color:#1d1d1d; line-height:1; margin-bottom:4px; }
  .stat-label { font-size:13px; color:#666; }

  /* Tables */
  .table-card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; margin-bottom:24px; }
  .table-card-header { display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #f0f0f0; }
  .table-card-header h3 { font-size:16px; font-weight:700; color:#1d1d1d; }
  .table-card-header a  { font-size:13px; color:#0a66c2; text-decoration:none; }
  table { width:100%; border-collapse:collapse; }
  thead th { padding:10px 16px; font-size:12px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.05em; text-align:left; border-bottom:1px solid #f0f0f0; }
  tbody td { padding:12px 16px; font-size:14px; color:#1d1d1d; border-bottom:1px solid #f8f8f8; vertical-align:middle; }
  tbody tr:last-child td { border-bottom:none; }
  tbody tr:hover td { background:#fafafa; }

  .badge { display:inline-block; padding:2px 10px; border-radius:12px; font-size:12px; font-weight:600; }
  .badge-green  { background:#e8f5e9; color:#2e7d32; }
  .badge-blue   { background:#e8f3ff; color:#0a66c2; }
  .badge-orange { background:#fff3e0; color:#e65100; }
  .badge-gray   { background:#f5f5f5; color:#666; }

  .flash-success { background:#e8f5e9; border:1px solid #a5d6a7; color:#2e7d32; border-radius:4px; padding:10px 14px; margin-bottom:16px; font-size:14px; }
</style>

@if(session('success'))
<div class="flash-success">{{ session('success') }}</div>
@endif

<div class="admin-header">
  <h1 class="page-title">Admin Dashboard</h1>
  <span style="font-size:13px;color:#888">Login sebagai: <strong>{{ auth()->user()->name }}</strong></span>
</div>

{{-- Nav --}}
<div class="admin-nav">
  <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
  <a href="{{ route('admin.courses') }}">Kelola Course</a>
  <a href="{{ route('admin.users') }}">Kelola User</a>
</div>

{{-- Stats --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon">👥</div>
    <div class="stat-val">{{ number_format($stats['total_users']) }}</div>
    <div class="stat-label">Total Users</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">📚</div>
    <div class="stat-val">{{ number_format($stats['total_courses']) }}</div>
    <div class="stat-label">Total Courses</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">📝</div>
    <div class="stat-val">{{ number_format($stats['total_enrollments']) }}</div>
    <div class="stat-label">Total Enrollments</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">✅</div>
    <div class="stat-val">{{ number_format($stats['total_completed']) }}</div>
    <div class="stat-label">Course Diselesaikan</div>
  </div>
</div>

{{-- Top Courses --}}
<div class="table-card">
  <div class="table-card-header">
    <h3>🔥 Top 5 Course Terpopuler</h3>
    <a href="{{ route('admin.courses') }}">Lihat semua →</a>
  </div>
  <table>
    <thead><tr>
      <th>#</th><th>Judul Course</th><th>Kategori</th><th>Level</th><th>Learners</th><th>Rating</th>
    </tr></thead>
    <tbody>
      @foreach($topCourses as $i => $course)
      <tr>
        <td style="color:#888;font-weight:700">{{ $i+1 }}</td>
        <td><strong>{{ $course->title }}</strong><br><span style="font-size:12px;color:#888">{{ $course->instructor_name }}</span></td>
        <td><span class="badge badge-blue">{{ optional($course->category)->name }}</span></td>
        <td>{{ $course->level }}</td>
        <td>{{ number_format($course->display_learner_count) }}</td>
        <td>⭐ {{ number_format($course->display_rating,1) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- Recent Enrollments --}}
<div class="table-card">
  <div class="table-card-header">
    <h3>🕒 Enrollment Terbaru</h3>
    <a href="{{ route('admin.users') }}">Lihat semua →</a>
  </div>
  <table>
    <thead><tr>
      <th>User</th><th>Course</th><th>Status</th><th>Tanggal</th>
    </tr></thead>
    <tbody>
      @foreach($recentEnrollments as $enroll)
      <tr>
        <td>{{ optional($enroll->user)->name }}<br><span style="font-size:12px;color:#888">{{ optional($enroll->user)->email }}</span></td>
        <td style="max-width:200px">{{ Str::limit(optional($enroll->course)->title, 45) }}</td>
        <td>
          @php $statusMap = ['in_progress'=>['Dalam Proses','badge-blue'],'completed'=>['Selesai','badge-green'],'saved'=>['Disimpan','badge-gray']]; @endphp
          <span class="badge {{ $statusMap[$enroll->status][1] ?? 'badge-gray' }}">{{ $statusMap[$enroll->status][0] ?? $enroll->status }}</span>
        </td>
        <td style="font-size:13px;color:#666">{{ $enroll->created_at->format('d M Y') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
