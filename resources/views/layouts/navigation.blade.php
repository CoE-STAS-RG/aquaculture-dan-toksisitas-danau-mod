<nav x-data="{ open: false, sidebarOpen: false }" class="bg-white border-b border-gray-100">


    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('user.dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>
                
                {{-- <!-- Sidebar Toggle -->
                <div class="relative">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                        <i class="fas fa-bars text-xl me-3"></i>
                    </button>

                    <!-- Sidebar Panel -->
                    <div x-show="sidebarOpen" @click.outside="sidebarOpen = false" class="absolute z-50 left-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-md">
                        <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Dashboard
                        </a>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Manajemen Ikan
                        </a>
                    </div>
                </div> --}}

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                   <x-nav-link :href="route('index-fish')" :active="request()->routeIs('index-fish')">
                        {{ __('Grafik Pertumbuhan') }}
                    </x-nav-link>
                   
                  
            

                    
                </div>
            </div>

            <!-- Search and Icons -->
            <div class="flex items-center">
                

                <!-- Icons -->
                <div class="hidden sm:flex sm:items-center space-x-4">
                    

                    

                    <!-- Settings Icon -->

                    <div class="relative">
                        {{-- Notifikasi Button --}}
                        <div class="relative">
                            {{-- Notifikasi Button --}}
                            <button id="notifBtn" class="flex items-center p-2 text-gray-500 hover:text-gray-700 focus:outline-none transition relative">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 
                                        .538-.214 1.055-.595 1.436L4 17h5m6 0v5a2 2 0 11-2 2H9a2 2 0 011-1.995V17h5z" />
                                </svg>
                                <span class="sr-only">Notifications</span>
                                <span class="absolute top-0 right-0 inline-block w-2 h-2 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"></span>
                            </button>

                            {{-- Dropdown --}}
                            <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50">
                                <div class="p-3">
                                    <h4 class="font-semibold text-gray-700 dark:text-white mb-2">Notifikasi Terbaru</h4>
                                    <ul id="notifList" class="text-sm text-gray-600 dark:text-gray-300 space-y-2">
                                        {{-- Diisi oleh JavaScript --}}
                                    </ul>
                                </div>
                            </div>
                        </div>


                        <div id="notifDropdown"
                            class="hidden absolute right-0 mt-2 w-80 max-w-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-50 transition-all duration-200">
                            <div class="p-4">
                                <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-3">Notifikasi Terbaru</h4>
                                <ul id="notifList"
                                    class="space-y-3 text-sm text-gray-700 dark:text-gray-300 max-h-64 overflow-y-auto pr-2">
                                    <!-- Diisi oleh JavaScript -->
                                </ul>
                            </div>
                        </div>

                    </div>

                    
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center p-2 text-gray-500 hover:text-gray-700 focus:outline-none transition">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
          
            {{-- <x-responsive-nav-link :href="" :active="request()->routeIs('kategori')">
                {{ __('Kategori') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="" :active="request()->routeIs('resep')">
                {{ __('Resep') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="" :active="request()->routeIs('pesanan')">
                {{ __('Pesanan') }}
            </x-responsive-nav-link> --}}
            {{-- <span>{{ __('Dashboard') }}</span>
            <span>{{ __('Kategori') }}</span>
            <span>{{ __('Resep') }}</span>
            <span>{{ __('Pesanan') }}</span> --}}

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

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');

    const latestNotifications = @json($latestNotifications);

    notifBtn.addEventListener('click', () => {
        notifDropdown.classList.toggle('hidden');
        if (!notifDropdown.classList.contains('hidden')) {
            notifList.innerHTML = '';

            if (latestNotifications.length === 0) {
                notifList.innerHTML = '<li>Tidak ada notifikasi baru.</li>';
            } else {
                latestNotifications.forEach(item => {
                    notifList.innerHTML += `<li class="border-b border-gray-200 dark:border-gray-600 pb-2">${item}</li>`;
                });
            }
        }
    });

    // Optional: klik di luar dropdown, maka dropdown hilang
    document.addEventListener('click', function(e) {
        if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
            notifDropdown.classList.add('hidden');
        }
    });
</script>
