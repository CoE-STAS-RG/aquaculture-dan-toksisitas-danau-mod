<x-app-layout>
   <!-- <h1>HERE</h1> -->

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" dark:bg-gray-800 overflow-hidden  sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Info Box --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-100 shadow-md  dark:bg-blue-900 p-4 rounded">
                            <h3 class="font-bold">{{ __('ui.device_code') }}</h3>
                            <p>{{ $device->device_code }}</p>
                        </div>
                        <div class="bg-green-100  shadow-md dark:bg-green-900 p-4 rounded">
                            <h3 class="font-bold">{{ __('ui.location') }}</h3>
                            <p>{{ $device->location ?? __('ui.not_set') }}</p>
                        </div>
                        <div class="bg-yellow-100 shadow-md dark:bg-yellow-900 p-4 rounded col-span-2">
                            <h3 class="font-bold">{{ __('ui.description') }}</h3>
                            <p>{{ $device->description ?? __('ui.no_description') }}</p>
                        </div>
                    </div>

                    {{-- Notification --}}
                    <div class="bg-red-100 shadow-md dark:bg-red-900 p-4 rounded mb-4">
                        <h3 class="font-bold text-red-800 dark:text-white">{{ __('ui.alert') }}</h3>
                        <div id="notification-message" class="mt-2 text-sm text-red-700 dark:text-red-300"></div>
                    </div>
                   {{-- Grafik Sensor - Grid 2x2 --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
    {{-- Suhu --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Suhu (°C)</h3>
        <canvas id="tempChart" height="120"></canvas>
    </div>

    {{-- pH --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">pH</h3>
        <canvas id="phChart" height="120"></canvas>
    </div>

    {{-- DO --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">DO (mg/L)</h3>
        <canvas id="doChart" height="120"></canvas>
    </div>

    {{-- Turbidity --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Turbidity (NTU)</h3>
        <canvas id="turbidityChart" height="120"></canvas>
    </div>

    {{-- EC --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">EC (µS/cm)</h3>
        <canvas id="ecChart" height="120"></canvas>
    </div>

    {{-- TDS --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">TDS (PPM)</h3>
        <canvas id="tdsChart" height="120"></canvas>
    </div>

    {{-- TDS EC Mod --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">TDS EC Mod (mg/L)</h3>
        <canvas id="tdsEcModChart" height="120"></canvas>
    </div>

    {{-- ORP --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">ORP (mV)</h3>
        <canvas id="orpChart" height="120"></canvas>
    </div>
</div>

                    {{-- Filter --}}
                    <div class="mb-4 mt-10">
                        <form method="GET" action="{{ route('devices.show', $device->id) }}">
                            <label for="filter" class="mr-2 font-semibold">{{ __('ui.filter_time') }}</label>
                            <select name="filter" id="filter" onchange="this.form.submit()" class="dark:bg-gray-700 dark:text-white border rounded px-3 py-1">
                                <option value="">{{ __('ui.all') }}</option>
                                <option value="daily" {{ request('filter') === 'daily' ? 'selected' : '' }}>{{ __('ui.daily') }}</option>
                                <option value="weekly" {{ request('filter') === 'weekly' ? 'selected' : '' }}>{{ __('ui.weekly') }}</option>
                                <option value="monthly" {{ request('filter') === 'monthly' ? 'selected' : '' }}>{{ __('ui.monthly') }}</option>
                                <option value="yearly" {{ request('filter') === 'yearly' ? 'selected' : '' }}>{{ __('ui.yearly') }}</option>
                            </select>
                        </form>
                    </div>


                    {{-- Tabel Sensor Lama --}}
<h3 class="text-lg font-semibold mt-8 flex items-center">
    <span class="inline-flex items-center px-2 py-1 mr-2 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">
        ESP A
    </span>
    {{ __('ui.old_sensor') }}
</h3>
<div class="bg-white dark:bg-gray-800 shadow-md overflow-x-auto rounded">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left">
        <thead class="bg-blue-100 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2">{{ __('ui.time') }}</th>
                <th class="px-4 py-2">Water Temp (°C)</th>
                <th class="px-4 py-2">pH</th>
                <th class="px-4 py-2">DO (mg/L)</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($readingLama as $r)
                <tr>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($r->reading_time)->format('d-m-Y H:i:s') }}</td>
                    <td class="px-4 py-2">{{ $r->water_temperature ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $r->ph ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $r->dissolved_oxygen ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                        {{ __('ui.no_old_sensor_data') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Tabel Sensor Baru --}}
<h3 class="text-lg font-semibold mt-8 flex items-center">
    <span class="inline-flex items-center px-2 py-1 mr-2 text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded">
        ESP B
    </span>
    {{ __('ui.new_sensor') }}
</h3>
<div class="bg-white dark:bg-gray-800 shadow-md overflow-x-auto rounded">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left">
        <thead class="bg-purple-100 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2">{{ __('ui.time') }}</th>
                <th class="px-4 py-2">Turbidity (NTU)</th>
                <th class="px-4 py-2">EC (µS/cm)</th>
                <th class="px-4 py-2">TDS (PPM)</th>
                <th class="px-4 py-2">TDS EC Mod (mg/L)</th>
                <th class="px-4 py-2">ORP (mV)</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($readingBaru as $r)
                <tr>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($r->reading_time)->format('d-m-Y H:i:s') }}</td>
                    <td class="px-4 py-2">{{ $r->turbidity_ntu ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $r->ec_s_m ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $r->tds_ppm ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $r->tds_ec_mod ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $r->orp_mv ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                        {{ __('ui.no_new_sensor_data') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


</div>

                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const tempCtx = document.getElementById('tempChart').getContext('2d');
    const phCtx = document.getElementById('phChart').getContext('2d');
    const doCtx = document.getElementById('doChart').getContext('2d');

    const tempChart = new Chart(tempCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Suhu (°C)',
                data: [],
                backgroundColor: 'rgba(255, 99, 132, 0.3)',
                borderColor: 'rgba(255, 99, 132, 0)',
                borderWidth: 0,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, max: 60, grid: { display: false } }
            }
        }
    });

    const phChart = new Chart(phCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'pH',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.3)',
                borderColor: 'rgba(54, 162, 235, 0)',
                borderWidth: 0,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, max: 14, grid: { display: false } }
            }
        }
    });

    const doChart = new Chart(doCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'DO (mg/L)',
                data: [],
                backgroundColor: 'rgba(75, 192, 192, 0.3)',
                borderColor: 'rgba(75, 192, 192, 0)',
                borderWidth: 0,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { display: false } }
            }
        }
    });

    // === CHART BARU ===
    const turbidityCtx = document.getElementById('turbidityChart').getContext('2d');
    const ecCtx = document.getElementById('ecChart').getContext('2d');
    const tdsCtx = document.getElementById('tdsChart').getContext('2d');
    const orpCtx = document.getElementById('orpChart').getContext('2d');

    const turbidityChart = new Chart(turbidityCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Turbidity (NTU)',
                data: [],
                backgroundColor: 'rgba(255, 159, 64, 0.3)', // orange
                borderColor: 'rgba(255, 159, 64, 0)',
                borderWidth: 0,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { display: false } }
            }
        }
    });

    const ecChart = new Chart(ecCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'EC (S/m)',
                data: [],
                backgroundColor: 'rgba(153, 102, 255, 0.3)', // purple
                borderColor: 'rgba(153, 102, 255, 0)',
                borderWidth: 0,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { display: false } }
            }
        }
    });

    const tdsChart = new Chart(tdsCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'TDS (PPM)',
                data: [],
                backgroundColor: 'rgba(255, 99, 132, 0.3)', // pink
                borderColor: 'rgba(255, 99, 132, 0)',
                borderWidth: 0,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { display: false } }
            }
        }
    });

    const orpChart = new Chart(orpCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'ORP (mV)',
                data: [],
                backgroundColor: 'rgba(75, 75, 75, 0.3)', // gray
                borderColor: 'rgba(75, 75, 75, 0)',
                borderWidth: 0,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: false, grid: { display: false } } // ORP bisa negatif
            }
        }
    });
