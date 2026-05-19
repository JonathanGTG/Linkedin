<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar - LinkedIn Learning</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  :root {
    --blue: #0a66c2;
    --blue-dark: #004182;
    --text: #1d1d1d;
    --muted: #666;
    --surface: #fff;
    --page: #f3f2ee;
    --border: #ccc;
    --error: #c0392b;
  }

  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--page);
    color: var(--text);
  }

  .topbar {
    background: var(--surface);
    padding: 18px 60px;
    border-bottom: 1px solid #e0dfdc;
  }

  .logo {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--blue);
    font-size: 26px;
    text-decoration: none;
  }

  .logo-badge {
    width: 36px;
    height: 36px;
    background: var(--blue);
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: 20px;
  }

  .logo strong { font-weight: 700; }
  .logo span:last-child { font-weight: 300; }

  .register-hero {
    background: var(--surface);
    padding: 34px 60px 42px;
    display: grid;
    grid-template-columns: minmax(320px, 460px) 1fr;
    gap: 56px;
    align-items: start;
  }

  .register-copy h1 {
    font-size: 34px;
    line-height: 1.15;
    font-weight: 600;
    margin-bottom: 12px;
  }

  .register-copy p {
    color: var(--muted);
    font-size: 15px;
    line-height: 1.6;
    max-width: 460px;
  }

  .register-card {
    border: 1px solid #e0dfdc;
    border-radius: 8px;
    padding: 24px;
    background: #fff;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
  }

  .register-card h2 {
    font-size: 22px;
    font-weight: 650;
    margin-bottom: 4px;
  }

  .register-card .helper {
    color: var(--muted);
    font-size: 14px;
    margin-bottom: 20px;
  }

  .error-box {
    background: #fff1f1;
    border: 1px solid #f3b9b9;
    color: var(--error);
    border-radius: 6px;
    padding: 12px 14px;
    font-size: 13px;
    line-height: 1.55;
    margin-bottom: 16px;
  }

  .error-box ul {
    padding-left: 18px;
  }

  .field {
    display: grid;
    gap: 7px;
    margin-bottom: 14px;
  }

  .field label {
    font-size: 13px;
    font-weight: 650;
    color: #333;
  }

  .field input {
    width: 100%;
    border: 1px solid #9b9b9b;
    border-radius: 4px;
    padding: 12px 13px;
    font-size: 15px;
    outline: none;
    background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
  }

  .field input:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(10,102,194,0.14);
  }

  .btn-register {
    width: 100%;
    border: 0;
    border-radius: 999px;
    background: var(--blue);
    color: #fff;
    padding: 13px 18px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s, box-shadow 0.15s, transform 0.15s;
  }

  .btn-register:hover {
    background: var(--blue-dark);
    box-shadow: 0 4px 16px rgba(10,102,194,0.25);
    transform: translateY(-1px);
  }

  .login-link {
    margin-top: 16px;
    font-size: 14px;
    text-align: center;
    color: var(--muted);
  }

  .login-link a {
    color: var(--blue);
    font-weight: 650;
    text-decoration: none;
  }

  .login-link a:hover { text-decoration: underline; }

  .topics-section {
    background: #fff;
    padding: 32px 60px 40px;
  }

  .grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px;
  }

  .category h3 {
    font-size: 15px;
    font-weight: 700;
    color: #1d1d1d;
    margin-bottom: 10px;
  }

  .category ul {
    list-style: none;
    margin-bottom: 12px;
  }

  .category ul li {
    font-size: 14px;
    color: #1d1d1d;
    padding: 3px 0;
    cursor: pointer;
  }

  .category ul li:hover { text-decoration: underline; }

  .show-all {
    font-size: 14px;
    font-weight: 600;
    color: var(--blue);
    cursor: pointer;
    text-decoration: none;
  }

  .show-all:hover { text-decoration: underline; }

  .divider {
    height: 12px;
    background: var(--page);
  }

  .software-section {
    background: #fff;
    padding: 32px 60px 40px;
  }

  .software-section h2 {
    font-size: 18px;
    font-weight: 700;
    color: #1d1d1d;
    margin-bottom: 20px;
  }

  .tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .tag {
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 8px 18px;
    font-size: 14px;
    color: #1d1d1d;
    cursor: pointer;
    white-space: nowrap;
    transition: border-color 0.15s, background 0.15s, color 0.15s;
  }

  .tag:hover {
    border-color: var(--blue);
    color: var(--blue);
    background: #f0f7ff;
  }

  @media (max-width: 900px) {
    .register-hero {
      grid-template-columns: 1fr;
      gap: 28px;
    }
  }

  @media (max-width: 768px) {
    .topbar,
    .register-hero,
    .topics-section,
    .software-section {
      padding-left: 20px;
      padding-right: 20px;
    }

    .grid {
      grid-template-columns: 1fr;
      gap: 28px;
    }

    .register-copy h1 {
      font-size: 28px;
    }
  }
