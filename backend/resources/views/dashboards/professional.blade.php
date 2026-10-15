<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Professional Dashboard | AfyaLink Uganda
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


        button {
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


        .sidebar-bottom {

            margin-top: auto;
        }


        .workplace-card {

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


        .workplace-card span {

            color: var(--green-dark);

            font-size: 6.5px;
            font-weight: 800;

            letter-spacing: .08em;
        }


        .workplace-card h4 {

            margin-top: 6px;

            font-size: 8.5px;
            font-weight: 800;
        }


        .workplace-card p {

            margin-top: 4px;

            font-size: 6.8px;
            line-height: 1.5;

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

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--green)
                );

            color: white;

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


        .verified-badge {

            padding: 9px 12px;

            display: inline-flex;
            align-items: center;

            gap: 6px;

            border-radius: 999px;

            color: var(--green-dark);

            background: var(--green-soft);

            font-size: 7px;

            font-weight: 800;
        }


        /* =====================================================
           STATS
        ===================================================== */

        .stats-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
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
        }


        .stat-head {

            display: flex;

            align-items: center;

            justify-content:
                space-between;
        }


        .stat-icon {

            width: 37px;
            height: 37px;

            border-radius: 11px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: var(--red);

            background: var(--red-soft);
        }


        .stat-card:nth-child(even)
        .stat-icon {

            color: var(--green);

            background: var(--green-soft);
        }


        .stat-label {

            font-size: 7px;

            font-weight: 700;

            color: var(--muted);
        }


        .stat-value {

            margin-top: 18px;

            font-size: 24px;

            font-weight: 800;
        }


        .stat-note {

            margin-top: 3px;

            font-size: 7px;

            color: var(--text);
        }


        /* =====================================================
           DASHBOARD GRID
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

            border:
                1px solid
                var(--border);

            border-radius: 19px;

            background: white;

            box-shadow: var(--shadow);
        }


        .card-header {

            padding: 17px 18px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;

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

            font-size: 6.8px;

            color: var(--muted);
        }


        .card-link {

            color: var(--red);

            font-size: 7px;

            font-weight: 700;
        }


        /* =====================================================
           SCHEDULE
        ===================================================== */

        .schedule-list {

            padding: 8px 18px 14px;
        }


        .schedule-row {

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


        .schedule-row:last-child {
            border-bottom: 0;
        }


        .schedule-time {

            color: var(--red);

            font-size: 8px;

            font-weight: 800;
        }


        .schedule-name {

            font-size: 8.5px;

            font-weight: 700;
        }


        .schedule-service {

            margin-top: 3px;

            color: var(--muted);

            font-size: 6.7px;
        }


        .schedule-status {

            padding: 6px 8px;

            border-radius: 999px;

            background: var(--green-soft);

            color: var(--green-dark);

            font-size: 6px;

            font-weight: 800;
        }


        /* =====================================================
           AVAILABILITY
        ===================================================== */

        .availability {

            padding: 18px;
        }


        .availability-status {

            display: flex;
            align-items: center;

            gap: 12px;
        }


        .availability-icon {

            width: 44px;
            height: 44px;

            border-radius: 13px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: var(--green);

            background: var(--green-soft);
        }


        .availability h3 {

            font-size: 9px;

            font-weight: 800;
        }


        .availability p {

            margin-top: 4px;

            color: var(--text);

            font-size: 7px;

            line-height: 1.5;
        }


        .availability-days {

            margin-top: 16px;

            display: grid;

            grid-template-columns:
                repeat(
                    7,
                    1fr
                );

            gap: 5px;
        }


        .day {

            min-height: 39px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            border:
                1px solid
                var(--border);

            font-size: 6.5px;

            font-weight: 700;

            color: var(--muted);
        }


        .day.active {

            color: var(--green-dark);

            background: var(--green-soft);

            border-color: #D5EDDF;
        }


        /* =====================================================
           WORKPLACE
        ===================================================== */

        .workplace {

            padding: 18px;
        }


        .workplace-main {

            display: flex;

            align-items: center;

            gap: 12px;
        }


        .workplace-icon {

            width: 45px;
            height: 45px;

            border-radius: 13px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: var(--red);

            background: var(--red-soft);
        }


        .workplace-name {

            font-size: 9px;

            font-weight: 800;
        }


        .workplace-location {

            margin-top: 3px;

            color: var(--muted);

            font-size: 6.8px;
        }


        /* =====================================================
           QUALIFICATIONS
        ===================================================== */

        .qualification-list {

            padding: 10px 18px 15px;
        }


        .qualification-row {

            padding: 12px 0;

            display: flex;

            gap: 11px;

            border-bottom:
                1px solid
                var(--border);
        }


        .qualification-row:last-child {
            border-bottom: 0;
        }


        .qualification-icon {

            width: 34px;
            height: 34px;

            flex-shrink: 0;

            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: var(--green);

            background: var(--green-soft);
        }


        .qualification-title {

            font-size: 8px;

            font-weight: 800;
        }


        .qualification-meta {

            margin-top: 3px;

            font-size: 6.7px;

            color: var(--muted);

            line-height: 1.5;
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
            border-bottom: 0;
        }


        .review-head {

            display: flex;

            justify-content:
                space-between;

            gap: 10px;
        }


        .review-user {

            font-size: 8px;

            font-weight: 700;
        }


        .review-date {

            font-size: 6px;

            color: var(--muted);
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

            line-height: 1.6;

            color: var(--text);
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

            min-height: 91px;

            padding: 13px;

            border:
                1px solid
                var(--border);

            border-radius: 14px;

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

            color: var(--muted);

            font-size: 6.5px;

            line-height: 1.5;
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

                flex-direction: column;

                align-items: flex-start;
            }


            .stats-grid {

                grid-template-columns: 1fr;
            }


            .quick-grid {

                grid-template-columns: 1fr;
            }


            .schedule-row {

                grid-template-columns:
                    62px
                    1fr;
            }


            .schedule-status {

                grid-column: 2;
            }

        }

    </style>

