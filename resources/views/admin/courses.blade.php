@extends('layouts.app')
@section('title', 'Kelola Course — Admin')

@section('content')
<style>
  .page-title { font-size:24px; font-weight:700; color:#1d1d1d; margin-bottom:24px; }
  .admin-nav    { display:flex; gap:8px; margin-bottom:24px; }
  .admin-nav a  { padding:8px 18px; border-radius:24px; font-size:14px; font-weight:600; text-decoration:none; border:1px solid #e0e0e0; color:#555; background:#fff; }
  .admin-nav a:hover { border-color:#1d1d1d; color:#1d1d1d; }
  .admin-nav a.active { background:#1d1d1d; color:#fff; border-color:#1d1d1d; }

  .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; gap:12px; }
  .search-box { display:flex; gap:8px; }
  .search-box input { padding:8px 12px; border:1px solid #ccc; border-radius:4px; font-size:14px; width:260px; outline:none; font-family:inherit; }
  .search-box input:focus { border-color:#0a66c2; }
  .btn-primary { height:38px; padding:0 20px; background:#1d1d1d; color:#fff; border:none; border-radius:24px; font-size:14px; font-weight:600; cursor:pointer; font-family:inherit; }
  .btn-primary:hover { background:#333; }
  .btn-sm { height:30px; padding:0 12px; font-size:12px; font-weight:600; border:none; border-radius:20px; cursor:pointer; font-family:inherit; }
  .btn-edit   { background:#e8f3ff; color:#0a66c2; }
  .btn-edit:hover { background:#d0e8ff; }
  .btn-delete { background:#fff0f0; color:#c0392b; }
  .btn-delete:hover { background:#ffe0e0; }

  .table-card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; }
  table { width:100%; border-collapse:collapse; }
  thead th { padding:10px 14px; font-size:12px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.05em; text-align:left; border-bottom:1px solid #f0f0f0; background:#fafafa; }
  tbody td { padding:11px 14px; font-size:14px; color:#1d1d1d; border-bottom:1px solid #f5f5f5; vertical-align:middle; }
  tbody tr:last-child td { border-bottom:none; }
  tbody tr:hover td { background:#fafafa; }

  .badge { display:inline-block; padding:2px 10px; border-radius:12px; font-size:12px; font-weight:600; }
  .badge-blue   { background:#e8f3ff; color:#0a66c2; }
  .badge-green  { background:#e8f5e9; color:#2e7d32; }
  .badge-gray   { background:#f5f5f5; color:#888; }

  /* Add Course Modal */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:200; align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal { background:#fff; border-radius:8px; padding:28px; width:520px; max-width:95vw; max-height:90vh; overflow-y:auto; }
  .modal h3 { font-size:18px; font-weight:700; color:#1d1d1d; margin-bottom:20px; }
  .form-group { margin-bottom:14px; }
  .form-group label { display:block; font-size:13px; font-weight:600; color:#444; margin-bottom:5px; }
  .form-group input, .form-group select, .form-group textarea { width:100%; padding:9px 12px; border:1px solid #ccc; border-radius:4px; font-size:14px; font-family:inherit; outline:none; }
  .form-group input:focus, .form-group select:focus { border-color:#0a66c2; }
  .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
  .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
  .btn-cancel { height:38px; padding:0 18px; background:transparent; color:#555; border:1px solid #ccc; border-radius:24px; font-size:14px; cursor:pointer; font-family:inherit; }

  .flash-success { background:#e8f5e9; border:1px solid #a5d6a7; color:#2e7d32; border-radius:4px; padding:10px 14px; margin-bottom:16px; font-size:14px; }
  .flash-error   { background:#fff0f0; border:1px solid #f5c2c7; color:#c0392b; border-radius:4px; padding:10px 14px; margin-bottom:16px; font-size:14px; }
</style>

@if(session('success'))<div class="flash-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="flash-error">{{ session('error') }}</div>@endif

<h1 class="page-title">Kelola Course</h1>

<div class="admin-nav">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <a href="{{ route('admin.courses') }}" class="active">Kelola Course</a>
  <a href="{{ route('admin.users') }}">Kelola User</a>
</div>

<div class="top-bar">
  <form class="search-box" method="GET">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari course...">
    <button type="submit" class="btn-primary">Cari</button>
  </form>
  <button class="btn-primary" onclick="document.getElementById('addModal').classList.add('open')">+ Tambah Course</button>
</div>

<div class="table-card">
  <table>
    <thead><tr>
      <th>Judul</th><th>Kategori</th><th>Instruktur</th><th>Level</th><th>Durasi</th><th>Rating</th><th>Status</th><th>Aksi</th>
    </tr></thead>
    <tbody>
      @forelse($courses as $course)
      <tr>
        <td style="max-width:200px">
          <strong>{{ Str::limit($course->title, 40) }}</strong>
          <br><span style="font-size:12px;color:#888">ID: {{ $course->id }}</span>
        </td>
        <td><span class="badge badge-blue">{{ optional($course->category)->name }}</span></td>
        <td style="font-size:13px">{{ $course->instructor_name }}</td>
        <td style="font-size:13px">{{ $course->level }}</td>
        <td style="font-size:13px">{{ $course->durasi_format }}</td>
        <td>⭐ {{ number_format($course->display_rating, 1) }}</td>
        <td>
          @if($course->is_published)
            <span class="badge badge-green">Publik</span>
          @else
            <span class="badge badge-gray">Draft</span>
          @endif
        </td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn-sm btn-edit"
              onclick="openEdit({{ $course->id }},'{{ addslashes($course->title) }}','{{ $course->category_id }}','{{ addslashes($course->instructor_name) }}','{{ $course->level }}','{{ $course->durasi_detik }}','{{ $course->rating }}','{{ (int)$course->is_published }}')">
              Edit
            </button>
            <form method="POST" action="{{ route('admin.courses.delete', $course) }}" onsubmit="return confirm('Hapus course ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-sm btn-delete">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center;color:#888;padding:40px">Tidak ada course ditemukan.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div style="margin-top:16px">{{ $courses->links() }}</div>

{{-- ADD MODAL --}}
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <h3>Tambah Course Baru</h3>
    <form method="POST" action="{{ route('admin.courses.store') }}">
      @csrf
      <div class="form-group">
        <label>Judul Course *</label>
        <input type="text" name="title" required placeholder="Judul course">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Kategori *</label>
          <select name="category_id" required>
            <option value="">Pilih kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Level *</label>
          <select name="level" required>
            <option value="Beginner">Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced">Advanced</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Nama Instruktur *</label>
        <input type="text" name="instructor_name" required placeholder="Nama instruktur">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Durasi (detik) *</label>
          <input type="number" name="durasi_detik" min="0" required placeholder="cth: 5400 = 1.5 jam">
        </div>
        <div class="form-group">
          <label>Rating (0-5)</label>
          <input type="number" name="rating" min="0" max="5" step="0.1" placeholder="4.5">
        </div>
      </div>
      <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="description" rows="3" placeholder="Deskripsi singkat..."></textarea>
      </div>
      <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
          <input type="checkbox" name="is_published" value="1" checked> Publish (tampil ke user)
        </label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="document.getElementById('addModal').classList.remove('open')">Batal</button>
        <button type="submit" class="btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <h3>Edit Course</h3>
    <form method="POST" id="editForm" action="#">
      @csrf @method('PUT')
      <div class="form-group">
        <label>Judul Course *</label>
        <input type="text" name="title" id="edit-title" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Kategori *</label>
          <select name="category_id" id="edit-category" required>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Level *</label>
          <select name="level" id="edit-level" required>
            <option value="Beginner">Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced">Advanced</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Nama Instruktur *</label>
        <input type="text" name="instructor_name" id="edit-instructor" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Durasi (detik) *</label>
          <input type="number" name="durasi_detik" id="edit-durasi" min="0" required>
        </div>
        <div class="form-group">
          <label>Rating</label>
          <input type="number" name="rating" id="edit-rating" min="0" max="5" step="0.1">
        </div>
      </div>
      <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
          <input type="checkbox" name="is_published" id="edit-published" value="1"> Publish
        </label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="document.getElementById('editModal').classList.remove('open')">Batal</button>
        <button type="submit" class="btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function openEdit(id, title, catId, instructor, level, durasi, rating, published) {
  document.getElementById('editForm').action = '/admin/courses/' + id;
  document.getElementById('edit-title').value      = title;
  document.getElementById('edit-category').value   = catId;
  document.getElementById('edit-instructor').value = instructor;
  document.getElementById('edit-level').value      = level;
  document.getElementById('edit-durasi').value     = durasi;
  document.getElementById('edit-rating').value     = rating;
  document.getElementById('edit-published').checked= published == 1;
  document.getElementById('editModal').classList.add('open');
}
// Klik luar modal = tutup
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('open'); });
});
</script>
@endpush
@endsection
