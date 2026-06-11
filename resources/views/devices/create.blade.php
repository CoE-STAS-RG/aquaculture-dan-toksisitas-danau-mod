<x-app-layout>
    <style>
        .dc-page {
            --dc-ink: #10243e;
            --dc-muted: #64748b;
            --dc-accent: #1a74cf;
            min-height: calc(100vh - 80px);
            padding: 36px 18px 64px;
            background:
                radial-gradient(circle at top left, rgba(190, 242, 255, 0.82), transparent 28%),
                radial-gradient(circle at top right, rgba(191, 219, 254, 0.72), transparent 26%),
                linear-gradient(180deg, #eff7ff 0%, #f8fbff 52%, #edf6ff 100%);
        }

        .dc-shell {
            max-width: 640px;
            margin: 0 auto;
        }

        /* ── Page intro ── */
        .dc-intro {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 28px;
        }

        .dc-intro-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: var(--dc-accent);
            box-shadow: 0 12px 26px rgba(26, 116, 207, 0.14);
            flex-shrink: 0;
        }

        .dc-intro-copy {
            min-width: 0;
        }

        .dc-intro-copy h1 {
            margin: 0;
            color: var(--dc-ink);
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.15;
        }

        .dc-intro-copy p {
            margin: 5px 0 0;
            color: var(--dc-muted);
            font-size: 0.88rem;
            line-height: 1.55;
        }

        /* ── Form card ── */
        .dc-card {
            padding: 28px;
            border-radius: 32px;
            background:
                radial-gradient(circle at top right, rgba(219, 234, 254, 0.34), transparent 44%),
                rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(191, 219, 254, 0.6);
            box-shadow:
                0 28px 56px rgba(15, 56, 98, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.72);
        }

        /* ── Fields ── */
        .dc-field {
            display: grid;
            gap: 7px;
            margin-bottom: 18px;
        }

        .dc-field:last-of-type {
            margin-bottom: 0;
        }

        .dc-label {
            color: #5e7d9f;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .dc-label .dc-required {
            color: #ef4444;
            margin-left: 2px;
        }

        .dc-input,
        .dc-textarea {
            width: 100%;
            padding: 13px 16px;
            border-radius: 16px;
            border: 1px solid rgba(207, 220, 233, 0.94);
            background: #fff;
            color: var(--dc-ink);
            font-size: 0.95rem;
            font-family: inherit;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92);
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
            outline: none;
        }

        .dc-input:focus,
        .dc-textarea:focus {
            border-color: rgba(56, 189, 248, 0.9);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.92),
                0 0 0 4px rgba(125, 211, 252, 0.2);
        }

        .dc-textarea {
            resize: vertical;
            min-height: 90px;
        }

        .dc-hint {
            color: #94a3b8;
            font-size: 0.76rem;
            margin-top: 2px;
        }

        /* ── Divider ── */
        .dc-divider {
            height: 1px;
            background: rgba(191, 219, 254, 0.5);
            margin: 24px 0;
        }

        /* ── Actions ── */
        .dc-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .dc-btn-cancel {
            display: inline-flex;
            align-items: center;
            padding: 12px 18px;
            border-radius: 16px;
            border: 1px solid rgba(207, 220, 233, 0.94);
            background: rgba(255, 255, 255, 0.88);
            color: var(--dc-muted);
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
        }

        .dc-btn-cancel:hover {
            transform: translateY(-1px);
            background: #fff;
            box-shadow: 0 10px 22px rgba(15, 56, 98, 0.08);
            color: var(--dc-ink);
        }

        .dc-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 22px;
            border-radius: 16px;
            border: 0;
            cursor: pointer;
            font-family: inherit;
            background: linear-gradient(135deg, #1a74cf 0%, #2cc2d3 100%);
            color: #fff;
            font-size: 0.92rem;
            font-weight: 800;
            box-shadow: 0 16px 28px rgba(26, 116, 207, 0.22);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .dc-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 20px 34px rgba(26, 116, 207, 0.28);
        }

        @media (max-width: 520px) {
            .dc-card {
                padding: 20px;
                border-radius: 26px;
            }
            .dc-actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }
            .dc-btn-cancel,
            .dc-btn-primary {
                justify-content: center;
            }
        }
    </style>

    <div class="dc-page">
        <div class="dc-shell">
            <div class="dc-intro">
                <span class="dc-intro-icon">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </span>
                <div class="dc-intro-copy">
                    <h1>{{ __('ui.add_new_device') }}</h1>
                    <p>{{ __('ui.devices_subtitle') }}</p>
                </div>
            </div>

            <div class="dc-card">
                <form method="POST" action="{{ route('devices.store') }}">
                    @csrf

                    <div class="dc-field">
                        <label class="dc-label" for="name">
                            {{ __('ui.device_name') }}<span class="dc-required">*</span>
                        </label>
                        <input
                            id="name"
                            class="dc-input"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            placeholder="e.g. Buoy A1"
                        >
                        <x-input-error :messages="$errors->get('name')" class="dc-hint" />
                    </div>

                    <div class="dc-field">
                        <label class="dc-label" for="location">
                            {{ __('ui.location') }}
                        </label>
                        <input
                            id="location"
                            class="dc-input"
                            type="text"
                            name="location"
                            value="{{ old('location') }}"
                            placeholder="e.g. Pond Zone 1"
                        >
                        <x-input-error :messages="$errors->get('location')" class="dc-hint" />
                    </div>

                    <div class="dc-field">
                        <label class="dc-label" for="description">
                            {{ __('ui.description') }}
                        </label>
                        <textarea
                            id="description"
                            class="dc-textarea"
                            name="description"
                            placeholder="Optional notes about this device…"
                        >{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="dc-hint" />
                    </div>

                    <div class="dc-divider"></div>

                    <div class="dc-actions">
                        <a href="{{ route('devices.index') }}" class="dc-btn-cancel">
                            {{ __('ui.cancel') }}
                        </a>
                        <button type="submit" class="dc-btn-primary">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('ui.save_device') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