</head>


<body>


<div class="dashboard-shell">


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
            PROFESSIONAL
        </div>


        <nav class="nav-menu">


            <a href="#" class="nav-link active">

                <i
                    data-lucide="layout-dashboard"
                    width="16"
                    height="16"
                ></i>

                Overview

            </a>


            <a href="#" class="nav-link">

                <i
                    data-lucide="calendar-days"
                    width="16"
                    height="16"
                ></i>

                Appointments

            </a>


            <a href="#" class="nav-link">

                <i
                    data-lucide="users"
                    width="16"
                    height="16"
                ></i>

                Patients

            </a>


            <a href="#" class="nav-link">

                <i
                    data-lucide="calendar-clock"
                    width="16"
                    height="16"
                ></i>

                Availability

            </a>


            <a href="#" class="nav-link">

                <i
                    data-lucide="star"
                    width="16"
                    height="16"
                ></i>

                Reviews & Ratings

            </a>


            <a href="#" class="nav-link">

                <i
                    data-lucide="user-round"
                    width="16"
                    height="16"
                ></i>

                Professional Profile

            </a>


            <a href="#" class="nav-link">

                <i
                    data-lucide="hospital"
                    width="16"
                    height="16"
                ></i>

                Workplace

            </a>


            <a href="#" class="nav-link">

                <i
                    data-lucide="graduation-cap"
                    width="16"
                    height="16"
                ></i>

                Qualifications

            </a>


            <a href="#" class="nav-link">

                <i
                    data-lucide="settings"
                    width="16"
                    height="16"
                ></i>

                Settings

            </a>


        </nav>



        <div class="sidebar-bottom">


            <div class="workplace-card">

                <span>
                    CURRENT WORKPLACE
                </span>

                <h4>
                    Kampala Family Medical Centre
                </h4>

                <p>
                    Verified AfyaLink healthcare facility
                </p>

            </div>


        </div>


    </aside>



    <main class="main">


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
                        Professional Dashboard
                    </h1>

                    <p>
                        Manage your practice and AfyaLink profile
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

                        {{
                            strtoupper(
                                substr(
                                    $application?->first_name ?? 'A',
                                    0,
                                    1
                                )
                                .
                                substr(
                                    $application?->last_name ?? 'L',
                                    0,
                                    1
                                )
                            )
                        }}

                    </div>


                    <div class="profile-menu-info">

                        <div class="profile-name">

                            {{
                                collect([
                                    $application?->first_name,
                                    $application?->middle_name,
                                    $application?->last_name
                                ])
                                ->filter()
                                ->implode(' ')
                                ?: $user->name
                            }}

                        </div>

                        <div class="profile-role">

                            {{ $application?->profession ?? 'Health Professional' }}

                        </div>

                    </div>


                </div>


            </div>


        </header>



        <section class="content">


            <div class="welcome">


                <div>

                    <h2>

                        Welcome back,

                        <span>

                            {{
                                $application?->first_name
                                ?? $user->name
                            }}

                        </span>

                    </h2>


                    <p>

                        Manage appointments, patients, availability,
                        professional details and your AfyaLink reputation.

                    </p>

                </div>


                <div class="verified-badge">

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
                                data-lucide="calendar-check"
                                width="17"
                                height="17"
                            ></i>

                        </div>

                        <div class="stat-label">
                            TODAY
                        </div>

                    </div>


                    <div class="stat-value">
                        5
                    </div>

                    <div class="stat-note">
                        Appointments today
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-head">

                        <div class="stat-icon">

                            <i
                                data-lucide="calendar-days"
                                width="17"
                                height="17"
                            ></i>

                        </div>

                        <div class="stat-label">
                            UPCOMING
                        </div>

                    </div>


                    <div class="stat-value">
                        14
                    </div>

                    <div class="stat-note">
                        Upcoming bookings
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-head">

                        <div class="stat-icon">

                            <i
                                data-lucide="users-round"
                                width="17"
                                height="17"
                            ></i>

                        </div>

                        <div class="stat-label">
                            PATIENTS
                        </div>

                    </div>


                    <div class="stat-value">
                        86
                    </div>

                    <div class="stat-note">
                        Patients seen
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
                        4.8
                    </div>

                    <div class="stat-note">
                        From 26 reviews
                    </div>

                </div>


            </div>



            <div class="dashboard-grid">


                {{-- LEFT COLUMN --}}

                <div class="dashboard-column">


                    <div class="card">


                        <div class="card-header">

                            <div>

                                <div class="card-title">
                                    Today's Schedule
                                </div>

                                <div class="card-subtitle">
                                    Your upcoming patient appointments
                                </div>

                            </div>


                            <a href="#" class="card-link">
                                View all
                            </a>

                        </div>



                        <div class="schedule-list">


                            <div class="schedule-row">

                                <div class="schedule-time">
                                    09:00 AM
                                </div>

                                <div>

                                    <div class="schedule-name">
                                        Miriam Nakiwala
                                    </div>

                                    <div class="schedule-service">
                                        General Consultation
                                    </div>

                                </div>

                                <div class="schedule-status">
                                    Confirmed
                                </div>

                            </div>


                            <div class="schedule-row">

                                <div class="schedule-time">
                                    10:30 AM
                                </div>

                                <div>

                                    <div class="schedule-name">
                                        Joseph Kato
                                    </div>

                                    <div class="schedule-service">
                                        Follow-up Consultation
                                    </div>

                                </div>

                                <div class="schedule-status">
                                    Confirmed
                                </div>

                            </div>


                            <div class="schedule-row">

                                <div class="schedule-time">
                                    01:00 PM
                                </div>

                                <div>

                                    <div class="schedule-name">
                                        Ruth Namutebi
                                    </div>

                                    <div class="schedule-service">
                                        Medical Consultation
                                    </div>

                                </div>

                                <div class="schedule-status">
                                    Pending
                                </div>

                            </div>


                        </div>


                    </div>



                    <div class="card">


                        <div class="card-header">

                            <div>

                                <div class="card-title">
                                    Qualifications
                                </div>

                                <div class="card-subtitle">
                                    Professional credentials
                                </div>

                            </div>


                            <a href="#" class="card-link">
                                Manage
                            </a>

                        </div>



                        <div class="qualification-list">


                            <div class="qualification-row">

                                <div class="qualification-icon">

                                    <i
                                        data-lucide="graduation-cap"
                                        width="16"
                                        height="16"
                                    ></i>

                                </div>


                                <div>

                                    <div class="qualification-title">

                                        {{
                                            $application?->qualification
                                            ??
                                            'Medical Qualification'
                                        }}

                                    </div>

                                    <div class="qualification-meta">

                                        Primary professional qualification

                                    </div>

                                </div>

                            </div>


                            <div class="qualification-row">

                                <div class="qualification-icon">

                                    <i
                                        data-lucide="badge-check"
                                        width="16"
                                        height="16"
                                    ></i>

                                </div>


                                <div>

                                    <div class="qualification-title">
                                        Regulatory Registration
                                    </div>

                                    <div class="qualification-meta">

                                        {{
                                            $application?->registration_number
                                            ??
                                            'Registration details pending'
                                        }}

                                    </div>

                                </div>

                            </div>


                            <div class="qualification-row">

                                <div class="qualification-icon">

                                    <i
                                        data-lucide="briefcase"
                                        width="16"
                                        height="16"
                                    ></i>

                                </div>


                                <div>

                                    <div class="qualification-title">
                                        Experience
                                    </div>

                                    <div class="qualification-meta">

                                        {{
                                            $application?->years_experience
                                            ?? 0
                                        }}
                                        years of professional experience

                                    </div>

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
                                    Patient feedback
                                </div>

                            </div>


                            <a href="#" class="card-link">
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
                                        1 day ago
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

                                    Very professional and explained
                                    everything clearly during the consultation.

                                </div>


                            </div>


                            <div class="review-item">


                                <div class="review-head">

                                    <div class="review-user">
                                        Patient Review
                                    </div>

                                    <div class="review-date">
                                        4 days ago
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

                                    Friendly, attentive and easy to talk to.

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
                                    My Availability
                                </div>

                                <div class="card-subtitle">
                                    Current booking schedule
                                </div>

                            </div>

                        </div>



                        <div class="availability">


                            <div class="availability-status">


                                <div class="availability-icon">

                                    <i
                                        data-lucide="calendar-clock"
                                        width="20"
                                        height="20"
                                    ></i>

                                </div>


                                <div>

                                    <h3>
                                        Available for bookings
                                    </h3>

                                    <p>

                                        Patients can currently
                                        request appointments.

                                    </p>

                                </div>


                            </div>



                            <div class="availability-days">

                                <div class="day active">
                                    Mon
                                </div>

                                <div class="day active">
                                    Tue
                                </div>

                                <div class="day active">
                                    Wed
                                </div>

                                <div class="day active">
                                    Thu
                                </div>

                                <div class="day active">
                                    Fri
                                </div>

                                <div class="day">
                                    Sat
                                </div>

                                <div class="day">
                                    Sun
                                </div>

                            </div>


                        </div>


                    </div>



                    <div class="card">


                        <div class="card-header">

                            <div>

                                <div class="card-title">
                                    Workplace
                                </div>

                                <div class="card-subtitle">
                                    Verified affiliation
                                </div>

                            </div>

                        </div>



                        <div class="workplace">


                            <div class="workplace-main">


                                <div class="workplace-icon">

                                    <i
                                        data-lucide="hospital"
                                        width="19"
                                        height="19"
                                    ></i>

                                </div>


                                <div>

                                    <div class="workplace-name">
                                        Kampala Family Medical Centre
                                    </div>

                                    <div class="workplace-location">
                                        Ntinda, Kampala
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
                                    Common professional tasks
                                </div>

                            </div>

                        </div>



                        <div class="quick-grid">


                            <a href="#" class="quick-action">

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
                                    Update professional information
                                </div>

                            </a>


                            <a href="#" class="quick-action">

                                <div class="quick-icon">

                                    <i
                                        data-lucide="calendar-clock"
                                        width="15"
                                        height="15"
                                    ></i>

                                </div>

                                <div class="quick-title">
                                    Availability
                                </div>

                                <div class="quick-text">
                                    Set your working schedule
                                </div>

                            </a>


                            <a href="#" class="quick-action">

                                <div class="quick-icon">

                                    <i
                                        data-lucide="graduation-cap"
                                        width="15"
                                        height="15"
                                    ></i>

                                </div>

                                <div class="quick-title">
                                    Qualifications
                                </div>

                                <div class="quick-text">
                                    Manage credentials
                                </div>

                            </a>


                            <a href="#" class="quick-action">

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
                                    View patient-facing profile
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


        const sidebar =
            document.getElementById(
                'sidebar'
            );


        const button =
            document.getElementById(
                'mobileMenuButton'
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