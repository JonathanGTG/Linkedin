<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LinkedIn Learning - Welcome</title>
  <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --blue: #0A66C2;
      --blue-dark: #004182;
      --blue-light: #70B5F9;
      --text: #1D2226;
      --muted: #56687A;
      --border: #C3CDD5;
      --bg: #FFFFFF;
    }

    html, body {
      height: 100%;
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      overflow-x: hidden;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(28px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideRight {
      from { opacity: 0; transform: translateX(60px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-12px); }
    }

    @keyframes paperFlutter {
      0%, 100% { transform: rotate(-1deg) translateY(0); }
      50% { transform: rotate(1.5deg) translateY(-7px); }
    }

    @keyframes shimmer {
      from { background-position: -300% center; }
      to { background-position: 300% center; }
    }

    nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 18px 64px;
      animation: fadeIn 0.5s ease 0.05s both;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      transition: opacity 0.2s;
    }

    .logo:hover { opacity: 0.85; }

    .logo-badge {
      width: 36px;
      height: 36px;
      background: var(--blue);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: transform 0.2s;
    }

    .logo:hover .logo-badge { transform: scale(1.06); }

    .logo-badge span {
      color: #fff;
      font-weight: 900;
      font-size: 1.15rem;
      line-height: 1;
      font-family: 'DM Sans', sans-serif;
    }

    .logo-text {
      display: flex;
      align-items: baseline;
      gap: 4px;
    }

    .logo-text strong,
    .logo-text span {
      color: var(--blue);
      font-size: 1.2rem;
      letter-spacing: 0;
    }

    .logo-text strong { font-weight: 900; }
    .logo-text span { font-weight: 300; }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .btn-signin,
    .btn-join-nav,
    .btn-join-hero {
      font-family: 'DM Sans', sans-serif;
      cursor: pointer;
      text-decoration: none;
      white-space: nowrap;
    }

    .btn-signin {
      border: 1.5px solid var(--blue);
      color: var(--blue);
      background: transparent;
      font-weight: 600;
      font-size: 0.9rem;
      border-radius: 9999px;
      padding: 9px 22px;
      transition: background 0.18s, box-shadow 0.18s, transform 0.15s;
    }

    .btn-signin:hover {
      background: #EBF4FF;
      box-shadow: 0 2px 14px rgba(10,102,194,0.15);
      transform: translateY(-1px);
    }

    .btn-join-nav {
      background: var(--blue);
      color: #fff;
      font-weight: 700;
      font-size: 0.9rem;
      border: none;
      border-radius: 9999px;
      padding: 9px 22px;
      transition: background 0.18s, box-shadow 0.18s, transform 0.15s;
    }

    .btn-join-nav:hover,
    .btn-join-hero:hover {
      background: var(--blue-dark);
      box-shadow: 0 4px 20px rgba(10,102,194,0.35);
      transform: translateY(-2px);
    }

    main {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 40px;
      padding: 40px 64px 80px;
      max-width: 1280px;
      margin: 0 auto;
      min-height: calc(100vh - 80px);
    }

    .hero-copy {
      flex: 0 0 auto;
      max-width: 440px;
      display: flex;
      flex-direction: column;
      gap: 32px;
    }

    h1 {
      font-family: 'Source Serif 4', Georgia, serif;
      font-weight: 700;
      font-size: clamp(1.9rem, 3.2vw, 2.75rem);
      line-height: 1.18;
      color: var(--text);
      animation: fadeUp 0.7s cubic-bezier(0.22,1,0.36,1) 0.1s both;
    }

    .btn-join-hero {
      display: block;
      width: 100%;
      max-width: 380px;
      background: var(--blue);
      color: #fff;
      font-weight: 700;
      font-size: 1rem;
      text-align: center;
      border: none;
      border-radius: 9999px;
      padding: 16px 0;
      position: relative;
      overflow: hidden;
      transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
      animation: fadeUp 0.7s cubic-bezier(0.22,1,0.36,1) 0.25s both;
    }

    .btn-join-hero::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,0.26) 52%, transparent 68%);
      background-size: 300% 100%;
      opacity: 0;
      transition: opacity 0.25s;
    }

    .btn-join-hero:hover::after {
      opacity: 1;
      animation: shimmer 0.5s linear;
    }

    .legal {
      font-size: 0.78rem;
      color: var(--muted);
      line-height: 1.65;
      max-width: 340px;
      animation: fadeUp 0.7s cubic-bezier(0.22,1,0.36,1) 0.4s both;
    }

    .legal a {
      color: var(--blue);
      text-decoration: none;
    }

    .legal a:hover { text-decoration: underline; }

    .hero-illo {
      flex: 1;
      display: flex;
      justify-content: flex-end;
      position: relative;
      animation: slideRight 0.85s cubic-bezier(0.22,1,0.36,1) 0.15s both;
    }

    .illo-wrap {
      position: relative;
      width: 100%;
      max-width: 580px;
    }

    .illo-float { animation: float 5s ease-in-out infinite; }

    .paper-card {
      position: absolute;
      top: -14px;
      right: -16px;
      width: 88px;
      background: white;
      border-radius: 4px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.13);
      padding: 10px 9px;
      display: flex;
      flex-direction: column;
      gap: 5px;
      animation: paperFlutter 4s ease-in-out infinite;
      z-index: 10;
    }

    .paper-line {
      height: 3px;
      background: #CBD5E1;
      border-radius: 2px;
    }

    .paper-line:nth-child(1) { width: 85%; }
    .paper-line:nth-child(2) { width: 70%; }
    .paper-line:nth-child(3) { width: 90%; }
    .paper-line:nth-child(4) { width: 60%; }

    .scene-svg {
      width: 100%;
      height: auto;
      display: block;
    }

    @media (max-width: 860px) {
      nav { padding: 16px 24px; }

      main {
        flex-direction: column;
        padding: 32px 24px 60px;
        min-height: auto;
      }

      .hero-copy {
        width: 100%;
        max-width: 520px;
      }

      .hero-illo { justify-content: center; }
      .illo-wrap { max-width: 420px; }
      .paper-card { display: none; }
    }

    @media (max-width: 520px) {
      nav {
        align-items: flex-start;
        gap: 18px;
      }

      .nav-actions {
        gap: 8px;
      }

      .btn-signin,
      .btn-join-nav {
        padding: 8px 14px;
        font-size: 0.82rem;
      }

      .logo-text strong,
      .logo-text span {
        font-size: 1rem;
      }
    }

    @media (max-width: 390px) {
      nav {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <nav>
    <a href="{{ url('/') }}" class="logo" aria-label="LinkedIn Learning">
      <div class="logo-badge"><span>in</span></div>
      <div class="logo-text">
        <strong>LinkedIn</strong>
        <span>Learning</span>
      </div>
    </a>
    <div class="nav-actions">
      <a href="{{ route('login') }}" class="btn-signin">Sign In</a>
      <a href="{{ route('register') }}" class="btn-join-nav">Join Now</a>
    </div>
  </nav>

  <main>
    <div class="hero-copy">
      <h1>Welcome to your<br>professional community</h1>

      <a href="{{ route('register') }}" class="btn-join-hero">Join Now</a>

      <p class="legal">
        By clicking Continue to join or sign in, you agree to
        LinkedIn's <a href="#">User Agreement</a>,
        <a href="#">Privacy Policy</a>,
        and <a href="#">Cookie Policy</a>.
      </p>
    </div>

    <div class="hero-illo">
      <div class="illo-wrap">
        <div class="paper-card" aria-hidden="true">
          <div class="paper-line"></div>
          <div class="paper-line"></div>
          <div class="paper-line"></div>
          <div class="paper-line"></div>
        </div>

        <div class="illo-float" aria-hidden="true">
          <svg class="scene-svg" viewBox="0 0 580 430" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <clipPath id="sceneClip"><circle cx="310" cy="215" r="195"/></clipPath>
              <linearGradient id="screenGlow" x1="0" x2="1" y1="0" y2="1">
                <stop stop-color="#70B5F9"/>
                <stop offset="1" stop-color="#0A66C2"/>
              </linearGradient>
            </defs>

            <circle cx="310" cy="215" r="195" fill="#F0E6D3"/>
            <ellipse cx="310" cy="155" rx="195" ry="135" fill="#C8DFF0" clip-path="url(#sceneClip)"/>
            <ellipse cx="228" cy="128" rx="38" ry="16" fill="#fff" opacity="0.85" clip-path="url(#sceneClip)"/>
            <ellipse cx="252" cy="120" rx="22" ry="13" fill="#fff" opacity="0.9" clip-path="url(#sceneClip)"/>
            <ellipse cx="342" cy="113" rx="28" ry="12" fill="#fff" opacity="0.8" clip-path="url(#sceneClip)"/>
            <rect x="115" y="295" width="390" height="130" fill="#E8D5BB" clip-path="url(#sceneClip)"/>

            <rect x="448" y="98" width="178" height="220" fill="#DDD0BB" clip-path="url(#sceneClip)"/>
            <rect x="448" y="98" width="178" height="6" fill="#C4B49A" clip-path="url(#sceneClip)"/>
            <rect x="448" y="173" width="178" height="5" fill="#C4B49A" clip-path="url(#sceneClip)"/>
            <rect x="448" y="245" width="178" height="5" fill="#C4B49A" clip-path="url(#sceneClip)"/>
            <circle cx="470" cy="146" r="18" fill="#3B82F6" clip-path="url(#sceneClip)"/>
            <ellipse cx="470" cy="146" rx="18" ry="5" fill="#1E40AF" opacity="0.28" clip-path="url(#sceneClip)"/>
            <line x1="452" y1="146" x2="488" y2="146" stroke="#1E40AF" stroke-width="1.2" clip-path="url(#sceneClip)"/>
            <line x1="470" y1="128" x2="470" y2="164" stroke="#1E40AF" stroke-width="1.2" clip-path="url(#sceneClip)"/>
            <rect x="496" y="113" width="14" height="60" rx="1" fill="#8B5CF6" clip-path="url(#sceneClip)"/>
            <rect x="510" y="116" width="12" height="57" rx="1" fill="#D97706" clip-path="url(#sceneClip)"/>
            <rect x="522" y="113" width="16" height="60" rx="1" fill="#059669" clip-path="url(#sceneClip)"/>
            <rect x="451" y="181" width="22" height="58" rx="1" fill="#6B7280" clip-path="url(#sceneClip)"/>
            <rect x="474" y="183" width="20" height="56" rx="1" fill="#3B82F6" clip-path="url(#sceneClip)"/>
            <rect x="534" y="181" width="10" height="59" rx="1" fill="#EF4444" clip-path="url(#sceneClip)"/>
            <rect x="544" y="181" width="8" height="59" rx="1" fill="#F59E0B" clip-path="url(#sceneClip)"/>
            <rect x="552" y="181" width="12" height="59" rx="1" fill="#10B981" clip-path="url(#sceneClip)"/>
            <rect x="452" y="252" width="42" height="35" rx="2" fill="#D4A96B" clip-path="url(#sceneClip)"/>
            <rect x="496" y="258" width="38" height="29" rx="2" fill="#D4A96B" clip-path="url(#sceneClip)"/>

            <ellipse cx="420" cy="382" rx="52" ry="14" fill="#8BA8B0" clip-path="url(#sceneClip)"/>
            <rect x="368" y="362" width="104" height="22" rx="8" fill="#A8C4CC" clip-path="url(#sceneClip)"/>
            <rect x="406" y="348" width="52" height="13" rx="2" fill="#1E293B" clip-path="url(#sceneClip)"/>
            <rect x="212" y="293" width="120" height="18" rx="6" fill="#B8C9CE" clip-path="url(#sceneClip)"/>
            <ellipse cx="272" cy="306" rx="62" ry="18" fill="#9EADB5" clip-path="url(#sceneClip)"/>
            <line x1="218" y1="311" x2="203" y2="372" stroke="#5E8289" stroke-width="5" stroke-linecap="round" clip-path="url(#sceneClip)"/>
            <line x1="326" y1="311" x2="341" y2="372" stroke="#5E8289" stroke-width="5" stroke-linecap="round" clip-path="url(#sceneClip)"/>

            <rect x="226" y="291" width="40" height="52" rx="8" fill="#4B5E50" clip-path="url(#sceneClip)"/>
            <rect x="269" y="291" width="32" height="52" rx="8" fill="#4B5E50" clip-path="url(#sceneClip)"/>
            <ellipse cx="240" cy="344" rx="22" ry="9" fill="#D4B899" clip-path="url(#sceneClip)"/>
            <ellipse cx="283" cy="344" rx="18" ry="8" fill="#D4B899" clip-path="url(#sceneClip)"/>
            <rect x="232" y="235" width="65" height="78" rx="20" fill="#1E4D5A" clip-path="url(#sceneClip)"/>
            <ellipse cx="267" cy="312" rx="48" ry="20" fill="#1E4D5A" clip-path="url(#sceneClip)"/>
            <path d="M285 270 Q312 282 322 297" stroke="#8B5E3C" stroke-width="14" stroke-linecap="round" fill="none" clip-path="url(#sceneClip)"/>
            <path d="M236 260 Q215 254 210 239 Q208 226 218 223" stroke="#8B5E3C" stroke-width="13" stroke-linecap="round" fill="none" clip-path="url(#sceneClip)"/>

            <rect x="297" y="283" width="96" height="62" rx="4" fill="#334155" clip-path="url(#sceneClip)"/>
            <rect x="301" y="287" width="88" height="54" rx="2" fill="#0F172A" clip-path="url(#sceneClip)"/>
            <rect x="303" y="289" width="84" height="50" rx="2" fill="url(#screenGlow)" opacity="0.35" clip-path="url(#sceneClip)"/>
            <rect x="310" y="298" width="52" height="4" rx="1" fill="#fff" opacity="0.5" clip-path="url(#sceneClip)"/>
            <rect x="310" y="308" width="38" height="4" rx="1" fill="#fff" opacity="0.35" clip-path="url(#sceneClip)"/>
            <rect x="310" y="318" width="56" height="4" rx="1" fill="#fff" opacity="0.35" clip-path="url(#sceneClip)"/>
            <rect x="287" y="345" width="116" height="7" rx="2" fill="#475569" clip-path="url(#sceneClip)"/>
            <rect x="282" y="352" width="126" height="4" rx="2" fill="#334155" clip-path="url(#sceneClip)"/>

            <circle cx="260" cy="217" r="34" fill="#8B5E3C" clip-path="url(#sceneClip)"/>
            <ellipse cx="260" cy="192" rx="34" ry="19" fill="#1A0A00" clip-path="url(#sceneClip)"/>
            <ellipse cx="260" cy="240" rx="18" ry="10" fill="#2D1200" opacity="0.42" clip-path="url(#sceneClip)"/>
            <circle cx="250" cy="221" r="8" fill="none" stroke="#1F2937" stroke-width="2" clip-path="url(#sceneClip)"/>
            <circle cx="270" cy="221" r="8" fill="none" stroke="#1F2937" stroke-width="2" clip-path="url(#sceneClip)"/>
            <line x1="258" y1="221" x2="262" y2="221" stroke="#1F2937" stroke-width="2" clip-path="url(#sceneClip)"/>
            <circle cx="228" cy="221" r="5" fill="#E5E7EB" clip-path="url(#sceneClip)"/>
            <ellipse cx="300" cy="388" rx="200" ry="22" fill="#D4C4A8" opacity="0.45" clip-path="url(#sceneClip)"/>
          </svg>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
