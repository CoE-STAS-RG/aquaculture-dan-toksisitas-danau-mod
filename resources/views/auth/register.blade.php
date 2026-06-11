<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }

        .blob-bg {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 100%);
        }

        .tab-active {
            background: #2563eb;
            color: white;
            border-radius: 9999px;
        }
        .tab-inactive {
            color: #6b7280;
        }
        .input-field {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }
        .input-field:focus {
            outline: none;
            border-color: #2563eb;
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .btn-register {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transition: all 0.2s;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        .glass-footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="blob-bg flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden flex" style="min-height: 560px;">

        {{-- KIRI: FORM REGISTER --}}
        <div class="w-full md:w-1/2 p-10 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-6">
                    <span class="font-semibold text-gray-900 text-base">AquaCulture</span>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-1">Create Account!</h1>
                <p class="text-sm text-gray-400 mb-6">Join us and start monitoring your aquaculture farm</p>

                {{-- Tabs Sign In / Sign Up --}}
                <div class="flex bg-gray-100 rounded-full p-1 mb-6">
                    <a href="{{ route('login') }}" class="tab-inactive flex-1 text-sm font-medium py-1.5 px-4 text-center">Sign in</a>
                    <button class="tab-active flex-1 text-sm font-medium py-1.5 px-4">Sign Up</button>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="relative mb-3">
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            required autofocus placeholder="Full name"
                            class="input-field w-full px-4 py-2.5 rounded-lg text-sm text-gray-700 pr-10">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="relative mb-3">
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            required placeholder="Email address"
                            class="input-field w-full px-4 py-2.5 rounded-lg text-sm text-gray-700 pr-10">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="relative mb-3">
                        <input id="password" type="password" name="password"
                            required placeholder="Password"
                            class="input-field w-full px-4 py-2.5 rounded-lg text-sm text-gray-700 pr-10">
                        <button type="button" onclick="togglePassword('password', 'eye-icon-1')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon-1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        @error('password')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="relative mb-5">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            required placeholder="Confirm password"
                            class="input-field w-full px-4 py-2.5 rounded-lg text-sm text-gray-700 pr-10">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon-2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        @error('password_confirmation')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Register Button --}}
                    <button type="submit" class="btn-register w-full py-2.5 text-white text-sm font-semibold rounded-full">
                        Create Account
                    </button>
                </form>
            </div>

            <p class="text-xs text-center text-gray-400 mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</a>
            </p>
        </div>

        {{-- KANAN: VIDEO BACKGROUND --}}
        <div class="hidden md:flex md:w-1/2 relative overflow-hidden flex-col justify-end p-6">
            <video autoplay muted loop playsinline
                class="absolute inset-0 w-full h-full object-cover">
                <source src="{{ asset('images/_MConverter.eu_mp_.webm') }}" type="video/webm">
            </video>
            <div class="absolute inset-0 bg-black/20"></div>

            <div class="glass-footer rounded-xl p-3 text-center relative z-10">
                <p class="text-white text-xs opacity-80">© 2025 AquaMonitor. All rights reserved.</p>
                <p class="text-white text-xs opacity-60 mt-0.5">Unauthorized use or reproduction is prohibited.</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, iconId) {
            const input = document.getElementById(fieldId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
