<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NaijaReview AI') — DSN x BCT LLM Agent Challenge</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300;1,9..40,400&display=swap" rel="stylesheet">

    <style>
        :root {
            --green-main:    #16A34A;
            --green-light:   #22C55E;
            --green-dark:    #14532D;
            --gold-main:     #EAB308;
            --gold-bright:   #FACC15;
            --gold-dark:     #CA8A04;
            --bg-primary:    #0F172A;
            --bg-secondary:  #111827;
            --bg-card:       #1E293B;
            --text-primary:  #FFFFFF;
            --text-secondary:#E5E7EB;
            --text-muted:    #94A3B8;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-secondary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .font-display {
            font-family: 'Syne', sans-serif;
        }

        /* ── Animated background grid ── */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(22,163,74,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(22,163,74,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        .bg-glow-1 {
            position: fixed;
            top: -200px;
            left: -200px;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(22,163,74,0.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
            animation: driftA 18s ease-in-out infinite alternate;
        }
        .bg-glow-2 {
            position: fixed;
            bottom: -150px;
            right: -100px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(234,179,8,0.08) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
            animation: driftB 22s ease-in-out infinite alternate;
        }

        @keyframes driftA {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(80px, 60px) scale(1.15); }
        }
        @keyframes driftB {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(-60px, -80px) scale(1.1); }
        }

        /* ── Header ── */
        #app-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(22,163,74,0.15);
            transition: all 0.3s ease;
        }
        #app-header.scrolled {
            background: rgba(15, 23, 42, 0.95);
            border-bottom-color: rgba(22,163,74,0.25);
            box-shadow: 0 4px 40px rgba(0,0,0,0.4);
        }

        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: height 0.3s ease;
        }
        .header-inner.compact { height: 56px; }

        /* Logo placeholder */
        .logo-area {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 300px;
        }
        .logo-img-wrap {
            /* Reserve space for the logo image — drop your <img> tag here */
            height: 50px;
            display: flex;
            align-items: center;
        }
        .logo-img-wrap img {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        /* ── Nav ── */
        nav a {
            font-family: 'Syne', sans-serif;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            transition: all 0.2s ease;
            position: relative;
            letter-spacing: 0.02em;
        }
        nav a:hover {
            color: var(--text-primary);
            background: rgba(22,163,74,0.08);
        }
        nav a.active {
            color: var(--green-light);
            background: rgba(22,163,74,0.12);
        }
        nav a.active::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--green-light);
        }

        /* ── Main content wrapper ── */
        #page-content {
            position: relative;
            z-index: 1;
            flex: 1;
            padding: 2.5rem 1rem 4rem;
        }
        .content-shell {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* ── Cards ── */
        .card {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 2rem;
            transition: border-color 0.2s;
        }
        .card:hover { border-color: rgba(22,163,74,0.2); }

        /* ── Buttons ── */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--green-main) 0%, #15803d 100%);
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            letter-spacing: 0.03em;
            padding: 0.75rem 1.5rem;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 0 0 0 rgba(22,163,74,0);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--green-light) 0%, var(--green-main) 100%);
            box-shadow: 0 0 24px rgba(22,163,74,0.35);
            transform: translateY(-1px);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ── Form inputs ── */
        .input-field, .select-field, .textarea-field {
            width: 100%;
            background: rgba(15,23,42,0.8);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            outline: none;
        }
        .input-field::placeholder, .textarea-field::placeholder { color: var(--text-muted); }
        .input-field:focus, .select-field:focus, .textarea-field:focus {
            border-color: var(--green-main);
            box-shadow: 0 0 0 3px rgba(22,163,74,0.15);
        }
        .select-field option { background: var(--bg-secondary); }

        label.field-label {
            display: block;
            font-size: 0.8rem;
            font-family: 'Syne', sans-serif;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        /* ── Badge / pill ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-family: 'Syne', sans-serif;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
        }
        .badge-green { background: rgba(22,163,74,0.15); color: var(--green-light); border: 1px solid rgba(22,163,74,0.25); }
        .badge-gold  { background: rgba(234,179,8,0.12); color: var(--gold-bright); border: 1px solid rgba(234,179,8,0.2); }
        .badge-muted { background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid rgba(255,255,255,0.08); }

        /* ── Section label ── */
        .section-eyebrow {
            font-family: 'Syne', sans-serif;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--green-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .section-eyebrow::before {
            content: '';
            display: block;
            width: 16px;
            height: 2px;
            background: var(--green-light);
            border-radius: 2px;
        }

        /* ── Stars ── */
        .star-filled { color: var(--gold-bright); }
        .star-empty  { color: rgba(255,255,255,0.12); }

        /* ── Empty state placeholder ── */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            min-height: 260px;
            border: 1px dashed rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        .empty-state svg { opacity: 0.3; }

        /* ── Review result card ── */
        .review-result {
            border-left: 3px solid var(--green-main);
            background: rgba(22,163,74,0.06);
            border-radius: 0 12px 12px 0;
            padding: 1.25rem 1.5rem;
        }

        /* ── Recommendation rank card ── */
        .rec-card {
            display: flex;
            gap: 0.875rem;
            align-items: flex-start;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            border-left: 3px solid transparent;
            transition: background 0.15s;
        }
        .rec-card:hover { background: rgba(255,255,255,0.03); }
        .rank-circle {
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 0.75rem;
        }

        /* ── Scenario cards ── */
        .scenario-card {
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 1.25rem;
            background: rgba(255,255,255,0.02);
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            width: 100%;
        }
        .scenario-card:hover {
            border-color: rgba(22,163,74,0.3);
            background: rgba(22,163,74,0.05);
        }
        .scenario-card.selected {
            border-color: var(--green-main);
            background: rgba(22,163,74,0.1);
            box-shadow: 0 0 20px rgba(22,163,74,0.1);
        }
        .scenario-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 0.875rem;
        }
        .scenario-card.selected .scenario-icon {
            background: rgba(22,163,74,0.15);
        }

        /* ── Chat bubble ── */
        .chat-user {
            background: linear-gradient(135deg, var(--green-main), #15803d);
            color: #fff;
            border-radius: 16px 4px 16px 16px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            max-width: 75%;
            margin-left: auto;
        }
        .chat-ai {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,0.07);
            color: var(--text-secondary);
            border-radius: 4px 16px 16px 16px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            max-width: 75%;
        }

        /* ── Error alert ── */
        .alert-error {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 12px;
            padding: 0.875rem 1.25rem;
            color: #fca5a5;
            font-size: 0.875rem;
        }

        /* ── Persona stats ── */
        .stat-card {
            background: rgba(22,163,74,0.06);
            border: 1px solid rgba(22,163,74,0.15);
            border-radius: 16px;
            padding: 1.25rem;
        }
        .stat-card .stat-label {
            font-family: 'Syne', sans-serif;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        .stat-card .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 1.875rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        /* ── Footer ── */
        footer {
            background: #000;
            border-top: 1px solid rgba(22,163,74,0.12);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 1.5rem 2rem;
        }
        .footer-glow {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 200px;
            background: radial-gradient(ellipse, rgba(22,163,74,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .footer-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(22,163,74,0.2), rgba(234,179,8,0.15), transparent);
            margin: 2rem 0 1.5rem;
        }
        .footer-copy {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.25);
            font-family: 'DM Sans', sans-serif;
        }
        .footer-tagline {
            font-family: 'Syne', sans-serif;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            background: linear-gradient(90deg, var(--green-light), var(--gold-bright));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Gradient text ── */
        .gradient-text {
            background: linear-gradient(135deg, var(--green-light) 0%, var(--gold-bright) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-secondary); }
        ::-webkit-scrollbar-thumb { background: rgba(22,163,74,0.3); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(22,163,74,0.5); }

        /* ── Fade in ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up {
            animation: fadeUp 0.5s ease forwards;
        }
        .fade-up-1 { animation-delay: 0.05s; opacity: 0; }
        .fade-up-2 { animation-delay: 0.1s;  opacity: 0; }
        .fade-up-3 { animation-delay: 0.15s; opacity: 0; }
        .fade-up-4 { animation-delay: 0.2s;  opacity: 0; }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .header-inner { padding: 0 1rem; height: 60px; }
            #page-content { padding: 1.5rem 0.75rem 3rem; }
            .card { padding: 1.25rem; }
            .logo-img-wrap img { height: 32px; }
        }
    </style>
</head>
<body>
    <!-- Background atmosphere -->
    <div class="bg-grid"></div>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <!-- SVG decorative lines (subtle) -->
    <svg style="position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;opacity:0.4" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="lineGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#16A34A" stop-opacity="0"/>
                <stop offset="50%" stop-color="#16A34A" stop-opacity="0.3"/>
                <stop offset="100%" stop-color="#EAB308" stop-opacity="0"/>
            </linearGradient>
            <linearGradient id="lineGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#EAB308" stop-opacity="0"/>
                <stop offset="50%" stop-color="#22C55E" stop-opacity="0.2"/>
                <stop offset="100%" stop-color="#EAB308" stop-opacity="0"/>
            </linearGradient>
        </defs>
        <line x1="-100" y1="300" x2="800" y2="-100" stroke="url(#lineGrad1)" stroke-width="1"/>
        <line x1="100%" y1="200" x2="60%" y2="100%" stroke="url(#lineGrad2)" stroke-width="0.8"/>
        <circle cx="92%" cy="15%" r="120" fill="none" stroke="rgba(234,179,8,0.06)" stroke-width="1"/>
        <circle cx="92%" cy="15%" r="200" fill="none" stroke="rgba(234,179,8,0.03)" stroke-width="1"/>
        <circle cx="8%" cy="85%" r="160" fill="none" stroke="rgba(22,163,74,0.05)" stroke-width="1"/>
    </svg>

    <div style="min-height:100vh;display:flex;flex-direction:column;position:relative;z-index:1;">

        <!-- ── HEADER ── -->
        <header id="app-header">
            <div class="header-inner" id="header-inner">

                <!-- Logo -->
                <div class="logo-area">
                    <div class="logo-img-wrap">
                        {{-- Drop your logo <img> here. Example:
                        <img src="{{ asset('images/logo.png') }}" alt="NaijaReview AI">
                        --}}
                        <img src="{{ asset('images/logo.png') }}" alt="NaijaReview AI" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <!-- Fallback SVG wordmark if logo not found -->
                        <svg style="display:none;height:36px;width:auto" viewBox="0 0 220 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="logoGrad" x1="0" y1="0" x2="220" y2="36" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#22C55E"/>
                                    <stop offset="100%" stop-color="#FACC15"/>
                                </linearGradient>
                            </defs>
                            <text x="0" y="27" font-family="Syne, sans-serif" font-weight="800" font-size="22" fill="url(#logoGrad)">NaijaReview</text>
                            <text x="158" y="27" font-family="Syne, sans-serif" font-weight="400" font-size="22" fill="rgba(255,255,255,0.4)"> AI</text>
                        </svg>
                    </div>
                </div>

                <!-- Nav -->
                <nav aria-label="Primary navigation" style="display:flex;align-items:center;gap:0.5rem;">
                    <a href="{{ route('task-a') }}" class="{{ request()->routeIs('task-a') ? 'active' : '' }}">
                        User Modeling
                    </a>
                    <a href="{{ route('task-b') }}" class="{{ request()->routeIs('task-b') ? 'active' : '' }}">
                        Recommendation
                    </a>
                </nav>
            </div>
        </header>

        <!-- ── MAIN ── -->
        <main id="page-content">
            <div class="content-shell">
                @yield('content')
            </div>
        </main>

        <!-- ── FOOTER ── -->
        <footer>
            <div class="footer-inner">
                <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1.5rem;">
                    <!-- Logo repeat / brand -->
                    <div>
                        <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.5rem;">
                            <!-- Mini logo -->
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="28" height="28" rx="8" fill="rgba(22,163,74,0.12)"/>
                                <defs>
                                    <linearGradient id="fLogoGrad" x1="0" y1="0" x2="28" y2="28" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#22C55E"/>
                                        <stop offset="100%" stop-color="#FACC15"/>
                                    </linearGradient>
                                </defs>
                                <path d="M8 20L12 8L16 16L19 12L22 20" stroke="url(#fLogoGrad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="footer-tagline">NaijaReview AI</span>
                        </div>
                        <p style="font-size:0.78rem;color:rgba(255,255,255,0.3);max-width:260px;line-height:1.6;">
                            AI-powered review simulation & recommendation platform for Nigerian businesses.
                        </p>
                    </div>

                    <!-- Stack badges -->
                    <div style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:center;">
                        <span class="badge badge-green">Laravel 13</span>
                        <span class="badge badge-gold">Mistral AI</span>
                        <span class="badge badge-muted">DSN × BCT 3.0</span>
                    </div>
                </div>

                <div class="footer-divider"></div>

                <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
                    <p class="footer-copy">Built for DSN x BCT Hackathon 3.0 · Powered by Laravel 13 + Mistral AI</p>
                    <div style="display:flex;align-items:center;gap:0.375rem;">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5" stroke="rgba(22,163,74,0.5)" stroke-width="1.5"/><circle cx="6" cy="6" r="2" fill="#22C55E" opacity="0.7"/></svg>
                        <span class="footer-copy">Systems operational</span>
                    </div>
                </div>
            </div>
            <div class="footer-glow"></div>
        </footer>

    </div>

    <script>
        (function () {
            var header = document.getElementById('app-header');
            var inner  = document.getElementById('header-inner');

            function onScroll() {
                if (window.scrollY > 24) {
                    header.classList.add('scrolled');
                    inner.classList.add('compact');
                } else {
                    header.classList.remove('scrolled');
                    inner.classList.remove('compact');
                }
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();
    </script>
</body>
</html>