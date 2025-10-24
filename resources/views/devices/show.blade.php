<x-app-layout>
   

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
                    {{-- Grafik Garis --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
                        {{-- Grafik Suhu --}}
                        <div class=" bg-white dark:bg-gray-800 shadow-md p-4 rounded">
                            <h3 class="text-lg font-semibold mb-2">Grafik Suhu</h3>
                            <canvas id="tempChart" height="200"></canvas>
                        </div>

                        {{-- Grafik pH --}}
                        <div class=" bg-white dark:bg-gray-800 shadow-md p-4 rounded">
                            <h3 class="text-lg font-semibold mb-2">Grafik pH</h3>
                            <canvas id="phChart" height="200"></canvas>
                        </div>
                    </div>

                    {{-- Grafik DO --}}
                    <div class="mt-10 bg-white dark:bg-gray-800 shadow-md p-4 rounded">
                        <h3 class="text-lg font-semibold mb-2">Grafik DO</h3>
                        <canvas id="doChart" height="100"></canvas>
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
                    <div class=" bg-white shadow-md  overflow-x-auto">
                            
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left">
                            <thead class="bg-blue-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2">Waktu</th>
                                    <th class="px-4 py-2">Suhu (°C)</th>
                                    <th class="px-4 py-2">pH</th>
                                    <th class="px-4 py-2">DO (mg/L)</th>
                                    <th class="px-4 py-2">Risiko</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($readings as $reading)
                                    <tr>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($reading->reading_time)->format('d-m-Y H:i') }}</td>
                                        <td class="px-4 py-2">{{ $reading->env_temperature }}</td>
                                        <td class="px-4 py-2">{{ $reading->ph }}</td>
                                        <td class="px-4 py-2">{{ $reading->dissolved_oxygen }}</td>
                                        <td class="px-4 py-2">{{ $reading->risk_level }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                          <div class="mt-4 px-4 mb-5">
                                {{ $readings->withQueryString()->links() }}
                            </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 60,
                        grid: {
                            display: false
                        }
                    }
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
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 14,
                        grid: {
                            display: false
                        }
                    }
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
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

    </script>

    {{-- termometer chart --}}
    
    <script>
        const deviceCode = "{{ $device->device_code }}";
        const tableBody = document.querySelector("tbody");

        async function fetchLatestData() {
            try {
                const response = await fetch(`/api/sensor-data?device_code=${deviceCode}`);
                const result = await response.json();

                if (result.status === "success") {
                    const rows = result.data.map(reading => {
                        const time = new Date(reading.reading_time).toLocaleString('id-ID');
                        return `
                            <tr>
                                <td class="px-4 py-2">${time}</td>
                                <td class="px-4 py-2">${reading.env_temperature}</td>
                                <td class="px-4 py-2">${reading.ph}</td>
                                <td class="px-4 py-2">${reading.dissolved_oxygen}</td>
                                <td class="px-4 py-2">${reading.risk_level}</td>
                            </tr>
                        `;
                    }).join("");

                    tableBody.innerHTML = rows;

                    // Ambil 10 data terbaru terakhir (atau sesuaikan)
                    const latest = result.data.slice(0, 10).reverse();
                    const labels = latest.map(r => new Date(r.reading_time).toLocaleTimeString('id-ID'));
                    const temps = latest.map(r => r.env_temperature);
                    const phs = latest.map(r => r.ph);
                    const dos = latest.map(r => r.dissolved_oxygen);

                    tempChart.data.labels = labels;
                    tempChart.data.datasets[0].data = temps;
                    tempChart.update();

                    phChart.data.labels = labels;
                    phChart.data.datasets[0].data = phs;
                    phChart.update();

                    doChart.data.labels = labels;
                    doChart.data.datasets[0].data = dos;
                    doChart.update();
                }
            } catch (err) {
                console.error('Error fetching data:', err);
            }
        }

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


 {{-- <script>
        // Data dummy untuk grafik
        const labelsLive = [];
        const tempsLive = [];
        const phsLive = [];
        const dosLive = [];

        // Inisialisasi grafik
        const tempCtx = document.getElementById('tempChart').getContext('2d');
        const phCtx = document.getElementById('phChart').getContext('2d');
        const doCtx = document.getElementById('doChart').getContext('2d');

        const tempChart = new Chart(tempCtx, {
            type: 'line',
            data: {
                labels: labelsLive,
                datasets: [{
                    label: 'Suhu (°C)',
                    data: tempsLive,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.3)',
                    fill: true
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        const phChart = new Chart(phCtx, {
            type: 'line',
            data: {
                labels: labelsLive,
                datasets: [{
                    label: 'pH',
                    data: phsLive,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.3)',
                    fill: true
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        const doChart = new Chart(doCtx, {
            type: 'line',
            data: {
                labels: labelsLive,
                datasets: [{
                    label: 'DO (mg/L)',
                    data: dosLive,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.3)',
                    fill: true
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

    
        setInterval(() => {
            fetch('/api/sensor-data')
                .then(response => response.json())
                .then(result => {
                    // Ambil data dari response
                    const data = result.data;

                    // Format waktu dari created_at
                    const createdAt = new Date(data.created_at);
                    const timeStr = createdAt.getHours().toString().padStart(2, '0') + ':' +
                                    createdAt.getMinutes().toString().padStart(2, '0') + ' ' +
                                    createdAt.getDate().toString().padStart(2, '0') + '/' +
                                    (createdAt.getMonth() + 1).toString().padStart(2, '0');

                    // Ambil nilai sensor
                    const newTemp = parseFloat(data.env_temperature);
                    const newPh = parseFloat(data.ph);
                    const newDo = parseFloat(data.dissolved_oxygen);

                    // Batasi maksimum 10 data
                    if (labelsLive.length >= 10) {
                        labelsLive.shift();
                        tempsLive.shift();
                        phsLive.shift();
                        dosLive.shift();
                    }

                    labelsLive.push(timeStr);
                    tempsLive.push(newTemp);
                    phsLive.push(newPh);
                    dosLive.push(newDo);

                    // Update chart
                    tempChart.update();
                    phChart.update();
                    doChart.update();
                })
                .catch(error => {
                    console.error('Gagal ambil data sensor:', error);
                });
        }, 3000); // tarik data tiap 3 detik

    </script> --}}
  {{-- const labels = @json($readings->pluck('reading_time')->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i d/m')));

//         // Data masing-masing sensor
//         const temps = @json($readings->pluck('env_temperature'));
//         const phs = @json($readings->pluck('ph'));
//         const dos = @json($readings->pluck('dissolved_oxygen'));

//         // Fungsi buat grafik garis
//         function createLineChart(canvasId, label, data, color) {
//             const ctx = document.getElementById(canvasId).getContext('2d');
//             new Chart(ctx, {
//                 type: 'line',
//                 data: {
//                     labels: labels,
//                     datasets: [{
//                         label: label,
//                         data: data,
//                         borderColor: color,
//                         backgroundColor: color + '33', // transparan
//                         fill: false,
//                         tension: 0.3
//                     }]
//                 },
//                 options: {
//                     responsive: true,
//                     plugins: {
//                         legend: { position: 'top' },
//                         title: { display: true, text: label }
//                     },
//                     scales: {
//                         x: { grid: { display: false } },
//                         y: { grid: { display: false } }
//                     }
//                 }
//             });
//         }

//         // Buat grafik suhu, pH, dan DO
//         createLineChart('tempChart', 'Suhu (°C)', temps, 'rgb(255, 99, 132)');
//         createLineChart('phChart', 'pH', phs, 'rgb(54, 162, 235)');
//         createLineChart('doChart', 'DO (mg/L)', dos, 'rgb(75, 192, 192)');

//             const latestPh = phs.length > 0 ? phs[phs.length - 1] : 0;
//         const latestDo = dos.length > 0 ? dos[dos.length - 1] : 0;

//         // Termometer pH
//         new Chart(document.getElementById('thermoPhChart').getContext('2d'), {
//             type: 'bar',
//             data: {
//                 labels: ['pH'],
//                 datasets: [{
//                     label: 'pH',
//                     data: [latestPh],
//                     backgroundColor: 'rgba(54, 162, 235, 0.8)',
//                     borderWidth: 1
//                 }]
//             },
//             options: {
//                 indexAxis: 'y',
//                 responsive: false,
//                 scales: {
//                     x: {
//                         min: 0,
//                         max: 14,
//                         title: {
//                             display: true,
//                             text: 'pH'
//                         }
//                     }
//                 },
//                 plugins: {
//                     legend: { display: false },
//                     title: {
//                         display: true,
//                         text: `pH Terakhir: ${latestPh}`
//                     }
//                 }
//             }
//         });

//         // Termometer DO
//         new Chart(document.getElementById('thermoDoChart').getContext('2d'), {
//             type: 'bar',
//             data: {
//                 labels: ['DO'],
//                 datasets: [{
//                     label: 'DO (mg/L)',
//                     data: [latestDo],
//                     backgroundColor: 'rgba(75, 192, 192, 0.8)',
//                     borderWidth: 1
//                 }]
//             },
//             options: {
//                 indexAxis: 'y',
//                 responsive: false,
//                 scales: {
//                     x: {
//                         min: 0,
//                         max: 20,
//                         title: {
//                             display: true,
//                             text: 'mg/L'
//                         }
//                     }
//                 },
//                 plugins: {
//                     legend: { display: false },
//                     title: {
//                         display: true,
//                         text: `DO Terakhir: ${latestDo} mg/L`
//                     }
//                 }
//             }
//         });
//         let timeIndex = 1;

//         // Inisialisasi data dummy awal
//         let labelsLive = [...labels];
//         let tempsLive = [...temps];
//         let phsLive = [...phs];
//         let dosLive = [...dos];

//         // Ambil context
//         const tempCtx = document.getElementById('tempChart').getContext('2d');
//         const phCtx = document.getElementById('phChart').getContext('2d');
//         const doCtx = document.getElementById('doChart').getContext('2d');

//         const tempChart = new Chart(tempCtx, {
//             type: 'line',
//             data: {
//                 labels: labelsLive,
//                 datasets: [{
//                     label: 'Suhu (°C)',
//                     data: tempsLive,
//                     borderColor: 'rgb(255, 99, 132)',
//                     backgroundColor: 'rgba(255, 99, 132, 0.2)',
//                     fill: false,
//                     tension: 0.3
//                 }]
//             },
//             options: { responsive: true }
//         });

//         const phChart = new Chart(phCtx, {
//             type: 'line',
//             data: {
//                 labels: labelsLive,
//                 datasets: [{
//                     label: 'pH',
//                     data: phsLive,
//                     borderColor: 'rgb(54, 162, 235)',
//                     backgroundColor: 'rgba(54, 162, 235, 0.2)',
//                     fill: false,
//                     tension: 0.3
//                 }]
//             },
//             options: { responsive: true }
//         });

//         const doChart = new Chart(doCtx, {
//             type: 'line',
//             data: {
//                 labels: labelsLive,
//                 datasets: [{
//                     label: 'DO (mg/L)',
//                     data: dosLive,
//                     borderColor: 'rgb(75, 192, 192)',
//                     backgroundColor: 'rgba(75, 192, 192, 0.2)',
//                     fill: false,
//                     tension: 0.3
//                 }]
//             },
//             options: { responsive: true }
//         }); --}}