<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Availability Finder — GBU</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#8B0000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="GBU Rooms">
    <link rel="apple-touch-icon" href="logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --maroon: #8B0000;
            --maroon-light: #a50000;
            --maroon-pale: #fff5f5;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f4f6f9;
            color: #2d2d2d;
        }

        /* ── Navbar ── */
        .gbu-navbar {
            background: linear-gradient(135deg, #6b0000 0%, #8B0000 60%, #a50000 100%);
            box-shadow: 0 2px 12px rgba(0,0,0,0.25);
            padding: 0.6rem 0;
        }
        .gbu-navbar .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .gbu-navbar .logo-wrap {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #fff;
            padding: 3px;
            flex-shrink: 0;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.4);
        }
        .gbu-navbar .logo-wrap img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .gbu-navbar .brand-text .title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            letter-spacing: 0.01em;
        }
        .gbu-navbar .brand-text .subtitle {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.75);
            font-weight: 400;
        }

        /* ── Query card ── */
        .query-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .query-card .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.3rem;
        }
        .query-card .form-select {
            border-radius: 8px;
            border-color: #ddd;
            font-size: 0.95rem;
        }
        .query-card .form-select:focus {
            border-color: var(--maroon);
            box-shadow: 0 0 0 0.2rem rgba(139,0,0,0.15);
        }
        .btn-maroon {
            background-color: var(--maroon);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.2rem;
            transition: background 0.2s;
        }
        .btn-maroon:hover { background-color: var(--maroon-light); color: #fff; }

        .btn-current-time {
            border: 2px solid var(--maroon);
            color: var(--maroon);
            background: #fff;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.45rem 1rem;
            transition: all 0.2s;
        }
        .btn-current-time:hover {
            background: var(--maroon-pale);
            color: var(--maroon);
        }

        /* ── Filter bar ── */
        .filter-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        .filter-label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #888;
            margin-bottom: 0.6rem;
        }
        .btn-filter {
            border: 1.5px solid #ddd;
            color: #555;
            background-color: #f8f9fa;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 500;
            padding: 0.3rem 0.9rem;
            transition: all 0.18s;
            cursor: pointer;
        }
        .btn-filter:hover:not(.active) {
            border-color: var(--maroon);
            color: var(--maroon);
            background: var(--maroon-pale);
        }
        .btn-filter.active {
            background-color: var(--maroon);
            color: #fff;
            border-color: var(--maroon);
            box-shadow: 0 2px 8px rgba(139,0,0,0.25);
        }

        /* ── Stats bar ── */
        .stats-bar {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.2rem;
        }
        .stat-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border-radius: 20px;
            padding: 0.35rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 500;
            box-shadow: 0 1px 6px rgba(0,0,0,0.08);
        }
        .stat-pill .dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .dot-free     { background: #28a745; }
        .dot-occupied { background: #dc3545; }

        /* ── Room cards ── */
        .section-heading {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #888;
            margin: 1.2rem 0 0.6rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-heading::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e5e5;
        }

        .room-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #eee;
            border-left-width: 4px;
            padding: 0.75rem 1rem;
            height: 100%;
            transition: box-shadow 0.18s, transform 0.18s;
        }
        .room-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .room-card.free     { border-left-color: #28a745; }
        .room-card.occupied { border-left-color: #dc3545; }

        .room-name {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 2px;
        }
        .room-school-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
            background: #f0f0f0;
            color: #555;
            display: inline-block;
            margin-bottom: 6px;
        }
        .room-subject {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 4px;
        }
        .teacher-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            background: #fff0f0;
            color: #8B0000;
            border: 1px solid #f5c6c6;
            border-radius: 6px;
            padding: 2px 8px;
            font-weight: 500;
        }
        .timetable-link {
            font-size: 0.75rem;
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .timetable-link:hover { color: #1a7a30; text-decoration: underline; }

        /* ── Spinner ── */
        #spinner .spinner-border { color: var(--maroon) !important; width: 2.5rem; height: 2.5rem; }

        /* ── Footer ── */
        .gbu-footer {
            background: #fff;
            border-top: 1px solid #eee;
            color: var(--maroon);
            font-size: 0.82rem;
            padding: 1rem 0;
            margin-top: 3rem;
        }

        /* ── Cached badge ── */
        .cached-notice {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            color: #888;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 3px 10px;
            margin-bottom: 1rem;
        }

        /* ── Hero Banner ── */
        .hero-banner {
            background: linear-gradient(135deg, #6b0000 0%, #8B0000 55%, #b30000 100%);
            padding: 2.8rem 0 3rem;
            position: relative;
            overflow: hidden;
        }
        .hero-banner::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .hero-banner::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -40px;
            width: 250px; height: 250px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
        }
        .hero-title {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 0.75rem;
        }
        .hero-title span { color: #ffcdd2; }
        .hero-sub {
            color: rgba(255,255,255,0.8);
            font-size: 0.95rem;
            line-height: 1.6;
            max-width: 440px;
        }
        .hero-stat {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 0.5rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 70px;
        }
        .hero-stat-num {
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            line-height: 1;
        }
        .hero-stat-label {
            font-size: 0.68rem;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 2px;
        }

        /* ── Hero form card ── */
        .hero-form-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        .hero-form-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #8B0000;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
        }
        .hero-form-card .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }
        .hero-form-card .form-select {
            border-radius: 8px;
            border-color: #ddd;
            font-size: 0.9rem;
        }
        .hero-form-card .form-select:focus {
            border-color: var(--maroon);
            box-shadow: 0 0 0 0.2rem rgba(139,0,0,0.15);
        }

        /* ── Info cards ── */
        .info-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.2rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            height: 100%;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .info-card:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }
        .info-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 0.7rem;
        }
        .info-card-title {
            font-weight: 700;
            font-size: 1rem;
            color: #1a1a1a;
            margin-bottom: 2px;
        }
        .info-card-sub {
            font-size: 0.75rem;
            color: #888;
            line-height: 1.5;
        }

        /* ── Dark mode ── */
        body.dark {
            background-color: #121212;
            color: #e0e0e0;
        }
        body.dark .hero-form-card,
        body.dark .info-card,
        body.dark .how-card,
        body.dark .filter-section,
        body.dark .room-card,
        body.dark .stat-pill,
        body.dark .gbu-footer {
            background: #1e1e1e;
            border-color: #333;
            color: #e0e0e0;
        }
        body.dark .room-name,
        body.dark .info-card-title,
        body.dark .how-step-title { color: #f0f0f0; }
        body.dark .room-school-badge { background: #333; color: #ccc; }
        body.dark .room-subject,
        body.dark .info-card-sub,
        body.dark .how-step-sub,
        body.dark .filter-label { color: #aaa; }
        body.dark .hero-form-card .form-select,
        body.dark .form-select {
            background-color: #2a2a2a;
            color: #e0e0e0;
            border-color: #444;
        }
        body.dark .section-heading { color: #aaa; }
        body.dark .section-heading::after { background: #333; }
        body.dark .cached-notice { background: #2a2a2a; border-color: #444; color: #aaa; }
        body.dark .timetable-link { color: #4caf50; }
        body.dark .btn-current-time { background: #1e1e1e; color: #ff8a80; border-color: #ff8a80; }
        body.dark .btn-filter { background: #2a2a2a; color: #ccc; border-color: #444; }
        body.dark .btn-filter:hover:not(.active) { background: #3a1a1a; color: #ff8a80; border-color: #ff8a80; }
        body.dark .gbu-footer { border-top-color: #333; }

        /* ── Dark mode toggle ── */
        .dark-toggle {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            border-radius: 20px;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-left: auto;
        }
        .dark-toggle:hover { background: rgba(255,255,255,0.25); }

        /* ── Sort dropdown ── */
        .sort-select {
            font-size: 0.78rem;
            border-radius: 8px;
            border: 1.5px solid #ddd;
            padding: 0.25rem 0.6rem;
            color: #555;
            background: #fff;
            cursor: pointer;
        }
        body.dark .sort-select { background: #2a2a2a; color: #ccc; border-color: #444; }

        /* ── Copy button ── */
        .copy-btn {
            background: none;
            border: none;
            color: #aaa;
            padding: 0;
            cursor: pointer;
            font-size: 0.75rem;
            transition: color 0.15s;
            float: right;
            margin-top: -2px;
        }
        .copy-btn:hover { color: #8B0000; }
        .copy-btn.copied { color: #28a745; }

        /* ── Occupancy bar ── */
        .occupancy-bar-wrap {
            background: #fff;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            margin-bottom: 1.2rem;
        }
        body.dark .occupancy-bar-wrap { background: #1e1e1e; }
        .occupancy-label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #888;
            margin-bottom: 0.5rem;
        }
        .occ-bar-track {
            height: 10px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        body.dark .occ-bar-track { background: #333; }
        .occ-bar-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #28a745, #ffc107, #dc3545);
            transition: width 0.6s ease;
        }
        .occ-pct { font-size: 0.82rem; font-weight: 600; color: #555; margin-top: 4px; }
        body.dark .occ-pct { color: #aaa; }

        /* ── Recent searches ── */
        .recent-wrap {
            background: #fff;
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }
        body.dark .recent-wrap { background: #1e1e1e; }
        .recent-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #aaa;
            flex-shrink: 0;
        }
        .recent-chip {
            font-size: 0.75rem;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 14px;
            padding: 3px 10px;
            cursor: pointer;
            color: #555;
            transition: all 0.15s;
        }
        body.dark .recent-chip { background: #2a2a2a; border-color: #444; color: #ccc; }
        .recent-chip:hover { background: #8B0000; color: #fff; border-color: #8B0000; }

        /* ── Next free slot modal ── */
        .next-free-btn {
            background: none;
            border: none;
            color: #0d6efd;
            font-size: 0.72rem;
            padding: 0;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-top: 4px;
        }
        .next-free-btn:hover { text-decoration: underline; }
        body.dark .next-free-btn { color: #90caf9; }

        /* ── Period countdown timer ── */
        .period-timer {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: 12px;
            padding: 0.9rem 1.4rem;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .timer-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(255,255,255,0.5); }
        .timer-period { font-size: 1rem; font-weight: 700; color: #fff; }
        .timer-time { font-size: 0.82rem; color: rgba(255,255,255,0.7); }
        .timer-countdown { font-size: 1.3rem; font-weight: 700; color: #ffd700; font-variant-numeric: tabular-nums; margin-left: auto; }
        .timer-bar-track { height: 4px; background: rgba(255,255,255,0.15); border-radius: 4px; margin-top: 4px; width: 100%; }
        .timer-bar-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, #28a745, #ffd700); transition: width 1s linear; }

        /* ── Search box ── */
        .room-search-wrap {
            position: relative;
            margin-bottom: 0.8rem;
        }
        .room-search-wrap .bi-search {
            position: absolute;
            left: 10px; top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 0.85rem;
        }
        #room-search {
            width: 100%;
            padding: 0.4rem 0.8rem 0.4rem 2rem;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 0.85rem;
            outline: none;
            transition: border-color 0.2s;
        }
        #room-search:focus { border-color: var(--maroon); }
        body.dark #room-search { background: #2a2a2a; color: #e0e0e0; border-color: #444; }

        /* ── Consecutive free badge ── */
        .consec-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 0.68rem;
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
            border-radius: 6px;
            padding: 1px 6px;
            font-weight: 600;
            margin-top: 3px;
        }
        body.dark .consec-badge { background: #1b3a1f; color: #81c784; border-color: #2e7d32; }

        /* ── Free soon badge ── */
        .free-soon-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 0.68rem;
            background: #fff8e1;
            color: #f57f17;
            border: 1px solid #ffe082;
            border-radius: 6px;
            padding: 1px 6px;
            font-weight: 600;
            margin-top: 3px;
        }

        /* ── Animated count ── */
        @keyframes countUp { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .count-anim { animation: countUp 0.4s ease; }

        /* ── Print styles ── */
        @media print {
            .gbu-navbar, .hero-banner, #info-cards, #how-it-works,
            .recent-wrap, .filter-section, .hero-form-card,
            .copy-btn, .next-free-btn, .timetable-link,
            .gbu-footer, #spinner, .btn-find-now { display: none !important; }
            body { background: #fff !important; }
            .room-card { break-inside: avoid; box-shadow: none !important; border: 1px solid #ccc !important; }
            #print-btn { display: none !important; }
        }

        /* ── How it works ── */
        .how-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.3rem 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .how-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #8B0000;
        }
        .how-step {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .how-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #8B0000;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .how-step-title {
            font-weight: 600;
            font-size: 0.88rem;
            color: #1a1a1a;
            margin-bottom: 2px;
        }
        .how-step-sub {
            font-size: 0.78rem;
            color: #888;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="gbu-navbar">
        <div class="container d-flex align-items-center">
            <a class="navbar-brand" href="#">
                <div class="logo-wrap">
                    <img src="logo.png" alt="GBU Logo">
                </div>
                <div class="brand-text">
                    <div class="title">Classroom Availability Finder</div>
                    <div class="subtitle">Gautam Buddha University, Greater Noida</div>
                </div>
            </a>
            <button class="dark-toggle ms-auto" id="dark-toggle" title="Toggle dark mode">
                <i class="bi bi-moon-fill" id="dark-icon"></i> Dark
            </button>
        </div>
    </nav>

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h2 class="hero-title">Find a Free Classroom<br><span>Instantly</span></h2>
                    <p class="hero-sub">Check real-time classroom availability across all schools and blocks at Gautam Buddha University. Select a day and period to get started.</p>
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div class="hero-stat"><span class="hero-stat-num">8</span><span class="hero-stat-label">Schools</span></div>
                        <div class="hero-stat"><span class="hero-stat-num">10</span><span class="hero-stat-label">Periods/Day</span></div>
                        <div class="hero-stat"><span class="hero-stat-num">6</span><span class="hero-stat-label">Days/Week</span></div>
                        <div class="hero-stat"><span class="hero-stat-num">Live</span><span class="hero-stat-label">Data</span></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <!-- Inline Query Form in Hero -->
                    <div class="hero-form-card">
                        <div class="hero-form-title"><i class="bi bi-search"></i> Check Availability</div>
                        <form id="check-form">
                            <div class="row g-2">
                                <div class="col-12 col-sm-6">
                                    <label for="day" class="form-label">Day</label>
                                    <select id="day" name="day" class="form-select">
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="period" class="form-label">Period</label>
                                    <select id="period" name="period" class="form-select">
                                        <option value="I">I — 8:30–9:30</option>
                                        <option value="II">II — 9:30–10:30</option>
                                        <option value="III">III — 10:30–11:30</option>
                                        <option value="IV">IV — 11:30–12:30</option>
                                        <option value="V">V — 12:30–1:30</option>
                                        <option value="VI">VI — 1:30–2:30</option>
                                        <option value="VII">VII — 2:30–3:30</option>
                                        <option value="VIII">VIII — 3:30–4:30</option>
                                        <option value="IX">IX — 4:30–5:30</option>
                                        <option value="X">X — 5:30–6:30</option>
                                    </select>
                                </div>
                                <div class="col-12 d-flex gap-2 mt-1">
                                    <button id="btn-current-time" type="button" class="btn-current-time flex-shrink-0">
                                        <i class="bi bi-clock-fill"></i> Use Current Time
                                    </button>
                                    <button type="submit" class="btn-maroon btn w-100">
                                        <i class="bi bi-search"></i> Check Availability
                                    </button>
                                </div>
                                <div class="col-12 mt-1">
                                    <button id="btn-find-now" type="button" class="btn w-100" style="background:#fff3cd;color:#856404;border:1.5px solid #ffc107;border-radius:8px;font-weight:600;">
                                        <i class="bi bi-lightning-charge-fill"></i> Find Me a Free Room Right Now
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="container py-4">

        <!-- Info Cards (visible before first search) -->
        <div id="info-cards" class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="info-card">
                    <div class="info-icon" style="background:#fff5f5;color:#8B0000;"><i class="bi bi-building"></i></div>
                    <div class="info-card-title">SOICT</div>
                    <div class="info-card-sub">School of ICT<br>IL · IP · IT blocks</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-card">
                    <div class="info-icon" style="background:#f0f7ff;color:#0d6efd;"><i class="bi bi-cpu"></i></div>
                    <div class="info-card-title">SOE</div>
                    <div class="info-card-sub">School of Engineering<br>EL · EP · ET blocks</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-card">
                    <div class="info-icon" style="background:#f0fff4;color:#198754;"><i class="bi bi-eyedropper"></i></div>
                    <div class="info-card-title">SOBT</div>
                    <div class="info-card-sub">School of Biotechnology<br>BL · BT · BTLab blocks</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-card">
                    <div class="info-icon" style="background:#fffbf0;color:#fd7e14;"><i class="bi bi-heart-pulse"></i></div>
                    <div class="info-card-title">SOVSAS</div>
                    <div class="info-card-sub">School of Vocational Science &amp; Applied Sciences<br>VL · VT · VP blocks</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-card">
                    <div class="info-icon" style="background:#f5f0ff;color:#6f42c1;"><i class="bi bi-bank"></i></div>
                    <div class="info-card-title">SOLJG</div>
                    <div class="info-card-sub">School of Law, Justice &amp; Governance<br>LL · LH blocks</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-card">
                    <div class="info-icon" style="background:#f0faff;color:#0dcaf0;"><i class="bi bi-bar-chart-line"></i></div>
                    <div class="info-card-title">SOM</div>
                    <div class="info-card-sub">School of Management<br>BP block</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-card">
                    <div class="info-icon" style="background:#fff0f8;color:#d63384;"><i class="bi bi-people"></i></div>
                    <div class="info-card-title">SOHSS</div>
                    <div class="info-card-sub">School of Humanities &amp; Social Sciences<br>V block</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-card">
                    <div class="info-icon" style="background:#fff8f0;color:#e67e22;"><i class="bi bi-flask"></i></div>
                    <div class="info-card-title">SOBSC</div>
                    <div class="info-card-sub">School of Basic &amp; Applied Sciences<br>Common blocks</div>
                </div>
            </div>
        </div>

        <!-- How it works -->
        <div id="how-it-works" class="how-card mb-4">
            <div class="how-title"><i class="bi bi-info-circle"></i> How it works</div>
            <div class="row g-3 mt-1">
                <div class="col-12 col-sm-4">
                    <div class="how-step">
                        <div class="how-num">1</div>
                        <div>
                            <div class="how-step-title">Select Day &amp; Period</div>
                            <div class="how-step-sub">Pick from the form above or hit "Use Current Time" to auto-fill.</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="how-step">
                        <div class="how-num">2</div>
                        <div>
                            <div class="how-step-title">Live Data</div>
                            <div class="how-step-sub">All rooms are checked live from mygbu.in timetables — no stale data.</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="how-step">
                        <div class="how-num">3</div>
                        <div>
                            <div class="how-step-title">Filter &amp; Find</div>
                            <div class="how-step-sub">Use school filters to narrow down results to your block.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Searches (hidden until used) -->
        <div class="recent-wrap d-none" id="recent-wrap">
            <span class="recent-label"><i class="bi bi-clock-history"></i> Recent</span>
            <div id="recent-chips"></div>
            <button id="btn-reset" class="ms-auto" style="background:none;border:none;color:#8B0000;font-size:0.78rem;font-weight:600;cursor:pointer;white-space:nowrap;">
                <i class="bi bi-house-fill"></i> Back to Home
            </button>
        </div>

        <!-- Filter Section -->
        <div class="filter-section d-none" id="filter-section">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <div class="filter-label mb-0"><i class="bi bi-funnel"></i> Filter by School / Block</div>
                <div class="d-flex align-items-center gap-2">
                    <button id="print-btn" onclick="window.print()" style="background:none;border:1px solid #ddd;border-radius:8px;padding:3px 10px;font-size:0.75rem;color:#555;cursor:pointer;"><i class="bi bi-printer"></i> Print</button>
                    <label for="sort-select" style="font-size:0.75rem;color:#888;white-space:nowrap;">Sort:</label>
                    <select id="sort-select" class="sort-select">
                        <option value="default">Default</option>
                        <option value="az">A → Z</option>
                        <option value="za">Z → A</option>
                    </select>
                </div>
            </div>
            <!-- Room search -->
            <div class="room-search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="room-search" placeholder="Search room e.g. IL202…" autocomplete="off">
            </div>
            <!-- Consecutive free filter -->
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <div class="d-flex flex-wrap gap-2" id="filter-bar">
                    <button class="btn-filter active" data-school="All">All Rooms</button>
                    <button class="btn-filter" data-school="SOICT">SOICT</button>
                    <button class="btn-filter" data-school="SOE">SOE</button>
                    <button class="btn-filter" data-school="SOBT">SOBT</button>
                    <button class="btn-filter" data-school="SOVS/AS">SOVSAS</button>
                    <button class="btn-filter" data-school="Common">Common</button>
                </div>
            </div>
        </div>

        <!-- Period Countdown Timer -->
        <div id="period-timer" class="period-timer d-none">
            <div>
                <div class="timer-label">Current Period</div>
                <div class="timer-period" id="timer-period-name">—</div>
                <div class="timer-time" id="timer-period-time">—</div>
                <div class="timer-bar-track"><div class="timer-bar-fill" id="timer-bar" style="width:0%"></div></div>
            </div>
            <div class="timer-countdown" id="timer-countdown">—</div>
        </div>

        <!-- Spinner -->
        <div id="spinner" class="d-none text-center my-5">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Fetching live timetable data&hellip;</p>
        </div>

        <!-- Results -->
        <div id="results"></div>

    </main>

    <!-- Next Free Slot Modal -->
    <div class="modal fade" id="nextFreeModal" tabindex="-1" aria-labelledby="nextFreeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:#8B0000;color:#fff;">
                    <h6 class="modal-title" id="nextFreeModalLabel"><i class="bi bi-calendar-check"></i> Free Periods Today</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="nextFreeModalBody">
                    <div class="text-center py-3"><div class="spinner-border spinner-border-sm" style="color:#8B0000;"></div> Loading…</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="gbu-footer text-center">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
            <img src="logo.png" alt="GBU" style="height:28px;border-radius:50%;vertical-align:middle;">
            <span class="fw-bold">Gautam Buddha University, Greater Noida</span>
        </div>
        <div style="font-size:0.75rem;color:#aaa;margin-top:4px;">
            Made with <i class="bi bi-heart-fill" style="color:#8B0000;font-size:0.7rem;"></i> by <strong style="color:#555;">Vaibhav Porwal</strong>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Period ranges + times ──────────────────────────────────────────
        const PERIOD_RANGES = [
            { period: 'I',    start: 510,  end: 570  },
            { period: 'II',   start: 570,  end: 630  },
            { period: 'III',  start: 630,  end: 690  },
            { period: 'IV',   start: 690,  end: 750  },
            { period: 'V',    start: 750,  end: 810  },
            { period: 'VI',   start: 810,  end: 870  },
            { period: 'VII',  start: 870,  end: 930  },
            { period: 'VIII', start: 930,  end: 990  },
            { period: 'IX',   start: 990,  end: 1050 },
            { period: 'X',    start: 1050, end: 1110 },
        ];

        const PERIOD_TIMES = {
            'I':    '8:30 – 9:30',
            'II':   '9:30 – 10:30',
            'III':  '10:30 – 11:30',
            'IV':   '11:30 – 12:30',
            'V':    '12:30 – 1:30',
            'VI':   '1:30 – 2:30',
            'VII':  '2:30 – 3:30',
            'VIII': '3:30 – 4:30',
            'IX':   '4:30 – 5:30',
            'X':    '5:30 – 6:30',
        };

        // ── State ──────────────────────────────────────────────────────────
        let lastData     = null;
        let activeFilter = 'All';
        let activeSort   = 'default';
        let recentSearches = JSON.parse(localStorage.getItem('gbu_recent') || '[]');

        // ── Feature 4: Dark mode ───────────────────────────────────────────
        const darkToggle = document.getElementById('dark-toggle');
        const darkIcon   = document.getElementById('dark-icon');
        if (localStorage.getItem('gbu_dark') === '1') {
            document.body.classList.add('dark');
            darkIcon.className = 'bi bi-sun-fill';
            darkToggle.innerHTML = '<i class="bi bi-sun-fill" id="dark-icon"></i> Light';
        }
        darkToggle.addEventListener('click', () => {
            const isDark = document.body.classList.toggle('dark');
            localStorage.setItem('gbu_dark', isDark ? '1' : '0');
            darkToggle.innerHTML = isDark
                ? '<i class="bi bi-sun-fill"></i> Light'
                : '<i class="bi bi-moon-fill"></i> Dark';
        });

        // ── Feature 6: Shareable URL ───────────────────────────────────────
        (function loadFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const d = params.get('day'), p = params.get('period');
            if (d && p) {
                const dayEl = document.getElementById('day');
                const perEl = document.getElementById('period');
                if ([...dayEl.options].some(o => o.value === d)) dayEl.value = d;
                if ([...perEl.options].some(o => o.value === p)) perEl.value = p;
                setTimeout(() => document.getElementById('check-form').requestSubmit(), 300);
            }
        })();

        // ── getCurrentDayAndPeriod ─────────────────────────────────────────
        function getCurrentDayAndPeriod() {
            const now = new Date();
            const dow = now.getDay();
            if (dow === 0) return null;
            const dayNames = ['','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            const mins = now.getHours() * 60 + now.getMinutes();
            for (const p of PERIOD_RANGES) {
                if (mins >= p.start && mins < p.end) return { day: dayNames[dow], period: p.period };
            }
            return null;
        }

        // ── Use Current Time ──────────────────────────────────────────────
        document.getElementById('btn-current-time').addEventListener('click', () => {
            const d = getCurrentDayAndPeriod();
            if (!d) { alert('No classes are scheduled at the current time.'); return; }
            document.getElementById('day').value    = d.day;
            document.getElementById('period').value = d.period;
            document.getElementById('check-form').requestSubmit();
        });

        // ── Feature 8: Find me a room now ─────────────────────────────────
        document.getElementById('btn-find-now').addEventListener('click', () => {
            const d = getCurrentDayAndPeriod();
            if (!d) { alert('No classes are scheduled at the current time.'); return; }
            document.getElementById('day').value    = d.day;
            document.getElementById('period').value = d.period;
            // After search, auto-scroll to first free room
            document.getElementById('check-form').requestSubmit();
            window._findNow = true;
        });

        // ── Feature 3: Sort ───────────────────────────────────────────────
        document.getElementById('sort-select').addEventListener('change', e => {
            activeSort = e.target.value;
            if (lastData) renderResults(lastData, activeFilter);
        });

        function sortRooms(rooms) {
            if (activeSort === 'az') return [...rooms].sort((a,b) => a.name.localeCompare(b.name));
            if (activeSort === 'za') return [...rooms].sort((a,b) => b.name.localeCompare(a.name));
            return rooms;
        }

        // ── Feature 9: Room search ────────────────────────────────────────
        let searchQuery = '';
        document.getElementById('room-search').addEventListener('input', e => {
            searchQuery = e.target.value.trim().toLowerCase();
            if (lastData) renderResults(lastData, activeFilter);
        });
        function applySearch(rooms) {
            if (!searchQuery) return rooms;
            return rooms.filter(r => r.name.toLowerCase().includes(searchQuery));
        }

        // ── Feature 4: Period countdown timer ────────────────────────────
        function updateTimer() {
            const now  = new Date();
            const dow  = now.getDay();
            const mins = now.getHours() * 60 + now.getMinutes();
            const timerEl = document.getElementById('period-timer');
            if (dow === 0) { timerEl.classList.add('d-none'); return; }
            const current = PERIOD_RANGES.find(p => mins >= p.start && mins < p.end);
            if (!current) { timerEl.classList.add('d-none'); return; }
            timerEl.classList.remove('d-none');
            const elapsed  = mins - current.start;
            const duration = current.end - current.start;
            const pct      = Math.round((elapsed / duration) * 100);
            const secs     = (current.end * 60) - (now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds());
            const mm       = String(Math.floor(secs / 60)).padStart(2, '0');
            const ss       = String(secs % 60).padStart(2, '0');
            document.getElementById('timer-period-name').textContent = `Period ${current.period}`;
            document.getElementById('timer-period-time').textContent = PERIOD_TIMES[current.period];
            document.getElementById('timer-countdown').textContent   = `${mm}:${ss} left`;
            document.getElementById('timer-bar').style.width         = pct + '%';
        }
        updateTimer();
        setInterval(updateTimer, 1000);

        function updateFilterCounts(data) {
            document.querySelectorAll('.btn-filter').forEach(btn => {
                const school = btn.dataset.school;
                const count = school === 'All'
                    ? data.free.length
                    : data.free.filter(r => r.school === school).length;
                const existing = btn.querySelector('.filter-count');
                if (existing) existing.remove();
                const badge = document.createElement('span');
                badge.className = 'filter-count';
                badge.style.cssText = 'margin-left:5px;background:rgba(255,255,255,0.3);border-radius:10px;padding:1px 6px;font-size:0.7rem;';
                badge.textContent = count;
                btn.appendChild(badge);
            });
        }

        // ── Feature 2: Copy to clipboard ──────────────────────────────────
        function copyRoom(name, btn) {
            navigator.clipboard.writeText(name).then(() => {
                btn.classList.add('copied');
                btn.innerHTML = '<i class="bi bi-check2"></i>';
                setTimeout(() => {
                    btn.classList.remove('copied');
                    btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 1500);
            });
        }

        // ── Feature 5: Next free slot ──────────────────────────────────────
        async function showNextFree(roomId, roomName, day) {
            const modal = new bootstrap.Modal(document.getElementById('nextFreeModal'));
            document.getElementById('nextFreeModalLabel').innerHTML =
                `<i class="bi bi-calendar-check"></i> ${roomName} — Free Periods on ${day}`;
            document.getElementById('nextFreeModalBody').innerHTML =
                '<div class="text-center py-3"><div class="spinner-border spinner-border-sm" style="color:#8B0000;"></div> Loading…</div>';
            modal.show();
            try {
                const res  = await fetch(`next_free.php?room_id=${roomId}&day=${encodeURIComponent(day)}`);
                const data = await res.json();
                if (!data.success || data.free_periods.length === 0) {
                    document.getElementById('nextFreeModalBody').innerHTML =
                        '<p class="text-muted mb-0">No free periods found for this room today.</p>';
                    return;
                }
                const chips = data.free_periods.map(p => `
                    <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                        <span style="background:#16a34a;color:#fff;border-radius:6px;padding:3px 10px;font-size:0.78rem;font-weight:700;min-width:52px;text-align:center;">${p}</span>
                        <span style="font-size:0.85rem;color:#374151;font-weight:500;"><i class="bi bi-clock" style="color:#16a34a;"></i> ${PERIOD_TIMES[p]}</span>
                    </div>`).join('');
                document.getElementById('nextFreeModalBody').innerHTML =
                    `<p class="text-muted small mb-3">Free periods for <strong>${roomName}</strong> on <strong>${day}</strong>:</p>${chips}`;
            } catch {
                document.getElementById('nextFreeModalBody').innerHTML =
                    '<p class="text-danger mb-0">Failed to load. Please try again.</p>';
            }
        }

        // ── Feature 9: Recent searches ────────────────────────────────────
        function saveRecent(day, period) {
            const key = `${day} · ${period}`;
            recentSearches = [key, ...recentSearches.filter(r => r !== key)].slice(0, 3);
            try { localStorage.setItem('gbu_recent', JSON.stringify(recentSearches)); } catch(e) {}
            renderRecent();
        }

        function renderRecent() {
            if (recentSearches.length === 0) return;
            const wrap  = document.getElementById('recent-wrap');
            const chips = document.getElementById('recent-chips');
            wrap.classList.remove('d-none');
            chips.innerHTML = recentSearches.map(r => {
                const [d, p] = r.split(' · ');
                return `<button class="recent-chip" data-day="${d}" data-period="${p}">${r}</button>`;
            }).join('');
            chips.querySelectorAll('.recent-chip').forEach(chip => {
                chip.addEventListener('click', () => {
                    document.getElementById('day').value    = chip.dataset.day;
                    document.getElementById('period').value = chip.dataset.period;
                    document.getElementById('check-form').requestSubmit();
                });
            });
        }
        renderRecent();

        // ── Back to Home ──────────────────────────────────────────────────
        document.getElementById('btn-reset').addEventListener('click', () => {
            lastData     = null;
            activeFilter = 'All';
            document.getElementById('results').innerHTML = '';
            document.getElementById('spinner').classList.add('d-none');
            document.getElementById('info-cards').classList.remove('d-none');
            document.getElementById('how-it-works').classList.remove('d-none');
            document.getElementById('filter-section').classList.add('d-none');
            document.getElementById('recent-wrap').classList.add('d-none');
            document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
            document.querySelector('.btn-filter[data-school="All"]').classList.add('active');
            document.querySelectorAll('.filter-count').forEach(b => b.remove());
            window.history.replaceState({}, '', window.location.pathname);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // ── filterBySchool ────────────────────────────────────────────────
        function filterBySchool(rooms, school) {
            if (school === 'All') return rooms;
            return rooms.filter(r => r.school === school);
        }

        // ── Feature 10: Occupancy bar + renderResults ─────────────────────
        function renderResults(data, filter) {
            const el = document.getElementById('results');
            if (!data.success) {
                el.innerHTML = `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>${data.error || 'An error occurred.'}</div>`;
                return;
            }

            const day = document.getElementById('day').value;

            let html = '';
            if (data.warning) html += `<div class="alert alert-warning"><i class="bi bi-wifi-off me-2"></i>${data.warning}</div>`;

            const freeRooms     = sortRooms(applySearch(filterBySchool(data.free,     filter)));
            const occupiedRooms = sortRooms(applySearch(filterBySchool(data.occupied, filter)));
            const total         = freeRooms.length + occupiedRooms.length;
            const occPct        = total > 0 ? Math.round((occupiedRooms.length / total) * 100) : 0;

            // Build a set of free room IDs for "free next period" check
            const freeIds = new Set(data.free.map(r => r.id));

            // Feature 10: Occupancy bar
            html += `
            <div class="occupancy-bar-wrap">
                <div class="occupancy-label"><i class="bi bi-bar-chart-fill"></i> Campus Occupancy — ${data.day}, Period ${data.period}</div>
                <div class="occ-bar-track"><div class="occ-bar-fill" style="width:${occPct}%"></div></div>
                <div class="occ-pct">${occPct}% occupied &nbsp;·&nbsp; ${freeRooms.length} free &nbsp;·&nbsp; ${occupiedRooms.length} occupied
                ${data.cached ? '&nbsp;<span class="cached-notice"><i class="bi bi-lightning-charge-fill"></i> Cached</span>' : ''}
                </div>
            </div>`;

            // Free rooms
            html += `<div class="section-heading"><i class="bi bi-door-open text-success"></i> Free Rooms (<span class="count-anim">${freeRooms.length}</span>)</div>`;
            if (freeRooms.length === 0) {
                html += `<p class="text-muted small">No free rooms for this filter.</p>`;
            } else {
                html += `<div class="row g-2 mb-3">`;
                for (const room of freeRooms) {
                    // Check consecutive: is this room also free in the next period?
                    const periodIdx = PERIOD_RANGES.findIndex(p => p.period === data.period);
                    const nextFreeInData = periodIdx < 9 && data.free.some(r => r.id === room.id);
                    // We can't know next period without fetching, so show badge based on current data only
                    html += `
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="room-card free">
                            <div class="room-name">
                                ${room.name}
                                <button class="copy-btn" onclick="copyRoom('${room.name}', this)" title="Copy room name"><i class="bi bi-clipboard"></i></button>
                            </div>
                            <div class="room-school-badge">${room.school}</div><br>
                            <a href="${room.url}" target="_blank" rel="noopener" class="timetable-link"><i class="bi bi-calendar3"></i> Timetable</a><br>
                            <button class="next-free-btn" onclick="showNextFree(${room.id},'${room.name}','${day}')"><i class="bi bi-clock-history"></i> All free slots</button>
                        </div>
                    </div>`;
                }
                html += `</div>`;
            }

            // Occupied rooms
            html += `<div class="section-heading"><i class="bi bi-door-closed text-danger"></i> Occupied Rooms (<span class="count-anim">${occupiedRooms.length}</span>)</div>`;
            if (occupiedRooms.length === 0) {
                html += `<p class="text-muted small">No occupied rooms for this filter.</p>`;
            } else {
                html += `<div class="row g-2 mb-3">`;
                for (const room of occupiedRooms) {
                    html += `
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="room-card occupied">
                            <div class="room-name">
                                ${room.name}
                                <button class="copy-btn" onclick="copyRoom('${room.name}', this)" title="Copy room name"><i class="bi bi-clipboard"></i></button>
                            </div>
                            <div class="room-school-badge">${room.school}</div>
                            <div class="room-subject">${room.subject} &bull; Sec ${room.section}</div>
                            <div class="teacher-badge"><i class="bi bi-person-fill"></i> ${room.teacher}</div><br>
                            <button class="next-free-btn" onclick="showNextFree(${room.id},'${room.name}','${day}')"><i class="bi bi-clock-history"></i> All free slots</button>
                        </div>
                    </div>`;
                }
                html += `</div>`;
            }

            el.innerHTML = html;

            // Feature 8: scroll to first free room
            if (window._findNow) {
                window._findNow = false;
                const firstFree = el.querySelector('.room-card.free');
                if (firstFree) firstFree.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // ── checkAvailability ─────────────────────────────────────────────
        async function checkAvailability(event) {
            event.preventDefault();
            const day    = document.getElementById('day').value;
            const period = document.getElementById('period').value;

            // Feature 6: update URL
            const url = new URL(window.location);
            url.searchParams.set('day', day);
            url.searchParams.set('period', period);
            window.history.replaceState({}, '', url);

            document.getElementById('spinner').classList.remove('d-none');
            document.getElementById('results').innerHTML = '';
            document.getElementById('info-cards').classList.add('d-none');
            document.getElementById('how-it-works').classList.add('d-none');
            document.getElementById('filter-section').classList.remove('d-none');
            document.getElementById('recent-wrap').classList.remove('d-none');

            try {
                const res = await fetch('check.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `day=${encodeURIComponent(day)}&period=${encodeURIComponent(period)}`
                });
                document.getElementById('spinner').classList.add('d-none');
                if (!res.ok) {
                    document.getElementById('results').innerHTML =
                        `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Server error (HTTP ${res.status}). Please try again.</div>`;
                    return;
                }
                const data = await res.json();
                lastData = data;
                // Feature 9: save recent
                saveRecent(day, period);
                // Feature 1: update filter counts
                updateFilterCounts(data);
                renderResults(lastData, activeFilter);
            } catch (err) {
                document.getElementById('spinner').classList.add('d-none');
                document.getElementById('results').innerHTML =
                    `<div class="alert alert-danger"><i class="bi bi-wifi-off me-2"></i>Network error. Please check your connection and try again.</div>`;
            }
        }

        document.getElementById('check-form').addEventListener('submit', checkAvailability);

        // ── PWA: Register service worker ──────────────────────────────────
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js').catch(() => {});
        }

        // ── Keyboard shortcuts ────────────────────────────────────────────
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && lastData) {
                document.getElementById('btn-reset').click();
            }
        });

        // ── Filter buttons ────────────────────────────────────────────────
        document.querySelectorAll('.btn-filter').forEach(btn => {
            btn.addEventListener('click', () => {
                activeFilter = btn.dataset.school;
                document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                if (lastData) renderResults(lastData, activeFilter);
            });
        });
    </script>
</body>
</html>
