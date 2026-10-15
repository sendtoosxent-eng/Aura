<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#D32F2F">

    <title>Join AfyaLink Uganda | Provider Registration</title>

    <link rel="icon" href="/favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>

    <style>
        /* =====================================================
           ROOT
        ===================================================== */
        :root {
            --red: #D32F2F;
            --red-dark: #B71C1C;
            --red-soft: #FFF4F4;

            --green: #168A55;
            --green-dark: #0F6A41;
            --green-soft: #EFFAF4;

            --dark: #162033;
            --text: #4F5B6D;
            --muted: #6B778A;

            --line: #E3E9F0;
            --line-strong: #CBD4E0;
            --surface: #F5F8FB;
            --white: #FFFFFF;

            --shadow-sm: 0 8px 24px rgba(22, 32, 51, .05);
            --shadow-md: 0 20px 45px rgba(22, 32, 51, .09);

            --topbar-h: 72px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
            font-size: 15px;
            line-height: 1.6;
            background: var(--surface);
            color: var(--dark);
            overflow-x: hidden;
            -webkit-text-size-adjust: 100%;
        }

        img { display: block; max-width: 100%; }
        button, input, select, textarea { font: inherit; color: inherit; }
        button { cursor: pointer; border: none; background: none; }
        a { color: inherit; text-decoration: none; }
        ul, ol { list-style: none; }

        .hidden { display: none !important; }

        .sr-only {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
        }

        :focus-visible { outline: 3px solid rgba(211, 47, 47, .5); outline-offset: 2px; }

        .skip-link {
            position: fixed; top: -60px; left: 16px; z-index: 5000;
            padding: 10px 16px; border-radius: 10px; background: var(--dark); color: #fff;
            font-size: 13px; font-weight: 600; transition: top .2s;
        }
        .skip-link:focus { top: 12px; }

        /* =====================================================
           SHARED
        ===================================================== */
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-mark {
            width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
            color: #fff; background: linear-gradient(135deg, var(--red), var(--red-dark));
            box-shadow: 0 10px 24px rgba(211, 47, 47, .22);
        }
        .brand-name { font-size: 20px; font-weight: 800; line-height: 1; }
        .brand-country { margin-top: 4px; font-size: 10px; font-weight: 800; letter-spacing: .18em; color: var(--green); }

        .btn {
            min-height: 50px; padding: 12px 24px; border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center; gap: 9px;
            font-size: 14.5px; font-weight: 700; text-align: center;
            transition: transform .2s ease, opacity .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .btn:hover:not(:disabled) { transform: translateY(-2px); }
        .btn:disabled { opacity: .5; cursor: not-allowed; }
        .btn-primary { color: #fff; background: linear-gradient(135deg, var(--red), var(--red-dark)); box-shadow: 0 12px 26px rgba(211, 47, 47, .2); }
        .btn-green { color: #fff; background: linear-gradient(135deg, var(--green), var(--green-dark)); box-shadow: 0 12px 26px rgba(22, 138, 85, .2); }
        .btn-outline { color: var(--dark); background: #fff; border: 1.5px solid var(--line-strong); }
        .btn-outline:hover:not(:disabled) { border-color: var(--dark); }
        .btn-ghost { color: var(--red); background: transparent; }
        .btn-sm { min-height: 42px; padding: 9px 16px; font-size: 13.5px; border-radius: 12px; }

        .pill {
            display: inline-flex; align-items: center; gap: 7px; padding: 8px 14px; border-radius: 999px;
            font-size: 11.5px; font-weight: 800; letter-spacing: .06em;
        }
        .pill-green { color: var(--green-dark); background: var(--green-soft); }
        .pill-light { color: #fff; background: rgba(255, 255, 255, .14); border: 1px solid rgba(255, 255, 255, .22); backdrop-filter: blur(12px); }

        .link { color: var(--red); font-weight: 700; }
        .link:hover { text-decoration: underline; }

        /* =====================================================
           WELCOME
        ===================================================== */
        .welcome-screen { min-height: 100vh; display: grid; grid-template-columns: 1.02fr .98fr; background: #fff; }

        .welcome-visual { position: relative; overflow: hidden; min-height: 100vh; background: var(--dark); }
        .welcome-visual > img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .welcome-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(22, 32, 51, .15) 0%, rgba(11, 81, 50, .2) 42%, rgba(22, 32, 51, .92) 100%); }
        .welcome-glow-red { position: absolute; width: 430px; height: 430px; border-radius: 50%; left: -160px; top: -140px; background: radial-gradient(circle, rgba(211, 47, 47, .7), transparent 70%); }
        .welcome-glow-green { position: absolute; width: 500px; height: 500px; border-radius: 50%; right: -180px; bottom: -200px; background: radial-gradient(circle, rgba(22, 138, 85, .62), transparent 70%); }

        .visual-top { position: absolute; z-index: 2; top: 34px; left: 40px; color: #fff; }
        .visual-top .brand-country { color: #8DE2B5; }

        .visual-copy { position: absolute; z-index: 2; left: 40px; right: 40px; bottom: 40px; color: #fff; }
        .visual-title { margin-top: 18px; max-width: 560px; font-size: clamp(30px, 4vw, 52px); font-weight: 800; line-height: 1.1; letter-spacing: -.035em; }
        .visual-text { margin-top: 14px; max-width: 520px; font-size: 14.5px; line-height: 1.8; color: rgba(255, 255, 255, .8); }
        .visual-list { margin-top: 20px; display: grid; gap: 10px; }
        .visual-list li { display: flex; gap: 10px; align-items: flex-start; font-size: 13.5px; color: rgba(255, 255, 255, .92); }
        .visual-list svg { flex-shrink: 0; margin-top: 3px; color: #8DE2B5; }

        .welcome-content { min-height: 100vh; padding: 30px clamp(22px, 6vw, 80px); display: flex; flex-direction: column; }
        .welcome-topbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 13.5px; }
        .back-home { display: inline-flex; align-items: center; gap: 7px; color: var(--text); font-weight: 600; }
        .back-home:hover { color: var(--red); }
        .signin-note { color: var(--text); }

        .welcome-inner { width: 100%; max-width: 640px; margin: auto 0; padding: 40px 0; }
        .welcome-title { margin-top: 18px; font-size: clamp(32px, 4vw, 50px); line-height: 1.1; font-weight: 800; letter-spacing: -.035em; }
        .welcome-title span { color: var(--red); }
        .welcome-description { margin-top: 16px; color: var(--text); font-size: 15px; line-height: 1.8; max-width: 580px; }

        .need-card { margin-top: 26px; padding: 20px; border: 1px solid var(--line); border-radius: 18px; background: linear-gradient(180deg, #fff, #fbfcfe); }
        .need-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .need-head h2 { font-size: 15px; font-weight: 800; }
        .time-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; background: var(--green-soft); color: var(--green-dark); font-size: 12px; font-weight: 700; }
        .need-grid { margin-top: 14px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .need-item { display: flex; gap: 11px; align-items: flex-start; }
        .need-icon { width: 38px; height: 38px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: var(--red-soft); color: var(--red); }
        .need-item:nth-child(even) .need-icon { background: var(--green-soft); color: var(--green); }
        .need-title { font-size: 13.5px; font-weight: 700; }
        .need-text { font-size: 12.5px; color: var(--muted); line-height: 1.5; }

        .agreement-list { margin-top: 22px; display: grid; gap: 10px; }
        .agreement-item { padding: 15px 16px; border: 1.5px solid var(--line); border-radius: 14px; background: #fff; display: flex; gap: 12px; align-items: flex-start; cursor: pointer; transition: border-color .2s; }
        .agreement-item:hover { border-color: var(--line-strong); }
        .agreement-item:has(input:checked) { border-color: var(--green); background: var(--green-soft); }
        .agreement-item input { width: 20px; height: 20px; margin-top: 2px; accent-color: var(--green); flex-shrink: 0; }
        .agreement-item span { font-size: 13.5px; color: var(--text); line-height: 1.65; }
        .agreement-item a { color: var(--red); font-weight: 700; text-decoration: underline; }

        .welcome-actions { margin-top: 20px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .privacy-note { display: flex; align-items: center; gap: 6px; color: var(--muted); font-size: 12.5px; }

        /* =====================================================
           WIZARD TOPBAR + PROGRESS
        ===================================================== */
        .wizard-screen { min-height: 100vh; }

        .wizard-topbar {
            position: sticky; top: 0; z-index: 60; height: var(--topbar-h);
            padding: 0 clamp(16px, 4vw, 48px);
            display: grid; grid-template-columns: 1fr auto 1fr; align-items: center;
            background: rgba(255, 255, 255, .97); border-bottom: 1px solid var(--line); backdrop-filter: blur(14px);
        }
        .wizard-status { justify-self: center; display: flex; align-items: center; gap: 8px; color: var(--text); font-size: 12.5px; }
        .wizard-status-dot { width: 9px; height: 9px; border-radius: 50%; background: #F5A524; }
        .wizard-status strong { color: var(--dark); }
        .wizard-exit { justify-self: end; display: inline-flex; align-items: center; gap: 6px; color: var(--text); font-size: 13px; font-weight: 600; }
        .wizard-exit:hover { color: var(--red); }

        .mobile-progress { display: none; position: sticky; top: var(--topbar-h); z-index: 50; padding: 12px 16px 14px; background: #fff; border-bottom: 1px solid var(--line); }
        .mobile-progress-row { display: flex; justify-content: space-between; gap: 10px; font-size: 13px; font-weight: 700; }
        .mobile-progress-row span:last-child { color: var(--red); }
        .mobile-progress-track { margin-top: 9px; height: 6px; border-radius: 999px; background: var(--line); overflow: hidden; }
        .mobile-progress-bar { height: 100%; width: 0; border-radius: 999px; background: linear-gradient(90deg, var(--red), var(--green)); transition: width .35s ease; }

        /* =====================================================
           WIZARD SHELL  (rail | form | context)
        ===================================================== */
        .wizard-shell {
            width: 100%; max-width: 1480px; margin: 0 auto; padding: 32px clamp(16px, 3vw, 40px) 60px;
            display: grid; grid-template-columns: 270px minmax(0, 1fr) 340px; gap: 28px; align-items: start;
        }

        .rail, .context { position: sticky; top: calc(var(--topbar-h) + 24px); }

        .rail { padding: 22px 18px; border-radius: 20px; background: #fff; border: 1px solid var(--line); box-shadow: var(--shadow-sm); }
        .rail-title { padding: 0 8px 14px; font-size: 11.5px; font-weight: 800; letter-spacing: .1em; color: var(--muted); }
        .rail-list { display: grid; }
        .rail-item { position: relative; }
        .rail-item:not(:last-child)::before { content: ""; position: absolute; left: 25px; top: 46px; bottom: -2px; width: 2px; background: var(--line); }
        .rail-item.done:not(:last-child)::before { background: var(--green); }
        .rail-btn { width: 100%; padding: 8px; border-radius: 14px; display: flex; align-items: center; gap: 12px; text-align: left; transition: background .2s; }
        .rail-btn:not(:disabled):hover { background: var(--surface); }
        .rail-btn:disabled { cursor: default; }
        .rail-dot { width: 34px; height: 34px; flex-shrink: 0; border-radius: 50%; border: 2px solid var(--line-strong); background: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: var(--muted); position: relative; z-index: 1; }
        .rail-item.active .rail-dot { border-color: var(--red); background: linear-gradient(135deg, var(--red), var(--red-dark)); color: #fff; box-shadow: 0 8px 16px rgba(211, 47, 47, .22); }
        .rail-item.done .rail-dot { border-color: var(--green); background: var(--green); color: #fff; }
        .rail-label { display: block; font-size: 13.5px; font-weight: 700; color: var(--dark); line-height: 1.3; }
        .rail-sub { display: block; font-size: 11.5px; color: var(--muted); }
        .rail-item.active .rail-label { color: var(--red); }
        .rail-item.done .rail-sub { color: var(--green-dark); }

        /* ----- form column ----- */
        .step-header { margin-bottom: 20px; }
        .step-eyebrow { color: var(--red); font-size: 12px; font-weight: 800; letter-spacing: .1em; }
        .step-title { margin-top: 6px; font-size: clamp(26px, 3vw, 36px); line-height: 1.15; font-weight: 800; letter-spacing: -.03em; outline: none; }
        .step-description { margin-top: 10px; max-width: 760px; font-size: 15px; line-height: 1.75; color: var(--text); }

        .why-box { margin-top: 14px; padding: 13px 15px; max-width: 760px; border-radius: 14px; border: 1px solid #D7ECE0; background: var(--green-soft); display: flex; gap: 11px; align-items: flex-start; font-size: 13.5px; line-height: 1.65; color: #345C47; }
        .why-box svg { flex-shrink: 0; margin-top: 3px; color: var(--green); }

        .message { display: none; margin-bottom: 16px; padding: 13px 16px; border-radius: 14px; font-size: 14px; line-height: 1.65; }
        .message.error { display: block; background: #FEF2F2; color: #991B1B; border: 1px solid #F7D4D4; }
        .message.success { display: block; background: #F0FDF4; color: #166534; border: 1px solid #CDECD7; }
        .message.info { display: block; background: #EFF6FF; color: #1E40AF; border: 1px solid #D5E3FB; }

        .form-card { padding: 30px; border-radius: 22px; background: #fff; border: 1px solid var(--line); box-shadow: var(--shadow-sm); }

        .step-panel { display: none; }
        .step-panel.active { display: block; animation: stepFade .35s cubic-bezier(.2, .8, .2, 1); }
        @keyframes stepFade { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        /* ----- formal sections + 12-column grid ----- */
        .form-section + .form-section { margin-top: 32px; padding-top: 28px; border-top: 1px solid var(--line); }
        .section-head { margin-bottom: 18px; display: flex; align-items: center; gap: 12px; }
        .section-index { width: 30px; height: 30px; flex-shrink: 0; border-radius: 9px; background: var(--dark); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; }
        .section-name { font-size: 16px; font-weight: 800; }
        .section-hint { font-size: 12.5px; color: var(--muted); font-weight: 500; }

        .grid { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 20px 18px; }
        .col-12 { grid-column: span 12; }
        .col-8 { grid-column: span 8; }
        .col-6 { grid-column: span 6; }
        .col-4 { grid-column: span 4; }

        .field { display: grid; gap: 7px; align-content: start; min-width: 0; scroll-margin-top: calc(var(--topbar-h) + 110px); }
        .label { font-size: 13.5px; font-weight: 700; color: #2A364B; }
        .req { color: var(--red); }
        .opt { color: var(--muted); font-weight: 500; font-size: 12.5px; }

        .control, .select, .textarea {
            width: 100%; border: 1.5px solid var(--line-strong); background: #fff; color: var(--dark);
            border-radius: 12px; outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .control, .select { height: 50px; padding: 0 15px; font-size: 15px; }
        .textarea { min-height: 110px; padding: 13px 15px; font-size: 15px; resize: vertical; }
        .control::placeholder, .textarea::placeholder { color: #98A3B4; }
        .control:focus, .select:focus, .textarea:focus { border-color: var(--red); box-shadow: 0 0 0 4px rgba(211, 47, 47, .08); }
        .field.has-error .control, .field.has-error .select, .field.has-error .textarea { border-color: var(--red); background: #FFFAFA; }

        .hint { font-size: 12.5px; line-height: 1.55; color: var(--muted); }
        .field-error { display: none; font-size: 12.5px; font-weight: 600; color: var(--red); }
        .field.has-error .field-error { display: block; }
        .validation-text { min-height: 18px; font-size: 12.5px; font-weight: 600; }
        .validation-text.success { color: var(--green); }
        .validation-text.error { color: var(--red); }

        .input-wrap { position: relative; }
        .input-wrap .control { padding-right: 76px; }
        .toggle-pass { position: absolute; right: 8px; top: 8px; height: 34px; padding: 0 12px; border-radius: 9px; background: var(--surface); color: var(--text); font-size: 12.5px; font-weight: 700; }
        .toggle-pass:hover { color: var(--red); }

        .rules { margin-top: 2px; display: grid; gap: 4px; }
        .rule { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: var(--muted); }
        .rule::before { content: ""; width: 8px; height: 8px; border-radius: 50%; background: var(--line-strong); flex-shrink: 0; }
        .rule.ok { color: var(--green-dark); }
        .rule.ok::before { background: var(--green); }

        /* ----- registration type cards ----- */
        .type-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .type-card { position: relative; display: flex; flex-direction: column; overflow: hidden; border: 2px solid var(--line); border-radius: 20px; background: #fff; cursor: pointer; transition: transform .2s, box-shadow .2s, border-color .2s; }
        .type-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-sm); }
        .type-card:focus-within { outline: 3px solid rgba(211, 47, 47, .45); outline-offset: 2px; }
        .type-card.selected { border-color: var(--red); background: #FFFAFA; }
        .type-card.professional.selected { border-color: var(--green); background: #F8FDFA; }
        .type-photo { position: relative; display: block; height: 150px; background: linear-gradient(135deg, var(--red-soft), var(--green-soft)); overflow: hidden; }
        .type-photo img { width: 100%; height: 100%; object-fit: cover; }
        .type-photo::after { content: ""; position: absolute; inset: 0; background: linear-gradient(to top, rgba(22, 32, 51, .35), transparent 60%); }
        .type-check { position: absolute; top: 12px; right: 12px; z-index: 2; width: 30px; height: 30px; border-radius: 50%; background: #fff; border: 2px solid var(--line-strong); display: flex; align-items: center; justify-content: center; color: transparent; }
        .type-card.selected .type-check { background: var(--red); border-color: var(--red); color: #fff; }
        .type-card.professional.selected .type-check { background: var(--green); border-color: var(--green); }
        .type-body { padding: 20px; display: flex; flex-direction: column; flex: 1; }
        .type-title { display: flex; align-items: center; gap: 11px; font-size: 18px; font-weight: 800; }
        .type-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: var(--red-soft); color: var(--red); flex-shrink: 0; }
        .type-card.professional .type-icon { background: var(--green-soft); color: var(--green); }
        .type-text { margin-top: 10px; font-size: 13.5px; line-height: 1.7; color: var(--text); }
        .type-foot { margin-top: auto; padding-top: 14px; display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 600; color: var(--muted); }

        /* ----- facility search ----- */
        .search-wrap { position: relative; }
        .search-wrap .search-icon { position: absolute; left: 15px; top: 16px; color: var(--muted); pointer-events: none; }
        .search-wrap .control { padding-left: 44px; }
        .facility-results { display: none; position: absolute; left: 0; right: 0; top: calc(100% + 6px); z-index: 20; max-height: 320px; overflow-y: auto; overscroll-behavior: contain; border-radius: 16px; border: 1px solid var(--line); background: #fff; box-shadow: var(--shadow-md); }
        .facility-results.open { display: block; }
        .facility-result { width: 100%; padding: 12px 14px; display: flex; align-items: center; gap: 12px; text-align: left; border-bottom: 1px solid #EEF2F6; background: #fff; }
        .facility-result:last-child { border-bottom: none; }
        .facility-result:hover, .facility-result:focus-visible { background: #FFF8F8; outline-offset: -3px; }
        .facility-thumb { width: 48px; height: 48px; flex-shrink: 0; border-radius: 12px; object-fit: cover; background: var(--surface); }
        .facility-name { display: block; font-size: 14px; font-weight: 700; }
        .facility-meta { display: block; margin-top: 2px; color: var(--muted); font-size: 12.5px; text-transform: capitalize; }
        .results-note { padding: 16px; font-size: 13.5px; color: var(--muted); }
        .results-note.error { color: var(--red); }

        .selected-facility { display: none; padding: 14px; border-radius: 16px; background: var(--green-soft); border: 1.5px solid #CFEADB; align-items: center; gap: 13px; }
        .selected-facility.show { display: flex; }
        .selected-facility .facility-thumb { width: 56px; height: 56px; }
        .selected-facility-text { flex: 1; min-width: 0; }
        .verified-badge { display: inline-flex; align-items: center; gap: 5px; margin-top: 4px; font-size: 12px; font-weight: 700; color: var(--green-dark); }

        .help-box { margin-top: 18px; padding: 16px; border-radius: 16px; border: 1px solid #F4D4D4; background: var(--red-soft); display: flex; gap: 12px; align-items: flex-start; font-size: 13.5px; line-height: 1.7; color: #6F4545; }
        .help-box svg { flex-shrink: 0; margin-top: 3px; color: var(--red); }
        .help-box button { margin-top: 8px; }

        /* ----- profile photo ----- */
        .photo-block { padding: 20px; border-radius: 18px; border: 1.5px dashed var(--line-strong); background: linear-gradient(135deg, #FFF8F8, #F5FCF8); display: flex; flex-wrap: wrap; gap: 20px; align-items: center; }
        .photo-circle { position: relative; width: 112px; height: 112px; border-radius: 50%; border: 4px solid #fff; overflow: hidden; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--red-soft), var(--green-soft)); color: var(--red); box-shadow: var(--shadow-md); flex-shrink: 0; }
        .photo-circle img { display: none; width: 100%; height: 100%; object-fit: cover; }
        .photo-info { flex: 1; min-width: 220px; }
        .photo-tips { margin: 6px 0 12px; display: grid; gap: 3px; font-size: 12.5px; color: var(--text); }
        .photo-tips li::before { content: "\2713"; margin-right: 7px; color: var(--green); font-weight: 800; }
        .photo-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .photo-actions label { cursor: pointer; }
        .photo-block:focus-within .photo-actions label { outline: 3px solid rgba(211, 47, 47, .5); outline-offset: 2px; }
        .photo-file { min-height: 18px; margin-top: 8px; font-size: 12.5px; font-weight: 700; color: var(--green-dark); }
        .photo-block .field-error { flex-basis: 100%; }
        .camera-panel { display: none; flex-basis: 100%; padding: 14px; border: 1px solid var(--line); border-radius: 16px; background: #111827; }
        .camera-panel.show { display: block; }
        .camera-viewport { position: relative; overflow: hidden; width: 100%; max-width: 520px; margin: 0 auto; aspect-ratio: 4 / 3; border-radius: 13px; background: #030712; }
        .camera-viewport video { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
        .camera-guide { position: absolute; inset: 12% 25%; border: 2px solid rgba(255,255,255,.8); border-radius: 48%; pointer-events: none; box-shadow: 0 0 0 999px rgba(0,0,0,.15); }
        .camera-controls { display: flex; justify-content: center; gap: 10px; margin-top: 12px; }
        .camera-note { margin-top: 9px; color: #D1D5DB; font-size: 12px; text-align: center; }

        .banner { margin-top: 24px; padding: 15px 16px; border-radius: 15px; border: 1px solid #D7ECE0; background: var(--green-soft); display: flex; gap: 12px; align-items: flex-start; font-size: 13.5px; line-height: 1.7; color: #345C47; }
        .banner-icon { width: 34px; height: 34px; border-radius: 11px; background: #fff; color: var(--green); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

        /* ----- verify email ----- */
        .verify-hero { text-align: center; padding: 6px 0 4px; }
        .verify-icon { width: 76px; height: 76px; margin: 0 auto; border-radius: 50%; background: var(--green-soft); color: var(--green); display: flex; align-items: center; justify-content: center; }
        .verify-hero h3 { margin-top: 16px; font-size: 20px; font-weight: 800; }
        .verify-hero p { margin: 8px auto 0; max-width: 520px; font-size: 14.5px; line-height: 1.75; color: var(--text); }
        .verify-hero strong { color: var(--dark); }
        .mini-grid { margin-top: 24px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .mini-card { padding: 18px; border-radius: 16px; border: 1px solid var(--line); background: #FBFCFE; }
        .mini-no { width: 30px; height: 30px; border-radius: 50%; background: var(--dark); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; }
        .mini-card h4 { margin-top: 12px; font-size: 14px; font-weight: 800; }
        .mini-card p { margin-top: 4px; font-size: 13px; line-height: 1.6; color: var(--muted); }
        .verify-tools { margin-top: 22px; display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 14px; font-size: 13px; color: var(--muted); }
        .auto-check { display: inline-flex; align-items: center; gap: 8px; }
        .auto-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--green); animation: pulse 1.8s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(22, 138, 85, .5); } 70% { box-shadow: 0 0 0 9px rgba(22, 138, 85, 0); } 100% { box-shadow: 0 0 0 0 rgba(22, 138, 85, 0); } }

        /* ----- chips + toggles ----- */
        .chip-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 10px; }
        .chip { position: relative; display: flex; align-items: center; gap: 10px; padding: 11px 14px; border: 1.5px solid var(--line-strong); border-radius: 12px; background: #fff; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: border-color .2s, background .2s; }
        .chip input { width: 18px; height: 18px; accent-color: var(--green); flex-shrink: 0; }
        .chip:has(input:checked) { border-color: var(--green); background: var(--green-soft); }
        .toggle-row { display: flex; gap: 14px; flex-wrap: wrap; }
        .toggle-row .chip { flex: 1 1 240px; }

        .locate-box { padding: 16px; border-radius: 16px; border: 1.5px dashed var(--line-strong); background: var(--surface); display: flex; flex-wrap: wrap; gap: 14px; align-items: center; justify-content: space-between; }
        .locate-text { flex: 1; min-width: 220px; }
        .locate-text strong { display: block; font-size: 14px; }
        .locate-status { margin-top: 3px; font-size: 12.5px; color: var(--muted); }
        .locate-status.ok { color: var(--green-dark); font-weight: 700; }

        /* ----- documents ----- */
        .doc-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .doc-card { padding: 18px; border-radius: 18px; border: 1.5px solid var(--line); background: #fff; display: flex; flex-direction: column; gap: 12px; transition: border-color .2s, background .2s; }
        .doc-card.has-file { border-color: #BFE3CE; background: #F8FDFA; }
        .doc-card.has-error { border-color: var(--red); background: #FFFAFA; }
        .doc-top { display: flex; gap: 12px; align-items: flex-start; }
        .doc-icon { width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0; background: var(--red-soft); color: var(--red); display: flex; align-items: center; justify-content: center; }
        .doc-card.has-file .doc-icon { background: var(--green-soft); color: var(--green); }
        .doc-title { font-size: 14.5px; font-weight: 800; line-height: 1.35; }
        .doc-desc { margin-top: 3px; font-size: 12.5px; line-height: 1.55; color: var(--muted); }
        .badge { display: inline-block; margin-left: 6px; padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 800; letter-spacing: .04em; vertical-align: middle; }
        .badge-req { background: var(--red-soft); color: var(--red); }
        .badge-opt { background: var(--surface); color: var(--muted); }
        .doc-drop { padding: 16px; border: 1.5px dashed var(--line-strong); border-radius: 14px; background: var(--surface); text-align: center; cursor: pointer; font-size: 13.5px; font-weight: 600; color: var(--text); transition: border-color .2s, background .2s; display: block; }
        .doc-drop:hover, .doc-card:focus-within .doc-drop { border-color: var(--red); background: #fff; color: var(--red); }
        .doc-drop small { display: block; margin-top: 2px; font-weight: 500; font-size: 12px; color: var(--muted); }
        .doc-file { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 12px; background: #fff; border: 1px solid #CFEADB; }
        .doc-file img { width: 44px; height: 44px; border-radius: 9px; object-fit: cover; flex-shrink: 0; }
        .doc-file-info { flex: 1; min-width: 0; }
        .doc-file-name { font-size: 13px; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .doc-file-size { font-size: 12px; color: var(--muted); }
        .doc-remove { width: 34px; height: 34px; border-radius: 10px; color: var(--muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .doc-remove:hover { background: var(--red-soft); color: var(--red); }
        .doc-error { display: none; font-size: 12.5px; font-weight: 600; color: var(--red); }
        .doc-card.has-error .doc-error { display: block; }

        /* ----- review ----- */
        .review-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .review-card { padding: 20px; border-radius: 18px; border: 1px solid var(--line); background: #fff; }
        .review-card.wide { grid-column: 1 / -1; }
        .review-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding-bottom: 12px; border-bottom: 1px solid var(--line); }
        .review-head h3 { font-size: 15px; font-weight: 800; }
        .review-edit { display: inline-flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 700; color: var(--red); }
        .review-locked { font-size: 11.5px; font-weight: 700; color: var(--green-dark); display: inline-flex; align-items: center; gap: 5px; }
        .review-list { margin-top: 14px; display: grid; gap: 12px; }
        .review-row dt { font-size: 11px; font-weight: 800; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; }
        .review-row dd { margin-top: 2px; font-size: 14px; line-height: 1.55; color: var(--dark); word-break: break-word; }
        .review-docs { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 18px; }

        .declaration { margin-top: 22px; padding: 16px; border: 1.5px solid var(--line-strong); border-radius: 14px; display: flex; gap: 12px; align-items: flex-start; cursor: pointer; font-size: 13.5px; line-height: 1.7; color: var(--text); }
        .declaration:has(input:checked) { border-color: var(--green); background: var(--green-soft); }
        .declaration input { width: 20px; height: 20px; margin-top: 3px; accent-color: var(--green); flex-shrink: 0; }

        .upload-progress { display: none; margin-top: 18px; padding: 16px; border-radius: 14px; background: var(--surface); }
        .upload-progress.show { display: block; }
        .upload-track { margin-top: 8px; height: 8px; border-radius: 999px; background: var(--line); overflow: hidden; }
        .upload-bar { height: 100%; width: 0; background: linear-gradient(90deg, var(--red), var(--green)); transition: width .2s; }
        .upload-text { font-size: 13px; font-weight: 600; }

        /* ----- action bar ----- */
        .action-bar { margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; background: #fff; }
        .action-bar .grow { flex: 1; }
        .action-note { font-size: 12.5px; color: var(--muted); }

        /* ----- context column ----- */
        .context { border-radius: 22px; overflow: hidden; background: #fff; border: 1px solid var(--line); box-shadow: var(--shadow-sm); }
        .context-photo { position: relative; height: 190px; background: linear-gradient(135deg, var(--red), var(--green)); }
        .context-photo img { width: 100%; height: 100%; object-fit: cover; }
        .context-photo::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(22, 32, 51, .1), rgba(22, 32, 51, .78)); }
        .context-badge { position: absolute; top: 14px; left: 14px; z-index: 2; }
        .context-caption { position: absolute; left: 16px; right: 16px; bottom: 14px; z-index: 2; color: #fff; }
        .context-caption h3 { font-size: 18px; line-height: 1.25; font-weight: 800; }
        .context-caption p { margin-top: 4px; font-size: 12.5px; line-height: 1.55; color: rgba(255, 255, 255, .85); }
        .context-body { padding: 18px; display: grid; gap: 16px; }

        .snapshot { padding: 16px; border-radius: 16px; border: 1px solid var(--line); background: linear-gradient(180deg, #fff, #FBFCFE); }
        .snapshot-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 11px; font-weight: 800; letter-spacing: .08em; color: var(--muted); }
        .snapshot-pill { padding: 4px 9px; border-radius: 999px; background: #FFF6D7; color: #8A5F00; font-size: 10.5px; font-weight: 800; }
        .snapshot-user { margin-top: 14px; display: flex; align-items: center; gap: 12px; }
        .snapshot-avatar { width: 56px; height: 56px; border-radius: 50%; overflow: hidden; border: 3px solid #fff; background: linear-gradient(135deg, var(--red-soft), var(--green-soft)); color: var(--red); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm); flex-shrink: 0; }
        .snapshot-avatar img { display: none; width: 100%; height: 100%; object-fit: cover; }
        .snapshot-name { font-size: 15px; font-weight: 800; line-height: 1.3; word-break: break-word; }
        .snapshot-role { margin-top: 2px; color: var(--green-dark); font-size: 12px; font-weight: 700; }
        .snapshot-list { margin-top: 14px; display: grid; gap: 11px; }
        .snapshot-item { display: flex; gap: 9px; align-items: flex-start; color: var(--muted); }
        .snapshot-item svg { flex-shrink: 0; margin-top: 2px; }
        .snapshot-item strong { display: block; font-size: 10.5px; letter-spacing: .08em; color: var(--muted); }
        .snapshot-item span { display: block; margin-top: 1px; font-size: 13px; line-height: 1.45; color: var(--dark); word-break: break-word; }

        .support-box { padding: 14px 16px; border-radius: 14px; background: var(--surface); font-size: 13px; line-height: 1.65; color: var(--text); }
        .support-box strong { color: var(--dark); }

        /* ----- success ----- */
        .success-screen { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 30px 18px; background: linear-gradient(135deg, #FFF8F8, #fff 50%, #F2FBF6); }
        .success-card { width: 100%; max-width: 760px; padding: clamp(26px, 5vw, 48px); border-radius: 28px; background: #fff; border: 1px solid var(--line); box-shadow: var(--shadow-md); text-align: center; }
        .success-icon { width: 92px; height: 92px; margin: 0 auto; border-radius: 50%; background: var(--green-soft); color: var(--green); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 12px rgba(22, 138, 85, .08); }
        .success-card h1 { margin-top: 24px; font-size: clamp(26px, 4vw, 36px); line-height: 1.15; font-weight: 800; letter-spacing: -.03em; }
        .success-card > p { margin: 12px auto 0; max-width: 520px; font-size: 15px; line-height: 1.75; color: var(--text); }
        .reference { display: inline-block; margin-top: 18px; padding: 10px 18px; border-radius: 12px; background: var(--surface); font-size: 13.5px; }
        .reference strong { font-size: 15px; letter-spacing: .04em; }
        .timeline { margin-top: 28px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; text-align: left; }
        .timeline-item { padding: 16px; border: 1px solid var(--line); border-radius: 16px; background: #FBFCFE; }
        .timeline-item .mini-no { background: var(--green); }
        .timeline-item h3 { margin-top: 10px; font-size: 14px; font-weight: 800; }
        .timeline-item p { margin-top: 3px; font-size: 12.5px; line-height: 1.6; color: var(--muted); }
        .success-actions { margin-top: 28px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        /* =====================================================
           RESPONSIVE
        ===================================================== */
        @media (max-width: 1320px) {
            .wizard-shell { grid-template-columns: 250px minmax(0, 1fr); }
            .context { display: none; }
        }

        @media (max-width: 1000px) {
            .welcome-screen { grid-template-columns: 1fr; }
            .welcome-visual { min-height: 440px; }
            .welcome-content { min-height: 0; }
        }

        @media (max-width: 960px) {
            .wizard-shell { grid-template-columns: 1fr; padding: 20px 16px 40px; }
            .rail { display: none; }
            .mobile-progress { display: block; }
            .wizard-status { display: none; }
            .wizard-topbar { grid-template-columns: 1fr auto; }
            .form-card { padding: 20px; }
            .type-grid, .doc-grid, .review-grid, .need-grid, .mini-grid, .timeline { grid-template-columns: 1fr; }
            .review-docs { grid-template-columns: 1fr; }
            .col-8, .col-6, .col-4 { grid-column: span 12; }
            .grid { gap: 18px 14px; }
            .control, .select, .textarea { font-size: 16px; }
            .action-bar { position: sticky; bottom: 0; z-index: 40; margin: 26px -20px -20px; padding: 14px 20px; border-radius: 0 0 22px 22px; box-shadow: 0 -10px 24px rgba(22, 32, 51, .08); }
            .action-bar .btn { flex: 1; }
            .action-note { display: none; }
        }

        @media (max-width: 560px) {
            .col-4-sm { grid-column: span 12; }
            .visual-top { left: 22px; top: 24px; }
            .visual-copy { left: 22px; right: 22px; bottom: 26px; }
            .photo-block { justify-content: center; text-align: center; }
            .photo-actions { justify-content: center; }
            .photo-tips li { text-align: left; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>

@php
    $hasRoute    = fn ($name) => \Illuminate\Support\Facades\Route::has($name);
    $termsUrl    = $hasRoute('terms')   ? route('terms')   : null;
    $privacyUrl  = $hasRoute('privacy') ? route('privacy') : null;

    // Fill these in to show a help box in the sidebar. Empty values are hidden.
    $supportEmail = null;   // e.g. 'providers@afyalink.ug'
    $supportPhone = null;   // e.g. '+256 700 000000'
@endphp

<a class="skip-link" href="#formArea">Skip to the form</a>


{{-- =========================================================
     WELCOME
========================================================== --}}
<section class="welcome-screen" id="welcomeScreen">

    <div class="welcome-visual">
        <img src="/images/hero-doctor.jpg" alt="Healthcare worker" fetchpriority="high" decoding="async">
        <div class="welcome-overlay"></div>
        <div class="welcome-glow-red"></div>
        <div class="welcome-glow-green"></div>

        <div class="visual-top">
            <a href="/" class="brand" aria-label="AfyaLink Uganda home">
                <div class="brand-mark"><i data-lucide="heart-pulse" width="21" height="21"></i></div>
                <div>
                    <div class="brand-name">AfyaLink</div>
                    <div class="brand-country">UGANDA</div>
                </div>
            </a>
        </div>

        <div class="visual-copy">
            

            <h2 class="visual-title">Join a trusted network of healthcare providers.</h2>

            <p class="visual-text">
                Register your healthcare facility or professional profile, complete the required details,
                and become visible to patients through AfyaLink.
            </p>

            <ul class="visual-list">
                <li><i data-lucide="circle-check" width="16" height="16"></i> Registration is free and simple.</li>
                <li><i data-lucide="circle-check" width="16" height="16"></i> Profiles are reviewed before public listing.</li>
                <li><i data-lucide="circle-check" width="16" height="16"></i> Your information is collected securely.</li>
            </ul>
        </div>
    </div>

    <div class="welcome-content">

        <div class="welcome-topbar">
            <a href="/" class="back-home">
                <i data-lucide="arrow-left" width="15" height="15"></i>
                Back to AfyaLink
            </a>
            <div class="signin-note">Already registered? <a href={{ route('provider.login') }} class="link">Sign in</a></div>
        </div>

        <div class="welcome-inner">

           

            <h1 class="welcome-title">Build your <span>AfyaLink</span> profile.</h1>

            <p class="welcome-description">
                Register as a healthcare facility or a qualified healthcare professional. We will guide you
                through each step, one small section at a time.
            </p>

            <div class="need-card">
                <div class="need-head">
                    <h2>Before you start, have these ready</h2>
                    <span class="time-chip"><i data-lucide="clock" width="13" height="13"></i> About 10 minutes</span>
                </div>

                <div class="need-grid">
                    <div class="need-item">
                        <div class="need-icon"><i data-lucide="fingerprint" width="18" height="18"></i></div>
                        <div>
                            <div class="need-title">National ID number</div>
                            <div class="need-text">Your 14-character NIN.</div>
                        </div>
                    </div>

                    <div class="need-item">
                        <div class="need-icon"><i data-lucide="badge-check" width="18" height="18"></i></div>
                        <div>
                            <div class="need-title">Licence details</div>
                            <div class="need-text">Registration and licence numbers.</div>
                        </div>
                    </div>

                    <div class="need-item">
                        <div class="need-icon"><i data-lucide="file-text" width="18" height="18"></i></div>
                        <div>
                            <div class="need-title">Document scans</div>
                            <div class="need-text">PDF or photo, up to 5 MB each.</div>
                        </div>
                    </div>

                    <div class="need-item">
                        <div class="need-icon"><i data-lucide="camera" width="18" height="18"></i></div>
                        <div>
                            <div class="need-title">A clear photo</div>
                            <div class="need-text">Head-and-shoulders, plain background.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="agreement-list">
                <label class="agreement-item">
                    <input type="checkbox" id="termsAgreement">
                    <span>
                        I have read and agree to the
                        @if ($termsUrl)
                            <a href="{{ $termsUrl }}" target="_blank" rel="noopener">AfyaLink Terms &amp; Conditions</a>
                        @else
                            <strong>AfyaLink Terms &amp; Conditions</strong>
                        @endif
                        @if ($privacyUrl)
                            and <a href="{{ $privacyUrl }}" target="_blank" rel="noopener">Privacy Policy</a>
                        @endif
                        .
                    </span>
                </label>

                <label class="agreement-item">
                    <input type="checkbox" id="disclaimerAgreement">
                    <span>
                        I confirm that the information I provide will be accurate and truthful, and I understand that
                        AfyaLink verification does not replace approval by the relevant regulatory authority.
                    </span>
                </label>
            </div>

            <div class="welcome-actions">
                <button type="button" class="btn btn-primary" id="beginRegistration" disabled>
                    Accept &amp; Continue
                    <i data-lucide="arrow-right" width="16" height="16"></i>
                </button>

                <div class="privacy-note">
                    <i data-lucide="lock" width="13" height="13"></i>
                    Secure onboarding
                </div>
            </div>

        </div>
    </div>

</section>


{{-- =========================================================
     WIZARD
========================================================== --}}
<section class="wizard-screen hidden" id="wizardScreen">

    <header class="wizard-topbar">
        <a href="/" class="brand" aria-label="AfyaLink Uganda home">
            <div class="brand-mark"><i data-lucide="heart-pulse" width="20" height="20"></i></div>
            <div>
                <div class="brand-name">AfyaLink</div>
                <div class="brand-country">UGANDA</div>
            </div>
        </a>

        <div class="wizard-status">
            <span class="wizard-status-dot"></span>
            Application: <strong>Draft, not yet submitted</strong>
        </div>

        <a href="/" class="wizard-exit" id="exitLink">
            Exit
            <i data-lucide="x" width="15" height="15"></i>
        </a>
    </header>

    <div class="mobile-progress" aria-hidden="true">
        <div class="mobile-progress-row">
            <span id="mobileStepLabel">Step 1 of 7</span>
            <span id="mobileStepName">Registration type</span>
        </div>
        <div class="mobile-progress-track"><div class="mobile-progress-bar" id="mobileProgressBar"></div></div>
    </div>

    <div class="wizard-shell">

        {{-- ---------- LEFT: step rail ---------- --}}
        <nav class="rail" aria-label="Registration steps">
            <div class="rail-title">YOUR PROGRESS</div>
            <ol class="rail-list" id="stepRail"></ol>
        </nav>

        {{-- ---------- CENTER: form ---------- --}}
        <main id="formArea">

            <div class="step-header">
                <div class="step-eyebrow" id="stepEyebrow">Step 1 of 7</div>
                <h1 class="step-title" id="stepTitle" tabindex="-1">How are you joining AfyaLink?</h1>
                <p class="step-description" id="stepDescription"></p>

                <div class="why-box" id="whyBox">
                    <i data-lucide="info" width="17" height="17"></i>
                    <div><strong>Why we ask:</strong> <span id="whyText"></span></div>
                </div>
            </div>

            <div class="message" id="stepMessage" role="alert" aria-live="polite"></div>

            <div class="form-card">

                {{-- ===== STEP: registration type ===== --}}
                <section class="step-panel active" data-step-id="type">
                    <div class="type-grid" role="radiogroup" aria-label="Registration type">

                        <label class="type-card" data-type="facility">
                            <input type="radio" name="account_type" value="facility" class="sr-only">
                            <span class="type-photo">
                                <img src="/images/hospital.jpg" alt="" loading="lazy" onerror="this.style.display='none'">
                                <span class="type-check"><i data-lucide="check" width="16" height="16"></i></span>
                            </span>
                            <span class="type-body">
                                <span class="type-title">
                                    <span class="type-icon"><i data-lucide="hospital" width="20" height="20"></i></span>
                                    Healthcare Facility
                                </span>
                                <span class="type-text">
                                    Register a hospital, clinic, pharmacy, laboratory, maternity unit, dental clinic
                                    or another healthcare facility.
                                </span>
                                <span class="type-foot"><i data-lucide="badge-check" width="15" height="15"></i> Facility licence verification required</span>
                            </span>
                        </label>

                        <label class="type-card professional" data-type="professional">
                            <input type="radio" name="account_type" value="professional" class="sr-only">
                            <span class="type-photo">
                                <img src="/images/hero-doctor.jpg" alt="" loading="lazy" onerror="this.style.display='none'">
                                <span class="type-check"><i data-lucide="check" width="16" height="16"></i></span>
                            </span>
                            <span class="type-body">
                                <span class="type-title">
                                    <span class="type-icon"><i data-lucide="stethoscope" width="20" height="20"></i></span>
                                    Health Professional
                                </span>
                                <span class="type-text">
                                    Doctors, nurses, midwives, dentists, pharmacists, clinical officers,
                                    laboratory staff and other professionals.
                                </span>
                                <span class="type-foot"><i data-lucide="building-2" width="15" height="15"></i> Linked to a verified workplace</span>
                            </span>
                        </label>

                    </div>
                </section>


                {{-- ===== STEP: workplace (professionals) ===== --}}
                <section class="step-panel" data-step-id="workplace">

                    <div class="form-section">
                        <div class="section-head">
                            <span class="section-index">A</span>
                            <div>
                                <div class="section-name">Verified workplace</div>
                                <div class="section-hint">Only facilities already verified by AfyaLink appear here.</div>
                            </div>
                        </div>

                        <div class="field" id="workplaceField">
                            <div id="facilitySearchBlock">
                                <label class="label" for="facilitySearch">Search your workplace <span class="req">*</span></label>

                                <div class="search-wrap" style="margin-top:7px">
                                    <i data-lucide="search" class="search-icon" width="18" height="18"></i>
                                    <input type="text" class="control" id="facilitySearch"
                                           placeholder="Type the facility name, district or town"
                                           autocomplete="off" role="combobox" aria-expanded="false"
                                           aria-controls="facilityResults" aria-autocomplete="list">

                                    <div class="facility-results" id="facilityResults" aria-live="polite"></div>
                                </div>

                                <div class="hint" style="margin-top:7px">Type at least 2 characters. Select your facility from the list.</div>
                            </div>

                            <input type="hidden" id="selectedFacilityId">

                            <div class="selected-facility" id="selectedFacility">
                                <img class="facility-thumb" id="selectedFacilityImage" src="" alt="">
                                <div class="selected-facility-text">
                                    <span class="facility-name" id="selectedFacilityName"></span>
                                    <span class="facility-meta" id="selectedFacilityMeta"></span>
                                    <span class="verified-badge"><i data-lucide="badge-check" width="14" height="14"></i> Verified by AfyaLink</span>
                                </div>
                                <button type="button" class="btn btn-outline btn-sm" id="changeFacility">Change</button>
                            </div>

                            <div class="field-error" id="workplaceError"></div>
                        </div>

                        <div class="help-box">
                            <i data-lucide="info" width="18" height="18"></i>
                            <div>
                                <strong>Can't find your workplace?</strong><br>
                                The facility must be registered and approved by AfyaLink first. If you own or run the facility,
                                you can register it now instead.
                                <div>
                                    <button type="button" class="btn btn-outline btn-sm" id="switchToFacility">Register a facility instead</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>


                {{-- ===== STEPS: personal + contact (one form, one POST) ===== --}}
                <form id="identityForm" novalidate enctype="multipart/form-data" autocomplete="on">

                    {{-- ----- personal details ----- --}}
                    <section class="step-panel" data-step-id="personal">

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">A</span>
                                <div>
                                    <div class="section-name" id="photoSectionName">Profile photo</div>
                                    <div class="section-hint">Shown on your profile once approved.</div>
                                </div>
                            </div>

                            <div class="photo-block field" id="photoField">
                                <div class="photo-circle">
                                    <span id="profilePhotoPlaceholder"><i data-lucide="user-round" width="44" height="44"></i></span>
                                    <img src="" alt="Profile photo preview" id="profilePhotoPreview">
                                </div>

                                <div class="photo-info">
                                    <div class="label">Upload a photo <span class="req">*</span></div>

                                    <ul class="photo-tips">
                                        <li>Clear, recent head-and-shoulders photo</li>
                                        <li>Face fully visible, plain background</li>
                                        <li>JPG, PNG or WebP, up to 5 MB</li>
                                    </ul>

                                    <div class="photo-actions">
                                        <label class="btn btn-outline btn-sm" for="profilePhotoInput">
                                            <i data-lucide="upload" width="16" height="16"></i>
                                            <span id="photoBtnText">Upload photo</span>
                                        </label>
                                        <button type="button" class="btn btn-outline btn-sm" id="openCamera">
                                            <i data-lucide="camera" width="16" height="16"></i>
                                            Take live photo
                                        </button>
                                        <button type="button" class="btn btn-ghost btn-sm hidden" id="photoRemove">Remove</button>
                                    </div>

                                    <input type="file" class="sr-only" id="profilePhotoInput" name="profile_photo"
                                           accept="image/jpeg,image/png,image/webp" required data-validate="photo">

                                    <div class="photo-file" id="profilePhotoName"></div>
                                </div>

                                <div class="camera-panel" id="cameraPanel" aria-live="polite">
                                    <div class="camera-viewport">
                                        <video id="cameraVideo" autoplay playsinline muted></video>
                                        <div class="camera-guide" aria-hidden="true"></div>
                                    </div>
                                    <canvas id="cameraCanvas" class="hidden"></canvas>
                                    <div class="camera-controls">
                                        <button type="button" class="btn btn-green btn-sm" id="capturePhoto">
                                            <i data-lucide="camera" width="16" height="16"></i>
                                            Capture photo
                                        </button>
                                        <button type="button" class="btn btn-outline btn-sm" id="closeCamera">Cancel</button>
                                    </div>
                                    <div class="camera-note" id="cameraStatus">Allow camera access, then centre your face inside the guide.</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">B</span>
                                <div>
                                    <div class="section-name">Personal information</div>
                                    <div class="section-hint">Exactly as it appears on your National ID.</div>
                                </div>
                            </div>

                            <div class="grid">

                                <div class="field col-4">
                                    <label class="label" for="firstName">First name <span class="req">*</span></label>
                                    <input type="text" class="control" id="firstName" name="first_name" required autocomplete="given-name" placeholder="e.g. Osxent">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-4">
                                    <label class="label" for="middleName">Middle name <span class="opt">(optional)</span></label>
                                    <input type="text" class="control" id="middleName" name="middle_name" autocomplete="additional-name">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-4">
                                    <label class="label" for="lastName">Last name <span class="req">*</span></label>
                                    <input type="text" class="control" id="lastName" name="last_name" required autocomplete="family-name" placeholder="e.g. Nsamba">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-4">
                                    <label class="label" for="dob">Date of birth <span class="req">*</span></label>
                                    <input type="date" class="control" id="dob" name="date_of_birth" required data-validate="dob" autocomplete="bday">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-4">
                                    <label class="label" for="sex">Sex <span class="req">*</span></label>
                                    <select class="select" id="sex" name="sex" required>
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-4">
                                    <label class="label" for="nationality">Nationality <span class="req">*</span></label>
                                    <input type="text" class="control" id="nationality" name="nationality" value="Ugandan" required autocomplete="country-name">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-12">
                                    <label class="label" for="ninInput">National Identification Number (NIN) <span class="req">*</span></label>
                                    <input type="text" class="control" id="ninInput" name="nin" required maxlength="14"
                                           autocomplete="off" autocapitalize="characters" spellcheck="false"
                                           placeholder="14 characters, starts with CM or CF" data-validate="nin">
                                    <div class="validation-text" id="ninValidation"></div>
                                    <div class="hint">Used only to confirm your identity. It is never shown publicly.</div>
                                    <div class="field-error"></div>
                                </div>

                            </div>
                        </div>

                    </section>


                    {{-- ----- contact + password ----- --}}
                    <section class="step-panel" data-step-id="contact">

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">A</span>
                                <div>
                                    <div class="section-name">Contact details</div>
                                    <div class="section-hint">We use these to reach you about your application.</div>
                                </div>
                            </div>

                            <div class="grid">

                                <div class="field col-6">
                                    <label class="label" for="phone">Phone number <span class="req">*</span></label>
                                    <input type="tel" class="control" id="phone" name="phone" required inputmode="tel"
                                           autocomplete="tel" placeholder="+256 7XX XXX XXX" data-validate="phone">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="email">Email address <span class="req">*</span></label>
                                    <input type="email" class="control" id="email" name="email" required inputmode="email"
                                           autocomplete="email" placeholder="name@example.com" data-validate="email">
                                    <div class="hint">We will send a verification link to this address.</div>
                                    <div class="field-error"></div>
                                </div>

                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">B</span>
                                <div>
                                    <div class="section-name" id="passwordSectionName">Create a password</div>
                                    <div class="section-hint">You will use your email and this password to sign in.</div>
                                </div>
                            </div>

                            <div class="grid">

                                <div class="field col-6">
                                    <label class="label" for="password">Password <span class="req">*</span></label>
                                    <div class="input-wrap">
                                        <input type="password" class="control" id="password" name="password" required
                                               autocomplete="new-password" data-validate="password">
                                        <button type="button" class="toggle-pass" data-toggle-password="password" aria-label="Show password">Show</button>
                                    </div>
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="passwordConfirm">Confirm password <span class="req">*</span></label>
                                    <div class="input-wrap">
                                        <input type="password" class="control" id="passwordConfirm" name="password_confirmation" required
                                               autocomplete="new-password" data-validate="passwordConfirm">
                                        <button type="button" class="toggle-pass" data-toggle-password="passwordConfirm" aria-label="Show password">Show</button>
                                    </div>
                                    <div class="field-error"></div>
                                </div>

                                <div class="col-12">
                                    <ul class="rules" aria-live="polite">
                                        <li class="rule" id="ruleLength">At least 8 characters</li>
                                        <li class="rule" id="ruleMix">Uppercase, lowercase, a number and a symbol</li>
                                        <li class="rule" id="ruleMatch">Both passwords match</li>
                                    </ul>
                                </div>

                            </div>
                        </div>

                        <div class="banner">
                            <div class="banner-icon"><i data-lucide="shield-check" width="17" height="17"></i></div>
                            <div>
                                <strong>Your information is protected.</strong><br>
                                AfyaLink uses your identity and contact details only for registration, account security and verification.
                            </div>
                        </div>

                    </section>

                </form>


                {{-- ===== STEP: verify email ===== --}}
                <section class="step-panel" data-step-id="verify">

                    <div class="verify-hero">
                        <div class="verify-icon"><i data-lucide="mail-check" width="34" height="34"></i></div>
                        <h3>Check your inbox</h3>
                        <p>We sent a verification link to <strong id="verifyEmailText">your email address</strong>. It can take a minute to arrive. Check your spam folder too.</p>
                    </div>

                    <div class="mini-grid">
                        <div class="mini-card">
                            <div class="mini-no">1</div>
                            <h4>Open the email</h4>
                            <p>Look for a message from AfyaLink.</p>
                        </div>
                        <div class="mini-card">
                            <div class="mini-no">2</div>
                            <h4>Click "Verify"</h4>
                            <p>Confirm your address using the secure link.</p>
                        </div>
                        <div class="mini-card">
                            <div class="mini-no">3</div>
                            <h4>Come back here</h4>
                            <p>We will detect it and continue automatically.</p>
                        </div>
                    </div>

                    <div class="verify-tools">
                        <span class="auto-check"><span class="auto-dot"></span> Checking automatically</span>
                        <button type="button" class="btn btn-outline btn-sm" id="resendEmailButton">Resend email</button>
                    </div>

                </section>


                {{-- ===== STEP: regulatory / facility details ===== --}}
                <section class="step-panel" data-step-id="regulatory">

                    {{-- ----- professional ----- --}}
                    <div class="account-specific hidden" data-for="professional">

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">A</span>
                                <div><div class="section-name">Professional profile</div></div>
                            </div>

                            <div class="grid">
                                <div class="field col-6">
                                    <label class="label" for="proCadre">Profession / cadre <span class="req">*</span></label>
                                    <select class="select" id="proCadre" data-reg="cadre" required>
                                        <option value="">Select profession</option>
                                        <option>Doctor</option>
                                        <option>Nurse</option>
                                        <option>Midwife</option>
                                        <option>Dentist</option>
                                        <option>Pharmacist</option>
                                        <option>Clinical Officer</option>
                                        <option>Laboratory Professional</option>
                                        <option>Physiotherapist</option>
                                        <option>Other</option>
                                    </select>
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="proSpecialty">Specialty <span class="opt">(optional)</span></label>
                                    <input type="text" class="control" id="proSpecialty" data-reg="specialty" placeholder="e.g. Paediatrics">
                                    <div class="field-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">B</span>
                                <div>
                                    <div class="section-name">Registration and licence</div>
                                    <div class="section-hint">As issued by your regulatory council.</div>
                                </div>
                            </div>

                            <div class="grid">
                                <div class="field col-12">
                                    <label class="label" for="proBody">Regulatory body <span class="req">*</span></label>
                                    <input type="text" class="control" id="proBody" data-reg="regulatory_body" required list="councilList"
                                           placeholder="Start typing or pick a council">
                                    <datalist id="councilList">
                                        <option value="Uganda Medical and Dental Practitioners Council">
                                        <option value="Uganda Nurses and Midwives Council">
                                        <option value="Allied Health Professionals Council">
                                    </datalist>
                                    <div class="hint">We suggest a council based on your profession. You can change it.</div>
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="proRegNo">Registration number <span class="req">*</span></label>
                                    <input type="text" class="control" id="proRegNo" data-reg="registration_number" required autocomplete="off">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="proLicense">Practising licence number <span class="req">*</span></label>
                                    <input type="text" class="control" id="proLicense" data-reg="licence_number" required autocomplete="off">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="proLicenseExpiry">Licence expiry date <span class="req">*</span></label>
                                    <input type="date" class="control" id="proLicenseExpiry" data-reg="licence_expiry" required data-validate="futureDate">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="proExperience">Years of experience <span class="opt">(optional)</span></label>
                                    <input type="number" class="control" id="proExperience" data-reg="years_experience" min="0" max="60" inputmode="numeric" placeholder="e.g. 5">
                                    <div class="field-error"></div>
                                </div>
                            </div>
                        </div>

                    </div>


                    {{-- ----- facility ----- --}}
                    <div class="account-specific hidden" data-for="facility">

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">A</span>
                                <div><div class="section-name">Facility identity</div></div>
                            </div>

                            <div class="grid">
                                <div class="field col-12">
                                    <label class="label" for="facName">Facility name <span class="req">*</span></label>
                                    <input type="text" class="control" id="facName" data-reg="name" required placeholder="As it appears on your licence" autocomplete="organization">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="facType">Facility type <span class="req">*</span></label>
                                    <select class="select" id="facType" data-reg="provider_type" required>
                                        <option value="">Select type</option>
                                        <option>Hospital</option>
                                        <option>Clinic</option>
                                        <option>Health Centre</option>
                                        <option>Pharmacy</option>
                                        <option>Dental Clinic</option>
                                        <option>Laboratory</option>
                                        <option>Maternity Centre</option>
                                        <option>Other</option>
                                    </select>
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="facOwnership">Ownership <span class="req">*</span></label>
                                    <select class="select" id="facOwnership" data-reg="ownership" required>
                                        <option value="">Select ownership</option>
                                        <option>Private</option>
                                        <option>Government</option>
                                        <option>NGO / Mission</option>
                                        <option>Community</option>
                                    </select>
                                    <div class="field-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">B</span>
                                <div>
                                    <div class="section-name">Location</div>
                                    <div class="section-hint">Helps patients find you on the AfyaLink map.</div>
                                </div>
                            </div>

                            <div class="grid">
                                <div class="field col-6">
                                    <label class="label" for="facDistrict">District <span class="req">*</span></label>
                                    <input type="text" class="control" id="facDistrict" data-reg="district" required list="districtList" placeholder="e.g. Kampala">
                                    <datalist id="districtList">
                                        <option value="Kampala"><option value="Wakiso"><option value="Mukono"><option value="Mpigi">
                                        <option value="Jinja"><option value="Mbarara"><option value="Masaka"><option value="Gulu">
                                        <option value="Lira"><option value="Mbale"><option value="Arua"><option value="Soroti">
                                        <option value="Kabale"><option value="Kabarole">
                                    </datalist>
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="facTown">Town / area <span class="req">*</span></label>
                                    <input type="text" class="control" id="facTown" data-reg="town" required placeholder="e.g. Ntinda">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-12">
                                    <label class="label" for="facAddress">Physical address <span class="req">*</span></label>
                                    <input type="text" class="control" id="facAddress" data-reg="address" required autocomplete="street-address" placeholder="Plot, street and landmark">
                                    <div class="field-error"></div>
                                </div>

                                <div class="col-12">
                                    <div class="locate-box">
                                        <div class="locate-text">
                                            <strong>Pin your location on the map <span class="opt">(optional)</span></strong>
                                            <div class="locate-status" id="locationStatus">Best done while you are at the facility.</div>
                                        </div>
                                        <button type="button" class="btn btn-outline btn-sm" id="locateFacility">
                                            <i data-lucide="locate-fixed" width="16" height="16"></i>
                                            Use my current location
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">C</span>
                                <div><div class="section-name">Licensing and management</div></div>
                            </div>

                            <div class="grid">
                                <div class="field col-6">
                                    <label class="label" for="facLicense">Operating licence number <span class="req">*</span></label>
                                    <input type="text" class="control" id="facLicense" data-reg="licence_number" required autocomplete="off">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="facLicenseExpiry">Licence expiry date <span class="req">*</span></label>
                                    <input type="date" class="control" id="facLicenseExpiry" data-reg="licence_expiry" required data-validate="futureDate">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-12">
                                    <label class="label" for="facInCharge">Facility in-charge <span class="opt">(optional)</span></label>
                                    <input type="text" class="control" id="facInCharge" data-reg="in_charge" placeholder="Full name of the in-charge">
                                    <div class="field-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-head">
                                <span class="section-index">D</span>
                                <div>
                                    <div class="section-name">Public contact and services</div>
                                    <div class="section-hint">Shown to patients on your profile.</div>
                                </div>
                            </div>

                            <div class="grid">
                                <div class="field col-6">
                                    <label class="label" for="facPhone">Facility phone <span class="req">*</span></label>
                                    <input type="tel" class="control" id="facPhone" data-reg="phone" required inputmode="tel" placeholder="+256 7XX XXX XXX" data-validate="phone">
                                    <div class="field-error"></div>
                                </div>

                                <div class="field col-6">
                                    <label class="label" for="facEmail">Facility email <span class="opt">(optional)</span></label>
                                    <input type="email" class="control" id="facEmail" data-reg="email" inputmode="email" placeholder="info@yourfacility.ug" data-validate="optionalEmail">
                                    <div class="field-error"></div>
                                </div>

                                <div class="col-12">
                                    <div class="toggle-row">
                                        <label class="chip"><input type="checkbox" id="facOpen24" data-reg="open_24_hours"> Open 24 hours, every day</label>
                                        <label class="chip"><input type="checkbox" id="facEmergency" data-reg="emergency_available"> Emergency services available</label>
                                    </div>
                                </div>

                                <div class="field col-12">
                                    <span class="label">Key services offered</span>
                                    <div class="chip-grid" id="serviceChips">
                                        <label class="chip"><input type="checkbox" name="svc" value="Outpatient care"> Outpatient care</label>
                                        <label class="chip"><input type="checkbox" name="svc" value="Inpatient care"> Inpatient care</label>
                                        <label class="chip"><input type="checkbox" name="svc" value="Maternity"> Maternity</label>
                                        <label class="chip"><input type="checkbox" name="svc" value="Child health and immunisation"> Child health &amp; immunisation</label>
                                        <label class="chip"><input type="checkbox" name="svc" value="Family planning"> Family planning</label>
                                        <label class="chip"><input type="checkbox" name="svc" value="Laboratory tests"> Laboratory tests</label>
                                        <label class="chip"><input type="checkbox" name="svc" value="Imaging"> Imaging (X-ray, ultrasound)</label>
                                        <label class="chip"><input type="checkbox" name="svc" value="Pharmacy"> Pharmacy</label>
                                        <label class="chip"><input type="checkbox" name="svc" value="Dental care"> Dental care</label>
                                        <label class="chip"><input type="checkbox" name="svc" value="Surgery"> Surgery</label>
                                    </div>
                                </div>

                                <div class="field col-12">
                                    <label class="label" for="facServicesOther">Other services <span class="opt">(optional)</span></label>
                                    <textarea class="textarea" id="facServicesOther" data-reg="services_other" placeholder="Briefly describe any other services you offer"></textarea>
                                    <div class="field-error"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                </section>


                {{-- ===== STEP: documents ===== --}}
                <section class="step-panel" data-step-id="documents">

                    <div class="form-section">
                        <div class="section-head">
                            <span class="section-index">A</span>
                            <div>
                                <div class="section-name">Supporting documents</div>
                                <div class="section-hint">PDF, JPG, PNG or WebP. Maximum 5 MB per file.</div>
                            </div>
                        </div>

                        <div class="doc-grid" id="docGrid"></div>
                    </div>

                </section>


                {{-- ===== STEP: review ===== --}}
                <section class="step-panel" data-step-id="review">

                    <div class="review-grid" id="reviewGrid"></div>

                    <label class="declaration">
                        <input type="checkbox" id="finalDeclaration">
                        <span>
                            I declare that the information and documents I have provided are true and complete, and I understand
                            that false information may lead to my application being rejected or my profile being removed.
                        </span>
                    </label>

                    <div class="upload-progress" id="submitProgress" aria-live="polite">
                        <div class="upload-text" id="submitProgressText">Uploading your application\u2026</div>
                        <div class="upload-track"><div class="upload-bar" id="submitProgressBar"></div></div>
                    </div>

                </section>


                {{-- ===== shared action bar ===== --}}
                <div class="action-bar" id="actionBar">
                    <button type="button" class="btn btn-outline" id="backBtn">
                        <i data-lucide="arrow-left" width="16" height="16"></i>
                        Back
                    </button>

                    <span class="grow action-note" id="actionNote"></span>

                    <button type="button" class="btn btn-primary" id="nextBtn">
                        <span id="nextLabel">Continue</span>
                        <i data-lucide="arrow-right" width="16" height="16"></i>
                    </button>
                </div>

            </div>
        </main>


        {{-- ---------- RIGHT: context ---------- --}}
        <aside class="context" id="contextPanel" aria-label="Step guidance and application summary">

            <div class="context-photo">
                <img src="/images/onboarding/stage-account.jpg" alt="" id="stageImage" onerror="this.style.display='none'">
                <div class="context-badge"><span class="pill pill-light" id="stagePill">GET STARTED</span></div>
                <div class="context-caption">
                    <h3 id="stageTitle">Choose your registration path</h3>
                    <p id="stageText"></p>
                </div>
            </div>

            <div class="context-body">

                <div class="snapshot">
                    <div class="snapshot-top">
                        <span>APPLICATION SUMMARY</span>
                        <span class="snapshot-pill">DRAFT</span>
                    </div>

                    <div class="snapshot-user">
                        <div class="snapshot-avatar">
                            <i id="snapshotAvatarPlaceholder" data-lucide="user-round" width="24" height="24"></i>
                            <img src="" alt="Applicant photo" id="snapshotAvatarImage">
                        </div>
                        <div>
                            <div class="snapshot-name" id="snapshotName">Your name</div>
                            <div class="snapshot-role" id="snapshotRole">AfyaLink applicant</div>
                        </div>
                    </div>

                    <div class="snapshot-list">
                        <div class="snapshot-item">
                            <i data-lucide="mail" width="15" height="15"></i>
                            <div><strong>EMAIL</strong><span id="snapshotEmail">Not added</span></div>
                        </div>
                        <div class="snapshot-item">
                            <i data-lucide="phone" width="15" height="15"></i>
                            <div><strong>PHONE</strong><span id="snapshotPhone">Not added</span></div>
                        </div>
                        <div class="snapshot-item">
                            <i data-lucide="building-2" width="15" height="15"></i>
                            <div><strong>FACILITY / WORKPLACE</strong><span id="snapshotFacility">Not selected</span></div>
                        </div>
                    </div>
                </div>

                @if ($supportEmail || $supportPhone)
                    <div class="support-box">
                        <strong>Need help?</strong><br>
                        @if ($supportEmail) <a class="link" href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a><br> @endif
                        @if ($supportPhone) <a class="link" href="tel:{{ preg_replace('/[^\d+]/', '', $supportPhone) }}">{{ $supportPhone }}</a> @endif
                    </div>
                @endif

            </div>
        </aside>

    </div>
</section>


{{-- =========================================================
     SUCCESS
========================================================== --}}
<section class="success-screen hidden" id="successScreen">
    <div class="success-card">

        <div class="success-icon"><i data-lucide="circle-check" width="46" height="46"></i></div>

        <h1>Application submitted</h1>
        <p>Thank you. Our team will review your details and documents. We will email you as soon as there is an update.</p>

        <div class="reference hidden" id="referenceBox">Reference number: <strong id="successReference"></strong></div>

        <div class="timeline">
            <div class="timeline-item">
                <div class="mini-no">1</div>
                <h3>Review</h3>
                <p>We check your details and documents.</p>
            </div>
            <div class="timeline-item">
                <div class="mini-no">2</div>
                <h3>Verification</h3>
                <p>We may contact you if anything needs clarifying.</p>
            </div>
            <div class="timeline-item">
                <div class="mini-no">3</div>
                <h3>Public listing</h3>
                <p>Once approved, your profile appears on AfyaLink.</p>
            </div>
        </div>

        <div class="success-actions">
            <a href="/" class="btn btn-outline">Back to AfyaLink</a>
            <a href="/login" class="btn btn-primary" id="successContinue">Go to my account</a>
        </div>

    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* =====================================================
       CONFIG
    ===================================================== */

    // Existing endpoints are unchanged. "submit" is the new final endpoint (see notes).
    const ROUTES = {
        facilities:  @json(route('registration.facilities')),
        identity:    @json(route('registration.identity')),
        updateIdentity: @json(route('registration.identity.update')),
        emailStatus: @json(route('registration.email.status')),
        resendEmail: @json(route('verification.send')),
        submit:      @json(route('registration.application.submit'))
    };

    const MAX_FILE   = 5 * 1024 * 1024;
    const DRAFT_KEY  = 'afyalink.onboarding.v1';
    const EMAIL_JUST_VERIFIED = @json(session('status') === 'email-verified');

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const scrollBehavior = reduceMotion ? 'auto' : 'smooth';

    const CHECK_SVG = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';

    /* =====================================================
       STEP DEFINITIONS
    ===================================================== */

    const isPro = function () { return state.type === 'professional'; };

    const STEPS = {
        type: {
            label: function () { return 'Registration type'; },
            title: function () { return 'How are you joining AfyaLink?'; },
            description: function () { return 'Choose the option that matches you. This decides which details and documents we will ask for.'; },
            why: function () { return 'Facilities and professionals are verified differently, so we need to know which path to follow.'; },
            image: 'stage-account', pill: 'GET STARTED',
            next: 'Continue'
        },
        workplace: {
            label: function () { return 'Workplace'; },
            title: function () { return 'Select your verified workplace'; },
            description: function () { return 'Search for the AfyaLink-verified facility where you work.'; },
            why: function () { return 'Linking you to a verified facility helps patients trust where you practise.'; },
            image: 'stage-workplace', pill: 'WORKPLACE',
            next: 'Continue'
        },
        personal: {
            label: function () { return isPro() ? 'Personal details' : 'Administrator'; },
            title: function () { return isPro() ? 'Your personal details' : 'Administrator details'; },
            description: function () {
                return isPro()
                    ? 'Enter your details exactly as they appear on your National ID.'
                    : 'Enter the details of the person responsible for this facility\u2019s AfyaLink account, exactly as on their National ID.';
            },
            why: function () { return 'Your NIN and photo are used only to confirm your identity. They are never shown publicly.'; },
            image: 'stage-profile', pill: 'PERSONAL DETAILS',
            next: 'Continue'
        },
        contact: {
            label: function () { return 'Contact & password'; },
            title: function () { return 'Contact details and password'; },
            description: function () { return 'These secure your account and let us reach you about your application.'; },
            why: function () { return 'We verify your email next, so we always have a reliable way to contact you.'; },
            image: 'stage-profile', pill: 'ACCOUNT',
            next: 'Create account & continue'
        },
        verify: {
            label: function () { return 'Verify email'; },
            title: function () { return 'Verify your email address'; },
            description: function () { return 'Open the link we emailed you, then return to this page.'; },
            why: function () { return 'Verifying your email protects your account from misuse.'; },
            image: 'stage-email', pill: 'EMAIL SECURITY',
            next: 'I\u2019ve verified my email'
        },
        regulatory: {
            label: function () { return isPro() ? 'Registration' : 'Facility details'; },
            title: function () { return isPro() ? 'Professional registration details' : 'Facility details'; },
            description: function () {
                return isPro()
                    ? 'Provide your registration and licence details as issued by your regulatory council.'
                    : 'Tell us about your facility, where it is, and the licence it operates under.';
            },
            why: function () {
                return isPro()
                    ? 'Our reviewers check these details against your council\u2019s register.'
                    : 'These details appear on your public profile and map listing once your application is approved.';
            },
            image: 'stage-regulatory', pill: 'REGULATORY',
            next: 'Save & continue'
        },
        documents: {
            label: function () { return 'Documents'; },
            title: function () { return 'Upload supporting documents'; },
            description: function () { return 'Upload clear scans or photos. Required documents are marked.'; },
            why: function () { return 'Documents are used only for verification and are never shown publicly. The facility photo is the only exception.'; },
            image: 'stage-documents', pill: 'DOCUMENTS',
            next: 'Continue to review'
        },
        review: {
            label: function () { return 'Review & submit'; },
            title: function () { return 'Review and submit'; },
            description: function () { return 'Check everything carefully. Use "Edit" on a section to change it.'; },
            why: function () { return 'After you submit, our team reviews your application before your profile is listed.'; },
            image: 'stage-review', pill: 'FINAL REVIEW',
            next: 'Submit application'
        }
    };

    const DOC_DEFS = {
        professional: [
            { key: 'national_id',          title: 'National ID (front and back)',  desc: 'A clear scan or photo showing both sides.', required: true },
            { key: 'practising_licence',   title: 'Practising licence',            desc: 'Your current licence issued by your council.', required: true },
            { key: 'academic_certificate', title: 'Academic / professional certificate', desc: 'Your main qualification certificate.', required: true },
            { key: 'other_supporting',     title: 'Other supporting document',     desc: 'For example a recommendation letter.', required: false }
        ],
        facility: [
            { key: 'operating_licence',        title: 'Facility operating licence',   desc: 'The licence or permit your facility operates under.', required: true },
            { key: 'registration_certificate', title: 'Certificate of registration',  desc: 'Your organisation\u2019s registration document.', required: true },
            { key: 'responsible_officer_id',   title: 'Responsible officer\u2019s ID',    desc: 'National ID of the person responsible for the facility.', required: true },
            { key: 'facility_photo',           title: 'Facility photo',               desc: 'A clear photo of your building. Shown on your public profile.', required: false, imageOnly: true },
            { key: 'other_supporting',         title: 'Other supporting document',    desc: 'Anything else that supports your application.', required: false }
        ]
    };

    const COUNCIL_BY_CADRE = {
        'Doctor': 'Uganda Medical and Dental Practitioners Council',
        'Dentist': 'Uganda Medical and Dental Practitioners Council',
        'Nurse': 'Uganda Nurses and Midwives Council',
        'Midwife': 'Uganda Nurses and Midwives Council',
        'Clinical Officer': 'Allied Health Professionals Council',
        'Laboratory Professional': 'Allied Health Professionals Council',
        'Physiotherapist': 'Allied Health Professionals Council'
    };

    /* =====================================================
       STATE + ELEMENT REFS
    ===================================================== */

    const state = {
        type: null,
        stepId: 'type',
        accountCreated: false,
        verified: false,
        needsType: false,
        maskedEmail: '',
        displayName: '',
        facility: null,
        docs: {},
        location: null,
        dirty: false,
        submitted: false
    };

    const $  = function (s, r) { return (r || document).querySelector(s); };
    const $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

    const welcomeScreen = $('#welcomeScreen');
    const wizardScreen  = $('#wizardScreen');
    const successScreen = $('#successScreen');

    const identityForm  = $('#identityForm');
    const stepMessage   = $('#stepMessage');
    const nextBtn       = $('#nextBtn');
    const backBtn       = $('#backBtn');
    const nextLabel     = $('#nextLabel');

    /* =====================================================
       HELPERS
    ===================================================== */

    const ESC = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
    function esc(v) { return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) { return ESC[c]; }); }

    function csrf() {
        const m = $('meta[name="csrf-token"]');
        return m ? m.content : '';
    }

    function renderIcons() {
        if (!window.lucide) return;
        lucide.createIcons();
        $$('svg.lucide').forEach(function (s) { s.setAttribute('aria-hidden', 'true'); });
    }

    function todayString() {
        const d = new Date();
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        return d.toISOString().split('T')[0];
    }

    function formatSize(bytes) {
        if (bytes < 1024 * 1024) return Math.max(1, Math.round(bytes / 1024)) + ' KB';
        return (bytes / 1024 / 1024).toFixed(1) + ' MB';
    }

    function formatDate(v) {
        if (!v) return '';
        const d = new Date(v + 'T00:00:00');
        return isNaN(d) ? v : d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function val(id) {
        const el = document.getElementById(id);
        return el ? String(el.value || '').trim() : '';
    }

    function fullName() {
        const n = [val('firstName'), val('middleName'), val('lastName')].filter(Boolean).join(' ');
        return n || state.displayName || '';
    }

    function maskNin(v) { return v && v.length >= 4 ? v.slice(0, 2) + '********' + v.slice(-4) : v; }

    function maskEmail(v) {
        const parts = String(v || '').split('@');
        if (parts.length !== 2 || !parts[0]) return '';
        return parts[0].charAt(0) + '***@' + parts[1];
    }

    function setMessage(type, text) {
        stepMessage.className = 'message' + (type ? ' ' + type : '');
        stepMessage.textContent = text || '';
    }

    function typeLabel() {
        return state.type === 'facility' ? 'Healthcare Facility' : state.type === 'professional' ? 'Health Professional' : '';
    }

    /* =====================================================
       FLOW + NAVIGATION
    ===================================================== */

    function flow() {
        return isPro()
            ? ['type', 'workplace', 'personal', 'contact', 'verify', 'regulatory', 'documents', 'review']
            : ['type', 'personal', 'contact', 'verify', 'regulatory', 'documents', 'review'];
    }

    function canGoTo(id) {
        if (!state.accountCreated) return true;
        if (id === 'type') return state.needsType;
        return true;
    }

    function prevStep() {
        const ids = flow();
        const idx = ids.indexOf(state.stepId);
        for (let i = idx - 1; i >= 0; i--) {
            if (canGoTo(ids[i])) return ids[i];
        }
        return null;
    }

    function nextStep() {
        const ids = flow();
        const idx = ids.indexOf(state.stepId);
        return ids[idx + 1] || null;
    }

    function railStatus(id, i, idx) {
        if (i < idx) return 'done';
        if (i === idx) return 'active';
        if (state.accountCreated && ['workplace', 'personal', 'contact'].indexOf(id) !== -1) return 'done';
        if (state.verified && id === 'verify') return 'done';
        return 'todo';
    }

    function renderRail() {
        const ids = flow();
        const idx = ids.indexOf(state.stepId);

        $('#stepRail').innerHTML = ids.map(function (id, i) {
            const status = railStatus(id, i, idx);
            const locked = status === 'done' && !canGoTo(id);
            const sub = status === 'active' ? 'In progress'
                : status === 'done' ? (locked ? 'Completed' : 'Completed, edit')
                : (id === 'workplace' ? 'Professionals' : 'Not started');

            return '<li class="rail-item ' + status + '">'
                + '<button type="button" class="rail-btn" data-goto="' + id + '"'
                + (status === 'done' && !locked ? '' : ' disabled')
                + (status === 'active' ? ' aria-current="step"' : '') + '>'
                + '<span class="rail-dot">' + (status === 'done' ? CHECK_SVG : (i + 1)) + '</span>'
                + '<span><span class="rail-label">' + esc(STEPS[id].label()) + '</span>'
                + '<span class="rail-sub">' + esc(sub) + '</span></span>'
                + '</button></li>';
        }).join('');

        $('#mobileStepLabel').textContent = 'Step ' + (idx + 1) + ' of ' + ids.length;
        $('#mobileStepName').textContent = STEPS[state.stepId].label();
        $('#mobileProgressBar').style.width = (((idx + 1) / ids.length) * 100) + '%';
    }

    $('#stepRail').addEventListener('click', function (e) {
        const btn = e.target.closest('[data-goto]');
        if (!btn || btn.disabled) return;
        showStep(btn.dataset.goto);
    });

    function showStep(id, opts) {
        opts = opts || {};
        const prev = state.stepId;
        state.stepId = id;

        const def = STEPS[id];
        const ids = flow();
        const idx = Math.max(0, ids.indexOf(id));

        $$('.step-panel').forEach(function (p) { p.classList.toggle('active', p.dataset.stepId === id); });

        $('#stepEyebrow').textContent = 'Step ' + (idx + 1) + ' of ' + ids.length;
        $('#stepTitle').textContent = def.title();
        $('#stepDescription').textContent = def.description();
        $('#whyText').textContent = def.why();

        // context column
        $('#stagePill').textContent = def.pill;
        $('#stageTitle').textContent = def.title();
        $('#stageText').textContent = def.description();
        const img = $('#stageImage');
        img.style.display = '';
        img.src = '/images/onboarding/' + def.image + '.jpg';

        // action bar
        nextLabel.textContent = id === 'contact' && state.accountCreated ? 'Save changes & continue' : def.next;
        nextBtn.classList.toggle('btn-green', id === 'review');
        nextBtn.classList.toggle('btn-primary', id !== 'review');
        $('#actionNote').textContent = id === 'review' ? 'You can still go back and edit.' : '';

        const back = prevStep();
        backBtn.classList.toggle('hidden', id === 'type' ? state.accountCreated : back === null);
        updateNextState();

        toggleAccountSpecific();
        toggleIdentityEditState();
        if (id === 'documents') renderDocs();
        if (id === 'review') renderReview();
        if (id === 'verify') startVerifyPolling(); else stopVerifyPolling();
        if (id === 'verify') updateVerifyText();

        renderRail();
        updateSnapshot();

        if (!opts.keepMessage && prev !== id) setMessage('', '');

        if (!opts.silent) {
            window.scrollTo({ top: 0, behavior: scrollBehavior });
            setTimeout(function () { $('#stepTitle').focus({ preventScroll: true }); }, 60);
        }

        renderIcons();
        saveDraft();
    }

    function updateNextState() {
        const id = state.stepId;
        if (id === 'type') nextBtn.disabled = !state.type;
        else if (id === 'review') nextBtn.disabled = !$('#finalDeclaration').checked;
        else nextBtn.disabled = false;
    }

    function toggleAccountSpecific() {
        $$('.account-specific').forEach(function (s) { s.classList.toggle('hidden', s.dataset.for !== state.type); });
    }

    function toggleIdentityEditState() {
        const editing = state.accountCreated;
        $('#passwordSectionName').closest('.form-section').classList.toggle('hidden', editing);
        $('#password').disabled = editing;
        $('#passwordConfirm').disabled = editing;
        $('#password').required = !editing;
        $('#passwordConfirm').required = !editing;
        $('#profilePhotoInput').required = !editing;
    }

    /* =====================================================
       WELCOME
    ===================================================== */

    const termsAgreement = $('#termsAgreement');
    const disclaimerAgreement = $('#disclaimerAgreement');
    const beginRegistration = $('#beginRegistration');

    function updateConsent() { beginRegistration.disabled = !(termsAgreement.checked && disclaimerAgreement.checked); }
    termsAgreement.addEventListener('change', updateConsent);
    disclaimerAgreement.addEventListener('change', updateConsent);

    function openWizard() {
        welcomeScreen.classList.add('hidden');
        wizardScreen.classList.remove('hidden');
        renderIcons();
    }

    beginRegistration.addEventListener('click', function () {
        if (beginRegistration.disabled) return;
        openWizard();
        showStep('type');
    });

    /* =====================================================
       STEP: registration type
    ===================================================== */

    function selectType(value) {
        state.type = value;
        $$('.type-card').forEach(function (c) { c.classList.toggle('selected', c.dataset.type === value); });
        $$('input[name="account_type"]').forEach(function (r) { r.checked = r.value === value; });
        renderRail();
        updateNextState();
        updateSnapshot();
        toggleAccountSpecific();
        $('#mobileStepLabel').textContent = 'Step 1 of ' + flow().length;
        $('#stepEyebrow').textContent = 'Step ' + (flow().indexOf(state.stepId) + 1) + ' of ' + flow().length;
        setMessage('', '');
    }

    $$('input[name="account_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () { if (radio.checked) selectType(radio.value); });
    });

    $('#switchToFacility').addEventListener('click', function () {
        selectType('facility');
        showStep('personal');
    });

    /* =====================================================
       STEP: workplace (facility search)
    ===================================================== */

    const facilitySearch  = $('#facilitySearch');
    const facilityResults = $('#facilityResults');
    let facilityTimer = null;
    let facilityController = null;
    let facilityRequest = 0;

    function closeFacilityResults() {
        facilityResults.classList.remove('open');
        facilitySearch.setAttribute('aria-expanded', 'false');
    }

    function openFacilityResults() {
        facilityResults.classList.add('open');
        facilitySearch.setAttribute('aria-expanded', 'true');
    }

    facilitySearch.addEventListener('input', function () {
        clearTimeout(facilityTimer);
        facilityTimer = setTimeout(searchFacilities, 300);
    });

    facilitySearch.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown') {
            const first = $('.facility-result', facilityResults);
            if (first) { e.preventDefault(); first.focus(); }
        } else if (e.key === 'Escape') {
            closeFacilityResults();
        }
    });

    facilityResults.addEventListener('keydown', function (e) {
        const items = $$('.facility-result', facilityResults);
        const i = items.indexOf(document.activeElement);
        if (e.key === 'ArrowDown') { e.preventDefault(); if (items[i + 1]) items[i + 1].focus(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); if (i > 0) items[i - 1].focus(); else facilitySearch.focus(); }
        else if (e.key === 'Escape') { closeFacilityResults(); facilitySearch.focus(); }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.search-wrap')) closeFacilityResults();
    });

    async function searchFacilities() {
        const q = facilitySearch.value.trim();

        if (facilityController) facilityController.abort();

        if (q.length < 2) { closeFacilityResults(); return; }

        const controller = new AbortController();
        facilityController = controller;
        const requestId = ++facilityRequest;

        facilityResults.innerHTML = '<div class="results-note">Searching verified facilities\u2026</div>';
        openFacilityResults();

        try {
            const response = await fetch(ROUTES.facilities + '?q=' + encodeURIComponent(q), {
                method: 'GET', credentials: 'same-origin', headers: { Accept: 'application/json' }, signal: controller.signal
            });

            const data = await readJson(response);
            if (requestId !== facilityRequest) return;

            if (!response.ok) throw new Error(errorText(data, 'We could not search facilities right now.', response.status));

            renderFacilities(data.facilities || []);
        } catch (err) {
            if (err && err.name === 'AbortError') return;
            if (requestId !== facilityRequest) return;
            facilityResults.innerHTML = '<div class="results-note error">' + esc(err.message || 'We could not search facilities right now.') + '</div>';
            openFacilityResults();
        }
    }

    function renderFacilities(list) {
        if (!list.length) {
            facilityResults.innerHTML = '<div class="results-note">No verified AfyaLink facility matches your search.</div>';
            openFacilityResults();
            return;
        }

        facilityResults.innerHTML = list.map(function (f) {
            const location = [f.town, f.district].filter(Boolean).join(', ');
            const type = String(f.provider_type || 'Healthcare facility').replace(/_/g, ' ');

            return '<button type="button" class="facility-result"'
                + ' data-id="' + esc(f.id) + '" data-name="' + esc(f.name) + '"'
                + ' data-meta="' + esc(type + (location ? ' \u2022 ' + location : '')) + '"'
                + ' data-image="' + esc(f.image || '/images/onboarding/facility-thumb.jpg') + '">'
                + '<img class="facility-thumb" src="' + esc(f.image || '/images/onboarding/facility-thumb.jpg') + '" alt="" loading="lazy"'
                + ' onerror="this.onerror=null;this.src=\'/images/hospital.jpg\'">'
                + '<span><span class="facility-name">' + esc(f.name) + '</span>'
                + '<span class="facility-meta">' + esc(type + (location ? ' \u2022 ' + location : '')) + '</span></span>'
                + '</button>';
        }).join('');

        openFacilityResults();
    }

    facilityResults.addEventListener('click', function (e) {
        const btn = e.target.closest('.facility-result');
        if (!btn) return;

        state.facility = { id: btn.dataset.id, name: btn.dataset.name, meta: btn.dataset.meta, image: btn.dataset.image };
        applyFacility();
        closeFacilityResults();
        clearWorkplaceError();
        saveDraft();
    });

    function applyFacility() {
        const f = state.facility;
        $('#selectedFacilityId').value = f ? f.id : '';

        $('#facilitySearchBlock').classList.toggle('hidden', !!f);
        $('#selectedFacility').classList.toggle('show', !!f);

        if (f) {
            $('#selectedFacilityName').textContent = f.name;
            $('#selectedFacilityMeta').textContent = f.meta;
            const img = $('#selectedFacilityImage');
            img.src = f.image || '/images/hospital.jpg';
            img.onerror = function () { img.onerror = null; img.src = '/images/hospital.jpg'; };
        } else {
            facilitySearch.value = '';
        }

        updateSnapshot();
        state.dirty = true;
    }

    $('#changeFacility').addEventListener('click', function () {
        state.facility = null;
        applyFacility();
        facilitySearch.focus();
    });

    function showWorkplaceError(msg) {
        $('#workplaceField').classList.add('has-error');
        $('#workplaceError').textContent = msg;
    }

    function clearWorkplaceError() {
        $('#workplaceField').classList.remove('has-error');
        $('#workplaceError').textContent = '';
    }

    /* =====================================================
       FIELD VALIDATION ENGINE
    ===================================================== */

    const VALIDATORS = {
        photo: function (v, el) { return el.files && el.files.length ? '' : 'Please upload a profile photo.'; },

        dob: function (v) {
            const d = new Date(v + 'T00:00:00');
            if (isNaN(d)) return 'Enter a valid date of birth.';
            const now = new Date();
            let age = now.getFullYear() - d.getFullYear();
            const m = now.getMonth() - d.getMonth();
            if (m < 0 || (m === 0 && now.getDate() < d.getDate())) age--;
            if (age < 18) return 'You must be at least 18 years old to register.';
            if (age > 100) return 'Please check the year of birth.';
            return '';
        },

        nin: function (v) {
            return /^C[MF][A-Z0-9]{12}$/.test(v) ? '' : 'Enter the 14-character NIN from your National ID. It starts with CM or CF.';
        },

        phone: function (v) {
            return /^\+?\d{9,15}$/.test(v.replace(/[\s()-]/g, '')) ? '' : 'Enter a valid phone number, for example +256 7XX XXX XXX.';
        },

        email: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) ? '' : 'Enter a valid email address.'; },

        optionalEmail: function (v) { return !v || /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) ? '' : 'Enter a valid email address.'; },

        password: function (v) {
            return v.length >= 8 && /[a-z]/.test(v) && /[A-Z]/.test(v) && /\d/.test(v) && /[^A-Za-z0-9]/.test(v)
                ? ''
                : 'Use 8+ characters with upper and lowercase letters, a number and a symbol.';
        },

        passwordConfirm: function (v) { return v === $('#password').value ? '' : 'The passwords do not match.'; },

        futureDate: function (v) { return v >= todayString() ? '' : 'This date has already passed. Please use a current licence.'; }
    };

    function labelFor(el) {
        const l = el.id ? $('label[for="' + el.id + '"]') : null;
        const t = l ? l.textContent.replace('*', '').replace('(optional)', '').trim() : 'This field';
        return t;
    }

    function errorBox(el) {
        const wrap = el.closest('.field');
        return wrap ? $('.field-error', wrap) || (function () {
            const d = document.createElement('div');
            d.className = 'field-error';
            wrap.appendChild(d);
            return d;
        })() : null;
    }

    function setFieldError(el, msg) {
        const wrap = el.closest('.field');
        if (!wrap) return;
        const box = errorBox(el);
        if (box) {
            if (!box.id) box.id = (el.id || el.name || 'field') + '-error';
            box.textContent = msg || '';
        }
        wrap.classList.toggle('has-error', !!msg);
        el.setAttribute('aria-invalid', msg ? 'true' : 'false');
        if (msg && box) el.setAttribute('aria-describedby', box.id); else el.removeAttribute('aria-describedby');
    }

    function validateControl(el) {
        if (el.disabled || el.closest('.hidden')) return '';

        const isFile = el.type === 'file';
        const value = isFile ? '' : String(el.value || '').trim();
        const custom = el.dataset.validate;

        if (el.required && !isFile && !value) return labelFor(el) + ' is required.';
        if (custom && VALIDATORS[custom]) {
            if (isFile) return VALIDATORS[custom](value, el);
            if (value || el.required) return VALIDATORS[custom](value, el);
        }
        return '';
    }

    function validateScope(scope) {
        const controls = $$('input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]), select, textarea', scope)
            .filter(function (el) { return !el.closest('.hidden'); });

        let first = null;

        controls.forEach(function (el) {
            const msg = validateControl(el);
            setFieldError(el, msg);
            if (msg && !first) first = el;
        });

        if (first) {
            setMessage('error', 'Please correct the highlighted fields to continue.');
            const target = first.type === 'file' ? first.closest('.field') : first;
            if (target) {
                target.scrollIntoView({ behavior: scrollBehavior, block: 'center' });
                if (first.type !== 'file') first.focus({ preventScroll: true });
            }
            return false;
        }

        setMessage('', '');
        return true;
    }

    // live validation: on blur, and re-check while editing a field that already shows an error
    document.addEventListener('focusout', function (e) {
        const el = e.target;
        if (!el.matches || !el.matches('input, select, textarea')) return;
        if (!el.closest('.form-card') || el.type === 'checkbox' || el.type === 'radio' || el.type === 'hidden') return;
        if (el.id === 'facilitySearch') return;
        if (!el.required && !el.dataset.validate) return;
        if (el.type !== 'file' && !String(el.value).trim() && !el.required) return;
        setFieldError(el, validateControl(el));
    });

    document.addEventListener('input', function (e) {
        const el = e.target;
        if (!el.closest || !el.closest('.form-card')) return;
        state.dirty = true;
        const wrap = el.closest('.field');
        if (wrap && wrap.classList.contains('has-error')) setFieldError(el, validateControl(el));
    });

    /* =====================================================
       STEP: personal details
    ===================================================== */

    const photoInput   = $('#profilePhotoInput');
    const photoPreview = $('#profilePhotoPreview');
    const photoHolder  = $('#profilePhotoPlaceholder');
    const photoName    = $('#profilePhotoName');
    const snapImg      = $('#snapshotAvatarImage');
    const snapHolder   = $('#snapshotAvatarPlaceholder');
    const cameraPanel  = $('#cameraPanel');
    const cameraVideo  = $('#cameraVideo');
    const cameraCanvas = $('#cameraCanvas');
    let cameraStream = null;

    $('#dob').max = todayString();

    function resetPhoto() {
        photoInput.value = '';
        photoPreview.src = ''; photoPreview.style.display = 'none'; photoHolder.style.display = '';
        snapImg.src = ''; snapImg.style.display = 'none'; snapHolder.style.display = '';
        photoName.textContent = '';
        $('#photoBtnText').textContent = 'Upload photo';
        $('#photoRemove').classList.add('hidden');
    }

    function stopCamera() {
        if (cameraStream) cameraStream.getTracks().forEach(function (track) { track.stop(); });
        cameraStream = null;
        cameraVideo.srcObject = null;
        cameraPanel.classList.remove('show');
    }

    $('#openCamera').addEventListener('click', async function () {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            setFieldError(photoInput, 'Live camera capture is not supported on this device. Please upload a photo instead.');
            return;
        }

        try {
            stopCamera();
            cameraPanel.classList.add('show');
            $('#cameraStatus').textContent = 'Requesting camera access…';
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 960 } },
                audio: false
            });
            cameraVideo.srcObject = cameraStream;
            $('#cameraStatus').textContent = 'Centre your face inside the guide, then capture the photo.';
        } catch (error) {
            stopCamera();
            setFieldError(photoInput, 'Camera access was not available. Allow camera permission or upload a photo instead.');
        }
    });

    $('#capturePhoto').addEventListener('click', function () {
        if (!cameraStream || !cameraVideo.videoWidth) return;

        cameraCanvas.width = cameraVideo.videoWidth;
        cameraCanvas.height = cameraVideo.videoHeight;
        const context = cameraCanvas.getContext('2d');
        context.translate(cameraCanvas.width, 0);
        context.scale(-1, 1);
        context.drawImage(cameraVideo, 0, 0);

        cameraCanvas.toBlob(function (blob) {
            if (!blob) return;
            const file = new File([blob], 'live-profile-photo.jpg', { type: 'image/jpeg' });
            const transfer = new DataTransfer();
            transfer.items.add(file);
            photoInput.files = transfer.files;
            photoInput.dispatchEvent(new Event('change', { bubbles: true }));
            stopCamera();
        }, 'image/jpeg', .9);
    });

    $('#closeCamera').addEventListener('click', stopCamera);
    window.addEventListener('beforeunload', stopCamera);

    photoInput.addEventListener('change', function () {
        const file = photoInput.files && photoInput.files[0];

        if (!file) { resetPhoto(); return; }

        if (['image/jpeg', 'image/png', 'image/webp'].indexOf(file.type) === -1) {
            resetPhoto();
            setFieldError(photoInput, 'Please choose a JPG, PNG or WebP image.');
            return;
        }

        if (file.size > MAX_FILE) {
            resetPhoto();
            setFieldError(photoInput, 'The photo is ' + formatSize(file.size) + '. Please choose one under 5 MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (ev) {
            photoPreview.src = ev.target.result; photoPreview.style.display = 'block'; photoHolder.style.display = 'none';
            snapImg.src = ev.target.result; snapImg.style.display = 'block'; snapHolder.style.display = 'none';
            photoName.textContent = '\u2713 ' + file.name + ' (' + formatSize(file.size) + ')';
            $('#photoBtnText').textContent = 'Replace photo';
            $('#photoRemove').classList.remove('hidden');
            setFieldError(photoInput, '');
        };
        reader.readAsDataURL(file);
    });

    $('#photoRemove').addEventListener('click', function () { resetPhoto(); });

    const ninInput = $('#ninInput');
    const ninValidation = $('#ninValidation');

    ninInput.addEventListener('input', function () {
        ninInput.value = ninInput.value.toUpperCase().replace(/\s+/g, '');
        const v = ninInput.value;

        if (!v) { ninValidation.className = 'validation-text'; ninValidation.textContent = ''; return; }

        if (/^C[MF][A-Z0-9]{12}$/.test(v)) {
            ninValidation.className = 'validation-text success';
            ninValidation.textContent = '\u2713 NIN format looks right.';
        } else {
            ninValidation.className = 'validation-text';
            ninValidation.textContent = v.length + ' of 14 characters';
        }
    });

    ['firstName', 'middleName', 'lastName', 'phone', 'email'].forEach(function (id) {
        $('#' + id).addEventListener('input', updateSnapshot);
    });

    /* =====================================================
       STEP: contact + password
    ===================================================== */

    $$('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(btn.dataset.togglePassword);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.textContent = show ? 'Hide' : 'Show';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });

    function updatePasswordRules() {
        const p = $('#password').value;
        const c = $('#passwordConfirm').value;
        $('#ruleLength').classList.toggle('ok', p.length >= 8);
        $('#ruleMix').classList.toggle('ok', /[a-z]/.test(p) && /[A-Z]/.test(p) && /\d/.test(p) && /[^A-Za-z0-9]/.test(p));
        $('#ruleMatch').classList.toggle('ok', !!p && p === c);
    }

    $('#password').addEventListener('input', updatePasswordRules);
    $('#passwordConfirm').addEventListener('input', updatePasswordRules);

    async function createAccount() {
        const fd = new FormData(identityForm);
        fd.set('nin', ninInput.value.trim().toUpperCase());
        fd.append('application_type', state.type);

        if (isPro()) {
            if (!state.facility) { showStep('workplace'); showWorkplaceError('Please select your verified workplace.'); return; }
            fd.append('healthcare_provider_id', state.facility.id);
        }

        setBusy(true, 'Creating your account\u2026');
        setMessage('', '');

        try {
            const response = await fetch(ROUTES.identity, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });

            const result = await readJson(response);

            if (response.status === 419) throw new Error('Your session expired. Please refresh the page and try again.');

            if (!response.ok) {
                if (result && result.errors && applyServerErrors(result.errors)) return;
                throw new Error(errorText(result, 'We could not create your account. Please try again.', response.status));
            }

            state.accountCreated = true;
            state.maskedEmail = maskEmail(val('email'));
            state.displayName = fullName();
            state.dirty = true;

            $('#password').value = '';
            $('#passwordConfirm').value = '';
            updatePasswordRules();

            setMessage('success', (result && result.message) || 'Your account has been created.');
            setTimeout(function () { showStep('verify', { keepMessage: true }); startCooldown(30); }, 500);
        } catch (err) {
            setMessage('error', err.message || 'We could not create your account. Please try again.');
        } finally {
            setBusy(false);
        }
    }

    async function updateAccount() {
        const fd = new FormData(identityForm);
        fd.delete('password');
        fd.delete('password_confirmation');
        fd.set('nin', ninInput.value.trim().toUpperCase());
        fd.set('application_type', state.type);

        if (isPro()) {
            if (!state.facility) { showStep('workplace'); showWorkplaceError('Please select your verified workplace.'); return; }
            fd.set('healthcare_provider_id', state.facility.id);
        } else {
            fd.delete('healthcare_provider_id');
        }

        setBusy(true, 'Saving your changes\u2026');
        setMessage('', '');

        try {
            const response = await fetch(ROUTES.updateIdentity, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });
            const result = await readJson(response);

            if (response.status === 419) throw new Error('Your session expired. Please refresh the page and try again.');
            if (!response.ok) {
                if (result && result.errors && applyServerErrors(result.errors)) return;
                throw new Error(errorText(result, 'We could not save your changes. Please try again.', response.status));
            }

            state.maskedEmail = maskEmail(val('email'));
            state.displayName = fullName();
            state.verified = result.next_step !== 'email';
            state.dirty = true;
            setMessage('success', result.message || 'Your changes were saved.');
            setTimeout(function () { showStep(result.next_step === 'email' ? 'verify' : 'regulatory', { keepMessage: true }); }, 400);
        } catch (err) {
            setMessage('error', err.message || 'We could not save your changes. Please try again.');
        } finally {
            setBusy(false);
        }
    }

    function applyServerErrors(errors) {
        let firstEl = null;
        let firstPanel = null;

        Object.keys(errors).forEach(function (key) {
            const el = $('[name="' + key + '"]', identityForm);
            if (!el) return;
            setFieldError(el, [].concat(errors[key]).join(' '));
            if (!firstEl) { firstEl = el; firstPanel = el.closest('.step-panel'); }
        });

        if (!firstEl) return false;

        if (firstPanel && firstPanel.dataset.stepId !== state.stepId) showStep(firstPanel.dataset.stepId, { keepMessage: true });
        setMessage('error', 'Some details need to be corrected. Please check the highlighted fields.');
        firstEl.scrollIntoView({ behavior: scrollBehavior, block: 'center' });
        if (firstEl.type !== 'file') firstEl.focus({ preventScroll: true });
        return true;
    }

    /* =====================================================
       STEP: verify email
    ===================================================== */

    let pollTimer = null;
    let checking = false;
    let cooldownTimer = null;

    function updateVerifyText() {
        $('#verifyEmailText').textContent = state.maskedEmail || 'your email address';
    }

    async function checkEmail(manual) {
        if (checking) return;
        checking = true;

        if (manual) { setBusy(true, 'Checking\u2026'); setMessage('', ''); }

        try {
            const response = await fetch(ROUTES.emailStatus, {
                method: 'GET', credentials: 'same-origin', headers: { Accept: 'application/json' }
            });

            const data = await readJson(response);

            if (!response.ok) {
                if (manual) throw new Error(errorText(data, 'We could not check your email status. Please try again.', response.status));
                return;
            }

            if (data.verified) {
                state.verified = true;
                stopVerifyPolling();
                setMessage('success', 'Email verified. Let\u2019s continue.');
                setTimeout(function () { showStep('regulatory', { keepMessage: true }); }, 600);
            } else if (manual) {
                setMessage('error', 'Your email is not verified yet. Open the link in your inbox, then try again.');
            }
        } catch (err) {
            if (manual) setMessage('error', err.message);
        } finally {
            checking = false;
            if (manual) setBusy(false);
        }
    }

    function startVerifyPolling() {
        stopVerifyPolling();
        pollTimer = setInterval(function () {
            if (document.visibilityState === 'visible' && state.stepId === 'verify') checkEmail(false);
        }, 6000);
    }

    function stopVerifyPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible' && state.stepId === 'verify') checkEmail(false);
    });

    function startCooldown(seconds) {
        const btn = $('#resendEmailButton');
        clearInterval(cooldownTimer);
        let left = seconds;

        btn.disabled = true;
        btn.textContent = 'Resend in ' + left + 's';

        cooldownTimer = setInterval(function () {
            left--;
            if (left <= 0) {
                clearInterval(cooldownTimer);
                btn.disabled = false;
                btn.textContent = 'Resend email';
            } else {
                btn.textContent = 'Resend in ' + left + 's';
            }
        }, 1000);
    }

    $('#resendEmailButton').addEventListener('click', async function () {
        setMessage('', '');
        this.disabled = true;

        try {
            const response = await fetch(ROUTES.resendEmail, {
                method: 'POST', credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await readJson(response);

            if (!response.ok) throw new Error(errorText(data, 'We could not resend the email. Please try again shortly.', response.status));

            setMessage('success', (data && data.message) || 'Verification email sent again.');
            startCooldown(60);
        } catch (err) {
            setMessage('error', err.message);
            this.disabled = false;
        }
    });

    /* =====================================================
       STEP: regulatory / facility details
    ===================================================== */

    $('#proCadre').addEventListener('change', function () {
        const body = $('#proBody');
        const suggestion = COUNCIL_BY_CADRE[this.value];
        if (suggestion && (!body.value.trim() || Object.keys(COUNCIL_BY_CADRE).some(function (k) { return COUNCIL_BY_CADRE[k] === body.value; }))) {
            body.value = suggestion;
            setFieldError(body, '');
        }
    });

    $('#proLicenseExpiry').min = todayString();
    $('#facLicenseExpiry').min = todayString();

    $('#locateFacility').addEventListener('click', function () {
        const status = $('#locationStatus');
        const btn = this;

        if (!navigator.geolocation) {
            status.className = 'locate-status';
            status.textContent = 'Your browser does not support location. You can skip this step.';
            return;
        }

        status.className = 'locate-status';
        status.textContent = 'Finding your location\u2026';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                btn.disabled = false;
                state.location = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                status.className = 'locate-status ok';
                status.textContent = '\u2713 Location captured (accurate to about ' + Math.round(pos.coords.accuracy) + ' m).';
                saveDraft();
            },
            function (err) {
                btn.disabled = false;
                status.className = 'locate-status';
                status.textContent = err && err.code === 1
                    ? 'Location permission was denied. You can skip this and we will place your facility during review.'
                    : 'We could not get your location. You can skip this step.';
            },
            { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
        );
    });

    function selectedServices() {
        return $$('input[name="svc"]:checked').map(function (c) { return c.value; });
    }

    /* =====================================================
       STEP: documents
    ===================================================== */

    function docDefs() { return DOC_DEFS[state.type] || []; }

    function docCardHtml(def) {
        const file = state.docs[def.key];
        const accept = def.imageOnly ? 'image/jpeg,image/png,image/webp' : 'application/pdf,image/jpeg,image/png,image/webp';
        const badge = def.required ? '<span class="badge badge-req">REQUIRED</span>' : '<span class="badge badge-opt">OPTIONAL</span>';

        let body;
        if (file) {
            const isImg = /^image\//.test(file.type);
            body = '<div class="doc-file">'
                + (isImg ? '<img src="' + esc(file.__preview || '') + '" alt="">' : '<span class="doc-icon" style="width:44px;height:44px"><i data-lucide="file-text" width="20" height="20"></i></span>')
                + '<div class="doc-file-info"><div class="doc-file-name">' + esc(file.name) + '</div><div class="doc-file-size">' + esc(formatSize(file.size)) + '</div></div>'
                + '<button type="button" class="doc-remove" data-remove-doc="' + def.key + '" aria-label="Remove ' + esc(def.title) + '"><i data-lucide="trash-2" width="17" height="17"></i></button>'
                + '</div>';
        } else {
            body = '<label class="doc-drop" for="doc-' + def.key + '">'
                + '<i data-lucide="upload" width="18" height="18"></i> Choose file'
                + '<small>' + (def.imageOnly ? 'JPG, PNG or WebP' : 'PDF, JPG, PNG or WebP') + ', up to 5 MB</small></label>';
        }

        return '<div class="doc-card' + (file ? ' has-file' : '') + '" data-doc="' + def.key + '">'
            + '<div class="doc-top"><span class="doc-icon"><i data-lucide="' + (def.imageOnly ? 'image' : 'file-text') + '" width="20" height="20"></i></span>'
            + '<div><div class="doc-title">' + esc(def.title) + badge + '</div><div class="doc-desc">' + esc(def.desc) + '</div></div></div>'
            + body
            + '<input type="file" class="sr-only" id="doc-' + def.key + '" data-doc-input="' + def.key + '" accept="' + accept + '">'
            + '<div class="doc-error" role="alert"></div>'
            + '</div>';
    }

    function renderDocs() {
        $('#docGrid').innerHTML = docDefs().map(docCardHtml).join('');
        renderIcons();
    }

    function setDocError(key, msg) {
        const card = $('.doc-card[data-doc="' + key + '"]');
        if (!card) return;
        card.classList.toggle('has-error', !!msg);
        $('.doc-error', card).textContent = msg || '';
    }

    $('#docGrid').addEventListener('change', function (e) {
        const input = e.target.closest('[data-doc-input]');
        if (!input) return;

        const key = input.dataset.docInput;
        const def = docDefs().filter(function (d) { return d.key === key; })[0];
        const file = input.files && input.files[0];
        if (!file || !def) return;

        const allowed = def.imageOnly ? ['image/jpeg', 'image/png', 'image/webp'] : ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];

        if (allowed.indexOf(file.type) === -1) {
            input.value = '';
            setDocError(key, def.imageOnly ? 'Please choose a JPG, PNG or WebP image.' : 'Please choose a PDF, JPG, PNG or WebP file.');
            return;
        }

        if (file.size > MAX_FILE) {
            input.value = '';
            setDocError(key, 'This file is ' + formatSize(file.size) + '. The maximum is 5 MB.');
            return;
        }

        if (/^image\//.test(file.type)) file.__preview = URL.createObjectURL(file);
        state.docs[key] = file;
        state.dirty = true;

        const card = $('.doc-card[data-doc="' + key + '"]');
        const holder = document.createElement('div');
        holder.innerHTML = docCardHtml(def);
        card.replaceWith(holder.firstChild);
        renderIcons();
        setMessage('', '');
    });

    $('#docGrid').addEventListener('click', function (e) {
        const btn = e.target.closest('[data-remove-doc]');
        if (!btn) return;

        const key = btn.dataset.removeDoc;
        const f = state.docs[key];
        if (f && f.__preview) URL.revokeObjectURL(f.__preview);
        delete state.docs[key];

        const def = docDefs().filter(function (d) { return d.key === key; })[0];
        const card = $('.doc-card[data-doc="' + key + '"]');
        const holder = document.createElement('div');
        holder.innerHTML = docCardHtml(def);
        card.replaceWith(holder.firstChild);
        renderIcons();
    });

    function validateDocs() {
        let firstKey = null;

        docDefs().forEach(function (def) {
            const missing = def.required && !state.docs[def.key];
            setDocError(def.key, missing ? 'This document is required.' : '');
            if (missing && !firstKey) firstKey = def.key;
        });

        if (firstKey) {
            setMessage('error', 'Please upload all required documents to continue.');
            const card = $('.doc-card[data-doc="' + firstKey + '"]');
            if (card) card.scrollIntoView({ behavior: scrollBehavior, block: 'center' });
            return false;
        }

        setMessage('', '');
        return true;
    }

    /* =====================================================
       STEP: review
    ===================================================== */

    function reviewCard(title, rows, editStep, wide) {
        const canEdit = editStep && canGoTo(editStep);
        const head = '<div class="review-head"><h3>' + esc(title) + '</h3>'
            + (canEdit
                ? '<button type="button" class="review-edit" data-edit="' + editStep + '"><i data-lucide="pencil" width="13" height="13"></i> Edit</button>'
                : (editStep ? '<span class="review-locked"><i data-lucide="lock" width="12" height="12"></i> Saved</span>' : ''))
            + '</div>';

        const list = rows.map(function (r) {
            return '<div class="review-row"><dt>' + esc(r[0]) + '</dt><dd>' + (r[1] ? esc(r[1]) : '<span style="color:var(--muted)">Not provided</span>') + '</dd></div>';
        }).join('');

        return '<div class="review-card' + (wide ? ' wide' : '') + '">' + head + '<dl class="review-list">' + list + '</dl></div>';
    }

    function renderReview() {
        const cards = [];

        const reg = [['Registering as', typeLabel()]];
        if (isPro()) reg.push(['Workplace', state.facility ? state.facility.name : '']);
        cards.push(reviewCard('Registration', reg, state.needsType ? 'type' : (isPro() ? 'workplace' : null)));

        cards.push(reviewCard(isPro() ? 'Personal details' : 'Administrator details', [
            ['Full name', fullName()],
            ['Date of birth', formatDate(val('dob'))],
            ['Sex', val('sex') ? val('sex').charAt(0).toUpperCase() + val('sex').slice(1) : ''],
            ['Nationality', val('nationality')],
            ['NIN', val('ninInput') ? maskNin(val('ninInput')) : (state.accountCreated ? 'Provided' : '')]
        ], 'personal'));

        cards.push(reviewCard('Contact', [
            ['Email', val('email') || state.maskedEmail],
            ['Phone', val('phone')]
        ], 'contact'));

        if (isPro()) {
            cards.push(reviewCard('Professional registration', [
                ['Profession', val('proCadre')],
                ['Specialty', val('proSpecialty')],
                ['Regulatory body', val('proBody')],
                ['Registration number', val('proRegNo')],
                ['Licence number', val('proLicense')],
                ['Licence expiry', formatDate(val('proLicenseExpiry'))],
                ['Years of experience', val('proExperience')]
            ], 'regulatory'));
        } else {
            const services = selectedServices().concat(val('facServicesOther') ? [val('facServicesOther')] : []);
            cards.push(reviewCard('Facility details', [
                ['Facility name', val('facName')],
                ['Type', val('facType')],
                ['Ownership', val('facOwnership')],
                ['Location', [val('facTown'), val('facDistrict')].filter(Boolean).join(', ')],
                ['Address', val('facAddress')],
                ['Map pin', state.location ? 'Captured' : 'Not added'],
                ['Licence number', val('facLicense')],
                ['Licence expiry', formatDate(val('facLicenseExpiry'))],
                ['In-charge', val('facInCharge')],
                ['Public phone', val('facPhone')],
                ['Public email', val('facEmail')],
                ['Hours', $('#facOpen24').checked ? 'Open 24 hours' : ''],
                ['Emergency services', $('#facEmergency').checked ? 'Available' : ''],
                ['Services', services.join(', ')]
            ], 'regulatory', true));
        }

        const docRows = docDefs().map(function (def) {
            const f = state.docs[def.key];
            return [def.title, f ? '\u2713 ' + f.name + ' (' + formatSize(f.size) + ')' : (def.required ? 'Missing' : '')];
        });

        cards.push('<div class="review-card wide"><div class="review-head"><h3>Documents</h3>'
            + '<button type="button" class="review-edit" data-edit="documents"><i data-lucide="pencil" width="13" height="13"></i> Edit</button></div>'
            + '<dl class="review-list review-docs">'
            + docRows.map(function (r) {
                return '<div class="review-row"><dt>' + esc(r[0]) + '</dt><dd>' + (r[1] ? esc(r[1]) : '<span style="color:var(--muted)">Not provided</span>') + '</dd></div>';
            }).join('')
            + '</dl></div>');

        $('#reviewGrid').innerHTML = cards.join('');
        renderIcons();
    }

    $('#reviewGrid').addEventListener('click', function (e) {
        const btn = e.target.closest('[data-edit]');
        if (btn) showStep(btn.dataset.edit);
    });

    $('#finalDeclaration').addEventListener('change', updateNextState);

    /* =====================================================
       FINAL SUBMIT
    ===================================================== */

    function xhrPost(url, fd, onProgress) {
        return new Promise(function (resolve, reject) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url);
            xhr.withCredentials = true;
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf());
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) onProgress(Math.round((e.loaded / e.total) * 100));
            };

            xhr.onload = function () {
                let data = {};
                try { data = JSON.parse(xhr.responseText); } catch (err) { data = {}; }
                resolve({ status: xhr.status, ok: xhr.status >= 200 && xhr.status < 300, data: data });
            };

            xhr.onerror = function () { reject(new Error('network')); };
            xhr.send(fd);
        });
    }

    async function submitApplication() {
        if (!$('#finalDeclaration').checked) { setMessage('error', 'Please confirm the declaration before submitting.'); return; }

        const fd = new FormData();
        fd.append('application_type', state.type);
        if (isPro() && state.facility) fd.append('healthcare_provider_id', state.facility.id);

        // regulatory fields: regulatory[name], regulatory[licence_number], ...
        $$('.account-specific:not(.hidden) [data-reg]').forEach(function (el) {
            const key = el.dataset.reg;
            if (el.type === 'checkbox') { fd.append('regulatory[' + key + ']', el.checked ? '1' : '0'); return; }
            const v = String(el.value || '').trim();
            if (v) fd.append('regulatory[' + key + ']', v);
        });

        if (!isPro()) {
            selectedServices().forEach(function (s) { fd.append('regulatory[services][]', s); });
            if (state.location) {
                fd.append('regulatory[latitude]', state.location.lat);
                fd.append('regulatory[longitude]', state.location.lng);
            }
        }

        Object.keys(state.docs).forEach(function (key) {
            fd.append('documents[' + key + ']', state.docs[key], state.docs[key].name);
        });

        fd.append('declaration', '1');

        setBusy(true, 'Submitting\u2026');
        setMessage('', '');
        $('#submitProgress').classList.add('show');

        try {
            const result = await xhrPost(ROUTES.submit, fd, function (pct) {
                $('#submitProgressBar').style.width = pct + '%';
                $('#submitProgressText').textContent = pct < 100 ? 'Uploading your application\u2026 ' + pct + '%' : 'Finishing up\u2026';
            });

            if (result.status === 419) throw new Error(errorText(result.data, '', result.status));

            if (result.status === 401) throw new Error(errorText(result.data, '', result.status));

            if (result.status === 403) throw new Error('Please verify your email address before submitting your application.');

            if (!result.ok) {
                if (result.status === 404 || result.status === 405) {
                    console.error('Final submission endpoint is not available: ' + ROUTES.submit);
                    throw new Error('The submission service was refreshed. Reload this page and submit again; your draft is still saved.');
                }
                throw new Error(errorText(result.data, 'We could not submit your application. Please try again.', result.status));
            }

            showSuccess(result.data || {});
        } catch (err) {
            $('#submitProgress').classList.remove('show');
            $('#submitProgressBar').style.width = '0';
            setMessage('error', err.message === 'network'
                ? 'We could not reach AfyaLink. Check your internet connection and try again. Your details are still here.'
                : err.message);
            window.scrollTo({ top: 0, behavior: scrollBehavior });
        } finally {
            setBusy(false);
        }
    }

    function showSuccess(data) {
        state.submitted = true;
        state.dirty = false;
        try { sessionStorage.removeItem(DRAFT_KEY); } catch (e) { /* ignore */ }

        stopVerifyPolling();
        wizardScreen.classList.add('hidden');
        successScreen.classList.remove('hidden');

        if (data.reference) {
            $('#successReference').textContent = data.reference;
            $('#referenceBox').classList.remove('hidden');
        }

        if (data.redirect) $('#successContinue').href = data.redirect;

        window.scrollTo({ top: 0, behavior: scrollBehavior });
        renderIcons();
    }

    /* =====================================================
       NEXT / BACK
    ===================================================== */

    function setBusy(busy, text) {
        nextBtn.disabled = busy ? true : false;
        backBtn.disabled = busy;
        if (busy && text) { nextBtn.dataset.label = nextLabel.textContent; nextLabel.textContent = text; }
        if (!busy) {
            if (nextBtn.dataset.label) { nextLabel.textContent = nextBtn.dataset.label; delete nextBtn.dataset.label; }
            updateNextState();
        }
    }

    nextBtn.addEventListener('click', async function () {
        const id = state.stepId;

        if (id === 'type') {
            if (!state.type) { setMessage('error', 'Please choose how you are joining AfyaLink.'); return; }
            if (state.accountCreated) { showStep(state.verified ? 'regulatory' : 'verify'); return; }
            showStep(nextStep());
            return;
        }

        if (id === 'workplace') {
            if (!state.facility) { showWorkplaceError('Please select your verified workplace from the list.'); setMessage('error', 'Please select your verified workplace to continue.'); return; }
            showStep(nextStep());
            return;
        }

        if (id === 'personal') {
            if (validateScope($('[data-step-id="personal"]'))) showStep('contact');
            return;
        }

        if (id === 'contact') {
            if (!validateScope($('[data-step-id="contact"]'))) return;
            if (state.accountCreated) await updateAccount(); else await createAccount();
            return;
        }

        if (id === 'verify') { await checkEmail(true); return; }

        if (id === 'regulatory') {
            if (validateScope($('[data-step-id="regulatory"]'))) showStep('documents');
            return;
        }

        if (id === 'documents') {
            if (validateDocs()) showStep('review');
            return;
        }

        if (id === 'review') { await submitApplication(); }
    });

    backBtn.addEventListener('click', function () {
        if (state.stepId === 'type') {
            wizardScreen.classList.add('hidden');
            welcomeScreen.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: scrollBehavior });
            return;
        }

        const prev = prevStep();
        if (prev) showStep(prev);
    });

    // pressing Enter inside the account form moves forward instead of submitting natively
    identityForm.addEventListener('submit', function (e) { e.preventDefault(); nextBtn.click(); });

    /* =====================================================
       SNAPSHOT
    ===================================================== */

    function updateSnapshot() {
        $('#snapshotName').textContent = fullName() || 'Your name';
        $('#snapshotRole').textContent = state.type === 'facility' ? 'Facility administrator'
            : state.type === 'professional' ? 'Health professional' : 'AfyaLink applicant';
        $('#snapshotEmail').textContent = val('email') || state.maskedEmail || 'Not added';
        $('#snapshotPhone').textContent = val('phone') || 'Not added';
        $('#snapshotFacility').textContent = isPro()
            ? (state.facility ? state.facility.name : 'Not selected')
            : (state.type === 'facility' ? (val('facName') || 'Registering a new facility') : 'Not selected');
    }

    $('#facName').addEventListener('input', updateSnapshot);

    /* =====================================================
       DRAFT (kept only for this browser tab, never sensitive data)
    ===================================================== */

    function saveDraft() {
        try {
            if (!state.accountCreated || state.submitted) { sessionStorage.removeItem(DRAFT_KEY); return; }

            const fields = {};
            $$('[data-reg]').forEach(function (el) {
                fields[el.id] = el.type === 'checkbox' ? el.checked : el.value;
            });

            sessionStorage.setItem(DRAFT_KEY, JSON.stringify({
                type: state.type,
                stepId: state.stepId,
                verified: state.verified,
                maskedEmail: state.maskedEmail,
                displayName: state.displayName,
                facility: state.facility,
                location: state.location,
                services: selectedServices(),
                fields: fields
            }));
        } catch (e) { /* storage unavailable */ }
    }

    function loadDraft() {
        try {
            const raw = sessionStorage.getItem(DRAFT_KEY);
            if (!raw) return false;

            const d = JSON.parse(raw);
            if (!d || !d.type) return false;

            state.accountCreated = true;
            state.verified = !!d.verified;
            state.maskedEmail = d.maskedEmail || '';
            state.displayName = d.displayName || '';
            state.facility = d.facility || null;
            state.location = d.location || null;

            selectType(d.type);
            if (state.facility) applyFacility();

            Object.keys(d.fields || {}).forEach(function (id) {
                const el = document.getElementById(id);
                if (!el) return;
                if (el.type === 'checkbox') el.checked = !!d.fields[id]; else el.value = d.fields[id];
            });

            $$('input[name="svc"]').forEach(function (c) { c.checked = (d.services || []).indexOf(c.value) !== -1; });

            if (state.location) {
                $('#locationStatus').className = 'locate-status ok';
                $('#locationStatus').textContent = '\u2713 Location captured.';
            }

            const ids = flow();
            const target = ids.indexOf(d.stepId) !== -1 && canGoTo(d.stepId) ? d.stepId : (state.verified ? 'regulatory' : 'verify');

            openWizard();
            showStep(target, { silent: true });
            return true;
        } catch (e) {
            return false;
        }
    }

    /* =====================================================
       NETWORK HELPERS
    ===================================================== */

    async function readJson(response) {
        const type = response.headers.get('content-type') || '';
        if (type.indexOf('application/json') !== -1) {
            try { return await response.json(); } catch (e) { return {}; }
        }
        return { message: '' };
    }

    function errorText(data, fallback, status) {
        if (data && data.errors) {
            return Object.keys(data.errors).map(function (k) { return [].concat(data.errors[k]).join(' '); }).join(' ');
        }

        const message = data && data.message ? String(data.message) : '';
        const normalized = message.trim().toLowerCase().replace(/[.!]+$/, '');

        if (status === 401 || normalized === 'unauthenticated') {
            return 'Your sign-in session has expired. Reload the page, sign in again, and continue your registration. Your details on this device are still saved.';
        }

        if (status === 419 || normalized.indexOf('csrf token mismatch') !== -1) {
            return 'This page has been open for too long and its security token expired. Reload the page, then try again. Your draft details are still saved.';
        }

        if (status === 413) {
            return 'One or more uploaded files are too large for the server. Use files smaller than 5 MB each, then try again.';
        }

        if (status === 429) {
            return 'Too many requests were sent in a short time. Wait one minute, then try again.';
        }

        if (status >= 500) {
            return message && normalized !== 'server error'
                ? message
                : 'AfyaLink encountered a server error while processing this step. Your draft is still saved; wait a moment and try again.';
        }

        return message || fallback;
    }

    /* =====================================================
       UNLOAD GUARD + EXIT
    ===================================================== */

    window.addEventListener('beforeunload', function (e) {
        if (!state.dirty || state.submitted || wizardScreen.classList.contains('hidden')) return;
        e.preventDefault();
        e.returnValue = '';
    });

    /* =====================================================
       INIT
    ===================================================== */

    const restored = loadDraft();

    if (EMAIL_JUST_VERIFIED) {
        // the user clicked the link in their email (possibly on another device)
        state.accountCreated = true;
        state.verified = true;
        openWizard();

        if (state.type) {
            showStep('regulatory', { silent: true });
        } else {
            state.needsType = true;
            showStep('type', { silent: true });
            setMessage('success', 'Your email is verified. Confirm how you are registering to continue.');
        }

        if (state.type) setMessage('success', 'Your email has been verified successfully.');
    } else if (!restored) {
        showStep('type', { silent: true });
    }

    updatePasswordRules();
    updateSnapshot();
    renderIcons();
});
</script>

</body>
</html>
