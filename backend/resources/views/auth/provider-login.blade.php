<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        Provider Login | AfyaLink Uganda
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
            --text: #5F6B7A;
            --muted: #8A94A3;

            --border: #E5EAF0;
            --surface: #F7F9FC;

            --white: #FFFFFF;

            --shadow:
                0 24px 70px
                rgba(23, 32, 51, .08);

        }


        * {
            box-sizing: border-box;

            margin: 0;
            padding: 0;
        }


        html {
            scroll-behavior: smooth;
        }


        body {
            min-height: 100vh;

            font-family:
                'Poppins',
                sans-serif;

            color: var(--dark);

            background:
                var(--surface);
        }


        button,
        input {
            font: inherit;
        }


        button {
            cursor: pointer;
        }


        a {
            color: inherit;

            text-decoration: none;
        }


        .hidden {
            display: none !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PAGE
        |--------------------------------------------------------------------------
        */

        .page {
            min-height: 100vh;

            display: flex;

            flex-direction: column;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .topbar {

            min-height: 78px;

            padding:
                16px
                clamp(
                    20px,
                    5vw,
                    70px
                );

            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 20px;

            border-bottom:
                1px solid
                var(--border);

            background:
                rgba(
                    255,
                    255,
                    255,
                    .95
                );

            backdrop-filter:
                blur(16px);
        }


        .brand {
            display: flex;

            align-items: center;

            gap: 11px;
        }


        .brand-mark {
            width: 45px;
            height: 45px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 14px;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--red-dark)
                );

            box-shadow:
                0 10px 24px
                rgba(
                    211,
                    47,
                    47,
                    .22
                );
        }


        .brand-name {
            font-size: 19px;

            font-weight: 800;

            line-height: 1;
        }


        .brand-country {
            margin-top: 4px;

            color: var(--green);

            font-size: 8px;

            font-weight: 800;

            letter-spacing:
                .18em;
        }


        .home-link {

            display: flex;

            align-items: center;

            gap: 7px;

            color: var(--text);

            font-size: 10px;

            font-weight: 600;
        }


        /*
        |--------------------------------------------------------------------------
        | MAIN
        |--------------------------------------------------------------------------
        */

        .main {

            width: 100%;

            max-width: 1320px;

            margin-inline: auto;

            padding:
                55px
                clamp(
                    20px,
                    5vw,
                    55px
                )
                70px;

            flex: 1;
        }


        .intro {

            max-width: 760px;

            margin-bottom: 30px;
        }


        .eyebrow {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding:
                7px 11px;

            border-radius: 999px;

            color:
                var(--green-dark);

            background:
                var(--green-soft);

            font-size: 8px;

            font-weight: 800;

            letter-spacing:
                .08em;
        }


        .intro h1 {

            margin-top: 15px;

            font-size:
                clamp(
                    33px,
                    4vw,
                    49px
                );

            line-height: 1.1;

            letter-spacing:
                -.04em;

            font-weight: 800;
        }


        .intro h1 span {
            color: var(--red);
        }


        .intro p {

            max-width: 650px;

            margin-top: 12px;

            color: var(--text);

            font-size: 11px;

            line-height: 1.8;
        }


        /*
        |--------------------------------------------------------------------------
        | GRID
        |--------------------------------------------------------------------------
        */

        .login-grid {

            display: grid;

            grid-template-columns:
                minmax(
                    0,
                    1.2fr
                )
                minmax(
                    300px,
                    .8fr
                );

            gap: 22px;

            align-items:
                stretch;
        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN CARD
        |--------------------------------------------------------------------------
        */

        .login-card {

            min-height: 500px;

            padding:
                clamp(
                    25px,
                    4vw,
                    42px
                );

            border:
                1px solid
                var(--border);

            border-radius:
                24px;

            background:
                white;

            box-shadow:
                var(--shadow);
        }


        .screen {

            display: none;
        }


        .screen.active {

            display: block;

            animation:
                screenIn
                .38s
                cubic-bezier(
                    .2,
                    .8,
                    .2,
                    1
                );
        }


        @keyframes screenIn {

            from {

                opacity: 0;

                transform:
                    translateY(
                        14px
                    );

            }

            to {

                opacity: 1;

                transform:
                    translateY(
                        0
                    );

            }

        }


        .screen-icon {

            width: 54px;
            height: 54px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 16px;

            color: var(--red);

            background:
                var(--red-soft);
        }


        .screen-title {

            margin-top: 19px;

            font-size:
                clamp(
                    23px,
                    3vw,
                    31px
                );

            font-weight: 800;

            letter-spacing:
                -.025em;
        }


        .screen-text {

            max-width: 570px;

            margin-top: 7px;

            color: var(--text);

            font-size: 10px;

            line-height: 1.75;
        }


        /*
        |--------------------------------------------------------------------------
        | INPUT
        |--------------------------------------------------------------------------
        */

        .form-area {

            margin-top: 27px;
        }


        .field {

            display: grid;

            gap: 8px;
        }


        .field label {

            color: #354158;

            font-size: 9px;

            font-weight: 700;
        }


        .input-wrap {

            position: relative;
        }


        .input-icon {

            position: absolute;

            left: 15px;

            top: 50%;

            transform:
                translateY(
                    -50%
                );

            display: flex;

            color:
                var(--muted);
        }


        .control {

            width: 100%;

            height: 55px;

            padding:
                0
                15px
                0
                45px;

            border:
                1px solid
                var(--border);

            border-radius:
                14px;

            outline: none;

            color: var(--dark);

            background: white;

            font-size: 11px;

            font-weight: 500;

            transition:
                border-color
                .2s,
                box-shadow
                .2s;
        }


        .control:focus {

            border-color:
                var(--red);

            box-shadow:
                0 0 0 4px
                rgba(
                    211,
                    47,
                    47,
                    .06
                );
        }


        .registration-input {

            text-transform:
                uppercase;

            letter-spacing:
                .06em;

            font-weight: 700;
        }


        .password-control {

            padding-right:
                48px;
        }


        .password-toggle {

            position: absolute;

            right: 14px;

            top: 50%;

            transform:
                translateY(
                    -50%
                );

            width: 32px;
            height: 32px;

            display: flex;

            align-items: center;

            justify-content: center;

            border: none;

            border-radius: 8px;

            color: var(--muted);

            background:
                transparent;
        }


        .field-note {

            color: var(--muted);

            font-size: 8px;

            line-height: 1.6;
        }


        /*
        |--------------------------------------------------------------------------
        | BUTTONS
        |--------------------------------------------------------------------------
        */

        .btn {

            min-height: 51px;

            padding:
                12px 21px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            gap: 8px;

            border: none;

            border-radius:
                14px;

            font-size: 10px;

            font-weight: 700;

            transition:
                transform .2s,
                opacity .2s,
                box-shadow .2s;
        }


        .btn:hover:not(:disabled) {

            transform:
                translateY(
                    -2px
                );
        }


        .btn:disabled {

            opacity: .45;

            cursor: not-allowed;
        }


        .btn-primary {

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--red-dark)
                );

            box-shadow:
                0 12px 27px
                rgba(
                    211,
                    47,
                    47,
                    .18
                );
        }


        .btn-green {

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--green),
                    var(--green-dark)
                );
        }


        .btn-outline {

            border:
                1px solid
                var(--border);

            color: var(--dark);

            background: white;
        }


        .wide {
            width: 100%;
        }


        .actions {

            margin-top: 22px;

            display: flex;

            gap: 10px;

            flex-wrap: wrap;
        }


        /*
        |--------------------------------------------------------------------------
        | MESSAGE
        |--------------------------------------------------------------------------
        */

        .message {

            display: none;

            margin-top: 16px;

            padding:
                11px 13px;

            border-radius: 12px;

            font-size: 8.5px;

            line-height: 1.65;
        }


        .message.error {

            display: block;

            color: #991B1B;

            background: #FEF2F2;

            border:
                1px solid
                #F7D5D5;
        }


        .message.success {

            display: block;

            color: #166534;

            background: #F0FDF4;

            border:
                1px solid
                #CDECD9;
        }


        /*
        |--------------------------------------------------------------------------
        | ACCOUNT FOUND
        |--------------------------------------------------------------------------
        */

        .account-result {

            margin-top: 25px;

            padding: 22px;

            border:
                1px solid
                #D9EADF;

            border-radius:
                19px;

            background:
                linear-gradient(
                    135deg,
                    #FFFFFF,
                    var(--green-soft)
                );
        }


        .result-head {

            display: flex;

            align-items: center;

            gap: 14px;
        }


        .avatar {

            width: 70px;
            height: 70px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--green)
                );

            box-shadow:
                0 12px 25px
                rgba(
                    23,
                    32,
                    51,
                    .13
                );

            font-size: 18px;

            font-weight: 800;
        }


        .result-name {

            font-size: 17px;

            line-height: 1.3;

            font-weight: 800;
        }


        .result-role {

            margin-top: 3px;

            color: var(--green);

            font-size: 8px;

            font-weight: 700;
        }


        .verified-chip {

            margin-left: auto;

            padding:
                6px 9px;

            display: inline-flex;

            align-items: center;

            gap: 5px;

            border-radius: 999px;

            color: var(--green-dark);

            background: white;

            font-size: 7px;

            font-weight: 800;
        }


        .result-grid {

            margin-top: 19px;

            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 10px;
        }


        .result-item {

            padding: 12px;

            border-radius: 12px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .78
                );

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    .9
                );
        }


        .result-label {

            color: var(--muted);

            font-size: 6.8px;

            font-weight: 800;

            letter-spacing:
                .07em;
        }


        .result-value {

            margin-top: 4px;

            font-size: 8.5px;

            line-height: 1.5;

            font-weight: 600;

            word-break:
                break-word;
        }


        /*
        |--------------------------------------------------------------------------
        | SIDE AREA
        |--------------------------------------------------------------------------
        */

        .side-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    1fr
                );

            gap: 12px;
        }


        .visual-card {

            grid-column:
                1 / -1;

            min-height: 280px;

            padding: 24px;

            position: relative;

            overflow: hidden;

            border-radius: 24px;

            color: white;

            background:
                linear-gradient(
                    145deg,
                    var(--green-dark),
                    var(--dark)
                );
        }


        .visual-card::before {

            content: "";

            position: absolute;

            width: 300px;
            height: 300px;

            top: -150px;

            right: -110px;

            border-radius: 50%;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .08
                );
        }


        .visual-card::after {

            content: "";

            position: absolute;

            width: 230px;
            height: 230px;

            bottom: -140px;

            left: -90px;

            border-radius: 50%;

            background:
                rgba(
                    211,
                    47,
                    47,
                    .25
                );
        }


        .visual-icon {

            width: 50px;
            height: 50px;

            position: relative;

            z-index: 1;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 15px;

            color: white;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .11
                );

            backdrop-filter:
                blur(10px);
        }


        .visual-card h2 {

            position: relative;

            z-index: 1;

            max-width: 310px;

            margin-top: 25px;

            font-size: 24px;

            line-height: 1.22;

            font-weight: 800;
        }


        .visual-card p {

            position: relative;

            z-index: 1;

            margin-top: 10px;

            color:
                rgba(
                    255,
                    255,
                    255,
                    .66
                );

            font-size: 9px;

            line-height: 1.75;
        }


        .mini-info {

            padding: 18px;

            border:
                1px solid
                var(--border);

            border-radius: 18px;

            background: white;

            box-shadow:
                0 8px 22px
                rgba(
                    23,
                    32,
                    51,
                    .035
                );
        }


        .mini-icon {

            width: 36px;
            height: 36px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 11px;

            color: var(--red);

            background:
                var(--red-soft);
        }


        .mini-info:nth-child(3)
        .mini-icon {

            color: var(--green);

            background:
                var(--green-soft);
        }


        .mini-info h3 {

            margin-top: 11px;

            font-size: 10px;

            font-weight: 800;
        }


        .mini-info p {

            margin-top: 4px;

            color: var(--muted);

            font-size: 7.8px;

            line-height: 1.6;
        }


        /*
        |--------------------------------------------------------------------------
        | PASSWORD ACCOUNT MINI
        |--------------------------------------------------------------------------
        */

        .password-profile {

            margin-top: 22px;

            padding: 14px;

            display: flex;

            align-items: center;

            gap: 11px;

            border:
                1px solid
                var(--border);

            border-radius: 15px;

            background:
                var(--surface);
        }


        .password-avatar {

            width: 45px;
            height: 45px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--red),
                    var(--green)
                );

            font-size: 12px;

            font-weight: 800;
        }


        .password-name {

            font-size: 10px;

            font-weight: 800;
        }


        .password-number {

            margin-top: 2px;

            color: var(--muted);

            font-size: 7.5px;

            font-weight: 600;
        }


        /*
        |--------------------------------------------------------------------------
        | REMEMBER
        |--------------------------------------------------------------------------
        */

        .remember-row {

            margin-top: 13px;

            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 10px;

            flex-wrap: wrap;
        }


        .remember {

            display: flex;

            align-items: center;

            gap: 7px;

            color: var(--text);

            font-size: 8.5px;
        }


        .remember input {

            width: 15px;
            height: 15px;

            accent-color:
                var(--red);
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (
            max-width: 920px
        ) {

            .login-grid {

                grid-template-columns:
                    1fr;

            }

        }


        @media (
            max-width: 600px
        ) {

            .topbar {

                min-height:
                    70px;

                padding:
                    12px
                    16px;

            }


            .brand-mark {

                width: 40px;
                height: 40px;

            }


            .brand-name {

                font-size: 16px;

            }


            .main {

                padding:
                    35px
                    16px
                    50px;

            }


            .login-card {

                min-height: auto;

                padding: 21px;

                border-radius: 19px;

            }


            .side-grid {

                grid-template-columns:
                    1fr;

            }


            .visual-card {

                grid-column: auto;

            }


            .result-grid {

                grid-template-columns:
                    1fr;

            }


            .verified-chip {

                display: none;

            }

        }

    </style>

</head>


<body>


<div class="page">


    <header class="topbar">


        <a
            href="{{ route('home') }}"
            class="brand"
        >

            <div class="brand-mark">

                <i
                    data-lucide="heart-pulse"
                    width="20"
                    height="20"
                ></i>

            </div>


            <div>

                <div class="brand-name">
                    AfyaLink
                </div>

                <div class="brand-country">
                    UGANDA
                </div>

            </div>

        </a>


        <a
            href="{{ route('home') }}"
            class="home-link"
        >

            <i
                data-lucide="arrow-left"
                width="14"
                height="14"
            ></i>

            Back to Home

        </a>


    </header>



    <main class="main">


        <div class="intro">


            


            <h1>

                Welcome back to

                <span>
                    AfyaLink.
                </span>

            </h1>


            <p>

                Healthcare facilities and health professionals
                can access their AfyaLink accounts using the
                registration number issued during registration.

            </p>


        </div>



        <div class="login-grid">


            <section class="login-card">


                {{-- ================================================= --}}
                {{-- SCREEN 1 --}}
                {{-- ================================================= --}}

                <div
                    class="screen active"
                    id="registrationScreen"
                >


                    <div class="screen-icon">

                        <i
                            data-lucide="badge-check"
                            width="24"
                            height="24"
                        ></i>

                    </div>


                    <h2 class="screen-title">
                        Find your AfyaLink account
                    </h2>


                    <p class="screen-text">

                        Enter the registration number issued
                        to you when your AfyaLink account
                        was created.

                    </p>



                    <div class="form-area">


                        <div class="field">

                            <label for="registrationNumber">

                                AfyaLink Registration Number

                            </label>


                            <div class="input-wrap">

                                <span class="input-icon">

                                    <i
                                        data-lucide="hash"
                                        width="16"
                                        height="16"
                                    ></i>

                                </span>


                                <input
                                    type="text"
                                    class="control registration-input"
                                    id="registrationNumber"
                                    placeholder="AFY-PRO-2026-000001"
                                    autocomplete="off"
                                >

                            </div>


                            <div class="field-note">

                                Example:
                                AFY-PRO-2026-000001 or
                                AFY-FAC-2026-000001

                            </div>

                        </div>



                        <div
                            class="message"
                            id="findMessage"
                        ></div>



                        <div class="actions">

                            <button
                                type="button"
                                class="btn btn-primary wide"
                                id="findAccountButton"
                            >

                                Find My Account

                                <i
                                    data-lucide="arrow-right"
                                    width="15"
                                    height="15"
                                ></i>

                            </button>

                        </div>


                    </div>


                </div>



                {{-- ================================================= --}}
                {{-- SCREEN 2 --}}
                {{-- ================================================= --}}

                <div
                    class="screen"
                    id="confirmScreen"
                >


                    <div class="screen-icon">

                        <i
                            data-lucide="user-check"
                            width="24"
                            height="24"
                        ></i>

                    </div>


                    <h2 class="screen-title">
                        Is this your account?
                    </h2>


                    <p class="screen-text">

                        Confirm that the account below
                        belongs to you before entering
                        your password.

                    </p>



                    <div class="account-result">
                           <div
                                class="avatar"
                                id="resultAvatar"
                            >
                                AL
                            </div>

                        <div class="result-head">


                            


                            <div>

                                <div
                                    class="result-name"
                                    id="resultName"
                                >
                                    AfyaLink Member
                                </div>


                                <div
                                    class="result-role"
                                    id="resultRole"
                                >
                                    Healthcare Provider
                                </div>

                            </div>


                            <div class="verified-chip">

                                <i
                                    data-lucide="search-check"
                                    width="12"
                                    height="12"
                                ></i>

                                ACCOUNT FOUND

                            </div>


                        </div>



                        <div class="result-grid">


                            <div class="result-item">

                                <div class="result-label">
                                    REGISTRATION NUMBER
                                </div>

                                <div
                                    class="result-value"
                                    id="resultNumber"
                                >
                                    -
                                </div>

                            </div>


                            <div class="result-item">

                                <div class="result-label">
                                    ACCOUNT TYPE
                                </div>

                                <div
                                    class="result-value"
                                    id="resultType"
                                >
                                    -
                                </div>

                            </div>


                            <div class="result-item">

                                <div class="result-label">
                                    FACILITY / WORKPLACE
                                </div>

                                <div
                                    class="result-value"
                                    id="resultFacility"
                                >
                                    -
                                </div>

                            </div>


                            <div class="result-item">

                                <div class="result-label">
                                    APPLICATION STATUS
                                </div>

                                <div
                                    class="result-value"
                                    id="resultStatus"
                                >
                                    -
                                </div>

                            </div>


                        </div>


                    </div>



                    <div class="actions">


                        <button
                            type="button"
                            class="btn btn-outline"
                            id="searchAgainButton"
                        >

                            <i
                                data-lucide="arrow-left"
                                width="14"
                                height="14"
                            ></i>

                            Search Again

                        </button>


                        <button
                            type="button"
                            class="btn btn-green"
                            id="confirmAccountButton"
                        >

                            This Is My Account

                            <i
                                data-lucide="arrow-right"
                                width="14"
                                height="14"
                            ></i>

                        </button>


                    </div>


                </div>



                {{-- ================================================= --}}
                {{-- SCREEN 3 --}}
                {{-- ================================================= --}}

                <div
                    class="screen"
                    id="passwordScreen"
                >


                    <div class="screen-icon">

                        <i
                            data-lucide="lock-keyhole"
                            width="24"
                            height="24"
                        ></i>

                    </div>


                    <h2 class="screen-title">
                        Enter your password
                    </h2>


                    <p class="screen-text">

                        Your AfyaLink account has been
                        confirmed. Enter your password
                        to continue to your dashboard.

                    </p>



                    <div class="password-profile">


                        <div
                            class="password-avatar"
                            id="passwordAvatar"
                        >
                            AL
                        </div>


                        <div>

                            <div
                                class="password-name"
                                id="passwordName"
                            >
                                AfyaLink Member
                            </div>

                            <div
                                class="password-number"
                                id="passwordRegistration"
                            >
                                -
                            </div>

                        </div>


                    </div>



                    <div class="form-area">


                        <div class="field">

                            <label for="password">
                                Password
                            </label>


                            <div class="input-wrap">

                                <span class="input-icon">

                                    <i
                                        data-lucide="lock"
                                        width="16"
                                        height="16"
                                    ></i>

                                </span>


                                <input
                                    type="password"
                                    class="control password-control"
                                    id="password"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                >


                                <button
                                    type="button"
                                    class="password-toggle"
                                    id="togglePassword"
                                    aria-label="Show password"
                                >

                                    <i
                                        data-lucide="eye"
                                        width="16"
                                        height="16"
                                    ></i>

                                </button>


                            </div>

                        </div>



                        <div class="remember-row">


                            <label class="remember">

                                <input
                                    type="checkbox"
                                    id="remember"
                                >

                                Keep me signed in

                            </label>


                            <button
                                type="button"
                                style="
                                    border:0;
                                    background:none;
                                    color:#D32F2F;
                                    font-size:8.5px;
                                    font-weight:700;
                                "
                            >
                                Forgot password?
                            </button>


                        </div>



                        <div
                            class="message"
                            id="loginMessage"
                        ></div>



                        <div class="actions">


                            <button
                                type="button"
                                class="btn btn-outline"
                                id="backToProfileButton"
                            >

                                <i
                                    data-lucide="arrow-left"
                                    width="14"
                                    height="14"
                                ></i>

                                Back

                            </button>


                            <button
                                type="button"
                                class="btn btn-primary"
                                id="loginButton"
                                style="flex:1;"
                            >

                                Sign In to AfyaLink

                                <i
                                    data-lucide="log-in"
                                    width="15"
                                    height="15"
                                ></i>

                            </button>


                        </div>


                    </div>


                </div>


            </section>



            {{-- ================================================= --}}
            {{-- SIDE GRID --}}
            {{-- ================================================= --}}

            <aside class="side-grid">


                <div class="visual-card">


                    <div class="visual-icon">

                        <i
                            data-lucide="heart-pulse"
                            width="23"
                            height="23"
                        ></i>

                    </div>


                    <h2>

                        Your healthcare profile,
                        all in one place.

                    </h2>


                    <p>

                        Manage your AfyaLink profile,
                        verification information,
                        services and patient-facing
                        details from your dashboard.

                    </p>


                </div>



                <div class="mini-info">


                    <div class="mini-icon">

                        <i
                            data-lucide="hospital"
                            width="17"
                            height="17"
                        ></i>

                    </div>


                    <h3>
                        Facilities
                    </h3>


                    <p>

                        Access facility information,
                        services and verification status.

                    </p>


                </div>



                <div class="mini-info">


                    <div class="mini-icon">

                        <i
                            data-lucide="stethoscope"
                            width="17"
                            height="17"
                        ></i>

                    </div>


                    <h3>
                        Professionals
                    </h3>


                    <p>

                        Manage your professional profile
                        and workplace affiliation.

                    </p>


                </div>


            </aside>


        </div>


    </main>


</div>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        if (window.lucide) {
            lucide.createIcons();
        }


        const registrationScreen =
            document.getElementById(
                'registrationScreen'
            );


        const confirmScreen =
            document.getElementById(
                'confirmScreen'
            );


        const passwordScreen =
            document.getElementById(
                'passwordScreen'
            );


        const registrationNumber =
            document.getElementById(
                'registrationNumber'
            );


        const findAccountButton =
            document.getElementById(
                'findAccountButton'
            );


        const findMessage =
            document.getElementById(
                'findMessage'
            );


        const loginMessage =
            document.getElementById(
                'loginMessage'
            );


        let selectedAccount = null;


        function csrf() {

            return document
                .querySelector(
                    'meta[name="csrf-token"]'
                )
                ?.content
                || '';

        }


        function showScreen(
            screen
        ) {

            [
                registrationScreen,
                confirmScreen,
                passwordScreen
            ]
            .forEach(
                function (item) {

                    item.classList.remove(
                        'active'
                    );

                }
            );


            screen.classList.add(
                'active'
            );


            if (window.lucide) {
                lucide.createIcons();
            }

        }


        registrationNumber
            .addEventListener(
                'input',
                function () {

                    this.value =
                        this.value
                            .toUpperCase()
                            .replace(
                                /\s+/g,
                                ''
                            );

                }
            );


        registrationNumber
            .addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key ===
                        'Enter'
                    ) {

                        event
                            .preventDefault();


                        findAccountButton
                            .click();

                    }

                }
            );


        findAccountButton
            .addEventListener(
                'click',
                async function () {

                    clearMessage(
                        findMessage
                    );


                    const number =
                        registrationNumber
                            .value
                            .trim()
                            .toUpperCase();


                    if (!number) {

                        setMessage(
                            findMessage,
                            'error',
                            'Enter your AfyaLink registration number.'
                        );

                        return;

                    }


                    findAccountButton.disabled =
                        true;


                    try {

                        const response =
                            await fetch(
                                '{{ route('provider.login.find') }}',
                                {
                                    method:
                                        'POST',

                                    credentials:
                                        'same-origin',

                                    headers: {

                                        'Accept':
                                            'application/json',

                                        'Content-Type':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            csrf()

                                    },

                                    body:
                                        JSON.stringify({
                                            registration_number:
                                                number
                                        })
                                }
                            );


                        const data =
                            await response.json();


                        if (!response.ok) {

                            throw new Error(
                                data.message
                                ||
                                'We could not find that account.'
                            );

                        }


                        selectedAccount =
                            data.account;


                        populateProfile(
                            selectedAccount
                        );


                        showScreen(
                            confirmScreen
                        );

                    } catch (error) {

                        setMessage(
                            findMessage,
                            'error',
                            error.message
                        );

                    } finally {

                        findAccountButton.disabled =
                            false;

                    }

                }
            );


        function populateProfile(
            account
        ) {

            document
                .getElementById(
                    'resultAvatar'
                )
                .textContent =
                    account.initials;


            document
                .getElementById(
                    'resultName'
                )
                .textContent =
                    account.name;


            document
                .getElementById(
                    'resultRole'
                )
                .textContent =
                    account.account_label;


            document
                .getElementById(
                    'resultNumber'
                )
                .textContent =
                    account.registration_number;


            document
                .getElementById(
                    'resultType'
                )
                .textContent =
                    account.account_label;


            document
                .getElementById(
                    'resultFacility'
                )
                .textContent =
                    account.facility_name
                    ||
                    (
                        account.account_type
                        ===
                        'facility'
                            ?
                            'Facility account'
                            :
                            'Not available'
                    );


            document
                .getElementById(
                    'resultStatus'
                )
                .textContent =
                    formatStatus(
                        account.status
                    );


            document
                .getElementById(
                    'passwordAvatar'
                )
                .textContent =
                    account.initials;


            document
                .getElementById(
                    'passwordName'
                )
                .textContent =
                    account.name;


            document
                .getElementById(
                    'passwordRegistration'
                )
                .textContent =
                    account.registration_number;

        }


        document
            .getElementById(
                'searchAgainButton'
            )
            .addEventListener(
                'click',
                function () {

                    selectedAccount =
                        null;


                    showScreen(
                        registrationScreen
                    );


                    registrationNumber
                        .focus();

                }
            );


        document
            .getElementById(
                'confirmAccountButton'
            )
            .addEventListener(
                'click',
                function () {

                    if (
                        !selectedAccount
                    ) {
                        return;
                    }


                    showScreen(
                        passwordScreen
                    );


                    setTimeout(
                        function () {

                            document
                                .getElementById(
                                    'password'
                                )
                                .focus();

                        },
                        100
                    );

                }
            );


        document
            .getElementById(
                'backToProfileButton'
            )
            .addEventListener(
                'click',
                function () {

                    showScreen(
                        confirmScreen
                    );

                }
            );


        const passwordInput =
            document.getElementById(
                'password'
            );


        passwordInput
            .addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key ===
                        'Enter'
                    ) {

                        event
                            .preventDefault();


                        document
                            .getElementById(
                                'loginButton'
                            )
                            .click();

                    }

                }
            );


        document
            .getElementById(
                'togglePassword'
            )
            .addEventListener(
                'click',
                function () {

                    const hidden =
                        passwordInput.type
                        ===
                        'password';


                    passwordInput.type =
                        hidden
                            ?
                            'text'
                            :
                            'password';


                    this.innerHTML =
                        hidden
                            ?
                            '<i data-lucide="eye-off" width="16" height="16"></i>'
                            :
                            '<i data-lucide="eye" width="16" height="16"></i>';


                    if (window.lucide) {
                        lucide.createIcons();
                    }

                }
            );


        document
            .getElementById(
                'loginButton'
            )
            .addEventListener(
                'click',
                async function () {

                    clearMessage(
                        loginMessage
                    );


                    if (
                        !selectedAccount
                    ) {

                        showScreen(
                            registrationScreen
                        );

                        return;

                    }


                    const password =
                        passwordInput
                            .value;


                    if (!password) {

                        setMessage(
                            loginMessage,
                            'error',
                            'Enter your password.'
                        );

                        return;

                    }


                    const button =
                        this;


                    button.disabled =
                        true;


                    try {

                        const response =
                            await fetch(
                                '{{ route('provider.login.authenticate') }}',
                                {
                                    method:
                                        'POST',

                                    credentials:
                                        'same-origin',

                                    headers: {

                                        'Accept':
                                            'application/json',

                                        'Content-Type':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            csrf()

                                    },

                                    body:
                                        JSON.stringify({

                                            registration_number:
                                                selectedAccount
                                                    .registration_number,

                                            password:
                                                password,

                                            remember:
                                                document
                                                    .getElementById(
                                                        'remember'
                                                    )
                                                    .checked

                                        })
                                }
                            );


                        const data =
                            await response.json();


                        if (!response.ok) {

                            throw new Error(
                                data.message
                                ||
                                'Unable to sign in.'
                            );

                        }


                        setMessage(
                            loginMessage,
                            'success',
                            data.message
                            ||
                            'Login successful.'
                        );


                        setTimeout(
                            function () {

                                window.location.href =
                                    data.redirect;

                            },
                            350
                        );

                    } catch (error) {

                        setMessage(
                            loginMessage,
                            'error',
                            error.message
                        );

                    } finally {

                        button.disabled =
                            false;

                    }

                }
            );


        function setMessage(
            element,
            type,
            text
        ) {

            element.className =
                'message '
                + type;


            element.textContent =
                text;

        }


        function clearMessage(
            element
        ) {

            element.className =
                'message';


            element.textContent =
                '';

        }


        function formatStatus(
            value
        ) {

            if (!value) {
                return 'Draft';
            }


            return value
                .replace(
                    /_/g,
                    ' '
                )
                .replace(
                    /\b\w/g,
                    function (letter) {

                        return letter
                            .toUpperCase();

                    }
                );

        }


    }
);

</script>


</body>
</html>