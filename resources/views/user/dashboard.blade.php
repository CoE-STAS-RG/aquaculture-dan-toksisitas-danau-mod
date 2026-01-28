<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Dashboard Monitoring Sensor Air') }}
        </h2>
    </x-slot> --}}

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Card Summary -->
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
                <!-- Total Devices Card -->
                <div class="p-6 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Total Perangkat</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ auth()->user()->devices->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Active Devices Card -->
                <div class="p-6 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Perangkat Aktif</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ auth()->user()->devices()->has('readings')->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Last Update Card -->
                <div class="p-6 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full dark:text-purple-100 dark:bg-purple-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Update Terakhir</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                @php
    				$latestReading = $latestReadings->first();
				@endphp
				@if($latestReading)
                                    {{ $latestReading->created_at->diffForHumans() }}
                                @else
                                    Belum ada data
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Device List Section -->
            <div class="mb-6 overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Daftar Perangkat Anda</h3>
                        <a href="{{ route('devices.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Tambah Perangkat
                        </a>
                    </div>

                    @if(auth()->user()->devices->isEmpty())
                        <div class="p-4 text-center text-gray-500 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-400">
                            <p>Anda belum memiliki perangkat. Tambahkan perangkat untuk memulai monitoring.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Nama Perangkat
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Kode Device
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Lokasi
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Update Terakhir
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach(auth()->user()->devices as $device)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-10 h-10">
                                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>

                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $device->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $device->description ? Str::limit($device->description, 30) : '-' }}
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $device->device_code }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $device->location ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($device->readings->isNotEmpty())
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $device->readings->first()->created_at->diffForHumans() }}</div>
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Belum ada data</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap flex space-x-2">
                                                <a href="{{ route('devices.show', $device->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    Detail
                                                </a>

                                                <button onclick="openEditModal({{ $device->id }}, '{{ $device->name }}', '{{ $device->device_code }}', '{{ $device->location ?? '' }}', '{{ $device->description ?? '' }}')" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                    Edit
                                                </button>


                                                <form action="{{ route('devices.destroy', $device->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus perangkat ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>


                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Modal Edit Perangkat -->
            <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center flex bg-black bg-opacity-50">
                <div class="m-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 ">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Edit Perangkat</h2>
                    <form id="editDeviceForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="device_id" id="editDeviceId">

                        <div class="mb-4">
                            <label for="editName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Perangkat</label>
                            <input type="text" id="editName" name="name" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <div class="mb-4">
                            <label for="editCode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Device</label>
                            <input type="text" id="editCode" name="device_code" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <div class="mb-4">
                            <label for="editLocation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                            <input type="text" id="editLocation" name="location" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <div class="mb-4">
                            <label for="editDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea id="editDescription" name="description" rows="3" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-black bg-yellow-600 rounded hover:bg-yellow-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <script>
                function openEditModal(id, name, code, location, description) {
                    document.getElementById('editModal').classList.remove('hidden');
                    document.getElementById('editDeviceForm').action = `/devices/${id}`;
                    document.getElementById('editName').value = name;
                    document.getElementById('editCode').value = code;
                    document.getElementById('editLocation').value = location;
                    document.getElementById('editDescription').value = description;
                }

                function closeEditModal() {
                    document.getElementById('editModal').classList.add('hidden');
                }

                // Optional: Tutup modal jika klik di luar
                window.addEventListener('click', function(e) {
                    const modal = document.getElementById('editModal');
                    if (e.target === modal) {
                        closeEditModal();
                    }
                });
            </script>



            <!-- Recent Readings Section -->
            <div class="overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 bg-white dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Pembacaan Terakhir</h3>

                    @if($latestReadings->isEmpty())
                        <div class="p-4 text-center text-gray-500 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-400">
                            <p>Belum ada data pembacaan dari perangkat Anda.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Perangkat
                                        </th>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Waktu
                                        </th>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Water Temp (°C)
                                        </th>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            pH
                                        </th>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            DO (mg/L)
                                        </th>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Turbidity (NTU)
                                        </th>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            EC (S/m)
                                        </th>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            TDS (PPM)
                                        </th>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            ORP (mV)
                                        </th>
                                        <th scope="col" class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Risiko
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach($latestReadings as $reading)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reading->device->name }}</div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $reading->created_at->format('d M Y H:i') }}</div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $reading->water_temperature ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $reading->ph ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $reading->dissolved_oxygen ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $reading->turbidity_ntu ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $reading->ec_s_m ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $reading->tds_ppm ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $reading->orp_mv ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                @php
                                                    $riskClass = '';
                                                    if ($reading->risk_level > 70) {
                                                        $riskClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                                    } elseif ($reading->risk_level > 30) {
                                                        $riskClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                                    } else {
                                                        $riskClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                                    }
                                                @endphp
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $riskClass }}">
                                                    {{ $reading->risk_level ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div class="mt-4">
                            {{ $latestReadings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
