<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">Monitoring Ikan</h2>
    </x-slot>

    <div class="p-6">
        <!-- Tombol untuk memunculkan modal -->
        <button class="btn btn-primary mb-3" data-modal-target="modal-tambah" data-modal-toggle="modal-tambah">
            + Tambah Data
        </button>
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y shadow-md divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Ikan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pakan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat Pakan (gr)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse ($feedings as $feeding)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $feeding->fish_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $feeding->feed_type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $feeding->feeding_time }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $feeding->feed_weight }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-8">
    <h3 class="text-lg font-semibold mb-2">Grafik Pertumbuhan Ikan</h3>
    <canvas id="growthChart" height="100"></canvas>
</div>


<script>
    const ctx = document.getElementById('growthChart').getContext('2d');
    const growthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Berat Ikan (gram)',
                data: @json($weights),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#1D4ED8',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                title: {
                    display: true,
                    text: 'Pertumbuhan Ikan per Waktu'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Berat (gr)' }
                },
                x: {
                    title: { display: true, text: 'Tanggal' }
                }
            }
        }
    });
</script>

        </div>

    <!-- Modal -->
    <div id="modal-tambah" tabindex="-1" class="hidden fixed top-0 left-0 right-0 z-50 flex justify-center items-center w-full h-full bg-black bg-opacity-50 overflow-y-scroll">
        <div class="bg-white rounded-lg shadow p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold mb-4">Tambah Data Pakan</h3>
            <form action="{{ route('store-fish') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="block">Nama Ikan</label>
                    <input type="text" name="fish_name" class="form-input w-full" required>
                </div>

                <div class="mb-3">
                    <label class="block">Jenis Pakan</label>
                    <input type="text" name="feed_type" class="form-input w-full" required>
                </div>

                <div class="mb-3">
                    <label class="block">Jam Pemberian</label>
                    <input type="time" name="feeding_time" class="form-input w-full" required>
                </div>

                <div class="mb-3">
                    <label class="block">Berat Pakan (gram)</label>
                    <input type="number" step="0.01" name="feed_weight" class="form-input w-full" required>
                </div>

                <div class="mb-3">
                    <label class="block">Berat Ikan (opsional)</label>
                    <input type="number" step="0.01" name="fish_weight" class="form-input w-full">
                </div>

                <div class="mb-3">
                    <label class="block">Jumlah Ikan (opsional)</label>
                    <input type="number" name="fish_count" class="form-input w-full">
                </div>

                <div class="mb-3">
                    <label class="block">Catatan</label>
                    <textarea name="notes" class="form-input w-full"></textarea>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" data-modal-hide="modal-tambah" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
<script>
    document.querySelectorAll('[data-modal-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-modal-target');
            document.getElementById(target).classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-modal-hide]').forEach(button => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-modal-hide');
            document.getElementById(target).classList.add('hidden');
        });
    });
</script>


</x-app-layout>

