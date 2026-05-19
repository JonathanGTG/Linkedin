<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'LinkedIn Learning'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif; background: #f3f2ee; }

        /* ── NAVBAR ── */
        .nav {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 0 60px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .nav-left { display: flex; align-items: center; gap: 10px; }
        .li-logo {
            background: #0a66c2;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            width: 36px; height: 36px;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none;
        }
        .nav-brand {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #1d1d1d;
            text-decoration: none;
        }
        .nav-search {
            flex: 1;
            max-width: 560px;
            margin: 0 48px;
            position: relative;
        }
        .nav-search input {
            width: 100%;
            height: 40px;
            padding: 0 16px 0 40px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 15px;
            color: #555;
            background: #fff;
            outline: none;
            font-family: inherit;
            transition: border-color .15s;
        }
        .nav-search input:focus { border-color: #0a66c2; box-shadow: 0 0 0 2px rgba(10,102,194,.15); }
        .nav-search .search-icon {
            position: absolute;
            left: 11px; top: 50%;
            transform: translateY(-50%);
            color: #888;
            pointer-events: none;
        }
        .nav-right { display: flex; align-items: center; gap: 24px; }
        .nav-trial { font-size: 15px; font-weight: 500; color: #1d1d1d; cursor: pointer; text-decoration: none; }
        .nav-login {
            font-size: 15px;
            font-weight: 600;
            color: #0a66c2;
            border: 1.5px solid #0a66c2;
            border-radius: 24px;
            padding: 7px 24px;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s;
        }
        .nav-login:hover { background: #e8f3ff; }

        /* ── CONTENT AREA ── */
        .page-content { min-height: calc(100vh - 64px); }

        @media (max-width: 768px) {
            .nav { padding: 0 20px; }
            .nav-search { display: none; }
            .nav-trial { display: none; }
        }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav-left">
        <a href="/" class="li-logo">in</a>
        <a href="/" class="nav-brand">Learning</a>
    </div>

    <div class="nav-search">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="17" height="17"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" placeholder="Cari keahlian, subjek, atau perangkat lunak">
    </div>

    <div class="nav-right">
        <a href="<?php echo e(route('register')); ?>" class="nav-trial">Mulai uji coba gratis</a>
        <a href="<?php echo e(route('login')); ?>" class="nav-login">Login</a>
    </div>
</nav>

<div class="page-content">
    <?php echo e($slot); ?>

</div>

</body>
</html><?php /**PATH D:\LinkedinLearning\resources\views/layouts/guest.blade.php ENDPATH**/ ?>