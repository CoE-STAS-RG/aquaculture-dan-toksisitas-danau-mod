<x-app-layout>
    {{-- Tidak pakai <x-slot name="header"> supaya bar putih header bawaan tidak muncul
         (menghindari kesan "dua header"). Judul halaman dipindah ke dalam konten. --}}

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Judul halaman --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-700 text-white shadow-md shadow-cyan-500/30">
                        <i class="fa-solid fa-microchip text-xl"></i>
                    </span>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 leading-tight">
                            {{ __('ui.my_devices') }}
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('ui.devices_subtitle') }}
                        </p>
                    </div>
                </div>

                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-cyan-50 text-cyan-700 text-sm font-semibold ring-1 ring-cyan-100">
                    <i class="fa-solid fa-circle-nodes"></i>
                    {{ trans_choice('ui.device_count', $devices->count(), ['count' => $devices->count()]) }}
                </span>
            </div>

            {{-- Kartu daftar device --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-100 dark:ring-gray-700 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-700 dark:text-gray-200">{{ __('ui.device_list') }}</h2>
                    <a href="{{ route('devices.create') }}"
                       class="inline-flex items-center gap-2 bg-gradient-to-br from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl shadow-sm shadow-cyan-500/30 transition">
                        <i class="fa-solid fa-plus"></i>
                        {{ __('ui.add_new_device') }}
                    </a>
                </div>

                @if($devices->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 mb-4">
                            <i class="fa-solid fa-satellite-dish text-2xl"></i>
                        </span>
                        <p class="text-gray-600 dark:text-gray-300 font-medium">{{ __('ui.no_devices_title') }}</p>
                        <p class="text-sm text-gray-400 mb-5">{{ __('ui.no_devices_desc') }}</p>
                        <a href="{{ route('devices.create') }}"
                           class="inline-flex items-center gap-2 bg-gradient-to-br from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl shadow-sm transition">
                            <i class="fa-solid fa-plus"></i>
                            {{ __('ui.add_new_device') }}
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 bg-gray-50 dark:bg-gray-700/40">
                                    <th class="px-6 py-3 font-semibold">{{ __('ui.name') }}</th>
                                    <th class="px-6 py-3 font-semibold">{{ __('ui.device_code') }}</th>
                                    <th class="px-6 py-3 font-semibold">{{ __('ui.location') }}</th>
                                    <th class="px-6 py-3 font-semibold text-right">{{ __('ui.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($devices as $device)
                                    <tr class="hover:bg-cyan-50/40 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-100">{{ $device->name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200 font-mono text-xs">
                                                {{ $device->device_code }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                            {{ $device->location ?: '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('devices.show', $device) }}"
                                               class="inline-flex items-center gap-1.5 text-cyan-600 hover:text-cyan-800 font-semibold">
                                                <i class="fa-solid fa-eye"></i> {{ __('ui.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
