<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Welcome - Laravel</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Tambahkan Lottie CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.9.6/lottie.min.js"></script>
</head>
<body class="bg-gray-900 dark:bg-gray-900 text-gray-900 dark:text-white flex flex-col items-center justify-center min-h-screen p-6">

    <!-- Lottie Animation -->
     <div id="lottie-container" style="width: 300px; height: 300px;"></div>
    <div class="text-center mb-10 mt-6">
        <h1 class="text-4xl font-bold mb-2">Selamat Datang

        </h1>
        <p class="text-lg text-gray-600">Dashboard Monitoring Air Tambak Ikan – Realtime.</p>
    </div>
    {{-- Menu Login/Register atau Dashboard --}}
    <div class="w-full max-w-md">
        @if (Route::has('login'))
            <div class="flex justify-center gap-4">
                @auth
                    <a href="{{ url('/user/dashboard') }}"
                        class="w-full text-center px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        Dashboard
                    </a>
                @else
                
            
                    <a href="{{ route('login') }}"
                        class="w-1/2 text-center px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Login
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="w-1/2 text-center px-6 py-3 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        @endif
    </div>

    <!-- Lottie Animation Script -->
   <script>
        document.addEventListener("DOMContentLoaded", function () {
            lottie.loadAnimation({
                container: document.getElementById('lottie-container'),
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: "{{ asset('build/assets/animations/welcome.json') }}"

            });
        });
    </script>


</body>
</html>