</script>

    {{-- termometer chart --}}
<script>
    // Helper: aman untuk null
    function safeNum(val) {
        return val == null ? null : parseFloat(val);
    }

    // === DATA UNTUK CHART ===
    const labelsLama = @json($chartLama->pluck('reading_time')->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i')));
    const tempData = @json($chartLama->pluck('water_temperature'));
    const phData = @json($chartLama->pluck('ph'));
    const doData = @json($chartLama->pluck('dissolved_oxygen'));

    const labelsBaru = @json($chartBaru->pluck('reading_time')->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i:s')));
    const turbidityData = @json($chartBaru->pluck('turbidity_ntu'));
    const ecData = @json($chartBaru->pluck('ec_s_m'));
    const tdsData = @json($chartBaru->pluck('tds_ppm'));
    const tdsEcModData = @json($chartBaru->pluck('tds_ec_mod'));
    const orpData = @json($chartBaru->pluck('orp_mv'));

    // === INISIALISASI CHART ===
    new Chart(document.getElementById('tempChart').getContext('2d'), {
        type: 'line',
        data: { labels: labelsLama, datasets: [{ label: 'Suhu (°C)', data: tempData.map(safeNum), borderColor: '#ef4444', backgroundColor: 'rgba(239, 68, 68, 0.1)', fill: true, tension: 0.3 }] },
        options: { responsive: true, scales: { x: { display: false }, y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('phChart').getContext('2d'), {
        type: 'line',
        data: { labels: labelsLama, datasets: [{ label: 'pH', data: phData.map(safeNum), borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.1)', fill: true, tension: 0.3 }] },
        options: { responsive: true, scales: { x: { display: false }, y: { beginAtZero: false, min: 0, max: 14 } } }
    });

    new Chart(document.getElementById('doChart').getContext('2d'), {
        type: 'line',
        data: { labels: labelsLama, datasets: [{ label: 'DO (mg/L)', data: doData.map(safeNum), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.3 }] },
        options: { responsive: true, scales: { x: { display: false }, y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('turbidityChart').getContext('2d'), {
        type: 'line',
        data: { labels: labelsBaru, datasets: [{ label: 'Turbidity (NTU)', data: turbidityData.map(safeNum), borderColor: '#f59e0b', backgroundColor: 'rgba(245, 158, 11, 0.1)', fill: true, tension: 0.3 }] },
        options: { responsive: true, scales: { x: { display: false }, y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('ecChart').getContext('2d'), {
        type: 'line',
        data: { labels: labelsBaru, datasets: [{ label: 'EC (µS/cm)', data: ecData.map(safeNum), borderColor: '#8b5cf6', backgroundColor: 'rgba(139, 92, 246, 0.1)', fill: true, tension: 0.3 }] },
        options: { responsive: true, scales: { x: { display: false }, y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('tdsChart').getContext('2d'), {
        type: 'line',
        data: { labels: labelsBaru, datasets: [{ label: 'TDS (PPM)', data: tdsData.map(safeNum), borderColor: '#ec4899', backgroundColor: 'rgba(236, 72, 153, 0.1)', fill: true, tension: 0.3 }] },
        options: { responsive: true, scales: { x: { display: false }, y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('tdsEcModChart').getContext('2d'), {
        type: 'line',
        data: { labels: labelsBaru, datasets: [{ label: 'TDS EC Mod (mg/L)', data: tdsEcModData.map(safeNum), borderColor: '#0ea5e9', backgroundColor: 'rgba(14, 165, 233, 0.1)', fill: true, tension: 0.3 }] },
        options: { responsive: true, scales: { x: { display: false }, y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('orpChart').getContext('2d'), {
        type: 'line',
        data: { labels: labelsBaru, datasets: [{ label: 'ORP (mV)', data: orpData.map(safeNum), borderColor: '#64748b', backgroundColor: 'rgba(100, 116, 139, 0.1)', fill: true, tension: 0.3 }] },
        options: { responsive: true, scales: { x: { display: false }, y: { beginAtZero: false } } }
    });
</script>


</x-app-layout>
