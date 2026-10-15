<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Facility Dashboard | AfyaLink Uganda
    </title>


    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <script
        src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"
        defer
    ></script>


    <style>

        :root {

            --red: #D32F2F;
            --red-dark: #B71C1C;
            --red-soft: #FFF1F1;

            --green: #168A55;
            --green-dark: #0D6B3F;
            --green-soft: #ECF9F1;

            --dark: #172033;
            --text: #64748B;
            --muted: #94A3B8;

            --border: #E8EDF3;
            --surface: #F7F9FC;
            --white: #FFFFFF;

            --sidebar-width: 245px;

            --shadow:
                0 10px 32px
                rgba(23, 32, 51, .06);

        }


        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {
            font-family: 'Poppins', sans-serif;
            background: var(--surface);
            color: var(--dark);
        }


        a {
            color: inherit;
            text-decoration: none;
        }


        button,
        input {
            font: inherit;
        }


        .dashboard-shell {
            min-height: 100vh;
            display: flex;
        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {

            width: var(--sidebar-width);
            min-height: 100vh;

            position: fixed;
            left: 0;
            top: 0;

            padding: 22px 16px;

            background: white;

            border-right:
                1px solid
                var(--border);

            display: flex;
            flex-direction: column;

            z-index: 30;
        }


        .brand {

            display: flex;
            align-items: center;
            gap: 11px;

            padding: 6px 8px 24px;
        }


        .brand-icon {

            width: 43px;
            height: 43px;

            border-radius: 13px;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--red-dark)
                );

            color: white;

            display: flex;
            align-items: center;
            justify-content: center;
        }


        .brand-title {

            font-size: 18px;
            font-weight: 800;
        }


        .brand-subtitle {

            margin-top: 3px;

            font-size: 7px;
            font-weight: 800;

            color: var(--green);

            letter-spacing: .18em;
        }


        .sidebar-section-title {

            margin:
                16px
                10px
                8px;

            font-size: 7px;

            color: var(--muted);

            font-weight: 800;

            letter-spacing: .13em;
        }


        .nav-menu {

            display: grid;

            gap: 5px;
        }


        .nav-link {

            min-height: 43px;

            padding: 0 12px;

            display: flex;
            align-items: center;

            gap: 10px;

            border-radius: 11px;

            font-size: 9.5px;
            font-weight: 600;

            color: #475569;

            transition:
                background .2s,
                color .2s;
        }


        .nav-link:hover {

            background: var(--surface);
            color: var(--red);
        }


        .nav-link.active {

            color: var(--red);

            background: var(--red-soft);
        }


        .nav-link svg {
            flex-shrink: 0;
        }


        .sidebar-bottom {

            margin-top: auto;

            padding-top: 20px;
        }


        .support-card {

            padding: 15px;

            border-radius: 16px;

            background:
                linear-gradient(
                    145deg,
                    var(--green-soft),
                    #FFFFFF
                );

            border:
                1px solid
                #DDEEE5;
        }


        .support-card h4 {

            font-size: 9px;
            font-weight: 800;
        }


        .support-card p {

            margin-top: 5px;

            font-size: 7px;
            line-height: 1.6;

            color: var(--text);
        }


        /* =====================================================
           MAIN
        ===================================================== */

        .main {

            flex: 1;

            margin-left:
                var(--sidebar-width);

            min-width: 0;
        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {

            height: 76px;

            padding: 0 28px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 20px;

            background: white;

            border-bottom:
                1px solid
                var(--border);

            position: sticky;
            top: 0;

            z-index: 20;
        }


        .mobile-menu-button {

            display: none;

            width: 40px;
            height: 40px;

            border:
                1px solid
                var(--border);

            border-radius: 10px;

            background: white;

            align-items: center;
            justify-content: center;
        }


        .topbar-left h1 {

            font-size: 15px;
            font-weight: 800;
        }


        .topbar-left p {

            margin-top: 3px;

            font-size: 7.5px;

            color: var(--muted);
        }


        .topbar-actions {

            display: flex;
            align-items: center;
            gap: 10px;
        }


        .icon-button {

            width: 40px;
            height: 40px;

            border-radius: 11px;

            border:
                1px solid
                var(--border);

            background: white;

            color: var(--dark);

            display: flex;
            align-items: center;
            justify-content: center;
        }


        .profile-menu {

            min-width: 190px;

            padding: 7px 9px;

            display: flex;
            align-items: center;

            gap: 9px;

            border:
                1px solid
                var(--border);

            border-radius: 12px;

            background: white;
        }


        .profile-avatar {

            width: 36px;
            height: 36px;

            border-radius: 50%;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--green)
                );

            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 9px;
            font-weight: 800;

            flex-shrink: 0;
        }


        .profile-name {

            font-size: 8px;
            font-weight: 800;
        }


        .profile-role {

            margin-top: 2px;

            font-size: 6.5px;

            color: var(--muted);
        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .content {

            padding:
                28px
                28px
                60px;

            max-width: 1500px;

            margin-inline: auto;
        }


        .welcome {

            margin-bottom: 23px;

            display: flex;
            align-items: flex-end;
            justify-content: space-between;

            gap: 20px;
        }


        .welcome h2 {

            font-size:
                clamp(
                    24px,
                    3vw,
                    34px
                );

            line-height: 1.18;

            font-weight: 800;

            letter-spacing: -.03em;
        }


        .welcome h2 span {

            color: var(--red);
        }


        .welcome p {

            margin-top: 7px;

            max-width: 650px;

            font-size: 9px;
            line-height: 1.7;

            color: var(--text);
        }


        .verification-badge {

            padding: 9px 12px;

            border-radius: 999px;

            display: inline-flex;
            align-items: center;

            gap: 6px;

            background: var(--green-soft);

            color: var(--green-dark);

            font-size: 7px;
            font-weight: 800;

            white-space: nowrap;
        }


        /* =====================================================
           STATS GRID
        ===================================================== */

        .stats-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 14px;

            margin-bottom: 17px;
        }


        .stat-card {

            min-height: 135px;

            padding: 18px;

            border:
                1px solid
                var(--border);

            border-radius: 18px;

            background: white;

            box-shadow:
                0 6px 18px
                rgba(
                    23,
                    32,
                    51,
                    .025
                );
        }


        .stat-head {

            display: flex;
            align-items: center;
            justify-content: space-between;
        }


        .stat-icon {

            width: 37px;
            height: 37px;

            border-radius: 11px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: var(--red-soft);

            color: var(--red);
        }


        .stat-card:nth-child(2) .stat-icon,
        .stat-card:nth-child(4) .stat-icon {

            background: var(--green-soft);

            color: var(--green);
        }


        .stat-label {

            font-size: 7.5px;

            color: var(--muted);

            font-weight: 700;
        }


        .stat-value {

            margin-top: 18px;

            font-size: 24px;

            font-weight: 800;

            letter-spacing: -.03em;
        }


        .stat-note {

            margin-top: 3px;

            font-size: 7px;

            color: var(--text);
        }


        /* =====================================================
           MAIN GRID
        ===================================================== */

        .dashboard-grid {

            display: grid;

            grid-template-columns:
                minmax(0, 1.55fr)
                minmax(280px, .75fr);

            gap: 16px;
        }


        .dashboard-column {

            display: grid;

            gap: 16px;
        }


        .card {

            background: white;

            border:
                1px solid
                var(--border);

            border-radius: 19px;

            box-shadow:
                var(--shadow);
        }


        .card-header {

            padding: 17px 18px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 14px;

            border-bottom:
                1px solid
                var(--border);
        }


        .card-title {

            font-size: 10px;

            font-weight: 800;
        }


        .card-subtitle {

            margin-top: 3px;

            color: var(--muted);

            font-size: 6.8px;
        }


        .card-link {

            color: var(--red);

            font-size: 7px;

            font-weight: 700;
        }


        /* =====================================================
           APPOINTMENTS
        ===================================================== */

        .appointment-list {

            padding: 8px 18px 14px;
        }


        .appointment-row {

            min-height: 67px;

            display: grid;

            grid-template-columns:
                70px
                1fr
                auto;

            align-items: center;

            gap: 12px;

            border-bottom:
                1px solid
                var(--border);
        }


        .appointment-row:last-child {
            border-bottom: none;
        }


        .appointment-time {

            font-size: 8px;

            font-weight: 800;

            color: var(--red);
        }


        .appointment-name {

            font-size: 8.5px;
            font-weight: 700;
        }


        .appointment-service {

            margin-top: 3px;

            font-size: 6.7px;

            color: var(--muted);
        }


        .appointment-status {

            padding: 6px 8px;

            border-radius: 999px;

            font-size: 6px;

            font-weight: 800;

            background: var(--green-soft);

            color: var(--green-dark);
        }


        /* =====================================================
           PROFILE STATUS
        ===================================================== */

        .profile-status {

            padding: 19px;
        }


        .profile-score {

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 20px;
        }


        .progress-ring {

            width: 90px;
            height: 90px;

            border-radius: 50%;

            background:
                conic-gradient(
                    var(--green) 0 82%,
                    #E7EBF0 82% 100%
                );

            display: flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;
        }


        .progress-inner {

            width: 68px;
            height: 68px;

            border-radius: 50%;

            background: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 15px;
            font-weight: 800;
        }


        .profile-status h3 {

            font-size: 10px;
            font-weight: 800;
        }


        .profile-status p {

            margin-top: 5px;

            font-size: 7px;
            line-height: 1.6;

            color: var(--text);
        }


        /* =====================================================
           RATING
        ===================================================== */

        .rating-card {

            padding: 18px;
        }


        .rating-main {

            display: flex;
            align-items: center;
            gap: 15px;
        }


        .rating-number {

            font-size: 31px;
            font-weight: 800;
        }


        .stars {

            color: #F59E0B;

            display: flex;
            gap: 2px;
        }


        .rating-count {

            margin-top: 3px;

            font-size: 6.8px;
            color: var(--muted);
        }


        /* =====================================================
           QUICK ACTIONS
        ===================================================== */

        .quick-grid {

            padding: 14px;

            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(0, 1fr)
                );

            gap: 10px;
        }


        .quick-action {

            min-height: 92px;

            padding: 13px;

            border:
                1px solid
                var(--border);

            border-radius: 14px;

            background: white;

            transition:
                transform .2s,
                border-color .2s;
        }


        .quick-action:hover {

            transform:
                translateY(-2px);

            border-color:
                #D7DEE7;
        }


        .quick-icon {

            width: 33px;
            height: 33px;

            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: var(--red);

            background: var(--red-soft);
        }


        .quick-action:nth-child(even)
        .quick-icon {

            color: var(--green);

            background: var(--green-soft);
        }


        .quick-title {

            margin-top: 10px;

            font-size: 8px;
            font-weight: 800;
        }


        .quick-text {

            margin-top: 3px;

            font-size: 6.5px;
            line-height: 1.5;

            color: var(--muted);
        }


        /* =====================================================
           REVIEWS
        ===================================================== */

        .review-list {

            padding: 10px 18px 16px;
        }


        .review-item {

            padding: 13px 0;

            border-bottom:
                1px solid
                var(--border);
        }


        .review-item:last-child {
            border-bottom: none;
        }


        .review-head {

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;
        }


        .review-user {

            font-size: 8px;
            font-weight: 700;
        }


        .review-date {

            color: var(--muted);

            font-size: 6px;
        }


        .review-stars {

            margin-top: 5px;

            display: flex;
            gap: 1px;

            color: #F59E0B;
        }


        .review-text {

            margin-top: 6px;

            font-size: 7px;
            line-height: 1.65;

            color: var(--text);
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1150px) {

            .stats-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }

        }


        @media (max-width: 950px) {

            .sidebar {

                transform:
                    translateX(-100%);

                transition:
                    transform .25s;
            }


            .sidebar.open {

                transform:
                    translateX(0);
            }


            .main {

                margin-left: 0;
            }


            .mobile-menu-button {

                display: flex;
            }


            .dashboard-grid {

                grid-template-columns:
                    1fr;
            }

        }


        @media (max-width: 650px) {

            .topbar {

                padding: 0 15px;
            }


            .topbar-left {

                display: none;
            }


            .profile-menu {

                min-width: auto;
            }


            .profile-menu-info {

                display: none;
            }


            .content {

                padding:
                    20px
                    15px
                    45px;
            }


            .welcome {

                align-items: flex-start;
                flex-direction: column;
            }


            .stats-grid {

                grid-template-columns: 1fr;
            }


            .quick-grid {

                grid-template-columns: 1fr;
            }


            .appointment-row {

                grid-template-columns:
                    62px
                    1fr;
            }


            .appointment-status {

                grid-column:
                    2;
            }

        }

    </style>

