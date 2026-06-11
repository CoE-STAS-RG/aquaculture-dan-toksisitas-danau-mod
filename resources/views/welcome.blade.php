<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AquaSmart Monitor — Real-Time Water Quality Monitoring | CoE STAS-RG</title>
    <meta name="description" content="Real-time water intelligence from CoE STAS-RG, home of the AquaSmart Buoy — monitoring 7 critical parameters across tambak, danau, dan perairan Indonesia.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('landing_page.css') }}">

    {{-- 3D model viewer (Google) for the hero asset --}}
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.5.0/model-viewer.min.js"></script>
</head>
<body>

    {{-- ============================== NAVBAR ============================== --}}
    <header class="nav">
        <div class="container nav__inner">
            <a href="#top" class="brand">
                <span class="brand__mark">
                    <svg viewBox="0 0 48 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="dropGrad" x1="24" y1="2" x2="24" y2="54" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#48C3CE"/><stop offset="1" stop-color="#1C4E58"/>
                            </linearGradient>
                        </defs>
                        <path d="M24 3C24 3 7 21 7 35a17 17 0 1 0 34 0C41 21 24 3 24 3Z" stroke="url(#dropGrad)" stroke-width="3.5"/>
                        <path d="M16 36c4 1 9-1 12-6 3 5 8 7 12 6-1 7-7 12-12 12s-11-5-12-12Z" fill="url(#dropGrad)"/>
                    </svg>
                </span>
                <span class="brand__text">
                    <span class="brand__name">Aqua Smart Monitor</span>
                    <span class="brand__sub"><i></i>CoE STAS-RG<i></i></span>
                </span>
            </a>

            <nav class="nav__links" id="navLinks">
                <a href="#about">About</a>
                <a href="#team">Team</a>
                <a href="#partners">Partners</a>
                <a href="#contact">Contact</a>
            </nav>

            <div style="display:flex;align-items:center;gap:12px;">
                @auth
                    <a href="{{ url('/user/dashboard') }}" class="btn btn--ghost">DASHBOARD</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn--ghost">LOGIN</a>
                @endauth
                <button class="nav__toggle" id="navToggle" aria-label="Toggle menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    {{-- ============================== HERO ============================== --}}
    <section class="hero" id="top">
        <div class="container hero__grid">
            <div class="hero__content">
                <h1 class="hero__title">Real-Time Precise Water Quality Monitoring System Built for the Field</h1>
                <p class="hero__text">
                    Experience real-time water intelligence from CoE STAS-RG, home of the AquaSmart Buoy,
                    monitoring 7 critical parameters across tambak, danau, dan perairan Indonesia.
                </p>
                <div class="hero__actions">
                    @auth
                        <a href="{{ url('/user/dashboard') }}" class="btn btn--primary">View Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn--primary">View Dashboard</a>
                    @endauth
                    <a href="#about" class="btn btn--light">Publications</a>
                </div>
            </div>

            <div class="hero__visual">
                {{-- Letakkan file 3D di public/models/aquasmart-buoy.glb (ganti src bila nama beda) --}}
                <model-viewer
                    class="hero__model"
                    src="{{ asset('models/aquasmart-buoy.glb') }}"
                    alt="AquaSmart Buoy 3D"
                    camera-controls
                    auto-rotate
                    auto-rotate-delay="0"
                    rotation-per-second="20deg"
                    interaction-prompt="none"
                    shadow-intensity="1"
                    exposure="1"
                    environment-image="neutral"
                    loading="eager"
                    reveal="auto">
                    <span slot="poster" class="hero__image-fallback">Memuat model 3D…<br>(letakkan file di public/models/aquasmart-buoy.glb)</span>
                </model-viewer>

                <div class="live-card" id="liveCard">
                    <div class="live-card__head">
                        <span class="live-dot"></span>
                        Live Sensor Data - <span id="liveDevice">{{ optional(optional($latestReading)->device)->name ?? 'Pond #3' }}</span>
                    </div>
                    <div class="live-card__metrics">
                        <div class="metric">
                            <span class="metric__label">pH</span>
                            <span class="metric__val" id="livePh">{{ $latestReading && $latestReading->ph !== null ? number_format($latestReading->ph, 2) : '—' }}</span>
                        </div>
                        <div class="metric">
                            <span class="metric__label">Suhu</span>
                            <span class="metric__val" id="liveTemp">{{ $latestReading && $latestReading->water_temperature !== null ? number_format($latestReading->water_temperature, 1) . '°' : '—' }}</span>
                        </div>
                        <div class="metric">
                            <span class="metric__label">DO</span>
                            <span class="metric__val" id="liveDo">{{ $latestReading && $latestReading->dissolved_oxygen !== null ? number_format($latestReading->dissolved_oxygen, 1) : '—' }}</span>
                        </div>
                    </div>
                    <div class="live-card__time" id="liveTime">
                        {{ $latestReading && $latestReading->reading_time ? 'Update ' . $latestReading->reading_time->format('H:i:s') : 'Belum ada data masuk' }}
                    </div>
                </div>
                <span class="hero__connector" aria-hidden="true"></span>
            </div>
        </div>
    </section>

    {{-- ============================== IMPACT STATS ============================== --}}
    <section class="section" id="impact">
        <div class="container">
            <h2 class="section__title">Impact Stats</h2>
            <div class="stats__grid">
                <div class="stat">
                    <span class="stat__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h4l3 8 4-16 3 8h4"/></svg>
                    </span>
                    <div>
                        <div class="stat__value">50+</div>
                        <div class="stat__label">Deployed Nodes</div>
                    </div>
                </div>
                <div class="stat">
                    <span class="stat__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/></svg>
                    </span>
                    <div>
                        <div class="stat__value">Real-time AI</div>
                        <div class="stat__label">Processing</div>
                    </div>
                </div>
                <div class="stat">
                    <span class="stat__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h7l-1 8 10-12h-7z"/></svg>
                    </span>
                    <div>
                        <div class="stat__value">Low-Latency</div>
                        <div class="stat__label">Monitoring</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== RESEARCH PILLARS ============================== --}}
    <section class="section pillars" id="about">
        <div class="container">
            <h2 class="section__title">About Our Research Pillars</h2>
            <div class="pillars__grid">
                <article class="pillar">
                    <span class="pillar__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 2v2M15 2v2M9 20v2M15 20v2M2 9h2M2 15h2M20 9h2M20 15h2"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
                    </span>
                    <h3 class="pillar__title">IoT Integration</h3>
                    <p class="pillar__text">Robust sensor network and connectivity for data collection.</p>
                </article>
                <article class="pillar">
                    <span class="pillar__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="2"/><path d="M12 2a4 4 0 0 0-4 4 4 4 0 0 0-2 7 4 4 0 0 0 6 5 4 4 0 0 0 6-5 4 4 0 0 0-2-7 4 4 0 0 0-4-4z"/></svg>
                    </span>
                    <h3 class="pillar__title">AI &amp; Machine Learning</h3>
                    <p class="pillar__text">Predictive analytics for water quality and health.</p>
                </article>
                <article class="pillar">
                    <span class="pillar__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="14" rx="2"/><path d="M7 13l3-3 2 2 4-4M3 21h18"/></svg>
                    </span>
                    <h3 class="pillar__title">Interactive Dashboard</h3>
                    <p class="pillar__text">Real-time visualization and management platform.</p>
                </article>
                <article class="pillar">
                    <span class="pillar__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2s7 4 7 11a7 7 0 0 1-14 0c0-7 7-11 7-11z"/><path d="M12 22V12"/></svg>
                    </span>
                    <h3 class="pillar__title">Sustainability Focus</h3>
                    <p class="pillar__text">Optimizing practices for long-term ecological balance.</p>
                </article>
            </div>
        </div>
    </section>

    {{-- ============================== TEAM ============================== --}}
    <section class="section" id="team">
        <div class="container">
            <h2 class="section__title">Aqua Culture Team</h2>
            <div class="team__grid">
                @php
                    $team = [
                        // 2 slot kosong di depan sesuai layout desain — isi nama/foto atau hapus bila tak perlu
                        ['name' => '', 'role' => '', 'photo' => null],
                        ['name' => '', 'role' => '', 'photo' => null],
                        ['name' => 'Chimika Bintang Fadjirijah', 'role' => 'Mahasiswa', 'photo' => null],
                        ['name' => 'Puteri Chika Arwana', 'role' => 'Mahasiswa', 'photo' => null],
                        ['name' => 'Erlangga Birawa Doza', 'role' => 'Mahasiswa', 'photo' => null],
                        ['name' => 'Renaldy Ariel Santoso', 'role' => 'Mahasiswa', 'photo' => null],
                        ['name' => 'Iffati Feruzia Putri', 'role' => 'Mahasiswa', 'photo' => null],
                        ['name' => 'Idris Syaifulloh', 'role' => 'Mahasiswa', 'photo' => null],
                        ['name' => 'Geo Bayu Febriando Dunggio', 'role' => '3D Designer and UI UX Designer', 'photo' => 'images/team/geo.jpg'],
                    ];
                @endphp
                @foreach ($team as $member)
                    <div class="member">
                        <div class="member__photo"
                             @if (!empty($member['photo'])) style="background-image:url('{{ asset($member['photo']) }}')" @endif>
                            @if (empty($member['photo']) && !empty($member['name'])){{ strtoupper(substr($member['name'], 0, 1)) }}@endif
                        </div>
                        @if (!empty($member['name']))
                            <div>
                                <div class="member__name">{{ $member['name'] }}</div>
                                <div class="member__role">{{ $member['role'] }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== PARTNERS ============================== --}}
    <section class="section partners" id="partners">
        <div class="container">
            <h2 class="section__title">Partners</h2>
            <div class="partners__grid">
                <div class="partner">
                    {{-- Letakkan logo di public/images/partner-btp.png --}}
                    <img src="{{ asset('images/partner-btp.png') }}" alt="Bandung Techno Park"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                    <span class="partner__fallback" style="display:none;">Bandung Techno Park</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== CONTACT ============================== --}}
    <section class="section contact" id="contact">
        <div class="container">
            <div class="contact__grid">
                <div class="contact__item">
                    <span class="contact__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    </span>
                    <div>
                        <div class="contact__label">Email</div>
                        <a class="contact__value" href="mailto:userstas@mail.com">userstas@mail.com</a>
                    </div>
                </div>
                <div class="contact__item">
                    <span class="contact__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </span>
                    <div>
                        <div class="contact__label">Location</div>
                        <div class="contact__value">Universitas Telkom, Bandung</div>
                    </div>
                </div>
                <div class="contact__item">
                    <span class="contact__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>
                    </span>
                    <div>
                        <div class="contact__label">Phone</div>
                        <a class="contact__value" href="tel:+6281315143774">+62 813-1514-3774</a>
                    </div>
                </div>
                <div class="contact__item">
                    <span class="contact__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/></svg>
                    </span>
                    <div>
                        <div class="contact__label">Website</div>
                        <a class="contact__value" href="https://www.stas-rg.com" target="_blank" rel="noopener">www.stas-rg.com</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== FOOTER ============================== --}}
    <footer class="footer">
        <div class="container footer__inner">
            <div class="footer__brand">AquaSmart Monitor</div>
            <div class="footer__copy">Copyright &copy; 2026 STAS-RG — Universitas Telkom.</div>
        </div>
    </footer>

    <script>
        const toggle = document.getElementById('navToggle');
        const links = document.getElementById('navLinks');
        toggle?.addEventListener('click', () => links.classList.toggle('open'));
        links?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => links.classList.remove('open')));

        // ---- Live sensor card: poll latest reading every 10s ----
        (function () {
            const url = "{{ url('/api/sensor-data/latest') }}";
            const fmt = (v, dec, suffix = '') =>
                (v === null || v === undefined || v === '') ? '—' : Number(v).toFixed(dec) + suffix;
            const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

            async function refreshLive() {
                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const { data } = await res.json();
                    if (!data) return;
                    setText('liveDevice', (data.device && data.device.name) ? data.device.name : 'Pond #3');
                    setText('livePh', fmt(data.ph, 2));
                    setText('liveTemp', fmt(data.water_temperature, 1, '°'));
                    setText('liveDo', fmt(data.dissolved_oxygen, 1));
                    if (data.reading_time) {
                        const t = new Date(data.reading_time);
                        setText('liveTime', 'Update ' + t.toLocaleTimeString('id-ID'));
                    }
                } catch (e) { /* abaikan error sementara, coba lagi siklus berikutnya */ }
            }

            refreshLive();
            setInterval(refreshLive, 10000);
        })();
    </script>
</body>
</html>
