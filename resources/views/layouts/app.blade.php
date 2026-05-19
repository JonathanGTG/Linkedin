<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LinkedIn Learning' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                linkedin: { blue: '#0a66c2', dark: '#004182', light: '#e8f3ff', bg: '#f3f2ee' }
            }}}
        }

        if (localStorage.getItem('learningSidebar') === 'expanded') {
            document.documentElement.classList.add('sidebar-expanded');
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif; }

        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            height: 82px;
            background: #fff;
            border-bottom: 1px solid #dedede;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 0 28px 0 34px;
        }
        .nav-left { display: flex; align-items: center; gap: 12px; min-width: 300px; }
        .sidebar-toggle {
            width: 46px;
            height: 46px;
            border: 0;
            border-radius: 4px;
            background: transparent;
            color: #3f3f3f;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s;
        }
        .sidebar-toggle:hover { background: #f3f2ee; color: #1d1d1d; }
        .sidebar-toggle svg { width: 31px; height: 31px; }
        .li-logo {
            width: 39px;
            height: 39px;
            border-radius: 6px;
            background: #0a66c2;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            font-weight: 700;
            line-height: 1;
            text-decoration: none;
        }
        .nav-brand {
            color: #0a66c2;
            font-size: 31px;
            line-height: 1;
            font-weight: 400;
            letter-spacing: 0;
            text-decoration: none;
        }
        .nav-search { flex: 1; max-width: 520px; position: relative; }
        .nav-search input {
            width: 100%;
            height: 40px;
            padding: 0 16px 0 40px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            color: #555;
            background: #fff;
            outline: none;
            font-family: inherit;
            transition: border-color .15s, box-shadow .15s;
        }
        .nav-search input:focus { border-color: #0a66c2; box-shadow: 0 0 0 2px rgba(10,102,194,.15); }
        .search-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
            pointer-events: none;
        }
        .nav-right { display: flex; align-items: center; gap: 18px; flex-shrink: 0; }
        .nav-user-name { font-size: 14px; font-weight: 500; color: #1d1d1d; }
        .nav-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #0a66c2;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }
        .btn-logout {
            font-size: 13px;
            font-weight: 500;
            color: #555;
            border: 1.5px solid #ccc;
            border-radius: 20px;
            padding: 5px 16px;
            cursor: pointer;
            background: transparent;
            font-family: inherit;
            transition: background .15s, color .15s;
        }
        .btn-logout:hover { background: #f3f2ee; color: #1d1d1d; }

        .page-wrapper {
            display: flex;
            min-height: 100vh;
            padding-top: 82px;
            background: #f3f2ee;
        }
        .sidebar {
            position: fixed;
            top: 82px;
            left: 0;
            bottom: 0;
            z-index: 40;
            width: 108px;
            min-height: calc(100vh - 82px);
            background: #fff;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 28px 0 18px;
            transition: width .22s ease, transform .22s ease;
        }
        html.sidebar-expanded .sidebar { width: 280px; }
        .sidebar-menu { display: flex; flex-direction: column; }
        .nav-item,
        .nav-help {
            min-height: 72px;
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 0 28px 0 35px;
            border-left: 5px solid transparent;
            color: #424242;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            transition: background .15s, color .15s, padding .22s ease;
        }
        html.sidebar-expanded .nav-item,
        html.sidebar-expanded .nav-help { padding-left: 24px; }
        .nav-item:hover,
        .nav-help:hover { background: #f3f2ee; color: #1d1d1d; }
        .nav-item.active {
            background: #eee;
            border-left-color: #3f3f3f;
            color: #1d1d1d;
        }
        .nav-icon {
            width: 39px;
            min-width: 39px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nav-icon svg {
            width: 31px;
            height: 31px;
            display: block;
            color: currentColor;
        }
        .nav-label {
            opacity: 0;
            transform: translateX(-8px);
            pointer-events: none;
            transition: opacity .16s ease, transform .16s ease;
        }
        html.sidebar-expanded .nav-label {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }
        .nav-section {
            height: 0;
            overflow: hidden;
            opacity: 0;
            padding: 0 24px;
            color: #888;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: uppercase;
            transition: opacity .16s ease;
        }
        html.sidebar-expanded .nav-section {
            height: auto;
            opacity: 1;
            padding: 18px 24px 6px 47px;
        }
        .nav-divider { height: 1px; background: #e0e0e0; margin: 10px 20px; }
        .nav-topic {
            display: block;
            height: 0;
            overflow: hidden;
            opacity: 0;
            padding: 0 24px;
            color: #1d1d1d;
            font-size: 14px;
            text-decoration: none;
            transition: background .15s, opacity .16s ease;
        }
        html.sidebar-expanded .nav-topic {
            height: auto;
            opacity: 1;
            padding: 8px 24px 8px 47px;
        }
        .nav-topic:hover { background: #f3f2ee; }
        .nav-help { margin-top: auto; }
        .help-icon {
            width: 31px;
            height: 31px;
            border-radius: 50%;
            background: #4a4a4a;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .main-content {
            flex: 1;
            min-height: calc(100vh - 82px);
            margin-left: 108px;
            padding: 28px 32px;
            transition: margin-left .22s ease;
        }
        html.sidebar-expanded .main-content { margin-left: 280px; }

        .carousel-scroll { display:flex; gap:1rem; overflow-x:auto; scrollbar-width:thin; padding-bottom:.5rem; }
        .carousel-scroll::-webkit-scrollbar { height:4px; }
        .carousel-scroll > * { flex:0 0 220px; }
        .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

        @media (max-width: 768px) {
            .top-nav { height: 72px; padding: 0 12px; gap: 10px; }
            .nav-left { min-width: 0; gap: 8px; }
            .sidebar-toggle { width: 42px; height: 42px; }
            .li-logo { width: 34px; height: 34px; font-size: 21px; }
            .nav-brand { font-size: 24px; }
            .nav-search { display: none; }
            .nav-user-name { display: none; }
            .btn-logout { padding: 5px 12px; }
            .page-wrapper { padding-top: 72px; }
            .sidebar {
                top: 72px;
                width: 260px;
                min-height: calc(100vh - 72px);
                transform: translateX(-100%);
            }
            html.sidebar-expanded .sidebar { width: 260px; transform: translateX(0); }
            .nav-label,
            html.sidebar-expanded .nav-label {
                opacity: 1;
                transform: none;
                pointer-events: auto;
            }
            .nav-section,
            html.sidebar-expanded .nav-section,
            .nav-topic,
            html.sidebar-expanded .nav-topic {
                height: auto;
                opacity: 1;
            }
            .main-content,
            html.sidebar-expanded .main-content {
                margin-left: 0;
                padding: 20px 16px;
            }
        }
    </style>
</head>
<body>
<nav class="top-nav">
    <div class="nav-left">
        <button class="sidebar-toggle" type="button" aria-label="Toggle sidebar" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="square">
                <line x1="4" y1="6" x2="20" y2="6"/>
                <line x1="4" y1="12" x2="20" y2="12"/>
                <line x1="4" y1="18" x2="20" y2="18"/>
            </svg>
        </button>
        <a href="{{ route('home') }}" class="li-logo">in</a>
        <a href="{{ route('home') }}" class="nav-brand">Learning</a>
    </div>

    <div class="nav-search">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <form action="{{ route('browse') }}" method="GET" style="margin:0">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari keahlian, subjek, atau perangkat lunak">
        </form>
    </div>

    <div class="nav-right">
        <span class="nav-user-name">{{ auth()->user()->name }}</span>
        <div class="nav-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</nav>

<div class="page-wrapper">
    <aside class="sidebar" aria-label="Main navigation">
        <div class="sidebar-menu">
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}" title="Home">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </span>
                <span class="nav-label">Home</span>
            </a>

            <a href="{{ route('journey.index') }}" class="nav-item {{ request()->routeIs('journey*') ? 'active' : '' }}" title="My Career Journey">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 5.5 10 3v15.5L4 21V5.5Zm10 0L20 3v15.5L14 21V5.5ZM11 3.3l2 2.1v15.3l-2-2.1V3.3Z"/>
                    </svg>
                </span>
                <span class="nav-label">My Career Journey</span>
            </a>

            <a href="{{ route('library') }}" class="nav-item {{ request()->routeIs('library*') ? 'active' : '' }}" title="My Library">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 3H7a2 2 0 0 0-2 2v16l7-3 7 3V5a2 2 0 0 0-2-2z"/>
                    </svg>
                </span>
                <span class="nav-label">My Library</span>
            </a>

            <a href="{{ route('browse') }}" class="nav-item {{ request()->routeIs('browse*') ? 'active' : '' }}" title="Content">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round">
                        <line x1="10" y1="6" x2="21" y2="6"/>
                        <line x1="10" y1="12" x2="21" y2="12"/>
                        <line x1="10" y1="18" x2="21" y2="18"/>
                        <circle cx="4.5" cy="6" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="4.5" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="4.5" cy="18" r="1.5" fill="currentColor" stroke="none"/>
                    </svg>
                </span>
                <span class="nav-label">Content</span>
            </a>

            <a href="{{ route('hands-on.index') }}" class="nav-item {{ request()->routeIs('hands-on*') ? 'active' : '' }}" title="Hands-On Tech">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 3H7a2 2 0 0 0-2 2v5a2 2 0 0 1-2 2 2 2 0 0 1 2 2v5a2 2 0 0 0 2 2h1"/>
                        <path d="M16 3h1a2 2 0 0 1 2 2v5a2 2 0 0 0 2 2 2 2 0 0 0-2 2v5a2 2 0 0 1-2 2h-1"/>
                    </svg>
                </span>
                <span class="nav-label">Hands-On Tech</span>
            </a>

            <a href="{{ route('certifications.index') }}" class="nav-item {{ request()->routeIs('certifications*') ? 'active' : '' }}" title="Certifications">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="14" rx="1.5"/>
                        <line x1="7" y1="10" x2="14" y2="10"/>
                        <line x1="7" y1="14" x2="12" y2="14"/>
                        <path d="M17 14.5 18.2 16 20 13.5"/>
                    </svg>
                </span>
                <span class="nav-label">Certifications</span>
            </a>
        </div>

        <a href="#" class="nav-help" title="Help">
            <span class="nav-icon"><span class="help-icon">?</span></span>
            <span class="nav-label">Help</span>
        </a>
    </aside>

    <main class="main-content">
        @if(session('success'))
            <div style="margin-bottom:16px;background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 16px;border-radius:6px;font-size:14px;">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
<script>
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const syncSidebarState = () => {
        const expanded = document.documentElement.classList.contains('sidebar-expanded');
        sidebarToggle?.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        localStorage.setItem('learningSidebar', expanded ? 'expanded' : 'collapsed');
    };

    sidebarToggle?.addEventListener('click', () => {
        document.documentElement.classList.toggle('sidebar-expanded');
        syncSidebarState();
    });

    syncSidebarState();
</script>
</body>
</html>
