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
    <!-- Modern Header -->
    <header class="bg-white/95 backdrop-blur-md shadow-lg border-b border-gray-200/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 lg:h-20">
                <!-- Logo Section -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <!-- QR Icon -->
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-[#138c79] to-[#0f7a69] rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <!-- Brand Text -->
                        <div class="flex flex-col">
                            <a href="{{ url('/') }}" class="text-xl lg:text-2xl font-bold text-gray-900 hover:text-[#138c79] transition-colors duration-200">
                                QR Anggurin
                            </a>
                            <span class="text-xs text-gray-500 font-medium hidden sm:block">QR Code Generator & Scanner</span>
                        </div>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-1">
                    <a href="{{ url('/') }}" class="group relative px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('/') ? 'text-[#138c79] bg-[#138c79]/10' : 'text-gray-700 hover:text-[#138c79] hover:bg-gray-100' }}">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Beranda</span>
                        </div>
                        @if(request()->is('/'))
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-0.5 bg-[#138c79] rounded-full"></div>
                        @endif
                    </a>
                    <a href="{{ route('qr.generator') }}" class="group relative px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('bikin-qr') ? 'text-[#138c79] bg-[#138c79]/10' : 'text-gray-700 hover:text-[#138c79] hover:bg-gray-100' }}">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <span>Bikin QR</span>
                        </div>
                        @if(request()->is('bikin-qr'))
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-0.5 bg-[#138c79] rounded-full"></div>
                        @endif
                    </a>
                    <a href="{{ route('qr.scanner') }}" class="group relative px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('scan-qr') ? 'text-[#138c79] bg-[#138c79]/10' : 'text-gray-700 hover:text-[#138c79] hover:bg-gray-100' }}">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Scan QR</span>
                        </div>
                        @if(request()->is('scan-qr'))
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-0.5 bg-[#138c79] rounded-full"></div>
                        @endif
                    </a>
                </nav>

                <!-- Mobile & Tablet Menu Button -->
                <div class="lg:hidden" x-data="{ mobileMenuOpen: false }">
                    <button type="button" class="p-2 rounded-xl text-gray-700 hover:text-[#138c79] hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#138c79]/20 transition-all duration-200 relative z-[60]" @click="mobileMenuOpen = !mobileMenuOpen">
                        <svg class="h-6 w-6 transition-transform duration-300" :class="mobileMenuOpen ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Mobile menu fullscreen -->
                    <div class="fixed inset-0 bg-gradient-to-br from-[#138c79] to-[#0f7a69] z-50 min-h-screen"
                         x-show="mobileMenuOpen"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         style="display: none;">

                        <!-- Close Button -->
                        <div class="absolute top-6 right-6 z-[70]">
                            <button @click="mobileMenuOpen = false" class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-all duration-300 shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Content Container -->
                        <div class="flex flex-col justify-between min-h-screen py-16 px-8">
                            <!-- Logo dan Brand - Bagian Atas -->
                            <div class="text-center">
                                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-2xl">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-4xl font-bold text-white mb-2">QR Anggurin</h2>
                                <p class="text-white/80 text-lg">Generator & Scanner QR Code</p>
                            </div>

                            <!-- Menu Navigation - Bagian Tengah -->
                            <div class="flex-1 flex items-center justify-center">
                                <nav class="space-y-5 w-full max-w-xs">
                                    <a href="{{ url('/') }}"
                                       class="block w-full text-white hover:text-white/90 transition-all duration-300 transform hover:scale-105 {{ request()->is('/') ? 'text-white/90' : '' }}"
                                       @click="mobileMenuOpen = false">
                                        <div class="flex items-center justify-center space-x-4 bg-white/15 backdrop-blur-sm rounded-2xl py-5 px-6 hover:bg-white/25 transition-all duration-300 shadow-lg">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            <span class="text-xl font-bold">Beranda</span>
                                        </div>
                                    </a>

                                    <a href="{{ route('qr.generator') }}"
                                       class="block w-full text-white hover:text-white/90 transition-all duration-300 transform hover:scale-105 {{ request()->is('bikin-qr') ? 'text-white/90' : '' }}"
                                       @click="mobileMenuOpen = false">
                                        <div class="flex items-center justify-center space-x-4 bg-white/15 backdrop-blur-sm rounded-2xl py-5 px-6 hover:bg-white/25 transition-all duration-300 shadow-lg">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                            </svg>
                                            <span class="text-xl font-bold">Bikin QR</span>
                                        </div>
                                    </a>

                                    <a href="{{ route('qr.scanner') }}"
                                       class="block w-full text-white hover:text-white/90 transition-all duration-300 transform hover:scale-105 {{ request()->is('scan-qr') ? 'text-white/90' : '' }}"
                                       @click="mobileMenuOpen = false">
                                        <div class="flex items-center justify-center space-x-4 bg-white/15 backdrop-blur-sm rounded-2xl py-5 px-6 hover:bg-white/25 transition-all duration-300 shadow-lg">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <span class="text-xl font-bold">Scan QR</span>
                                        </div>
                                    </a>
                                </nav>
                            </div>

                            <!-- Footer - Bagian Bawah -->
                            <div class="text-center">
                                <div class="border-t border-white/30 pt-6">
                                    <p class="text-white/80 text-base font-medium">© {{ date('Y') }} QR Anggurin</p>
                                    <p class="text-white/60 text-sm mt-1">Developed by Angga Artupas</p>
                                </div>
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
    <footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white mt-12 pb-20 md:pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Section -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-[#138c79] rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white">QR Anggurin</h3>
                    </div>
                    <p class="text-gray-300 mb-6 max-w-md">
                        Generator dan Scanner QR Code gratis. Buat QR Code untuk berbagai keperluan dengan mudah dan cepat <b>TANPA REGISTRASI!</b>
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://github.com/tupski/qr-anggurin" target="_blank" class="w-10 h-10 bg-gray-700 hover:bg-[#138c79] rounded-lg flex items-center justify-center transition-colors duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                        <a href="mailto:artupski@gmail.com" class="w-10 h-10 bg-gray-700 hover:bg-[#138c79] rounded-lg flex items-center justify-center transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Menu Utama</h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ url('/') }}" class="text-gray-300 hover:text-[#138c79] transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('qr.generator') }}" class="text-gray-300 hover:text-[#138c79] transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                                Generator QR
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('qr.scanner') }}" class="text-gray-300 hover:text-[#138c79] transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Scanner QR
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Features -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Fitur Unggulan</h4>
                    <ul class="space-y-3">
                        <li class="text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-[#138c79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            9 Jenis QR Code
                        </li>
                        <li class="text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-[#138c79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Kustomisasi Lengkap
                        </li>
                        <li class="text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-[#138c79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Upload Logo
                        </li>
                        <li class="text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-[#138c79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Download Multi Format
                        </li>
                        <li class="text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-[#138c79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Scanner Fleksibel
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="border-t border-gray-700 mt-8 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-gray-400 text-sm mb-4 md:mb-0">
                        Hak Cipta © {{ date('Y') }} <a class="text-[#138c79] hover:text-[#0f7a69] font-medium transition-colors duration-200" href="/" target="_self">QR Anggurin</a>. Semua hak cipta dilindungi.
                    </div>
                    <div class="flex items-center space-x-6 text-sm">
                        <span class="text-gray-400">Developed by
                            <a href="https://github.com/tupski" target="_blank" class="text-[#138c79] hover:text-[#0f7a69] font-medium transition-colors duration-200">
                                Angga Artupas
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Floating Footer Menu -->
    <div class="fixed bottom-4 left-4 right-4 md:hidden z-50">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-200/50">
            <div class="flex justify-around items-center py-3 px-2">
                <!-- Beranda -->
                <a href="{{ url('/') }}" class="flex flex-col items-center py-3 px-4 rounded-xl transition-all duration-300 {{ request()->is('/') ? 'text-[#138c79] bg-[#138c79]/10' : 'text-gray-600 hover:text-[#138c79] hover:bg-gray-100' }}">
                    <div class="relative">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        @if(request()->is('/'))
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-[#138c79] rounded-full"></div>
                        @endif
                    </div>
                    <span class="text-xs font-medium">Beranda</span>
                </a>

                <!-- Buat QR -->
                <a href="{{ route('qr.generator') }}" class="flex flex-col items-center py-3 px-4 rounded-xl transition-all duration-300 {{ request()->is('bikin-qr') ? 'text-[#138c79] bg-[#138c79]/10' : 'text-gray-600 hover:text-[#138c79] hover:bg-gray-100' }}">
                    <div class="relative">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        @if(request()->is('bikin-qr'))
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-[#138c79] rounded-full"></div>
                        @endif
                    </div>
                    <span class="text-xs font-medium">Buat QR</span>
                </a>

                <!-- Scan QR -->
                <a href="{{ route('qr.scanner') }}" class="flex flex-col items-center py-3 px-4 rounded-xl transition-all duration-300 {{ request()->is('scan-qr') ? 'text-[#138c79] bg-[#138c79]/10' : 'text-gray-600 hover:text-[#138c79] hover:bg-gray-100' }}">
                    <div class="relative">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        @if(request()->is('scan-qr'))
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-[#138c79] rounded-full"></div>
                        @endif
                    </div>
                    <span class="text-xs font-medium">Scan QR</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
