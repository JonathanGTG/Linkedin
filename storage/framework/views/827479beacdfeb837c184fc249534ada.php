<?php
    use Illuminate\Support\Str;

    $user = auth()->user();
    $userName = $user->name ?? 'Learner';
    $userInitial = strtoupper(substr($userName, 0, 1));
    $level = $course->level ?: ($course->scraped_level ?: 'Beginner');
    $thumbnail = $course->thumbnail;
    $courseImage = null;

    if ($thumbnail) {
        $courseImage = Str::startsWith($thumbnail, ['http://', 'https://', '/'])
            ? $thumbnail
            : asset($thumbnail);
    }

    $rating = number_format((float) $course->display_rating, 1);
    $ratingCount = number_format((int) $course->display_rating_count);
    $learnerCount = number_format((int) $course->display_learner_count);
    $updatedDate = $course->release_date ? $course->release_date->format('F j, Y') : 'Recently updated';
    $projectFileCount = $course->includes()->count();
    $firstVideo = $course->videos->first();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title><?php echo e($course->title); ?> - LinkedIn Learning</title>

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: #fff;
    color: #1d1d1d;
  }

  .navbar {
    background: #1d1d1d;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .nav-left {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .hamburger {
    color: #fff;
    cursor: pointer;
    font-size: 18px;
    text-decoration: none;
  }

  .li-logo {
    background: #0a66c2;
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    width: 28px;
    height: 28px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
  }

  .nav-brand {
    color: #fff;
    font-size: 18px;
    font-weight: 400;
    text-decoration: none;
  }

  .nav-right {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-left: auto;
  }

  .nav-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #ccc;
    font-size: 11px;
    cursor: pointer;
    gap: 2px;
    position: relative;
  }

  .nav-icon svg {
    width: 20px;
    height: 20px;
  }

  .nav-icon:hover { color: #fff; }

  .avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #8b6914;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    overflow: hidden;
  }

  .profile-wrapper { position: relative; }

  .profile-dropdown {
    display: none;
    position: absolute;
    top: 56px;
    right: 0;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
    min-width: 260px;
    z-index: 999;
    overflow: hidden;
  }

  .profile-dropdown.show { display: block; }

  .pd-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 16px 14px;
    border-bottom: 1px solid #e0e0e0;
  }

  .pd-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #8b6914;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    font-weight: 700;
    flex-shrink: 0;
    overflow: hidden;
  }

  .pd-name {
    font-size: 16px;
    font-weight: 700;
    color: #1d1d1d;
    line-height: 1.3;
  }

  .pd-account-type {
    font-size: 12px;
    color: #666;
    font-weight: 400;
    margin-top: 2px;
  }

  .pd-section-label {
    padding: 10px 16px 4px;
    font-size: 12px;
    color: #666;
    font-weight: 400;
  }

  .pd-item {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 600;
    color: #1d1d1d;
    cursor: pointer;
    transition: background 0.15s;
    border: none;
    border-left: 3px solid transparent;
    background: none;
    text-align: left;
    width: 100%;
    line-height: 1.3;
    text-decoration: none;
    font-family: inherit;
  }

  .pd-item:hover { background: #f3f2ee; }

  .pd-item.active {
    background: #f3f2ee;
    border-left: 3px solid #1d1d1d;
  }

  .pd-item-sub {
    font-size: 12px;
    font-weight: 400;
    color: #666;
    margin-top: 2px;
  }

  .pd-divider {
    height: 1px;
    background: #e0e0e0;
    margin: 4px 0;
  }

  .lang-wrapper { position: relative; }

  .lang-dropdown {
    display: none;
    position: absolute;
    top: 48px;
    right: 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.18);
    min-width: 160px;
    z-index: 999;
    overflow: hidden;
  }

  .lang-dropdown.show { display: block; }

  .lang-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    font-size: 14px;
    color: #1d1d1d;
    cursor: pointer;
    transition: background 0.15s;
  }

  .lang-option:hover { background: #f3f2ee; }

  .lang-option.active {
    font-weight: 700;
    color: #0a66c2;
  }

  .lang-flag { font-size: 18px; }

  .notif-dot {
    position: absolute;
    top: 0;
    right: -2px;
    width: 8px;
    height: 8px;
    background: red;
    border-radius: 50%;
    border: 1px solid #1d1d1d;
  }

  .solutions-bar {
    background: #fff;
    border-bottom: 1px solid #e0e0e0;
    padding: 8px 60px;
    text-align: right;
    font-size: 13px;
    color: #555;
  }

  .solutions-bar a {
    color: #1d1d1d;
    font-weight: 600;
    text-decoration: none;
    margin-left: 6px;
    cursor: pointer;
  }

  .solutions-bar a:hover { text-decoration: underline; }

  .solutions-bar span {
    color: #ccc;
    margin-left: 6px;
  }

  .flash-message {
    max-width: 1200px;
    margin: 12px auto 0;
    background: #dcfce7;
    border: 1px solid #86efac;
    color: #166534;
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 14px;
  }

  .flash-message.error {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
  }

  .hero-banner {
    position: relative;
    width: 100%;
    height: 420px;
    overflow: hidden;
    background: #1a1a2e;
  }

  .hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center right;
    opacity: 0.75;
    z-index: 0;
    pointer-events: none;
  }

  .hero-bg-fallback {
    position: absolute;
    inset: 0;
    background:
      radial-gradient(circle at 78% 24%, rgba(79, 141, 199, .55), transparent 28%),
      linear-gradient(135deg, #172039 0%, #101726 48%, #233f60 100%);
    z-index: 0;
    pointer-events: none;
  }

  .hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      to right,
      rgba(20, 20, 40, 0.92) 0%,
      rgba(20, 20, 40, 0.75) 50%,
      rgba(20, 20, 40, 0.15) 100%
    );
    z-index: 1;
    pointer-events: none;
  }

  .hero-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    height: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 60px;
    gap: 40px;
  }

  .hero-left {
    flex: 1;
    max-width: 680px;
  }

  .hero-title {
    font-size: clamp(1.6rem, 3vw, 2.4rem);
    font-weight: 800;
    color: #fff;
    line-height: 1.25;
    margin-bottom: 20px;
    letter-spacing: 0;
  }

  .hero-meta {
    font-size: 14px;
    color: #ccc;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  .hero-meta .dot { color: #888; }

  .hero-rating {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #ccc;
    margin-bottom: 28px;
    flex-wrap: wrap;
  }

  .hero-rating .score {
    color: #e7a83e;
    font-weight: 700;
  }

  .hero-stars {
    color: #e7a83e;
    font-size: 16px;
    letter-spacing: 1px;
  }

  .hero-rating .reviews { color: #aaa; }

  .hero-buttons {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    align-items: center;
  }

  .hero-buttons form { margin: 0; }

  .btn-primary {
    background: #0a66c2;
    color: #fff;
    border: none;
    border-radius: 24px;
    padding: 13px 26px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s;
    white-space: nowrap;
    font-family: inherit;
  }

  .btn-primary:hover {
    background: #004182;
    transform: translateY(-1px);
  }

  .btn-outline {
    background: transparent;
    color: #fff;
    border: 2px solid #fff;
    border-radius: 24px;
    padding: 11px 26px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, transform 0.15s;
    white-space: nowrap;
    font-family: inherit;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  .btn-outline:hover {
    background: rgba(255,255,255,0.12);
    transform: translateY(-1px);
  }

  .enrolled-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
  }

  .status-dot {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #2e7d32;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
  }

  .hero-right {
    flex-shrink: 0;
    width: 320px;
  }

  .preview-card {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 8px;
    overflow: hidden;
    backdrop-filter: blur(6px);
  }

  .preview-thumb-placeholder {
    width: 100%;
    height: 180px;
    background: linear-gradient(135deg, #2a2a4a, #1a3a5c);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }

  .preview-thumb-placeholder img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .play-btn {
    width: 56px;
    height: 56px;
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, transform 0.2s;
    position: relative;
    z-index: 2;
  }

  .preview-thumb-placeholder:hover .play-btn {
    background: rgba(0,0,0,0.9);
    transform: scale(1.08);
  }

  .play-btn svg {
    width: 22px;
    height: 22px;
    fill: #fff;
    margin-left: 4px;
  }

  .preview-label {
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    text-align: center;
    letter-spacing: 0;
  }

  .main-layout {
    display: flex;
    max-width: 1200px;
    margin: 0 auto;
  }

  .reviews-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 40px 40px;
  }

  .content-area {
    flex: 1;
    padding: 28px 40px;
    border-right: 1px solid #e0e0e0;
  }

  .info-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #1d1d1d;
    margin-bottom: 12px;
  }

  .info-row svg {
    width: 18px;
    height: 18px;
    color: #555;
    flex-shrink: 0;
  }

  .show-all {
    color: #0a66c2;
    font-weight: 600;
    cursor: pointer;
  }

  .section-title {
    font-size: 17px;
    font-weight: 700;
    margin: 24px 0 12px;
  }

  .instructor-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 10px;
  }

  .instructor-card {
    display: flex;
    gap: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 12px;
    background: #fff;
  }

  .instructor-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #0a66c2;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    flex-shrink: 0;
    overflow: hidden;
  }

  .instructor-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .instructor-name {
    font-size: 14px;
    font-weight: 800;
    color: #1d1d1d;
    line-height: 1.3;
  }

  .instructor-info {
    font-size: 13px;
    color: #555;
    margin-top: 2px;
    line-height: 1.35;
  }

  .instructor-link {
    display: inline-block;
    margin-top: 6px;
    font-size: 13px;
    font-weight: 700;
    color: #0a66c2;
    text-decoration: none;
  }

  .instructor-link:hover { text-decoration: underline; }

  .reviews-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 12px;
  }

  .review-card {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 14px;
    background: #fff;
  }

  .review-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 8px;
  }

  .review-user {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
  }

  .review-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #8b6914;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    flex-shrink: 0;
    overflow: hidden;
  }

  .review-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .review-name {
    font-size: 13px;
    font-weight: 800;
    color: #1d1d1d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .review-date {
    font-size: 12px;
    color: #777;
    flex-shrink: 0;
  }

  .review-stars {
    font-size: 13px;
    color: #f59e0b;
    letter-spacing: 1px;
    flex-shrink: 0;
  }

  .review-body {
    font-size: 14px;
    color: #333;
    line-height: 1.6;
    white-space: pre-wrap;
  }

  .empty-text {
    font-size: 14px;
    color: #555;
  }

  .description {
    font-size: 14px;
    line-height: 1.75;
    color: #333;
  }

  .tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
  }

  .tag {
    border: 1px solid #ccc;
    border-radius: 20px;
    padding: 7px 16px;
    font-size: 13px;
    color: #1d1d1d;
    cursor: pointer;
  }

  .tag:hover {
    border-color: #0a66c2;
    color: #0a66c2;
  }

  .progress-box {
    border: 1px solid #d6e6f7;
    background: #f3f8fd;
    border-radius: 8px;
    padding: 14px 16px;
    margin-top: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
  }

  .progress-copy {
    font-size: 14px;
    color: #333;
    line-height: 1.45;
  }

  .progress-track {
    height: 8px;
    background: #d7e7f7;
    border-radius: 999px;
    overflow: hidden;
    flex: 1;
    min-width: 160px;
  }

  .progress-fill {
    height: 100%;
    background: #0a66c2;
    width: 0;
  }

  .certificate-box {
    display: flex;
    gap: 24px;
    margin-top: 16px;
    align-items: flex-start;
  }

  .cert-img {
    width: 140px;
    height: 100px;
    background: linear-gradient(135deg, #e8f4fd, #c8e6fa);
    border: 1px solid #ccc;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 11px;
    color: #555;
    text-align: center;
    padding: 8px;
  }

  .cert-info .cert-brand {
    font-size: 18px;
    font-weight: 700;
    color: #0a66c2;
    margin-bottom: 6px;
  }

  .cert-info .cert-brand span {
    background: #0a66c2;
    color: #fff;
    padding: 1px 4px;
    border-radius: 3px;
    font-size: 14px;
    margin: 0 2px;
  }

  .cert-info h4 {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 8px;
  }

  .cert-point {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 13px;
    color: #333;
    line-height: 1.5;
  }

  .cert-point svg {
    width: 16px;
    height: 16px;
    color: #0a66c2;
    flex-shrink: 0;
    margin-top: 2px;
  }

  .right-panel {
    width: 340px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
  }

  .share-btn {
    padding: 16px 20px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 600;
    color: #1d1d1d;
    cursor: pointer;
  }

  .share-btn:hover { background: #f3f2ee; }

  .chapters {
    overflow-y: auto;
    flex: 1;
  }

  .chapter { border-bottom: 1px solid #e0e0e0; }

  .chapter-header {
    padding: 14px 20px;
    font-size: 14px;
    font-weight: 700;
    color: #1d1d1d;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    background: #fff;
    transition: background 0.2s;
    gap: 12px;
  }

  .chapter-header:hover { background: #f7f7f7; }

  .chapter-header span {
    min-width: 0;
  }

  .chapter-header svg {
    width: 18px;
    height: 18px;
    transition: transform 0.3s;
    flex-shrink: 0;
  }

  .chapter.active .chapter-header svg { transform: rotate(180deg); }

  .chapter-content { display: none; }

  .chapter.active .chapter-content { display: block; }

  .lesson {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 20px;
    border-top: 1px solid #f0f0f0;
    cursor: pointer;
  }

  .lesson:hover { background: #f9f9f9; }

  .lesson-info {
    flex: 1;
    min-width: 0;
  }

  .lesson-title {
    font-size: 13px;
    color: #1d1d1d;
    line-height: 1.4;
    margin-bottom: 3px;
  }

  .lesson-duration {
    font-size: 12px;
    color: #666;
  }

  .lesson-badge {
    font-size: 11px;
    font-weight: 600;
    color: #0a66c2;
    border: 1px solid #0a66c2;
    border-radius: 3px;
    padding: 2px 6px;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .lesson-complete {
    font-size: 11px;
    font-weight: 600;
    color: #666;
    border: 1px solid #ccc;
    background: #fff;
    border-radius: 14px;
    padding: 3px 8px;
    white-space: nowrap;
    flex-shrink: 0;
    cursor: pointer;
    font-family: inherit;
  }

  .lesson-complete.done {
    color: #fff;
    border-color: #0a66c2;
    background: #0a66c2;
  }

  .lesson-check {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #eef3f8;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    flex-shrink: 0;
    margin-top: 1px;
  }

  .lesson.done .lesson-check {
    background: #0a66c2;
    color: #fff;
  }

  .rate-form {
    margin-top: 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 520px;
  }

  .rate-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .rate-label {
    font-size: 13px;
    font-weight: 600;
    color: #fff;
  }

  .rate-select {
    height: 36px;
    border-radius: 6px;
    border: 1px solid rgba(255,255,255,0.35);
    background: rgba(0,0,0,0.25);
    color: #fff;
    padding: 0 10px;
    font-family: inherit;
  }

  .rate-text {
    width: 100%;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.35);
    background: rgba(0,0,0,0.25);
    color: #fff;
    padding: 10px 12px;
    resize: vertical;
    font-family: inherit;
  }

  @media (max-width: 960px) {
    .hero-banner { height: auto; min-height: 460px; }
    .hero-content {
      padding: 42px 24px;
      flex-direction: column;
      align-items: flex-start;
    }
    .hero-right { width: min(100%, 360px); }
    .main-layout { flex-direction: column; }
    .content-area { border-right: 0; padding: 24px; }
    .right-panel { width: 100%; border-top: 1px solid #e0e0e0; }
    .solutions-bar { padding: 8px 16px; text-align: left; }
  }

  @media (max-width: 640px) {
    .nav-brand { display: none; }
    .nav-right { gap: 12px; }
    .nav-icon span { display: none; }
    .solutions-bar { display: none; }
    .hero-title { font-size: 1.55rem; }
    .hero-buttons { width: 100%; }
    .hero-buttons form,
    .hero-buttons button,
    .hero-buttons a { width: 100%; justify-content: center; }
    .certificate-box,
    .progress-box { flex-direction: column; }
    .progress-track { width: 100%; }
  }
</style>
</head>

<body>

<nav class="navbar">
  <div class="nav-left">
    <a href="<?php echo e(route('home')); ?>" class="hamburger" aria-label="Back to home">&#9776;</a>
    <a href="<?php echo e(route('home')); ?>" class="li-logo">in</a>
    <a href="<?php echo e(route('home')); ?>" class="nav-brand">Learning</a>
  </div>

  <div class="nav-right">
    <a class="nav-icon" href="<?php echo e(route('browse')); ?>" style="text-decoration:none;">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <span data-i18n="search">Search</span>
    </a>

    <div class="nav-icon profile-wrapper" onclick="toggleProfile(event)">
      <div class="avatar"><?php echo e($userInitial); ?></div>
      <span data-i18n="me">Me</span> &#9662;

      <div class="profile-dropdown" id="profileDropdown">
        <div class="pd-header">
          <div class="pd-avatar"><?php echo e($userInitial); ?></div>
          <div>
            <div class="pd-name"><?php echo e($userName); ?></div>
            <div class="pd-account-type">Personal account</div>
          </div>
        </div>

        <div class="pd-section-label">My Learning</div>
        <a href="<?php echo e(route('journey.index')); ?>" class="pd-item">My Career Plan</a>
        <a href="<?php echo e(route('library')); ?>" class="pd-item active">My Content</a>

        <div class="pd-divider"></div>

        <a href="<?php echo e(route('home')); ?>" class="pd-item">Home</a>
        <a href="<?php echo e(route('browse')); ?>" class="pd-item">Browse courses</a>

        <div class="pd-divider"></div>

        <a href="#" class="pd-item">
          Buy for my team
          <span class="pd-item-sub">20 people or less</span>
        </a>
        <a href="#" class="pd-item">
          Buy for your organization
          <span class="pd-item-sub">Contact sales</span>
        </a>

        <div class="pd-divider"></div>

        <a href="#" class="pd-item">Get Help</a>
        <form method="POST" action="<?php echo e(route('logout')); ?>" onclick="event.stopPropagation()" style="margin:0">
          <?php echo csrf_field(); ?>
          <button type="submit" class="pd-item">Sign out</button>
        </form>
      </div>
    </div>

    <div class="nav-icon lang-wrapper" onclick="toggleLang(event)">
      <div style="position:relative;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/>
          <line x1="2" y1="12" x2="22" y2="12"/>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
        <div class="notif-dot"></div>
      </div>
      <span id="currentLang">EN</span> &#9662;
      <div class="lang-dropdown" id="langDropdown">
        <div class="lang-option active" onclick="setLang('EN', this, event)">
          <span class="lang-flag">US</span> English
        </div>
        <div class="lang-option" onclick="setLang('ID', this, event)">
          <span class="lang-flag">ID</span> Indonesia
        </div>
      </div>
    </div>
  </div>
</nav>

<div class="solutions-bar">
  <span data-i18n="solutions">Solutions for:</span>
  <a data-i18n="business">Business</a>
  <a data-i18n="higher_edu">Higher Education</a>
  <a data-i18n="government">Government</a>
  <span>|</span>
  <a data-i18n="buy_team">Buy for my team</a>
</div>

<?php if(session('success')): ?>
  <div class="flash-message"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
  <div class="flash-message error"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="hero-banner">
  <?php if($courseImage): ?>
    <img src="<?php echo e($courseImage); ?>" alt="" class="hero-bg" onerror="this.style.display='none'" />
  <?php else: ?>
    <div class="hero-bg-fallback"></div>
  <?php endif; ?>

  <div class="hero-overlay"></div>

  <div class="hero-content">
    <div class="hero-left">
      <h1 class="hero-title"><?php echo e($course->title); ?></h1>

      <div class="hero-meta">
        <span><?php echo e($level); ?></span>
        <span class="dot">&bull;</span>
        <span><?php echo e($course->durasi_format); ?></span>
        <span class="dot">&bull;</span>
        <span data-i18n="hero_updated">Updated: <?php echo e($updatedDate); ?></span>
      </div>

      <div class="hero-rating">
        <span class="score"><?php echo e($rating); ?></span>
        <span class="hero-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
        <span class="reviews">(<?php echo e($ratingCount); ?>)</span>
        <span class="dot">&bull;</span>
        <span><?php echo e($learnerCount); ?> learners</span>
      </div>

      <div class="hero-buttons">
        <?php if($enrollment): ?>
          <span class="enrolled-status">
            <span class="status-dot">&#10003;</span>
            <?php echo e($enrollment->status === 'completed' ? 'Completed' : 'In your learning'); ?>

          </span>
          <a href="#chapters" class="btn-primary" data-i18n="btn_continue">Continue learning</a>
        <?php else: ?>
          <form method="POST" action="<?php echo e(route('library.enroll', $course)); ?>">
            <?php echo csrf_field(); ?>
            <button class="btn-primary" data-i18n="btn_free">Start learning</button>
          </form>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('library.save', $course)); ?>">
          <?php echo csrf_field(); ?>
          <button class="btn-outline" data-i18n="btn_save">Save</button>
        </form>
      </div>

      <?php if($enrollment && $enrollment->status === 'completed'): ?>
        <form method="POST" action="<?php echo e(route('course.rate', $course)); ?>" class="rate-form">
          <?php echo csrf_field(); ?>
          <div class="rate-row">
            <span class="rate-label">Your rating</span>
            <select name="rating" class="rate-select" required>
              <?php for($i = 5; $i >= 1; $i--): ?>
                <option value="<?php echo e($i); ?>" <?php echo e($userRating && (int) $userRating->rating === $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
              <?php endfor; ?>
            </select>
            <button class="btn-outline" type="submit"><?php echo e($userRating ? 'Update rating' : 'Submit rating'); ?></button>
          </div>
          <textarea name="review" class="rate-text" rows="3" placeholder="Optional review"><?php echo e($userRating->review ?? ''); ?></textarea>
        </form>
      <?php endif; ?>
    </div>

    <div class="hero-right">
      <div class="preview-card">
        <a class="preview-thumb-placeholder" href="#chapters" aria-label="Preview course">
          <?php if($courseImage): ?>
            <img src="<?php echo e($courseImage); ?>" alt="Preview" onerror="this.style.display='none'" />
          <?php endif; ?>
          <div class="play-btn">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M8 5v14l11-7z"/>
            </svg>
          </div>
        </a>
        <div class="preview-label" data-i18n="preview_label">&#9654; Preview</div>
      </div>
    </div>
  </div>
</div>

<div class="main-layout">
  <div class="content-area">
    <div class="info-row">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
      </svg>
      <span><?php echo e($projectFileCount > 0 ? $projectFileCount : 1); ?> <span data-i18n="project_file">project file</span></span>
    </div>

    <div class="info-row">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <rect x="5" y="2" width="14" height="20" rx="2"/>
        <line x1="12" y1="18" x2="12.01" y2="18"/>
      </svg>
      <span data-i18n="access_device">Access on tablet and phone</span>
    </div>

    <div class="info-row">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="8" r="7"/>
        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
      </svg>
      <span data-i18n="certificate">Certificate of completion</span>
      <span style="color:#999">&middot;</span>
      <span class="show-all" data-i18n="show_all">Show all</span>
    </div>

    <?php if($enrollment && $totalVideos > 0): ?>
      <div class="progress-box">
        <div class="progress-copy">
          <strong><?php echo e($progressPct); ?>% complete</strong><br>
          <?php echo e($completedCount); ?> of <?php echo e($totalVideos); ?> videos completed
        </div>
        <div class="progress-track" aria-label="Course progress">
          <div class="progress-fill" style="width: <?php echo e($progressPct); ?>%"></div>
        </div>
      </div>
    <?php endif; ?>

    <div class="section-title" data-i18n="course_desc">Course description</div>

    <p class="description">
      <?php echo e($course->description ?: 'Build practical skills through guided lessons, hands-on examples, and a structured learning path designed for real work.'); ?>

    </p>

    <div class="section-title" data-i18n="skills_title">Skills covered in this course</div>

    <div class="tags">
      <?php $__empty_1 = true; $__currentLoopData = $course->skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <span class="tag"><?php echo e($skill->name); ?></span>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <span class="tag"><?php echo e($course->category->name ?? 'Professional Development'); ?></span>
        <span class="tag">Workflow</span>
        <span class="tag">Learning</span>
      <?php endif; ?>
    </div>

    <div class="section-title" data-i18n="cert_title">Shareable certificate</div>

    <div class="certificate-box">
      <div class="cert-img">
        <div data-i18n="sample_cert">Sample Certificate</div>
      </div>
      <div class="cert-info">
        <div class="cert-brand">Linked<span>in</span> Learning</div>
        <h4 data-i18n="cert_completion">Certificate of Completion</h4>
        <div class="cert-point">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <span data-i18n="cert_desc">Showcase on your LinkedIn profile under the "Licenses and Certificates" section</span>
        </div>
      </div>
    </div>

    <div class="section-title" data-i18n="instructor_title">Instructor</div>

    <div class="instructor-list">
      <?php $__empty_1 = true; $__currentLoopData = $course->instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="instructor-card">
          <div class="instructor-avatar"><?php echo e(strtoupper(substr($instructor->name, 0, 1))); ?></div>
          <div>
            <div class="instructor-name"><?php echo e($instructor->name); ?></div>
            <?php if($instructor->info): ?>
              <div class="instructor-info"><?php echo e($instructor->info); ?></div>
            <?php endif; ?>
            <?php if($instructor->link): ?>
              <a class="instructor-link" href="<?php echo e($instructor->link); ?>" target="_blank" rel="noopener">View profile</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="instructor-card">
          <div class="instructor-avatar">
            <?php if($course->instructor_avatar): ?>
              <img src="<?php echo e($course->instructor_avatar); ?>" alt="" onerror="this.style.display='none'" />
            <?php else: ?>
              <?php echo e(strtoupper(substr($course->instructor_name, 0, 1))); ?>

            <?php endif; ?>
          </div>
          <div>
            <div class="instructor-name"><?php echo e($course->instructor_name); ?></div>
          </div>
        </div>
      <?php endif; ?>
    </div>

  </div>

  <div class="right-panel" id="chapters">
    <div class="share-btn">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18">
        <circle cx="18" cy="5" r="3"/>
        <circle cx="6" cy="12" r="3"/>
        <circle cx="18" cy="19" r="3"/>
        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
      </svg>
      <span data-i18n="share_course">Share this course</span>
    </div>

    <div class="chapters">
      <?php $__empty_1 = true; $__currentLoopData = $course->chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $chapterVideos = $chapter->videos;
          $chapterDuration = $chapterVideos->sum('durasi_detik');
          $chapterMinutes = max(1, intdiv($chapterDuration, 60));
        ?>
        <div class="chapter <?php echo e($loop->first ? 'active' : ''); ?>">
          <div class="chapter-header" onclick="toggleChapter(this)">
            <span><?php echo e($chapter->urutan); ?>. <?php echo e($chapter->judul); ?></span>
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </div>
          <div class="chapter-content">
            <?php $__currentLoopData = $chapterVideos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php $isDone = isset($videoProgress[$video->id]) && $videoProgress[$video->id]->is_completed; ?>
              <div class="lesson <?php echo e($isDone ? 'done' : ''); ?>" id="vrow-<?php echo e($video->id); ?>">
                <div class="lesson-check"><?php echo $isDone ? '&#10003;' : e($video->urutan); ?></div>
                <div class="lesson-info">
                  <div class="lesson-title"><?php echo e($video->title); ?></div>
                  <div class="lesson-duration"><?php echo e($video->durasi_format); ?></div>
                </div>
                <?php if($video->is_preview): ?>
                  <span class="lesson-badge" data-i18n="preview">Preview</span>
                <?php endif; ?>
                <?php if($enrollment): ?>
                  <button
                    class="lesson-complete <?php echo e($isDone ? 'done' : ''); ?>"
                    onclick="markDone(event, '<?php echo e($video->id); ?>', this)"
                    data-course="<?php echo e($course->id); ?>"
                    data-durasi="<?php echo e($video->durasi_detik); ?>">
                    <?php echo e($isDone ? 'Done' : 'Complete'); ?>

                  </button>
                <?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="chapter active">
          <div class="chapter-header" onclick="toggleChapter(this)">
            <span>1. Course videos</span>
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </div>
          <div class="chapter-content">
            <?php $__empty_1 = true; $__currentLoopData = $course->videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php $isDone = isset($videoProgress[$video->id]) && $videoProgress[$video->id]->is_completed; ?>
              <div class="lesson <?php echo e($isDone ? 'done' : ''); ?>" id="vrow-<?php echo e($video->id); ?>">
                <div class="lesson-check"><?php echo $isDone ? '&#10003;' : e($video->urutan); ?></div>
                <div class="lesson-info">
                  <div class="lesson-title"><?php echo e($video->title); ?></div>
                  <div class="lesson-duration"><?php echo e($video->durasi_format); ?></div>
                </div>
                <?php if($video->is_preview): ?>
                  <span class="lesson-badge" data-i18n="preview">Preview</span>
                <?php endif; ?>
                <?php if($enrollment): ?>
                  <button
                    class="lesson-complete <?php echo e($isDone ? 'done' : ''); ?>"
                    onclick="markDone(event, '<?php echo e($video->id); ?>', this)"
                    data-course="<?php echo e($course->id); ?>"
                    data-durasi="<?php echo e($video->durasi_detik); ?>">
                    <?php echo e($isDone ? 'Done' : 'Complete'); ?>

                  </button>
                <?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="lesson">
                <div class="lesson-info">
                  <div class="lesson-title">Course content will be available soon.</div>
                  <div class="lesson-duration">0:00</div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="reviews-section">
  <div class="section-title" data-i18n="reviews_title">Learner reviews</div>

  <?php if($comments->count() === 0): ?>
    <div class="empty-text" data-i18n="reviews_empty">No reviews yet.</div>
  <?php else: ?>
    <div class="reviews-list">
      <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $commentUser = $comment->user;
          $commentName = $commentUser->name ?? 'Learner';
          $commentInitial = strtoupper(substr($commentName, 0, 1));
        ?>
        <div class="review-card">
          <div class="review-header">
            <div class="review-user">
              <div class="review-avatar">
                <?php if($commentUser && $commentUser->avatar): ?>
                  <img src="<?php echo e(Str::startsWith($commentUser->avatar, ['http://', 'https://', '/']) ? $commentUser->avatar : asset($commentUser->avatar)); ?>" alt="" onerror="this.style.display='none'" />
                <?php else: ?>
                  <?php echo e($commentInitial); ?>

                <?php endif; ?>
              </div>
              <div style="min-width:0">
                <div class="review-name"><?php echo e($commentName); ?></div>
                <div class="review-stars">
                  <?php for($i = 1; $i <= 5; $i++): ?>
                    <?php echo $i <= (int) $comment->rating ? '&#9733;' : '&#9734;'; ?>

                  <?php endfor; ?>
                </div>
              </div>
            </div>
            <div class="review-date"><?php echo e($comment->created_at?->format('M j, Y')); ?></div>
          </div>
          <div class="review-body"><?php echo e($comment->review); ?></div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  <?php endif; ?>
</div>

<script>
  const translations = {
    EN: {
      search: "Search",
      me: "Me",
      solutions: "Solutions for:",
      business: "Business",
      higher_edu: "Higher Education",
      government: "Government",
      buy_team: "Buy for my team",
      project_file: "project file",
      access_device: "Access on tablet and phone",
      certificate: "Certificate of completion",
      show_all: "Show all",
      course_desc: "Course description",
      skills_title: "Skills covered in this course",
      cert_title: "Shareable certificate",
      sample_cert: "Sample Certificate",
      cert_completion: "Certificate of Completion",
      cert_desc: 'Showcase on your LinkedIn profile under the "Licenses and Certificates" section',
      share_course: "Share this course",
      preview: "Preview",
      btn_free: "Start learning",
      btn_save: "Save",
      btn_continue: "Continue learning",
      preview_label: "\u25B6 Preview",
      instructor_title: "Instructor",
      reviews_title: "Learner reviews",
      reviews_empty: "No reviews yet."
    },
    ID: {
      search: "Cari",
      me: "Saya",
      solutions: "Solusi untuk:",
      business: "Bisnis",
      higher_edu: "Pendidikan Tinggi",
      government: "Pemerintah",
      buy_team: "Beli untuk tim saya",
      project_file: "file proyek",
      access_device: "Akses di tablet dan ponsel",
      certificate: "Sertifikat penyelesaian",
      show_all: "Tampilkan semua",
      course_desc: "Deskripsi kursus",
      skills_title: "Keterampilan yang dicakup",
      cert_title: "Sertifikat yang dapat dibagikan",
      sample_cert: "Contoh Sertifikat",
      cert_completion: "Sertifikat Penyelesaian",
      cert_desc: 'Tampilkan di profil LinkedIn Anda pada bagian "Lisensi dan Sertifikat"',
      share_course: "Bagikan kursus ini",
      preview: "Pratinjau",
      btn_free: "Mulai belajar",
      btn_save: "Simpan",
      btn_continue: "Lanjut belajar",
      preview_label: "\u25B6 Pratinjau",
      instructor_title: "Instruktur",
      reviews_title: "Ulasan learner",
      reviews_empty: "Belum ada ulasan."
    }
  };

  function applyTranslations(lang) {
    const t = translations[lang] || translations.EN;
    document.querySelectorAll("[data-i18n]").forEach(el => {
      const key = el.getAttribute("data-i18n");
      if (t[key]) el.innerText = t[key];
    });
    document.getElementById("currentLang").innerText = lang;
  }

  function toggleProfile(e) {
    e.stopPropagation();
    document.getElementById("profileDropdown").classList.toggle("show");
    document.getElementById("langDropdown").classList.remove("show");
  }

  function toggleLang(e) {
    e.stopPropagation();
    document.getElementById("langDropdown").classList.toggle("show");
    document.getElementById("profileDropdown").classList.remove("show");
  }

  function setLang(code, el, e) {
    e.stopPropagation();
    document.querySelectorAll(".lang-option").forEach(o => o.classList.remove("active"));
    el.classList.add("active");
    document.getElementById("langDropdown").classList.remove("show");
    applyTranslations(code);
  }

  function toggleChapter(element) {
    const chapter = element.parentElement;
    chapter.classList.toggle("active");
  }

  function markDone(event, videoId, btn) {
    event.stopPropagation();
    const courseId = btn.dataset.course;
    const durasi = btn.dataset.durasi;

    fetch(`/course/${courseId}/video-complete`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
      },
      body: JSON.stringify({ video_id: videoId, detik: durasi })
    })
    .then(response => response.json())
    .then(data => {
      if (!data.success) return;
      const row = document.getElementById("vrow-" + videoId);
      row.classList.add("done");
      btn.classList.add("done");
      btn.textContent = "Done";
      const check = row.querySelector(".lesson-check");
      if (check) check.textContent = "\u2713";
    });
  }

  document.addEventListener("click", function () {
    document.getElementById("profileDropdown").classList.remove("show");
    document.getElementById("langDropdown").classList.remove("show");
  });
</script>

</body>
</html>
<?php /**PATH D:\LinkedinLearning\resources\views/course/show.blade.php ENDPATH**/ ?>