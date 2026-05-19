<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LinkedIn Learning - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --blue: #0A66C2;
      --blue-dark: #004182;
      --blue-light: #AED8F7;
      --text: #1D2226;
      --muted: #56687A;
      --border: #B3C0CA;
      --error: #C0392B;
    }

    html, body {
      min-height: 100%;
      font-family: 'DM Sans', sans-serif;
      background: #fff;
      color: var(--text);
    }

    @keyframes fadeDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(22px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes scaleIn {
      from { opacity: 0; transform: scale(.96); }
      to { opacity: 1; transform: scale(1); }
    }
    @keyframes shimmer {
      from { background-position: -300% center; }
      to { background-position: 300% center; }
    }

    .page {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 60px 24px 80px;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      margin-bottom: 44px;
      animation: fadeDown .55s cubic-bezier(.22,1,.36,1) .05s both;
      transition: opacity .2s;
    }
    .logo:hover { opacity: .85; }

    .logo-badge {
      width: 36px;
      height: 36px;
      background: var(--blue);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: transform .2s;
    }
    .logo:hover .logo-badge { transform: scale(1.06); }
    .logo-badge span {
      color: #fff;
      font-weight: 900;
      font-size: 1.15rem;
      line-height: 1;
    }

    .logo-text { display: flex; align-items: baseline; gap: 4px; }
    .logo-text strong { font-weight: 900; color: var(--blue); font-size: 1.2rem; }
    .logo-text span { font-weight: 300; color: var(--blue); font-size: 1.2rem; }

    .card {
      width: 100%;
      max-width: 480px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 22px;
    }

    h1 {
      font-family: 'Source Serif 4', Georgia, serif;
      font-weight: 600;
      font-size: clamp(1.9rem, 5vw, 2.2rem);
      color: var(--text);
      text-align: center;
      line-height: 1.2;
      animation: fadeUp .55s cubic-bezier(.22,1,.36,1) .15s both;
    }

    .subtitle {
      font-size: .95rem;
      color: var(--muted);
      text-align: center;
      line-height: 1.65;
      max-width: 420px;
      animation: fadeUp .55s cubic-bezier(.22,1,.36,1) .28s both;
    }

    .error-box {
      width: 100%;
      background: #FEF2F2;
      border: 1px solid #FECACA;
      color: var(--error);
      font-size: .82rem;
      border-radius: 6px;
      padding: 12px 16px;
      line-height: 1.6;
      animation: scaleIn .35s ease both;
    }

    form {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .field {
      position: relative;
      width: 100%;
      animation: fadeUp .55s cubic-bezier(.22,1,.36,1) both;
    }
    form > .field:nth-of-type(1) { animation-delay: .38s; }

    .field input {
      width: 100%;
      border: 1.5px solid var(--border);
      border-radius: 4px;
      padding: 20px 16px 8px;
      font-size: .98rem;
      font-family: 'DM Sans', sans-serif;
      color: var(--text);
      background: #fff;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
      caret-color: var(--blue);
    }
    .field input:focus {
      border-color: var(--blue);
      box-shadow: 0 0 0 3px rgba(10,102,194,.12);
    }
    .field input.has-error {
      border-color: var(--error);
      box-shadow: 0 0 0 3px rgba(192,57,43,.1);
    }

    .field label {
      position: absolute;
      top: 14px;
      left: 14px;
      font-size: .94rem;
      color: #8B9AA8;
      font-family: 'DM Sans', sans-serif;
      pointer-events: none;
      transform-origin: left top;
      transition: top .18s, font-size .18s, color .18s;
    }
    .field input:focus + label,
    .field input:not(:placeholder-shown) + label {
      top: 6px;
      font-size: .7rem;
      color: var(--blue);
    }

    .field-error {
      font-size: .78rem;
      color: var(--error);
      margin-top: 4px;
      padding-left: 2px;
    }

    .field-extras {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      margin-top: 6px;
    }
    .toggle-pw, .forgot-link {
      font-size: .78rem;
      color: var(--blue);
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: color .15s;
      background: none;
      border: none;
      font-family: 'DM Sans', sans-serif;
      letter-spacing: .03em;
    }
    .toggle-pw:hover, .forgot-link:hover {
      color: var(--blue-dark);
      text-decoration: underline;
    }

    .password-row {
      overflow: hidden;
      max-height: 0;
      opacity: 0;
      transition: max-height .35s cubic-bezier(.22,1,.36,1), opacity .3s ease;
    }
    .password-row.visible {
      max-height: 170px;
      opacity: 1;
    }

    .btn-submit-wrap {
      width: 100%;
      animation: fadeUp .55s cubic-bezier(.22,1,.36,1) .65s both;
    }

    .btn-submit {
      width: 100%;
      padding: 15px 0;
      background: var(--blue-light);
      color: #fff;
      font-family: 'DM Sans', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      border: none;
      border-radius: 4px;
      cursor: default;
      position: relative;
      overflow: hidden;
      transition: background .22s, box-shadow .22s, transform .15s;
      letter-spacing: .01em;
    }
    .btn-submit.active {
      background: var(--blue);
      cursor: pointer;
    }
    .btn-submit.active::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(105deg, transparent 30%, rgba(255,255,255,.28) 50%, transparent 70%);
      background-size: 300% 100%;
      opacity: 0;
      transition: opacity .25s;
    }
    .btn-submit.active:hover::after {
      opacity: 1;
      animation: shimmer .5s linear;
    }
    .btn-submit.active:hover {
      background: var(--blue-dark);
      box-shadow: 0 5px 20px rgba(10,102,194,.3);
      transform: translateY(-1px);
    }
    .btn-submit.active:active {
      transform: translateY(0);
      box-shadow: none;
    }

    .divider {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 14px;
      animation: fadeUp .55s cubic-bezier(.22,1,.36,1) .78s both;
    }
    .divider-line { flex: 1; height: 1px; background: var(--border); }
    .divider span {
      font-size: .72rem;
      font-weight: 500;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: .1em;
    }

    .back-link {
      font-size: .9rem;
      color: var(--blue);
      font-weight: 500;
      text-decoration: none;
      transition: color .15s, text-decoration .15s;
      animation: fadeUp .55s cubic-bezier(.22,1,.36,1) .88s both;
    }
    .back-link:hover {
      color: var(--blue-dark);
      text-decoration: underline;
    }

    @media (max-width: 520px) {
      .page { padding: 40px 16px 60px; }
      h1 { font-size: 1.75rem; }
    }
  </style>
</head>
<body>
  <div class="page">
    <a href="{{ url('/') }}" class="logo">
      <div class="logo-badge"><span>in</span></div>
      <div class="logo-text">
        <strong>LinkedIn</strong>
        <span>Learning</span>
      </div>
    </a>

    <div class="card">
      <h1>Login</h1>

      <p class="subtitle">
        Login menggunakan alamat email yang Anda gunakan untuk
        Linkedin.com atau email organisasi Anda
      </p>

      @if($errors->any())
        <div class="error-box">{{ $errors->first() }}</div>
      @endif

      @if(session('status'))
        <div class="error-box" style="background:#EFF6FF;border-color:#BFDBFE;color:#0A66C2;">
          {{ session('status') }}
        </div>
      @endif

      <form id="loginForm" action="{{ route('login') }}" method="POST" novalidate>
        @csrf

        <div class="field">
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder=" "
            autocomplete="email"
            class="@error('email') has-error @enderror"
            required
            autofocus
          />
          <label for="email">Email</label>
          @error('email')
            <p class="field-error">{{ $message }}</p>
          @else
            <p class="field-error" id="emailError" style="display:none;"></p>
          @enderror
        </div>

        <div class="password-row" id="passwordRow">
          <div class="field" style="animation:none;">
            <input
              type="password"
              id="password"
              name="password"
              placeholder=" "
              autocomplete="current-password"
              class="@error('password') has-error @enderror"
            />
            <label for="password">Password</label>
            @error('password')
              <p class="field-error">{{ $message }}</p>
            @else
              <p class="field-error" id="passwordError" style="display:none;"></p>
            @enderror
          </div>
          <div class="field-extras">
            <button type="button" class="toggle-pw" id="togglePw">SHOW</button>
            &nbsp;&nbsp;
            <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
          </div>
        </div>

        <div class="btn-submit-wrap">
          <button type="submit" id="submitBtn" class="btn-submit" disabled>
            Lanjutkan
          </button>
        </div>
      </form>

      <div class="divider">
        <div class="divider-line"></div>
        <span>atau</span>
        <div class="divider-line"></div>
      </div>

      <a href="{{ route('register') }}" class="back-link">Buat akun baru</a>
    </div>
  </div>

  <script>
    const emailInput = document.getElementById('email');
    const passwordRow = document.getElementById('passwordRow');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');
    const togglePwBtn = document.getElementById('togglePw');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const loginForm = document.getElementById('loginForm');

    function isValidEmail(value) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());
    }

    function updateState() {
      const emailOk = isValidEmail(emailInput.value);

      passwordRow.classList.toggle('visible', emailOk);
      passwordInput.required = emailOk;

      submitBtn.disabled = !emailOk;
      submitBtn.classList.toggle('active', emailOk);
    }

    emailInput.addEventListener('input', updateState);

    togglePwBtn.addEventListener('click', function () {
      const isHidden = passwordInput.type === 'password';
      passwordInput.type = isHidden ? 'text' : 'password';
      togglePwBtn.textContent = isHidden ? 'HIDE' : 'SHOW';
    });

    emailInput.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' && passwordRow.classList.contains('visible')) {
        event.preventDefault();
        passwordInput.focus();
      }
    });

    loginForm.addEventListener('submit', function (event) {
      let valid = true;

      if (!isValidEmail(emailInput.value)) {
        event.preventDefault();
        if (emailError) {
          emailError.textContent = 'Masukkan email yang valid.';
          emailError.style.display = 'block';
        }
        emailInput.classList.add('has-error');
        valid = false;
      }

      if (passwordRow.classList.contains('visible') && passwordInput.value.length === 0) {
        event.preventDefault();
        if (passwordError) {
          passwordError.textContent = 'Password wajib diisi.';
          passwordError.style.display = 'block';
        }
        passwordInput.classList.add('has-error');
        valid = false;
      }

      if (valid) {
        submitBtn.textContent = 'Memproses...';
      }
    });

    emailInput.addEventListener('input', function () {
      if (emailError) emailError.style.display = 'none';
      emailInput.classList.remove('has-error');
    });

    passwordInput.addEventListener('input', function () {
      if (passwordError) passwordError.style.display = 'none';
      passwordInput.classList.remove('has-error');
    });

    updateState();
  </script>
</body>
</html>
