<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#D32F2F">
    <meta name="description" content="AfyaLink Uganda helps people discover hospitals, clinics, pharmacies, laboratories, doctors and healthcare services near them.">

    <title>AfyaLink Uganda | Healthcare Near You</title>

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="/favicon.ico">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="AfyaLink Uganda">
    <meta property="og:title" content="AfyaLink Uganda | Healthcare Near You">
    <meta property="og:description" content="Find hospitals, clinics, pharmacies, laboratories and doctors across Uganda.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/hero-doctor.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Brand icons only (Google Play / Apple / socials) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/brands.min.css">

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        /* =====================================================
           ROOT
        ===================================================== */
        :root {
            --red: #D32F2F;
            --red-dark: #B71C1C;
            --red-light: #FFF1F1;
            --green: #168A55;
            --green-dark: #0D6B3F;
            --green-light: #ECF9F1;
            --dark: #172033;
            --dark-2: #111827;
            --text: #5F6B7A;
            --muted: #6B7686;
            --white: #FFFFFF;
            --light: #F7F9FC;
            --light-2: #F2F5F9;
            --border: #E8EDF3;
            --shadow: 0 20px 50px rgba(23, 32, 51, .08);
            --shadow-lg: 0 30px 80px rgba(23, 32, 51, .14);
            --nav-h: 78px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        /* anchors never hide under the fixed navbar */
        [id] { scroll-margin-top: calc(var(--nav-h) + 12px); }
        #home { scroll-margin-top: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--white);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-text-size-adjust: 100%;
        }

        body.menu-open,
        body.modal-open { overflow: hidden; }

        img { max-width: 100%; display: block; }
        a { text-decoration: none; color: inherit; }
        button, input, select, textarea { font: inherit; }
        button { cursor: pointer; }

        :focus-visible {
            outline: 3px solid rgba(211, 47, 47, .55);
            outline-offset: 2px;
        }

        .sr-only {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
        }

        .skip-link {
            position: fixed; top: -60px; left: 16px; z-index: 20000;
            padding: 10px 16px; border-radius: 10px;
            background: var(--dark); color: white; font-size: 13px; font-weight: 600;
            transition: top .2s;
        }
        .skip-link:focus { top: 12px; }

        .container { width: min(1360px, calc(100% - 64px)); margin-inline: auto; }
        .section { padding: 90px 0; }
        .section-light { background: var(--light); }
        .section-heading { max-width: 780px; }
        .section-heading.center { margin-inline: auto; text-align: center; }

        .eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 16px; border-radius: 50px;
            background: var(--green-light); color: var(--green);
            font-size: 12px; font-weight: 700; letter-spacing: .08em;
        }
        .eyebrow.red { color: var(--red); background: var(--red-light); }

        .section-title {
            margin-top: 18px;
            font-size: clamp(30px, 4vw, 46px);
            line-height: 1.18; letter-spacing: -.03em; font-weight: 800;
        }

        .section-description { margin-top: 18px; color: var(--text); font-size: 16px; line-height: 1.9; }

        .red { color: var(--red); }
        .green { color: var(--green); }

        /* =====================================================
           HERO HIGHLIGHT
        ===================================================== */
        .hero-highlight { position: relative; display: inline-block; color: var(--red); white-space: nowrap; }
        .hero-highlight svg {
            position: absolute; left: 0; bottom: -10px; width: 100%; height: 14px;
            overflow: visible; pointer-events: none;
        }
        .hero-highlight path { opacity: .45; }

        /* =====================================================
           BUTTONS
        ===================================================== */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            min-height: 50px; padding: 14px 22px; border: none; border-radius: 14px;
            font-size: 14px; font-weight: 700; transition: .25s ease; text-align: center;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-primary { color: white; background: linear-gradient(135deg, var(--red), var(--red-dark)); box-shadow: 0 12px 30px rgba(211, 47, 47, .24); }
        .btn-primary:hover { box-shadow: 0 18px 38px rgba(211, 47, 47, .30); }
        .btn-green { color: white; background: linear-gradient(135deg, var(--green), var(--green-dark)); }
        .btn-white { color: var(--dark); background: white; border: 1px solid var(--border); }
        .btn-outline-white { color: white; border: 1px solid rgba(255, 255, 255, .35); background: rgba(255, 255, 255, .08); backdrop-filter: blur(10px); }
        .btn[disabled] { opacity: .6; cursor: not-allowed; transform: none; }

        .text-link {
            display: inline-flex; align-items: center; gap: 8px;
            color: var(--red); font-size: 13px; font-weight: 700;
        }
        button.text-link { border: none; background: none; padding: 0; font-family: inherit; text-align: left; }

        /* =====================================================
           NAVBAR
        ===================================================== */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(232, 237, 243, .8);
        }

        .nav-inner { min-height: var(--nav-h); display: flex; align-items: center; justify-content: space-between; gap: 30px; }

        .logo { display: flex; align-items: center; gap: 11px; }

        .logo-mark {
            width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;
            border-radius: 14px; color: white;
            background: linear-gradient(135deg, var(--red), var(--red-dark));
            box-shadow: 0 10px 28px rgba(211, 47, 47, .23);
        }

        .logo-title { font-size: 20px; font-weight: 800; line-height: 1; }
        .logo-subtitle { margin-top: 5px; color: var(--green); font-size: 10px; font-weight: 800; letter-spacing: .18em; }

        .desktop-nav { display: flex; align-items: center; gap: 27px; }
        .desktop-nav a { position: relative; color: #596577; font-size: 13px; font-weight: 500; transition: .2s; }
        .desktop-nav a:hover { color: var(--red); }
        .desktop-nav a.active { color: var(--red); font-weight: 700; }
        .desktop-nav a.active::after {
            content: ""; position: absolute; left: 0; right: 0; bottom: -11px;
            height: 3px; border-radius: 10px; background: var(--red);
        }

        .nav-actions { display: flex; align-items: center; gap: 18px; }
        .signin-link { font-size: 14px; font-weight: 600; }

        .menu-button {
            display: none; width: 44px; height: 44px; align-items: center; justify-content: center;
            border: 1px solid var(--border); border-radius: 12px; background: white; color: var(--dark);
        }

        .mobile-menu {
            display: none; padding: 10px 20px 24px; background: white;
            border-top: 1px solid var(--border);
            max-height: calc(100vh - var(--nav-h)); max-height: calc(100dvh - var(--nav-h));
            overflow-y: auto; overscroll-behavior: contain;
        }
        .mobile-menu.open { display: block; }
        .mobile-menu a:not(.btn) { display: block; padding: 13px 10px; border-radius: 10px; font-size: 14px; font-weight: 500; }
        .mobile-menu a:not(.btn):hover, .mobile-menu a[aria-current="true"] { color: var(--red); background: var(--light); }
        .mobile-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 12px; padding-top: 16px; border-top: 1px solid var(--border); }

        /* =====================================================
           HERO
        ===================================================== */
        .hero {
            position: relative;
            padding-top: 135px; padding-bottom: 110px;
            overflow-x: clip;            /* clip sideways only so the search dropdown is never cut off */
            background:
                radial-gradient(circle at 0% 25%, rgba(211, 47, 47, .09), transparent 35%),
                radial-gradient(circle at 100% 70%, rgba(22, 138, 85, .10), transparent 35%),
                linear-gradient(135deg, #FFF7F7, #FFFFFF 55%, #F1FBF6);
        }

        .hero-grid {
            display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, .95fr);
            align-items: center; gap: 70px; min-height: 610px;
        }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;
            border: 1px solid #DDF2E6; border-radius: 50px; background: white; color: var(--green);
            box-shadow: 0 8px 28px rgba(22, 138, 85, .09); font-size: 13px; font-weight: 700;
        }

        .hero-title { max-width: 720px; margin-top: 26px; font-size: clamp(36px, 5vw, 67px); line-height: 1.06; letter-spacing: -.045em; font-weight: 800; }
        .hero-description { max-width: 640px; margin-top: 24px; color: var(--text); font-size: 17px; line-height: 1.85; }

        /* LIVE SEARCH */
        .search-wrapper { position: relative; max-width: 640px; }

        .search-box {
            width: 100%; margin-top: 32px; padding: 8px; display: flex; gap: 8px;
            background: white; border: 1px solid var(--border); border-radius: 18px; box-shadow: var(--shadow);
        }

        .search-input { flex: 1; min-width: 0; display: flex; align-items: center; gap: 12px; padding: 0 14px; color: var(--muted); }
        .search-input input { width: 100%; padding: 15px 0; border: none; outline: none; color: var(--dark); background: transparent; }
        .search-box:focus-within { border-color: #F2BBBB; box-shadow: var(--shadow), 0 0 0 4px rgba(211, 47, 47, .10); }

        .live-search-results {
            display: none; position: absolute; top: calc(100% + 10px); left: 0; right: 0; z-index: 300;
            max-height: min(470px, 70vh); overflow-y: auto; overscroll-behavior: contain;
            background: white; border: 1px solid var(--border); border-radius: 20px; box-shadow: var(--shadow-lg);
        }
        .live-search-results.open { display: block; }

        .search-result-heading { padding: 14px 18px 8px; color: var(--muted); font-size: 10px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }

        .search-result-item {
            width: 100%; display: flex; align-items: center; gap: 13px; padding: 13px 18px;
            border: 0; border-top: 1px solid #F1F4F7; background: white; color: inherit;
            text-align: left; transition: .2s;
        }
        button.search-result-item { cursor: pointer; }
        button.search-result-item:hover, button.search-result-item:focus-visible { background: #FFF7F7; outline-offset: -3px; }

        .search-result-image { width: 50px; height: 50px; flex-shrink: 0; object-fit: cover; border-radius: 13px; background: #F1F5F9; }
        .search-result-icon { width: 50px; height: 50px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 13px; color: var(--green); background: var(--green-light); }
        .search-result-body { display: block; min-width: 0; flex: 1; }
        .search-result-name { display: block; font-size: 13px; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .search-result-meta { display: block; margin-top: 3px; color: var(--muted); font-size: 10px; text-transform: capitalize; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .search-result-rating { margin-left: auto; flex-shrink: 0; color: #A16207; font-size: 11px; font-weight: 700; }
        .search-empty { padding: 25px; color: var(--muted); text-align: center; font-size: 13px; }

        .popular { margin-top: 17px; display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
        .popular-label { color: var(--muted); font-size: 12px; }
        .popular button { padding: 7px 13px; border: 1px solid var(--border); border-radius: 30px; background: white; color: #647084; font-size: 12px; transition: .2s; }
        .popular button:hover { border-color: #F2BBBB; color: var(--red); }

        .hero-features { margin-top: 26px; display: flex; flex-wrap: wrap; gap: 20px; }
        .hero-feature { display: flex; align-items: center; gap: 8px; color: #6E7988; font-size: 13px; }
        .hero-feature-icon { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: var(--red); background: var(--red-light); }
        .hero-feature:nth-child(2) .hero-feature-icon { color: var(--green); background: var(--green-light); }

        .hero-image-wrap { position: relative; min-height: 555px; }
        .hero-image-shadow { position: absolute; inset: 18px -25px -15px 30px; border-radius: 42px; background: linear-gradient(135deg, #FDEAEA, #FBE1E1); transform: rotate(2.5deg); }
        .hero-image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; border-radius: 42px; box-shadow: var(--shadow-lg); background: #F1F5F9; }

        .floating-card {
            position: absolute; display: flex; align-items: center; gap: 13px; padding: 16px 18px;
            border: 1px solid rgba(232, 237, 243, .8); border-radius: 17px;
            background: rgba(255, 255, 255, .96); box-shadow: var(--shadow); backdrop-filter: blur(12px);
        }
        .floating-card.top { top: 45px; right: -30px; }
        .floating-card.bottom { left: -35px; bottom: 60px; }
        .floating-icon { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 13px; color: var(--green); background: var(--green-light); }
        .floating-card.bottom .floating-icon { color: var(--red); background: var(--red-light); }
        .floating-small { color: var(--muted); font-size: 11px; }
        .floating-title { margin-top: 2px; font-size: 13px; font-weight: 700; }

        /* =====================================================
           FEATURE BAR
        ===================================================== */
        .feature-bar { position: relative; z-index: 20; margin-top: -60px; }
        .feature-grid { display: grid; grid-template-columns: repeat(4, 1fr); overflow: hidden; border: 1px solid var(--border); border-radius: 26px; background: white; box-shadow: var(--shadow-lg); }
        .feature-item { padding: 25px; display: flex; align-items: center; gap: 14px; border-right: 1px solid var(--border); }
        .feature-item:last-child { border-right: none; }
        .feature-icon { width: 49px; height: 49px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 15px; color: var(--red); background: var(--red-light); }
        .feature-item:nth-child(even) .feature-icon { color: var(--green); background: var(--green-light); }
        .feature-title { font-size: 15px; font-weight: 800; }
        .feature-subtitle { margin-top: 2px; color: var(--muted); font-size: 11px; }

        /* =====================================================
           STEPS
        ===================================================== */
        .steps-grid { margin-top: 55px; display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px; }
        .step-card { position: relative; padding: 32px; overflow: hidden; border: 1px solid var(--border); border-radius: 26px; background: #FBFCFE; transition: .25s ease; }
        .step-card:hover { transform: translateY(-5px); background: white; box-shadow: var(--shadow); }
        .step-number { position: absolute; top: 14px; right: 22px; color: #EEF1F5; font-size: 53px; font-weight: 800; }
        .step-icon { position: relative; z-index: 2; width: 58px; height: 58px; display: flex; align-items: center; justify-content: center; border-radius: 17px; color: var(--red); background: var(--red-light); }
        .step-title { margin-top: 25px; font-size: 18px; font-weight: 800; }
        .step-text { margin-top: 11px; color: var(--text); font-size: 14px; line-height: 1.8; }

        /* =====================================================
           ABOUT
        ===================================================== */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 75px; }
        .about-media { position: relative; padding-bottom: 35px; }
        .about-main-image { width: 100%; height: 500px; object-fit: cover; border-radius: 35px; box-shadow: var(--shadow); background: #E7ECF1; }
        .about-small-image { position: absolute; width: 280px; height: 185px; right: -15px; bottom: 0; object-fit: cover; border: 5px solid white; border-radius: 22px; box-shadow: var(--shadow); background: #E7ECF1; }
        .about-badge { position: absolute; top: 25px; left: 25px; display: flex; align-items: center; gap: 11px; padding: 14px 16px; border-radius: 16px; background: var(--red); color: white; box-shadow: 0 12px 30px rgba(211, 47, 47, .24); }
        .about-badge-title { font-size: 13px; font-weight: 800; }
        .about-badge-subtitle { margin-top: 2px; color: #FFD4D4; font-size: 10px; }
        .about-features { margin-top: 30px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .about-feature { padding: 16px; display: flex; align-items: flex-start; gap: 13px; border: 1px solid var(--border); border-radius: 16px; background: white; }
        .about-feature-icon { width: 44px; height: 44px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 13px; color: var(--red); background: var(--red-light); }
        .about-feature-title { font-size: 13px; font-weight: 700; }
        .about-feature-text { margin-top: 3px; color: var(--muted); font-size: 11px; }

        /* =====================================================
           SERVICES
        ===================================================== */
        .services-grid { margin-top: 55px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
        .service-card { position: relative; display: block; height: 285px; overflow: hidden; border-radius: 25px; background: var(--dark); box-shadow: 0 10px 30px rgba(23, 32, 51, .08); transition: .3s ease; }
        .service-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); }
        .service-image { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
        .service-card:hover .service-image { transform: scale(1.06); }
        .service-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(10, 15, 25, .90), rgba(10, 15, 25, .18) 65%, transparent); }
        .service-content { position: absolute; left: 0; right: 0; bottom: 0; padding: 24px; }
        .service-icon { width: 48px; height: 48px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; border-radius: 14px; color: var(--red); background: white; }
        .service-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 15px; }
        .service-title { color: white; font-size: 20px; font-weight: 800; }
        .service-text { margin-top: 3px; color: rgba(255, 255, 255, .78); font-size: 11px; }
        .service-arrow { width: 38px; height: 38px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255, 255, 255, .12); border-radius: 50%; color: white; background: rgba(255, 255, 255, .13); }

        /* =====================================================
           PROVIDERS
        ===================================================== */
        .providers-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 30px; }
        .providers-grid { margin-top: 45px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .provider-card { display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--border); border-radius: 25px; background: white; transition: .3s; }
        .provider-card:hover { transform: translateY(-5px); box-shadow: var(--shadow); }
        .provider-image-wrap { position: relative; height: 240px; overflow: hidden; background: #EEF2F5; }
        .provider-image { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
        .provider-card:hover .provider-image { transform: scale(1.05); }
        .provider-type { position: absolute; top: 15px; right: 15px; padding: 7px 12px; border-radius: 30px; background: rgba(255, 255, 255, .94); color: var(--red); font-size: 11px; font-weight: 800; box-shadow: 0 8px 20px rgba(0, 0, 0, .08); text-transform: capitalize; }
        .provider-body { flex: 1; display: flex; flex-direction: column; padding: 22px; }
        .provider-title-row { display: flex; justify-content: space-between; gap: 15px; }
        .provider-title { font-size: 15px; font-weight: 800; }
        .provider-location { margin-top: 8px; display: flex; align-items: center; gap: 6px; color: var(--muted); font-size: 11px; }
        .provider-status { height: fit-content; flex-shrink: 0; display: inline-flex; align-items: center; gap: 5px; padding: 6px 10px; border-radius: 30px; color: var(--green); background: var(--green-light); font-size: 10px; font-weight: 700; }
        .provider-rating { display: inline-flex; align-items: center; gap: 5px; margin-top: 12px; color: #A16207; font-size: 12px; font-weight: 700; }
        .provider-ownership { display: inline-block; margin-top: 12px; margin-right: 5px; padding: 5px 9px; border-radius: 30px; background: #F1F5F9; color: #64748B; font-size: 10px; font-weight: 700; text-transform: capitalize; }
        .provider-ownership.is-green { color: var(--green); background: var(--green-light); }
        .provider-ownership.is-red { color: var(--red); background: var(--red-light); }
        .provider-footer { margin-top: auto; padding-top: 17px; }
        .provider-actions { margin-top: 18px; padding-top: 17px; border-top: 1px solid var(--border); display: flex; flex-wrap: wrap; gap: 8px 20px; }
        .providers-empty { grid-column: 1 / -1; padding: 40px 20px; text-align: center; color: var(--muted); border: 1px dashed var(--border); border-radius: 20px; background: white; }

        .clickable-profile { cursor: pointer; }
        .clickable-profile:focus-within { box-shadow: var(--shadow); }

        /* =====================================================
           DOCTORS
        ===================================================== */
        .doctors-grid { margin-top: 45px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
        .doctor-card { overflow: hidden; border: 1px solid var(--border); border-radius: 24px; background: white; transition: .3s; }
        .doctor-card:hover { transform: translateY(-5px); box-shadow: var(--shadow); }
        .doctor-photo { position: relative; height: 270px; overflow: hidden; background: #EEF2F5; }
        .doctor-photo img { width: 100%; height: 100%; object-fit: cover; object-position: top; transition: .4s; }
        .doctor-card:hover .doctor-photo img { transform: scale(1.04); }
        .doctor-rank { position: absolute; top: 15px; left: 15px; display: inline-flex; align-items: center; gap: 6px; padding: 7px 11px; border-radius: 30px; background: rgba(255, 255, 255, .95); color: var(--red); font-size: 10px; font-weight: 800; box-shadow: 0 7px 20px rgba(0, 0, 0, .08); }
        .doctor-body { padding: 20px; }
        .doctor-name { font-size: 15px; font-weight: 800; }
        .doctor-specialty { margin-top: 4px; color: var(--red); font-size: 11px; font-weight: 700; }
        .doctor-facility { margin-top: 10px; display: flex; align-items: center; gap: 6px; color: var(--muted); font-size: 11px; }
        .doctor-facility .text-link { font-size: 11px; font-weight: 600; color: var(--muted); }
        .doctor-facility .text-link:hover { color: var(--red); }
        .doctor-bottom { margin-top: 16px; padding-top: 14px; display: flex; align-items: center; justify-content: space-between; gap: 10px; border-top: 1px solid var(--border); }
        .doctor-bottom .provider-location { margin-top: 0; }
        .doctor-actions { margin-top: 14px; }

        /* =====================================================
           MAP
        ===================================================== */
        .health-map-section { position: relative; overflow: hidden; background: linear-gradient(180deg, #F7F9FC, #FFFFFF); }
        .map-toolbar { margin-top: 45px; margin-bottom: 17px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
        .map-legend { display: flex; flex-wrap: wrap; gap: 10px; }
        .map-legend-item { display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; border: 1px solid var(--border); border-radius: 30px; background: white; color: var(--text); font-size: 11px; font-weight: 600; transition: .2s; }
        button.map-legend-item:hover { border-color: #D6DDE6; }
        .map-legend-item[aria-pressed="false"] { opacity: .45; }
        .map-dot { width: 9px; height: 9px; border-radius: 50%; }
        .map-dot.hospital { background: var(--red); }
        .map-dot.clinic { background: #2563EB; }
        .map-dot.pharmacy { background: var(--green); }
        .map-dot.laboratory { background: #7C3AED; }
        .map-tools { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .map-count { color: var(--muted); font-size: 11px; }
        .map-hint { margin-bottom: 12px; color: var(--muted); font-size: 12px; text-align: center; }
        .map-wrap { position: relative; }
        #healthcareMap { width: 100%; height: 610px; overflow: hidden; border: 1px solid var(--border); border-radius: 30px; background: #E7ECF1; box-shadow: var(--shadow); z-index: 1; }
        .map-empty { position: absolute; inset: 0; z-index: 500; display: flex; align-items: center; justify-content: center; padding: 20px; text-align: center; border-radius: 30px; background: rgba(247, 249, 252, .82); color: var(--text); font-size: 14px; font-weight: 600; pointer-events: none; }
        .locate-status { margin-top: 12px; min-height: 18px; color: var(--muted); font-size: 12px; }
        .nearest-list { margin-top: 12px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; }
        .nearest-item { padding: 14px 16px; border: 1px solid var(--border); border-radius: 16px; background: white; }
        .nearest-name { font-size: 13px; font-weight: 800; }
        .nearest-meta { margin-top: 3px; color: var(--muted); font-size: 11px; text-transform: capitalize; }
        .nearest-actions { margin-top: 10px; display: flex; gap: 16px; flex-wrap: wrap; }
        .nearest-actions .text-link { font-size: 12px; }

        .afya-map-marker { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 3px solid white; border-radius: 50% 50% 50% 8px; color: white; box-shadow: 0 6px 16px rgba(0, 0, 0, .25); transform: rotate(-45deg); }
        .afya-map-marker span { transform: rotate(45deg); font-size: 14px; font-weight: 800; }
        .afya-map-marker.hospital { background: var(--red); }
        .afya-map-marker.clinic { background: #2563EB; }
        .afya-map-marker.pharmacy { background: var(--green); }
        .afya-map-marker.laboratory { background: #7C3AED; }
        .afya-map-marker.other { background: #475569; }

        .leaflet-tooltip.afya-tooltip { padding: 6px 10px; border: none; border-radius: 10px; color: var(--dark); font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 700; box-shadow: 0 8px 24px rgba(15, 23, 42, .18); }

        /* =====================================================
           AUDIENCE
        ===================================================== */
        .audience-grid { margin-top: 50px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .audience-card { padding: 38px; border: 1px solid var(--border); border-radius: 30px; background: white; box-shadow: 0 10px 30px rgba(23, 32, 51, .05); }
        .audience-card.dark { color: white; background: linear-gradient(145deg, #172033, #111827); border: none; }
        .audience-icon { width: 58px; height: 58px; display: flex; align-items: center; justify-content: center; border-radius: 17px; color: var(--red); background: var(--red-light); }
        .audience-card.dark .audience-icon { color: white; background: rgba(255, 255, 255, .10); }
        .audience-title { margin-top: 24px; font-size: 25px; font-weight: 800; }
        .audience-text { max-width: 530px; margin-top: 9px; color: var(--text); font-size: 14px; line-height: 1.8; }
        .audience-card.dark .audience-text { color: #BAC2CF; }
        .benefit-grid { margin-top: 26px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 13px; }
        .benefit-item { display: flex; align-items: center; gap: 9px; font-size: 12px; }
        .benefit-check { width: 26px; height: 26px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: var(--green); background: var(--green-light); }
        .audience-card.dark .benefit-check { color: white; background: var(--green); }

        /* =====================================================
           EMERGENCY
        ===================================================== */
        .emergency-card { position: relative; min-height: 430px; overflow: hidden; border-radius: 37px; background: #751717; box-shadow: 0 30px 70px rgba(118, 20, 20, .22); }
        .emergency-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .emergency-overlay { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(117, 23, 23, .98), rgba(131, 27, 27, .89) 50%, rgba(131, 27, 27, .45)); }
        .emergency-content { position: relative; z-index: 2; max-width: 680px; padding: 75px 65px; color: white; }
        .emergency-label { display: flex; align-items: center; gap: 9px; color: #FFCDCD; font-size: 13px; font-weight: 700; }
        .pulse-dot { width: 10px; height: 10px; border-radius: 50%; background: #FF8D8D; animation: pulse 1.8s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(255, 141, 141, .7); } 70% { box-shadow: 0 0 0 10px rgba(255, 141, 141, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 141, 141, 0); } }
        .emergency-title { margin-top: 20px; font-size: clamp(30px, 4vw, 50px); line-height: 1.15; font-weight: 800; }
        .emergency-text { margin-top: 18px; color: #FFEAEA; font-size: 16px; line-height: 1.8; }
        .emergency-actions { margin-top: 26px; display: flex; flex-wrap: wrap; gap: 12px; }
        .emergency-primary { color: var(--red); background: white; }
        .emergency-note { margin-top: 18px; color: #FFDADA; font-size: 12px; line-height: 1.7; }

        /* =====================================================
           ARTICLES
        ===================================================== */
        .articles-grid { margin-top: 45px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .article-card { overflow: hidden; border: 1px solid var(--border); border-radius: 25px; background: white; transition: .3s; }
        .article-card:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
        .article-image-wrap { height: 225px; overflow: hidden; background: #EEF2F5; }
        .article-image { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
        .article-card:hover .article-image { transform: scale(1.05); }
        .article-body { padding: 24px; }
        .article-category { color: var(--red); font-size: 10px; font-weight: 800; letter-spacing: .08em; }
        .article-title { margin-top: 10px; font-size: 17px; font-weight: 800; line-height: 1.55; }
        .article-text { margin-top: 8px; color: var(--text); font-size: 13px; line-height: 1.8; }

        /* =====================================================
           APP DOWNLOAD
        ===================================================== */
        .app-section { position: relative; overflow: hidden; background: linear-gradient(180deg, white, #F4F7FB); }
        .app-card { position: relative; overflow: hidden; padding: 65px; border: 1px solid rgba(255, 255, 255, .08); border-radius: 36px; color: white; background: linear-gradient(135deg, #101828, #1D2939 50%, #0F172A); box-shadow: 0 30px 80px rgba(16, 24, 40, .25); }
        .app-card::before { content: ""; position: absolute; width: 420px; height: 420px; top: -220px; right: 10%; border-radius: 50%; background: radial-gradient(circle, rgba(211, 47, 47, .35), transparent 70%); filter: blur(30px); pointer-events: none; }
        .app-grid { position: relative; z-index: 2; display: grid; grid-template-columns: 1.1fr .9fr; align-items: center; gap: 65px; }
        .app-title { margin-top: 20px; font-size: clamp(30px, 4vw, 48px); line-height: 1.15; font-weight: 800; }
        .app-text { max-width: 620px; margin-top: 18px; color: #C7D0DD; font-size: 14px; line-height: 1.9; }
        .app-features { margin-top: 28px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 13px; }
        .app-feature { display: flex; align-items: center; gap: 9px; color: #D6DEE8; font-size: 12px; }
        .app-feature-check { width: 27px; height: 27px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; background: var(--green); }
        .app-actions { margin-top: 30px; display: flex; flex-wrap: wrap; gap: 12px; }
        .store-button { position: relative; display: inline-flex; align-items: center; gap: 11px; padding: 12px 18px; border: 1px solid rgba(255, 255, 255, .17); border-radius: 13px; background: rgba(255, 255, 255, .08); color: white; opacity: .85; cursor: default; }
        .store-small { color: #AEB8C6; font-size: 9px; }
        .store-title { font-size: 13px; font-weight: 700; }
        .store-soon { margin-left: 4px; padding: 3px 8px; border-radius: 20px; background: var(--red); color: white; font-size: 9px; font-weight: 800; letter-spacing: .04em; }

        .phone-mockup { position: relative; width: 280px; height: 555px; margin-inline: auto; padding: 9px; border: 7px solid #111827; border-radius: 40px; background: white; box-shadow: 0 30px 60px rgba(0, 0, 0, .40); }
        .phone-screen { width: 100%; height: 100%; padding: 22px 18px; overflow: hidden; border-radius: 28px; color: var(--dark); background: linear-gradient(180deg, #FFF8F8, white 45%, #F4FBF7); }
        .phone-logo { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; }
        .phone-logo-mark { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 9px; color: white; background: var(--red); }
        .phone-headline { margin-top: 32px; font-size: 23px; font-weight: 800; line-height: 1.2; }
        .phone-search { margin-top: 28px; padding: 12px; display: flex; align-items: center; gap: 8px; border: 1px solid var(--border); border-radius: 13px; color: var(--muted); background: white; font-size: 10px; }
        .phone-service-grid { margin-top: 20px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .phone-service { padding: 15px 8px; text-align: center; border-radius: 13px; color: var(--red); background: var(--red-light); font-size: 9px; font-weight: 700; }
        .phone-map-card { margin-top: 24px; padding: 18px; border-radius: 18px; color: white; background: linear-gradient(135deg, var(--red), var(--red-dark)); }
        .phone-map-card small { display: block; font-size: 10px; opacity: .75; }
        .phone-map-card strong { display: block; margin-top: 6px; font-size: 15px; font-weight: 800; }

        /* =====================================================
           FAQ
        ===================================================== */
        .faq-list { max-width: 850px; margin: 45px auto 0; display: grid; gap: 13px; }
        .faq-item { padding: 21px 23px; border: 1px solid var(--border); border-radius: 17px; background: #FBFCFE; }
        .faq-item[open] { background: white; box-shadow: 0 10px 30px rgba(23, 32, 51, .05); }
        .faq-item summary { display: flex; align-items: center; justify-content: space-between; gap: 20px; list-style: none; cursor: pointer; font-size: 14px; font-weight: 700; }
        .faq-item summary::-webkit-details-marker { display: none; }
        .faq-plus { width: 31px; height: 31px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: white; color: var(--text); transition: .2s; }
        .faq-item[open] .faq-plus { transform: rotate(45deg); }
        .faq-answer { max-width: 740px; margin-top: 15px; color: var(--text); font-size: 13px; line-height: 1.8; }

        /* =====================================================
           PROVIDER CTA
        ===================================================== */
        .provider-cta { position: relative; overflow: hidden; padding: 55px; border-radius: 37px; color: white; background: linear-gradient(135deg, var(--green), var(--green-dark)); }
        .provider-cta::before { content: ""; position: absolute; width: 300px; height: 300px; top: -150px; right: -100px; border: 55px solid rgba(255, 255, 255, .05); border-radius: 50%; pointer-events: none; }
        .provider-cta-inner { position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; gap: 50px; }
        .provider-cta-content { max-width: 720px; }
        .provider-cta-label { display: flex; align-items: center; gap: 8px; color: #B8F0D1; font-size: 12px; font-weight: 700; }
        .provider-cta-title { margin-top: 12px; font-size: clamp(26px, 4vw, 42px); line-height: 1.2; font-weight: 800; }
        .provider-cta-text { margin-top: 14px; color: #CCF1DC; font-size: 14px; line-height: 1.8; }
        .provider-benefits { margin-top: 20px; display: flex; flex-wrap: wrap; gap: 16px; color: #D4F4E2; font-size: 12px; }
        .provider-benefit { display: flex; align-items: center; gap: 6px; }
        .provider-cta-button { flex-shrink: 0; background: white; color: var(--green); }

        /* =====================================================
           FOOTER
        ===================================================== */
        .footer { padding-top: 70px; background: var(--dark-2); color: white; }
        .footer-grid { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1.2fr; gap: 60px; }
        .footer-description { max-width: 330px; margin-top: 20px; color: #9AA4B2; font-size: 13px; line-height: 1.8; }
        .social-links { margin-top: 22px; display: flex; gap: 9px; }
        .social-link { width: 39px; height: 39px; display: flex; align-items: center; justify-content: center; border-radius: 11px; background: rgba(255, 255, 255, .05); color: #A8B0BD; transition: .2s; }
        .social-link i { font-size: 17px; }
        .social-link:hover { background: var(--red); color: white; }
        .footer-heading { font-size: 14px; font-weight: 700; }
        .footer-links { margin-top: 20px; display: grid; gap: 12px; list-style: none; }
        .footer-links a { color: #9AA4B2; font-size: 12px; transition: .2s; }
        .footer-links a:hover { color: white; }
        .footer-contact { margin-top: 20px; display: grid; gap: 15px; }
        .footer-contact-item { display: flex; align-items: flex-start; gap: 10px; color: #9AA4B2; font-size: 12px; }
        .footer-contact-item a:hover { color: white; }
        .footer-contact-icon { color: var(--red); flex-shrink: 0; }
        .footer-bottom { margin-top: 55px; padding: 23px 0; display: flex; align-items: center; justify-content: space-between; gap: 25px; border-top: 1px solid rgba(255, 255, 255, .08); color: #8A94A4; font-size: 11px; }
        .footer-legal { display: flex; gap: 20px; flex-wrap: wrap; }
        .footer-legal a:hover { color: white; }

        /* =====================================================
           CHATBOT
        ===================================================== */
        .chat-widget { position: fixed; right: 25px; bottom: 25px; z-index: 900; }
        .chat-launcher { width: 62px; height: 62px; display: flex; align-items: center; justify-content: center; border: none; border-radius: 50%; background: linear-gradient(135deg, var(--red), var(--red-dark)); color: white; box-shadow: 0 12px 30px rgba(211, 47, 47, .45); }
        .chat-window { display: none; position: absolute; right: 0; bottom: 76px; width: 370px; overflow: hidden; border: 1px solid var(--border); border-radius: 22px; background: white; box-shadow: var(--shadow-lg); }
        .chat-window.open { display: block; }
        .chat-header { padding: 17px; display: flex; align-items: center; justify-content: space-between; color: white; background: linear-gradient(135deg, var(--red), var(--red-dark)); }
        .chat-title { font-size: 13px; font-weight: 800; }
        .chat-subtitle { color: #FFDADA; font-size: 10px; }
        .chat-close { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: none; border-radius: 9px; color: white; background: rgba(255, 255, 255, .12); }
        .chat-body { display: flex; flex-direction: column; gap: 10px; min-height: 270px; max-height: min(370px, 55vh); overflow-y: auto; overscroll-behavior: contain; padding: 18px; background: #F8FAFC; }
        .chat-message { width: fit-content; max-width: 88%; padding: 11px 13px; border-radius: 14px 14px 14px 4px; background: white; color: #475569; font-size: 12px; line-height: 1.65; box-shadow: 0 4px 15px rgba(0, 0, 0, .04); overflow-wrap: anywhere; }
        .chat-message.user { align-self: flex-end; border-radius: 14px 14px 4px 14px; background: var(--red); color: white; }
        .chat-results { margin-top: 10px; display: grid; gap: 8px; }
        .chat-result { width: 100%; padding: 9px 11px; display: block; text-align: left; border: 1px solid var(--border); border-radius: 11px; background: #FBFCFE; color: inherit; }
        button.chat-result:hover { border-color: #F2BBBB; background: #FFF7F7; }
        .chat-result strong { display: block; font-size: 12px; color: var(--dark); }
        .chat-result small { display: block; margin-top: 2px; font-size: 10px; color: var(--muted); text-transform: capitalize; }
        .chat-quick { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px; }
        .chat-quick button { padding: 6px 10px; border: 1px solid var(--border); border-radius: 20px; background: #FBFCFE; color: #647084; font-size: 11px; }
        .chat-quick button:hover { border-color: #F2BBBB; color: var(--red); }
        .chat-footer { padding: 11px; border-top: 1px solid var(--border); }
        .chat-form { display: flex; gap: 8px; }
        .chat-input { flex: 1; min-width: 0; padding: 10px 12px; border: 1px solid var(--border); border-radius: 11px; outline: none; font-size: 16px; }
        .chat-input:focus { border-color: #F2BBBB; }
        .chat-send { width: 42px; border: none; border-radius: 11px; background: var(--red); color: white; }

        /* =====================================================
           PROFILE + AUTH + BOOKING MODALS
        ===================================================== */
        .afya-modal {
            position: fixed; inset: 0; z-index: 12000;
            display: none; align-items: center; justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, .62);
            backdrop-filter: blur(8px);
        }
        .afya-modal.open { display: flex; }

        .afya-modal-panel {
            width: min(760px, 100%);
            max-height: calc(100vh - 48px); max-height: calc(100dvh - 48px);
            overflow-y: auto; overscroll-behavior: contain;
            border-radius: 28px; background: white;
            box-shadow: 0 35px 100px rgba(15, 23, 42, .30);
            animation: afyaModalIn .22s ease;
        }
        .afya-modal-panel.small { width: min(560px, 100%); }

        @keyframes afyaModalIn {
            from { opacity: 0; transform: translateY(15px) scale(.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .afya-modal-header {
            position: sticky; top: 0; z-index: 5;
            padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; gap: 20px;
            border-bottom: 1px solid var(--border); background: white;
        }
        .afya-modal-brand { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 800; }
        .afya-modal-brand-icon { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 12px; color: white; background: linear-gradient(135deg, var(--red), var(--red-dark)); }
        .afya-modal-close { width: 39px; height: 39px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: none; border-radius: 12px; color: var(--text); background: var(--light); }
        .afya-modal-close:hover { color: var(--red); background: var(--red-light); }

        .afya-profile-cover { position: relative; height: 260px; overflow: hidden; background: #EEF2F5; }
        .afya-profile-cover img { width: 100%; height: 100%; object-fit: cover; }
        .afya-profile-cover::after { content: ""; position: absolute; inset: 0; background: linear-gradient(to top, rgba(15, 23, 42, .38), transparent 55%); }
        .afya-profile-body { padding: 28px; }
        .afya-profile-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; }
        .afya-profile-title { font-size: 25px; font-weight: 800; line-height: 1.3; }
        .afya-profile-specialty { margin-top: 5px; color: var(--red); font-size: 12px; font-weight: 700; text-transform: capitalize; }
        .afya-profile-meta { margin-top: 9px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px; color: var(--muted); font-size: 11px; }
        .afya-profile-meta .good { color: var(--green); font-weight: 700; }
        .afya-profile-meta .stars { color: #A16207; font-weight: 700; }
        .afya-profile-description { margin-top: 22px; color: var(--text); font-size: 13px; line-height: 1.85; white-space: pre-line; }
        .afya-profile-details { margin-top: 18px; display: grid; gap: 8px; color: var(--text); font-size: 12px; }
        .afya-profile-details strong { color: var(--dark); }
        .afya-profile-tags { margin-top: 22px; display: flex; flex-wrap: wrap; gap: 8px; }
        .afya-profile-tag { padding: 7px 11px; border: none; border-radius: 30px; color: var(--green); background: var(--green-light); font-size: 10px; font-weight: 700; }
        button.afya-profile-tag:hover { background: #D7F3E3; }
        .afya-profile-actions { margin-top: 28px; padding-top: 22px; display: flex; flex-wrap: wrap; gap: 11px; border-top: 1px solid var(--border); }

        .afya-auth-intro { padding: 28px 28px 10px; text-align: center; }
        .afya-auth-icon { width: 62px; height: 62px; margin-inline: auto; display: flex; align-items: center; justify-content: center; border-radius: 19px; color: var(--red); background: var(--red-light); }
        .afya-auth-title { margin-top: 17px; font-size: 23px; font-weight: 800; }
        .afya-auth-description { margin: 7px auto 0; max-width: 430px; color: var(--text); font-size: 12px; line-height: 1.7; }

        .afya-auth-tabs { margin: 20px 28px 0; padding: 5px; display: grid; grid-template-columns: 1fr 1fr; border-radius: 14px; background: #F1F5F9; }
        .afya-auth-tab { padding: 10px; border: none; border-radius: 10px; color: #64748B; background: transparent; font-size: 12px; font-weight: 700; }
        .afya-auth-tab.active { color: var(--red); background: white; box-shadow: 0 5px 18px rgba(15, 23, 42, .06); }

        .afya-auth-panel { display: none; padding: 24px 28px 30px; }
        .afya-auth-panel.active { display: block; }

        .afya-form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .afya-form-group { display: grid; gap: 7px; align-content: start; }
        .afya-form-group.full { grid-column: 1 / -1; }
        .afya-label { color: #475569; font-size: 11px; font-weight: 700; }

        .afya-input, .afya-select, .afya-textarea {
            width: 100%; padding: 12px 13px;
            border: 1px solid var(--border); border-radius: 12px; outline: none;
            color: var(--dark); background: white; font-size: 13px; transition: .2s;
        }
        .afya-input:focus, .afya-select:focus, .afya-textarea:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(211, 47, 47, .08); }
        .afya-textarea { min-height: 85px; resize: vertical; }

        .afya-details { grid-column: 1 / -1; padding: 14px 16px; border: 1px solid var(--border); border-radius: 14px; background: #FBFCFE; }
        .afya-details summary { cursor: pointer; font-size: 12px; font-weight: 700; color: var(--dark); }
        .afya-details-note { margin-top: 6px; color: var(--muted); font-size: 11px; line-height: 1.6; }
        .afya-details .afya-form-grid { margin-top: 14px; }

        .afya-consent { display: flex; align-items: flex-start; gap: 9px; color: var(--text); font-size: 11px; line-height: 1.6; }
        .afya-consent input { margin-top: 3px; flex-shrink: 0; }
        .afya-consent a { color: var(--red); font-weight: 600; }

        .afya-auth-message { display: none; margin-bottom: 14px; padding: 10px 12px; border-radius: 10px; font-size: 12px; line-height: 1.6; }
        .afya-auth-message.error { display: block; color: #991B1B; background: #FEF2F2; }
        .afya-auth-message.success { display: block; color: #166534; background: #F0FDF4; }

        .afya-auth-footer { margin-top: 16px; text-align: center; color: var(--muted); font-size: 11px; }
        .afya-auth-footer a { color: var(--red); font-weight: 700; }

        .afya-booking-summary { margin-bottom: 20px; padding: 16px; border-radius: 15px; background: var(--light); }
        .afya-booking-summary-title { font-size: 13px; font-weight: 800; }
        .afya-booking-summary-text { margin-top: 3px; color: var(--text); font-size: 11px; }

        /* =====================================================
           RESPONSIVE
        ===================================================== */
        @media (max-width: 1240px) {
            .desktop-nav, .nav-actions { display: none; }
            .menu-button { display: flex; }
        }

        @media (max-width: 1100px) {
            .hero-grid, .about-grid, .app-grid { grid-template-columns: 1fr; }
            .hero-grid { gap: 50px; min-height: 0; }
            .hero-image-wrap { min-height: 520px; }
            .feature-grid { grid-template-columns: repeat(2, 1fr); }
            .feature-item:nth-child(2n) { border-right: none; }
            .feature-item:nth-child(-n+2) { border-bottom: 1px solid var(--border); }
            .services-grid, .providers-grid, .articles-grid, .doctors-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: repeat(2, 1fr); gap: 45px; }
            .provider-cta-inner { align-items: flex-start; flex-direction: column; }
            .about-grid { gap: 55px; }
        }

        @media (max-width: 900px) {
            .audience-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 760px) {
            .container { width: min(100% - 32px, 1360px); }
            .section { padding: 70px 0; }
            .hero { padding-top: 105px; padding-bottom: 90px; }
            .hero-description { font-size: 15px; }
            .hero-image-wrap { min-height: 410px; }
            .floating-card.top { right: 10px; }
            .floating-card.bottom { left: 10px; }
            .hero-highlight { white-space: normal; }
            .hero-highlight svg { display: none; }
            .search-box { flex-direction: column; }
            .search-box .btn { width: 100%; }
            .providers-header { flex-direction: column; align-items: flex-start; gap: 18px; }
            .services-grid, .providers-grid, .doctors-grid, .articles-grid { grid-template-columns: 1fr; }
            .about-small-image { display: none; }
            .about-main-image { height: 420px; }
            .about-features, .benefit-grid, .app-features { grid-template-columns: 1fr; }
            #healthcareMap { height: 460px; border-radius: 22px; }
            .map-empty { border-radius: 22px; }
            .map-toolbar { align-items: flex-start; flex-direction: column; }
            .emergency-content { padding: 55px 28px; }
            .emergency-actions .btn { flex: 1 1 100%; }
            .app-card { padding: 40px 25px; }
            .provider-cta { padding: 40px 28px; }
            .audience-card { padding: 28px; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-bottom { align-items: flex-start; flex-direction: column; }
        }

        @media (max-width: 650px) {
            .afya-modal { padding: 10px; align-items: flex-end; }
            .afya-modal-panel { max-height: calc(100vh - 20px); max-height: calc(100dvh - 20px); border-radius: 24px; }
            .afya-profile-cover { height: 210px; }
            .afya-profile-body { padding: 21px; }
            .afya-profile-heading { flex-direction: column; }
            .afya-form-grid { grid-template-columns: 1fr; }
            .afya-form-group.full { grid-column: auto; }
            .afya-auth-panel { padding: 22px 20px 26px; }
            .afya-auth-tabs { margin-inline: 20px; }
            .afya-input, .afya-select, .afya-textarea { font-size: 16px; }
            .afya-profile-actions .btn { width: 100%; }
        }

        @media (max-width: 520px) {
            .feature-grid { grid-template-columns: 1fr; }
            .feature-item { border-right: none; border-bottom: 1px solid var(--border); }
            .feature-item:last-child { border-bottom: none; }
            .hero-title { font-size: 34px; }
            .phone-mockup { width: 245px; height: 500px; }
            .floating-card { padding: 12px 14px; }
            .chat-widget { right: 16px; bottom: 16px; }
            .chat-window { position: fixed; left: 16px; right: 16px; bottom: 90px; width: auto; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }

        /* =====================================================
           SMART DIRECTORY MODAL
        ===================================================== */
        .service-card.service-browser-trigger { width: 100%; border: none; padding: 0; text-align: left; font-family: inherit; }
        .directory-panel { width: min(1120px, 100%); }
        .directory-body { padding: 28px; }
        .directory-hero { display: flex; align-items: flex-end; justify-content: space-between; gap: 22px; }
        .directory-title { margin-top: 12px; font-size: clamp(25px, 4vw, 38px); line-height: 1.15; font-weight: 800; letter-spacing: -.03em; }
        .directory-subtitle { margin-top: 8px; color: var(--text); font-size: 12px; }
        .directory-count { flex-shrink: 0; padding: 8px 12px; border-radius: 30px; color: var(--green); background: var(--green-light); font-size: 11px; font-weight: 700; }
        .directory-categories { margin-top: 24px; display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: thin; }
        .directory-category { flex: 0 0 auto; display: inline-flex; align-items: center; gap: 7px; padding: 10px 13px; border: 1px solid var(--border); border-radius: 30px; background: white; color: #64748B; font-size: 11px; font-weight: 700; }
        .directory-category.active { border-color: #F2B8B8; color: var(--red); background: var(--red-light); }
        .directory-search-row { margin-top: 20px; display: grid; grid-template-columns: 1fr auto; gap: 10px; }
        .directory-search-box { display: flex; align-items: center; gap: 10px; padding: 0 14px; border: 1px solid var(--border); border-radius: 14px; background: white; color: var(--muted); }
        .directory-search-box:focus-within { border-color: #F2BBBB; box-shadow: 0 0 0 4px rgba(211, 47, 47, .08); }
        .directory-search-box input { width: 100%; min-width: 0; padding: 13px 0; border: none; outline: none; color: var(--dark); background: transparent; }
        .directory-location-btn { display: inline-flex; align-items: center; gap: 8px; padding: 0 16px; border: 1px solid #CDEBDC; border-radius: 14px; background: var(--green-light); color: var(--green); font-size: 11px; font-weight: 700; }
        .directory-filters { margin-top: 14px; display: grid; grid-template-columns: 1.2fr 1.2fr auto auto; gap: 10px; align-items: end; }
        .directory-filter { display: grid; gap: 5px; }
        .directory-filter span { color: var(--muted); font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
        .directory-filter select { min-height: 43px; padding: 0 11px; border: 1px solid var(--border); border-radius: 12px; background: white; color: var(--dark); font-size: 11px; outline: none; }
        .directory-check { min-height: 43px; padding: 0 12px; display: flex; align-items: center; gap: 8px; border: 1px solid var(--border); border-radius: 12px; background: #FBFCFE; color: var(--text); font-size: 10px; font-weight: 600; }
        .directory-status { min-height: 22px; margin-top: 14px; color: var(--muted); font-size: 11px; }
        .directory-grid { margin-top: 8px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .directory-card { overflow: hidden; border: 1px solid var(--border); border-radius: 20px; background: white; transition: .2s; }
        .directory-card:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
        .directory-card-media { position: relative; height: 150px; overflow: hidden; background: #EEF2F5; }
        .directory-card-media img { width: 100%; height: 100%; object-fit: cover; }
        .directory-card-media.doctor { height: 122px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #FFF1F1, #ECF9F1); }
        .directory-card-media.doctor img { width: 88px; height: 88px; border-radius: 50%; object-fit: cover; object-position: top; border: 4px solid white; box-shadow: 0 8px 20px rgba(15,23,42,.12); }
        .directory-card-badge { position: absolute; top: 10px; right: 10px; padding: 5px 8px; border-radius: 20px; background: rgba(255,255,255,.95); color: var(--red); font-size: 9px; font-weight: 800; text-transform: capitalize; }
        .directory-card-body { padding: 15px; }
        .directory-card-name { font-size: 13px; font-weight: 800; }
        .directory-card-meta { margin-top: 5px; color: var(--muted); font-size: 10px; text-transform: capitalize; }
        .directory-card-foot { margin-top: 12px; padding-top: 11px; display: flex; align-items: center; justify-content: space-between; gap: 10px; border-top: 1px solid var(--border); }
        .directory-card-rating { color: #A16207; font-size: 10px; font-weight: 700; }
        .directory-empty { grid-column: 1 / -1; padding: 48px 20px; text-align: center; color: var(--muted); border: 1px dashed var(--border); border-radius: 18px; background: #FBFCFE; }

        /* =====================================================
           PREMIUM SOCIAL-STYLE PROFILE
        ===================================================== */
        .profile-panel { width: min(860px, 100%); }
        .social-profile-cover { position: relative; height: 185px; overflow: hidden; background: linear-gradient(135deg, #FFF1F1, #ECF9F1); }
        .social-profile-cover img { width: 100%; height: 100%; object-fit: cover; opacity: .30; filter: blur(1px); }
        .social-profile-cover::after { content: ''; position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(255,255,255,.06), rgba(23,32,51,.16)); }
        .social-profile-body { position: relative; padding: 0 30px 30px; }
        .social-profile-avatar-row { margin-top: -72px; position: relative; z-index: 2; display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; }
        .social-profile-avatar-wrap { position: relative; width: 148px; height: 148px; flex-shrink: 0; }
        .social-profile-avatar { width: 148px; height: 148px; object-fit: cover; object-position: top; border: 6px solid white; border-radius: 50%; background: #EEF2F5; box-shadow: 0 14px 36px rgba(15,23,42,.18); }
        .social-profile-check { position: absolute; right: 6px; bottom: 12px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border: 4px solid white; border-radius: 50%; color: white; background: var(--green); }
        .social-profile-identity { margin-top: 16px; }
        .social-profile-name-row { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
        .social-profile-name { font-size: clamp(22px, 4vw, 29px); font-weight: 800; line-height: 1.25; }
        .social-profile-subtitle { margin-top: 4px; color: var(--red); font-size: 12px; font-weight: 700; text-transform: capitalize; }
        .social-profile-meta { margin-top: 12px; display: flex; flex-wrap: wrap; gap: 10px 14px; color: var(--muted); font-size: 11px; }
        .social-profile-meta span { display: inline-flex; align-items: center; gap: 5px; }
        .social-profile-meta .rating { color: #A16207; font-weight: 700; }
        .social-profile-pills { margin-top: 14px; display: flex; flex-wrap: wrap; gap: 7px; }
        .social-pill { padding: 6px 10px; border-radius: 30px; background: #F1F5F9; color: #64748B; font-size: 9px; font-weight: 700; text-transform: capitalize; }
        .social-pill.green { color: var(--green); background: var(--green-light); }
        .social-pill.red { color: var(--red); background: var(--red-light); }
        .social-profile-actions { margin-top: 22px; display: grid; grid-template-columns: 1.45fr 1fr 1fr; gap: 9px; }
        .social-action { min-height: 48px; padding: 12px 13px; display: flex; align-items: center; justify-content: center; gap: 7px; border: 1px solid var(--border); border-radius: 13px; background: white; color: var(--dark); font-size: 11px; font-weight: 700; }
        .social-action.primary { border: none; color: white; background: linear-gradient(135deg, var(--red), var(--red-dark)); box-shadow: 0 10px 25px rgba(211,47,47,.20); }
        .social-action.green { border-color: #CDEBDC; color: var(--green); background: var(--green-light); }
        .social-stats { margin-top: 24px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .social-stat { padding: 14px; border: 1px solid var(--border); border-radius: 14px; background: #FAFBFD; }
        .social-stat small { display: block; color: var(--muted); font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .social-stat strong { display: block; margin-top: 4px; color: var(--dark); font-size: 11px; }
        .social-section { margin-top: 26px; padding-top: 22px; border-top: 1px solid var(--border); }
        .social-section h3 { font-size: 15px; font-weight: 800; }
        .social-section p { margin-top: 8px; color: var(--text); font-size: 12px; line-height: 1.85; white-space: pre-line; }
        .social-tags { margin-top: 13px; display: flex; flex-wrap: wrap; gap: 7px; }
        .social-tag { padding: 7px 10px; border: none; border-radius: 30px; color: var(--green); background: var(--green-light); font-size: 9px; font-weight: 700; }
        .social-doctors { margin-top: 13px; display: grid; gap: 9px; }
        .social-doctor-row { width: 100%; padding: 10px; display: flex; align-items: center; gap: 11px; border: 1px solid var(--border); border-radius: 14px; background: #FBFCFE; text-align: left; color: inherit; }
        .social-doctor-row:hover { border-color: #F2BBBB; background: #FFF8F8; }
        .social-doctor-row img { width: 50px; height: 50px; flex-shrink: 0; border-radius: 50%; object-fit: cover; object-position: top; }
        .social-doctor-row strong { display: block; font-size: 11px; }
        .social-doctor-row small { display: block; margin-top: 2px; color: var(--red); font-size: 9px; }
        .social-doctor-rating { margin-left: auto; color: #A16207; font-size: 10px; font-weight: 700; }

        /* =====================================================
           AMBULANCE / EMERGENCY MODAL
        ===================================================== */
        .emergency-panel { width: min(930px, 100%); }
        .emergency-modal-header .afya-modal-brand-icon { background: linear-gradient(135deg, #D32F2F, #8E1616); }
        .ambulance-body { padding: 28px; }
        .ambulance-alert { display: flex; gap: 14px; padding: 17px; border: 1px solid #F6CACA; border-radius: 16px; background: #FFF4F4; }
        .ambulance-alert-icon { width: 46px; height: 46px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 14px; color: var(--red); background: white; }
        .ambulance-alert strong { font-size: 13px; }
        .ambulance-alert p { margin-top: 4px; color: #7F1D1D; font-size: 11px; line-height: 1.7; }
        .ambulance-actions-top { margin-top: 18px; display: flex; flex-wrap: wrap; gap: 9px; }
        .ambulance-location-status { min-height: 22px; margin-top: 16px; color: var(--muted); font-size: 11px; }
        .ambulance-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 13px; }
        .ambulance-card { padding: 16px; border: 1px solid var(--border); border-radius: 17px; background: white; }
        .ambulance-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .ambulance-name { font-size: 13px; font-weight: 800; }
        .ambulance-meta { margin-top: 4px; color: var(--muted); font-size: 10px; text-transform: capitalize; }
        .ambulance-distance { padding: 5px 8px; border-radius: 20px; color: var(--green); background: var(--green-light); font-size: 9px; font-weight: 700; }
        .ambulance-card-actions { margin-top: 13px; display: flex; flex-wrap: wrap; gap: 8px; }
        .ambulance-mini-btn { padding: 8px 10px; display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--border); border-radius: 10px; background: #FBFCFE; color: var(--dark); font-size: 9px; font-weight: 700; }
        .ambulance-mini-btn.primary { border-color: transparent; color: white; background: var(--red); }
        .ambulance-knowledge { margin-top: 24px; padding-top: 22px; border-top: 1px solid var(--border); }
        .ambulance-knowledge h3 { font-size: 15px; font-weight: 800; }
        .ambulance-steps { margin-top: 13px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .ambulance-steps div { padding: 13px; border: 1px solid var(--border); border-radius: 14px; background: #FAFBFD; }
        .ambulance-steps span { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; background: var(--red); font-size: 10px; font-weight: 800; }
        .ambulance-steps strong { display: block; margin-top: 9px; font-size: 10px; }
        .ambulance-steps small { display: block; margin-top: 4px; color: var(--muted); font-size: 9px; line-height: 1.6; }

        /* =====================================================
           QUICKBOT
        ===================================================== */
        .quickbot-widget { z-index: 1100; }
        .quickbot-header-left { display: flex; align-items: center; gap: 10px; }
        .quickbot-avatar { position: relative; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 13px; color: var(--red); background: white; }
        .quickbot-online { position: absolute; right: -1px; bottom: -1px; width: 11px; height: 11px; border: 2px solid var(--red); border-radius: 50%; background: #4ADE80; }
        .quickbot-launcher { position: relative; animation: quickbotFloat 3s ease-in-out infinite; }
        @keyframes quickbotFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
        .quickbot-badge { position: absolute; top: -3px; right: -3px; min-width: 20px; height: 20px; padding: 0 5px; display: flex; align-items: center; justify-content: center; border: 2px solid white; border-radius: 20px; background: var(--green); color: white; font-size: 9px; font-weight: 800; }
        .quickbot-nudge { position: absolute; right: 72px; bottom: 8px; width: max-content; max-width: 220px; padding: 9px 12px; border: 1px solid var(--border); border-radius: 12px; background: white; color: var(--dark); box-shadow: var(--shadow); font-size: 10px; font-weight: 700; opacity: 0; transform: translateX(8px); pointer-events: none; transition: .25s; }
        .quickbot-nudge.show { opacity: 1; transform: translateX(0); }
        .quickbot-welcome strong { color: var(--dark); }
        .quickbot-ping { animation: quickbotPing .55s ease; }
        @keyframes quickbotPing { 0% { box-shadow: 0 0 0 0 rgba(211,47,47,.45); } 100% { box-shadow: 0 0 0 18px rgba(211,47,47,0); } }

        @media (max-width: 900px) {
            .directory-grid { grid-template-columns: repeat(2, 1fr); }
            .directory-filters { grid-template-columns: repeat(2, 1fr); }
            .ambulance-steps { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 650px) {
            .directory-body, .ambulance-body { padding: 20px; }
            .directory-hero { align-items: flex-start; flex-direction: column; }
            .directory-search-row { grid-template-columns: 1fr; }
            .directory-filters, .directory-grid, .ambulance-grid, .ambulance-steps, .social-stats { grid-template-columns: 1fr; }
            .social-profile-cover { height: 145px; }
            .social-profile-body { padding: 0 20px 24px; }
            .social-profile-avatar-row { margin-top: -55px; }
            .social-profile-avatar-wrap, .social-profile-avatar { width: 112px; height: 112px; }
            .social-profile-check { width: 30px; height: 30px; }
            .social-profile-actions { grid-template-columns: 1fr; }
            .quickbot-nudge { display: none; }
        }

        /* =====================================================
           MULTILINGUAL HERO
        ===================================================== */
        .hero-language-row { width: fit-content; margin-top: 13px; display: flex; align-items: center; gap: 8px; padding: 7px 10px; border: 1px solid var(--border); border-radius: 30px; background: rgba(255,255,255,.82); color: var(--muted); font-size: 10px; box-shadow: 0 6px 20px rgba(23,32,51,.04); }
        .hero-language-row > span { display: inline-flex; align-items: center; gap: 5px; font-weight: 700; color: var(--dark); }
        .hero-language-select { border: none; outline: none; background: transparent; color: var(--red); font-size: 10px; font-weight: 800; cursor: pointer; }
        .hero-language-row small { color: var(--green); font-weight: 700; }

        /* =====================================================
           GUIDED CARE SLIDER
        ===================================================== */
        .guided-care-section { overflow: hidden; }
        .guided-slider-controls { display: flex; gap: 8px; }
        .guided-arrow { width: 43px; height: 43px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); border-radius: 13px; background: white; color: var(--dark); transition: .2s; }
        .guided-arrow:hover { color: white; background: var(--red); border-color: var(--red); }
        .guided-slider-shell { margin-top: 42px; overflow: hidden; border-radius: 30px; }
        .guided-slider-track { display: flex; transition: transform .55s cubic-bezier(.2,.8,.2,1); will-change: transform; }
        .guided-slide { position: relative; flex: 0 0 100%; min-height: 470px; overflow: hidden; border-radius: 30px; background: var(--dark); }
        .guided-slide-image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .guided-slide-overlay { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(17,24,39,.92), rgba(17,24,39,.68) 52%, rgba(17,24,39,.20)); }
        .guided-slide-content { position: relative; z-index: 2; max-width: 670px; min-height: 470px; padding: 70px 64px; display: flex; flex-direction: column; justify-content: center; color: white; }
        .guided-kicker { width: fit-content; display: inline-flex; align-items: center; gap: 7px; padding: 8px 12px; border: 1px solid rgba(255,255,255,.20); border-radius: 30px; background: rgba(255,255,255,.10); color: #D1FAE5; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; }
        .guided-slide h3 { margin-top: 18px; font-size: clamp(30px, 4vw, 48px); line-height: 1.12; font-weight: 800; }
        .guided-slide p { margin-top: 15px; max-width: 600px; color: #E5E7EB; font-size: 14px; line-height: 1.85; }
        .guided-slide .btn { margin-top: 27px; width: fit-content; }
        .guided-dots { margin-top: 18px; display: flex; justify-content: center; gap: 7px; }
        .guided-dot { width: 8px; height: 8px; padding: 0; border: none; border-radius: 50%; background: #CBD5E1; transition: .2s; }
        .guided-dot.active { width: 25px; border-radius: 20px; background: var(--red); }

        /* =====================================================
           CONDITION MODAL
        ===================================================== */
        .condition-panel { width: min(1100px, 100%); }
        .condition-body { padding: 28px; }
        .condition-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; }
        .condition-heading h2 { margin-top: 12px; font-size: clamp(26px, 4vw, 38px); line-height: 1.15; font-weight: 800; }
        .condition-heading p { margin-top: 8px; max-width: 720px; color: var(--text); font-size: 12px; line-height: 1.8; }
        .condition-search-row { margin-top: 22px; display: grid; grid-template-columns: 1fr auto; gap: 10px; }
        .condition-filter-row { margin-top: 12px; display: grid; grid-template-columns: 1.15fr auto auto auto; gap: 9px; align-items: end; }
        .condition-grid { margin-top: 10px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .condition-disclaimer { margin-top: 18px; padding: 13px 15px; display: flex; align-items: flex-start; gap: 8px; border: 1px solid #F3D2D2; border-radius: 14px; background: #FFF8F8; color: #7F1D1D; font-size: 10px; line-height: 1.65; }

        /* =====================================================
           REPORT ISSUE / SCAM
        ===================================================== */
        .report-strip-section { padding: 0 0 70px; }
        .report-strip { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 16px; padding: 20px 22px; border: 1px solid var(--border); border-radius: 22px; background: linear-gradient(135deg, #FFF8F8, #FFFFFF 50%, #F4FBF7); box-shadow: var(--shadow); }
        .report-strip-icon { width: 52px; height: 52px; display: flex; align-items: center; justify-content: center; border-radius: 15px; color: var(--red); background: var(--red-light); }
        .report-strip strong { display: block; font-size: 14px; }
        .report-strip span { display: block; margin-top: 3px; color: var(--text); font-size: 11px; }
        .report-strip-btn { color: var(--red); background: white; border: 1px solid #F0C7C7; }
        .report-modal-body { padding: 26px; }
        .report-modal-intro { margin-bottom: 18px; color: var(--text); font-size: 11px; line-height: 1.75; }

        /* =====================================================
           QUICKBOT LANGUAGE + ATTENTION
        ===================================================== */
        .quickbot-language-bar { padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 10px; border-bottom: 1px solid var(--border); background: #FFFDFD; color: var(--muted); font-size: 10px; }
        .quickbot-language-bar label { display: inline-flex; align-items: center; gap: 6px; font-weight: 700; color: var(--dark); }
        .quickbot-language-bar select { max-width: 150px; border: 1px solid var(--border); border-radius: 9px; padding: 5px 8px; background: white; color: var(--red); font-size: 10px; font-weight: 700; outline: none; }
        .quickbot-launcher.quickbot-yarn { animation: quickbotYarn .95s ease 3, quickbotFloat 3s ease-in-out infinite; }
        @keyframes quickbotYarn { 0%,100% { transform: scale(1) rotate(0); } 35% { transform: scale(1.12) rotate(-7deg); } 70% { transform: scale(1.08) rotate(7deg); } }
        .chat-google-link { margin-top: 9px; display: inline-flex; align-items: center; gap: 6px; color: var(--red); font-size: 10px; font-weight: 700; }

        @media (max-width: 900px) {
            .condition-grid { grid-template-columns: repeat(2, 1fr); }
            .condition-filter-row { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 650px) {
            .guided-slider-shell { border-radius: 24px; }
            .guided-slide { min-height: 500px; border-radius: 24px; }
            .guided-slide-content { min-height: 500px; padding: 50px 24px; }
            .guided-slide-overlay { background: linear-gradient(90deg, rgba(17,24,39,.93), rgba(17,24,39,.75)); }
            .guided-slide .btn { width: 100%; }
            .condition-body { padding: 20px; }
            .condition-heading { align-items: flex-start; flex-direction: column; }
            .condition-search-row, .condition-filter-row, .condition-grid { grid-template-columns: 1fr; }
            .report-strip { grid-template-columns: auto 1fr; }
            .report-strip-btn { grid-column: 1 / -1; width: 100%; }
        }

    </style>
</head>


<body>

{{-- ========================================================= --}}
{{-- SETUP / SAFE DEFAULTS                                      --}}
{{-- ========================================================= --}}
@php
    $hasRoute = fn ($name) => \Illuminate\Support\Facades\Route::has($name);

    $topProviders = $topProviders ?? collect();
    $topDoctors   = $topDoctors   ?? collect();

    // Only providers with real coordinates are used for the map and counters.
    $mapProviders = collect($mapProviders ?? [])->filter(function ($p) {
        $lat = data_get($p, 'latitude');
        $lng = data_get($p, 'longitude');

        return is_numeric($lat) && is_numeric($lng)
            && abs((float) $lat) <= 90 && abs((float) $lng) <= 180
            && ! ((float) $lat == 0 && (float) $lng == 0);
    })->values();

    $mapIds = $mapProviders->pluck('id')->map(fn ($id) => (string) $id)->all();

    // Endpoints used by the page. A missing route becomes null instead of crashing the page.
    $pageRoutes = [
        'search'          => route('health.search'),
        'authStatus'      => $hasRoute('patient.auth.status')   ? route('patient.auth.status')   : null,
        'login'           => $hasRoute('patient.auth.login')    ? route('patient.auth.login')    : null,
        'register'        => $hasRoute('patient.auth.register') ? route('patient.auth.register') : null,
        'booking'         => $hasRoute('appointments.store')    ? route('appointments.store')    : null,
        'providerProfile' => url('/providers'),   // GET /providers/{id}/profile
        'doctorProfile'   => url('/doctors'),     // GET /doctors/{id}/profile
        'reportIssue'     => $hasRoute('reports.store') ? route('reports.store') : null,
    ];

    // Fill these in when you have them. Empty values are simply not shown.
    $contactEmail = null;   // e.g. 'info@afyalink.ug'
    $contactPhone = null;   // e.g. '+256 700 000000'
    $social = [
        'facebook'  => ['icon' => 'fa-facebook-f',  'url' => null],
        'instagram' => ['icon' => 'fa-instagram',   'url' => null],
        'x'         => ['icon' => 'fa-x-twitter',   'url' => null],
        'linkedin'  => ['icon' => 'fa-linkedin-in', 'url' => null],
    ];
    $hasSocial = collect($social)->pluck('url')->filter()->isNotEmpty();

    // Legal pages: shown only when a route with this name exists.
    $legal = ['Privacy Policy' => 'privacy', 'Terms of Use' => 'terms', 'Disclaimer' => 'disclaimer'];

    $placeOf = fn ($m) => collect([data_get($m, 'town'), data_get($m, 'district')])
        ->filter()->unique()->implode(', ');

    $imageUrl = function ($path, $fallback) {
        if (! $path) {
            return $fallback;
        }
        $path = trim((string) $path);

        if (str_starts_with($path, 'images/') || str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (preg_match('#^(https?:)?//#', $path) || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    };

    $cleanPhone = fn ($p) => preg_replace('/[^\d+]/', '', (string) $p);

    $mapProviderData = $mapProviders->map(function ($provider) {
        return [
            'id'            => data_get($provider, 'id'),
            'name'          => (string) data_get($provider, 'name', ''),
            'type'          => strtolower((string) data_get($provider, 'provider_type', '')),
            'ownership'     => (string) data_get($provider, 'ownership', ''),
            'district'      => (string) data_get($provider, 'district', ''),
            'town'          => (string) data_get($provider, 'town', ''),
            'latitude'      => (float) data_get($provider, 'latitude'),
            'longitude'     => (float) data_get($provider, 'longitude'),
            'rating'        => (float) data_get($provider, 'rating', 0),
            'phone'         => (string) data_get($provider, 'phone', ''),
            'verified'      => (bool) data_get($provider, 'is_verified', false),
            'emergency'     => (bool) data_get($provider, 'emergency_available', false),
            'open_24_hours' => (bool) data_get($provider, 'open_24_hours', false),
        ];
    })->values();
@endphp


<a class="skip-link" href="#main">Skip to content</a>


{{-- ========================================================= --}}
{{-- NAVBAR                                                     --}}
{{-- ========================================================= --}}
<header class="navbar">

    <div class="container nav-inner">

        <a href="/" class="logo" aria-label="AfyaLink Uganda home">
            <div class="logo-mark"><i data-lucide="heart-pulse" width="23" height="23"></i></div>
            <div>
                <div class="logo-title">AfyaLink</div>
                <div class="logo-subtitle">UGANDA</div>
            </div>
        </a>

        <nav class="desktop-nav" aria-label="Main">
            <a href="#home" class="active" aria-current="true">Home</a>
            <a href="#about">About</a>
            <a href="#services">Services</a>
            <a href="#providers">Providers</a>
            <a href="#doctors">Doctors</a>
            <a href="#healthcare-map">Map</a>
            <a href="#emergency">Emergency</a>
            <a href="#download">Get App</a>
            <a href="#download">blogs</a>
        </nav>

        <div class="nav-actions">
            <a href="{{ route('provider.login') }}" class="signin-link">Provider Sign In</a>
            <a href="{{ route('registration.start') }}" class="btn btn-primary">
                <i data-lucide="building-2" width="17" height="17"></i>
                Register Facility
            </a>
        </div>

        <button type="button" class="menu-button" id="menuButton"
                aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
            <i data-lucide="menu" width="21" height="21"></i>
        </button>

    </div>

    <nav class="mobile-menu" id="mobileMenu" aria-label="Mobile">
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#services">Services</a>
        <a href="#providers">Providers</a>
        <a href="#doctors">Doctors</a>
        <a href="#healthcare-map">Map</a>
        <a href="#emergency">Emergency</a>
        <a href="#download">Get App</a>

        <div class="mobile-actions">
            <a href="{{ route('provider.login') }}" class="btn btn-white">Provider Sign In</a>
            <a href="{{ route('registration.start') }}" class="btn btn-primary">Register</a>
        </div>
    </nav>

</header>


<main id="main">

{{-- ========================================================= --}}
{{-- HERO                                                       --}}
{{-- ========================================================= --}}
<section class="hero" id="home">

    <div class="container hero-grid">

        <div>

            <div class="hero-badge">
                <i data-lucide="heart-pulse" width="17" height="17"></i>
                <span id="heroBadgeText">Your connection to better healthcare</span>
            </div>

            <div class="hero-language-row" aria-label="Choose language">
                <span><i data-lucide="languages" width="14" height="14"></i> Language</span>
                <select id="siteLanguage" class="hero-language-select">
                    <option value="en">English</option>
                    <option value="lg">Luganda</option>
                    <option value="sw">Kiswahili</option>
                    <option value="laj">Lango / Langi</option>
                    <option value="lgg">Lugbara</option>
                    <option value="xog">Lusoga</option>
                    <option value="nyn">Runyankore</option>
                </select>
                <small id="heroLanguageName">English</small>
            </div>

            <h1 class="hero-title">
                <span class="hero-highlight" id="heroTitlePrimary">
                    Healthcare in Uganda
                    <svg viewBox="0 0 220 16" preserveAspectRatio="none" aria-hidden="true">
                        <path d="M3 11 C55 2, 165 2, 217 11" fill="none" stroke="#D32F2F" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                </span>
                <span id="heroTitleSecondary">made easier to find.</span>
            </h1>

            <p class="hero-description" id="heroDescription">
                Search hospitals, clinics, pharmacies, laboratories and doctors across Uganda.
                Browse healthcare services, districts, ratings and locations, then book your visit.
            </p>

            <div class="search-wrapper">

                <form class="search-box" id="liveSearchForm" autocomplete="off" role="search">

                    <div class="search-input">
                        <i data-lucide="search" width="20" height="20"></i>
                        <label for="heroSearchInput" class="sr-only">Search hospitals, doctors, clinics, pharmacies or districts</label>
                        <input type="search" id="heroSearchInput"
                               placeholder="Search hospital, doctor, clinic, pharmacy or district..."
                               aria-controls="liveSearchResults" aria-expanded="false">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Search
                        <i data-lucide="arrow-right" width="17" height="17"></i>
                    </button>

                </form>

                <div class="live-search-results" id="liveSearchResults"
                     role="region" aria-label="Search results" aria-live="polite"></div>

            </div>

            <div class="popular">
                <span class="popular-label">Popular:</span>

                @foreach (['Kampala', 'Wakiso', 'Mbarara', 'Mpigi', 'Hospital', 'Pharmacy'] as $term)
                    <button type="button" class="popular-search" data-search="{{ $term }}">{{ $term }}</button>
                @endforeach
            </div>

            <div class="hero-features">
                <div class="hero-feature">
                    <span class="hero-feature-icon"><i data-lucide="map-pin" width="14" height="14"></i></span>
                    Find care near you
                </div>
                <div class="hero-feature">
                    <span class="hero-feature-icon"><i data-lucide="badge-check" width="14" height="14"></i></span>
                    Provider profiles
                </div>
                <div class="hero-feature">
                    <span class="hero-feature-icon"><i data-lucide="calendar-heart" width="14" height="14"></i></span>
                    Book a visit
                </div>
            </div>

        </div>

        <div class="hero-image-wrap">

            <div class="hero-image-shadow"></div>

            <img src="/images/hero-doctor.jpg" alt="African healthcare professional"
                 id="welcomeHeroImage" class="hero-image" fetchpriority="high" decoding="async">

            <div class="floating-card top">
                <div class="floating-icon"><i data-lucide="badge-check" width="21" height="21"></i></div>
                <div>
                    <div class="floating-small">Healthcare providers</div>
                    <div class="floating-title">
                        {{ $mapProviders->count() > 0 ? $mapProviders->count() . ' on the map' : 'Growing network' }}
                    </div>
                </div>
            </div>

            <div class="floating-card bottom">
                <div class="floating-icon"><i data-lucide="map-pin" width="21" height="21"></i></div>
                <div>
                    <div class="floating-small">Find healthcare</div>
                    <div class="floating-title">Across Uganda</div>
                </div>
            </div>

        </div>

    </div>

</section>


{{-- ========================================================= --}}
{{-- FEATURE BAR                                                --}}
{{-- ========================================================= --}}
<div class="feature-bar">
    <div class="container">
        <div class="feature-grid">

            <div class="feature-item">
                <div class="feature-icon"><i data-lucide="database" width="21" height="21"></i></div>
                <div>
                    <div class="feature-title">Up-to-date</div>
                    <div class="feature-subtitle">Regularly updated listings</div>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon"><i data-lucide="badge-check" width="21" height="21"></i></div>
                <div>
                    <div class="feature-title">Profiles</div>
                    <div class="feature-subtitle">Verified badges where earned</div>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon"><i data-lucide="map" width="21" height="21"></i></div>
                <div>
                    <div class="feature-title">Interactive Map</div>
                    <div class="feature-subtitle">Explore available care</div>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon"><i data-lucide="star" width="21" height="21"></i></div>
                <div>
                    <div class="feature-title">Top Rated</div>
                    <div class="feature-subtitle">Hospitals &amp; doctors</div>
                </div>
            </div>

        </div>
    </div>
</div>


{{-- ========================================================= --}}
{{-- HOW IT WORKS                                               --}}
{{-- ========================================================= --}}
<section class="section">
    <div class="container">

        <div class="section-heading center">
            <span class="eyebrow">Simple Healthcare Discover</span>
            <h2 class="section-title">Find healthcare in three simple steps</h2>
            <p class="section-description">
                AfyaLink brings healthcare providers, doctors and location information together in one place.
            </p>
        </div>

        <div class="steps-grid">

            <div class="step-card">
                <div class="step-number" aria-hidden="true">01</div>
                <div class="step-icon"><i data-lucide="search" width="25" height="25"></i></div>
                <h3 class="step-title">Search</h3>
                <p class="step-text">Search by hospital, doctor, specialty, pharmacy, district or healthcare service.</p>
            </div>

            <div class="step-card">
                <div class="step-number" aria-hidden="true">02</div>
                <div class="step-icon"><i data-lucide="map" width="25" height="25"></i></div>
                <h3 class="step-title">Explore</h3>
                <p class="step-text">Open a profile from the list, search or map to see services, ratings and contact details.</p>
            </div>

            <div class="step-card">
                <div class="step-number" aria-hidden="true">03</div>
                <div class="step-icon"><i data-lucide="calendar-heart" width="25" height="25"></i></div>
                <h3 class="step-title">Connect</h3>
                <p class="step-text">Request an appointment, call the facility or get directions before your visit.</p>
            </div>

        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- ABOUT                                                      --}}
{{-- ========================================================= --}}
<section class="section section-light" id="about">

    <div class="container about-grid">

        <div class="about-media">

            <img src="/images/hospital.jpg" alt="Healthcare facility" class="about-main-image" loading="lazy" decoding="async">
            <img src="/images/medical-clinic.jpg" alt="African healthcare consultation" class="about-small-image" loading="lazy" decoding="async">

            <div class="about-badge">
                <i data-lucide="map-pinned" width="24" height="24"></i>
                <div>
                    <div class="about-badge-title">Built for Uganda</div>
                    <div class="about-badge-subtitle">Local healthcare discovery</div>
                </div>
            </div>

        </div>

        <div>

            <span class="eyebrow red">About Afyalink</span>

            <h2 class="section-title">
                Connecting Uganda to <span class="red">better healthcare</span>
            </h2>

            <p class="section-description">
                AfyaLink helps people discover healthcare facilities, doctors, pharmacies and medical
                services in different districts while giving healthcare providers a platform to manage
                their information.
            </p>

            <div class="about-features">
                @foreach ([
                    ['map-pin',     'Location Search', 'Find healthcare by district'],
                    ['badge-check', 'Provider Profiles', 'Facility information'],
                    ['star',        'Ratings',         'Discover highly rated care'],
                    ['calendar-heart', 'Book a Visit', 'Request appointments online'],
                ] as [$icon, $title, $description])
                    <div class="about-feature">
                        <div class="about-feature-icon"><i data-lucide="{{ $icon }}" width="19" height="19"></i></div>
                        <div>
                            <div class="about-feature-title">{{ $title }}</div>
                            <div class="about-feature-text">{{ $description }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <a href="#services" class="btn btn-primary" style="margin-top:30px">
                Explore Healthcare
                <i data-lucide="arrow-right" width="17" height="17"></i>
            </a>

        </div>

    </div>

</section>


{{-- ========================================================= --}}
{{-- SERVICES                                                   --}}
{{-- ========================================================= --}}
<section class="section" id="services">
    <div class="container">

        <div class="section-heading center">
            <span class="eyebrow red">Healthcare Services Near You</span>
            <h2 class="section-title">Find the care you need</h2>
            <p class="section-description">
                Search hospitals, clinics, pharmacies, laboratories, doctors and emergency services.
            </p>
        </div>

        @php
            $services = [
                ['hospital',        'Hospitals',    'Government & private hospitals', '/images/mulago-hospital.jpg', 'hospital'],
                ['stethoscope',     'Clinics',      'General & specialist clinics',   '/images/medical-clinic.jpg',   'clinic'],
                ['pill',            'Pharmacies',   'Medicines and health supplies',  '/images/pharmacy.jpg',         'pharmacy'],
                ['flask-conical',   'Laboratories', 'Medical tests & diagnostics',    '/images/labgirl.jpg',       'laboratory'],
                ['user-round-check','Doctors',      'Find medical professionals',     '/images/youthdoc.jpg',       'doctor'],
                ['ambulance',       'Emergency',    'Find urgent healthcare',         '/images/ambulance.jpg',        'emergency'],
            ];
        @endphp

        <div class="services-grid">
            @foreach ($services as [$icon, $name, $text, $image, $search])
                <button type="button" class="service-card service-browser-trigger" data-category="{{ $search }}"
                        aria-label="Browse {{ strtolower($name) }}">

                    <img src="{{ $image }}" alt="" class="service-image" loading="lazy" decoding="async">
                    <div class="service-overlay"></div>

                    <div class="service-content">
                        <div class="service-icon"><i data-lucide="{{ $icon }}" width="22" height="22"></i></div>

                        <div class="service-row">
                            <div>
                                <div class="service-title">{{ $name }}</div>
                                <div class="service-text">{{ $text }}</div>
                            </div>
                            <div class="service-arrow"><i data-lucide="arrow-up-right" width="18" height="18"></i></div>
                        </div>
                    </div>

                </button>
            @endforeach
        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- GUIDED CARE / CONDITIONS SLIDER                            --}}
{{-- ========================================================= --}}
<section class="section section-light guided-care-section" id="guided-care">
    <div class="container">

        <div class="providers-header">
            <div class="section-heading">
                <span class="eyebrow red">Guided Care</span>
                <h2 class="section-title">Not sure where to start?</h2>
                <p class="section-description">
                    Choose what you are worried about and AfyaLink will help you discover nearby doctors,
                    midwives, clinics and hospitals that may be relevant to your needs.
                </p>
            </div>

            <div class="guided-slider-controls" aria-label="Guided care slider controls">
                <button type="button" class="guided-arrow" id="guidedPrev" aria-label="Previous topic">
                    <i data-lucide="chevron-left" width="18" height="18"></i>
                </button>
                <button type="button" class="guided-arrow" id="guidedNext" aria-label="Next topic">
                    <i data-lucide="chevron-right" width="18" height="18"></i>
                </button>
            </div>
        </div>

        @php
            $guidedTopics = [
                ['maternal', 'Expectant mothers', 'Need to talk to a midwife or maternity expert?', 'Pregnancy can bring many questions. Find nearby midwives, maternity clinics, obstetric care and hospitals.', '/images/maternity.jpg', '/images/hero-doctor.jpg', 'baby'],
                ['children', 'Children', 'Worried about your child’s health?', 'Find pediatricians, child-health clinics and hospitals that support children and families.', '/images/child.jpg', '/images/medical-clinic.jpg', 'baby'],
                ['prostate', 'Prostate health', 'Concerned about prostate symptoms?', 'Find urology specialists, diagnostic services and hospitals that can assess prostate concerns.', '/images/prostate-health.jpg', '/images/specialist.jpg', 'activity'],
                ['dental', 'Dental care', 'Do you need a dentist?', 'Find dental clinics and healthcare professionals for tooth pain, check-ups and oral-health concerns.', '/images/dentist.jpg', '/images/medical-clinic.jpg', 'smile'],
                ['accident', 'Accidents & trauma', 'Has there been an accident?', 'Find nearby emergency departments, trauma-capable facilities and ambulance contacts quickly.', '/images/accident-care.jpg', '/images/ambulance.jpg', 'siren'],
                ['specialist', 'Specialist consultation', 'Worried about where to get a specialist?', 'Tell AfyaLink what you need and explore doctors, clinics and hospitals that may be able to help.', '/images/specialist-consultation.jpg', '/images/specialist.jpg', 'stethoscope'],
            ];
        @endphp

        <div class="guided-slider-shell">
            <div class="guided-slider-track" id="guidedSliderTrack">
                @foreach ($guidedTopics as [$key, $eyebrow, $title, $text, $image, $fallback, $icon])
                    <article class="guided-slide" data-condition="{{ $key }}">
                        <img src="{{ $image }}" alt="" class="guided-slide-image" loading="lazy" decoding="async"
                             onerror="this.onerror=null;this.src='{{ $fallback }}'">
                        <div class="guided-slide-overlay"></div>

                        <div class="guided-slide-content">
                            <span class="guided-kicker">
                                <i data-lucide="{{ $icon }}" width="15" height="15"></i>
                                {{ $eyebrow }}
                            </span>

                            <h3>{{ $title }}</h3>
                            <p>{{ $text }}</p>

                            <button type="button" class="btn btn-primary guided-care-trigger" data-condition="{{ $key }}">
                                Find the right care
                                <i data-lucide="arrow-right" width="16" height="16"></i>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="guided-dots" id="guidedDots" aria-label="Guided care slides"></div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- TOP PROVIDERS                                              --}}
{{-- ========================================================= --}}
<section class="section section-light" id="providers">
    <div class="container">

        <div class="providers-header">

            <div class="section-heading">
                <span class="eyebrow">Top Rated Facilities</span>
                <h2 class="section-title">Hospitals &amp; clinics on AfyaLink</h2>
                <p class="section-description">
                    A selection of facilities on AfyaLink, ordered by rating. Select a card to open its profile.
                </p>
            </div>

            <a href="#healthcare-map" class="text-link">
                View on map
                <i data-lucide="map" width="17" height="17"></i>
            </a>

        </div>

        <div class="providers-grid">

            @forelse ($topProviders as $provider)

                @php
                    $pid       = data_get($provider, 'id');
                    $onMap     = in_array((string) $pid, $mapIds, true);
                    $phone     = data_get($provider, 'phone');
                    $rating    = (float) data_get($provider, 'rating', 0);
                    $reviews   = (int) data_get($provider, 'review_count', 0);
                    $ownership = trim(str_replace('_', ' ', (string) data_get($provider, 'ownership', '')));
                    $place     = $placeOf($provider);
                    $pType     = (string) data_get($provider, 'provider_type', 'facility');
                @endphp

                <article class="provider-card clickable-profile"
                         data-profile-type="provider"
                         data-profile-id="{{ $pid }}">

                    <div class="provider-image-wrap">
                        <img src="{{ $imageUrl(data_get($provider, 'image'), '/images/profile-placeholder.svg') }}"
                             alt="{{ data_get($provider, 'name') }}" class="provider-image"
                             loading="lazy" decoding="async"
                             onerror="this.onerror=null;this.src='/images/profile-placeholder.svg'">

                        <div class="provider-type">{{ $pType }}</div>
                    </div>

                    <div class="provider-body">

                        <div class="provider-title-row">
                            <div>
                                <h3 class="provider-title">{{ data_get($provider, 'name') }}</h3>

                                @if ($place)
                                    <div class="provider-location">
                                        <i data-lucide="map-pin" width="14" height="14"></i>
                                        {{ $place }}
                                    </div>
                                @endif
                            </div>

                            @if (data_get($provider, 'is_verified'))
                                <div class="provider-status">
                                    <i data-lucide="badge-check" width="12" height="12"></i>
                                    Verified
                                </div>
                            @endif
                        </div>

                        <div>
                            @if ($ownership !== '')
                                <span class="provider-ownership">{{ $ownership }}</span>
                            @endif

                            @if (data_get($provider, 'open_24_hours'))
                                <span class="provider-ownership is-green">Open 24/7</span>
                            @endif

                            @if (data_get($provider, 'emergency_available'))
                                <span class="provider-ownership is-red">Emergency</span>
                            @endif
                        </div>

                        <div class="provider-rating">
                            @if ($rating > 0)
                                <i data-lucide="star" width="15" height="15" fill="currentColor"></i>
                                {{ number_format($rating, 1) }}
                                <span style="color:var(--muted);font-weight:500;">({{ $reviews }} {{ \Illuminate\Support\Str::plural('review', $reviews) }})</span>
                            @else
                                <span style="color:var(--muted);font-weight:500;">No reviews yet</span>
                            @endif
                        </div>

                        <div class="provider-footer">
                            <div class="provider-actions">

                                <button type="button" class="text-link"
                                        data-profile-type="provider" data-profile-id="{{ $pid }}">
                                    <i data-lucide="user-round" width="15" height="15"></i>
                                    View profile
                                </button>

                                @if ($onMap)
                                    <button type="button" class="text-link focus-provider"
                                            data-provider-id="{{ $pid }}"
                                            data-provider-name="{{ data_get($provider, 'name') }}">
                                        <i data-lucide="map-pin" width="15" height="15"></i>
                                        View on map
                                    </button>

                                    <a class="text-link" target="_blank" rel="noopener"
                                       href="https://www.google.com/maps/dir/?api=1&destination={{ data_get($provider, 'latitude') }},{{ data_get($provider, 'longitude') }}">
                                        <i data-lucide="navigation" width="15" height="15"></i>
                                        Directions
                                    </a>
                                @endif

                                @if ($phone)
                                    <a class="text-link" href="tel:{{ $cleanPhone($phone) }}">
                                        <i data-lucide="phone" width="15" height="15"></i>
                                        Call
                                    </a>
                                @endif

                            </div>
                        </div>

                    </div>

                </article>

            @empty

                <div class="providers-empty">No healthcare facilities have been added yet.</div>

            @endforelse

        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- TOP DOCTORS                                                --}}
{{-- ========================================================= --}}
<section class="section" id="doctors">
    <div class="container">

        <div class="providers-header">

            <div class="section-heading">
                <span class="eyebrow red">Top Rated Doctors</span>
                <h2 class="section-title">Healthcare professionals on AfyaLink</h2>
                <p class="section-description">
                    Discover doctors listed on AfyaLink, ordered by current rating. Select a card to open the profile.
                </p>
            </div>

            <button type="button" class="text-link popular-search" data-search="doctor">
                Search Doctors
                <i data-lucide="arrow-right" width="17" height="17"></i>
            </button>

        </div>

        <div class="doctors-grid">

            @forelse ($topDoctors as $doctor)

                @php
                    $facility   = $doctor->healthcareProvider;
                    $facilityId = $facility ? data_get($facility, 'id') : null;
                    $facilityOnMap = $facility && in_array((string) $facilityId, $mapIds, true);
                    $docRating  = (float) data_get($doctor, 'rating', 0);
                @endphp

                <article class="doctor-card clickable-profile"
                         data-profile-type="doctor"
                         data-profile-id="{{ data_get($doctor, 'id') }}">

                    <div class="doctor-photo">
                        <img src="{{ $imageUrl(data_get($doctor, 'image'), '/images/profile-placeholder.svg') }}"
                             alt="{{ data_get($doctor, 'name') }}" loading="lazy" decoding="async"
                             onerror="this.onerror=null;this.src='/images/profile-placeholder.svg'">

                        @if ($docRating > 0)
                            <span class="doctor-rank">
                                <i data-lucide="star" width="13" height="13" fill="currentColor"></i>
                                {{ number_format($docRating, 1) }}
                            </span>
                        @endif
                    </div>

                    <div class="doctor-body">

                        <h3 class="doctor-name">{{ data_get($doctor, 'name') }}</h3>
                        <div class="doctor-specialty">{{ data_get($doctor, 'specialty') }}</div>

                        @if ($facility)
                            <div class="doctor-facility">
                                <i data-lucide="hospital" width="13" height="13"></i>

                                @if ($facilityOnMap)
                                    <button type="button" class="text-link focus-provider"
                                            data-provider-id="{{ $facilityId }}"
                                            data-provider-name="{{ data_get($facility, 'name') }}">
                                        {{ data_get($facility, 'name') }}
                                    </button>
                                @else
                                    <span>{{ data_get($facility, 'name') }}</span>
                                @endif
                            </div>
                        @endif

                        <div class="doctor-bottom">

                            @if (data_get($doctor, 'district'))
                                <div class="provider-location">
                                    <i data-lucide="map-pin" width="13" height="13"></i>
                                    {{ data_get($doctor, 'district') }}
                                </div>
                            @else
                                <span></span>
                            @endif

                            @if (data_get($doctor, 'is_verified'))
                                <div class="provider-status">
                                    <i data-lucide="badge-check" width="12" height="12"></i>
                                    Verified
                                </div>
                            @endif

                        </div>

                        <div class="doctor-actions">
                            <button type="button" class="text-link"
                                    data-profile-type="doctor" data-profile-id="{{ data_get($doctor, 'id') }}">
                                <i data-lucide="user-round" width="15" height="15"></i>
                                View profile
                            </button>
                        </div>

                    </div>

                </article>

            @empty

                <div class="providers-empty">No doctors have been added yet.</div>

            @endforelse

        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- HEALTHCARE MAP                                             --}}
{{-- ========================================================= --}}
<section class="section health-map-section" id="healthcare-map">
    <div class="container">

        <div class="section-heading center">
            <span class="eyebrow">Healthcare Map</span>
            <h2 class="section-title">Explore healthcare services across Uganda</h2>
            <p class="section-description">
                Hospitals, clinics, pharmacies and laboratories on AfyaLink appear automatically on the map below.
                Select a pin to open that facility's profile.
            </p>
        </div>

        <div class="map-toolbar">

            <div class="map-legend" role="group" aria-label="Filter map by facility type">
                <button type="button" class="map-legend-item" data-layer="hospital" aria-pressed="true">
                    <span class="map-dot hospital"></span> Hospitals
                </button>
                <button type="button" class="map-legend-item" data-layer="clinic" aria-pressed="true">
                    <span class="map-dot clinic"></span> Clinics
                </button>
                <button type="button" class="map-legend-item" data-layer="pharmacy" aria-pressed="true">
                    <span class="map-dot pharmacy"></span> Pharmacies
                </button>
                <button type="button" class="map-legend-item" data-layer="laboratory" aria-pressed="true">
                    <span class="map-dot laboratory"></span> Laboratories
                </button>
            </div>

            <div class="map-tools">
                <span class="map-count">{{ $mapProviders->count() }} healthcare {{ \Illuminate\Support\Str::plural('location', $mapProviders->count()) }}</span>

                <button type="button" class="btn btn-green" id="locateButton" style="min-height:42px;padding:10px 16px;">
                    <i data-lucide="locate-fixed" width="16" height="16"></i>
                    Find nearest to me
                </button>
            </div>

        </div>

        <div class="map-wrap">
            <div id="healthcareMap" role="application" aria-label="Map of healthcare facilities in Uganda"></div>

            @if ($mapProviders->isEmpty())
                <div class="map-empty">No facilities with map locations have been added yet.</div>
            @endif
        </div>

        <div class="locate-status" id="locateStatus" role="status" aria-live="polite"></div>
        <div class="nearest-list" id="nearestList"></div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- FOR PATIENTS & PROVIDERS                                   --}}
{{-- ========================================================= --}}
<section class="section section-light">
    <div class="container">

        <div class="section-heading center">
            <span class="eyebrow">One Platform</span>
            <h2 class="section-title">Built for patients and healthcare providers</h2>
            <p class="section-description">
                AfyaLink connects people looking for care with healthcare organisations and professionals.
            </p>
        </div>

        <div class="audience-grid">

            <div class="audience-card">
                <div class="audience-icon"><i data-lucide="users" width="26" height="26"></i></div>
                <h3 class="audience-title">For patients &amp; families</h3>
                <p class="audience-text">Search healthcare providers, read facility information and request appointments.</p>

                <div class="benefit-grid">
                    @foreach (['Search hospitals', 'Find clinics', 'Discover pharmacies', 'Find doctors', 'Book a visit', 'See ratings'] as $item)
                        <div class="benefit-item">
                            <span class="benefit-check"><i data-lucide="check" width="14" height="14"></i></span>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="audience-card dark">
                <div class="audience-icon"><i data-lucide="hospital" width="26" height="26"></i></div>
                <h3 class="audience-title">For healthcare providers</h3>
                <p class="audience-text">Manage your facility and make services easier for patients to discover.</p>

                <div class="benefit-grid">
                    @foreach (['Facility profile', 'Service management', 'Working hours', 'Location on the map', 'Doctor listings', 'Appointment requests'] as $item)
                        <div class="benefit-item">
                            <span class="benefit-check"><i data-lucide="check" width="14" height="14"></i></span>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('registration.start') }}" class="btn btn-white" style="margin-top:28px">
                    Register Facility
                    <i data-lucide="arrow-right" width="16" height="16"></i>
                </a>
            </div>

        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- EMERGENCY                                                  --}}
{{-- ========================================================= --}}
<section class="section" id="emergency">
    <div class="container">

        <div class="emergency-card">

            <img src="/images/ambulance.jpg" alt="" class="emergency-bg" loading="lazy" decoding="async">
            <div class="emergency-overlay"></div>

            <div class="emergency-content">

                <div class="emergency-label">
                    <span class="pulse-dot"></span>
                    Emergency Healthcare
                </div>

                <h2 class="emergency-title">Need urgent medical help?</h2>

                <p class="emergency-text">
                    In a life-threatening emergency, call for help straight away. Once you are safe, use AfyaLink
                    to find facilities that list emergency services.
                </p>

                {{-- Verify these emergency numbers are correct before launch. --}}
                <div class="emergency-actions">

                    <a href="tel:999" class="btn emergency-primary">
                        <i data-lucide="phone" width="17" height="17"></i>
                        Call 999
                    </a>

                    <a href="tel:112" class="btn btn-outline-white">
                        <i data-lucide="phone" width="17" height="17"></i>
                        Call 112
                    </a>

                    <button type="button" class="btn btn-outline-white" id="openAmbulanceModal">
                        <i data-lucide="ambulance" width="17" height="17"></i>
                        Find Nearby Ambulance
                    </button>

                    <a href="#healthcare-map" class="btn btn-outline-white">
                        <i data-lucide="map" width="17" height="17"></i>
                        Open Healthcare Map
                    </a>

                </div>

                <p class="emergency-note">
                    Do not book online or wait to search if someone is seriously ill or injured. Call or go to the nearest facility now.
                </p>

            </div>

        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- HEALTH CONTENT                                             --}}
{{-- ========================================================= --}}
<section class="section section-light" id="health">
    <div class="container">

        <div class="providers-header">
            <div class="section-heading">
                <span class="eyebrow">Health &amp; Wellnes</span>
                <h2 class="section-title">Helpful health information</h2>
                <p class="section-description">
                    Educational information can help support healthier decisions while professional
                    healthcare remains important when needed.
                </p>
            </div>
        </div>

        @php
            $articles = [
                ['CHILD HEALTH',  'Helping children stay healthy and protected', 'Regular check-ups and vaccinations help protect children as they grow.', '/images/medical-clinic.jpg'],
                ['HEALTH ADVICE', 'When should you visit a healthcare provider?', 'Persistent, worsening or unusual symptoms are a good reason to get checked.', '/images/specialist.jpg'],
                ['MEDICATION',    'Using medicines safely and responsibly',      'Follow the instructions and ask a pharmacist if you are unsure.',            '/images/pharmacy.jpg'],
            ];
        @endphp

        <div class="articles-grid">
            @foreach ($articles as [$category, $title, $summary, $image])
                <article class="article-card">
                    <div class="article-image-wrap">
                        <img src="{{ $image }}" alt="" class="article-image" loading="lazy" decoding="async">
                    </div>
                    <div class="article-body">
                        <div class="article-category">{{ $category }}</div>
                        <h3 class="article-title">{{ $title }}</h3>
                        <p class="article-text">{{ $summary }}</p>
                    </div>
                </article>
            @endforeach
        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- MOBILE APP                                                 --}}
{{-- ========================================================= --}}
<section class="section app-section" id="download">
    <div class="container">

        <div class="app-card">

            <div class="app-grid">

                <div>

                    <span class="eyebrow" style="color:#4ADE80;background:rgba(22,138,85,.20);">Afyalink Mobile</span>

                    <h2 class="app-title">Healthcare discovery in your pocket</h2>

                    <p class="app-text">
                        The AfyaLink mobile app is on its way. It will give patients fast access to healthcare
                        providers, location search, favourite facilities, emergency discovery and healthcare
                        information on Android and iOS.
                    </p>

                    <div class="app-features">
                        @foreach (['Find nearby healthcare', 'Search doctors', 'View provider profiles', 'Explore healthcare maps', 'Save favourite facilities', 'Emergency discovery'] as $feature)
                            <div class="app-feature">
                                <span class="app-feature-check"><i data-lucide="check" width="14" height="14"></i></span>
                                {{ $feature }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Swap these for real <a href="..."> links once the apps are published. --}}
                    <div class="app-actions">

                        <div class="store-button" aria-label="Google Play, coming soon">
                            <i class="fa-brands fa-google-play" style="font-size:25px" aria-hidden="true"></i>
                            <div>
                                <div class="store-small">GET IT ON</div>
                                <div class="store-title">Google Play</div>
                            </div>
                            <span class="store-soon">SOON</span>
                        </div>

                        <div class="store-button" aria-label="App Store, coming soon">
                            <i class="fa-brands fa-apple" style="font-size:28px" aria-hidden="true"></i>
                            <div>
                                <div class="store-small">DOWNLOAD ON THE</div>
                                <div class="store-title">App Store</div>
                            </div>
                            <span class="store-soon">SOON</span>
                        </div>

                    </div>

                </div>

                <div aria-hidden="true">

                    <div class="phone-mockup">
                        <div class="phone-screen">

                            <div class="phone-logo">
                                <span class="phone-logo-mark"><i data-lucide="heart-pulse" width="16" height="16"></i></span>
                                AfyaLink
                            </div>

                            <div class="phone-headline">Find healthcare near you</div>

                            <div class="phone-search">
                                <i data-lucide="search" width="14" height="14"></i>
                                Search healthcare...
                            </div>

                            <div class="phone-service-grid">
                                <div class="phone-service">Hospitals</div>
                                <div class="phone-service">Clinics</div>
                                <div class="phone-service">Pharmacies</div>
                                <div class="phone-service">Doctors</div>
                            </div>

                            <div class="phone-map-card">
                                <small>HEALTHCARE MAP</small>
                                <strong>Explore providers around you</strong>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- FAQ                                                        --}}
{{-- ========================================================= --}}
<section class="section">
    <div class="container">

        <div class="section-heading center">
            <span class="eyebrow">Frequently Asked Questions</span>
            <h2 class="section-title">Questions about AfyaLink?</h2>
        </div>

        <div class="faq-list">
            @foreach ([
                ['What is AfyaLink Uganda?', 'AfyaLink is a healthcare discovery platform that helps users find healthcare facilities, doctors, pharmacies and healthcare information.'],
                ['Can I search by district?', 'Yes. The live search can find healthcare records using names, provider types, towns and districts such as Kampala, Wakiso, Mbarara and Mpigi. You can also type phrases like "hospitals in Kampala".'],
                ['How do I book a visit?', 'Open a facility or doctor profile and choose "Book Visit". You will be asked to sign in or create a free patient account, then you can send an appointment request. The provider confirms the final time.'],
                ['Where does the map information come from?', 'The map reads latitude and longitude values from the healthcare providers stored in AfyaLink. Use "Find nearest to me" to see the closest facilities to your current location.'],
                ['Can healthcare facilities register?', 'Yes. Healthcare providers can create and manage facility profiles through the provider system.'],
                ['Does AfyaLink provide medical advice?', 'No. AfyaLink is a healthcare discovery platform and does not replace qualified professional medical care. In an emergency, call for help immediately.'],
            ] as [$question, $answer])
                <details class="faq-item">
                    <summary>
                        {{ $question }}
                        <span class="faq-plus"><i data-lucide="plus" width="16" height="16"></i></span>
                    </summary>
                    <div class="faq-answer">{{ $answer }}</div>
                </details>
            @endforeach
        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- PROVIDER CTA                                               --}}
{{-- ========================================================= --}}
<section class="section" style="padding-top:20px">
    <div class="container">

        <div class="provider-cta">
            <div class="provider-cta-inner">

                <div class="provider-cta-content">

                    <div class="provider-cta-label">
                        <i data-lucide="building-2" width="17" height="17"></i>
                        For Healthcare Providers
                    </div>

                    <h2 class="provider-cta-title">Bring your healthcare facility closer to patients</h2>

                    <p class="provider-cta-text">
                        Create your healthcare profile, add your location to the map and manage services,
                        working hours, doctors and facility information.
                    </p>

                    <div class="provider-benefits">
                        @foreach (['Facility profile', 'Healthcare map', 'Services', 'Working hours', 'Doctors', 'Appointment requests'] as $benefit)
                            <span class="provider-benefit">
                                <i data-lucide="circle-check" width="14" height="14"></i>
                                {{ $benefit }}
                            </span>
                        @endforeach
                    </div>

                </div>

                <a href="{{ route('registration.start') }}" class="btn provider-cta-button">
                    Register Facility
                    <i data-lucide="arrow-right" width="18" height="18"></i>
                </a>

            </div>
        </div>

    </div>
</section>


{{-- ========================================================= --}}
{{-- REPORT ISSUE / SCAM                                        --}}
{{-- ========================================================= --}}
<section class="report-strip-section">
    <div class="container">
        <div class="report-strip">
            <div class="report-strip-icon">
                <i data-lucide="shield-alert" width="24" height="24"></i>
            </div>

            <div>
                <strong>Help keep AfyaLink safe.</strong>
                <span>Seen wrong information, suspicious activity or a possible healthcare scam?</span>
            </div>

            <button type="button" class="btn report-strip-btn" id="openReportModal">
                Report an issue or scam
                <i data-lucide="flag" width="16" height="16"></i>
            </button>
        </div>
    </div>
</section>

</main>


{{-- ========================================================= --}}
{{-- FOOTER                                                     --}}
{{-- ========================================================= --}}
<footer class="footer">
    <div class="container">

        <div class="footer-grid">

            <div>

                <a href="/" class="logo" aria-label="AfyaLink Uganda home">
                    <div class="logo-mark"><i data-lucide="heart-pulse" width="23" height="23"></i></div>
                    <div>
                        <div class="logo-title" style="color:white">AfyaLink</div>
                        <div class="logo-subtitle">UGANDA</div>
                    </div>
                </a>

                <p class="footer-description">
                    Connecting people to hospitals, clinics, pharmacies, doctors and useful healthcare
                    information across Uganda.
                </p>

                @if ($hasSocial)
                    <div class="social-links">
                        @foreach ($social as $name => $s)
                            @if ($s['url'])
                                <a href="{{ $s['url'] }}" class="social-link" target="_blank" rel="noopener"
                                   aria-label="{{ ucfirst($name) }}">
                                    <i class="fa-brands {{ $s['icon'] }}" aria-hidden="true"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif

            </div>

            <div>
                <h3 class="footer-heading">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#providers">Providers</a></li>
                    <li><a href="#healthcare-map">Healthcare Map</a></li>
                </ul>
            </div>

            <div>
                <h3 class="footer-heading">Healthcare</h3>
                <ul class="footer-links">
                    <li><a href="#providers">Hospitals</a></li>
                    <li><a href="#providers">Clinics</a></li>
                    <li><a href="#doctors">Doctors</a></li>
                    <li><a href="#services">Pharmacies</a></li>
                    <li><a href="#emergency">Emergency</a></li>
                </ul>
            </div>

            <div>
                <h3 class="footer-heading">Contact</h3>

                <div class="footer-contact">

                    <div class="footer-contact-item">
                        <i data-lucide="map-pin" width="17" height="17" class="footer-contact-icon"></i>
                        Kampala, Uganda
                    </div>

                    @if ($contactEmail)
                        <div class="footer-contact-item">
                            <i data-lucide="mail" width="17" height="17" class="footer-contact-icon"></i>
                            <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                        </div>
                    @endif

                    @if ($contactPhone)
                        <div class="footer-contact-item">
                            <i data-lucide="phone" width="17" height="17" class="footer-contact-icon"></i>
                            <a href="tel:{{ $cleanPhone($contactPhone) }}">{{ $contactPhone }}</a>
                        </div>
                    @endif

                    <div class="footer-contact-item">
                        <i data-lucide="building-2" width="17" height="17" class="footer-contact-icon"></i>
                        <a href="/register">Register your facility</a>
                    </div>

                </div>
            </div>

        </div>

        <div class="footer-bottom">

            <div>&copy; {{ date('Y') }} AfyaLink Uganda. All rights reserved.</div>

            <div class="footer-legal">
                @foreach ($legal as $label => $routeName)
                    @if ($hasRoute($routeName))
                        <a href="{{ route($routeName) }}">{{ $label }}</a>
                    @endif
                @endforeach
            </div>

        </div>

    </div>
</footer>


{{-- ========================================================= --}}
{{-- CHAT ASSISTANT                                             --}}
{{-- ========================================================= --}}
<div class="chat-widget quickbot-widget">

    <div class="chat-window" id="chatWindow" role="dialog" aria-label="QuickBot healthcare assistant">

        <div class="chat-header quickbot-header">
            <div class="quickbot-header-left">
                <span class="quickbot-avatar"><i data-lucide="bot" width="20" height="20"></i><span class="quickbot-online"></span></span>
                <div>
                    <div class="chat-title">QuickBot</div>
                    <div class="chat-subtitle">Nearby care • health guidance • translation</div>
                </div>
            </div>

            <button type="button" class="chat-close" id="chatClose" aria-label="Close QuickBot">
                <i data-lucide="x" width="17" height="17"></i>
            </button>
        </div>

        <div class="quickbot-language-bar">
            <label for="quickbotLanguage"><i data-lucide="languages" width="14" height="14"></i> Reply in</label>
            <select id="quickbotLanguage">
                <option value="en">English</option>
                <option value="lg">Luganda</option>
                <option value="sw">Kiswahili</option>
                <option value="laj">Lango / Langi</option>
                <option value="lgg">Lugbara</option>
                <option value="xog">Lusoga</option>
                <option value="nyn">Runyankore</option>
            </select>
        </div>

        <div class="chat-body" id="chatMessages" aria-live="polite">

            <div class="chat-message quickbot-welcome" id="quickbotWelcome">
                <strong>Hi, I’m QuickBot.</strong><br>
                Tell me what is worrying you. I can comfort you, give general health guidance,
                search AfyaLink for nearby care and help you find a hospital, midwife, doctor,
                ambulance contact or another health worker.

                <div class="chat-quick">
                    <button type="button" data-nearby="hospital">Nearby hospital</button>
                    <button type="button" data-nearby="emergency">Nearby ambulance help</button>
                    <button type="button" data-nearby="doctor">Nearby health worker</button>
                    <button type="button" data-condition-chat="maternal">Talk to a midwife</button>
                    <button type="button" data-chat="clinic">Browse clinics</button>
                </div>
            </div>

        </div>

        <div class="chat-footer">
            <form class="chat-form" id="chatForm" autocomplete="off">
                <label for="chatInput" class="sr-only">Ask QuickBot</label>
                <input type="text" class="chat-input" id="chatInput" placeholder="Ask QuickBot anything about nearby care…">

                <button type="submit" class="chat-send" aria-label="Send message">
                    <i data-lucide="send" width="15" height="15"></i>
                </button>
            </form>
        </div>

    </div>

    <div class="quickbot-nudge" id="quickbotNudge" aria-hidden="true">Need help finding care?</div>

    <button type="button" class="chat-launcher quickbot-launcher" id="chatLauncher"
            aria-label="Open QuickBot" aria-expanded="false" aria-controls="chatWindow">
        <i data-lucide="bot" width="28" height="28"></i>
        <span class="quickbot-badge" id="quickbotBadge">1</span>
    </button>

</div>

{{-- ========================================================= --}}
{{-- SMART SERVICE DIRECTORY MODAL                              --}}
{{-- ========================================================= --}}
<div class="afya-modal" id="serviceBrowserModal" role="dialog" aria-modal="true"
     aria-labelledby="serviceBrowserTitle" aria-hidden="true">

    <div class="afya-modal-panel directory-panel">

        <div class="afya-modal-header">
            <div class="afya-modal-brand" id="serviceBrowserTitle">
                <span class="afya-modal-brand-icon"><i data-lucide="layout-grid" width="18" height="18"></i></span>
                Explore Healthcare
            </div>

            <button type="button" class="afya-modal-close" data-close-modal="serviceBrowserModal" aria-label="Close directory">
                <i data-lucide="x" width="18" height="18"></i>
            </button>
        </div>

        <div class="directory-body">

            <div class="directory-hero">
                <div>
                    <span class="eyebrow red" id="directoryEyebrow">HEALTHCARE DIRECTORY</span>
                    <h2 class="directory-title" id="directoryTitle">Browse healthcare providers</h2>
                    <p class="directory-subtitle">Search and filter in real time without leaving this page.</p>
                </div>
                <div class="directory-count" id="directoryCount">0 results</div>
            </div>

            <div class="directory-categories" id="directoryCategories" aria-label="Healthcare categories">
                @foreach ([
                    ['hospital', 'hospital', 'Hospitals'],
                    ['clinic', 'stethoscope', 'Clinics'],
                    ['pharmacy', 'pill', 'Pharmacies'],
                    ['laboratory', 'flask-conical', 'Laboratories'],
                    ['doctor', 'user-round-check', 'Doctors'],
                    ['emergency', 'ambulance', 'Emergency']
                ] as [$value, $icon, $label])
                    <button type="button" class="directory-category" data-directory-category="{{ $value }}">
                        <i data-lucide="{{ $icon }}" width="16" height="16"></i>
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="directory-search-row">
                <label class="directory-search-box">
                    <i data-lucide="search" width="19" height="19"></i>
                    <input type="search" id="directorySearch" placeholder="Search by name, district, service or specialty…" autocomplete="off">
                </label>

                <button type="button" class="directory-location-btn" id="directoryNearMe">
                    <i data-lucide="locate-fixed" width="17" height="17"></i>
                    Near me
                </button>
            </div>

            <div class="directory-filters">
                <label class="directory-filter">
                    <span>District</span>
                    <select id="directoryDistrict">
                        <option value="">All districts</option>
                    </select>
                </label>

                <label class="directory-filter">
                    <span>Ownership</span>
                    <select id="directoryOwnership">
                        <option value="">Government & private</option>
                        <option value="government">Government</option>
                        <option value="private">Private</option>
                        <option value="faith based">Faith based</option>
                    </select>
                </label>

                <label class="directory-check">
                    <input type="checkbox" id="directoryOpen24">
                    <span>Open 24/7</span>
                </label>

                <label class="directory-check">
                    <input type="checkbox" id="directoryEmergency">
                    <span>Emergency care</span>
                </label>
            </div>

            <div class="directory-status" id="directoryStatus" aria-live="polite"></div>
            <div class="directory-grid" id="directoryGrid"></div>

        </div>
    </div>
</div>


{{-- ========================================================= --}}
{{-- CONDITION / GUIDED CARE MODAL                              --}}
{{-- ========================================================= --}}
<div class="afya-modal" id="conditionModal" role="dialog" aria-modal="true"
     aria-labelledby="conditionModalTitle" aria-hidden="true">

    <div class="afya-modal-panel condition-panel">

        <div class="afya-modal-header">
            <div class="afya-modal-brand" id="conditionModalTitle">
                <span class="afya-modal-brand-icon"><i data-lucide="heart-handshake" width="18" height="18"></i></span>
                Guided Care
            </div>

            <button type="button" class="afya-modal-close" data-close-modal="conditionModal" aria-label="Close guided care">
                <i data-lucide="x" width="18" height="18"></i>
            </button>
        </div>

        <div class="condition-body">

            <div class="condition-heading">
                <div>
                    <span class="eyebrow red" id="conditionEyebrow">Guided Care</span>
                    <h2 id="conditionTitle">Find the right care</h2>
                    <p id="conditionDescription">AfyaLink will search doctors, clinics and hospitals that may be relevant.</p>
                </div>
                <span class="directory-count" id="conditionCount">0 results</span>
            </div>

            <div class="condition-search-row">
                <div class="directory-search-box">
                    <i data-lucide="search" width="17" height="17"></i>
                    <label for="conditionSearch" class="sr-only">Search within this care topic</label>
                    <input type="search" id="conditionSearch" placeholder="Search specialty, provider or district…">
                </div>
                <button type="button" class="directory-location-btn" id="conditionNearMe">
                    <i data-lucide="locate-fixed" width="16" height="16"></i> Near me
                </button>
            </div>

            <div class="condition-filter-row">
                <label class="directory-filter">
                    <span>District</span>
                    <select id="conditionDistrict"><option value="">All districts</option></select>
                </label>
                <label class="directory-check"><input type="checkbox" id="conditionHospitals" checked> Hospitals</label>
                <label class="directory-check"><input type="checkbox" id="conditionClinics" checked> Clinics</label>
                <label class="directory-check"><input type="checkbox" id="conditionDoctors" checked> Doctors / health workers</label>
            </div>

            <div class="directory-status" id="conditionStatus" aria-live="polite"></div>
            <div class="condition-grid" id="conditionGrid"></div>

            <div class="condition-disclaimer">
                <i data-lucide="info" width="15" height="15"></i>
                AfyaLink helps you discover possible care options. It does not diagnose disease. If symptoms are severe or rapidly worsening, seek urgent medical help.
            </div>
        </div>
    </div>
</div>


{{-- ========================================================= --}}
{{-- REPORT ISSUE / SCAM MODAL                                  --}}
{{-- ========================================================= --}}
<div class="afya-modal" id="reportModal" role="dialog" aria-modal="true"
     aria-labelledby="reportModalTitle" aria-hidden="true">

    <div class="afya-modal-panel small">
        <div class="afya-modal-header">
            <div class="afya-modal-brand" id="reportModalTitle">
                <span class="afya-modal-brand-icon"><i data-lucide="shield-alert" width="18" height="18"></i></span>
                Report an issue or scam
            </div>
            <button type="button" class="afya-modal-close" data-close-modal="reportModal" aria-label="Close report form">
                <i data-lucide="x" width="18" height="18"></i>
            </button>
        </div>

        <div class="report-modal-body">
            <p class="report-modal-intro">
                Tell us about incorrect information, impersonation, suspicious requests for money, fake healthcare listings or another problem. Do not include passwords or unnecessary medical details.
            </p>

            <div class="afya-auth-message" id="reportMessage" role="alert"></div>

            <form id="reportIssueForm">
                @csrf
                <div class="afya-form-grid">
                    <label class="afya-form-group full">
                        <span class="afya-label">What are you reporting?</span>
                        <select class="afya-select" name="issue_type" required>
                            <option value="">Select one</option>
                            <option value="wrong_information">Wrong provider information</option>
                            <option value="suspected_scam">Suspected scam or fraud</option>
                            <option value="impersonation">Impersonation / fake provider</option>
                            <option value="unsafe_content">Unsafe or misleading content</option>
                            <option value="technical_problem">Technical problem</option>
                            <option value="other">Other</option>
                        </select>
                    </label>
                    <label class="afya-form-group full">
                        <span class="afya-label">Provider / page / person involved</span>
                        <input class="afya-input" name="subject" maxlength="180" placeholder="Example: clinic name, doctor name or page">
                    </label>
                    <label class="afya-form-group full">
                        <span class="afya-label">Describe the issue</span>
                        <textarea class="afya-textarea" name="details" maxlength="2000" required placeholder="What happened? What did you see?"></textarea>
                    </label>
                    <label class="afya-form-group full">
                        <span class="afya-label">Your email or phone (optional)</span>
                        <input class="afya-input" name="contact" maxlength="150" placeholder="Only if you want AfyaLink to follow up">
                    </label>
                    <div class="afya-form-group full">
                        <button type="submit" class="btn btn-primary"><i data-lucide="send" width="16" height="16"></i> Send report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ========================================================= --}}
{{-- EMERGENCY / AMBULANCE MODAL                               --}}
{{-- ========================================================= --}}
<div class="afya-modal" id="ambulanceModal" role="dialog" aria-modal="true"
     aria-labelledby="ambulanceModalTitle" aria-hidden="true">

    <div class="afya-modal-panel emergency-panel">

        <div class="afya-modal-header emergency-modal-header">
            <div class="afya-modal-brand" id="ambulanceModalTitle">
                <span class="afya-modal-brand-icon"><i data-lucide="ambulance" width="18" height="18"></i></span>
                Emergency Transport
            </div>

            <button type="button" class="afya-modal-close" data-close-modal="ambulanceModal" aria-label="Close emergency transport">
                <i data-lucide="x" width="18" height="18"></i>
            </button>
        </div>

        <div class="ambulance-body">
            <div class="ambulance-alert">
                <div class="ambulance-alert-icon"><i data-lucide="siren" width="24" height="24"></i></div>
                <div>
                    <strong>If the situation is life-threatening, call emergency services immediately.</strong>
                    <p>AfyaLink can help you locate nearby facilities that list emergency care. Ambulance readiness must be confirmed by the provider before dispatch.</p>
                </div>
            </div>

            <div class="ambulance-actions-top">
                <button type="button" class="btn btn-primary" id="ambulanceUseLocation">
                    <i data-lucide="locate-fixed" width="17" height="17"></i>
                    Find nearest emergency help
                </button>
                <a href="tel:999" class="btn btn-white"><i data-lucide="phone" width="17" height="17"></i> Call 999</a>
                <a href="tel:112" class="btn btn-white"><i data-lucide="phone" width="17" height="17"></i> Call 112</a>
            </div>

            <div class="ambulance-location-status" id="ambulanceLocationStatus">Use your location to rank nearby emergency providers.</div>
            <div class="ambulance-grid" id="ambulanceGrid"></div>

            <div class="ambulance-knowledge">
                <h3>How an AfyaLink ambulance request should work</h3>
                <div class="ambulance-steps">
                    <div><span>1</span><strong>Share location</strong><small>Allow location so AfyaLink can sort emergency providers by distance.</small></div>
                    <div><span>2</span><strong>Confirm availability</strong><small>Call the selected provider and confirm that an ambulance is ready before relying on dispatch.</small></div>
                    <div><span>3</span><strong>Share patient details</strong><small>Give the dispatcher the pickup location, phone number, patient condition and destination if known.</small></div>
                    <div><span>4</span><strong>Track the request</strong><small>A future live fleet module can show ambulance status, driver, ETA and route once providers connect their fleet.</small></div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ========================================================= --}}
{{-- PROFILE MODAL                                              --}}
{{-- ========================================================= --}}
<div class="afya-modal" id="profileModal" role="dialog" aria-modal="true"
     aria-labelledby="profileModalLabel" aria-hidden="true">

    <div class="afya-modal-panel profile-panel">

        <div class="afya-modal-header">
            <div class="afya-modal-brand" id="profileModalLabel">
                <span class="afya-modal-brand-icon"><i data-lucide="heart-pulse" width="18" height="18"></i></span>
                AfyaLink Profile
            </div>

            <button type="button" class="afya-modal-close" data-close-modal="profileModal" aria-label="Close profile">
                <i data-lucide="x" width="18" height="18"></i>
            </button>
        </div>

        <div id="profileModalContent" aria-live="polite"></div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- LOGIN / SIGNUP MODAL                                       --}}
{{-- ========================================================= --}}
<div class="afya-modal" id="authModal" role="dialog" aria-modal="true"
     aria-labelledby="authModalLabel" aria-hidden="true">

    <div class="afya-modal-panel small">

        <div class="afya-modal-header">
            <div class="afya-modal-brand" id="authModalLabel">
                <span class="afya-modal-brand-icon"><i data-lucide="shield-check" width="18" height="18"></i></span>
                Patient Account
            </div>

            <button type="button" class="afya-modal-close" data-close-modal="authModal" aria-label="Close">
                <i data-lucide="x" width="18" height="18"></i>
            </button>
        </div>

        <div class="afya-auth-intro">
            <div class="afya-auth-icon"><i data-lucide="calendar-heart" width="28" height="28"></i></div>
            <h3 class="afya-auth-title">Sign in to book your visit</h3>
            <p class="afya-auth-description">
                Appointments are linked to your private AfyaLink patient account so providers can
                identify your booking correctly.
            </p>
        </div>

        <div class="afya-auth-tabs" role="tablist" aria-label="Account">
            <button type="button" class="afya-auth-tab active" role="tab" id="tabLogin"
                    aria-selected="true" aria-controls="authLoginPanel" data-auth-tab="login">Sign In</button>
            <button type="button" class="afya-auth-tab" role="tab" id="tabRegister"
                    aria-selected="false" aria-controls="authRegisterPanel" data-auth-tab="register">Create Account</button>
        </div>


        {{-- LOGIN --}}
        <div class="afya-auth-panel active" id="authLoginPanel" role="tabpanel" aria-labelledby="tabLogin">

            <div class="afya-auth-message" id="loginMessage" role="alert"></div>

            <form id="modalLoginForm" novalidate>
                @csrf

                <div class="afya-form-grid">

                    <label class="afya-form-group full">
                        <span class="afya-label">Email address</span>
                        <input class="afya-input" type="email" name="email" required autocomplete="email">
                    </label>

                    <label class="afya-form-group full">
                        <span class="afya-label">Password</span>
                        <input class="afya-input" type="password" name="password" required autocomplete="current-password">
                    </label>

                    <div class="afya-form-group full">
                        <label class="afya-consent">
                            <input type="checkbox" name="remember" value="1">
                            Keep me signed in on this device.
                        </label>
                    </div>

                    <div class="afya-form-group full">
                        <button type="submit" class="btn btn-primary">Sign In &amp; Continue</button>
                    </div>

                </div>

            </form>

            <div class="afya-auth-footer">
                Are you a healthcare provider? <a href="/login">Provider sign in</a>
            </div>

        </div>


        {{-- REGISTER --}}
        <div class="afya-auth-panel" id="authRegisterPanel" role="tabpanel" aria-labelledby="tabRegister">

            <div class="afya-auth-message" id="registerMessage" role="alert"></div>

            <form id="modalRegisterForm" novalidate>
                @csrf

                <div class="afya-form-grid">

                    <label class="afya-form-group">
                        <span class="afya-label">Full name</span>
                        <input class="afya-input" name="name" required autocomplete="name">
                    </label>

                    <label class="afya-form-group">
                        <span class="afya-label">Phone number</span>
                        <input class="afya-input" type="tel" name="phone" required autocomplete="tel" inputmode="tel">
                    </label>

                    <label class="afya-form-group full">
                        <span class="afya-label">Email address</span>
                        <input type="email" class="afya-input" name="email" required autocomplete="email">
                    </label>

                    <label class="afya-form-group">
                        <span class="afya-label">Password</span>
                        <input type="password" class="afya-input" name="password" required minlength="8" autocomplete="new-password">
                    </label>

                    <label class="afya-form-group">
                        <span class="afya-label">Confirm password</span>
                        <input type="password" class="afya-input" name="password_confirmation" required minlength="8" autocomplete="new-password">
                    </label>

                    <label class="afya-form-group">
                        <span class="afya-label">Date of birth</span>
                        <input type="date" class="afya-input" name="date_of_birth" id="registerDob" required autocomplete="bday">
                    </label>

                    <label class="afya-form-group">
                        <span class="afya-label">Gender</span>
                        <select class="afya-select" name="gender">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                            <option value="prefer_not_to_say">Prefer not to say</option>
                        </select>
                    </label>

                    <details class="afya-details">
                        <summary>Health information (optional)</summary>
                        <p class="afya-details-note">
                            You can skip this now and add it later. Share only what you are comfortable with.
                        </p>

                        <div class="afya-form-grid">

                            <label class="afya-form-group full">
                                <span class="afya-label">Blood group</span>
                                <select class="afya-select" name="blood_group">
                                    <option value="">Unknown / Select</option>
                                    @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $blood)
                                        <option value="{{ $blood }}">{{ $blood }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="afya-form-group full">
                                <span class="afya-label">Known allergies</span>
                                <textarea class="afya-textarea" name="allergies" placeholder="Example: penicillin, peanuts..."></textarea>
                            </label>

                            <label class="afya-form-group full">
                                <span class="afya-label">Existing health conditions</span>
                                <textarea class="afya-textarea" name="chronic_conditions"></textarea>
                            </label>

                            <label class="afya-form-group full">
                                <span class="afya-label">Current medication</span>
                                <textarea class="afya-textarea" name="current_medications"></textarea>
                            </label>

                        </div>
                    </details>

                    <label class="afya-form-group">
                        <span class="afya-label">Emergency contact</span>
                        <input class="afya-input" name="emergency_contact_name" autocomplete="off">
                    </label>

                    <label class="afya-form-group">
                        <span class="afya-label">Emergency phone</span>
                        <input class="afya-input" type="tel" name="emergency_contact_phone" autocomplete="off" inputmode="tel">
                    </label>

                    <div class="afya-form-group full">
                        <label class="afya-consent">
                            <input type="checkbox" name="health_data_consent" value="1" required>
                            <span>
                                I agree to AfyaLink storing the information I enter here for my patient profile
                                and healthcare booking experience.
                                @if ($hasRoute('privacy'))
                                    <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Read the privacy policy</a>.
                                @endif
                            </span>
                        </label>
                    </div>

                    <div class="afya-form-group full">
                        <button type="submit" class="btn btn-primary">Create Account &amp; Continue</button>
                    </div>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- BOOKING MODAL                                              --}}
{{-- ========================================================= --}}
<div class="afya-modal" id="bookingModal" role="dialog" aria-modal="true"
     aria-labelledby="bookingModalLabel" aria-hidden="true">

    <div class="afya-modal-panel small">

        <div class="afya-modal-header">
            <div class="afya-modal-brand" id="bookingModalLabel">
                <span class="afya-modal-brand-icon"><i data-lucide="calendar-heart" width="18" height="18"></i></span>
                Book Visit
            </div>

            <button type="button" class="afya-modal-close" data-close-modal="bookingModal" aria-label="Close">
                <i data-lucide="x" width="18" height="18"></i>
            </button>
        </div>

        <div style="padding:28px">

            <div class="afya-auth-message" id="bookingMessage" role="alert"></div>

            <div class="afya-booking-summary">
                <div class="afya-booking-summary-title" id="bookingTargetName">Healthcare provider</div>
                <div class="afya-booking-summary-text" id="bookingTargetType">Appointment</div>
            </div>

            <form id="appointmentForm" novalidate>
                @csrf

                <input type="hidden" name="healthcare_provider_id" id="bookingProviderId">
                <input type="hidden" name="doctor_id" id="bookingDoctorId">
                <input type="hidden" name="appointment_type" value="in_person">

                <div class="afya-form-grid">

                    <label class="afya-form-group">
                        <span class="afya-label">Preferred date</span>
                        <input type="date" class="afya-input" name="appointment_date" id="bookingDate" required>
                    </label>

                    <label class="afya-form-group">
                        <span class="afya-label">Preferred time</span>
                        <input type="time" class="afya-input" name="appointment_time" required>
                    </label>

                    <label class="afya-form-group full">
                        <span class="afya-label">Reason for visit</span>
                        <textarea class="afya-textarea" name="reason"
                                  placeholder="Briefly tell the provider why you would like the appointment."></textarea>
                    </label>

                    <div class="afya-form-group full">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="calendar-check" width="17" height="17"></i>
                            Request Appointment
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- JAVASCRIPT LIBRARIES                                       --}}
{{-- ========================================================= --}}
<script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* =====================================================
       DATA + HELPERS
    ===================================================== */

    const ROUTES    = @json($pageRoutes);
    const providers = @json($mapProviderData);

    const heroImage = document.getElementById('welcomeHeroImage');
    const heroSlides = [
        { src: '/images/hero-doctor.jpg', alt: 'African healthcare professional' },
        { src: '/images/hospital.jpg', alt: 'Healthcare facility' },
        { src: '/images/medical-clinic.jpg', alt: 'Healthcare consultation' }
    ];
    let heroSlideIndex = 0;

    const loadedHeroSlides = heroSlides.map(function (slide) {
        const image = new Image();
        image.src = slide.src;
        return image;
    });

    if (heroImage) {
        window.setInterval(function () {
            if (document.hidden) return;
            for (let offset = 1; offset < heroSlides.length; offset++) {
                const nextIndex = (heroSlideIndex + offset) % heroSlides.length;
                const nextImage = loadedHeroSlides[nextIndex];
                if (!nextImage.complete || !nextImage.naturalWidth) continue;
                heroSlideIndex = nextIndex;
                heroImage.src = heroSlides[nextIndex].src;
                heroImage.alt = heroSlides[nextIndex].alt;
                break;
            }
        }, 5000);
    }

    const reduceMotion   = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const scrollBehavior = reduceMotion ? 'auto' : 'smooth';

    const ESC = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) { return ESC[c]; });
    }

    function label(value) {
        return String(value == null ? '' : value).replace(/_/g, ' ').trim();
    }

    function imageUrl(path, fallback) {
        if (!path) return fallback;
        path = String(path).trim();
        if (/^(images|storage)\//.test(path)) return '/' + path;
        if (/^(https?:)?\/\//.test(path) || path.charAt(0) === '/') return path;
        return '/storage/' + path.replace(/^\/+/, '');
    }

    function placeOf(o) {
        const seen = [];
        [o.town, o.district].forEach(function (v) { if (v && seen.indexOf(v) === -1) seen.push(v); });
        return seen.join(', ');
    }

    function ratingText(r) {
        const n = Number(r);
        return n > 0 ? '\u2605 ' + n.toFixed(1) : 'New';
    }

    function cleanPhone(p) {
        return String(p || '').replace(/[^\d+]/g, '');
    }

    function directionsUrl(lat, lng) {
        return 'https://www.google.com/maps/dir/?api=1&destination=' + lat + ',' + lng;
    }

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function localDateString() {
        const d = new Date();
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        return d.toISOString().split('T')[0];
    }

    function renderIcons() {
        if (!window.lucide) return;
        lucide.createIcons();
        document.querySelectorAll('svg.lucide').forEach(function (s) { s.setAttribute('aria-hidden', 'true'); });
    }

    renderIcons();


    /* =====================================================
       MULTILINGUAL HERO + QUICKBOT LANGUAGE
       Community translations should be reviewed by native
       speakers before production publication.
    ===================================================== */

    const UI_LANGUAGES = {
        en: {
            name: 'English',
            badge: 'Your connection to better healthcare',
            primary: 'Healthcare in Uganda',
            secondary: 'made easier to find.',
            description: 'Search hospitals, clinics, pharmacies, laboratories and doctors across Uganda. Browse healthcare services, districts, ratings and locations, then book your visit.',
            botPlaceholder: 'Ask QuickBot anything about nearby care…',
            comfort: 'I’m sorry you are dealing with this. I can help you think through the next safe step and find nearby care.'
        },
        lg: {
            name: 'Luganda',
            badge: 'Tukuyamba okufuna obujjanjabi obulungi',
            primary: 'Obujjanjabi mu Uganda',
            secondary: 'bufuuke bwangu okubufuna.',
            description: 'Noonya amalwaliro, kliniki, eddagala n’abasawo mu Uganda. Laba ebifo n’obuweereza, oluvannyuma osabe okulaba omusawo.',
            botPlaceholder: 'Buuza QuickBot ku bujjanjabi obuli okumpi…',
            comfort: 'Nsaasira olw’ekikweraliikiriza. Nsobola okukuyamba okunoonya obujjanjabi obuli okumpi.'
        },
        sw: {
            name: 'Kiswahili',
            badge: 'Kiungo chako cha huduma bora za afya',
            primary: 'Huduma za afya Uganda',
            secondary: 'rahisi zaidi kuzipata.',
            description: 'Tafuta hospitali, kliniki, maduka ya dawa, maabara na madaktari kote Uganda. Linganisha huduma, maeneo na taarifa kisha omba miadi.',
            botPlaceholder: 'Uliza QuickBot kuhusu huduma ya karibu…',
            comfort: 'Pole kwa hali unayopitia. Naweza kukusaidia kufikiria hatua salama inayofuata na kupata huduma ya karibu.'
        },
        laj: {
            name: 'Lango / Langi',
            badge: 'AfyaLink tye ka konyi nongo yat',
            primary: 'Yat i Uganda',
            secondary: 'obed yot me nongo.',
            description: 'Yeny ot yat, kliniki, pharmacy ki jo yat. AfyaLink konyi nongo kabedo me yat ki jo tic me konyi.',
            botPlaceholder: 'Penj QuickBot pi yat ma tye cok…',
            comfort: 'Awinyo cwer cwinyi. Atwero konyi yeny yoo ma ber me nongo kony.'
        },
        lgg: {
            name: 'Lugbara',
            badge: 'AfyaLink asi eri ma dria',
            primary: 'Nzoka ma dria Uganda',
            secondary: 'eri ni nzi.',
            description: 'Oru hospitali, kliniki, pharmacy, laboratory ni doctor dria Uganda. AfyaLink ni ma eri care ma tualu.',
            botPlaceholder: 'QuickBot ma ori healthcare…',
            comfort: 'QuickBot ni ma eri care ma tualu ni step ma safe.'
        },
        xog: {
            name: 'Lusoga',
            badge: 'AfyaLink ekuwaya ku bulamu obulungi',
            primary: 'Obujjanjabi mu Uganda',
            secondary: 'bube bwangu okunoonya.',
            description: 'Noonya amalwaliro, kliniki, amaduuka g’eddagala n’abasawo mu Uganda. Laba obuweereza n’ebifo era osabe okubonagana n’omusawo.',
            botPlaceholder: 'Buuza QuickBot ku bujjanjabi…',
            comfort: 'Nsaasira olw’ekikweraliikiriza. Nsobola okukuyamba okunoonya obujjanjabi obuli okumpi.'
        },
        nyn: {
            name: 'Runyankore',
            badge: 'AfyaLink nekwatanisa n’obujanjabi burungi',
            primary: 'Obujanjabi omuri Uganda',
            secondary: 'bworoba kushanga.',
            description: 'Shanga amarwariro, clinics, pharmacies, laboratories hamwe n’abashaho omuri Uganda. Reeba services n’emyanya, reero oshabe appointment.',
            botPlaceholder: 'Buuza QuickBot aha bujanjabi haihi…',
            comfort: 'Ninkubaasa kukuhwera kumanya ekikorwa ekirikukurataho n’okushanga obujanjabi haihi.'
        }
    };

    let selectedLanguage = localStorage.getItem('afyalink-language') || 'en';
    if (!UI_LANGUAGES[selectedLanguage]) selectedLanguage = 'en';

    const siteLanguage = document.getElementById('siteLanguage');
    const heroBadgeText = document.getElementById('heroBadgeText');
    const heroTitlePrimary = document.getElementById('heroTitlePrimary');
    const heroTitleSecondary = document.getElementById('heroTitleSecondary');
    const heroDescription = document.getElementById('heroDescription');
    const heroLanguageName = document.getElementById('heroLanguageName');

    function setSiteLanguage(code, userChosen) {
        const lang = UI_LANGUAGES[code] || UI_LANGUAGES.en;
        selectedLanguage = code;
        try { localStorage.setItem('afyalink-language', code); } catch (e) { /* storage may be blocked */ }

        if (siteLanguage) siteLanguage.value = code;
        if (heroBadgeText) heroBadgeText.textContent = lang.badge;
        if (heroTitlePrimary) {
            const svg = heroTitlePrimary.querySelector('svg');
            Array.from(heroTitlePrimary.childNodes).forEach(function (node) {
                if (node.nodeType === Node.TEXT_NODE) node.remove();
            });
            heroTitlePrimary.insertBefore(document.createTextNode(lang.primary + ' '), svg || null);
        }
        if (heroTitleSecondary) heroTitleSecondary.textContent = lang.secondary;
        if (heroDescription) heroDescription.textContent = lang.description;
        if (heroLanguageName) heroLanguageName.textContent = lang.name;

        const qbLanguage = document.getElementById('quickbotLanguage');
        if (qbLanguage) qbLanguage.value = code;
        const qbInput = document.getElementById('chatInput');
        if (qbInput) qbInput.placeholder = lang.botPlaceholder;

        if (userChosen && window.__afyaLanguageRotation) {
            clearInterval(window.__afyaLanguageRotation);
            window.__afyaLanguageRotation = null;
        }
    }

    if (siteLanguage) {
        siteLanguage.value = selectedLanguage;
        siteLanguage.addEventListener('change', function () {
            setSiteLanguage(siteLanguage.value, true);
        });
    }

    setSiteLanguage(selectedLanguage, false);

    const rotatingCodes = Object.keys(UI_LANGUAGES);
    let rotateIndex = Math.max(0, rotatingCodes.indexOf(selectedLanguage));
    window.__afyaLanguageRotation = setInterval(function () {
        if (document.hidden || document.querySelector('.afya-modal.open')) return;
        rotateIndex = (rotateIndex + 1) % rotatingCodes.length;
        setSiteLanguage(rotatingCodes[rotateIndex], false);
    }, 7000);


    /* =====================================================
       GUIDED CARE SLIDER
    ===================================================== */

    const guidedTrack = document.getElementById('guidedSliderTrack');
    const guidedSlides = guidedTrack ? Array.from(guidedTrack.querySelectorAll('.guided-slide')) : [];
    const guidedDots = document.getElementById('guidedDots');
    const guidedPrev = document.getElementById('guidedPrev');
    const guidedNext = document.getElementById('guidedNext');
    let guidedIndex = 0;

    function renderGuidedSlide() {
        if (!guidedTrack || !guidedSlides.length) return;
        guidedTrack.style.transform = 'translateX(-' + (guidedIndex * 100) + '%)';
        if (guidedDots) {
            guidedDots.querySelectorAll('.guided-dot').forEach(function (dot, i) {
                dot.classList.toggle('active', i === guidedIndex);
                dot.setAttribute('aria-current', i === guidedIndex ? 'true' : 'false');
            });
        }
    }

    function goGuided(delta) {
        if (!guidedSlides.length) return;
        guidedIndex = (guidedIndex + delta + guidedSlides.length) % guidedSlides.length;
        renderGuidedSlide();
    }

    if (guidedDots && guidedSlides.length) {
        guidedDots.innerHTML = guidedSlides.map(function (_, i) {
            return '<button type="button" class="guided-dot' + (i === 0 ? ' active' : '') + '" aria-label="Go to care topic ' + (i + 1) + '" data-guided-index="' + i + '"></button>';
        }).join('');
        guidedDots.addEventListener('click', function (event) {
            const dot = event.target.closest('[data-guided-index]');
            if (!dot) return;
            guidedIndex = Number(dot.dataset.guidedIndex) || 0;
            renderGuidedSlide();
        });
    }

    if (guidedPrev) guidedPrev.addEventListener('click', function () { goGuided(-1); });
    if (guidedNext) guidedNext.addEventListener('click', function () { goGuided(1); });

    if (guidedSlides.length && !reduceMotion) {
        setInterval(function () {
            if (!document.hidden && !document.querySelector('.afya-modal.open')) goGuided(1);
        }, 6500);
    }


    /* =====================================================
       MODAL ENGINE (profile, auth, booking)
    ===================================================== */

    const profileModal   = document.getElementById('profileModal');
    const profileContent = document.getElementById('profileModalContent');
    const authModal      = document.getElementById('authModal');
    const bookingModal   = document.getElementById('bookingModal');
    const serviceBrowserModal = document.getElementById('serviceBrowserModal');
    const ambulanceModal = document.getElementById('ambulanceModal');
    const conditionModal = document.getElementById('conditionModal');
    const reportModal = document.getElementById('reportModal');

    function openModal(modal, trigger) {
        if (!modal) return;

        if (!modal.classList.contains('open')) {
            modal.__returnFocus = trigger || document.activeElement;
        }

        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');

        const closeBtn = modal.querySelector('.afya-modal-close');
        if (closeBtn) setTimeout(function () { closeBtn.focus({ preventScroll: true }); }, 30);
    }

    function closeModal(modal) {
        if (!modal || !modal.classList.contains('open')) return;

        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');

        const stillOpen = document.querySelector('.afya-modal.open');
        if (!stillOpen) document.body.classList.remove('modal-open');

        const back = modal.__returnFocus;
        modal.__returnFocus = null;

        if (!stillOpen && back && document.contains(back) && typeof back.focus === 'function') {
            try { back.focus({ preventScroll: true }); } catch (e) { /* ignore */ }
        }
    }

    function closeAllModals() {
        document.querySelectorAll('.afya-modal.open').forEach(closeModal);
    }

    [profileModal, authModal, bookingModal, serviceBrowserModal, ambulanceModal, conditionModal, reportModal].forEach(function (modal) {
        if (!modal) return;
        modal.addEventListener('mousedown', function (event) {
            if (event.target === modal) closeModal(modal);
        });
    });

    // keep Tab focus inside the open modal
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Tab') return;

        const open = document.querySelectorAll('.afya-modal.open');
        if (!open.length) return;

        const modal = open[open.length - 1];
        const items = Array.prototype.filter.call(
            modal.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), summary'),
            function (el) { return el.offsetParent !== null; }
        );

        if (!items.length) return;

        const first = items[0];
        const last = items[items.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        } else if (!modal.contains(document.activeElement)) {
            event.preventDefault();
            first.focus();
        }
    });


    /* =====================================================
       MOBILE MENU
    ===================================================== */

    const menuButton = document.getElementById('menuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    function setMenu(open) {
        if (!menuButton || !mobileMenu) return;
        mobileMenu.classList.toggle('open', open);
        document.body.classList.toggle('menu-open', open);
        menuButton.setAttribute('aria-expanded', String(open));
        menuButton.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    }

    if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', function () {
            setMenu(!mobileMenu.classList.contains('open'));
        });

        mobileMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () { setMenu(false); });
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 1240) setMenu(false);
        });
    }


    /* =====================================================
       SCROLL SPY (active nav link)
    ===================================================== */

    const spyIds = ['home', 'about', 'services', 'providers', 'doctors', 'healthcare-map', 'emergency', 'download'];
    const navLinks = document.querySelectorAll('.desktop-nav a, .mobile-menu a:not(.btn)');

    function setActive(id) {
        navLinks.forEach(function (a) {
            const on = a.getAttribute('href') === '#' + id;
            a.classList.toggle('active', on);
            if (on) a.setAttribute('aria-current', 'true'); else a.removeAttribute('aria-current');
        });
    }

    if ('IntersectionObserver' in window) {
        const spy = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) setActive(entry.target.id);
            });
        }, { rootMargin: '-35% 0px -60% 0px' });

        spyIds.forEach(function (id) {
            const el = document.getElementById(id);
            if (el) spy.observe(el);
        });
    }


    /* =====================================================
       MAP  (a pin opens the profile modal)
    ===================================================== */

    const KNOWN_TYPES = ['hospital', 'clinic', 'pharmacy', 'laboratory'];
    const SYMBOLS = { hospital: 'H', clinic: 'C', pharmacy: 'P', laboratory: 'L' };

    let map = null;
    const layers = {};
    const markerById = new Map();
    const markerByName = new Map();

    function typeOf(t) {
        t = String(t || '').toLowerCase();
        return KNOWN_TYPES.indexOf(t) !== -1 ? t : 'other';
    }

    function validCoord(p) {
        return Number.isFinite(p.latitude) && Number.isFinite(p.longitude);
    }

    const legendButtons = document.querySelectorAll('.map-legend-item[data-layer]');

    function setLayerVisible(type, visible) {
        const group = layers[type];
        if (!map || !group) return;
        if (visible && !map.hasLayer(group)) map.addLayer(group);
        if (!visible && map.hasLayer(group)) map.removeLayer(group);
        legendButtons.forEach(function (b) {
            if (b.dataset.layer === type) b.setAttribute('aria-pressed', String(visible));
        });
    }

    legendButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            setLayerVisible(btn.dataset.layer, btn.getAttribute('aria-pressed') !== 'true');
        });
    });

    function focusProvider(id, name) {
        const section = document.getElementById('healthcare-map');
        if (section) section.scrollIntoView({ behavior: scrollBehavior, block: 'start' });
        if (!map) return;

        let marker = null;
        if (id !== undefined && id !== null && id !== '') marker = markerById.get(String(id));
        if (!marker && name) marker = markerByName.get(String(name).toLowerCase());
        if (!marker) return;

        setTimeout(function () {
            setLayerVisible(marker.__type, true);
            map.invalidateSize();
            map.setView(marker.getLatLng(), Math.max(map.getZoom(), 14), { animate: !reduceMotion });
            marker.openTooltip();
        }, reduceMotion ? 0 : 450);
    }

    const mapElement = document.getElementById('healthcareMap');

    if (mapElement && typeof L !== 'undefined') {

        map = L.map(mapElement, { scrollWheelZoom: false }).setView([1.3733, 32.2903], 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // enable wheel zoom only after the user interacts with the map
        map.on('click', function () { map.scrollWheelZoom.enable(); });
        map.on('mouseout', function () { map.scrollWheelZoom.disable(); });

        KNOWN_TYPES.concat(['other']).forEach(function (t) {
            layers[t] = L.layerGroup().addTo(map);
        });

        const bounds = [];

        providers.forEach(function (p) {
            if (!validCoord(p)) return;

            const t = typeOf(p.type);
            const symbol = SYMBOLS[t] || (String(p.type || '+').charAt(0).toUpperCase() || '+');

            const icon = L.divIcon({
                className: '',
                html: '<div class="afya-map-marker ' + t + '"><span>' + esc(symbol) + '</span></div>',
                iconSize: [38, 38],
                iconAnchor: [19, 38],
                tooltipAnchor: [0, -34]
            });

            const marker = L.marker([p.latitude, p.longitude], { icon: icon, keyboard: true, riseOnHover: true });

            marker.__type = t;

            marker.bindTooltip(esc(p.name), { direction: 'top', className: 'afya-tooltip' });

            marker.on('add', function () {
                const el = marker.getElement();
                if (el) el.setAttribute('aria-label', 'Open profile for ' + p.name);
            });

            // one consistent experience: a pin opens the same AfyaLink profile modal
            marker.on('click', function () {
                openProfile('provider', p.id, marker.getElement());
            });

            layers[t].addLayer(marker);

            markerById.set(String(p.id), marker);
            markerByName.set(String(p.name).toLowerCase(), marker);

            bounds.push([p.latitude, p.longitude]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [55, 55] });
        } else if (bounds.length === 1) {
            map.setView(bounds[0], 13);
        }

        window.addEventListener('load', function () { setTimeout(function () { map.invalidateSize(); }, 250); });
        window.addEventListener('resize', function () { map.invalidateSize(); });
    }


    /* =====================================================
       NEAREST TO ME (geolocation)
    ===================================================== */

    const locateButton = document.getElementById('locateButton');
    const locateStatus = document.getElementById('locateStatus');
    const nearestList  = document.getElementById('nearestList');
    let userMarker = null;

    function distanceKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const toRad = function (x) { return x * Math.PI / 180; };
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
            + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
        return 2 * R * Math.asin(Math.sqrt(a));
    }

    function setLocateStatus(text) {
        if (locateStatus) locateStatus.textContent = text;
    }

    function showNearest(lat, lng) {
        const withDistance = providers.filter(validCoord).map(function (p) {
            return { p: p, d: distanceKm(lat, lng, p.latitude, p.longitude) };
        }).sort(function (a, b) { return a.d - b.d; }).slice(0, 5);

        if (!withDistance.length) {
            nearestList.innerHTML = '';
            setLocateStatus('No facilities with map locations have been added yet.');
            return;
        }

        nearestList.innerHTML = withDistance.map(function (o) {
            const p = o.p;
            const meta = [label(p.type), placeOf(p)].filter(Boolean).join(' \u2022 ');

            return '<div class="nearest-item">'
                + '<div class="nearest-name">' + esc(p.name) + '</div>'
                + '<div class="nearest-meta">' + esc(meta) + ' \u2022 ' + o.d.toFixed(1) + ' km away</div>'
                + '<div class="nearest-actions">'
                +   '<button type="button" class="text-link" data-profile-type="provider" data-profile-id="' + esc(p.id) + '">View profile</button>'
                +   '<button type="button" class="text-link focus-provider" data-provider-id="' + esc(p.id) + '" data-provider-name="' + esc(p.name) + '">View on map</button>'
                +   '<a class="text-link" target="_blank" rel="noopener" href="' + directionsUrl(p.latitude, p.longitude) + '">Directions</a>'
                + '</div>'
                + '</div>';
        }).join('');

        setLocateStatus('Showing the ' + withDistance.length + ' closest facilities to you (straight-line distance).');

        if (map) {
            if (userMarker) map.removeLayer(userMarker);
            userMarker = L.circleMarker([lat, lng], {
                radius: 9, color: '#fff', weight: 3, fillColor: '#2563EB', fillOpacity: 1
            }).addTo(map).bindTooltip('You are here');

            const pts = [[lat, lng]].concat(withDistance.slice(0, 3).map(function (o) {
                return [o.p.latitude, o.p.longitude];
            }));
            map.fitBounds(pts, { padding: [60, 60], maxZoom: 15 });
        }
    }

    if (locateButton) {
        locateButton.addEventListener('click', function () {
            if (!navigator.geolocation) {
                setLocateStatus('Your browser does not support location. Try searching by district instead.');
                return;
            }

            setLocateStatus('Finding your location\u2026');
            locateButton.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    locateButton.disabled = false;
                    showNearest(pos.coords.latitude, pos.coords.longitude);
                },
                function (err) {
                    locateButton.disabled = false;
                    setLocateStatus(err && err.code === 1
                        ? 'Location permission was denied. You can still search by district.'
                        : 'We could not get your location. Please try again or search by district.');
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 }
            );
        });
    }


    /* =====================================================
       SEARCH CORE (shared by hero search + assistant)
    ===================================================== */

    const STOP = new Set([
        'in', 'at', 'near', 'nearby', 'me', 'my', 'the', 'a', 'an', 'of', 'for', 'and', 'find', 'show',
        'search', 'looking', 'look', 'i', 'need', 'want', 'to', 'around', 'get', 'is', 'are', 'any',
        'some', 'please', 'where', 'can', 'best', 'good', 'within', 'with', 'on', 'there'
    ]);

    function singular(t) {
        if (t.length > 4 && t.slice(-3) === 'ies') return t.slice(0, -3) + 'y';
        if (t.length > 3 && t.slice(-1) === 's' && t.slice(-2) !== 'ss') return t.slice(0, -1);
        return t;
    }

    function tokensOf(q) {
        return q.toLowerCase().replace(/[^a-z0-9\s'-]/g, ' ').split(/\s+/)
            .filter(function (t) { return t && !STOP.has(t); })
            .map(singular)
            .filter(function (t, i, a) { return a.indexOf(t) === i; });
    }

    function providerHay(p) {
        return [p.name, p.provider_type, p.town, p.district, p.ownership, label(p.ownership),
            p.address, p.description, (p.services || []).join(' '), p.emergency_available ? 'emergency' : '', p.open_24_hours ? '24 hours' : ''].join(' ').toLowerCase();
    }

    function doctorHay(d) {
        return ['doctor', d.name, d.specialty, d.district,
            d.healthcare_provider && d.healthcare_provider.name].join(' ').toLowerCase();
    }

    async function fetchSearch(q, signal, category) {
        const res = await fetch(ROUTES.search + '?q=' + encodeURIComponent(q) + (category ? '&category=' + encodeURIComponent(category) : ''), {
            headers: { 'Accept': 'application/json' },
            signal: signal
        });

        if (!res.ok) throw new Error('Search failed (' + res.status + ')');

        const d = await res.json();

        return {
            providers: Array.isArray(d.providers) ? d.providers : [],
            doctors: Array.isArray(d.doctors) ? d.doctors : []
        };
    }

    // Tries the whole phrase first; if the backend finds nothing it searches each keyword
    // ("hospitals in Kampala" -> hospital + kampala) and keeps records matching all of them.
    async function smartSearch(query, signal) {
        const direct = await fetchSearch(query, signal);
        if (direct.providers.length || direct.doctors.length) return direct;

        const toks = tokensOf(query).slice(0, 3);
        if (!toks.length || (toks.length === 1 && toks[0] === query.trim().toLowerCase())) return direct;

        const parts = await Promise.all(toks.map(function (t) {
            return fetchSearch(t, signal).catch(function (e) {
                if (e && e.name === 'AbortError') throw e;
                return { providers: [], doctors: [] };
            });
        }));

        const pMap = new Map();
        const dMap = new Map();

        parts.forEach(function (r) {
            r.providers.forEach(function (p) { pMap.set(p.id != null ? p.id : p.name, p); });
            r.doctors.forEach(function (d) { dMap.set(d.id != null ? d.id : d.name, d); });
        });

        function matchAll(list, hayFn) {
            return list.filter(function (x) {
                const hay = hayFn(x);
                return toks.every(function (t) { return hay.indexOf(t) !== -1; });
            }).sort(function (a, b) { return Number(b.rating || 0) - Number(a.rating || 0); });
        }

        return {
            providers: matchAll(Array.from(pMap.values()), providerHay),
            doctors: matchAll(Array.from(dMap.values()), doctorHay)
        };
    }


    /* =====================================================
       HERO LIVE SEARCH  (results open the profile modal)
    ===================================================== */

    const searchForm  = document.getElementById('liveSearchForm');
    const searchInput = document.getElementById('heroSearchInput');
    const resultsBox  = document.getElementById('liveSearchResults');

    let searchTimer = null;
    let activeController = null;
    let lastRequest = 0;

    function openResults() {
        resultsBox.classList.add('open');
        searchInput.setAttribute('aria-expanded', 'true');
    }

    function closeResults() {
        if (!resultsBox || !searchInput) return;
        resultsBox.classList.remove('open');
        searchInput.setAttribute('aria-expanded', 'false');
    }

    function showMessage(text) {
        resultsBox.innerHTML = '<div class="search-empty">' + esc(text) + '</div>';
        openResults();
    }

    function providerRow(p) {
        const meta = [label(p.provider_type), placeOf(p), label(p.ownership)].filter(Boolean).join(' \u2022 ');

        const attrs = p.id != null
            ? ' open-profile" data-profile-type="provider" data-profile-id="' + esc(p.id) + '"'
            : ' focus-provider" data-provider-id="" data-provider-name="' + esc(p.name) + '"';

        return '<button type="button" class="search-result-item' + attrs + '>'
            + '<img src="' + esc(imageUrl(p.image, '/images/profile-placeholder.svg')) + '" class="search-result-image" alt="" loading="lazy"'
            + ' onerror="this.onerror=null;this.src=\'/images/profile-placeholder.svg\'">'
            + '<span class="search-result-body">'
            +   '<span class="search-result-name">' + esc(p.name) + '</span>'
            +   '<span class="search-result-meta">' + esc(meta) + '</span>'
            + '</span>'
            + '<span class="search-result-rating">' + esc(ratingText(p.rating)) + '</span>'
            + '</button>';
    }

    function doctorRow(d) {
        const fac = d.healthcare_provider || null;
        const meta = [d.specialty, d.district, fac ? fac.name : 'Independent provider'].filter(Boolean).join(' \u2022 ');

        const inner = '<span class="search-result-icon"><i data-lucide="user-round" width="20" height="20"></i></span>'
            + '<span class="search-result-body">'
            +   '<span class="search-result-name">' + esc(d.name) + '</span>'
            +   '<span class="search-result-meta">' + esc(meta) + '</span>'
            + '</span>'
            + '<span class="search-result-rating">' + esc(ratingText(d.rating)) + '</span>';

        if (d.id != null) {
            return '<button type="button" class="search-result-item open-profile"'
                + ' data-profile-type="doctor" data-profile-id="' + esc(d.id) + '">' + inner + '</button>';
        }

        return '<div class="search-result-item">' + inner + '</div>';
    }

    function renderResults(data, query) {
        let html = '';

        if (data.providers.length) {
            html += '<div class="search-result-heading">Healthcare facilities</div>';
            data.providers.slice(0, 8).forEach(function (p) { html += providerRow(p); });
        }

        if (data.doctors.length) {
            html += '<div class="search-result-heading">Doctors</div>';
            data.doctors.slice(0, 8).forEach(function (d) { html += doctorRow(d); });
        }

        if (!html) {
            html = '<div class="search-empty">No results found for \u201C<strong>' + esc(query) + '</strong>\u201D.<br>'
                + 'Try a district (Kampala, Wakiso), a type (hospital, clinic, pharmacy) or a doctor\u2019s specialty.</div>';
        }

        resultsBox.innerHTML = html;
        openResults();
        renderIcons();
    }

    async function performSearch(custom) {
        const hasCustom = typeof custom === 'string';
        const query = (hasCustom ? custom : searchInput.value).trim();

        if (hasCustom) searchInput.value = custom;

        if (activeController) activeController.abort();

        if (query.length < 2) {
            closeResults();
            resultsBox.innerHTML = '';
            return;
        }

        const controller = new AbortController();
        activeController = controller;
        const requestId = ++lastRequest;

        showMessage('Searching AfyaLink\u2026');

        try {
            const data = await smartSearch(query, controller.signal);
            if (requestId !== lastRequest) return;
            renderResults(data, query);
        } catch (err) {
            if (err && err.name === 'AbortError') return;
            if (requestId !== lastRequest) return;
            console.error(err);
            showMessage('AfyaLink could not search right now. Please try again in a moment.');
        }
    }

    if (searchInput && searchForm && resultsBox) {

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () { performSearch(); }, 300);
        });

        searchForm.addEventListener('submit', function (event) {
            event.preventDefault();
            clearTimeout(searchTimer);

            if (searchInput.value.trim().length < 2) {
                showMessage('Type at least 2 characters to search.');
                return;
            }

            performSearch();
        });

        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowDown') {
                const first = resultsBox.querySelector('button.search-result-item');
                if (first) { event.preventDefault(); first.focus(); }
            } else if (event.key === 'Escape') {
                closeResults();
            }
        });

        resultsBox.addEventListener('keydown', function (event) {
            const items = Array.prototype.slice.call(resultsBox.querySelectorAll('button.search-result-item'));
            const index = items.indexOf(document.activeElement);

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                if (items[index + 1]) items[index + 1].focus();
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                if (index > 0) items[index - 1].focus(); else searchInput.focus();
            } else if (event.key === 'Escape') {
                closeResults();
                searchInput.focus();
            }
        });

        document.addEventListener('click', function (event) {
            if (!searchForm.contains(event.target) && !resultsBox.contains(event.target)) closeResults();
        });
    }


    /* =====================================================
       POPULAR + SERVICE SEARCH BUTTONS
    ===================================================== */

    document.querySelectorAll('.popular-search').forEach(function (el) {
        el.addEventListener('click', function (event) {
            event.preventDefault();

            const term = el.getAttribute('data-search');
            if (!term || !searchInput) return;

            window.scrollTo({ top: 0, behavior: scrollBehavior });

            setTimeout(function () {
                performSearch(term);
                searchInput.focus({ preventScroll: true });
            }, reduceMotion ? 0 : 350);
        });
    });



    /* =====================================================
       SMART SERVICE DIRECTORY (AJAX, NO PAGE RELOAD)
    ===================================================== */

    const directoryGrid = document.getElementById('directoryGrid');
    const directorySearch = document.getElementById('directorySearch');
    const directoryDistrict = document.getElementById('directoryDistrict');
    const directoryOwnership = document.getElementById('directoryOwnership');
    const directoryOpen24 = document.getElementById('directoryOpen24');
    const directoryEmergency = document.getElementById('directoryEmergency');
    const directoryStatus = document.getElementById('directoryStatus');
    const directoryCount = document.getElementById('directoryCount');
    const directoryTitle = document.getElementById('directoryTitle');
    const directoryEyebrow = document.getElementById('directoryEyebrow');
    const directoryNearMe = document.getElementById('directoryNearMe');

    let directoryCategory = 'clinic';
    let directoryData = { providers: [], doctors: [] };
    let directoryUserLocation = null;
    let directoryTimer = null;
    let directoryController = null;

    const directoryLabels = {
        hospital: 'Hospitals', clinic: 'Clinics', pharmacy: 'Pharmacies',
        laboratory: 'Laboratories', doctor: 'Doctors', emergency: 'Emergency Care'
    };

    function haversineKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const toRad = function (x) { return x * Math.PI / 180; };
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
            + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2))
            * Math.sin(dLon / 2) * Math.sin(dLon / 2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function populateDirectoryDistricts(data) {
        const values = [];
        data.providers.concat(data.doctors).forEach(function (item) {
            if (item.district && values.indexOf(item.district) === -1) values.push(item.district);
        });
        providers.forEach(function (item) {
            if (item.district && values.indexOf(item.district) === -1) values.push(item.district);
        });
        values.sort();

        const current = directoryDistrict.value;
        directoryDistrict.innerHTML = '<option value="">All districts</option>'
            + values.map(function (d) { return '<option value="' + esc(d) + '">' + esc(d) + '</option>'; }).join('');
        if (values.indexOf(current) !== -1) directoryDistrict.value = current;
    }

    function directoryMatchesProvider(p) {
        if (directoryCategory === 'doctor') return false;
        if (directoryCategory === 'emergency') {
            if (!p.emergency_available) return false;
        } else if (String(p.provider_type || '').toLowerCase() !== directoryCategory) {
            return false;
        }

        const district = directoryDistrict.value.toLowerCase();
        const ownership = directoryOwnership.value.toLowerCase();
        if (district && String(p.district || '').toLowerCase() !== district) return false;
        if (ownership && label(p.ownership).toLowerCase() !== ownership) return false;
        if (directoryOpen24.checked && !p.open_24_hours) return false;
        if (directoryEmergency.checked && !p.emergency_available) return false;
        return true;
    }

    function directoryMatchesDoctor(d) {
        if (directoryCategory !== 'doctor') return false;
        const district = directoryDistrict.value.toLowerCase();
        if (district && String(d.district || '').toLowerCase() !== district) return false;
        return true;
    }

    function directoryCardProvider(p) {
        const mapRecord = providers.find(function (x) { return String(x.id) === String(p.id); });
        let distance = '';
        if (directoryUserLocation && mapRecord && Number.isFinite(Number(mapRecord.latitude)) && Number.isFinite(Number(mapRecord.longitude))) {
            distance = haversineKm(directoryUserLocation.lat, directoryUserLocation.lng, Number(mapRecord.latitude), Number(mapRecord.longitude));
        }
        return '<article class="directory-card">'
            + '<div class="directory-card-media">'
            + '<img src="' + esc(imageUrl(p.image, '/images/profile-placeholder.svg')) + '" alt="' + esc(p.name) + '" onerror="this.onerror=null;this.src=\'/images/profile-placeholder.svg\'">'
            + '<span class="directory-card-badge">' + esc(label(p.provider_type)) + '</span>'
            + '</div>'
            + '<div class="directory-card-body">'
            + '<div class="directory-card-name">' + esc(p.name) + '</div>'
            + '<div class="directory-card-meta">' + esc([placeOf(p), label(p.ownership)].filter(Boolean).join(' • ')) + '</div>'
            + '<div class="directory-card-foot">'
            + '<span class="directory-card-rating">' + esc(ratingText(p.rating)) + (distance !== '' ? ' • ' + distance.toFixed(1) + ' km' : '') + '</span>'
            + (p.id != null ? '<button type="button" class="text-link open-profile" data-profile-type="provider" data-profile-id="' + esc(p.id) + '">View profile</button>' : '')
            + '</div></div></article>';
    }

    function directoryCardDoctor(d) {
        const fac = d.healthcare_provider || null;
        return '<article class="directory-card">'
            + '<div class="directory-card-media doctor">'
            + '<img src="' + esc(imageUrl(d.image, '/images/profile-placeholder.svg')) + '" alt="' + esc(d.name) + '" onerror="this.onerror=null;this.src=\'/images/profile-placeholder.svg\'">'
            + '<span class="directory-card-badge">Doctor</span>'
            + '</div>'
            + '<div class="directory-card-body">'
            + '<div class="directory-card-name">' + esc(d.name) + '</div>'
            + '<div class="directory-card-meta">' + esc([d.specialty, d.district, fac ? fac.name : ''].filter(Boolean).join(' • ')) + '</div>'
            + '<div class="directory-card-foot">'
            + '<span class="directory-card-rating">' + esc(ratingText(d.rating)) + '</span>'
            + (d.id != null ? '<button type="button" class="text-link open-profile" data-profile-type="doctor" data-profile-id="' + esc(d.id) + '">View profile</button>' : '')
            + '</div></div></article>';
    }

    function renderDirectory() {
        const ps = directoryData.providers.filter(directoryMatchesProvider);
        const ds = directoryData.doctors.filter(directoryMatchesDoctor);

        if (directoryUserLocation) {
            ps.sort(function (a, b) {
                function dist(p) {
                    const x = providers.find(function (m) { return String(m.id) === String(p.id); });
                    if (!x) return Infinity;
                    const lat = Number(x.latitude), lng = Number(x.longitude);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return Infinity;
                    return haversineKm(directoryUserLocation.lat, directoryUserLocation.lng, lat, lng);
                }
                return dist(a) - dist(b);
            });
        }

        const total = ps.length + ds.length;
        directoryCount.textContent = total + (total === 1 ? ' result' : ' results');
        directoryStatus.textContent = directoryUserLocation ? 'Sorted using your shared location where coordinates are available.' : '';

        let html = '';
        ps.forEach(function (p) { html += directoryCardProvider(p); });
        ds.forEach(function (d) { html += directoryCardDoctor(d); });
        if (!html) html = '<div class="directory-empty"><strong>No matching results.</strong><br>Try another district, filter or search term.</div>';
        directoryGrid.innerHTML = html;
        renderIcons();
    }

    async function loadDirectory() {
        if (!directoryGrid) return;
        const query = directorySearch.value.trim();
        const searchTerm = query;

        if (directoryController) directoryController.abort();
        directoryController = new AbortController();
        directoryStatus.textContent = 'Searching AfyaLink…';
        directoryGrid.innerHTML = '<div class="directory-empty">Loading healthcare results…</div>';

        try {
            directoryData = await fetchSearch(searchTerm, directoryController.signal, directoryCategory);
            populateDirectoryDistricts(directoryData);
            renderDirectory();
        } catch (err) {
            if (err && err.name === 'AbortError') return;
            console.error(err);
            directoryStatus.textContent = 'Could not load results right now.';
            directoryGrid.innerHTML = '<div class="directory-empty">AfyaLink could not load this category. Please try again.</div>';
        }
    }

    function openDirectory(category, trigger) {
        directoryCategory = category || 'clinic';
        directorySearch.value = '';
        directoryDistrict.value = '';
        directoryOwnership.value = '';
        directoryOpen24.checked = false;
        directoryEmergency.checked = directoryCategory === 'emergency';
        directoryTitle.textContent = 'Browse ' + (directoryLabels[directoryCategory] || 'Healthcare');
        directoryEyebrow.textContent = (directoryLabels[directoryCategory] || 'Healthcare').toUpperCase();

        document.querySelectorAll('[data-directory-category]').forEach(function (button) {
            button.classList.toggle('active', button.dataset.directoryCategory === directoryCategory);
        });

        openModal(serviceBrowserModal, trigger);
        loadDirectory();
    }

    document.querySelectorAll('.service-browser-trigger').forEach(function (button) {
        button.addEventListener('click', function () { openDirectory(button.dataset.category, button); });
    });

    document.querySelectorAll('[data-directory-category]').forEach(function (button) {
        button.addEventListener('click', function () {
            directoryCategory = button.dataset.directoryCategory;
            directoryTitle.textContent = 'Browse ' + (directoryLabels[directoryCategory] || 'Healthcare');
            directoryEyebrow.textContent = (directoryLabels[directoryCategory] || 'Healthcare').toUpperCase();
            directoryEmergency.checked = directoryCategory === 'emergency';
            document.querySelectorAll('[data-directory-category]').forEach(function (x) { x.classList.toggle('active', x === button); });
            loadDirectory();
        });
    });

    if (directorySearch) directorySearch.addEventListener('input', function () {
        clearTimeout(directoryTimer);
        directoryTimer = setTimeout(loadDirectory, 280);
    });
    [directoryDistrict, directoryOwnership, directoryOpen24, directoryEmergency].forEach(function (el) {
        if (el) el.addEventListener('change', renderDirectory);
    });

    if (directoryNearMe) directoryNearMe.addEventListener('click', function () {
        if (!navigator.geolocation) {
            directoryStatus.textContent = 'Location is not available in this browser.';
            return;
        }
        directoryStatus.textContent = 'Requesting your location…';
        navigator.geolocation.getCurrentPosition(function (pos) {
            directoryUserLocation = { lat: pos.coords.latitude, lng: pos.coords.longitude };
            renderDirectory();
        }, function () {
            directoryStatus.textContent = 'Location was not shared. You can still filter by district.';
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
    });


    /* =====================================================
       GUIDED CARE / CONDITION SEARCH MODAL
    ===================================================== */

    const CONDITION_CONFIG = {
        maternal: {
            eyebrow: 'MATERNAL & MIDWIFERY CARE',
            title: 'Talk to a midwife or maternity expert',
            description: 'Find nearby midwives, maternity clinics, obstetric services and hospitals that may support pregnancy and childbirth.',
            queries: ['midwife', 'maternity', 'antenatal', 'obstetric', 'pregnancy']
        },
        children: {
            eyebrow: 'CHILD HEALTH',
            title: 'Find care for your child',
            description: 'Explore pediatricians, child-health clinics and hospitals with child-focused services.',
            queries: ['pediatric', 'child health', 'children', 'paediatric']
        },
        prostate: {
            eyebrow: 'PROSTATE & UROLOGY',
            title: 'Find prostate and urology care',
            description: 'Explore urologists, diagnostic services, clinics and hospitals that may assess prostate concerns.',
            queries: ['urology', 'urologist', 'prostate']
        },
        dental: {
            eyebrow: 'DENTAL CARE',
            title: 'Find a dentist or dental clinic',
            description: 'Search dental clinics, dentists and healthcare facilities for oral-health concerns.',
            queries: ['dentist', 'dental', 'oral health']
        },
        accident: {
            eyebrow: 'ACCIDENT & TRAUMA',
            title: 'Find urgent accident and trauma care',
            description: 'Search emergency-capable facilities and trauma services. If someone is in immediate danger, call emergency help now.',
            queries: ['emergency', 'trauma', 'accident']
        },
        specialist: {
            eyebrow: 'SPECIALIST CONSULTATION',
            title: 'Find the specialist you need',
            description: 'Search doctors, clinics and hospitals by specialty, condition or service.',
            queries: ['specialist', 'doctor', 'clinic', 'hospital']
        }
    };

    const conditionTitle = document.getElementById('conditionTitle');
    const conditionEyebrow = document.getElementById('conditionEyebrow');
    const conditionDescription = document.getElementById('conditionDescription');
    const conditionCount = document.getElementById('conditionCount');
    const conditionSearch = document.getElementById('conditionSearch');
    const conditionDistrict = document.getElementById('conditionDistrict');
    const conditionHospitals = document.getElementById('conditionHospitals');
    const conditionClinics = document.getElementById('conditionClinics');
    const conditionDoctors = document.getElementById('conditionDoctors');
    const conditionNearMe = document.getElementById('conditionNearMe');
    const conditionStatus = document.getElementById('conditionStatus');
    const conditionGrid = document.getElementById('conditionGrid');

    let activeCondition = 'maternal';
    let conditionData = { providers: [], doctors: [] };
    let conditionLocation = null;
    let conditionTimer = null;
    let conditionAbort = null;

    function mergeSearchData(results) {
        const p = new Map(), d = new Map();
        results.forEach(function (r) {
            (r.providers || []).forEach(function (x) { p.set(x.id != null ? x.id : x.name, x); });
            (r.doctors || []).forEach(function (x) { d.set(x.id != null ? x.id : x.name, x); });
        });
        return { providers: Array.from(p.values()), doctors: Array.from(d.values()) };
    }

    function conditionDoctorDistance(d) {
        if (!conditionLocation) return Infinity;
        const fac = d.healthcare_provider || null;
        if (!fac) return Infinity;
        const mapped = providers.find(function (m) {
            return (fac.id != null && String(m.id) === String(fac.id))
                || (fac.name && String(m.name).toLowerCase() === String(fac.name).toLowerCase());
        });
        if (!mapped) return Infinity;
        const lat = Number(mapped.latitude), lng = Number(mapped.longitude);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return Infinity;
        return haversineKm(conditionLocation.lat, conditionLocation.lng, lat, lng);
    }

    function conditionProviderDistance(p) {
        if (!conditionLocation) return Infinity;
        const mapped = providers.find(function (m) { return String(m.id) === String(p.id); });
        if (!mapped) return Infinity;
        const lat = Number(mapped.latitude), lng = Number(mapped.longitude);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return Infinity;
        return haversineKm(conditionLocation.lat, conditionLocation.lng, lat, lng);
    }

    function populateConditionDistricts() {
        const all = [];
        conditionData.providers.concat(conditionData.doctors).forEach(function (x) {
            if (x.district && all.indexOf(x.district) === -1) all.push(x.district);
        });
        providers.forEach(function (x) {
            if (x.district && all.indexOf(x.district) === -1) all.push(x.district);
        });
        all.sort();
        const current = conditionDistrict.value;
        conditionDistrict.innerHTML = '<option value="">All districts</option>'
            + all.map(function (d) { return '<option value="' + esc(d) + '">' + esc(d) + '</option>'; }).join('');
        if (all.indexOf(current) !== -1) conditionDistrict.value = current;
    }

    function renderConditionResults() {
        if (!conditionGrid) return;

        const district = String(conditionDistrict.value || '').toLowerCase();
        const ps = conditionData.providers.filter(function (p) {
            const type = String(p.provider_type || '').toLowerCase();
            if (type === 'hospital' && !conditionHospitals.checked) return false;
            if (type === 'clinic' && !conditionClinics.checked) return false;
            if (type !== 'hospital' && type !== 'clinic') return false;
            if (district && String(p.district || '').toLowerCase() !== district) return false;
            return true;
        });

        const ds = conditionData.doctors.filter(function (d) {
            if (!conditionDoctors.checked) return false;
            if (district && String(d.district || '').toLowerCase() !== district) return false;
            return true;
        });

        if (conditionLocation) {
            ps.sort(function (a,b) { return conditionProviderDistance(a) - conditionProviderDistance(b); });
            ds.sort(function (a,b) { return conditionDoctorDistance(a) - conditionDoctorDistance(b); });
        }

        const total = ps.length + ds.length;
        conditionCount.textContent = total + (total === 1 ? ' result' : ' results');

        let html = '';
        ds.forEach(function (d) { html += directoryCardDoctor(d); });
        ps.forEach(function (p) { html += directoryCardProvider(p); });

        if (!html) {
            html = '<div class="directory-empty"><strong>No matching care found yet.</strong><br>'
                + 'Try another district or a broader term. AfyaLink listings grow as providers register.</div>';
        }

        conditionGrid.innerHTML = html;
        renderIcons();
    }

    async function loadConditionResults() {
        const cfg = CONDITION_CONFIG[activeCondition] || CONDITION_CONFIG.specialist;
        const typed = conditionSearch.value.trim();
        const queries = typed ? [typed] : cfg.queries;

        if (conditionAbort) conditionAbort.abort();
        conditionAbort = new AbortController();

        conditionStatus.textContent = 'Searching AfyaLink for relevant care…';
        conditionGrid.innerHTML = '<div class="directory-empty">Finding doctors, clinics and hospitals…</div>';

        try {
            const results = await Promise.all(queries.slice(0, 5).map(function (q) {
                return smartSearch(q, conditionAbort.signal).catch(function (e) {
                    if (e && e.name === 'AbortError') throw e;
                    return { providers: [], doctors: [] };
                });
            }));

            conditionData = mergeSearchData(results);
            populateConditionDistricts();
            conditionStatus.textContent = conditionLocation
                ? 'Results are sorted using your shared location where map data is available.'
                : 'Use “Near me” to sort mapped facilities by distance.';
            renderConditionResults();
        } catch (e) {
            if (e && e.name === 'AbortError') return;
            console.error(e);
            conditionStatus.textContent = 'Could not load guided-care results right now.';
            conditionGrid.innerHTML = '<div class="directory-empty">Please try again in a moment.</div>';
        }
    }

    function openConditionCare(key, trigger) {
        activeCondition = CONDITION_CONFIG[key] ? key : 'specialist';
        const cfg = CONDITION_CONFIG[activeCondition];

        conditionEyebrow.textContent = cfg.eyebrow;
        conditionTitle.textContent = cfg.title;
        conditionDescription.textContent = cfg.description;
        conditionSearch.value = '';
        conditionDistrict.value = '';
        conditionHospitals.checked = true;
        conditionClinics.checked = true;
        conditionDoctors.checked = true;
        conditionLocation = null;

        openModal(conditionModal, trigger);
        loadConditionResults();
    }

    document.querySelectorAll('.guided-care-trigger').forEach(function (button) {
        button.addEventListener('click', function () {
            openConditionCare(button.dataset.condition, button);
        });
    });

    if (conditionSearch) {
        conditionSearch.addEventListener('input', function () {
            clearTimeout(conditionTimer);
            conditionTimer = setTimeout(loadConditionResults, 260);
        });
    }

    [conditionDistrict, conditionHospitals, conditionClinics, conditionDoctors].forEach(function (el) {
        if (el) el.addEventListener('change', renderConditionResults);
    });

    if (conditionNearMe) {
        conditionNearMe.addEventListener('click', function () {
            if (!navigator.geolocation) {
                conditionStatus.textContent = 'Location is not available in this browser. Filter by district instead.';
                return;
            }

            conditionStatus.textContent = 'Finding your location…';

            navigator.geolocation.getCurrentPosition(function (pos) {
                conditionLocation = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                conditionStatus.textContent = 'Location shared. Sorting mapped care options by distance.';
                renderConditionResults();
            }, function () {
                conditionStatus.textContent = 'Location was not shared. You can still filter by district.';
            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
        });
    }


    /* =====================================================
       EMERGENCY TRANSPORT / AMBULANCE FINDER
    ===================================================== */

    const openAmbulanceModalBtn = document.getElementById('openAmbulanceModal');
    const ambulanceUseLocation = document.getElementById('ambulanceUseLocation');
    const ambulanceGrid = document.getElementById('ambulanceGrid');
    const ambulanceLocationStatus = document.getElementById('ambulanceLocationStatus');

    function emergencyProviders() {
        return providers.filter(function (p) { return !!(p.emergency_available || p.emergency); });
    }

    function renderAmbulanceProviders(origin) {
        const list = emergencyProviders().map(function (p) {
            const lat = Number(p.latitude), lng = Number(p.longitude);
            let distance = null;
            if (origin && Number.isFinite(lat) && Number.isFinite(lng)) distance = haversineKm(origin.lat, origin.lng, lat, lng);
            return { provider: p, distance: distance };
        });

        if (origin) list.sort(function (a, b) { return (a.distance == null ? Infinity : a.distance) - (b.distance == null ? Infinity : b.distance); });

        if (!list.length) {
            ambulanceGrid.innerHTML = '<div class="directory-empty">No emergency providers with map data are listed yet. Call emergency services or go to the nearest known facility.</div>';
            return;
        }

        ambulanceGrid.innerHTML = list.slice(0, 8).map(function (item) {
            const p = item.provider;
            const phone = cleanPhone(p.phone);
            const status = p.ambulance_available === true ? 'Ambulance listed ready' : 'Call to confirm ambulance availability';
            return '<article class="ambulance-card">'
                + '<div class="ambulance-card-top"><div><div class="ambulance-name">' + esc(p.name) + '</div>'
                + '<div class="ambulance-meta">' + esc([placeOf(p), label(p.provider_type || p.type)].filter(Boolean).join(' • ')) + '</div></div>'
                + (item.distance != null ? '<span class="ambulance-distance">' + item.distance.toFixed(1) + ' km</span>' : '') + '</div>'
                + '<div class="ambulance-meta" style="margin-top:9px;color:' + (p.ambulance_available === true ? 'var(--green)' : 'var(--red)') + ';font-weight:700;">' + esc(status) + '</div>'
                + '<div class="ambulance-card-actions">'
                + (phone ? '<a class="ambulance-mini-btn primary" href="tel:' + esc(phone) + '"><i data-lucide="phone" width="14" height="14"></i> Call provider</a>' : '')
                + (p.id != null ? '<button type="button" class="ambulance-mini-btn open-profile" data-profile-type="provider" data-profile-id="' + esc(p.id) + '"><i data-lucide="hospital" width="14" height="14"></i> Profile</button>' : '')
                + (Number.isFinite(Number(p.latitude)) && Number.isFinite(Number(p.longitude)) ? '<a class="ambulance-mini-btn" target="_blank" rel="noopener" href="' + directionsUrl(Number(p.latitude), Number(p.longitude)) + '"><i data-lucide="navigation" width="14" height="14"></i> Directions</a>' : '')
                + '</div></article>';
        }).join('');
        renderIcons();
    }

    if (openAmbulanceModalBtn) openAmbulanceModalBtn.addEventListener('click', function () {
        openModal(ambulanceModal, openAmbulanceModalBtn);
        ambulanceLocationStatus.textContent = 'Use your location to rank nearby emergency providers.';
        renderAmbulanceProviders(null);
    });

    if (ambulanceUseLocation) ambulanceUseLocation.addEventListener('click', function () {
        if (!navigator.geolocation) {
            ambulanceLocationStatus.textContent = 'Location is not available in this browser.';
            return;
        }
        ambulanceLocationStatus.textContent = 'Finding nearby emergency providers…';
        navigator.geolocation.getCurrentPosition(function (pos) {
            const origin = { lat: pos.coords.latitude, lng: pos.coords.longitude };
            ambulanceLocationStatus.textContent = 'Nearby emergency providers are sorted by straight-line distance. Call to confirm ambulance readiness and ETA.';
            renderAmbulanceProviders(origin);
        }, function () {
            ambulanceLocationStatus.textContent = 'Location was not shared. Showing listed emergency providers without distance ranking.';
            renderAmbulanceProviders(null);
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 });
    });

    /* =====================================================
       PROFILE MODAL
    ===================================================== */

    let profileRequest = 0;

    function profileMessage(html) {
        profileContent.innerHTML = '<div style="padding:50px;text-align:center;">' + html + '</div>';
    }

    async function openProfile(type, id, trigger) {
        if (!profileModal || id === undefined || id === null || id === '') return;

        const requestId = ++profileRequest;

        openModal(profileModal, trigger);
        profileMessage('<span style="color:var(--muted)">Loading AfyaLink profile\u2026</span>');

        try {
            const base = type === 'doctor' ? ROUTES.doctorProfile : ROUTES.providerProfile;

            const response = await fetch(base + '/' + encodeURIComponent(id) + '/profile', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Profile could not be loaded.');

            const data = await response.json();

            if (requestId !== profileRequest) return;

            renderProfile(data.type || type, data.profile || data);
        } catch (error) {
            if (requestId !== profileRequest) return;
            console.error(error);
            profileMessage('<span style="color:var(--red)">This healthcare profile could not be loaded. Please try again.</span>');
        }
    }

    function renderProfile(type, p) {
        const isDoctor = type === 'doctor';
        const fallbackImg = '/images/profile-placeholder.svg';
        const image = imageUrl(p.image, fallbackImg);
        const name = p.name || (isDoctor ? 'Doctor' : 'Healthcare facility');
        const rating = Number(p.rating) || 0;
        const reviews = Number(p.review_count) || 0;
        const place = placeOf(p);
        const phone = cleanPhone(p.phone);
        const facility = isDoctor && p.facility && p.facility.name ? p.facility : null;
        const services = !isDoctor && Array.isArray(p.services)
            ? p.services.map(function (x) { return typeof x === 'string' ? x : (x && x.name) || ''; }).filter(Boolean)
            : [];
        const doctors = !isDoctor && Array.isArray(p.doctors) ? p.doctors : [];

        const mapRecord = !isDoctor ? providers.find(function (x) { return String(x.id) === String(p.id); }) : null;
        const lat = Number.isFinite(Number(p.latitude)) && p.latitude !== null && p.latitude !== '' ? Number(p.latitude) : (mapRecord ? Number(mapRecord.latitude) : NaN);
        const lng = Number.isFinite(Number(p.longitude)) && p.longitude !== null && p.longitude !== '' ? Number(p.longitude) : (mapRecord ? Number(mapRecord.longitude) : NaN);
        const hasCoords = Number.isFinite(lat) && Number.isFinite(lng);

        const subtitle = isDoctor
            ? (p.specialty || 'Healthcare professional')
            : [label(p.provider_type), label(p.ownership)].filter(Boolean).join(' • ');
        const description = isDoctor
            ? (p.bio || 'Healthcare professional listed on AfyaLink Uganda.')
            : (p.description || 'Healthcare provider listed on AfyaLink Uganda.');

        let pills = '';
        if (p.verified) pills += '<span class="social-pill green">Verified</span>';
        if (!isDoctor && p.open_24_hours) pills += '<span class="social-pill green">Open 24/7</span>';
        if (!isDoctor && p.emergency_available) pills += '<span class="social-pill red">Emergency care</span>';
        if (!isDoctor && p.ownership) pills += '<span class="social-pill">' + esc(label(p.ownership)) + '</span>';

        let actions = '<button type="button" class="social-action primary start-booking"'
            + ' data-book-type="' + (isDoctor ? 'doctor' : 'provider') + '"'
            + ' data-book-id="' + esc(p.id) + '"'
            + ' data-book-name="' + esc(name) + '"'
            + ' data-book-facility-id="' + esc(facility && facility.id != null ? facility.id : '') + '">'
            + '<i data-lucide="calendar-heart" width="17" height="17"></i> Book Visit</button>';

        actions += phone
            ? '<a href="tel:' + esc(phone) + '" class="social-action"><i data-lucide="phone" width="17" height="17"></i> ' + (isDoctor ? 'Contact' : 'Call') + '</a>'
            : '<button type="button" class="social-action" disabled><i data-lucide="phone-off" width="17" height="17"></i> No phone</button>';

        if (isDoctor && facility && facility.id != null) {
            actions += '<button type="button" class="social-action open-profile" data-profile-type="provider" data-profile-id="' + esc(facility.id) + '"><i data-lucide="hospital" width="17" height="17"></i> Facility</button>';
        } else if (!isDoctor && hasCoords) {
            actions += '<a target="_blank" rel="noopener" href="' + directionsUrl(lat, lng) + '" class="social-action green"><i data-lucide="navigation" width="17" height="17"></i> Directions</a>';
        } else if (!isDoctor && markerById.has(String(p.id))) {
            actions += '<button type="button" class="social-action green focus-provider" data-provider-id="' + esc(p.id) + '" data-provider-name="' + esc(name) + '"><i data-lucide="map-pin" width="17" height="17"></i> Map</button>';
        }

        let extra = '';
        if (isDoctor && facility) {
            extra += '<div class="social-section"><h3>Works at</h3>'
                + '<button type="button" class="social-doctor-row open-profile" data-profile-type="provider" data-profile-id="' + esc(facility.id || '') + '">'
                + '<span class="about-feature-icon" style="width:50px;height:50px"><i data-lucide="hospital" width="19" height="19"></i></span>'
                + '<span><strong>' + esc(facility.name) + '</strong><small>' + esc([facility.town, facility.district].filter(Boolean).join(', ')) + '</small></span>'
                + '<i data-lucide="chevron-right" width="17" height="17" style="margin-left:auto;color:#94A3B8"></i></button></div>';
        }

        if (!isDoctor && services.length) {
            extra += '<div class="social-section"><h3>Services available</h3><div class="social-tags">'
                + services.map(function (x) { return '<span class="social-tag">' + esc(x) + '</span>'; }).join('')
                + '</div></div>';
        }

        if (!isDoctor && doctors.length) {
            extra += '<div class="social-section"><h3>Doctors at this facility</h3><div class="social-doctors">'
                + doctors.map(function (d) {
                    return '<button type="button" class="social-doctor-row open-profile" data-profile-type="doctor" data-profile-id="' + esc(d.id) + '">'
                        + '<img src="' + esc(imageUrl(d.image, '/images/profile-placeholder.svg')) + '" alt="' + esc(d.name) + '" onerror="this.onerror=null;this.src=\'/images/profile-placeholder.svg\'">'
                        + '<span><strong>' + esc(d.name) + '</strong><small>' + esc(d.specialty || 'Doctor') + '</small></span>'
                        + '<span class="social-doctor-rating">' + esc(ratingText(d.rating)) + '</span>'
                        + '<i data-lucide="chevron-right" width="17" height="17" style="color:#94A3B8"></i></button>';
                }).join('') + '</div></div>';
        }

        let contact = '';
        if (p.address || p.phone || p.email) {
            contact = '<div class="social-section"><h3>Contact & location</h3><div class="social-stats">'
                + (p.address ? '<div class="social-stat"><small>Address</small><strong>' + esc(p.address) + '</strong></div>' : '')
                + (p.phone ? '<div class="social-stat"><small>Phone</small><strong>' + esc(p.phone) + '</strong></div>' : '')
                + (p.email ? '<div class="social-stat"><small>Email</small><strong>' + esc(p.email) + '</strong></div>' : '')
                + '</div></div>';
        }

        profileContent.innerHTML =
            '<div class="social-profile-cover"><img src="' + esc(image) + '" alt="" onerror="this.onerror=null;this.src=\'' + fallbackImg + '\'"></div>'
            + '<div class="social-profile-body">'
            + '<div class="social-profile-avatar-row"><div class="social-profile-avatar-wrap">'
            + '<img class="social-profile-avatar" src="' + esc(image) + '" alt="' + esc(name) + '" onerror="this.onerror=null;this.src=\'' + fallbackImg + '\'">'
            + (p.verified ? '<span class="social-profile-check"><i data-lucide="check" width="15" height="15"></i></span>' : '')
            + '</div></div>'
            + '<div class="social-profile-identity"><div class="social-profile-name-row"><h2 class="social-profile-name">' + esc(name) + '</h2></div>'
            + '<div class="social-profile-subtitle">' + esc(subtitle) + '</div>'
            + '<div class="social-profile-meta">'
            + (rating > 0 ? '<span class="rating"><i data-lucide="star" width="14" height="14" fill="currentColor"></i>' + rating.toFixed(1) + ' (' + reviews + ' reviews)</span>' : '<span>New profile</span>')
            + (place ? '<span><i data-lucide="map-pin" width="14" height="14"></i>' + esc(place) + '</span>' : '')
            + '</div><div class="social-profile-pills">' + pills + '</div></div>'
            + '<div class="social-profile-actions">' + actions + '</div>'
            + '<div class="social-stats">'
            + '<div class="social-stat"><small>' + (isDoctor ? 'Specialty' : 'Type') + '</small><strong>' + esc(isDoctor ? (p.specialty || 'Healthcare') : label(p.provider_type || 'Facility')) + '</strong></div>'
            + '<div class="social-stat"><small>Location</small><strong>' + esc(place || 'Not listed') + '</strong></div>'
            + '<div class="social-stat"><small>Rating</small><strong>' + (rating > 0 ? rating.toFixed(1) + ' / 5' : 'New') + '</strong></div>'
            + '</div>'
            + '<div class="social-section"><h3>About</h3><p>' + esc(description) + '</p></div>'
            + extra + contact
            + '</div>';

        renderIcons();
    }


    /* =====================================================
       GLOBAL CLICK HANDLING
       - [data-close-modal]  closes a modal
       - [data-profile-*]    opens a profile (cards, buttons, results, pins)
       - .focus-provider     flies to a facility on the map
       - .start-booking      begins the book-visit flow
    ===================================================== */

    document.addEventListener('click', function (event) {

        // close buttons
        const close = event.target.closest('[data-close-modal]');
        if (close) {
            closeModal(document.getElementById(close.dataset.closeModal));
            return;
        }

        // book a visit
        const bookBtn = event.target.closest('.start-booking');
        if (bookBtn) {
            startBooking(bookBtn);
            return;
        }

        // view on map
        const focusBtn = event.target.closest('.focus-provider');
        if (focusBtn) {
            event.preventDefault();
            closeResults();
            closeAllModals();
            if (focusBtn.closest('#chatWindow')) setChat(false);

            focusProvider(focusBtn.getAttribute('data-provider-id'), focusBtn.getAttribute('data-provider-name'));
            return;
        }

        // open a profile
        const trigger = event.target.closest('[data-profile-type][data-profile-id]');
        if (!trigger) return;

        // a link/button nested inside a clickable card keeps its own job
        const inner = event.target.closest('a, button, input, select, textarea, summary');
        if (inner && inner !== trigger && trigger.contains(inner) && !inner.hasAttribute('data-profile-type')) return;

        event.preventDefault();
        closeResults();
        if (trigger.closest('#chatWindow')) setChat(false);

        openProfile(trigger.dataset.profileType, trigger.dataset.profileId, trigger);
    });


    /* =====================================================
       BOOKING FLOW (profile -> auth check -> booking form)
    ===================================================== */

    let pendingBooking = null;

    async function startBooking(button) {
        pendingBooking = {
            type: button.dataset.bookType,
            id: button.dataset.bookId,
            name: button.dataset.bookName,
            facilityId: button.dataset.bookFacilityId || ''
        };

        closeModal(profileModal);

        if (!ROUTES.authStatus) {
            openModal(authModal);
            return;
        }

        try {
            const response = await fetch(ROUTES.authStatus, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });

            const status = await response.json();

            if (status && status.authenticated) {
                openBookingModal();
            } else {
                openModal(authModal);
            }
        } catch (error) {
            openModal(authModal);
        }
    }

    function openBookingModal() {
        if (!pendingBooking) return;

        document.getElementById('bookingTargetName').textContent = pendingBooking.name;
        document.getElementById('bookingTargetType').textContent =
            pendingBooking.type === 'doctor' ? 'Doctor appointment' : 'Facility visit';

        const providerInput = document.getElementById('bookingProviderId');
        const doctorInput = document.getElementById('bookingDoctorId');

        providerInput.value = '';
        doctorInput.value = '';

        if (pendingBooking.type === 'doctor') {
            doctorInput.value = pendingBooking.id;
            providerInput.value = pendingBooking.facilityId || '';
        } else {
            providerInput.value = pendingBooking.id;
        }

        document.getElementById('bookingDate').min = localDateString();
        setMessage(document.getElementById('bookingMessage'), '', '');

        openModal(bookingModal);
    }

    /* ---------- shared form helpers ---------- */

    function setMessage(el, type, text) {
        if (!el) return;
        el.className = 'afya-auth-message' + (type ? ' ' + type : '');
        el.textContent = text || '';
    }

    function errorText(data, fallback) {
        if (data && data.errors) return Object.values(data.errors).reduce(function (a, b) { return a.concat(b); }, []).join(' ');
        return (data && data.message) || fallback;
    }

    async function submitForm(form, url, msgEl, networkMessage) {
        if (!url) {
            setMessage(msgEl, 'error', 'This feature is not available yet. Please try again later.');
            return null;
        }

        const btn = form.querySelector('[type="submit"]');
        if (btn) btn.disabled = true;

        setMessage(msgEl, '', '');

        try {
            const res = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            });

            let data = {};
            try { data = await res.json(); } catch (e) { /* non-JSON response */ }

            if (res.status === 419) {
                setMessage(msgEl, 'error', 'Your session expired. Please refresh the page and try again.');
                return null;
            }

            return { res: res, data: data };
        } catch (err) {
            setMessage(msgEl, 'error', networkMessage || 'Could not reach AfyaLink. Check your connection and try again.');
            return null;
        } finally {
            if (btn) btn.disabled = false;
        }
    }

    function reportValidity(form, msgEl) {
        if (form.checkValidity()) return true;
        setMessage(msgEl, 'error', 'Please fill in all required fields correctly.');
        const bad = form.querySelector(':invalid');
        if (bad) {
            const details = bad.closest('details');
            if (details) details.open = true;
            bad.focus();
        }
        return false;
    }


    /* =====================================================
       AUTH TABS
    ===================================================== */

    const authTabs = document.querySelectorAll('[data-auth-tab]');

    authTabs.forEach(function (button) {
        button.addEventListener('click', function () {
            const tab = button.dataset.authTab;

            authTabs.forEach(function (item) {
                const on = item === button;
                item.classList.toggle('active', on);
                item.setAttribute('aria-selected', String(on));
            });

            document.getElementById('authLoginPanel').classList.toggle('active', tab === 'login');
            document.getElementById('authRegisterPanel').classList.toggle('active', tab === 'register');
        });
    });

    const dobInput = document.getElementById('registerDob');
    if (dobInput) dobInput.max = localDateString();


    /* =====================================================
       LOGIN
    ===================================================== */

    const modalLoginForm = document.getElementById('modalLoginForm');

    if (modalLoginForm) {
        modalLoginForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const message = document.getElementById('loginMessage');
            if (!reportValidity(modalLoginForm, message)) return;

            const result = await submitForm(modalLoginForm, ROUTES.login, message);
            if (!result) return;

            if (!result.res.ok) {
                setMessage(message, 'error', errorText(result.data, 'Login failed. Check your email and password.'));
                return;
            }

            setMessage(message, 'success', 'Signed in successfully.');

            setTimeout(function () {
                closeModal(authModal);
                openBookingModal();
            }, 350);
        });
    }


    /* =====================================================
       REGISTRATION
    ===================================================== */

    const modalRegisterForm = document.getElementById('modalRegisterForm');

    if (modalRegisterForm) {
        modalRegisterForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const message = document.getElementById('registerMessage');
            if (!reportValidity(modalRegisterForm, message)) return;

            const result = await submitForm(modalRegisterForm, ROUTES.register, message);
            if (!result) return;

            if (!result.res.ok) {
                setMessage(message, 'error', errorText(result.data, 'Registration failed.'));
                return;
            }

            setMessage(message, 'success', 'Account created successfully.');

            setTimeout(function () {
                closeModal(authModal);
                openBookingModal();
            }, 400);
        });
    }


    /* =====================================================
       SUBMIT APPOINTMENT
    ===================================================== */

    const appointmentForm = document.getElementById('appointmentForm');

    if (appointmentForm) {
        appointmentForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const message = document.getElementById('bookingMessage');
            if (!reportValidity(appointmentForm, message)) return;

            const result = await submitForm(appointmentForm, ROUTES.booking, message);
            if (!result) return;

            if (result.res.status === 401) {
                closeModal(bookingModal);
                openModal(authModal);
                return;
            }

            if (!result.res.ok) {
                setMessage(message, 'error', errorText(result.data, 'Booking could not be submitted.'));
                return;
            }

            setMessage(message, 'success', (result.data && result.data.message) || 'Your appointment request has been sent.');
            appointmentForm.reset();
            document.getElementById('bookingDate').min = localDateString();

            // reset() clears the hidden ids, so restore them in case the user books again
            if (pendingBooking) {
                document.getElementById('bookingDoctorId').value = pendingBooking.type === 'doctor' ? pendingBooking.id : '';
                document.getElementById('bookingProviderId').value = pendingBooking.type === 'doctor' ? (pendingBooking.facilityId || '') : pendingBooking.id;
            }

            setTimeout(function () { closeModal(bookingModal); }, 1800);
        });
    }


    /* =====================================================
       REPORT ISSUE / SCAM
    ===================================================== */

    const openReportModal = document.getElementById('openReportModal');
    const reportIssueForm = document.getElementById('reportIssueForm');
    const reportMessage = document.getElementById('reportMessage');

    if (openReportModal) {
        openReportModal.addEventListener('click', function () {
            setMessage(reportMessage, '', '');
            openModal(reportModal, openReportModal);
        });
    }

    if (reportIssueForm) {
        reportIssueForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (!reportIssueForm.checkValidity()) {
                reportIssueForm.reportValidity();
                return;
            }

            const btn = reportIssueForm.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                if (ROUTES.reportIssue) {
                    const result = await submitForm(reportIssueForm, ROUTES.reportIssue, reportMessage, 'We could not send the report right now.');
                    if (!result) return;
                    if (!result.res.ok) {
                        setMessage(reportMessage, 'error', errorText(result.data, 'The report could not be sent.'));
                        return;
                    }
                    setMessage(reportMessage, 'success', (result.data && result.data.message) || 'Thank you. Your report has been sent to AfyaLink.');
                    reportIssueForm.reset();
                    return;
                }

                const fd = new FormData(reportIssueForm);
                const summary = [
                    'AfyaLink issue report',
                    'Type: ' + (fd.get('issue_type') || ''),
                    'Subject: ' + (fd.get('subject') || ''),
                    'Details: ' + (fd.get('details') || ''),
                    'Contact: ' + (fd.get('contact') || '')
                ].join('\n');

                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(summary);
                    setMessage(reportMessage, 'success', 'Reporting is not connected to the server yet, so your report was copied. Paste it into AfyaLink support when the reporting endpoint is enabled.');
                } else {
                    setMessage(reportMessage, 'error', 'The reporting endpoint is not connected yet. Please copy your report details and send them to AfyaLink support.');
                }
            } catch (e) {
                setMessage(reportMessage, 'error', 'We could not prepare the report. Please try again.');
            } finally {
                if (btn) btn.disabled = false;
            }
        });
    }


    /* =====================================================
       CHAT ASSISTANT
    ===================================================== */

    const chatLauncher = document.getElementById('chatLauncher');
    const chatWindow   = document.getElementById('chatWindow');
    const chatClose    = document.getElementById('chatClose');
    const chatForm     = document.getElementById('chatForm');
    const chatInput    = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    function setChat(open) {
        if (!chatWindow || !chatLauncher) return;
        chatWindow.classList.toggle('open', open);
        chatLauncher.setAttribute('aria-expanded', String(open));
        if (open && chatInput) setTimeout(function () { chatInput.focus({ preventScroll: true }); }, 50);
    }

    function scrollChat() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function addMessage(text, isUser) {
        const bubble = document.createElement('div');
        bubble.className = 'chat-message' + (isUser ? ' user' : '');
        bubble.textContent = text;
        chatMessages.appendChild(bubble);
        scrollChat();
        return bubble;
    }

    function plural(n, word, pluralWord) {
        return n + ' ' + (n === 1 ? word : pluralWord);
    }

    function renderChatResults(bubble, data, query) {
        const pCount = data.providers.length;
        const dCount = data.doctors.length;

        if (!pCount && !dCount) {
            bubble.textContent = 'I could not find a match for \u201C' + query + '\u201D. '
                + 'Try a district (Kampala, Wakiso), a type (hospital, clinic, pharmacy) or a doctor\u2019s specialty.';
            return;
        }

        const found = [];
        if (pCount) found.push(plural(pCount, 'facility', 'facilities'));
        if (dCount) found.push(plural(dCount, 'doctor', 'doctors'));

        bubble.textContent = 'I found ' + found.join(' and ') + ':';

        const rows = [];

        data.providers.forEach(function (p) {
            const meta = [label(p.provider_type), placeOf(p)].filter(Boolean).join(' \u2022 ');
            const open = p.id != null;

            rows.push(
                (open
                    ? '<button type="button" class="chat-result" data-profile-type="provider" data-profile-id="' + esc(p.id) + '">'
                    : '<div class="chat-result">')
                + '<strong>' + esc(p.name) + '</strong><small>' + esc(meta) + '</small>'
                + (open ? '</button>' : '</div>')
            );
        });

        data.doctors.forEach(function (d) {
            const fac = d.healthcare_provider || null;
            const meta = [d.specialty, d.district, fac ? fac.name : ''].filter(Boolean).join(' \u2022 ');
            const open = d.id != null;

            rows.push(
                (open
                    ? '<button type="button" class="chat-result" data-profile-type="doctor" data-profile-id="' + esc(d.id) + '">'
                    : '<div class="chat-result">')
                + '<strong>' + esc(d.name) + '</strong><small>' + esc(meta) + '</small>'
                + (open ? '</button>' : '</div>')
            );
        });

        const list = document.createElement('div');
        list.className = 'chat-results';
        list.innerHTML = rows.slice(0, 5).join('');
        bubble.appendChild(list);

        if (rows.length > 5) {
            const more = document.createElement('div');
            more.style.marginTop = '8px';
            more.textContent = '\u2026and ' + (rows.length - 5) + ' more. Use the main search above to see them all.';
            bubble.appendChild(more);
        }
    }

    function quickbotTopic(message) {
        const q = String(message || '').toLowerCase();
        if (/\b(pregnan|antenatal|midwife|maternity|labour|labor|expectant)\b/.test(q)) return 'maternal';
        if (/\b(child|baby|pediatric|paediatric|infant)\b/.test(q)) return 'children';
        if (/\b(prostate|urine|urinary|urolog)\b/.test(q)) return 'prostate';
        if (/\b(tooth|teeth|dental|dentist|gum)\b/.test(q)) return 'dental';
        if (/\b(accident|trauma|bleed|bleeding|unconscious|ambulance|emergency)\b/.test(q)) return 'accident';
        if (/\b(fever|malaria|temperature|chills)\b/.test(q)) return 'fever';
        if (/\b(cough|breath|breathing|chest)\b/.test(q)) return 'breathing';
        if (/\b(pain|sick|ill|unwell|worried|worry)\b/.test(q)) return 'general';
        return null;
    }

    function quickbotGuidance(topic) {
        const lang = selectedLanguage;
        const comfort = (UI_LANGUAGES[lang] || UI_LANGUAGES.en).comfort;

        const english = {
            maternal: 'If you are pregnant and have heavy bleeding, severe abdominal pain, seizures, fainting, severe headache with vision changes, or reduced baby movement later in pregnancy, seek urgent medical care. For routine questions, a midwife or maternity clinic can help with antenatal care, birth planning and warning signs.',
            children: 'For a child, urgent assessment is important for trouble breathing, severe weakness, seizures, persistent vomiting, dehydration, very high fever, or if the child is unusually difficult to wake. For non-urgent concerns, a pediatric clinician or child-health clinic can assess them.',
            prostate: 'Prostate symptoms can include difficulty starting urine, a weak stream, frequent urination or waking often at night. These symptoms have several possible causes, so a clinician or urology specialist should assess them rather than guessing the diagnosis.',
            dental: 'Severe tooth pain, facial swelling, fever, trouble swallowing or trouble breathing can need urgent dental or medical care. Otherwise, a dentist can examine the tooth and recommend treatment.',
            accident: 'For serious accidents, heavy bleeding, loss of consciousness, breathing problems, major burns or suspected spine injury, seek emergency help immediately. Avoid moving someone with a possible neck or spine injury unless there is immediate danger.',
            fever: 'Fever can come from many illnesses. Drink fluids if you can, rest, and seek medical assessment if fever is severe, persistent, associated with confusion, breathing trouble, seizures, pregnancy, or a very young child.',
            breathing: 'Breathing difficulty or severe chest pain can be an emergency. Seek urgent medical help, especially if symptoms are sudden, severe, associated with fainting, blue lips, confusion or worsening quickly.',
            general: 'Symptoms can have many causes. I can help you find an appropriate clinic, doctor or hospital, but a qualified clinician is needed for diagnosis.'
        };

        const sw = {
            maternal: 'Ikiwa una ujauzito na una damu nyingi, maumivu makali ya tumbo, kuzimia, degedege, maumivu makali ya kichwa au mtoto kutosogea kama kawaida, tafuta huduma ya haraka. Kwa maswali ya kawaida, mkunga au kliniki ya wajawazito inaweza kusaidia.',
            children: 'Kwa mtoto mwenye shida ya kupumua, degedege, kutapika sana, upungufu wa maji, homa kali au usingizi usio wa kawaida, tafuta huduma ya haraka.',
            dental: 'Maumivu makali ya jino, uvimbe wa uso, homa au shida ya kumeza/kupumua yanaweza kuhitaji huduma ya haraka ya meno au hospitali.',
            accident: 'Kwa ajali kubwa, damu nyingi, kupoteza fahamu au shida ya kupumua, tafuta huduma ya dharura mara moja.',
            general: 'Dalili zinaweza kuwa na sababu nyingi. Naweza kukusaidia kupata kliniki, daktari au hospitali inayofaa, lakini uchunguzi unahitaji mtaalamu wa afya.'
        };

        const lg = {
            maternal: 'Bw’oba oli lubuto era ng’olina okuvaamu omusaayi mungi, obulumi obw’amaanyi, okugwa wansi, okusannyalala oba omwana obutambula bulungi, funa obujjanjabi obw’amangu. Ku bibuuzo ebya bulijjo, omuzaalisa oba kliniki y’abakyala abali embuto esobola okukuyamba.',
            children: 'Omwana bw’aba azibuwa okussa, asannyalala, asesema nnyo, akankana oba azibuwa okuzuukuka, funa obujjanjabi obw’amangu.',
            dental: 'Obulumi bw’erinnyo obw’amaanyi, okuzimba mu maaso, omusujja oba obuzibu okumira/okussa biyinza okwetaaga obujjanjabi obw’amangu.',
            accident: 'Ku kabenje ak’amaanyi, okuvaamu omusaayi mungi, okugwa mu koma oba obuzibu okussa, funa obuyambi obw’amangu.',
            general: 'Obubonero buyinza okuva ku bintu bingi. Nsobola okukuyamba okunoonya kliniki, omusawo oba eddwaliro, naye okuzuula obulwadde kwetaaga omusawo omukugu.'
        };

        let body = english[topic] || english.general;
        if (lang === 'sw') body = sw[topic] || sw.general;
        if (lang === 'lg') body = lg[topic] || lg.general;

        return comfort + '\n\n' + body;
    }

    function addGoogleLink(bubble, query) {
        const a = document.createElement('a');
        a.className = 'chat-google-link';
        a.target = '_blank';
        a.rel = 'noopener';
        a.href = 'https://www.google.com/search?q=' + encodeURIComponent(query);
        a.innerHTML = '<i data-lucide="external-link" width="13" height="13"></i> Search this on Google';
        bubble.appendChild(a);
        renderIcons();
    }

    async function askAssistant(message) {
        addMessage(message, true);
        if (typeof noteQuickbotActivity === 'function') noteQuickbotActivity();

        const topic = quickbotTopic(message);
        const bubble = addMessage(topic ? quickbotGuidance(topic) : 'Searching AfyaLink…', false);

        chatInput.disabled = true;

        try {
            const data = await smartSearch(message);

            if (data.providers.length || data.doctors.length) {
                if (topic) {
                    const resultsWrap = document.createElement('div');
                    resultsWrap.className = 'chat-message';
                    resultsWrap.style.marginTop = '2px';
                    resultsWrap.textContent = 'I also found AfyaLink care options that may be relevant:';
                    chatMessages.appendChild(resultsWrap);
                    renderChatResults(resultsWrap, data, message);
                } else {
                    renderChatResults(bubble, data, message);
                }
            } else if (!topic) {
                bubble.textContent = (UI_LANGUAGES[selectedLanguage] || UI_LANGUAGES.en).comfort
                    + ' I could not find a matching AfyaLink listing for that question.';
                addGoogleLink(bubble, message + ' health information Uganda');
            } else {
                addGoogleLink(bubble, message + ' health information Uganda');
            }
        } catch (err) {
            console.error(err);
            if (!topic) {
                bubble.textContent = 'I could not connect to AfyaLink right now. I can still open a web search for more information.';
                addGoogleLink(bubble, message + ' health information Uganda');
            }
        } finally {
            chatInput.disabled = false;
            chatInput.focus({ preventScroll: true });
            scrollChat();
        }
    }

    if (chatLauncher && chatWindow) {
        chatLauncher.addEventListener('click', function () {
            setChat(!chatWindow.classList.contains('open'));
        });
    }

    if (chatClose) chatClose.addEventListener('click', function () { setChat(false); });


    let quickbotAudioUnlocked = false;
    let quickbotContext = null;
    let quickbotNotificationCount = 1;
    let quickbotLastActivity = Date.now();
    let quickbotLastAlert = 0;
    const quickbotBadge = document.getElementById('quickbotBadge');
    const quickbotNudge = document.getElementById('quickbotNudge');
    const quickbotLanguage = document.getElementById('quickbotLanguage');

    function unlockQuickbotAudio() {
        if (quickbotAudioUnlocked) return;
        quickbotAudioUnlocked = true;
        try {
            quickbotContext = new (window.AudioContext || window.webkitAudioContext)();
            if (quickbotContext.state === 'suspended') quickbotContext.resume();
        } catch (e) { quickbotContext = null; }
    }

    function noteQuickbotActivity() {
        quickbotLastActivity = Date.now();
    }

    ['pointerdown', 'keydown', 'scroll', 'touchstart'].forEach(function (name) {
        document.addEventListener(name, function () {
            noteQuickbotActivity();
            if (name === 'pointerdown' || name === 'keydown') unlockQuickbotAudio();
        }, { passive: true });
    });

    function playQuickbotAmbulanceAlert() {
        if (!quickbotAudioUnlocked || !quickbotContext || reduceMotion) return;
        try {
            if (quickbotContext.state === 'suspended') quickbotContext.resume();
            const start = quickbotContext.currentTime;
            const gain = quickbotContext.createGain();
            gain.gain.setValueAtTime(0.0001, start);
            gain.gain.exponentialRampToValueAtTime(0.022, start + 0.04);
            gain.gain.setValueAtTime(0.022, start + 2.8);
            gain.gain.exponentialRampToValueAtTime(0.0001, start + 3.0);
            gain.connect(quickbotContext.destination);

            for (let i = 0; i < 6; i++) {
                const osc = quickbotContext.createOscillator();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(i % 2 === 0 ? 720 : 520, start + i * 0.5);
                osc.connect(gain);
                osc.start(start + i * 0.5);
                osc.stop(start + Math.min(3, i * 0.5 + 0.5));
            }
        } catch (e) { /* browser may block audio */ }
    }

    function quickbotPop(withSound) {
        if (!chatLauncher) return;
        chatLauncher.classList.remove('quickbot-ping', 'quickbot-yarn');
        void chatLauncher.offsetWidth;
        chatLauncher.classList.add('quickbot-yarn');

        if (quickbotNudge) {
            quickbotNudge.textContent = selectedLanguage === 'sw'
                ? 'Unahitaji msaada kupata huduma?'
                : selectedLanguage === 'lg'
                    ? 'Oyagala okuyambibwa okunoonya obujjanjabi?'
                    : 'Need help finding care?';
            quickbotNudge.classList.add('show');
            setTimeout(function () { quickbotNudge.classList.remove('show'); }, 4500);
        }

        quickbotNotificationCount += 1;
        if (quickbotBadge) {
            quickbotBadge.style.display = 'flex';
            quickbotBadge.textContent = Math.min(quickbotNotificationCount, 9);
        }

        if (withSound) playQuickbotAmbulanceAlert();
    }

    setTimeout(function () {
        if (chatWindow && !chatWindow.classList.contains('open')) quickbotPop(false);
    }, 1800);

    window.setInterval(function () {
        if (document.visibilityState !== 'visible' || !chatWindow || chatWindow.classList.contains('open')) return;
        const now = Date.now();
        if (now - quickbotLastActivity >= 10000 && now - quickbotLastAlert >= 10000) {
            quickbotLastAlert = now;
            quickbotPop(true);
        }
    }, 1000);

    if (quickbotLanguage) {
        quickbotLanguage.value = selectedLanguage;
        quickbotLanguage.addEventListener('change', function () {
            setSiteLanguage(quickbotLanguage.value, true);
        });
    }

    function nearbyProviderFromLocation(kind, origin) {
        const wanted = String(kind || '').toLowerCase();
        return providers
            .filter(function (p) {
                if (wanted === 'emergency') return !!(p.emergency_available || p.emergency);
                return String(p.provider_type || p.type || '').toLowerCase() === wanted;
            })
            .map(function (p) {
                const lat = Number(p.latitude), lng = Number(p.longitude);
                return { p: p, d: Number.isFinite(lat) && Number.isFinite(lng) ? haversineKm(origin.lat, origin.lng, lat, lng) : Infinity };
            })
            .filter(function (x) { return Number.isFinite(x.d); })
            .sort(function (a, b) { return a.d - b.d; })[0] || null;
    }

    function askNearby(kind) {
        const labelText = kind === 'emergency' ? 'emergency provider' : (kind === 'doctor' ? 'health worker' : kind);
        addMessage('Find a nearby ' + labelText, true);
        const bubble = addMessage('I’ll use your shared browser location to look for the nearest matching AfyaLink listing…', false);

        if (!navigator.geolocation) {
            bubble.textContent = 'Location is not available in this browser. Try searching by district instead.';
            return;
        }

        navigator.geolocation.getCurrentPosition(async function (pos) {
            const origin = { lat: pos.coords.latitude, lng: pos.coords.longitude };

            if (kind === 'doctor') {
                try {
                    const data = await smartSearch('doctor');
                    let best = null;

                    data.doctors.forEach(function (d) {
                        const fac = d.healthcare_provider || null;
                        if (!fac) return;

                        const mapped = providers.find(function (p) {
                            return (fac.id != null && String(p.id) === String(fac.id))
                                || (fac.name && String(p.name).toLowerCase() === String(fac.name).toLowerCase());
                        });
                        if (!mapped) return;

                        const lat = Number(mapped.latitude), lng = Number(mapped.longitude);
                        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                        const dist = haversineKm(origin.lat, origin.lng, lat, lng);
                        if (!best || dist < best.distance) best = { doctor: d, facility: mapped, distance: dist };
                    });

                    if (!best) {
                        bubble.textContent = 'I found doctors, but none are linked to mapped facilities yet. Try a district search such as “doctor in Kampala”.';
                        return;
                    }

                    bubble.innerHTML = 'The nearest mapped health worker I found is <strong>' + esc(best.doctor.name) + '</strong> (' + esc(best.doctor.specialty || 'Doctor') + '), about ' + best.distance.toFixed(1) + ' km away at ' + esc(best.facility.name) + '.'
                        + '<div class="chat-results"><button type="button" class="chat-result" data-profile-type="doctor" data-profile-id="' + esc(best.doctor.id) + '"><strong>' + esc(best.doctor.name) + '</strong><small>' + esc([best.doctor.specialty, best.facility.name, best.facility.district].filter(Boolean).join(' • ')) + '</small></button></div>';
                    renderIcons(); scrollChat();
                    return;
                } catch (e) {
                    bubble.textContent = 'I could not search health workers right now. Try again in a moment.';
                    return;
                }
            }

            const nearest = nearbyProviderFromLocation(kind, origin);
            if (!nearest) {
                bubble.textContent = 'I could not find a mapped ' + labelText + ' in the current AfyaLink data. Try a district search.';
                return;
            }

            const p = nearest.p;
            bubble.innerHTML = 'The nearest mapped ' + esc(labelText) + ' I found is <strong>' + esc(p.name) + '</strong>, about ' + nearest.d.toFixed(1) + ' km away in a straight line.'
                + '<div class="chat-results"><button type="button" class="chat-result" data-profile-type="provider" data-profile-id="' + esc(p.id) + '"><strong>' + esc(p.name) + '</strong><small>' + esc([placeOf(p), label(p.provider_type || p.type)].filter(Boolean).join(' • ')) + '</small></button></div>';
            renderIcons(); scrollChat();
        }, function () {
            bubble.textContent = 'You did not share location. Tell me your district instead, for example “hospital in Wakiso”.';
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
    }

    if (chatLauncher) chatLauncher.addEventListener('click', function () {
        if (quickbotBadge) { quickbotBadge.textContent = '0'; quickbotBadge.style.display = 'none'; }
    });

    if (chatForm && chatInput && chatMessages) {

        chatForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const message = chatInput.value.trim();
            if (!message) return;

            chatInput.value = '';
            askAssistant(message);
        });

        chatMessages.addEventListener('click', function (event) {
            const nearby = event.target.closest('[data-nearby]');
            if (nearby) {
                askNearby(nearby.getAttribute('data-nearby'));
                return;
            }

            const conditionQuick = event.target.closest('[data-condition-chat]');
            if (conditionQuick) {
                setChat(false);
                openConditionCare(conditionQuick.getAttribute('data-condition-chat'), conditionQuick);
                return;
            }

            const quick = event.target.closest('[data-chat]');
            if (quick) {
                const value = quick.getAttribute('data-chat');
                if (value === 'clinic') {
                    setChat(false);
                    openDirectory('clinic', quick);
                } else {
                    askAssistant(value);
                }
            }
        });
    }


    /* =====================================================
       ESCAPE KEY (modals first, then menu and chat)
    ===================================================== */

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        const openModals = document.querySelectorAll('.afya-modal.open');
        if (openModals.length) {
            closeModal(openModals[openModals.length - 1]);
            return;
        }

        if (mobileMenu && mobileMenu.classList.contains('open')) {
            setMenu(false);
            if (menuButton) menuButton.focus();
        }

        if (chatWindow && chatWindow.classList.contains('open')) {
            setChat(false);
            if (chatLauncher) chatLauncher.focus();
        }
    });

});
</script>

</body>
</html>