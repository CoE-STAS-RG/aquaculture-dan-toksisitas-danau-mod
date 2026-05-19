@php
    $navUser = Auth::user();
    $navInitials = collect(explode(' ', trim($navUser->name ?? 'Akun')))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $navDeviceCount = $navUser?->devices()->count() ?? 0;
    $navLocaleLabel = session('locale', app()->getLocale()) === 'id' ? 'Indonesia' : 'English';
    $navLocaleSwitch = session('locale', app()->getLocale()) === 'id' ? 'en' : 'id';
    $navLocaleSwitchLabel = $navLocaleSwitch === 'id' ? 'Bahasa Indonesia' : 'English';
    $navRoleLabel = ucfirst(str_replace('_', ' ', $navUser->role ?? 'member'));
    $navPhoneLabel = filled($navUser->phone ?? null) ? $navUser->phone : 'Tambahkan di profil';
@endphp

<nav x-data="{ open: false }" class="aq-topnav">
    <style>
        .aq-topnav {
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 14px 14px 10px;
            background: linear-gradient(180deg, rgba(244, 250, 255, 0.94), rgba(244, 250, 255, 0.7) 68%, rgba(244, 250, 255, 0));
            backdrop-filter: blur(12px);
        }

        .aq-topnav__bar {
            max-width: 1320px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 18px;
            padding: 14px 20px;
            border-radius: 32px;
            border: 1px solid rgba(143, 198, 230, 0.34);
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.55), transparent 32%),
                linear-gradient(135deg, rgba(255, 255, 255, 0.52) 0%, rgba(212, 239, 255, 0.46) 48%, rgba(191, 238, 255, 0.42) 100%);
            box-shadow:
                0 22px 46px rgba(12, 55, 103, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.52);
        }

        .aq-topnav__brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            min-width: 0;
        }

        .aq-topnav__brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.68);
            border: 1px solid rgba(255, 255, 255, 0.72);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
            color: #1a74cf;
            flex-shrink: 0;
        }

        .aq-topnav__brand-copy {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .aq-topnav__brand-copy strong {
            color: #17426f;
            font-size: 0.96rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.2;
        }

        .aq-topnav__brand-copy span {
            margin-top: 2px;
            color: #6c8aa9;
            font-size: 0.72rem;
            line-height: 1.2;
        }

        .aq-topnav__links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 0;
        }

        .aq-topnav__link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 999px;
            color: #527498;
            font-size: 0.92rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.22s ease, color 0.22s ease, transform 0.22s ease, box-shadow 0.22s ease;
            white-space: nowrap;
        }

        .aq-topnav__link:hover {
            color: #184a7d;
            background: rgba(255, 255, 255, 0.36);
            transform: translateY(-1px);
        }

        .aq-topnav__link.is-active {
            color: #123b69;
            background: rgba(255, 255, 255, 0.76);
            box-shadow: 0 10px 20px rgba(15, 64, 110, 0.08);
        }

        .aq-topnav__actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .aq-topnav__iconbtn,
        .aq-topnav__textbtn,
        .aq-topnav__accountbtn,
        .aq-topnav__mobile-toggle {
            border: 0;
            outline: none;
            cursor: pointer;
            transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease, color 0.22s ease;
        }

        .aq-topnav__iconbtn,
        .aq-topnav__mobile-toggle {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.56);
            color: #36587b;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.78);
        }

        .aq-topnav__iconbtn:hover,
        .aq-topnav__textbtn:hover,
        .aq-topnav__accountbtn:hover,
        .aq-topnav__mobile-toggle:hover {
            transform: translateY(-1px);
        }

        .aq-topnav__textbtn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: transparent;
            color: #527498;
            font-size: 0.88rem;
            font-weight: 700;
        }

        .aq-topnav__textbtn:hover {
            color: #184a7d;
            background: rgba(255, 255, 255, 0.34);
        }

        .aq-topnav__accountbtn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 8px 10px 8px 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.88);
            color: #123b69;
            box-shadow: 0 12px 24px rgba(13, 55, 98, 0.1);
        }

        .aq-topnav__accountbtn svg {
            color: #7a93b2;
            flex-shrink: 0;
        }

        .aq-topnav__account-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: linear-gradient(135deg, #d7ebff 0%, #bdefff 100%);
            color: #12416f;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            flex-shrink: 0;
        }

        .aq-topnav__account-copy {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            min-width: 0;
            line-height: 1.1;
        }

        .aq-topnav__account-copy strong {
            max-width: 110px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.84rem;
            font-weight: 800;
        }

        .aq-topnav__account-copy span {
            margin-top: 4px;
            color: #6c8aa9;
            font-size: 0.68rem;
            font-weight: 700;
        }

        .aq-topnav__badge {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #ef4444;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.82);
        }

        .aq-topnav__notif-wrap {
            position: relative;
        }

        .aq-topnav__notif-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 12px);
            width: 320px;
            border-radius: 24px;
            border: 1px solid rgba(191, 219, 254, 0.82);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 28px 50px rgba(15, 56, 98, 0.16);
            backdrop-filter: blur(16px);
            overflow: hidden;
        }

        .aq-topnav__notif-head {
            padding: 16px 18px 12px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.84);
        }

        .aq-topnav__notif-head h4 {
            margin: 0;
            color: #143a67;
            font-size: 0.95rem;
            font-weight: 800;
        }

        .aq-topnav__notif-list {
            margin: 0;
            padding: 10px 18px 14px;
            list-style: none;
            color: #526f90;
            font-size: 0.86rem;
        }

        .aq-topnav__notif-list li + li {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(226, 232, 240, 0.8);
        }

        .aq-topnav__menu-surface {
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(191, 219, 254, 0.82);
            border-radius: 22px;
            box-shadow: 0 24px 46px rgba(15, 56, 98, 0.16);
            backdrop-filter: blur(16px);
            overflow: hidden;
        }

        .aq-topnav__account-surface {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(191, 219, 254, 0.9);
            border-radius: 28px;
            box-shadow: 0 28px 52px rgba(15, 56, 98, 0.18);
            backdrop-filter: blur(16px);
            overflow: hidden;
        }

        .aq-topnav__menu-head {
            padding: 14px 16px 10px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.84);
        }

        .aq-topnav__menu-head strong {
            display: block;
            color: #143a67;
            font-size: 0.94rem;
            font-weight: 800;
        }

        .aq-topnav__menu-head span {
            display: block;
            margin-top: 4px;
            color: #6c8aa9;
            font-size: 0.78rem;
        }

        .aq-topnav__account-panel {
            padding: 16px;
        }

        .aq-topnav__account-card {
            padding: 16px;
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(167, 227, 255, 0.52), transparent 28%),
                linear-gradient(180deg, #ffffff 0%, #eef8ff 100%);
            border: 1px solid rgba(191, 219, 254, 0.8);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.84);
        }

        .aq-topnav__account-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .aq-topnav__account-hero {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .aq-topnav__account-photo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            border-radius: 24px;
            background: linear-gradient(135deg, #cfe7ff 0%, #b4f0ec 100%);
            color: #123b69;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.88);
            flex-shrink: 0;
        }

        .aq-topnav__account-info {
            min-width: 0;
        }

        .aq-topnav__account-info strong {
            display: block;
            color: #143a67;
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .aq-topnav__account-info span,
        .aq-topnav__account-info small {
            display: block;
            margin-top: 5px;
            color: #6c8aa9;
        }

        .aq-topnav__account-info span {
            font-size: 0.78rem;
            line-height: 1.4;
        }

        .aq-topnav__account-info small {
            font-size: 0.72rem;
            line-height: 1.4;
        }

        .aq-topnav__account-tag {
            display: inline-flex;
            align-items: center;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.82);
            color: #17426f;
            font-size: 0.68rem;
            font-weight: 800;
            white-space: nowrap;
            box-shadow: 0 8px 18px rgba(15, 56, 98, 0.08);
        }

        .aq-topnav__account-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .aq-topnav__account-meta-item {
            padding: 10px 12px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.84);
            border: 1px solid rgba(226, 232, 240, 0.9);
        }

        .aq-topnav__account-meta-item span {
            display: block;
            color: #7b8fa8;
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .aq-topnav__account-meta-item strong {
            display: block;
            margin-top: 6px;
            color: #173f6d;
            font-size: 0.84rem;
            font-weight: 800;
            line-height: 1.25;
        }

        .aq-topnav__account-links {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .aq-topnav__account-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            padding: 13px 14px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, 0.92);
            color: #244a75;
            font-size: 0.86rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.22s ease, background 0.22s ease, box-shadow 0.22s ease, color 0.22s ease;
        }

        .aq-topnav__account-link:hover {
            transform: translateY(-1px);
            background: rgba(239, 248, 255, 0.96);
            box-shadow: 0 10px 22px rgba(15, 56, 98, 0.08);
            color: #123b69;
        }

        .aq-topnav__account-link-main {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .aq-topnav__account-link-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: rgba(232, 243, 255, 0.92);
            color: #1a74cf;
            flex-shrink: 0;
        }

        .aq-topnav__account-link-copy {
            min-width: 0;
        }

        .aq-topnav__account-link-copy strong,
        .aq-topnav__account-link-copy span {
            display: block;
        }

        .aq-topnav__account-link-copy strong {
            color: inherit;
            font-size: 0.84rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .aq-topnav__account-link-copy span {
            margin-top: 3px;
            color: #7b8fa8;
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.35;
        }

        .aq-topnav__account-link-arrow {
            color: #89a1bb;
            flex-shrink: 0;
        }

        .aq-topnav__account-linkbtn {
            appearance: none;
            cursor: pointer;
            text-align: left;
        }

        .aq-topnav__mobile-panel {
            max-width: 1320px;
            margin: 12px auto 0;
            padding: 14px;
            border-radius: 26px;
            border: 1px solid rgba(143, 198, 230, 0.28);
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 20px 40px rgba(12, 55, 103, 0.1);
            backdrop-filter: blur(16px);
        }

        .aq-topnav__mobile-links,
        .aq-topnav__mobile-group {
            display: grid;
            gap: 10px;
        }

        .aq-topnav__mobile-group {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid rgba(226, 232, 240, 0.82);
        }

        .aq-topnav__mobile-link,
        .aq-topnav__mobile-ghost,
        .aq-topnav__mobile-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 14px;
            border-radius: 16px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .aq-topnav__mobile-link,
        .aq-topnav__mobile-ghost {
            border: 1px solid rgba(209, 220, 234, 0.9);
            background: rgba(247, 251, 255, 0.84);
            color: #36587b;
        }

        .aq-topnav__mobile-link.is-active {
            background: rgba(228, 241, 255, 0.96);
            color: #123b69;
            border-color: rgba(147, 197, 253, 0.9);
        }

        .aq-topnav__mobile-primary {
            border: 0;
            background: linear-gradient(135deg, #ffffff 0%, #eef7ff 100%);
            color: #123b69;
            box-shadow: 0 14px 24px rgba(13, 55, 98, 0.08);
        }

        .aq-topnav__mobile-label {
            margin-bottom: 2px;
            color: #6c8aa9;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        @media (max-width: 1023px) {
            .aq-topnav {
                padding-inline: 12px;
            }

            .aq-topnav__bar {
                grid-template-columns: auto 1fr auto;
                padding: 12px 14px;
                border-radius: 26px;
            }
        }

        @media (max-width: 767px) {
            .aq-topnav__brand-copy span {
                display: none;
            }

            .aq-topnav__brand-copy strong {
                font-size: 0.88rem;
            }
        }
    </style>

    <div class="aq-topnav__bar">
        <a href="{{ route('user.dashboard') }}" class="aq-topnav__brand">
            <span class="aq-topnav__brand-mark">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M4 17c2.2-3.4 4.7-5.1 7.5-5.1S16.8 13.6 20 17" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M7 9.5a2.25 2.25 0 1 0 0-.01Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M16.5 8.5a1.75 1.75 0 1 0 0-.01Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M3.5 5.5h17" />
                </svg>
            </span>
            <span class="aq-topnav__brand-copy">
                <strong>Aquaculture</strong>
                <span>Water quality monitoring</span>
            </span>
        </a>

        <div class="aq-topnav__links hidden lg:flex">
            <a href="{{ route('user.dashboard') }}" class="aq-topnav__link {{ request()->routeIs('user.dashboard') ? 'is-active' : '' }}">
                {{ __('navigation.dashboard') }}
            </a>
            <a href="{{ route('index-fish') }}" class="aq-topnav__link {{ request()->routeIs('index-fish') ? 'is-active' : '' }}">
                {{ __('navigation.growth_chart') }}
            </a>
            <a href="{{ route('devices.index') }}" class="aq-topnav__link {{ request()->routeIs('devices.*') ? 'is-active' : '' }}">
                Devices
            </a>
        </div>

        <div class="aq-topnav__actions">
            <div class="aq-topnav__notif-wrap hidden lg:block">
                <button id="notifBtn" class="aq-topnav__iconbtn" type="button" aria-label="{{ __('navigation.notifications') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.3-1.3a2 2 0 0 1-.6-1.42V11a6 6 0 0 0-4-5.66V5a2 2 0 1 0-4 0v.34A6 6 0 0 0 6 11v3.28c0 .53-.21 1.04-.58 1.42L4 17h5" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.5 17a2.5 2.5 0 0 0 5 0" />
                    </svg>
                    <span class="aq-topnav__badge"></span>
                </button>

                <div id="notifDropdown" class="aq-topnav__notif-dropdown hidden">
                    <div class="aq-topnav__notif-head">
                        <h4>{{ __('navigation.latest_notifications') }}</h4>
                    </div>
                    <ul id="notifList" class="aq-topnav__notif-list"></ul>
                </div>
            </div>

            <div class="hidden lg:block">
                <x-dropdown align="right" width="48" contentClasses="aq-topnav__menu-surface py-2">
                    <x-slot name="trigger">
                        <button type="button" class="aq-topnav__textbtn">
                            <span>{{ session('locale', app()->getLocale()) === 'id' ? 'Indonesian' : 'English' }}</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('language.switch', ['locale' => 'id'])" class="rounded-xl">
                            Bahasa Indonesia
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('language.switch', ['locale' => 'en'])" class="rounded-xl">
                            English
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="hidden lg:block">
                <x-dropdown align="right" width="w-96" contentClasses="aq-topnav__account-surface">
                    <x-slot name="trigger">
                        <button type="button" class="aq-topnav__accountbtn">
                            <span class="aq-topnav__account-avatar">{{ $navInitials }}</span>
                            <span class="aq-topnav__account-copy">
                                <strong>{{ $navUser->name }}</strong>
                                <span>{{ $navRoleLabel }}</span>
                            </span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="aq-topnav__account-panel">
                            <div class="aq-topnav__account-card">
                                <div class="aq-topnav__account-top">
                                    <div class="aq-topnav__account-hero">
                                        <span class="aq-topnav__account-photo">{{ $navInitials }}</span>
                                        <div class="aq-topnav__account-info">
                                            <strong>{{ $navUser->name }}</strong>
                                            <span>{{ $navUser->email }}</span>
                                            <small>{{ $navPhoneLabel }}</small>
                                        </div>
                                    </div>
                                    <span class="aq-topnav__account-tag">{{ $navRoleLabel }}</span>
                                </div>

                                <div class="aq-topnav__account-meta">
                                    <div class="aq-topnav__account-meta-item">
                                        <span>Akses</span>
                                        <strong>{{ $navRoleLabel }}</strong>
                                    </div>
                                    <div class="aq-topnav__account-meta-item">
                                        <span>Device</span>
                                        <strong>{{ $navDeviceCount }} terhubung</strong>
                                    </div>
                                    <div class="aq-topnav__account-meta-item">
                                        <span>Bahasa</span>
                                        <strong>{{ $navLocaleLabel }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="aq-topnav__account-links">
                                <a href="{{ route('profile.edit') }}" class="aq-topnav__account-link">
                                    <span class="aq-topnav__account-link-main">
                                        <span class="aq-topnav__account-link-icon">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6.75a3 3 0 1 1 4.24 4.24L8.5 22H4v-4.5z" />
                                            </svg>
                                        </span>
                                        <span class="aq-topnav__account-link-copy">
                                            <strong>Profil & keamanan</strong>
                                            <span>Perbarui data akun, kata sandi, dan detail login.</span>
                                        </span>
                                    </span>
                                    <span class="aq-topnav__account-link-arrow">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 6 6 6-6 6" />
                                        </svg>
                                    </span>
                                </a>

                                <a href="{{ route('devices.index') }}" class="aq-topnav__account-link">
                                    <span class="aq-topnav__account-link-main">
                                        <span class="aq-topnav__account-link-icon">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 4.75h10.5A1.75 1.75 0 0 1 19 6.5v11A1.75 1.75 0 0 1 17.25 19H6.75A1.75 1.75 0 0 1 5 17.5v-11A1.75 1.75 0 0 1 6.75 4.75Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.5 8.5h7M8.5 12h7M8.5 15.5h4.5" />
                                            </svg>
                                        </span>
                                        <span class="aq-topnav__account-link-copy">
                                            <strong>Perangkat & kolam</strong>
                                            <span>Lihat semua device, lokasi kolam, dan pembacaan sensor.</span>
                                        </span>
                                    </span>
                                    <span class="aq-topnav__account-link-arrow">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 6 6 6-6 6" />
                                        </svg>
                                    </span>
                                </a>

                                <a href="{{ route('user.dashboard') }}" class="aq-topnav__account-link">
                                    <span class="aq-topnav__account-link-main">
                                        <span class="aq-topnav__account-link-icon">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.75 12 12 5l7.25 7v6.25A1.75 1.75 0 0 1 17.5 20h-11a1.75 1.75 0 0 1-1.75-1.75Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.5 20v-5h5v5" />
                                            </svg>
                                        </span>
                                        <span class="aq-topnav__account-link-copy">
                                            <strong>Dashboard utama</strong>
                                            <span>Kembali ke ringkasan farm dan aktivitas terbaru.</span>
                                        </span>
                                    </span>
                                    <span class="aq-topnav__account-link-arrow">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 6 6 6-6 6" />
                                        </svg>
                                    </span>
                                </a>

                                <a href="{{ route('language.switch', ['locale' => $navLocaleSwitch]) }}" class="aq-topnav__account-link">
                                    <span class="aq-topnav__account-link-main">
                                        <span class="aq-topnav__account-link-icon">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6.5h10M9 4v2.5m0 0A12.7 12.7 0 0 1 6 16m3-9.5A12.7 12.7 0 0 0 12 16m0 0 1.5-3m-1.5 3H8.5m10-9.5h1.75A1.75 1.75 0 0 1 22 8.25v7.5A1.75 1.75 0 0 1 20.25 17.5H18.5A1.75 1.75 0 0 1 16.75 15.75v-7.5A1.75 1.75 0 0 1 18.5 6.5Z" />
                                            </svg>
                                        </span>
                                        <span class="aq-topnav__account-link-copy">
                                            <strong>Bahasa</strong>
                                            <span>Ganti cepat ke {{ $navLocaleSwitchLabel }}.</span>
                                        </span>
                                    </span>
                                    <span class="aq-topnav__account-link-arrow">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 6 6 6-6 6" />
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <div class="px-4 pb-4">
                                <button type="submit" class="aq-topnav__account-link aq-topnav__account-linkbtn">
                                    <span class="aq-topnav__account-link-main">
                                        <span class="aq-topnav__account-link-icon">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 8.25V6.5A1.75 1.75 0 0 0 14 4.75H6.5A1.75 1.75 0 0 0 4.75 6.5v11A1.75 1.75 0 0 0 6.5 19.25H14a1.75 1.75 0 0 0 1.75-1.75v-1.75" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.5 12h8.75m0 0-2.75-2.75m2.75 2.75-2.75 2.75" />
                                            </svg>
                                        </span>
                                        <span class="aq-topnav__account-link-copy">
                                            <strong>{{ __('navigation.log_out') }}</strong>
                                            <span>Akhiri sesi saat ini dari dashboard monitoring.</span>
                                        </span>
                                    </span>
                                    <span class="aq-topnav__account-link-arrow">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 6 6 6-6 6" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = ! open" type="button" class="aq-topnav__mobile-toggle lg:hidden" aria-label="Toggle navigation">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                    <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-transition.opacity.duration.200ms class="aq-topnav__mobile-panel lg:hidden" style="display: none;">
        <div class="aq-topnav__mobile-links">
            <a href="{{ route('user.dashboard') }}" class="aq-topnav__mobile-link {{ request()->routeIs('user.dashboard') ? 'is-active' : '' }}">
                {{ __('navigation.dashboard') }}
            </a>
            <a href="{{ route('index-fish') }}" class="aq-topnav__mobile-link {{ request()->routeIs('index-fish') ? 'is-active' : '' }}">
                {{ __('navigation.growth_chart') }}
            </a>
            <a href="{{ route('devices.index') }}" class="aq-topnav__mobile-link {{ request()->routeIs('devices.*') ? 'is-active' : '' }}">
                Devices
            </a>
        </div>

        <div class="aq-topnav__mobile-group">
            <span class="aq-topnav__mobile-label">{{ __('navigation.notifications') }}</span>
            <button id="notifBtnMobile" type="button" class="aq-topnav__mobile-ghost">
                {{ __('navigation.latest_notifications') }}
            </button>
            <div id="notifDropdownMobile" class="aq-topnav__notif-dropdown hidden static w-full">
                <ul id="notifListMobile" class="aq-topnav__notif-list"></ul>
            </div>
        </div>

        <div class="aq-topnav__mobile-group">
            <span class="aq-topnav__mobile-label">{{ __('navigation.language') }}</span>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('language.switch', ['locale' => 'id']) }}" class="aq-topnav__mobile-link">
                    Bahasa Indonesia
                </a>
                <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="aq-topnav__mobile-link">
                    English
                </a>
            </div>
        </div>

        <div class="aq-topnav__mobile-group">
            <span class="aq-topnav__mobile-label">{{ Auth::user()->name }}</span>
            <a href="{{ route('profile.edit') }}" class="aq-topnav__mobile-link">
                {{ __('navigation.profile') }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="aq-topnav__mobile-primary">
                    {{ __('navigation.log_out') }}
                </button>
            </form>
        </div>
    </div>
</nav>

<script>
    const notifBtn = document.getElementById('notifBtn');
    const notifBtnMobile = document.getElementById('notifBtnMobile');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifDropdownMobile = document.getElementById('notifDropdownMobile');
    const notifList = document.getElementById('notifList');
    const notifListMobile = document.getElementById('notifListMobile');
    const latestNotifications = [];
    const noNotificationsText = "{{ __('navigation.no_notifications') }}";

    function renderNotifications(target) {
        if (!target) {
            return;
        }

        target.innerHTML = '';

        if (latestNotifications.length === 0) {
            target.innerHTML = `<li>${noNotificationsText}</li>`;
            return;
        }

        latestNotifications.forEach((item) => {
            target.innerHTML += `<li>${item}</li>`;
        });
    }

    function toggleNotificationPanel(panel, listTarget, otherPanel = null) {
        if (!panel) {
            return;
        }

        if (otherPanel) {
            otherPanel.classList.add('hidden');
        }

        panel.classList.toggle('hidden');

        if (!panel.classList.contains('hidden')) {
            renderNotifications(listTarget);
        }
    }

    notifBtn?.addEventListener('click', function (event) {
        event.stopPropagation();
        toggleNotificationPanel(notifDropdown, notifList, notifDropdownMobile);
    });

    notifBtnMobile?.addEventListener('click', function (event) {
        event.stopPropagation();
        toggleNotificationPanel(notifDropdownMobile, notifListMobile, notifDropdown);
    });

    document.addEventListener('click', function (event) {
        if (notifDropdown && notifBtn && !notifBtn.contains(event.target) && !notifDropdown.contains(event.target)) {
            notifDropdown.classList.add('hidden');
        }

        if (notifDropdownMobile && notifBtnMobile && !notifBtnMobile.contains(event.target) && !notifDropdownMobile.contains(event.target)) {
            notifDropdownMobile.classList.add('hidden');
        }
    });
</script>
