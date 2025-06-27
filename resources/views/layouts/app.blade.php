<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'QR Anggurin - Generator & Scanner QR Code Gratis')</title>
    <meta name="description" content="@yield('description', 'Generator dan Scanner QR Code gratis untuk semua. Buat QR Code untuk teks, URL, SMS, WhatsApp, telepon, email, lokasi, WiFi, dan VCard.')">
    <meta name="keywords" content="QR Code, Generator, Scanner, Gratis, Free, QR Code Generator, QR Code Scanner, Indonesia">
    <meta name="author" content="QR Anggurin">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'QR Anggurin - Generator & Scanner QR Code Gratis')">
    <meta property="og:description" content="@yield('description', 'Generator dan Scanner QR Code gratis untuk semua. Buat QR Code untuk teks, URL, SMS, WhatsApp, telepon, email, lokasi, WiFi, dan VCard.')">
    <meta property="og:image" content="{{ asset('favicon.ico') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'QR Anggurin - Generator & Scanner QR Code Gratis')">
    <meta property="twitter:description" content="@yield('description', 'Generator dan Scanner QR Code gratis untuk semua. Buat QR Code untuk teks, URL, SMS, WhatsApp, telepon, email, lokasi, WiFi, dan VCard.')">
    <meta property="twitter:image" content="{{ asset('favicon.ico') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-2xl font-bold text-[#138c79]">
                        QR Anggurin
                    </a>
                </div>

                <nav class="hidden md:flex space-x-8">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-[#138c79] px-3 py-2 rounded-md text-sm font-medium {{ request()->is('/') ? 'text-[#138c79] bg-[#138c79]/10' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('qr.generator') }}" class="text-gray-700 hover:text-[#138c79] px-3 py-2 rounded-md text-sm font-medium {{ request()->is('generator') ? 'text-[#138c79] bg-[#138c79]/10' : '' }}">
                        Bikin QR
                    </a>
                    <a href="{{ route('qr.scanner') }}" class="text-gray-700 hover:text-[#138c79] px-3 py-2 rounded-md text-sm font-medium {{ request()->is('scanner') ? 'text-[#138c79] bg-[#138c79]/10' : '' }}">
                        Scan QR
                    </a>
                </nav>

                <!-- Mobile menu button -->
                <div class="md:hidden" x-data="{ mobileMenuOpen: false }">
                    <button type="button" class="text-gray-700 hover:text-[#138c79] relative z-50" @click="mobileMenuOpen = !mobileMenuOpen">
                        <svg class="h-6 w-6 transition-transform duration-300" :class="mobileMenuOpen ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Mobile menu fullscreen -->
                    <div class="fixed inset-0 bg-[#138c79] z-40 flex items-center justify-center"
                         x-show="mobileMenuOpen"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">

                        <div class="text-center space-y-8">
                            <div class="mb-12">
                                <h2 class="text-4xl font-bold text-white mb-2">QR Anggurin</h2>
                                <p class="text-[#138c79]/70 text-lg">Generator & Scanner QR Code</p>
                            </div>

                            <nav class="space-y-6">
                                <a href="{{ url('/') }}"
                                   class="block text-2xl font-semibold text-white hover:text-[#138c79]/80 transition-all duration-300 transform hover:scale-110 {{ request()->is('/') ? 'text-[#138c79]/80' : '' }}"
                                   @click="mobileMenuOpen = false">
                                    <div class="flex items-center justify-center space-x-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        <span>Beranda</span>
                                    </div>
                                </a>

                                <a href="{{ route('qr.generator') }}"
                                   class="block text-2xl font-semibold text-white hover:text-[#138c79]/80 transition-all duration-300 transform hover:scale-110 {{ request()->is('generator') ? 'text-[#138c79]/80' : '' }}"
                                   @click="mobileMenuOpen = false">
                                    <div class="flex items-center justify-center space-x-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        <span>Bikin QR</span>
                                    </div>
                                </a>

                                <a href="{{ route('qr.scanner') }}"
                                   class="block text-2xl font-semibold text-white hover:text-[#138c79]/80 transition-all duration-300 transform hover:scale-110 {{ request()->is('scanner') ? 'text-[#138c79]/80' : '' }}"
                                   @click="mobileMenuOpen = false">
                                    <div class="flex items-center justify-center space-x-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <span>Scan QR</span>
                                    </div>
                                </a>
                            </nav>

                            <div class="mt-12 pt-8 border-t border-white/20">
                                <p class="text-white/70 text-sm">© {{ date('Y') }} QR Anggurin</p>
                                <p class="text-white/50 text-xs mt-1">by Angga Artupas</p>
                            </div>
                        </div>
                    </div>
                </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12 pb-20 md:pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-gray-600">
                    © {{ date('Y') }} QR Anggurin.
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Generator dan Scanner QR Code.
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    by <a href="https://github.com/tupski" class="text-[#138c79] hover:underline" target="_blank">Angga Artupas</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Mobile Floating Footer Menu -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg md:hidden z-50">
        <div class="flex justify-around items-center py-2">
            <!-- Beranda -->
            <a href="{{ url('/') }}" class="flex flex-col items-center py-2 px-3 {{ request()->is('/') ? 'text-[#138c79]' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs font-medium">Beranda</span>
            </a>

            <!-- Buat QR -->
            <a href="{{ route('qr.generator') }}" class="flex flex-col items-center py-2 px-3 {{ request()->is('generator') ? 'text-[#138c79]' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="text-xs font-medium">Buat QR</span>
            </a>

            <!-- Scan QR -->
            <a href="{{ route('qr.scanner') }}" class="flex flex-col items-center py-2 px-3 {{ request()->is('scanner') ? 'text-[#138c79]' : 'text-gray-600' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span class="text-xs font-medium">Scan QR</span>
            </a>
        </div>
    </div>
</body>
</html>
