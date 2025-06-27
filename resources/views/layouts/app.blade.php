<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'QR Anggurin - Generator & Scanner QR Code Gratis')</title>
    <meta name="description" content="Generator dan Scanner QR Code gratis untuk semua. Buat QR Code untuk teks, URL, SMS, WhatsApp, telepon, email, lokasi, WiFi, dan VCard.">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-2xl font-bold text-indigo-600">
                        QR Anggurin
                    </a>
                </div>
                
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->is('/') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('qr.generator') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->is('generator') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                        Generator
                    </a>
                    <a href="{{ route('qr.scanner') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->is('scanner') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                        Scanner
                    </a>
                </nav>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-700 hover:text-indigo-600" x-data x-on:click="$refs.mobileMenu.classList.toggle('hidden')">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" x-ref="mobileMenu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t">
                <a href="{{ url('/') }}" class="text-gray-700 hover:text-indigo-600 block px-3 py-2 rounded-md text-base font-medium {{ request()->is('/') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('qr.generator') }}" class="text-gray-700 hover:text-indigo-600 block px-3 py-2 rounded-md text-base font-medium {{ request()->is('generator') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                    Generator
                </a>
                <a href="{{ route('qr.scanner') }}" class="text-gray-700 hover:text-indigo-600 block px-3 py-2 rounded-md text-base font-medium {{ request()->is('scanner') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                    Scanner
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-gray-600">
                    © {{ date('Y') }} QR Anggurin. Gratis untuk semua.
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Generator dan Scanner QR Code terbaik untuk kebutuhan Anda.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
