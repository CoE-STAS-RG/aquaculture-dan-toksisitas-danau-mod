<x-app-layout>
    @php
        $timestampOf = function ($reading) {
            return $reading?->reading_time ?? $reading?->created_at;
        };

        $thresholds = [
            'water_temperature' => ['min' => 20, 'max' => 30],
            'ph' => ['min' => 6.5, 'max' => 8.5],
            'dissolved_oxygen' => ['min' => 5],
            'turbidity_ntu' => ['max' => 5],
            'ec_s_m' => ['max' => 0.005],
            'tds_ppm' => ['max' => 500],
            'orp_mv' => ['min' => 100, 'max' => 500],
        ];

        $formatMetric = function ($value, $decimals = 1, $suffix = '') {
            if ($value === null) {
                return '--';
            }

            $formatted = number_format((float) $value, $decimals);
            $formatted = rtrim(rtrim($formatted, '0'), '.');

            return $formatted.$suffix;
        };

        $evaluateReading = function ($reading) use ($thresholds) {
            if (! $reading) {
                return [
                    'label' => 'Belum ada data',
                    'tone' => 'muted',
                    'score' => 0,
                    'issues' => [],
                ];
            }

            $issues = [];

            if ($reading->water_temperature !== null &&
                ($reading->water_temperature < $thresholds['water_temperature']['min'] ||
                    $reading->water_temperature > $thresholds['water_temperature']['max'])) {
                $issues[] = 'Suhu air';
            }

            if ($reading->ph !== null &&
                ($reading->ph < $thresholds['ph']['min'] || $reading->ph > $thresholds['ph']['max'])) {
                $issues[] = 'pH';
            }

            if ($reading->dissolved_oxygen !== null &&
                $reading->dissolved_oxygen < $thresholds['dissolved_oxygen']['min']) {
                $issues[] = 'DO';
            }

            if ($reading->turbidity_ntu !== null &&
                $reading->turbidity_ntu > $thresholds['turbidity_ntu']['max']) {
                $issues[] = 'Turbidity';
            }

            if ($reading->ec_s_m !== null && $reading->ec_s_m > $thresholds['ec_s_m']['max']) {
                $issues[] = 'EC';
            }

            if ($reading->tds_ppm !== null && $reading->tds_ppm > $thresholds['tds_ppm']['max']) {
                $issues[] = 'TDS';
            }

            if ($reading->orp_mv !== null &&
                ($reading->orp_mv < $thresholds['orp_mv']['min'] ||
                    $reading->orp_mv > $thresholds['orp_mv']['max'])) {
                $issues[] = 'ORP';
            }

            $riskScore = (int) round((float) ($reading->risk_level ?? 0) * 100);
            $healthScore = max(12, 100 - (count($issues) * 18) - (int) round($riskScore * 0.35));

            if (count($issues) === 0 && $riskScore < 35) {
                $label = 'Optimal';
                $tone = 'good';
            } elseif (count($issues) <= 2 && $riskScore < 70) {
                $label = 'Perlu perhatian';
                $tone = 'watch';
            } else {
                $label = 'Kritis';
                $tone = 'risk';
            }

            return [
                'label' => $label,
                'tone' => $tone,
                'score' => $healthScore,
                'issues' => $issues,
            ];
        };

        $deviceCards = $devices->map(function ($device) use ($timestampOf, $evaluateReading) {
            $reading = $device->readings->sortByDesc(function ($item) use ($timestampOf) {
                return optional($timestampOf($item))->timestamp ?? 0;
            })->first();

            return [
                'device' => $device,
                'reading' => $reading,
                'status' => $evaluateReading($reading),
                'timestamp' => $timestampOf($reading),
            ];
        })->values();

        $liveDeviceCards = $deviceCards->filter(fn ($card) => $card['reading'] !== null)->values();

        $primaryCard = $liveDeviceCards->sortByDesc(function ($card) {
            return optional($card['timestamp'])->timestamp ?? 0;
        })->first();

        $primaryEditPayload = $primaryCard ? [
            'id' => $primaryCard['device']->id,
            'name' => $primaryCard['device']->name,
            'device_code' => $primaryCard['device']->device_code,
            'location' => $primaryCard['device']->location,
            'description' => $primaryCard['device']->description,
        ] : null;

        $deviceCount = $deviceCards->count();
        $liveDevicesCount = $liveDeviceCards->count();
        $stableDevicesCount = $liveDeviceCards->filter(fn ($card) => $card['status']['tone'] === 'good')->count();
        $issueCount = $liveDeviceCards->sum(fn ($card) => count($card['status']['issues']));

        $avgPhRaw = $liveDeviceCards
            ->filter(fn ($card) => $card['reading']->ph !== null)
            ->avg(fn ($card) => $card['reading']->ph);

        $avgDoRaw = $liveDeviceCards
            ->filter(fn ($card) => $card['reading']->dissolved_oxygen !== null)
            ->avg(fn ($card) => $card['reading']->dissolved_oxygen);

        $avgTempRaw = $liveDeviceCards
            ->filter(fn ($card) => $card['reading']->water_temperature !== null)
            ->avg(fn ($card) => $card['reading']->water_temperature);

        $avgRiskRaw = $liveDeviceCards
            ->filter(fn ($card) => $card['reading']->risk_level !== null)
            ->avg(fn ($card) => $card['reading']->risk_level * 100);

        $latestOverall = $latestReadings->getCollection()->first();
        $latestOverallTime = $latestOverall ? $timestampOf($latestOverall) : null;
        $latestOverallStatus = $latestOverall ? $evaluateReading($latestOverall) : null;
        $lastSync = $latestOverallTime ? $latestOverallTime->diffForHumans() : 'Belum ada sinkronisasi';

        if ($liveDevicesCount === 0) {
            $farmTone = 'muted';
            $farmStatus = 'Menunggu data pertama';
            $farmMessage = 'Hubungkan perangkat dan kirim pembacaan sensor untuk mulai memantau kualitas air.';
        } elseif ($stableDevicesCount === $liveDevicesCount) {
            $farmTone = 'good';
            $farmStatus = 'Air kolam stabil';
            $farmMessage = 'Mayoritas indikator kualitas air berada dalam rentang aman untuk budidaya ikan.';
        } elseif ($stableDevicesCount >= max(1, (int) ceil($liveDevicesCount / 2))) {
            $farmTone = 'watch';
            $farmStatus = 'Perlu perhatian ringan';
            $farmMessage = 'Beberapa sensor mulai keluar dari rentang ideal, tetapi kondisi masih bisa dikendalikan.';
        } else {
            $farmTone = 'risk';
            $farmStatus = 'Perlu tindakan cepat';
            $farmMessage = 'Ada cukup banyak indikator yang menyimpang dan perlu dicek segera di lapangan.';
        }

        $trendLabels = $trendReadings
            ->map(fn ($reading) => optional($reading->reading_time)->format('H:i'))
            ->values();

        $trendTemperature = $trendReadings
            ->map(fn ($reading) => $reading->water_temperature !== null ? round($reading->water_temperature, 2) : null)
            ->values();

        $trendPh = $trendReadings
            ->map(fn ($reading) => $reading->ph !== null ? round($reading->ph, 2) : null)
            ->values();

        $trendOxygen = $trendReadings
            ->map(fn ($reading) => $reading->dissolved_oxygen !== null ? round($reading->dissolved_oxygen, 2) : null)
            ->values();

        $toneClass = function ($tone) {
            return match ($tone) {
                'good' => 'aq-pill-good',
                'watch' => 'aq-pill-watch',
                'risk' => 'aq-pill-risk',
                default => 'aq-pill-muted',
            };
        };
    @endphp

    <style>
        .aq-page {
            --aq-ink: #10243e;
            --aq-muted: #62738a;
            --aq-border: rgba(16, 36, 62, 0.1);
            --aq-primary: #1875d1;
            --aq-primary-deep: #0d3f7a;
            --aq-accent: #31c7be;
            --aq-soft: rgba(255, 255, 255, 0.78);
            --aq-card: rgba(255, 255, 255, 0.88);
            --aq-good: #138968;
            --aq-watch: #dc9b1f;
            --aq-risk: #d25c66;
            --aq-shadow: 0 26px 70px rgba(17, 37, 62, 0.14);
            padding: 32px 16px 48px;
            background:
                radial-gradient(circle at top left, rgba(49, 199, 190, 0.26), transparent 28%),
                radial-gradient(circle at top right, rgba(24, 117, 209, 0.22), transparent 24%),
                linear-gradient(180deg, #f5fbff 0%, #eef5fb 48%, #f8fcff 100%);
        }

        .aq-shell {
            position: relative;
            max-width: 1320px;
            margin: 0 auto;
        }

        .aq-shell::before,
        .aq-shell::after {
            content: "";
            position: absolute;
            z-index: 0;
            border-radius: 999px;
            filter: blur(16px);
            opacity: 0.5;
        }

        .aq-shell::before {
            width: 180px;
            height: 180px;
            top: 56px;
            right: 40px;
            background: rgba(49, 199, 190, 0.22);
        }

        .aq-shell::after {
            width: 220px;
            height: 220px;
            bottom: 40px;
            left: -40px;
            background: rgba(24, 117, 209, 0.14);
        }

        .aq-page a,
        .aq-page button,
        .aq-page input,
        .aq-page textarea {
            font-family: "Figtree", sans-serif;
        }

        .aq-flash,
        .aq-hero,
        .aq-metrics,
        .aq-main-grid,
        .aq-bottom-grid,
        .aq-modal-card {
            position: relative;
            z-index: 1;
        }

        .aq-flash {
            margin-bottom: 18px;
            padding: 14px 18px;
            border-radius: 20px;
            border: 1px solid rgba(19, 137, 104, 0.18);
            background: rgba(255, 255, 255, 0.88);
            color: var(--aq-ink);
            box-shadow: var(--aq-shadow);
        }

        .aq-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(300px, 0.9fr);
            gap: 24px;
            padding: 32px;
            border-radius: 34px;
            overflow: hidden;
            color: #fff;
            background: linear-gradient(135deg, #0c3767 0%, #176bb6 55%, #2cbdb5 100%);
            box-shadow: var(--aq-shadow);
            animation: aq-rise 0.7s ease both;
        }

        .aq-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(7, 31, 58, 0.48) 0%, rgba(17, 95, 162, 0.34) 52%, rgba(39, 182, 176, 0.24) 100%),
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.16), transparent 24%);
            z-index: 1;
            pointer-events: none;
        }

        .aq-hero::after {
            content: "";
            position: absolute;
            inset: auto -12% -48% auto;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            z-index: 1;
            pointer-events: none;
        }

        .aq-hero-video {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .aq-hero-video video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            opacity: 0.88;
            filter: saturate(1.08) contrast(1.04) brightness(0.94);
            transform: scale(1.03);
        }

        .aq-hero-copy,
        .aq-hero-aside {
            position: relative;
            z-index: 2;
        }

        .aq-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 999px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.16);
            color: rgba(255, 255, 255, 0.92);
        }

        .aq-eyebrow::before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #95f5d4;
            box-shadow: 0 0 0 8px rgba(149, 245, 212, 0.16);
        }

        .aq-hero h1 {
            margin: 18px 0 14px;
            max-width: 12ch;
            font-size: clamp(2.2rem, 4vw, 3.8rem);
            line-height: 0.95;
            letter-spacing: -0.05em;
            font-weight: 800;
        }

        .aq-hero p {
            max-width: 62ch;
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 1rem;
            line-height: 1.7;
        }

        .aq-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 26px;
        }

        .aq-btn,
        .aq-btn-inline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-radius: 999px;
            padding: 12px 18px;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease, color 0.25s ease;
        }

        .aq-btn:hover,
        .aq-btn-inline:hover,
        .aq-device-action:hover,
        .aq-device-delete:hover,
        .aq-modal-close:hover,
        .aq-modal-submit:hover {
            transform: translateY(-1px);
        }

        .aq-btn-primary {
            background: #fff;
            color: var(--aq-primary-deep);
            box-shadow: 0 16px 35px rgba(9, 41, 76, 0.22);
        }

        .aq-btn-secondary {
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.24);
            background: rgba(255, 255, 255, 0.1);
        }

        .aq-hero-aside {
            display: grid;
            gap: 16px;
            align-content: start;
        }

        .aq-status-banner,
        .aq-mini-stat {
            padding: 18px 18px 20px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
        }

        .aq-status-banner strong {
            display: block;
            margin-top: 6px;
            font-size: 1.45rem;
            letter-spacing: -0.03em;
        }

        .aq-status-banner p,
        .aq-mini-stat p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.6;
            font-size: 0.92rem;
        }

        .aq-status-label,
        .aq-mini-stat span {
            display: block;
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.75rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .aq-hero-mini-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .aq-mini-stat strong {
            display: block;
            margin-top: 6px;
            font-size: 1.55rem;
            letter-spacing: -0.04em;
        }

        .aq-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-top: 20px;
        }

        .aq-metric-card,
        .aq-panel,
        .aq-device-card {
            background: var(--aq-card);
            border: 1px solid var(--aq-border);
            box-shadow: var(--aq-shadow);
            backdrop-filter: blur(14px);
        }

        .aq-metric-card {
            padding: 22px;
            border-radius: 24px;
            animation: aq-rise 0.7s ease both;
        }

        .aq-metric-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--aq-muted);
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .aq-metric-value {
            margin-top: 16px;
            font-size: clamp(1.8rem, 3vw, 2.7rem);
            line-height: 1;
            letter-spacing: -0.05em;
            color: var(--aq-ink);
            font-weight: 800;
        }

        .aq-metric-foot {
            margin-top: 12px;
            color: var(--aq-muted);
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .aq-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(320px, 0.85fr);
            gap: 20px;
            margin-top: 20px;
        }

        .aq-bottom-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.9fr);
            gap: 20px;
            margin-top: 20px;
        }

        .aq-panel {
            border-radius: 30px;
            padding: 24px;
            animation: aq-rise 0.78s ease both;
        }

        .aq-panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 20px;
        }

        .aq-panel-header h2,
        .aq-panel-header h3 {
            margin: 0;
            color: var(--aq-ink);
            font-size: 1.4rem;
            letter-spacing: -0.04em;
            font-weight: 800;
        }

        .aq-panel-header p {
            margin: 6px 0 0;
            color: var(--aq-muted);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .aq-overview-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
            gap: 18px;
        }

        .aq-pond-stage,
        .aq-chart-card,
        .aq-focus-card {
            border-radius: 28px;
            border: 1px solid var(--aq-border);
        }

        .aq-pond-stage {
            position: relative;
            overflow: hidden;
            padding: 24px;
            min-height: 320px;
            background:
                radial-gradient(circle at top right, rgba(49, 199, 190, 0.24), transparent 24%),
                linear-gradient(160deg, #ffffff 0%, #e9f8ff 64%, #d4f1ff 100%);
            animation: aq-stage-float 4.8s ease-in-out infinite;
        }

        .aq-pond-stage::before,
        .aq-pond-stage::after {
            content: "";
            position: absolute;
            inset: auto -10% 14% auto;
            height: 108px;
            width: 140%;
            border-radius: 999px;
            background: rgba(24, 117, 209, 0.12);
            animation: aq-water-drift 9s ease-in-out infinite;
            opacity: 0.92;
        }

        .aq-pond-stage::after {
            bottom: -2%;
            right: -18%;
            background: rgba(49, 199, 190, 0.2);
            transform: rotate(-4deg);
            animation-duration: 12s;
            animation-direction: reverse;
            opacity: 1;
        }

        .aq-stage-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .aq-stage-title {
            margin: 10px 0 0;
            font-size: 1.5rem;
            color: var(--aq-ink);
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .aq-stage-subtitle {
            margin: 6px 0 0;
            color: var(--aq-muted);
            line-height: 1.7;
            max-width: 42ch;
        }

        .aq-chip-row,
        .aq-readout-grid,
        .aq-focus-stats,
        .aq-activity-grid {
            display: grid;
            gap: 12px;
        }

        .aq-chip-row {
            position: relative;
            z-index: 1;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 22px;
        }

        .aq-chip {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.84);
            box-shadow: 0 10px 24px rgba(17, 37, 62, 0.07);
        }

        .aq-pond-stage .aq-chip,
        .aq-pond-stage .aq-readout,
        .aq-focus-card .aq-chip,
        .aq-focus-card .aq-stat-row {
            animation: aq-rise-soft 0.7s ease both;
        }

        .aq-pond-stage .aq-chip-row .aq-chip:nth-child(1) {
            animation-delay: 0.08s;
        }

        .aq-pond-stage .aq-chip-row .aq-chip:nth-child(2) {
            animation-delay: 0.16s;
        }

        .aq-pond-stage .aq-chip-row .aq-chip:nth-child(3) {
            animation-delay: 0.24s;
        }

        .aq-pond-stage .aq-readout-grid .aq-readout:nth-child(1) {
            animation-delay: 0.28s;
        }

        .aq-pond-stage .aq-readout-grid .aq-readout:nth-child(2) {
            animation-delay: 0.36s;
        }

        .aq-pond-stage .aq-readout-grid .aq-readout:nth-child(3) {
            animation-delay: 0.44s;
        }

        .aq-pond-stage .aq-readout-grid .aq-readout:nth-child(4) {
            animation-delay: 0.52s;
        }

        .aq-chip span {
            display: block;
            color: var(--aq-muted);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .aq-chip strong {
            display: block;
            margin-top: 8px;
            color: var(--aq-ink);
            font-size: 1.35rem;
            letter-spacing: -0.04em;
        }

        .aq-readout-grid {
            position: relative;
            z-index: 1;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 22px;
        }

        .aq-readout {
            padding: 16px;
            border-radius: 20px;
            background: rgba(12, 55, 103, 0.92);
            color: #fff;
            box-shadow: 0 18px 40px rgba(11, 45, 82, 0.22);
        }

        .aq-readout span {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .aq-readout strong {
            display: block;
            margin-top: 8px;
            font-size: 1.3rem;
            letter-spacing: -0.04em;
        }

        .aq-chart-card {
            position: relative;
            overflow: hidden;
            padding: 18px 20px 20px;
            background: rgba(255, 255, 255, 0.86);
        }

        .aq-chart-card::after {
            content: "";
            position: absolute;
            top: -18%;
            left: -24%;
            width: 54%;
            height: 160%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.45), transparent);
            transform: rotate(12deg);
            pointer-events: none;
            animation: aq-card-glint 5.8s ease-in-out infinite;
        }

        .aq-chart-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .aq-chart-meta span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--aq-muted);
            font-size: 0.86rem;
            font-weight: 700;
        }

        .aq-chart-meta span::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 999px;
        }

        .aq-chart-meta .aq-temp::before {
            background: #1875d1;
        }

        .aq-chart-meta .aq-ph::before {
            background: #31c7be;
        }

        .aq-chart-meta .aq-do::before {
            background: #0c3767;
        }

        .aq-chart-box {
            height: 260px;
        }

        .aq-focus-card {
            padding: 24px;
            background:
                radial-gradient(circle at top center, rgba(49, 199, 190, 0.16), transparent 30%),
                linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        }

        .aq-focus-empty {
            display: grid;
            place-items: center;
            min-height: 320px;
            text-align: center;
            color: var(--aq-muted);
            line-height: 1.7;
        }

        .aq-focus-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .aq-focus-device {
            margin: 6px 0 0;
            color: var(--aq-muted);
            line-height: 1.6;
        }

        .aq-gauge {
            position: relative;
            display: grid;
            place-items: center;
            width: 222px;
            height: 222px;
            margin: 26px auto 22px;
            border-radius: 50%;
            background:
                radial-gradient(circle at center, #ffffff 0 44%, transparent 45%),
                conic-gradient(#31c7be 0deg, #1875d1 calc(var(--aq-score) * 1deg), rgba(24, 117, 209, 0.12) 0deg);
            box-shadow:
                inset 0 0 0 18px rgba(255, 255, 255, 0.78),
                0 22px 44px rgba(24, 117, 209, 0.14);
            animation: aq-gauge-float 4.2s ease-in-out infinite;
        }

        .aq-gauge::before {
            content: "";
            position: absolute;
            inset: -14px;
            border-radius: 50%;
            border: 2px solid rgba(49, 199, 190, 0.28);
            opacity: 0.82;
            animation: aq-gauge-pulse 2.4s ease-in-out infinite;
        }

        .aq-gauge::after {
            content: "";
            position: absolute;
            inset: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: inset 0 0 0 1px rgba(16, 36, 62, 0.06);
        }

        .aq-gauge-inner {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .aq-gauge-inner span {
            display: block;
            color: var(--aq-muted);
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
        }

        .aq-gauge-inner strong {
            display: block;
            margin-top: 10px;
            color: var(--aq-ink);
            font-size: 2.6rem;
            line-height: 1;
            letter-spacing: -0.06em;
        }

        .aq-gauge-inner small {
            display: block;
            margin-top: 10px;
            color: var(--aq-muted);
            font-size: 0.9rem;
        }

        .aq-focus-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 18px;
        }

        .aq-focus-card .aq-chip {
            animation-delay: 0.12s;
        }

        .aq-focus-card .aq-focus-stats .aq-stat-row:nth-child(1) {
            animation-delay: 0.18s;
        }

        .aq-focus-card .aq-focus-stats .aq-stat-row:nth-child(2) {
            animation-delay: 0.26s;
        }

        .aq-focus-card .aq-focus-stats .aq-stat-row:nth-child(3) {
            animation-delay: 0.34s;
        }

        .aq-focus-card .aq-focus-stats .aq-stat-row:nth-child(4) {
            animation-delay: 0.42s;
        }

        .aq-stat-row {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(16, 36, 62, 0.04);
            border: 1px solid rgba(16, 36, 62, 0.06);
        }

        .aq-stat-row span {
            display: block;
            color: var(--aq-muted);
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }

        .aq-stat-row strong {
            display: block;
            margin-top: 8px;
            color: var(--aq-ink);
            font-size: 1.1rem;
            letter-spacing: -0.03em;
        }

        .aq-focus-actions {
            display: flex;
            gap: 10px;
            margin-top: 18px;
        }

        .aq-btn-inline {
            padding: 11px 16px;
            border: 1px solid var(--aq-border);
            background: rgba(255, 255, 255, 0.9);
            color: var(--aq-ink);
        }

        .aq-btn-inline-primary {
            background: linear-gradient(135deg, var(--aq-primary), var(--aq-accent));
            border-color: transparent;
            color: #fff;
            box-shadow: 0 16px 34px rgba(24, 117, 209, 0.18);
        }

        .aq-device-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .aq-device-card {
            padding: 18px;
            border-radius: 24px;
            animation: aq-rise 0.82s ease both;
        }

        .aq-device-top,
        .aq-device-meta,
        .aq-device-actions,
        .aq-activity-item-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .aq-device-name {
            margin: 0;
            color: var(--aq-ink);
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .aq-device-subtext,
        .aq-device-code,
        .aq-activity-meta,
        .aq-empty-note {
            color: var(--aq-muted);
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .aq-device-meta {
            margin-top: 14px;
            align-items: center;
        }

        .aq-device-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 16px;
        }

        .aq-device-metric {
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(16, 36, 62, 0.04);
            border: 1px solid rgba(16, 36, 62, 0.06);
        }

        .aq-device-metric span {
            display: block;
            color: var(--aq-muted);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }

        .aq-device-metric strong {
            display: block;
            margin-top: 7px;
            color: var(--aq-ink);
            font-size: 1rem;
        }

        .aq-device-actions {
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .aq-device-action,
        .aq-device-delete {
            border: 0;
            border-radius: 999px;
            padding: 10px 14px;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
        }

        .aq-device-action {
            background: rgba(24, 117, 209, 0.1);
            color: var(--aq-primary-deep);
        }

        .aq-device-action-secondary {
            background: rgba(49, 199, 190, 0.12);
            color: #0f695f;
        }

        .aq-device-delete {
            background: rgba(210, 92, 102, 0.1);
            color: var(--aq-risk);
        }

        .aq-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .aq-pill-good {
            background: rgba(19, 137, 104, 0.1);
            color: var(--aq-good);
        }

        .aq-pill-watch {
            background: rgba(220, 155, 31, 0.12);
            color: var(--aq-watch);
        }

        .aq-pill-risk {
            background: rgba(210, 92, 102, 0.12);
            color: var(--aq-risk);
        }

        .aq-pill-muted {
            background: rgba(98, 115, 138, 0.12);
            color: var(--aq-muted);
        }

        .aq-activity-grid {
            margin-top: 6px;
        }

        .aq-activity-item {
            padding: 16px 0;
            border-top: 1px solid rgba(16, 36, 62, 0.08);
        }

        .aq-activity-item:first-child {
            padding-top: 0;
            border-top: 0;
        }

        .aq-activity-values {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(112px, 1fr));
            gap: 10px;
        }

        .aq-activity-value {
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(16, 36, 62, 0.04);
            border: 1px solid rgba(16, 36, 62, 0.05);
        }

        .aq-activity-value span {
            display: block;
            color: var(--aq-muted);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }

        .aq-activity-value strong {
            display: block;
            margin-top: 7px;
            color: var(--aq-ink);
            font-size: 1rem;
            line-height: 1.4;
        }

        .aq-pagination {
            margin-top: 18px;
        }

        .aq-empty-state {
            padding: 26px;
            border-radius: 24px;
            background: rgba(16, 36, 62, 0.04);
            border: 1px dashed rgba(16, 36, 62, 0.12);
            text-align: center;
            color: var(--aq-muted);
            line-height: 1.7;
        }

        .aq-modal {
            position: fixed;
            inset: 0;
            z-index: 60;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(8, 18, 33, 0.55);
            backdrop-filter: blur(6px);
        }

        .aq-modal.flex {
            display: flex;
        }

        .aq-modal-card {
            width: min(100%, 560px);
            padding: 26px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 30px 70px rgba(8, 18, 33, 0.25);
        }

        .aq-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .aq-modal-head h3 {
            margin: 0;
            color: var(--aq-ink);
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .aq-modal-close {
            border: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            background: rgba(16, 36, 62, 0.06);
            color: var(--aq-ink);
            font-size: 1.1rem;
            font-weight: 800;
        }

        .aq-modal-grid {
            display: grid;
            gap: 16px;
        }

        .aq-modal-field label {
            display: block;
            margin-bottom: 7px;
            color: var(--aq-ink);
            font-size: 0.88rem;
            font-weight: 700;
        }

        .aq-modal-field input,
        .aq-modal-field textarea {
            width: 100%;
            border-radius: 16px;
            border: 1px solid rgba(16, 36, 62, 0.12);
            padding: 13px 15px;
            background: #fff;
            color: var(--aq-ink);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .aq-modal-field input:focus,
        .aq-modal-field textarea:focus {
            border-color: rgba(24, 117, 209, 0.46);
            box-shadow: 0 0 0 4px rgba(24, 117, 209, 0.12);
        }

        .aq-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 18px;
        }

        .aq-modal-cancel,
        .aq-modal-submit {
            border: 0;
            border-radius: 999px;
            padding: 12px 18px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .aq-modal-cancel {
            background: rgba(16, 36, 62, 0.08);
            color: var(--aq-ink);
        }

        .aq-modal-submit {
            background: linear-gradient(135deg, var(--aq-primary), var(--aq-accent));
            color: #fff;
            box-shadow: 0 18px 34px rgba(24, 117, 209, 0.18);
        }

        @keyframes aq-rise {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes aq-rise-soft {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.985);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes aq-stage-float {
            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes aq-water-drift {
            0%,
            100% {
                transform: translate3d(0, 0, 0) rotate(0deg);
            }

            50% {
                transform: translate3d(-7%, 14%, 0) rotate(-3deg);
            }
        }

        @keyframes aq-card-glint {
            0%,
            100% {
                transform: translateX(-150%) rotate(12deg);
                opacity: 0;
            }

            18%,
            62% {
                opacity: 0;
            }

            34% {
                transform: translateX(175%) rotate(12deg);
                opacity: 0.85;
            }
        }

        @keyframes aq-gauge-float {
            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes aq-gauge-pulse {
            0%,
            100% {
                transform: scale(1);
                opacity: 0.45;
            }

            50% {
                transform: scale(1.07);
                opacity: 1;
            }
        }

        @media (max-width: 1200px) {
            .aq-main-grid,
            .aq-bottom-grid,
            .aq-overview-grid,
            .aq-hero {
                grid-template-columns: 1fr;
            }

            .aq-focus-card {
                max-width: 780px;
            }
        }

        @media (max-width: 960px) {
            .aq-metrics,
            .aq-device-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .aq-chip-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .aq-hero {
                padding: 26px;
            }
        }

        @media (max-width: 640px) {
            .aq-page {
                padding: 18px 12px 32px;
            }

            .aq-hero,
            .aq-panel,
            .aq-metric-card,
            .aq-device-card {
                border-radius: 24px;
            }

            .aq-hero h1 {
                max-width: none;
                font-size: 2.35rem;
            }

            .aq-metrics,
            .aq-device-grid,
            .aq-chip-row,
            .aq-readout-grid,
            .aq-focus-stats {
                grid-template-columns: 1fr;
            }

            .aq-hero-mini-grid {
                grid-template-columns: 1fr;
            }

            .aq-device-actions,
            .aq-focus-actions,
            .aq-modal-actions {
                flex-direction: column;
            }

            .aq-gauge {
                width: 196px;
                height: 196px;
            }
        }
    </style>

    <div class="aq-page">
        <div class="aq-shell">
            @if (session('status'))
                <div class="aq-flash">
                    {{ session('status') }}
                </div>
            @endif

            <section class="aq-hero">
                <div class="aq-hero-video" aria-hidden="true">
                    <video autoplay muted loop playsinline preload="auto">
                        <source src="{{ asset('images/_MConverter.eu_vid_mp_.webm') }}" type="video/webm">
                    </video>
                </div>

                <div class="aq-hero-copy">
                    <span class="aq-eyebrow">Aquaculture Monitoring</span>
                    <h1>Dashboard kualitas air untuk ternak ikan.</h1>
                    <p>
                        Tampilan ini saya arahkan ke nuansa monitoring modern seperti referensi, tetapi konteksnya
                        disesuaikan untuk pembacaan suhu air, pH, dissolved oxygen, turbidity, EC, TDS, dan ORP
                        pada kolam budidaya.
                    </p>

                    <div class="aq-hero-actions">
                        <a href="{{ route('devices.create') }}" class="aq-btn aq-btn-primary">Tambah device</a>
                        <a href="{{ route('devices.index') }}" class="aq-btn aq-btn-secondary">Kelola perangkat</a>
                    </div>
                </div>

                <div class="aq-hero-aside">
                    <div class="aq-status-banner">
                        <span class="aq-status-label">Status farm</span>
                        <strong>{{ $farmStatus }}</strong>
                        <p>{{ $farmMessage }}</p>
                    </div>

                    <div class="aq-hero-mini-grid">
                        <div class="aq-mini-stat">
                            <span>Sync terbaru</span>
                            <strong>{{ $lastSync }}</strong>
                            <p>Data diambil dari pembacaan sensor paling baru yang masuk ke dashboard.</p>
                        </div>
                        <div class="aq-mini-stat">
                            <span>Device aktif</span>
                            <strong>{{ $liveDevicesCount }}/{{ $deviceCount }}</strong>
                            <p>{{ $stableDevicesCount }} device berada pada status stabil saat ini.</p>
                        </div>
                        <div class="aq-mini-stat">
                            <span>Risiko rata-rata</span>
                            <strong>{{ $avgRiskRaw !== null ? $formatMetric($avgRiskRaw, 0, '%') : '--' }}</strong>
                            <p>Semakin rendah nilainya, semakin aman kondisi air untuk budidaya.</p>
                        </div>
                        <div class="aq-mini-stat">
                            <span>Isu terdeteksi</span>
                            <strong>{{ $issueCount }}</strong>
                            <p>Total indikator yang sedang keluar dari rentang aman pada device aktif.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="aq-metrics">
                <article class="aq-metric-card" style="animation-delay: 0.05s;">
                    <div class="aq-metric-label">
                        <span>Device terhubung</span>
                        <span>{{ $deviceCount > 0 ? '01' : '00' }}</span>
                    </div>
                    <div class="aq-metric-value">{{ $deviceCount }}</div>
                    <div class="aq-metric-foot">Total unit monitoring yang sudah dipasangkan ke akun ini.</div>
                </article>

                <article class="aq-metric-card" style="animation-delay: 0.1s;">
                    <div class="aq-metric-label">
                        <span>pH rata-rata</span>
                        <span>02</span>
                    </div>
                    <div class="aq-metric-value">{{ $avgPhRaw !== null ? $formatMetric($avgPhRaw, 1) : '--' }}</div>
                    <div class="aq-metric-foot">Target aman umumnya berada di kisaran 6.5 sampai 8.5.</div>
                </article>

                <article class="aq-metric-card" style="animation-delay: 0.15s;">
                    <div class="aq-metric-label">
                        <span>DO rata-rata</span>
                        <span>03</span>
                    </div>
                    <div class="aq-metric-value">{{ $avgDoRaw !== null ? $formatMetric($avgDoRaw, 1, ' mg/L') : '--' }}</div>
                    <div class="aq-metric-foot">Dissolved oxygen yang cukup menjaga ikan tetap aktif dan sehat.</div>
                </article>

                <article class="aq-metric-card" style="animation-delay: 0.2s;">
                    <div class="aq-metric-label">
                        <span>Suhu air rata-rata</span>
                        <span>04</span>
                    </div>
                    <div class="aq-metric-value">{{ $avgTempRaw !== null ? $formatMetric($avgTempRaw, 1, ' C') : '--' }}</div>
                    <div class="aq-metric-foot">Pantau suhu agar tidak keluar dari rentang ideal kolam budidaya.</div>
                </article>
            </section>

            <section class="aq-main-grid">
                <div class="aq-panel">
                    <div class="aq-panel-header">
                        <div>
                            <h2>Ringkasan kolam utama</h2>
                            <p>Panel ini menjadi pusat baca cepat untuk kondisi air yang paling baru masuk.</p>
                        </div>
                        <span class="aq-pill {{ $toneClass($farmTone) }}">{{ $farmStatus }}</span>
                    </div>

                    <div class="aq-overview-grid">
                        <div class="aq-pond-stage">
                            @if ($primaryCard)
                                <div class="aq-stage-top">
                                    <div>
                                        <span class="aq-pill {{ $toneClass($primaryCard['status']['tone']) }}">
                                            {{ $primaryCard['status']['label'] }}
                                        </span>
                                        <h3 class="aq-stage-title">{{ $primaryCard['device']->name }}</h3>
                                        <p class="aq-stage-subtitle">
                                            {{ $primaryCard['device']->location ?: 'Lokasi belum diisi' }}.
                                            Device code: {{ $primaryCard['device']->device_code }}.
                                            Sinkron terakhir {{ optional($primaryCard['timestamp'])->diffForHumans() }}.
                                        </p>
                                    </div>
                                    <div class="aq-chip">
                                        <span>Health score</span>
                                        <strong>{{ $primaryCard['status']['score'] }}/100</strong>
                                    </div>
                                </div>

                                <div class="aq-chip-row">
                            <div class="aq-chip">
                                <span>Suhu air</span>
                                <strong>{{ $formatMetric($primaryCard['reading']->water_temperature, 1, ' C') }}</strong>
                            </div>
                                    <div class="aq-chip">
                                        <span>pH</span>
                                        <strong>{{ $formatMetric($primaryCard['reading']->ph, 1) }}</strong>
                                    </div>
                                    <div class="aq-chip">
                                        <span>DO</span>
                                        <strong>{{ $formatMetric($primaryCard['reading']->dissolved_oxygen, 1, ' mg/L') }}</strong>
                                    </div>
                                </div>

                                <div class="aq-readout-grid">
                                    <div class="aq-readout">
                                        <span>Turbidity</span>
                                        <strong>{{ $formatMetric($primaryCard['reading']->turbidity_ntu, 1, ' NTU') }}</strong>
                                    </div>
                                    <div class="aq-readout">
                                        <span>EC</span>
                                        <strong>{{ $formatMetric($primaryCard['reading']->ec_s_m, 4, ' S/m') }}</strong>
                                    </div>
                                    <div class="aq-readout">
                                        <span>TDS</span>
                                        <strong>{{ $formatMetric($primaryCard['reading']->tds_ppm, 0, ' ppm') }}</strong>
                                    </div>
                                    <div class="aq-readout">
                                        <span>ORP</span>
                                        <strong>{{ $formatMetric($primaryCard['reading']->orp_mv, 0, ' mV') }}</strong>
                                    </div>
                                </div>
                            @else
                                <div class="aq-focus-empty">
                                    <div>
                                        <span class="aq-pill aq-pill-muted">Belum ada data</span>
                                        <p class="aq-empty-note">
                                            Tambahkan perangkat lalu kirim pembacaan sensor agar ringkasan kolam
                                            utama bisa tampil di sini.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="aq-chart-card">
                            <div class="aq-panel-header">
                                <div>
                                    <h3>Tren kualitas air</h3>
                                    <p>12 pembacaan terakhir untuk suhu air, pH, dan dissolved oxygen.</p>
                                </div>
                            </div>

                            <div class="aq-chart-meta">
                                <span class="aq-temp">Suhu air</span>
                                <span class="aq-ph">pH</span>
                                <span class="aq-do">DO</span>
                            </div>

                            @if ($trendLabels->isNotEmpty())
                                <div class="aq-chart-box">
                                    <canvas id="qualityTrendChart"></canvas>
                                </div>
                            @else
                                <div class="aq-focus-empty" style="min-height: 260px;">
                                    <div>
                                        <span class="aq-pill aq-pill-muted">Belum ada tren</span>
                                        <p class="aq-empty-note">
                                            Grafik akan muncul otomatis setelah sensor mengirim beberapa pembacaan.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <aside class="aq-panel aq-focus-card">
                    <div class="aq-panel-header">
                        <div>
                            <h3>Fokus monitoring</h3>
                            <p>Ringkasan cepat untuk satu kolam yang paling baru aktif.</p>
                        </div>
                    </div>

                    @if ($primaryCard)
                        <div class="aq-focus-head">
                            <div>
                                <span class="aq-pill {{ $toneClass($primaryCard['status']['tone']) }}">
                                    {{ $primaryCard['status']['label'] }}
                                </span>
                                <p class="aq-focus-device">
                                    {{ $primaryCard['device']->name }}<br>
                                    {{ $primaryCard['device']->location ?: 'Lokasi belum diisi' }}
                                </p>
                            </div>
                            <div class="aq-chip">
                                <span>Risk</span>
                                <strong>{{ $formatMetric($primaryCard['reading']->risk_level !== null ? $primaryCard['reading']->risk_level * 100 : null, 0, '%') }}</strong>
                            </div>
                        </div>

                        <div class="aq-gauge" style="--aq-score: {{ max(0, min(360, (int) round($primaryCard['status']['score'] * 3.6))) }};">
                            <div class="aq-gauge-inner">
                                <span>Suhu air</span>
                                <strong>{{ $formatMetric($primaryCard['reading']->water_temperature, 1) }}</strong>
                                <small>&deg;C</small>
                            </div>
                        </div>

                        <div class="aq-focus-stats">
                            <div class="aq-stat-row">
                                <span>pH</span>
                                <strong>{{ $formatMetric($primaryCard['reading']->ph, 1) }}</strong>
                            </div>
                            <div class="aq-stat-row">
                                <span>DO</span>
                                <strong>{{ $formatMetric($primaryCard['reading']->dissolved_oxygen, 1, ' mg/L') }}</strong>
                            </div>
                            <div class="aq-stat-row">
                                <span>TDS</span>
                                <strong>{{ $formatMetric($primaryCard['reading']->tds_ppm, 0, ' ppm') }}</strong>
                            </div>
                            <div class="aq-stat-row">
                                <span>ORP</span>
                                <strong>{{ $formatMetric($primaryCard['reading']->orp_mv, 0, ' mV') }}</strong>
                            </div>
                        </div>

                        <div class="aq-focus-actions">
                            <a href="{{ route('devices.show', $primaryCard['device']->id) }}" class="aq-btn-inline aq-btn-inline-primary">
                                Lihat detail
                            </a>
                            <button
                                type="button"
                                class="aq-btn-inline"
                                onclick='openEditModal(@json($primaryEditPayload))'
                            >
                                Edit device
                            </button>
                        </div>
                    @else
                        <div class="aq-focus-empty">
                            <div>
                                <span class="aq-pill aq-pill-muted">Belum ada device aktif</span>
                                <p class="aq-empty-note">
                                    Begitu pembacaan sensor pertama masuk, panel fokus monitoring akan otomatis terisi.
                                </p>
                            </div>
                        </div>
                    @endif
                </aside>
            </section>

            <section class="aq-bottom-grid">
                <div class="aq-panel">
                    <div class="aq-panel-header">
                        <div>
                            <h3>Perangkat dan kolam</h3>
                            <p>Setiap kartu menampilkan status air, pembacaan penting, dan aksi pengelolaan.</p>
                        </div>
                        <a href="{{ route('devices.create') }}" class="aq-btn-inline aq-btn-inline-primary">Tambah device</a>
                    </div>

                    @if ($deviceCards->isEmpty())
                        <div class="aq-empty-state">
                            Belum ada perangkat yang terdaftar. Tambahkan device untuk mulai memonitor kualitas air
                            kolam budidaya.
                        </div>
                    @else
                        <div class="aq-device-grid">
                            @foreach ($deviceCards as $card)
                                @php
                                    $deviceEditPayload = [
                                        'id' => $card['device']->id,
                                        'name' => $card['device']->name,
                                        'device_code' => $card['device']->device_code,
                                        'location' => $card['device']->location,
                                        'description' => $card['device']->description,
                                    ];
                                @endphp
                                <article class="aq-device-card" style="animation-delay: {{ 0.03 * ($loop->index + 1) }}s;">
                                    <div class="aq-device-top">
                                        <div>
                                            <h4 class="aq-device-name">{{ $card['device']->name }}</h4>
                                            <div class="aq-device-subtext">
                                                {{ $card['device']->location ?: 'Lokasi belum diisi' }}
                                            </div>
                                        </div>
                                        <span class="aq-pill {{ $toneClass($card['status']['tone']) }}">
                                            {{ $card['status']['label'] }}
                                        </span>
                                    </div>

                                    <div class="aq-device-meta">
                                        <div class="aq-device-code">Code: {{ $card['device']->device_code }}</div>
                                        <div class="aq-device-subtext">
                                            {{ $card['timestamp'] ? $card['timestamp']->diffForHumans() : 'Belum ada sinkronisasi' }}
                                        </div>
                                    </div>

                                    <div class="aq-device-subtext" style="margin-top: 10px;">
                                        {{ $card['device']->description ? \Illuminate\Support\Str::limit($card['device']->description, 90) : 'Belum ada deskripsi perangkat.' }}
                                    </div>

                                    @if ($card['reading'])
                                        <div class="aq-device-metrics">
                                            <div class="aq-device-metric">
                                                <span>Suhu</span>
                                                <strong>{{ $formatMetric($card['reading']->water_temperature, 1, ' C') }}</strong>
                                            </div>
                                            <div class="aq-device-metric">
                                                <span>pH</span>
                                                <strong>{{ $formatMetric($card['reading']->ph, 1) }}</strong>
                                            </div>
                                            <div class="aq-device-metric">
                                                <span>DO</span>
                                                <strong>{{ $formatMetric($card['reading']->dissolved_oxygen, 1, ' mg/L') }}</strong>
                                            </div>
                                            <div class="aq-device-metric">
                                                <span>Risk</span>
                                                <strong>{{ $formatMetric($card['reading']->risk_level !== null ? $card['reading']->risk_level * 100 : null, 0, '%') }}</strong>
                                            </div>
                                        </div>
                                    @else
                                        <div class="aq-empty-state" style="margin-top: 16px; padding: 18px;">
                                            Device sudah terdaftar, tetapi belum ada data sensor yang masuk.
                                        </div>
                                    @endif

                                    <div class="aq-device-actions">
                                        <a href="{{ route('devices.show', $card['device']->id) }}" class="aq-device-action">
                                            Detail
                                        </a>
                                        <button
                                            type="button"
                                            class="aq-device-action aq-device-action-secondary"
                                            onclick='openEditModal(@json($deviceEditPayload))'
                                        >
                                            Edit
                                        </button>
                                        <form action="{{ route('devices.destroy', $card['device']->id) }}" method="POST" onsubmit="return confirm('Hapus device ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="aq-device-delete">Hapus</button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="aq-panel">
                    <div class="aq-panel-header">
                        <div>
                            <h3>Aktivitas pembacaan terbaru</h3>
                            <p>Feed ini memudahkan Anda melihat nilai kunci sensor tanpa masuk ke detail device.</p>
                        </div>
                    </div>

                    @if (! $latestOverall)
                        <div class="aq-empty-state">
                            Belum ada pembacaan sensor yang tercatat untuk akun ini.
                        </div>
                    @else
                        <div class="aq-activity-grid">
                            <article class="aq-activity-item">
                                <div class="aq-activity-item-top">
                                    <div>
                                        <h4 class="aq-device-name">{{ $latestOverall->device->name }}</h4>
                                        <div class="aq-activity-meta">
                                            {{ $latestOverallTime ? $latestOverallTime->format('d M Y H:i') : '-' }}
                                        </div>
                                    </div>
                                    <span class="aq-pill {{ $toneClass($latestOverallStatus['tone']) }}">
                                        {{ $latestOverallStatus['label'] }}
                                    </span>
                                </div>

                                <div class="aq-activity-values">
                                    <div class="aq-activity-value">
                                        <span>Suhu</span>
                                        <strong>{{ $formatMetric($latestOverall->water_temperature, 1, ' C') }}</strong>
                                    </div>
                                    <div class="aq-activity-value">
                                        <span>pH</span>
                                        <strong>{{ $formatMetric($latestOverall->ph, 1) }}</strong>
                                    </div>
                                    <div class="aq-activity-value">
                                        <span>DO</span>
                                        <strong>{{ $formatMetric($latestOverall->dissolved_oxygen, 1, ' mg/L') }}</strong>
                                    </div>
                                    <div class="aq-activity-value">
                                        <span>Turbidity</span>
                                        <strong>{{ $formatMetric($latestOverall->turbidity_ntu, 1, ' NTU') }}</strong>
                                    </div>
                                    <div class="aq-activity-value">
                                        <span>EC</span>
                                        <strong>{{ $formatMetric($latestOverall->ec_s_m, 4, ' S/m') }}</strong>
                                    </div>
                                    <div class="aq-activity-value">
                                        <span>TDS</span>
                                        <strong>{{ $formatMetric($latestOverall->tds_ppm, 0, ' ppm') }}</strong>
                                    </div>
                                    <div class="aq-activity-value">
                                        <span>ORP</span>
                                        <strong>{{ $formatMetric($latestOverall->orp_mv, 0, ' mV') }}</strong>
                                    </div>
                                </div>

                                <div class="aq-focus-actions" style="margin-top: 12px;">
                                    <a href="{{ route('devices.show', $latestOverall->device->id) }}" class="aq-btn-inline aq-btn-inline-primary">
                                        Lihat semua parameter
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <div id="editModal" class="aq-modal">
        <div class="aq-modal-card">
            <div class="aq-modal-head">
                <h3>Edit device</h3>
                <button type="button" class="aq-modal-close" onclick="closeEditModal()">x</button>
            </div>

            <form id="editDeviceForm" method="POST">
                @csrf
                @method('PUT')

                <div class="aq-modal-grid">
                    <div class="aq-modal-field">
                        <label for="editName">Nama device</label>
                        <input type="text" id="editName" name="name" required>
                    </div>

                    <div class="aq-modal-field">
                        <label for="editCode">Kode device</label>
                        <input type="text" id="editCode" name="device_code" required>
                    </div>

                    <div class="aq-modal-field">
                        <label for="editLocation">Lokasi</label>
                        <input type="text" id="editLocation" name="location">
                    </div>

                    <div class="aq-modal-field">
                        <label for="editDescription">Deskripsi</label>
                        <textarea id="editDescription" name="description" rows="4"></textarea>
                    </div>
                </div>

                <div class="aq-modal-actions">
                    <button type="button" class="aq-modal-cancel" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="aq-modal-submit">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(payload) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editDeviceForm');

            form.action = `/devices/${payload.id}`;
            document.getElementById('editName').value = payload.name ?? '';
            document.getElementById('editCode').value = payload.device_code ?? '';
            document.getElementById('editLocation').value = payload.location ?? '';
            document.getElementById('editDescription').value = payload.description ?? '';

            modal.classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('flex');
        }

        document.getElementById('editModal')?.addEventListener('click', function (event) {
            if (event.target.id === 'editModal') {
                closeEditModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeEditModal();
            }
        });

        const heroVideo = document.querySelector('.aq-hero-video video');

        if (heroVideo) {
            heroVideo.muted = true;
            const playAttempt = heroVideo.play();

            if (playAttempt && typeof playAttempt.catch === 'function') {
                playAttempt.catch(() => {});
            }
        }

        const trendCanvas = document.getElementById('qualityTrendChart');
        const trendLabels = @json($trendLabels);
        const trendTemperature = @json($trendTemperature);
        const trendPh = @json($trendPh);
        const trendOxygen = @json($trendOxygen);

        if (trendCanvas && trendLabels.length > 0 && typeof Chart !== 'undefined') {
            new Chart(trendCanvas, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [
                        {
                            label: 'Suhu air',
                            data: trendTemperature,
                            borderColor: '#1875d1',
                            backgroundColor: 'rgba(24, 117, 209, 0.10)',
                            borderWidth: 3,
                            pointRadius: 3,
                            pointHoverRadius: 4,
                            tension: 0.38,
                            fill: false
                        },
                        {
                            label: 'pH',
                            data: trendPh,
                            borderColor: '#31c7be',
                            backgroundColor: 'rgba(49, 199, 190, 0.12)',
                            borderWidth: 3,
                            pointRadius: 3,
                            pointHoverRadius: 4,
                            tension: 0.38,
                            fill: false
                        },
                        {
                            label: 'DO',
                            data: trendOxygen,
                            borderColor: '#0c3767',
                            backgroundColor: 'rgba(12, 55, 103, 0.14)',
                            borderWidth: 3,
                            pointRadius: 3,
                            pointHoverRadius: 4,
                            tension: 0.38,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(12, 24, 42, 0.92)',
                            padding: 12,
                            titleFont: {
                                family: 'Figtree'
                            },
                            bodyFont: {
                                family: 'Figtree'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#62738a'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(16, 36, 62, 0.08)'
                            },
                            ticks: {
                                color: '#62738a'
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