</style>
</head>
<body>
  <header class="topbar">
    <a href="{{ url('/') }}" class="logo" aria-label="LinkedIn Learning">
      <span class="logo-badge">in</span>
      <strong>LinkedIn</strong>
      <span>Learning</span>
    </a>
  </header>

  <section class="register-hero">
    <div class="register-card">
      <h2>Buat akun</h2>
      <p class="helper">Mulai perjalanan belajar Anda hari ini.</p>

      @if($errors->any())
        <div class="error-box">
          <ul>
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="field">
          <label for="name">Nama Lengkap</label>
          <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required placeholder="Nama lengkap">
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required placeholder="email@example.com">
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input id="password" type="password" name="password" autocomplete="new-password" required placeholder="Minimal 8 karakter">
        </div>

        <div class="field">
          <label for="password_confirmation">Konfirmasi Password</label>
          <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required placeholder="Ulangi password">
        </div>

        <button type="submit" class="btn-register">Buat Akun</button>
      </form>

      <div class="login-link">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
      </div>
    </div>

    <div class="register-copy">
      <h1>Explore skills that move your career forward</h1>
      <p>Pilih akun belajar Anda, lalu temukan topik dan software populer seperti di LinkedIn Learning. Data katalog di aplikasi ini tetap bisa memakai data dummy atau hasil scraping yang sudah kalian punya.</p>
    </div>
  </section>

  <div class="topics-section">
    <div class="grid">
      <div class="category">
        <h3>Sales</h3>
        <ul>
          <li>CRM Software</li>
          <li>Sales Skills</li>
          <li>Social Selling</li>
          <li>Sales Management</li>
          <li>Sales Metrics</li>
        </ul>
        <a class="show-all">Show all</a>
      </div>

      <div class="category">
        <h3>Small Business and Entrepreneurship</h3>
        <ul>
          <li>Design Business</li>
          <li>Small Business Marketing</li>
          <li>Entrepreneurship</li>
          <li>Small Business Finance</li>
          <li>Web Design Business</li>
        </ul>
        <a class="show-all">Show all</a>
      </div>

      <div class="category">
        <h3>Training and Education</h3>
        <ul>
          <li>E-Learning Software</li>
          <li>Corporate Training</li>
          <li>Instructional Design</li>
          <li>Educational Technology</li>
          <li>Teaching</li>
        </ul>
        <a class="show-all">Show all</a>
      </div>
    </div>
  </div>

  <div class="divider"></div>

  <div class="software-section">
    <h2>Software</h2>
    <div class="tags">
      <span class="tag">Microsoft Excel</span>
      <span class="tag">Power BI</span>
      <span class="tag">Microsoft Copilot</span>
      <span class="tag">ChatGPT</span>
      <span class="tag">SAP ERP</span>
      <span class="tag">LinkedIn</span>
      <span class="tag">Salesforce</span>
      <span class="tag">PowerPoint</span>
      <span class="tag">SharePoint</span>
      <span class="tag">Outlook</span>
      <span class="tag">Microsoft Project</span>
      <span class="tag">Microsoft Teams</span>
      <span class="tag">Microsoft Word</span>
      <span class="tag">Microsoft 365</span>
      <span class="tag">Google Analytics</span>
      <span class="tag">Google Workspace</span>
      <span class="tag">Dynamics</span>
      <span class="tag">Microsoft Access</span>
    </div>
  </div>
</body>
</html>
