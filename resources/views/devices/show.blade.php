<x-app-layout>
   <!-- <h1>HERE</h1> -->

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" dark:bg-gray-800 overflow-hidden  sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Info Box --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-100 shadow-md  dark:bg-blue-900 p-4 rounded">
                            <h3 class="font-bold">Device Code</h3>
                            <p>{{ $device->device_code }}</p>
                        </div>
                        <div class="bg-green-100  shadow-md dark:bg-green-900 p-4 rounded">
                            <h3 class="font-bold">Location</h3>
                            <p>{{ $device->location ?? 'Not set' }}</p>
                        </div>
                        <div class="bg-yellow-100 shadow-md dark:bg-yellow-900 p-4 rounded col-span-2">
                            <h3 class="font-bold">Description</h3>
                            <p>{{ $device->description ?? 'No description' }}</p>
                        </div>
                    </div>

                    {{-- Notification --}}
                    <div class="bg-red-100 shadow-md dark:bg-red-900 p-4 rounded mb-4">
                        <h3 class="font-bold text-red-800 dark:text-white">Alert</h3>
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
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">EC (S/m)</h3>
        <canvas id="ecChart" height="120"></canvas>
    </div>

    {{-- TDS --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">TDS (PPM)</h3>
        <canvas id="tdsChart" height="120"></canvas>
    </div>

    {{-- ORP --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 md:col-span-2">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">ORP (mV)</h3>
        <canvas id="orpChart" height="120"></canvas>
    </div>
</div>

                    {{-- Filter --}}
                    <div class="mb-4 mt-10">
                        <form method="GET" action="{{ route('devices.show', $device->id) }}">
                            <label for="filter" class="mr-2 font-semibold">Filter Waktu:</label>
                            <select name="filter" id="filter" onchange="this.form.submit()" class="dark:bg-gray-700 dark:text-white border rounded px-3 py-1">
                                <option value="">-- Semua --</option>
                                <option value="daily" {{ request('filter') === 'daily' ? 'selected' : '' }}>Harian</option>
                                <option value="weekly" {{ request('filter') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                <option value="monthly" {{ request('filter') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                <option value="yearly" {{ request('filter') === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </form>
                    </div>


                    {{-- Table --}}

                    <h3 class="text-lg font-semibold mb-4">Latest Readings</h3>
<div class="bg-white dark:bg-gray-800 shadow-md overflow-x-auto rounded">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left">
        <thead class="bg-blue-100 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2">Waktu</th>
                <th class="px-4 py-2">Water Temp (°C)</th>
                <th class="px-4 py-2">pH</th>
                <th class="px-4 py-2">DO (mg/L)</th>
                <th class="px-4 py-2">Turbidity (NTU)</th>
                <th class="px-4 py-2">EC (S/m)</th>
                <th class="px-4 py-2">TDS (PPM)</th>
                <th class="px-4 py-2">ORP (mV)</th>
                <th class="px-4 py-2">Risiko</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($readings as $reading)
                <tr>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($reading->reading_time)->format('d-m-Y H:i') }}</td>
                    <td class="px-4 py-2">{{ $reading->water_temperature ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $reading->ph ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $reading->dissolved_oxygen ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $reading->turbidity_ntu ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $reading->ec_s_m ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $reading->tds_ppm ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $reading->orp_mv ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $reading->risk_level ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 px-4 mb-5">
        {{ $readings->withQueryString()->links() }}
    </div>
</div>

                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // === Chart Lama (Suhu, pH, DO) ===
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
    const deviceCode = "{{ $device->device_code }}";

    async function fetchLatestData() {
        try {
            const response = await fetch(`/api/sensor-data/device/${deviceCode}`);
            const result = await response.json();

            if (result.status === "success" && Array.isArray(result.data)) {
                // Ambil max 10 data terbaru
                const latest = result.data.slice(0, 10).reverse();
                const labels = latest.map(r =>
                    new Date(r.reading_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                );

                // Helper: aman untuk null/undefined
                const safeValue = (val) => val == null ? null : parseFloat(val);

                // Update chart lama
                tempChart.data.labels = labels;
                tempChart.data.datasets[0].data = latest.map(r => safeValue(r.water_temperature));
                tempChart.update();

                phChart.data.labels = labels;
                phChart.data.datasets[0].data = latest.map(r => safeValue(r.ph));
                phChart.update();

                doChart.data.labels = labels;
                doChart.data.datasets[0].data = latest.map(r => safeValue(r.dissolved_oxygen));
                doChart.update();

                // Update chart baru
                turbidityChart.data.labels = labels;
                turbidityChart.data.datasets[0].data = latest.map(r => safeValue(r.turbidity_ntu));
                turbidityChart.update();

                ecChart.data.labels = labels;
                ecChart.data.datasets[0].data = latest.map(r => safeValue(r.ec_s_m));
                ecChart.update();

                tdsChart.data.labels = labels;
                tdsChart.data.datasets[0].data = latest.map(r => safeValue(r.tds_ppm));
                tdsChart.update();

                orpChart.data.labels = labels;
                orpChart.data.datasets[0].data = latest.map(r => safeValue(r.orp_mv));
                orpChart.update();

                // Update tabel
                const tableBody = document.querySelector("tbody");
                if (tableBody) {
                    const rows = latest.map(reading => {
                        const time = new Date(reading.reading_time).toLocaleString('id-ID');
                        return `
                            <tr>
                                <td class="px-4 py-2">${time}</td>
                                <td class="px-4 py-2">${safeValue(reading.water_temperature) ?? '-'}</td>
                                <td class="px-4 py-2">${safeValue(reading.ph) ?? '-'}</td>
                                <td class="px-4 py-2">${safeValue(reading.dissolved_oxygen) ?? '-'}</td>
                                <td class="px-4 py-2">${safeValue(reading.turbidity_ntu) ?? '-'}</td>
                                <td class="px-4 py-2">${safeValue(reading.ec_s_m) ?? '-'}</td>
                                <td class="px-4 py-2">${safeValue(reading.tds_ppm) ?? '-'}</td>
                                <td class="px-4 py-2">${safeValue(reading.orp_mv) ?? '-'}</td>
                                <td class="px-4 py-2">${safeValue(reading.risk_level) ?? '-'}</td>
                            </tr>
                        `;
                    }).join("");
                    tableBody.innerHTML = rows;
                }
            }
        } catch (err) {
            console.error('Error fetching sensor data:', err);
        }
    }

    // Jalankan pertama kali
    fetchLatestData();
    setInterval(fetchLatestData, 5000);
</script>


    <script>
        const notifications = @json($notifications);
        let currentIndex = 0;
        const displayElement = document.getElementById('notification-message');

        function showNextNotification() {
            if (notifications.length === 0) {
                displayElement.innerText = "Semua parameter dalam batas normal.";
                return;
            }

            displayElement.innerText = notifications[currentIndex];
            currentIndex = (currentIndex + 1) % notifications.length;
        }

        showNextNotification(); // tampilkan pertama kali
        setInterval(showNextNotification, 3000); // ganti setiap 3 detik
    </script>

</x-app-layout>
