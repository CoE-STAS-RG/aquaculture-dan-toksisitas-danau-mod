<x-app-layout>
    @php
        $formatMetric = function ($value, $decimals = 1, $suffix = '') {
            if ($value === null) {
                return '--';
            }

            $formatted = number_format((float) $value, $decimals);
            $formatted = rtrim(rtrim($formatted, '0'), '.');

            return $formatted.$suffix;
        };

        $latestChartPoint = $chartLama->last();
        $latestSync = $latestChartPoint?->reading_time?->format('d M Y H:i') ?? '--';
        $rowCount = $combinedReadings->total();
        $pageFirstItem = $combinedReadings->firstItem() ?? 0;
        $pageLastItem = $combinedReadings->lastItem() ?? 0;

        $resolveRiskTone = function ($risk) {
            if ($risk === null) {
                return [
                    'label' => 'Belum dinilai',
                    'class' => 'reading-status reading-status--neutral',
                    'score' => '--',
                ];
            }

            $score = number_format((float) $risk * 100, 0).'%';

            if ($risk >= 0.7) {
                return [
                    'label' => 'Kritis',
                    'class' => 'reading-status reading-status--critical',
                    'score' => $score,
                ];
            }

            if ($risk >= 0.35) {
                return [
                    'label' => 'Waspada',
                    'class' => 'reading-status reading-status--warning',
                    'score' => $score,
                ];
            }

            return [
                'label' => 'Optimal',
                'class' => 'reading-status reading-status--safe',
                'score' => $score,
            ];
        };
    @endphp

    <style>
        .device-page {
            --device-ink: #10243e;
            --device-muted: #64748b;
            --device-border: rgba(15, 23, 42, 0.08);
            --device-shadow: 0 22px 50px rgba(15, 23, 42, 0.12);
        }

        .device-meta-card,
        .device-alert-box,
        .metric-card,
        .device-table-shell {
            border: 1px solid var(--device-border);
            box-shadow: var(--device-shadow);
        }

        .device-meta-card h3,
        .device-alert-box h3,
        .metric-card h3,
        .device-section-title {
            color: var(--device-ink);
        }

        .device-meta-card p,
        .device-alert-box p,
        .device-alert-box li,
        .metric-card p,
        .metric-card span,
        .device-table-note {
            color: var(--device-muted);
        }

        .device-table-shell {
            padding: 1.2rem;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.96);
        }

        .device-table-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .device-table-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.82rem;
            border-radius: 999px;
            background: rgba(37, 84, 223, 0.08);
            color: #2554df;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .device-table-heading {
            margin-top: 0.8rem;
            font-size: 1.45rem;
            letter-spacing: -0.04em;
        }

        .device-table-note {
            max-width: 60ch;
            line-height: 1.7;
        }

        .device-table-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .device-table-count {
            min-width: 150px;
            padding: 0.95rem 1rem;
            border-radius: 20px;
            border: 1px solid rgba(37, 84, 223, 0.1);
            background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        }

        .device-table-count strong {
            display: block;
            color: var(--device-ink);
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1.3;
        }

        .device-table-count span {
            display: block;
            margin-top: 0.25rem;
            color: var(--device-muted);
            font-size: 0.78rem;
        }

        .device-table-surface {
            margin-top: 1rem;
            overflow-x: auto;
        }

        .reading-table-head {
            display: grid;
            grid-template-columns: minmax(170px, 2.1fr) repeat(7, minmax(72px, 1fr)) minmax(92px, 1fr) 74px;
            gap: 0.45rem;
            min-width: 0;
            padding: 0 0.5rem 0.65rem;
            color: #8292aa;
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .reading-table-head span:not(:first-child) {
            text-align: center;
        }

        .reading-list {
            display: block;
        }

        .device-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.95rem;
        }

        .device-table tbody {
            display: block;
        }

        .device-table tbody tr {
            display: grid;
            grid-template-columns: minmax(170px, 2.1fr) repeat(7, minmax(72px, 1fr)) minmax(92px, 1fr) 74px;
            gap: 0.45rem;
            min-width: 0;
            padding: 0.75rem;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 20px;
            background: #fff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.06);
            align-items: stretch;
        }

        .device-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 42px rgba(15, 23, 42, 0.09);
        }

        .device-table tbody td {
            padding: 0;
            border: 0;
            min-width: 0;
            vertical-align: top;
        }

        .reading-row {
            display: grid;
            grid-template-columns: minmax(220px, 280px) minmax(0, 1fr) minmax(140px, 170px);
            gap: 1rem;
            padding: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 24px;
            background: #fff;
            background: #fff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.06);
        }

        .reading-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 42px rgba(15, 23, 42, 0.09);
        }

        .reading-product {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            min-width: 0;
        }

        .reading-product__index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 12px;
            background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
            color: #2554df;
            font-size: 0.78rem;
            font-weight: 800;
            flex-shrink: 0;
        }

        .reading-product__title {
            display: block;
            color: var(--device-ink);
            font-size: 0.88rem;
            font-weight: 800;
            line-height: 1.3;
        }

        .reading-product__meta,
        .reading-product__device {
            display: block;
            margin-top: 0.16rem;
            color: var(--device-muted);
            font-size: 0.71rem;
            line-height: 1.35;
        }

        .reading-chip-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
            margin-top: 0.45rem;
        }

        .reading-inline-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.28rem 0.45rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: #4a5c75;
            font-size: 0.64rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .reading-metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(112px, 1fr));
            gap: 0.75rem;
            min-width: 0;
        }

        .reading-value {
            min-width: 0;
            padding: 0.56rem 0.55rem;
            border-radius: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid rgba(148, 163, 184, 0.16);
            text-align: center;
        }

        .reading-value__label {
            display: block;
            color: #91a0b7;
            font-size: 0.56rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .reading-value__number {
            display: block;
            margin-top: 0.28rem;
            color: var(--device-ink);
            font-size: 0.82rem;
            font-weight: 800;
            line-height: 1.3;
            white-space: nowrap;
        }

        .reading-side {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.75rem;
            min-width: 0;
        }

        .reading-status-wrap {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.3rem;
            min-width: 0;
            justify-content: center;
            height: 100%;
        }

        .reading-status {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.48rem;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: 0.65rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .reading-status--safe {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.16);
            color: #059669;
        }

        .reading-status--warning {
            background: rgba(245, 158, 11, 0.12);
            border-color: rgba(245, 158, 11, 0.18);
            color: #b45309;
        }

        .reading-status--critical {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.16);
            color: #dc2626;
        }

        .reading-status--neutral {
            background: rgba(100, 116, 139, 0.08);
            border-color: rgba(100, 116, 139, 0.12);
            color: #475569;
        }

        .reading-risk-score {
            color: var(--device-muted);
            font-size: 0.66rem;
            font-weight: 700;
        }

        .reading-actions {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            justify-content: center;
            height: 100%;
        }

        .table-action-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.42rem 0.36rem;
            border-radius: 11px;
            background: #10243e;
            color: #fff;
            font-size: 0.64rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .table-action-link--soft {
            background: #eef3ff;
            color: #2554df;
        }

        .device-table-empty {
            padding: 3rem 1.25rem;
            border: 1px dashed rgba(148, 163, 184, 0.24);
            border-radius: 22px;
            text-align: center;
            color: var(--device-muted);
            font-weight: 600;
        }

        .device-table-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.85rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(148, 163, 184, 0.16);
            color: var(--device-muted);
            font-size: 0.86rem;
        }

        .device-pagination {
            margin-top: 1rem;
        }

        .metric-board {
            position: relative;
            margin-top: 2rem;
            padding: 1.5rem;
            border-radius: 32px;
            overflow: hidden;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.22), transparent 24%),
                linear-gradient(180deg, #d8e5ff 0%, #90b6ff 28%, #4f86ff 58%, #2554df 100%);
            box-shadow: 0 30px 90px rgba(37, 84, 223, 0.22);
        }

        .metric-board::before {
            content: "";
            position: absolute;
            inset: 18px;
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.04));
            pointer-events: none;
        }

        .metric-board__content {
            position: relative;
            z-index: 1;
        }

        .metric-board__header {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .metric-board__eyebrow {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.85rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.92);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .metric-board__title {
            margin-top: 0.85rem;
            font-size: clamp(1.9rem, 3vw, 2.7rem);
            line-height: 1;
            letter-spacing: -0.05em;
            font-weight: 800;
            color: #fff;
        }

        .metric-board__subtitle {
            max-width: 52ch;
            margin-top: 0.85rem;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.7;
        }

        .metric-board__meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.9rem;
            min-width: min(100%, 320px);
        }

        .metric-meta-card {
            padding: 1rem 1.1rem;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .metric-meta-card span {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .metric-meta-card strong {
            display: block;
            margin-top: 0.55rem;
            color: #fff;
            font-size: 1.1rem;
            line-height: 1.5;
            letter-spacing: -0.03em;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
        }

        .metric-card {
            padding: 1.25rem;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.95);
            min-height: 240px;
        }

        .metric-card--span-7 {
            grid-column: span 7;
        }

        .metric-card--span-5 {
            grid-column: span 5;
        }

        .metric-card--span-6 {
            grid-column: span 6;
        }

        .metric-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.8rem;
        }

        .metric-card__label {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--device-muted);
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .metric-card__label::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--metric-accent, #2563eb);
            box-shadow: 0 0 0 6px var(--metric-soft, rgba(37, 99, 235, 0.12));
        }

        .metric-card__badge {
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            background: var(--metric-soft, rgba(37, 99, 235, 0.12));
            color: var(--metric-accent, #2563eb);
            font-size: 0.75rem;
            font-weight: 700;
        }

        .metric-card h3 {
            margin-top: 0.7rem;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .metric-card__value {
            margin-top: 1rem;
            color: var(--device-ink);
            font-size: clamp(2rem, 3vw, 2.8rem);
            line-height: 1;
            letter-spacing: -0.06em;
            font-weight: 800;
        }

        .metric-card__value small {
            font-size: 1rem;
            color: var(--device-muted);
            letter-spacing: 0;
            font-weight: 700;
        }

        .metric-card__hint {
            margin-top: 0.45rem;
            color: var(--device-muted);
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .metric-card__canvas {
            position: relative;
            margin-top: 1rem;
            height: 150px;
        }

        .metric-card__canvas--large {
            height: 190px;
        }

        .metric-card__canvas canvas {
            width: 100% !important;
            height: 100% !important;
        }

        @media (max-width: 1024px) {
            .metric-card--span-7,
            .metric-card--span-5,
            .metric-card--span-6 {
                grid-column: span 12;
            }

            .reading-table-head,
            .reading-row {
                grid-template-columns: 1fr;
            }

            .device-table tbody tr {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .device-table tbody td {
                grid-column: auto !important;
                grid-row: auto !important;
            }

            .device-table tbody td:nth-child(1),
            .device-table tbody td:nth-child(9),
            .device-table tbody td:nth-child(10) {
                grid-column: 1 / -1 !important;
            }
        }

        @media (max-width: 768px) {
            .metric-board {
                padding: 1rem;
                border-radius: 24px;
            }

            .device-table-shell {
                padding: 1rem;
                border-radius: 24px;
            }

            .device-table-heading {
                font-size: 1.2rem;
            }

            .device-table-count {
                min-width: 100%;
            }

            .reading-table-head {
                display: none;
            }

            .reading-row {
                padding: 0.95rem;
                border-radius: 20px;
            }

            .device-table tbody tr {
                grid-template-columns: 1fr;
                padding: 0.95rem;
                border-radius: 20px;
            }

            .device-table tbody td {
                grid-column: 1 / -1 !important;
                grid-row: auto !important;
            }

            .reading-metrics-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .reading-actions {
                flex-direction: column;
            }

            .metric-board__meta {
                grid-template-columns: 1fr;
                width: 100%;
            }

            .metric-card {
                border-radius: 22px;
            }
        }
    </style>

    <div id="device-top" class="device-page py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
                        <div class="device-meta-card p-4 bg-blue-100 rounded dark:bg-blue-900">
                            <h3 class="font-bold">{{ __('ui.device_code') }}</h3>
                            <p>{{ $device->device_code }}</p>
                        </div>
                        <div class="device-meta-card p-4 bg-green-100 rounded dark:bg-green-900">
                            <h3 class="font-bold">{{ __('ui.location') }}</h3>
                            <p>{{ $device->location ?? __('ui.not_set') }}</p>
                        </div>
                        <div class="device-meta-card p-4 bg-yellow-100 rounded md:col-span-2 dark:bg-yellow-900">
                            <h3 class="font-bold">{{ __('ui.description') }}</h3>
                            <p>{{ $device->description ?? __('ui.no_description') }}</p>
                        </div>
                    </div>

                    <div class="device-alert-box p-4 mb-4 bg-red-100 rounded dark:bg-red-900">
                        <h3 class="font-bold text-red-800 dark:text-white">{{ __('ui.alert') }}</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            @if (count($notifications) === 0)
                                <p>Tidak ada alert saat ini.</p>
                            @else
                                <ul class="space-y-1 list-disc list-inside">
                                    @foreach ($notifications as $notification)
                                        <li>{{ $notification }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <section id="trend-monitor" class="metric-board">
                        <div class="metric-board__content">
                            <div class="metric-board__header">
                                <div>
                                    <span class="metric-board__eyebrow">Trend monitor</span>
                                    <h2 class="metric-board__title">Chart pembacaan kualitas air</h2>
                                    <p class="metric-board__subtitle">
                                        Tampilan chart ini saya ubah ke gaya kartu modern seperti referensi:
                                        lebih visual, lebih ringan dibaca, dan tetap menampilkan 20 titik data terbaru
                                        dari perangkat {{ $device->name }}.
                                    </p>
                                </div>

                                <div class="metric-board__meta">
                                    <div class="metric-meta-card">
                                        <span>Sync terakhir</span>
                                        <strong>{{ $latestSync }}</strong>
                                    </div>
                                    <div class="metric-meta-card">
                                        <span>Device aktif</span>
                                        <strong>{{ $device->name }}<br>{{ $device->location ?? 'Lokasi belum diatur' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="metric-grid">
                                <article class="metric-card metric-card--span-7" style="--metric-accent:#ef4444; --metric-soft:rgba(239,68,68,0.12);">
                                    <div class="metric-card__top">
                                        <div>
                                            <span class="metric-card__label">Water temperature</span>
                                            <h3>Suhu Air</h3>
                                        </div>
                                        <span class="metric-card__badge">20 titik terbaru</span>
                                    </div>
                                    <div class="metric-card__value">{{ $formatMetric($latestChartPoint?->water_temperature, 1) }} <small>C</small></div>
                                    <p class="metric-card__hint">Rentang ideal umum 20 sampai 30 C untuk budidaya.</p>
                                    <div class="metric-card__canvas metric-card__canvas--large">
                                        <canvas id="tempChart"></canvas>
                                    </div>
                                </article>

                                <article class="metric-card metric-card--span-5" style="--metric-accent:#3b82f6; --metric-soft:rgba(59,130,246,0.12);">
                                    <div class="metric-card__top">
                                        <div>
                                            <span class="metric-card__label">Acidity balance</span>
                                            <h3>pH</h3>
                                        </div>
                                        <span class="metric-card__badge">Skala 0-14</span>
                                    </div>
                                    <div class="metric-card__value">{{ $formatMetric($latestChartPoint?->ph, 1) }}</div>
                                    <p class="metric-card__hint">Batas aman umum 6.5 sampai 8.5.</p>
                                    <div class="metric-card__canvas metric-card__canvas--large">
                                        <canvas id="phChart"></canvas>
                                    </div>
                                </article>

                                <article class="metric-card metric-card--span-7" style="--metric-accent:#10b981; --metric-soft:rgba(16,185,129,0.12);">
                                    <div class="metric-card__top">
                                        <div>
                                            <span class="metric-card__label">Oxygen level</span>
                                            <h3>DO</h3>
                                        </div>
                                        <span class="metric-card__badge">Dissolved oxygen</span>
                                    </div>
                                    <div class="metric-card__value">{{ $formatMetric($latestChartPoint?->dissolved_oxygen, 1) }} <small>mg/L</small></div>
                                    <p class="metric-card__hint">Semakin stabil, semakin nyaman untuk ikan.</p>
                                    <div class="metric-card__canvas">
                                        <canvas id="doChart"></canvas>
                                    </div>
                                </article>

                                <article class="metric-card metric-card--span-5" style="--metric-accent:#f59e0b; --metric-soft:rgba(245,158,11,0.12);">
                                    <div class="metric-card__top">
                                        <div>
                                            <span class="metric-card__label">Water clarity</span>
                                            <h3>Turbidity</h3>
                                        </div>
                                        <span class="metric-card__badge">NTU</span>
                                    </div>
                                    <div class="metric-card__value">{{ $formatMetric($latestChartPoint?->turbidity_ntu, 1) }} <small>NTU</small></div>
                                    <p class="metric-card__hint">Pantau kekeruhan agar kualitas air tetap terjaga.</p>
                                    <div class="metric-card__canvas">
                                        <canvas id="turbidityChart"></canvas>
                                    </div>
                                </article>

                                <article class="metric-card metric-card--span-6" style="--metric-accent:#8b5cf6; --metric-soft:rgba(139,92,246,0.12);">
                                    <div class="metric-card__top">
                                        <div>
                                            <span class="metric-card__label">Conductivity</span>
                                            <h3>EC</h3>
                                        </div>
                                        <span class="metric-card__badge">S/m</span>
                                    </div>
                                    <div class="metric-card__value">{{ $formatMetric($latestChartPoint?->ec_s_m, 4) }} <small>S/m</small></div>
                                    <p class="metric-card__hint">Memberi gambaran total ion terlarut di air.</p>
                                    <div class="metric-card__canvas">
                                        <canvas id="ecChart"></canvas>
                                    </div>
                                </article>

                                <article class="metric-card metric-card--span-6" style="--metric-accent:#ec4899; --metric-soft:rgba(236,72,153,0.12);">
                                    <div class="metric-card__top">
                                        <div>
                                            <span class="metric-card__label">Total dissolved solid</span>
                                            <h3>TDS</h3>
                                        </div>
                                        <span class="metric-card__badge">PPM</span>
                                    </div>
                                    <div class="metric-card__value">{{ $formatMetric($latestChartPoint?->tds_ppm, 0) }} <small>ppm</small></div>
                                    <p class="metric-card__hint">Melihat kepadatan partikel terlarut di perairan.</p>
                                    <div class="metric-card__canvas">
                                        <canvas id="tdsChart"></canvas>
                                    </div>
                                </article>

                                <article class="metric-card metric-card--span-7" style="--metric-accent:#0ea5e9; --metric-soft:rgba(14,165,233,0.12);">
                                    <div class="metric-card__top">
                                        <div>
                                            <span class="metric-card__label">Derived value</span>
                                            <h3>TDS EC Mod</h3>
                                        </div>
                                        <span class="metric-card__badge">mg/L</span>
                                    </div>
                                    <div class="metric-card__value">{{ $formatMetric($latestChartPoint?->tds_ec_mod, 1) }} <small>mg/L</small></div>
                                    <p class="metric-card__hint">Nilai turunan untuk pembacaan kualitas air lanjutan.</p>
                                    <div class="metric-card__canvas">
                                        <canvas id="tdsEcModChart"></canvas>
                                    </div>
                                </article>

                                <article class="metric-card metric-card--span-5" style="--metric-accent:#64748b; --metric-soft:rgba(100,116,139,0.12);">
                                    <div class="metric-card__top">
                                        <div>
                                            <span class="metric-card__label">Oxidation reduction</span>
                                            <h3>ORP</h3>
                                        </div>
                                        <span class="metric-card__badge">mV</span>
                                    </div>
                                    <div class="metric-card__value">{{ $formatMetric($latestChartPoint?->orp_mv, 0) }} <small>mV</small></div>
                                    <p class="metric-card__hint">Menunjukkan kecenderungan oksidasi dan reduksi air.</p>
                                    <div class="metric-card__canvas">
                                        <canvas id="orpChart"></canvas>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </section>

                    <div class="mt-10 mb-4">
                        <form method="GET" action="{{ route('devices.show', $device->id) }}">
                            <label for="filter" class="mr-2 font-semibold">{{ __('ui.filter_time') }}</label>
                            <select name="filter" id="filter" onchange="this.form.submit()" class="px-3 py-1 border rounded dark:bg-gray-700 dark:text-white">
                                <option value="">{{ __('ui.all') }}</option>
                                <option value="daily" {{ request('filter') === 'daily' ? 'selected' : '' }}>{{ __('ui.daily') }}</option>
                                <option value="weekly" {{ request('filter') === 'weekly' ? 'selected' : '' }}>{{ __('ui.weekly') }}</option>
                                <option value="monthly" {{ request('filter') === 'monthly' ? 'selected' : '' }}>{{ __('ui.monthly') }}</option>
                                <option value="yearly" {{ request('filter') === 'yearly' ? 'selected' : '' }}>{{ __('ui.yearly') }}</option>
                            </select>
                        </form>
                    </div>

                    <section id="sensor-table" class="mt-8">
                        <div class="device-table-shell">
                            <div class="device-table-toolbar">
                                <div>
                                    <span class="device-table-pill">Sensor log</span>
                                    <h3 class="device-section-title device-table-heading">Pembacaan Semua Sensor</h3>
                                    <p class="device-table-note mt-1 text-sm">
                                        Tabel ini saya ubah ke gaya data table modern seperti referensi. Tujuh parameter utama
                                        tampil langsung di baris, dan semua data sensor sekarang bisa dibuka lewat nomor halaman di bawah.
                                    </p>
                                </div>

                                <div class="device-table-summary">
                                    <div class="device-table-count">
                                        <strong>{{ number_format($rowCount, 0, ',', '.') }}</strong>
                                        <span>total data sensor</span>
                                    </div>
                                    <div class="device-table-count">
                                        <strong>{{ $latestSync }}</strong>
                                        <span>sinkron terakhir</span>
                                    </div>
                                </div>
                            </div>

                            <div class="device-table-surface">
                                <div class="reading-table-head">
                                    <span>Pembacaan</span>
                                    <span>Suhu</span>
                                    <span>pH</span>
                                    <span>DO</span>
                                    <span>Turbidity</span>
                                    <span>EC</span>
                                    <span>TDS</span>
                                    <span>ORP</span>
                                    <span>Status</span>
                                    <span>Aksi</span>
                                </div>

                                <div class="reading-list">
                                    <table class="device-table">
                                        <tbody>
                                    @forelse($combinedReadings as $index => $reading)
                                            @php
                                                $readingTime = $reading->reading_time ? \Carbon\Carbon::parse($reading->reading_time) : null;
                                                $riskTone = $resolveRiskTone($reading->risk_level);
                                                $displayIndex = $pageFirstItem + $index;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="reading-product">
                                                        <span class="reading-product__index">{{ str_pad((string) $displayIndex, 2, '0', STR_PAD_LEFT) }}</span>
                                                        <div>
                                                            <strong class="reading-product__title">{{ $device->name }}</strong>
                                                            <span class="reading-product__meta">
                                                                {{ $readingTime ? $readingTime->format('d M Y') : '--' }}
                                                                ·
                                                                {{ $readingTime ? $readingTime->format('H:i:s') : '--' }}
                                                            </span>
                                                            <span class="reading-product__device">{{ $device->device_code }}</span>
                                                            <div class="reading-chip-stack">
                                                                <span class="reading-inline-chip">Env {{ $formatMetric($reading->env_temperature, 1, ' C') }}</span>
                                                                <span class="reading-inline-chip">TDS EC Mod {{ $formatMetric($reading->tds_ec_mod, 1, ' mg/L') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="reading-value">
                                                        <span class="reading-value__label">Water</span>
                                                        <span class="reading-value__number">{{ $formatMetric($reading->water_temperature, 1, ' C') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="reading-value">
                                                        <span class="reading-value__label">Acidity</span>
                                                        <span class="reading-value__number">{{ $formatMetric($reading->ph, 1) }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="reading-value">
                                                        <span class="reading-value__label">Oxygen</span>
                                                        <span class="reading-value__number">{{ $formatMetric($reading->dissolved_oxygen, 1, ' mg/L') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="reading-value">
                                                        <span class="reading-value__label">Clarity</span>
                                                        <span class="reading-value__number">{{ $formatMetric($reading->turbidity_ntu, 1, ' NTU') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="reading-value">
                                                        <span class="reading-value__label">Conduct</span>
                                                        <span class="reading-value__number">{{ $formatMetric($reading->ec_s_m, 4, ' S/m') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="reading-value">
                                                        <span class="reading-value__label">Solid</span>
                                                        <span class="reading-value__number">{{ $formatMetric($reading->tds_ppm, 0, ' ppm') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="reading-value">
                                                        <span class="reading-value__label">Redox</span>
                                                        <span class="reading-value__number">{{ $formatMetric($reading->orp_mv, 0, ' mV') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="reading-status-wrap">
                                                        <span class="{{ $riskTone['class'] }}">{{ $riskTone['label'] }}</span>
                                                        <span class="reading-risk-score">{{ $riskTone['score'] }} risiko</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="reading-actions">
                                                        <a class="table-action-link" href="#trend-monitor">Chart</a>
                                                        <a class="table-action-link table-action-link--soft" href="#device-top">Atas</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="device-table-empty">
                                                    Belum ada pembacaan sensor untuk device ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="device-table-footer">
                                <span>
                                    Menampilkan {{ number_format($pageFirstItem, 0, ',', '.') }}-{{ number_format($pageLastItem, 0, ',', '.') }}
                                    dari {{ number_format($rowCount, 0, ',', '.') }} data sensor untuk perangkat {{ $device->name }}.
                                </span>
                                <span>{{ $device->location ?? 'Lokasi perangkat belum diatur.' }}</span>
                            </div>

                            @if ($combinedReadings->hasPages())
                                <div class="device-pagination">
                                    {{ $combinedReadings->onEachSide(1)->links() }}
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function safeNum(value) {
            return value == null ? null : parseFloat(value);
        }

        function buildLineChart(config) {
            const canvas = document.getElementById(config.id);

            if (!canvas) {
                return;
            }

            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.clientHeight || 220);
            gradient.addColorStop(0, config.fillTop);
            gradient.addColorStop(1, config.fillBottom);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: config.labels,
                    datasets: [{
                        label: config.label,
                        data: config.data.map(safeNum),
                        borderColor: config.borderColor,
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointRadius: 2.5,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: config.borderColor,
                        pointBorderWidth: 2,
                        fill: true,
                        tension: 0.42
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.92)',
                            padding: 12,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: config.beginAtZero ?? true,
                            min: config.min,
                            max: config.max,
                            ticks: {
                                color: '#74839b',
                                font: {
                                    size: 11
                                },
                                padding: 8
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.18)',
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }

        const labelsBasic = @json($chartLama->pluck('reading_time')->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i')));
        const tempData = @json($chartLama->pluck('water_temperature'));
        const phData = @json($chartLama->pluck('ph'));
        const doData = @json($chartLama->pluck('dissolved_oxygen'));

        const labelsQuality = @json($chartBaru->pluck('reading_time')->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i')));
        const turbidityData = @json($chartBaru->pluck('turbidity_ntu'));
        const ecData = @json($chartBaru->pluck('ec_s_m'));
        const tdsData = @json($chartBaru->pluck('tds_ppm'));
        const tdsEcModData = @json($chartBaru->pluck('tds_ec_mod'));
        const orpData = @json($chartBaru->pluck('orp_mv'));

        buildLineChart({
            id: 'tempChart',
            labels: labelsBasic,
            data: tempData,
            label: 'Suhu Air (C)',
            borderColor: '#ef4444',
            fillTop: 'rgba(239, 68, 68, 0.24)',
            fillBottom: 'rgba(239, 68, 68, 0.02)'
        });

        buildLineChart({
            id: 'phChart',
            labels: labelsBasic,
            data: phData,
            label: 'pH',
            borderColor: '#3b82f6',
            fillTop: 'rgba(59, 130, 246, 0.24)',
            fillBottom: 'rgba(59, 130, 246, 0.02)',
            beginAtZero: false,
            min: 0,
            max: 14
        });

        buildLineChart({
            id: 'doChart',
            labels: labelsBasic,
            data: doData,
            label: 'DO (mg/L)',
            borderColor: '#10b981',
            fillTop: 'rgba(16, 185, 129, 0.24)',
            fillBottom: 'rgba(16, 185, 129, 0.02)'
        });

        buildLineChart({
            id: 'turbidityChart',
            labels: labelsQuality,
            data: turbidityData,
            label: 'Turbidity (NTU)',
            borderColor: '#f59e0b',
            fillTop: 'rgba(245, 158, 11, 0.24)',
            fillBottom: 'rgba(245, 158, 11, 0.02)'
        });

        buildLineChart({
            id: 'ecChart',
            labels: labelsQuality,
            data: ecData,
            label: 'EC (S/m)',
            borderColor: '#8b5cf6',
            fillTop: 'rgba(139, 92, 246, 0.24)',
            fillBottom: 'rgba(139, 92, 246, 0.02)'
        });

        buildLineChart({
            id: 'tdsChart',
            labels: labelsQuality,
            data: tdsData,
            label: 'TDS (PPM)',
            borderColor: '#ec4899',
            fillTop: 'rgba(236, 72, 153, 0.24)',
            fillBottom: 'rgba(236, 72, 153, 0.02)'
        });

        buildLineChart({
            id: 'tdsEcModChart',
            labels: labelsQuality,
            data: tdsEcModData,
            label: 'TDS EC Mod (mg/L)',
            borderColor: '#0ea5e9',
            fillTop: 'rgba(14, 165, 233, 0.24)',
            fillBottom: 'rgba(14, 165, 233, 0.02)'
        });

        buildLineChart({
            id: 'orpChart',
            labels: labelsQuality,
            data: orpData,
            label: 'ORP (mV)',
            borderColor: '#64748b',
            fillTop: 'rgba(100, 116, 139, 0.24)',
            fillBottom: 'rgba(100, 116, 139, 0.02)',
            beginAtZero: false
        });
    </script>
</x-app-layout>
