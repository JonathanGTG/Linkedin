@extends('layouts.app')
@section('title', 'Kelola User - Admin')

@section('content')
<style>
  .page-title { font-size:24px; font-weight:700; color:#1d1d1d; margin-bottom:24px; }
  .admin-nav { display:flex; gap:8px; margin-bottom:24px; flex-wrap:wrap; }
  .admin-nav a { padding:8px 18px; border-radius:24px; font-size:14px; font-weight:600; text-decoration:none; border:1px solid #e0e0e0; color:#555; background:#fff; }
  .admin-nav a:hover { border-color:#1d1d1d; color:#1d1d1d; }
  .admin-nav a.active { background:#1d1d1d; color:#fff; border-color:#1d1d1d; }

  .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; gap:12px; flex-wrap:wrap; }
  .search-box { display:flex; gap:8px; }
  .search-box input { padding:8px 12px; border:1px solid #ccc; border-radius:4px; font-size:14px; width:260px; outline:none; font-family:inherit; }
  .search-box input:focus { border-color:#0a66c2; }

  .btn-primary { height:38px; padding:0 20px; background:#1d1d1d; color:#fff; border:none; border-radius:24px; font-size:14px; font-weight:600; cursor:pointer; font-family:inherit; }
  .btn-primary:hover { background:#333; }
  .btn-sm { height:30px; padding:0 12px; font-size:12px; font-weight:600; border:none; border-radius:20px; cursor:pointer; font-family:inherit; }
  .btn-edit { background:#e8f3ff; color:#0a66c2; }
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
  .badge-blue { background:#e8f3ff; color:#0a66c2; }
  .badge-gray { background:#f5f5f5; color:#666; }
  .badge-dark { background:#1d1d1d; color:#fff; }

  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:200; align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal { background:#fff; border-radius:8px; padding:28px; width:460px; max-width:95vw; }
  .modal h3 { font-size:18px; font-weight:700; color:#1d1d1d; margin-bottom:20px; }
  .form-group { margin-bottom:14px; }
  .form-group label { display:block; font-size:13px; font-weight:600; color:#444; margin-bottom:5px; }
  .form-group input, .form-group select { width:100%; padding:9px 12px; border:1px solid #ccc; border-radius:4px; font-size:14px; font-family:inherit; outline:none; }
  .form-group input:focus, .form-group select:focus { border-color:#0a66c2; }
  .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
  .btn-cancel { height:38px; padding:0 18px; background:transparent; color:#555; border:1px solid #ccc; border-radius:24px; font-size:14px; cursor:pointer; font-family:inherit; }

  .flash-success { background:#e8f5e9; border:1px solid #a5d6a7; color:#2e7d32; border-radius:4px; padding:10px 14px; margin-bottom:16px; font-size:14px; }
  .flash-error { background:#fff0f0; border:1px solid #f5c2c7; color:#c0392b; border-radius:4px; padding:10px 14px; margin-bottom:16px; font-size:14px; }
</style>

@if(session('success'))<div class="flash-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="flash-error">{{ session('error') }}</div>@endif

<h1 class="page-title">Kelola User</h1>

<div class="admin-nav">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <a href="{{ route('admin.courses') }}">Kelola Course</a>
  <a href="{{ route('admin.users') }}" class="active">Kelola User</a>
</div>

<div class="top-bar">
  <form class="search-box" method="GET">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari user...">
    <button type="submit" class="btn-primary">Cari</button>
  </form>
</div>

<div class="table-card">
  <table>
    <thead>
      <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
        <th>Enrollment</th>
        <th>Bergabung</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $user)
      <tr>
        <td>
          <strong>{{ $user->name }}</strong>
          <br><span style="font-size:12px;color:#888">ID: {{ $user->id }}</span>
        </td>
        <td>{{ $user->email }}</td>
        <td>
          <span class="badge {{ $user->role === 'admin' ? 'badge-dark' : 'badge-blue' }}">
            {{ ucfirst($user->role) }}
          </span>
        </td>
        <td>{{ number_format($user->enrollments_count) }}</td>
        <td style="font-size:13px;color:#666">{{ optional($user->created_at)->format('d M Y') }}</td>
        <td>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            <button class="btn-sm btn-edit"
              onclick="openEdit({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}')">
              Edit
            </button>
            @if($user->role !== 'admin')
            <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Hapus user ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-sm btn-delete">Hapus</button>
            </form>
            @else
            <span class="badge badge-gray">Protected</span>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:#888;padding:40px">Tidak ada user ditemukan.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div style="margin-top:16px">{{ $users->links() }}</div>

<div class="modal-overlay" id="editModal">
  <div class="modal">
    <h3>Edit User</h3>
    <form method="POST" id="editForm" action="#">
      @csrf @method('PUT')
      <div class="form-group">
        <label>Nama *</label>
        <input type="text" name="name" id="edit-name" required>
      </div>
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" id="edit-email" required>
      </div>
      <div class="form-group">
        <label>Role *</label>
        <select name="role" id="edit-role" required>
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
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
function openEdit(id, name, email, role) {
  document.getElementById('editForm').action = '/admin/users/' + id;
  document.getElementById('edit-name').value = name;
  document.getElementById('edit-email').value = email;
  document.getElementById('edit-role').value = role;
  document.getElementById('editModal').classList.add('open');
}

document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', e => {
    if (e.target === overlay) overlay.classList.remove('open');
  });
});
</script>
@endpush
@endsection
