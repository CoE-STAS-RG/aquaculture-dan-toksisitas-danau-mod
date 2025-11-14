<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                Perangkat
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                Waktu
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                Suhu (°C)
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                pH
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                DO (mg/L)
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                Risiko
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
        @forelse($readings as $reading)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reading->device->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $reading->created_at->format('d M Y H:i') }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($reading->water_temperature, 2) }}</div>
                </td> 
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($reading->ph, 2) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($reading->dissolved_oxygen, 2) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
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
                        {{ number_format($reading->risk_level, 1) }}%
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    Belum ada data pembacaan dari perangkat Anda.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>