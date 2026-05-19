<x-app-layout>
    @php
        $profileInitials = collect(explode(' ', trim($user->name ?? 'Akun')))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
        $profileDeviceCount = $user->devices()->count();
        $profileLocaleLabel = session('locale', app()->getLocale()) === 'id' ? 'Indonesia' : 'English';
        $profileRoleLabel = ucfirst(str_replace('_', ' ', $user->role ?? 'member'));
        $profilePhoneLabel = filled($user->phone ?? null) ? $user->phone : 'Belum ditambahkan';
        $profileHomeRoute = ($user->role ?? null) === 'admin' ? route('admin.dashboard') : route('user.dashboard');
    @endphp

    <div class="aq-profile-page">
        <style>
            .aq-profile-page {
                padding: 24px 18px 48px;
                background:
                    radial-gradient(circle at top left, rgba(190, 242, 255, 0.9), transparent 24%),
                    radial-gradient(circle at top right, rgba(191, 219, 254, 0.82), transparent 22%),
                    linear-gradient(180deg, #eff7ff 0%, #f8fbff 52%, #edf6ff 100%);
            }

            .aq-profile-shell {
                max-width: 1320px;
                margin: 0 auto;
            }

            .aq-profile-intro {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 18px;
                margin-bottom: 24px;
            }

            .aq-profile-intro-copy {
                max-width: 720px;
            }

            .aq-profile-kicker {
                display: inline-flex;
                align-items: center;
                padding: 8px 14px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(191, 219, 254, 0.84);
                color: #1b74c8;
                font-size: 0.76rem;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                box-shadow: 0 14px 26px rgba(15, 56, 98, 0.08);
            }

            .aq-profile-intro h1 {
                margin: 16px 0 0;
                color: #133d6c;
                font-size: clamp(2.1rem, 4vw, 3.6rem);
                font-weight: 800;
                letter-spacing: -0.05em;
                line-height: 0.96;
            }

            .aq-profile-intro p {
                margin: 18px 0 0;
                color: #5d7c9d;
                font-size: 1rem;
                line-height: 1.75;
            }

            .aq-profile-chipbar {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-end;
            }

            .aq-profile-chip {
                display: inline-flex;
                flex-direction: column;
                min-width: 160px;
                padding: 14px 16px;
                border-radius: 22px;
                background: rgba(255, 255, 255, 0.78);
                border: 1px solid rgba(191, 219, 254, 0.88);
                box-shadow: 0 18px 34px rgba(15, 56, 98, 0.1);
            }

            .aq-profile-chip strong {
                color: #143a67;
                font-size: 1.15rem;
                font-weight: 800;
                line-height: 1.1;
            }

            .aq-profile-chip span {
                margin-top: 6px;
                color: #6c8aa9;
                font-size: 0.82rem;
                font-weight: 600;
            }

            .aq-profile-grid {
                display: grid;
                grid-template-columns: minmax(300px, 360px) minmax(0, 1fr);
                gap: 22px;
                align-items: start;
            }

            .aq-profile-summary,
            .aq-profile-section {
                border-radius: 34px;
                border: 1px solid rgba(206, 229, 247, 0.95);
                background: rgba(255, 255, 255, 0.92);
                box-shadow: 0 26px 54px rgba(15, 56, 98, 0.12);
                backdrop-filter: blur(14px);
            }

            .aq-profile-summary {
                position: sticky;
                top: 108px;
                overflow: hidden;
            }

            .aq-profile-summary::before {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(circle at top right, rgba(167, 227, 255, 0.46), transparent 34%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(231, 245, 255, 0.56) 100%);
                pointer-events: none;
            }

            .aq-profile-summary-inner {
                position: relative;
                padding: 24px;
            }

            .aq-profile-summary-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .aq-profile-summary-tag {
                display: inline-flex;
                align-items: center;
                padding: 8px 12px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.84);
                color: #1a4b7f;
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .aq-profile-summary-action {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.9);
                color: #1a74cf;
                box-shadow: 0 12px 24px rgba(15, 56, 98, 0.08);
            }

            .aq-profile-avatar {
                width: 180px;
                height: 180px;
                margin: 22px auto 18px;
                border-radius: 38px;
                background: linear-gradient(145deg, #d6f2ff 0%, #b7deff 100%);
                box-shadow:
                    inset 0 1px 0 rgba(255, 255, 255, 0.92),
                    0 22px 40px rgba(17, 88, 156, 0.14);
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #123b69;
                font-size: 3rem;
                font-weight: 800;
                letter-spacing: 0.05em;
            }

            .aq-profile-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .aq-profile-summary-copy {
                text-align: center;
            }

            .aq-profile-summary-copy h2 {
                margin: 0;
                color: #163f6e;
                font-size: 1.4rem;
                font-weight: 800;
                letter-spacing: -0.03em;
            }

            .aq-profile-summary-copy p,
            .aq-profile-summary-copy span {
                display: block;
                margin-top: 8px;
                color: #6888a8;
                line-height: 1.55;
            }

            .aq-profile-summary-pills {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: center;
                margin-top: 18px;
            }

            .aq-profile-summary-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 14px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.84);
                border: 1px solid rgba(226, 232, 240, 0.92);
                color: #1d4e7e;
                font-size: 0.8rem;
                font-weight: 700;
            }

            .aq-profile-summary-stats {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
                margin-top: 22px;
            }

            .aq-profile-stat {
                padding: 16px;
                border-radius: 22px;
                background: rgba(255, 255, 255, 0.9);
                border: 1px solid rgba(226, 232, 240, 0.92);
            }

            .aq-profile-stat span {
                display: block;
                color: #7991ab;
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .aq-profile-stat strong {
                display: block;
                margin-top: 10px;
                color: #143a67;
                font-size: 1.02rem;
                font-weight: 800;
                line-height: 1.3;
            }

            .aq-profile-settings {
                display: grid;
                gap: 18px;
            }

            .aq-profile-section {
                padding: 24px;
            }

            .aq-profile-section--danger {
                border-color: rgba(253, 205, 211, 0.92);
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(255, 245, 247, 0.96) 100%);
            }

            .aq-profile-section-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 20px;
            }

            .aq-profile-section-head h3 {
                margin: 0;
                color: #143a67;
                font-size: 1.22rem;
                font-weight: 800;
                letter-spacing: -0.03em;
            }

            .aq-profile-section-head p {
                margin: 10px 0 0;
                color: #6c8aa9;
                line-height: 1.65;
            }

            .aq-profile-section-badge {
                display: inline-flex;
                align-items: center;
                padding: 8px 12px;
                border-radius: 999px;
                background: rgba(231, 245, 255, 0.92);
                color: #1a74cf;
                font-size: 0.72rem;
                font-weight: 800;
                white-space: nowrap;
            }

            .aq-profile-form-grid {
                display: grid;
                grid-template-columns: minmax(220px, 260px) minmax(0, 1fr);
                gap: 18px;
                align-items: start;
            }

            .aq-profile-upload-card {
                padding: 18px;
                border-radius: 26px;
                background: linear-gradient(180deg, #f9fcff 0%, #eef8ff 100%);
                border: 1px solid rgba(215, 228, 241, 0.94);
            }

            .aq-profile-preview {
                width: 100%;
                aspect-ratio: 1 / 1;
                border-radius: 28px;
                background: linear-gradient(145deg, #d6f2ff 0%, #b7deff 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                color: #123b69;
                font-size: 2.7rem;
                font-weight: 800;
                letter-spacing: 0.05em;
                box-shadow:
                    inset 0 1px 0 rgba(255, 255, 255, 0.94),
                    0 18px 34px rgba(17, 88, 156, 0.12);
            }

            .aq-profile-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .aq-profile-upload-actions {
                margin-top: 16px;
                display: grid;
                gap: 10px;
            }

            .aq-profile-upload-btn,
            .aq-profile-ghost-btn,
            .aq-profile-primary-btn,
            .aq-profile-danger-btn {
                appearance: none;
                border: 0;
                cursor: pointer;
                text-decoration: none;
                transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease, color 0.22s ease;
            }

            .aq-profile-upload-btn,
            .aq-profile-ghost-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                padding: 12px 14px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.92);
                border: 1px solid rgba(209, 220, 234, 0.94);
                color: #244a75;
                font-size: 0.9rem;
                font-weight: 700;
            }

            .aq-profile-upload-btn:hover,
            .aq-profile-ghost-btn:hover,
            .aq-profile-primary-btn:hover,
            .aq-profile-danger-btn:hover {
                transform: translateY(-1px);
            }

            .aq-profile-primary-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 13px 18px;
                border-radius: 18px;
                background: linear-gradient(135deg, #1a74cf 0%, #2cc2d3 100%);
                color: #fff;
                font-size: 0.92rem;
                font-weight: 800;
                box-shadow: 0 16px 28px rgba(26, 116, 207, 0.22);
            }

            .aq-profile-danger-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 13px 18px;
                border-radius: 18px;
                background: linear-gradient(135deg, #f87171 0%, #fb7185 100%);
                color: #fff;
                font-size: 0.92rem;
                font-weight: 800;
                box-shadow: 0 16px 28px rgba(248, 113, 113, 0.22);
            }

            .aq-profile-form-fields {
                display: grid;
                gap: 16px;
            }

            .aq-profile-field {
                display: grid;
                gap: 8px;
            }

            .aq-profile-field label {
                color: #5e7d9f;
                font-size: 0.82rem;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .aq-profile-field input {
                width: 100%;
                padding: 14px 16px;
                border-radius: 18px;
                border: 1px solid rgba(207, 220, 233, 0.94);
                background: #fff;
                color: #143a67;
                font-size: 0.96rem;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92);
            }

            .aq-profile-field input:focus {
                border-color: rgba(56, 189, 248, 0.94);
                outline: none;
                box-shadow: 0 0 0 4px rgba(125, 211, 252, 0.22);
            }

            .aq-profile-inline-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 8px;
            }

            .aq-profile-inline-note {
                display: inline-flex;
                align-items: center;
                padding: 8px 12px;
                border-radius: 14px;
                background: rgba(239, 248, 255, 0.96);
                color: #477094;
                font-size: 0.78rem;
                font-weight: 700;
            }

            .aq-profile-actions {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 12px;
                margin-top: 8px;
            }

            .aq-profile-success {
                display: inline-flex;
                align-items: center;
                padding: 9px 12px;
                border-radius: 14px;
                background: rgba(220, 252, 231, 0.96);
                color: #15803d;
                font-size: 0.82rem;
                font-weight: 700;
            }

            .aq-profile-danger-copy {
                color: #87606d;
            }

            .aq-profile-modal-card {
                padding: 24px;
                border-radius: 28px;
                background: #fff;
            }

            .aq-profile-modal-card h3 {
                margin: 0;
                color: #143a67;
                font-size: 1.24rem;
                font-weight: 800;
            }

            .aq-profile-modal-card p {
                margin: 12px 0 0;
                color: #6c8aa9;
                line-height: 1.7;
            }

            .aq-profile-modal-actions {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                margin-top: 18px;
            }

            @media (max-width: 1100px) {
                .aq-profile-intro {
                    flex-direction: column;
                    align-items: stretch;
                }

                .aq-profile-chipbar {
                    justify-content: flex-start;
                }

                .aq-profile-grid {
                    grid-template-columns: 1fr;
                }

                .aq-profile-summary {
                    position: static;
                }
            }

            @media (max-width: 760px) {
                .aq-profile-page {
                    padding-inline: 14px;
                }

                .aq-profile-section,
                .aq-profile-summary-inner {
                    padding: 18px;
                }

                .aq-profile-form-grid,
                .aq-profile-summary-stats {
                    grid-template-columns: 1fr;
                }

                .aq-profile-chip {
                    min-width: 0;
                    flex: 1 1 150px;
                }
            }
        </style>

        <section class="aq-profile-shell">
            <div class="aq-profile-intro">
                <div class="aq-profile-intro-copy">
                    <span class="aq-profile-kicker">Profile Settings</span>
                    <h1>Kelola akun dan identitas monitoring Anda.</h1>
                    <p>
                        Halaman ini saya rapikan agar selaras dengan dashboard baru. Informasi akun,
                        foto profil, keamanan login, dan penghapusan akun sekarang terkumpul dalam
                        satu layout yang lebih rapi.
                    </p>
                </div>

                <div class="aq-profile-chipbar">
                    <div class="aq-profile-chip">
                        <strong>{{ $profileDeviceCount }}</strong>
                        <span>device terhubung</span>
                    </div>
                    <div class="aq-profile-chip">
                        <strong>{{ $profileLocaleLabel }}</strong>
                        <span>bahasa aktif</span>
                    </div>
                </div>
            </div>

            <div class="aq-profile-grid">
                <aside class="aq-profile-summary">
                    <div class="aq-profile-summary-inner">
                        <div class="aq-profile-summary-top">
                            <span class="aq-profile-summary-tag">{{ $profileRoleLabel }}</span>
                            <a href="{{ $profileHomeRoute }}" class="aq-profile-summary-action" aria-label="Kembali ke dashboard">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 7.5 12 3.75l3.75 3.75M12 4.5v15.75" transform="rotate(-90 12 12)" />
                                </svg>
                            </a>
                        </div>

                        <div class="aq-profile-avatar">
                            @if ($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}">
                            @else
                                <span>{{ $profileInitials }}</span>
                            @endif
                        </div>

                        <div class="aq-profile-summary-copy">
                            <h2>{{ $user->name }}</h2>
                            <p>{{ $user->email }}</p>
                            <span>{{ $profilePhoneLabel }}</span>
                        </div>

                        <div class="aq-profile-summary-pills">
                            <span class="aq-profile-summary-pill">{{ $profileRoleLabel }}</span>
                            <span class="aq-profile-summary-pill">{{ $profileLocaleLabel }}</span>
                        </div>

                        <div class="aq-profile-summary-stats">
                            <div class="aq-profile-stat">
                                <span>Device</span>
                                <strong>{{ $profileDeviceCount }} kolam aktif</strong>
                            </div>
                            <div class="aq-profile-stat">
                                <span>Kontak</span>
                                <strong>{{ filled($user->phone ?? null) ? 'Tersimpan' : 'Belum lengkap' }}</strong>
                            </div>
                            <div class="aq-profile-stat">
                                <span>Login</span>
                                <strong>Akun terlindungi</strong>
                            </div>
                            <div class="aq-profile-stat">
                                <span>Panel</span>
                                <strong>Siap dipakai</strong>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="aq-profile-settings">
                    <div class="aq-profile-section">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="aq-profile-section">
                        @include('profile.partials.update-password-form')
                    </div>

                    <div class="aq-profile-section aq-profile-section--danger">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