</head>


<body>


<div class="dashboard-shell">


    {{-- ===================================================== --}}
    {{-- SIDEBAR --}}
    {{-- ===================================================== --}}

    <aside
        class="sidebar"
        id="sidebar"
    >


        <a
            href="{{ route('home') }}"
            class="brand"
        >

            <div class="brand-icon">

                <i
                    data-lucide="heart-pulse"
                    width="21"
                    height="21"
                ></i>

            </div>


            <div>

                <div class="brand-title">
                    AfyaLink
                </div>

                <div class="brand-subtitle">
                    UGANDA
                </div>

            </div>

        </a>



        <div class="sidebar-section-title">
            FACILITY
        </div>


        <nav class="nav-menu">


            <a
                href="#"
                class="nav-link active"
            >

                <i
                    data-lucide="layout-dashboard"
                    width="16"
                    height="16"
                ></i>

                Overview

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i
                    data-lucide="calendar-days"
                    width="16"
                    height="16"
                ></i>

                Appointments

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i
                    data-lucide="users"
                    width="16"
                    height="16"
                ></i>

                Patients

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i
                    data-lucide="stethoscope"
                    width="16"
                    height="16"
                ></i>

                Health Workers

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i
                    data-lucide="briefcase-medical"
                    width="16"
                    height="16"
                ></i>

                Services

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i
                    data-lucide="star"
                    width="16"
                    height="16"
                ></i>

                Reviews & Ratings

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i
                    data-lucide="building-2"
                    width="16"
                    height="16"
                ></i>

                Facility Profile

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i
                    data-lucide="clock-3"
                    width="16"
                    height="16"
                ></i>

                Opening Hours

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i
                    data-lucide="settings"
                    width="16"
                    height="16"
                ></i>

                Settings

            </a>


        </nav>



        <div class="sidebar-bottom">


            <div class="support-card">

                <h4>
                    Need help?
                </h4>

                <p>
                    Contact AfyaLink support if you need help
                    updating or verifying your facility profile.
                </p>

            </div>


        </div>


    </aside>



    {{-- ===================================================== --}}
    {{-- MAIN --}}
    {{-- ===================================================== --}}

    <main class="main">


        {{-- TOP BAR --}}

        <header class="topbar">


            <div
                style="
                    display:flex;
                    align-items:center;
                    gap:12px;
                "
            >

                <button
                    type="button"
                    class="mobile-menu-button"
                    id="mobileMenuButton"
                >

                    <i
                        data-lucide="menu"
                        width="18"
                        height="18"
                    ></i>

                </button>


                <div class="topbar-left">

                    <h1>
                        Facility Dashboard
                    </h1>

                    <p>
                        Manage your AfyaLink facility account
                    </p>

                </div>

            </div>



            <div class="topbar-actions">


                <button
                    type="button"
                    class="icon-button"
                >

                    <i
                        data-lucide="bell"
                        width="17"
                        height="17"
                    ></i>

                </button>


                <div class="profile-menu">


                    <div class="profile-avatar">

                        {{ strtoupper(substr($application?->facility_name ?? 'AF', 0, 2)) }}

                    </div>


                    <div class="profile-menu-info">

                        <div class="profile-name">

                            {{ $application?->facility_name ?? $user->name }}

                        </div>

                        <div class="profile-role">
                            Healthcare Facility
                        </div>

                    </div>


                </div>


            </div>


        </header>



        {{-- CONTENT --}}

        <section class="content">


            <div class="welcome">


                <div>

                    <h2>

                        Welcome back,

                        <span>
                            {{ $application?->facility_name ?? $user->name }}
                        </span>

                    </h2>


                    <p>

                        Manage your facility profile, appointments,
                        patient activity, services and reputation
                        from one AfyaLink workspace.

                    </p>

                </div>


                <div class="verification-badge">

                    <i
                        data-lucide="badge-check"
                        width="14"
                        height="14"
                    ></i>

                    {{ ucfirst($user->registration_status ?? 'verified') }}

                </div>


            </div>



            {{-- ================================================= --}}
            {{-- STATS --}}
            {{-- ================================================= --}}

            <div class="stats-grid">


                <div class="stat-card">

                    <div class="stat-head">

                        <div class="stat-icon">

                            <i
                                data-lucide="calendar-check-2"
                                width="17"
                                height="17"
                            ></i>

                        </div>

                        <div class="stat-label">
                            APPOINTMENTS
                        </div>

                    </div>


                    <div class="stat-value">
                        24
                    </div>

                    <div class="stat-note">
                        Total this month
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-head">

                        <div class="stat-icon">

                            <i
                                data-lucide="calendar-clock"
                                width="17"
                                height="17"
                            ></i>

                        </div>

                        <div class="stat-label">
                            TODAY
                        </div>

                    </div>


                    <div class="stat-value">
                        6
                    </div>

                    <div class="stat-note">
                        Appointments scheduled
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-head">

                        <div class="stat-icon">

                            <i
                                data-lucide="users"
                                width="17"
                                height="17"
                            ></i>

                        </div>

                        <div class="stat-label">
                            PATIENTS
                        </div>

                    </div>


                    <div class="stat-value">
                        113
                    </div>

                    <div class="stat-note">
                        Patients served
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-head">

                        <div class="stat-icon">

                            <i
                                data-lucide="star"
                                width="17"
                                height="17"
                            ></i>

                        </div>

                        <div class="stat-label">
                            RATING
                        </div>

                    </div>


                    <div class="stat-value">
                        4.7
                    </div>

                    <div class="stat-note">
                        From 38 reviews
                    </div>

                </div>


            </div>



            {{-- ================================================= --}}
            {{-- MAIN GRID --}}
            {{-- ================================================= --}}

            <div class="dashboard-grid">


                {{-- LEFT COLUMN --}}

                <div class="dashboard-column">


                    <div class="card">


                        <div class="card-header">

                            <div>

                                <div class="card-title">
                                    Today's Appointments
                                </div>

                                <div class="card-subtitle">
                                    Upcoming patient visits
                                </div>

                            </div>


                            <a
                                href="#"
                                class="card-link"
                            >
                                View all
                            </a>

                        </div>



                        <div class="appointment-list">


                            <div class="appointment-row">

                                <div class="appointment-time">
                                    08:30 AM
                                </div>

                                <div>

                                    <div class="appointment-name">
                                        Sarah Nakato
                                    </div>

                                    <div class="appointment-service">
                                        General Consultation
                                    </div>

                                </div>

                                <div class="appointment-status">
                                    Confirmed
                                </div>

                            </div>


                            <div class="appointment-row">

                                <div class="appointment-time">
                                    10:00 AM
                                </div>

                                <div>

                                    <div class="appointment-name">
                                        Brian Mugisha
                                    </div>

                                    <div class="appointment-service">
                                        Laboratory Services
                                    </div>

                                </div>

                                <div class="appointment-status">
                                    Confirmed
                                </div>

                            </div>


                            <div class="appointment-row">

                                <div class="appointment-time">
                                    11:30 AM
                                </div>

                                <div>

                                    <div class="appointment-name">
                                        Joan Namusoke
                                    </div>

                                    <div class="appointment-service">
                                        Maternal Health
                                    </div>

                                </div>

                                <div class="appointment-status">
                                    Pending
                                </div>

                            </div>


                        </div>


                    </div>



                    <div class="card">


                        <div class="card-header">

                            <div>

                                <div class="card-title">
                                    Recent Reviews
                                </div>

                                <div class="card-subtitle">
                                    What patients are saying
                                </div>

                            </div>


                            <a
                                href="#"
                                class="card-link"
                            >
                                View all
                            </a>

                        </div>



                        <div class="review-list">


                            <div class="review-item">


                                <div class="review-head">

                                    <div class="review-user">
                                        Patient Review
                                    </div>

                                    <div class="review-date">
                                        2 days ago
                                    </div>

                                </div>


                                <div class="review-stars">

                                    @for ($i = 0; $i < 5; $i++)

                                        <i
                                            data-lucide="star"
                                            width="11"
                                            height="11"
                                            fill="currentColor"
                                        ></i>

                                    @endfor

                                </div>


                                <div class="review-text">

                                    The staff were professional and the
                                    service was handled efficiently.

                                </div>


                            </div>


                            <div class="review-item">


                                <div class="review-head">

                                    <div class="review-user">
                                        Patient Review
                                    </div>

                                    <div class="review-date">
                                        5 days ago
                                    </div>

                                </div>


                                <div class="review-stars">

                                    @for ($i = 0; $i < 4; $i++)

                                        <i
                                            data-lucide="star"
                                            width="11"
                                            height="11"
                                            fill="currentColor"
                                        ></i>

                                    @endfor

                                </div>


                                <div class="review-text">

                                    Easy booking process and a clean
                                    facility environment.

                                </div>


                            </div>


                        </div>


                    </div>


                </div>



                {{-- RIGHT COLUMN --}}

                <div class="dashboard-column">


                    <div class="card">


                        <div class="card-header">

                            <div>

                                <div class="card-title">
                                    Profile Completion
                                </div>

                                <div class="card-subtitle">
                                    Keep your profile updated
                                </div>

                            </div>

                        </div>



                        <div class="profile-status">


                            <div class="profile-score">


                                <div class="progress-ring">

                                    <div class="progress-inner">
                                        82%
                                    </div>

                                </div>


                                <div>

                                    <h3>
                                        Almost complete
                                    </h3>

                                    <p>

                                        Add more facility details and services
                                        to improve your public profile.

                                    </p>

                                </div>


                            </div>


                        </div>


                    </div>



                    <div class="card">


                        <div class="card-header">

                            <div>

                                <div class="card-title">
                                    Rating Overview
                                </div>

                                <div class="card-subtitle">
                                    Patient feedback
                                </div>

                            </div>

                        </div>



                        <div class="rating-card">


                            <div class="rating-main">


                                <div class="rating-number">
                                    4.7
                                </div>


                                <div>

                                    <div class="stars">

                                        @for ($i = 0; $i < 5; $i++)

                                            <i
                                                data-lucide="star"
                                                width="14"
                                                height="14"
                                                fill="currentColor"
                                            ></i>

                                        @endfor

                                    </div>


                                    <div class="rating-count">
                                        38 patient reviews
                                    </div>

                                </div>


                            </div>


                        </div>


                    </div>



                    <div class="card">


                        <div class="card-header">

                            <div>

                                <div class="card-title">
                                    Quick Actions
                                </div>

                                <div class="card-subtitle">
                                    Common facility tasks
                                </div>

                            </div>

                        </div>



                        <div class="quick-grid">


                            <a
                                href="#"
                                class="quick-action"
                            >

                                <div class="quick-icon">

                                    <i
                                        data-lucide="pencil"
                                        width="15"
                                        height="15"
                                    ></i>

                                </div>

                                <div class="quick-title">
                                    Edit Profile
                                </div>

                                <div class="quick-text">
                                    Update facility information
                                </div>

                            </a>


                            <a
                                href="#"
                                class="quick-action"
                            >

                                <div class="quick-icon">

                                    <i
                                        data-lucide="plus"
                                        width="15"
                                        height="15"
                                    ></i>

                                </div>

                                <div class="quick-title">
                                    Add Service
                                </div>

                                <div class="quick-text">
                                    Add a new healthcare service
                                </div>

                            </a>


                            <a
                                href="#"
                                class="quick-action"
                            >

                                <div class="quick-icon">

                                    <i
                                        data-lucide="user-plus"
                                        width="15"
                                        height="15"
                                    ></i>

                                </div>

                                <div class="quick-title">
                                    Add Worker
                                </div>

                                <div class="quick-text">
                                    Link a health professional
                                </div>

                            </a>


                            <a
                                href="#"
                                class="quick-action"
                            >

                                <div class="quick-icon">

                                    <i
                                        data-lucide="eye"
                                        width="15"
                                        height="15"
                                    ></i>

                                </div>

                                <div class="quick-title">
                                    Public Profile
                                </div>

                                <div class="quick-text">
                                    View your AfyaLink listing
                                </div>

                            </a>


                        </div>


                    </div>


                </div>


            </div>


        </section>


    </main>


</div>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        if (window.lucide) {
            lucide.createIcons();
        }


        const button =
            document.getElementById(
                'mobileMenuButton'
            );


        const sidebar =
            document.getElementById(
                'sidebar'
            );


        button?.addEventListener(
            'click',
            function () {

                sidebar.classList.toggle(
                    'open'
                );

            }
        );


        document.addEventListener(
            'click',
            function (event) {

                if (
                    window.innerWidth <= 950
                    &&
                    sidebar.classList.contains(
                        'open'
                    )
                    &&
                    !sidebar.contains(
                        event.target
                    )
                    &&
                    !button.contains(
                        event.target
                    )
                ) {

                    sidebar.classList.remove(
                        'open'
                    );

                }

            }
        );

    }
);

</script>


</body>
</html>