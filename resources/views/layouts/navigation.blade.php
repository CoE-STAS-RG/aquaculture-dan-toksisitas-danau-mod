<nav x-data="{ open: false, sidebarOpen: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo dan Link -->
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('user.dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links (desktop only) -->
                <div class="hidden sm:flex space-x-8 sm:ml-10">
                    <x-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('index-fish')" :active="request()->routeIs('index-fish')">
                        {{ __('Grafik Pertumbuhan') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Icons (always visible) -->
            <div class="flex items-center space-x-4">
                <!-- Notifikasi -->
                <div class="relative">
                    <button id="notifBtn" class="flex items-center p-2 text-gray-500 hover:text-gray-700 focus:outline-none transition relative">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 
                                .538-.214 1.055-.595 1.436L4 17h5m6 0v5a2 2 0 11-2 2H9a2 2 0 011-1.995V17h5z" />
                        </svg>
                        <span class="sr-only">Notifications</span>
                        <span class="absolute top-0 right-0 inline-block w-2 h-2 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"></span>
                    </button>

                    <!-- Dropdown Notifikasi -->
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded shadow-lg z-50">
                        <div class="p-3">
                            <h4 class="font-semibold text-gray-700 mb-2">Notifikasi Terbaru</h4>
                            <ul id="notifList" class="text-sm text-gray-600 space-y-2">
                                {{-- Diisi oleh JavaScript --}}
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- User Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center p-2 text-gray-500 hover:text-gray-700 focus:outline-none transition">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 
                                        1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 
                                        3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 
                                        1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 
                                        1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 
                                        1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 
                                        2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger Menu (mobile only) -->
                <div class="sm:hidden">
                    <button @click="open = ! open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('index-fish')" :active="request()->routeIs('index-fish')">
                {{ __('Grafik Pertumbuhan') }}
            </x-responsive-nav-link>
        </div>

        <!-- Notifikasi di mobile -->
        <div class="px-4 pb-3 border-t border-gray-200">
            <button id="notifBtnMobile" class="flex items-center text-gray-500 hover:text-gray-700 w-full">
                <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 
                        00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 
                        6 8.388 6 11v3.159c0 .538-.214 
                        1.055-.595 1.436L4 17h5m6 0v5a2 2 0 
                        11-2 2H9a2 2 0 011-1.995V17h5z" />
                </svg>
                <span>Notifikasi</span>
            </button>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Script -->
<script>
    const notifBtn = document.getElementById('notifBtn');
    const notifBtnMobile = document.getElementById('notifBtnMobile');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const latestNotifications = {};

    function toggleNotif() {
        notifDropdown.classList.toggle('hidden');
        if (!notifDropdown.classList.contains('hidden')) {
            notifList.innerHTML = '';
            if (latestNotifications.length === 0) {
                notifList.innerHTML = '<li>Tidak ada notifikasi baru.</li>';
            } else {
                latestNotifications.forEach(item => {
                    notifList.innerHTML += `<li class="border-b border-gray-200 pb-2">${item}</li>`;
                });
            }
        }
    }

    notifBtn?.addEventListener('click', toggleNotif);
    notifBtnMobile?.addEventListener('click', toggleNotif);

    document.addEventListener('click', function (e) {
        if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
            notifDropdown.classList.add('hidden');
        }
    });
</script>
