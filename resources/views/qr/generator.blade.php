@extends('layouts.app')

@section('title', 'Generator QR Code - QR Anggurin')

@section('content')
<style>
.qr-container {
    position: relative;
    display: inline-block;
}

.qr-image {
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.frame-square .qr-image {
    border: 8px solid var(--frame-color, #138c79);
    border-radius: 0;
}

.frame-circle .qr-image {
    border: 8px solid var(--frame-color, #138c79);
    border-radius: 50%;
}

.frame-rounded .qr-image {
    border: 8px solid var(--frame-color, #138c79);
    border-radius: 1.5rem;
}

.frame-square::before,
.frame-circle::before,
.frame-rounded::before {
    content: '';
    position: absolute;
    top: -4px;
    left: -4px;
    right: -4px;
    bottom: -4px;
    background: linear-gradient(45deg, var(--frame-color, #138c79), rgba(255,255,255,0.1));
    z-index: -1;
}

.frame-square::before {
    border-radius: 0;
}

.frame-circle::before {
    border-radius: 50%;
}

.frame-rounded::before {
    border-radius: 2rem;
}

/* Loading Animation */
@keyframes progress {
    0% { transform: translateX(-100%); }
    50% { transform: translateX(0%); }
    100% { transform: translateX(100%); }
}

@keyframes fadeInScale {
    0% {
        opacity: 0;
        transform: scale(0.9) translateY(10px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.qr-container {
    animation: fadeInScale 0.5s ease-out;
}
</style>

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Generator QR Code</h1>
            <p class="text-lg text-gray-600">Buat QR Code dengan kustomisasi lengkap</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="qrGenerator()" x-init="init()">
            <!-- Floating Preview Button (Mobile Only) -->
            <div class="fixed bottom-6 right-6 z-50 lg:hidden" x-show="qrImage" x-transition>
                <button @click="scrollToPreview()" class="bg-[#138c79] hover:bg-[#0f7a69] text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>

            <!-- Left Panel - Form -->
            <div class="bg-white rounded-xl shadow-xl p-8 border border-gray-100">
                <form @submit.prevent="generateQR">
                    <!-- QR Type Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Jenis QR Code</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="form.type = 'text'; updateFormFields()"
                                    :class="form.type === 'text' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Teks</span>
                            </button>
                            <button type="button" @click="form.type = 'url'; updateFormFields()"
                                    :class="form.type === 'url' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <span>URL</span>
                            </button>
                            <button type="button" @click="form.type = 'sms'; updateFormFields()"
                                    :class="form.type === 'sms' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>SMS</span>
                            </button>
                            <button type="button" @click="form.type = 'whatsapp'; updateFormFields()"
                                    :class="form.type === 'whatsapp' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                                <span>WhatsApp</span>
                            </button>
                            <button type="button" @click="form.type = 'phone'; updateFormFields()"
                                    :class="form.type === 'phone' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>Telepon</span>
                            </button>
                            <button type="button" @click="form.type = 'email'; updateFormFields()"
                                    :class="form.type === 'email' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>Email</span>
                            </button>
                            <button type="button" @click="form.type = 'location'; updateFormFields()"
                                    :class="form.type === 'location' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Lokasi</span>
                            </button>
                            <button type="button" @click="form.type = 'wifi'; updateFormFields()"
                                    :class="form.type === 'wifi' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                                </svg>
                                <span>WiFi</span>
                            </button>
                            <button type="button" @click="form.type = 'vcard'; updateFormFields()"
                                    :class="form.type === 'vcard' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>VCard</span>
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Form Fields -->
                    <div class="mb-6" x-show="form.type === 'text' || form.type === 'url'">
                        <label class="block text-sm font-medium text-gray-700 mb-2" x-text="form.type === 'text' ? 'Teks' : 'URL'"></label>
                        <textarea x-model="form.content" @input="debouncedGenerate" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79]" rows="3" :placeholder="form.type === 'text' ? 'Masukkan teks...' : 'https://example.com'"></textarea>
                    </div>

                    <!-- SMS Fields -->
                    <div x-show="form.type === 'sms'">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="text" x-model="form.phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79]" placeholder="+628123456789">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                            <textarea x-model="form.message" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79]" rows="3" placeholder="Pesan SMS..."></textarea>
                        </div>
                    </div>

                    <!-- WhatsApp Fields -->
                    <div x-show="form.type === 'whatsapp'">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp</label>
                            <input type="text" x-model="form.phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79]" placeholder="628123456789">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                            <textarea x-model="form.message" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79]" rows="3" placeholder="Pesan WhatsApp..."></textarea>
                        </div>
                    </div>

                    <!-- Phone Field -->
                    <div class="mb-6" x-show="form.type === 'phone'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" x-model="form.content" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79]" placeholder="+628123456789">
                    </div>

                    <!-- Email Fields -->
                    <div x-show="form.type === 'email'">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" x-model="form.email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="email@example.com">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" x-model="form.subject" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Subject email...">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                            <textarea x-model="form.message" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" placeholder="Isi email..."></textarea>
                        </div>
                    </div>

                    <!-- Location Fields -->
                    <div x-show="form.type === 'location'">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                            <input type="text" x-model="form.latitude" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="-6.2088">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                            <input type="text" x-model="form.longitude" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="106.8456">
                        </div>
                    </div>

                    <!-- WiFi Fields -->
                    <div x-show="form.type === 'wifi'">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">SSID</label>
                            <input type="text" x-model="form.ssid" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nama WiFi">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="text" x-model="form.password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Password WiFi">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keamanan</label>
                            <select x-model="form.security" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="WPA">WPA/WPA2</option>
                                <option value="WEP">WEP</option>
                                <option value="nopass">Tanpa Password</option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="form.hidden" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">WiFi tersembunyi</span>
                            </label>
                        </div>
                    </div>

                    <!-- VCard Fields -->
                    <div x-show="form.type === 'vcard'">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" x-model="form.name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="John Doe">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Organisasi</label>
                            <input type="text" x-model="form.organization" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Perusahaan">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                            <input type="text" x-model="form.phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="+628123456789">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" x-model="form.email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="email@example.com">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                            <input type="text" x-model="form.website" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://website.com">
                        </div>
                    </div>

                    <!-- Customization Options -->
                    <div class="border-t border-gray-200 pt-8 mt-8" x-data="{
                        openSection: 'basic',
                        toggleSection(section) {
                            this.openSection = this.openSection === section ? '' : section;
                        }
                    }">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 bg-[#138c79]/10 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-[#138c79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Kustomisasi QR Code</h3>
                        </div>

                        <!-- Accordion Sections -->
                        <div class="space-y-4">
                            <!-- Basic Settings -->
                            <div class="border border-gray-200 rounded-lg">
                                <button @click="toggleSection('basic')" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 transition-colors duration-200">
                                    <span class="font-medium text-gray-900">Pengaturan Dasar</span>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200" :class="openSection === 'basic' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openSection === 'basic'" x-transition class="p-4 border-t border-gray-200">

                        <!-- Size and Margin -->
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Ukuran QR Code</label>
                                <div class="relative">
                                    <input type="number" x-model="form.size" @input="debouncedGenerate" min="100" max="1000" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79] pl-4 pr-12 py-3">
                                    <span class="absolute right-3 top-3 text-sm text-gray-500">px</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>100px</span>
                                    <span x-text="form.size + 'px'" class="font-medium text-[#138c79]"></span>
                                    <span>1000px</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Margin</label>
                                <div class="relative">
                                    <input type="number" x-model="form.margin" @input="debouncedGenerate" min="0" max="50" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79] pl-4 pr-12 py-3">
                                    <span class="absolute right-3 top-3 text-sm text-gray-500">px</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>0px</span>
                                    <span x-text="form.margin + 'px'" class="font-medium text-[#138c79]"></span>
                                    <span>50px</span>
                                </div>
                            </div>
                        </div>

                        <!-- Color Picker -->
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Warna Foreground</label>
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <input type="color" x-model="form.foreground_color" @change="debouncedGenerate" class="w-16 h-12 rounded-lg border-2 border-gray-300 cursor-pointer shadow-sm">
                                        <div class="absolute inset-0 rounded-lg border-2 border-gray-300 pointer-events-none"></div>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" x-model="form.foreground_color" @input="debouncedGenerate" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79] py-2 px-3 text-sm font-mono">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Warna Background</label>
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <input type="color" x-model="form.background_color" @change="debouncedGenerate" class="w-16 h-12 rounded-lg border-2 border-gray-300 cursor-pointer shadow-sm">
                                        <div class="absolute inset-0 rounded-lg border-2 border-gray-300 pointer-events-none"></div>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" x-model="form.background_color" @input="debouncedGenerate" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79] py-2 px-3 text-sm font-mono">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Error Correction -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Error Correction Level</label>
                            <div class="grid grid-cols-4 gap-2">
                                <button type="button" @click="form.error_correction = 'L'; debouncedGenerate()" :class="form.error_correction === 'L' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="py-3 px-2 rounded-lg text-sm font-medium transition-colors">
                                    <div class="font-bold">L</div>
                                    <div class="text-xs">7%</div>
                                </button>
                                <button type="button" @click="form.error_correction = 'M'; debouncedGenerate()" :class="form.error_correction === 'M' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="py-3 px-2 rounded-lg text-sm font-medium transition-colors">
                                    <div class="font-bold">M</div>
                                    <div class="text-xs">15%</div>
                                </button>
                                <button type="button" @click="form.error_correction = 'Q'; debouncedGenerate()" :class="form.error_correction === 'Q' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="py-3 px-2 rounded-lg text-sm font-medium transition-colors">
                                    <div class="font-bold">Q</div>
                                    <div class="text-xs">25%</div>
                                </button>
                                <button type="button" @click="form.error_correction = 'H'; debouncedGenerate()" :class="form.error_correction === 'H' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="py-3 px-2 rounded-lg text-sm font-medium transition-colors">
                                    <div class="font-bold">H</div>
                                    <div class="text-xs">30%</div>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Higher levels provide better error recovery but create denser QR codes</p>
                        </div>

                        <!-- Logo Upload -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Logo/Branding</label>

                            <!-- Logo Type Selection -->
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <button type="button" @click="form.logoType = 'none'; form.logo = null; form.defaultLogo = null; debouncedGenerate()"
                                            :class="form.logoType === 'none' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200">
                                        Tanpa Logo
                                    </button>
                                    <button type="button" @click="form.logoType = 'default'; form.logo = null; setDefaultLogo(); debouncedGenerate()"
                                            :class="form.logoType === 'default' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200">
                                        Logo Default
                                    </button>
                                    <button type="button" @click="form.logoType = 'upload'; form.defaultLogo = null"
                                            :class="form.logoType === 'upload' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200">
                                        Upload Custom
                                    </button>
                                </div>
                            </div>

                            <!-- Default Logo Preview -->
                            <div x-show="form.logoType === 'default'" class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-center">
                                    <div class="w-16 h-16 rounded-lg flex items-center justify-center"
                                         :style="`background-color: ${form.foreground_color}20; border: 2px solid ${form.foreground_color}30`">
                                        <div x-html="getDefaultLogoSVG()" class="w-8 h-8" :style="`color: ${form.foreground_color}`"></div>
                                    </div>
                                </div>
                                <p class="text-center text-sm text-gray-600 mt-2">Logo default akan mengikuti warna foreground</p>
                            </div>

                            <!-- Upload Area -->
                            <div x-show="form.logoType === 'upload'" class="relative group">
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-300 group-hover:shadow-lg"
                                     :class="form.logo ? 'border-[#138c79] bg-[#138c79]/10' : 'hover:border-[#138c79] hover:bg-[#138c79]/5'"
                                     @dragover.prevent="$event.dataTransfer.dropEffect = 'copy'"
                                     @drop.prevent="handleLogoDrop($event)">

                                    <!-- Upload Icon -->
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center transition-transform duration-300 group-hover:scale-110"
                                         :class="form.logo ? 'bg-gradient-to-br from-[#138c79]/20 to-[#138c79]/30' : 'bg-gradient-to-br from-[#138c79]/10 to-[#138c79]/20'">
                                        <svg class="w-8 h-8 text-[#138c79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>

                                    <!-- Upload Text -->
                                    <div class="space-y-2">
                                        <h4 class="text-lg font-semibold text-gray-900">Upload Logo Custom</h4>
                                        <p class="text-sm text-gray-600">Drag & drop file atau klik untuk memilih</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, SVG (Max 2MB)</p>
                                    </div>

                                    <!-- Upload Button -->
                                    <input type="file" @change="handleLogoUpload" accept="image/*" class="hidden" x-ref="logoInput">
                                    <button type="button" @click="$refs.logoInput.click()"
                                            class="mt-4 inline-flex items-center px-6 py-3 bg-[#138c79] hover:bg-[#0f7a69] text-white font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Pilih File
                                    </button>

                                    <!-- File Preview -->
                                    <div x-show="form.logo" class="mt-4 p-4 bg-white rounded-lg border border-[#138c79]/30">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-[#138c79]/10 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-[#138c79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900" x-text="form.logo?.name || 'Logo file'"></p>
                                                    <p class="text-xs text-gray-500" x-text="form.logo ? (form.logo.size / 1024 / 1024).toFixed(2) + ' MB' : ''"></p>
                                                </div>
                                            </div>
                                            <button type="button" @click="form.logo = null; $refs.logoInput.value = ''; debouncedGenerate()"
                                                    class="text-red-500 hover:text-red-700 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Logo Size Control -->
                            <div x-show="form.logoType !== 'none'" class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran Logo (%)</label>
                                <div class="flex items-center space-x-4">
                                    <input type="range" x-model="form.logoSize" @input="debouncedGenerate" min="10" max="30"
                                           class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                    <span class="text-sm font-medium text-[#138c79] min-w-[3rem]" x-text="form.logoSize + '%'"></span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>10%</span>
                                    <span>30%</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Logo akan ditempatkan di tengah QR dengan margin yang aman</p>
                            </div>
                        </div>
                                </div>
                            </div>

                            <!-- Frame Settings -->
                            <div class="border border-gray-200 rounded-lg">
                                <button @click="toggleSection('frame')" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 transition-colors duration-200">
                                    <span class="font-medium text-gray-900">Frame & Border</span>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200" :class="openSection === 'frame' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openSection === 'frame'" x-transition class="p-4 border-t border-gray-200">
                                    <!-- Frame Style -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Style Frame</label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <button type="button" @click="form.frame_style = 'none'; debouncedGenerate()"
                                                    :class="form.frame_style === 'none' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Tanpa Frame
                                            </button>
                                            <button type="button" @click="form.frame_style = 'square'; debouncedGenerate()"
                                                    :class="form.frame_style === 'square' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Square
                                            </button>
                                            <button type="button" @click="form.frame_style = 'circle'; debouncedGenerate()"
                                                    :class="form.frame_style === 'circle' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Circle
                                            </button>
                                            <button type="button" @click="form.frame_style = 'rounded'; debouncedGenerate()"
                                                    :class="form.frame_style === 'rounded' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Rounded
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Frame Color -->
                                    <div x-show="form.frame_style !== 'none'" class="mb-6">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Warna Frame</label>
                                        <div class="flex items-center space-x-3">
                                            <div class="relative">
                                                <input type="color" x-model="form.frame_color" @change="debouncedGenerate" class="w-16 h-12 rounded-lg border-2 border-gray-300 cursor-pointer shadow-sm">
                                                <div class="absolute inset-0 rounded-lg border-2 border-gray-300 pointer-events-none"></div>
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" x-model="form.frame_color" @input="debouncedGenerate" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#138c79] focus:ring-[#138c79] py-2 px-3 text-sm font-mono">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- QR Style Settings -->
                            <div class="border border-gray-200 rounded-lg">
                                <button @click="toggleSection('style')" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 transition-colors duration-200">
                                    <span class="font-medium text-gray-900">Style QR Code</span>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200" :class="openSection === 'style' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="openSection === 'style'" x-transition class="p-4 border-t border-gray-200">
                                    <!-- Inner Eye Style -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Style Inner Eye</label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <button type="button" @click="form.inner_eye_style = 'square'; debouncedGenerate()"
                                                    :class="form.inner_eye_style === 'square' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Square
                                            </button>
                                            <button type="button" @click="form.inner_eye_style = 'circle'; debouncedGenerate()"
                                                    :class="form.inner_eye_style === 'circle' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Circle
                                            </button>
                                            <button type="button" @click="form.inner_eye_style = 'rounded'; debouncedGenerate()"
                                                    :class="form.inner_eye_style === 'rounded' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Rounded
                                            </button>
                                            <button type="button" @click="form.inner_eye_style = 'leaf'; debouncedGenerate()"
                                                    :class="form.inner_eye_style === 'leaf' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Leaf
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Outer Eye Style -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Style Outer Eye</label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <button type="button" @click="form.outer_eye_style = 'square'; debouncedGenerate()"
                                                    :class="form.outer_eye_style === 'square' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Square
                                            </button>
                                            <button type="button" @click="form.outer_eye_style = 'circle'; debouncedGenerate()"
                                                    :class="form.outer_eye_style === 'circle' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Circle
                                            </button>
                                            <button type="button" @click="form.outer_eye_style = 'rounded'; debouncedGenerate()"
                                                    :class="form.outer_eye_style === 'rounded' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Rounded
                                            </button>
                                            <button type="button" @click="form.outer_eye_style = 'leaf'; debouncedGenerate()"
                                                    :class="form.outer_eye_style === 'leaf' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Leaf
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Data Style -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Style Data Pattern</label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <button type="button" @click="form.data_style = 'square'; debouncedGenerate()"
                                                    :class="form.data_style === 'square' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Square
                                            </button>
                                            <button type="button" @click="form.data_style = 'circle'; debouncedGenerate()"
                                                    :class="form.data_style === 'circle' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Circle
                                            </button>
                                            <button type="button" @click="form.data_style = 'rounded'; debouncedGenerate()"
                                                    :class="form.data_style === 'rounded' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Rounded
                                            </button>
                                            <button type="button" @click="form.data_style = 'dot'; debouncedGenerate()"
                                                    :class="form.data_style === 'dot' ? 'bg-[#138c79] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                    class="p-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Dot
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <button type="submit" class="w-full bg-[#138c79] hover:bg-[#0f7a69] text-white font-bold py-4 px-6 rounded-xl transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5" :disabled="loading">
                        <span x-show="!loading" class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Generate QR Code
                        </span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generating...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Right Panel - Preview -->
            <div id="preview-section" class="bg-white rounded-xl shadow-xl p-8 border border-gray-100 lg:sticky lg:top-8 lg:self-start">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-[#138c79]/10 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-[#138c79]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Live Preview</h3>
                </div>

                <div class="flex items-center justify-center min-h-96 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-200 relative">
                    <!-- Loading Overlay -->
                    <div x-show="loading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center z-10">
                        <div class="text-center">
                            <div class="relative">
                                <!-- Spinning QR Icon -->
                                <div class="w-16 h-16 bg-[#138c79]/10 rounded-xl flex items-center justify-center mx-auto mb-4 animate-pulse">
                                    <svg class="w-8 h-8 text-[#138c79] animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </div>
                                <!-- Progress Bar -->
                                <div class="w-32 h-1 bg-gray-200 rounded-full mx-auto mb-3 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-[#138c79] to-[#0f7a69] rounded-full animate-pulse" style="width: 100%; animation: progress 1.5s ease-in-out infinite;"></div>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-[#138c79]">Generating QR Code...</p>
                            <p class="text-xs text-gray-500 mt-1">Please wait</p>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!qrImage && !loading" x-transition class="text-center text-gray-500">
                        <div class="w-20 h-20 bg-gray-200 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-lg font-medium">QR Code Preview</p>
                        <p class="text-sm">QR Code akan muncul di sini secara real-time</p>
                    </div>

                    <!-- QR Code Display -->
                    <div x-show="qrImage && !loading" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="text-center">
                        <div class="qr-container inline-block"
                             :class="{
                                 'frame-square': form.frame_style === 'square',
                                 'frame-circle': form.frame_style === 'circle',
                                 'frame-rounded': form.frame_style === 'rounded'
                             }"
                             :style="form.frame_style !== 'none' ? `--frame-color: ${form.frame_color}` : ''">
                            <img :src="qrImage" alt="QR Code" class="max-w-full max-h-80 mx-auto qr-image">
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            <span x-text="form.size + 'x' + form.size + 'px'"></span> •
                            <span x-text="form.error_correction + ' Error Correction'"></span>
                        </div>
                    </div>
                </div>

                <div x-show="qrImage" class="mt-6 space-y-4">
                    <!-- Simpan QR Button -->
                    <button @click="saveQR()" x-show="!qrSaved" class="w-full bg-[#138c79] hover:bg-[#0f7a69] text-white font-semibold py-3 px-4 rounded-xl transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Simpan QR
                    </button>

                    <!-- Download Options -->
                    <div x-show="qrSaved" class="space-y-3">
                        <div class="flex gap-3 justify-center">
                            <button @click="downloadQR('png')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                PNG
                            </button>
                            <button @click="downloadQR('jpg')" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                JPG
                            </button>
                            <button @click="downloadQR('svg')" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                SVG
                            </button>
                            <button @click="downloadQR('webp')" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                WebP
                            </button>
                        </div>

                        <!-- Bagikan QR Button -->
                        <button @click="shareQR()" class="w-full bg-[#138c79] hover:bg-[#0f7a69] text-white font-semibold py-3 px-4 rounded-xl transition duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Bagikan QR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function qrGenerator() {
    return {
        loading: false,
        qrImage: null,
        qrSaved: false,
        debounceTimer: null,
        form: {
            type: 'text',
            content: 'QR Anggurin - Generator QR Code Gratis',
            phone: '',
            message: '',
            email: '',
            subject: '',
            latitude: '',
            longitude: '',
            ssid: '',
            password: '',
            security: 'WPA',
            hidden: false,
            name: '',
            organization: '',
            website: '',
            size: 300,
            margin: 10,
            foreground_color: '#000000',
            background_color: '#ffffff',
            error_correction: 'M',
            logo: null,
            logoType: 'none',
            defaultLogo: null,
            logoSize: 20,
            frame_style: 'none',
            frame_color: '#138c79',
            inner_eye_style: 'square',
            outer_eye_style: 'square',
            data_style: 'square'
        },

        init() {
            // Generate initial QR code with auto-update
            this.$nextTick(() => {
                this.autoGenerate();
            });
        },

        updateFormFields() {
            // Reset form fields when type changes
            this.form.content = '';
            this.form.phone = '';
            this.form.message = '';
            this.form.email = '';
            this.form.subject = '';
            this.form.latitude = '';
            this.form.longitude = '';
            this.form.ssid = '';
            this.form.password = '';
            this.form.name = '';
            this.form.organization = '';
            this.form.website = '';

            // Set default content based on type
            switch(this.form.type) {
                case 'text':
                    this.form.content = 'QR Anggurin - Generator QR Code Gratis';
                    break;
                case 'url':
                    this.form.content = 'https://qr-anggurin.com';
                    break;
                case 'sms':
                    this.form.phone = '+62812345678';
                    this.form.message = 'Halo dari QR Anggurin!';
                    break;
                case 'whatsapp':
                    this.form.phone = '+62812345678';
                    this.form.message = 'Halo! Saya tertarik dengan layanan Anda.';
                    break;
                case 'phone':
                    this.form.phone = '+62812345678';
                    break;
                case 'email':
                    this.form.email = 'hello@qr-anggurin.com';
                    this.form.subject = 'Pertanyaan tentang QR Anggurin';
                    this.form.body = 'Halo, saya ingin bertanya tentang...';
                    break;
                case 'location':
                    this.form.latitude = '-6.2088';
                    this.form.longitude = '106.8456';
                    break;
                case 'wifi':
                    this.form.ssid = 'QR-Anggurin-WiFi';
                    this.form.password = 'password123';
                    this.form.security = 'WPA';
                    break;
                case 'vcard':
                    this.form.name = 'QR Anggurin';
                    this.form.organization = 'QR Generator Indonesia';
                    this.form.title = 'QR Code Service';
                    this.form.phone = '+62812345678';
                    this.form.email = 'contact@qr-anggurin.com';
                    this.form.website = 'https://qr-anggurin.com';
                    break;
            }

            // Auto-generate QR after updating fields
            this.$nextTick(() => {
                this.autoGenerate();
            });
        },

        handleLogoUpload(event) {
            this.form.logo = event.target.files[0];
            this.autoGenerate();
        },

        handleLogoDrop(event) {
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    this.form.logo = file;
                    this.autoGenerate();
                }
            }
        },

        autoGenerate() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                if (this.hasValidContent()) {
                    this.generateQR();
                }
            }, 300); // Reduced delay for faster response
        },

        debouncedGenerate() {
            // Keep for backward compatibility
            this.autoGenerate();
        },

        hasValidContent() {
            switch (this.form.type) {
                case 'text':
                case 'url':
                case 'phone':
                    return this.form.content.trim() !== '';
                case 'sms':
                case 'whatsapp':
                    return this.form.phone.trim() !== '';
                case 'email':
                    return this.form.email.trim() !== '';
                case 'location':
                    return this.form.latitude.trim() !== '' && this.form.longitude.trim() !== '';
                case 'wifi':
                    return this.form.ssid.trim() !== '';
                case 'vcard':
                    return this.form.name.trim() !== '';
                default:
                    return true;
            }
        },

        async generateQR() {
            this.loading = true;

            try {
                const formData = new FormData();
                Object.keys(this.form).forEach(key => {
                    if (key === 'logo' && this.form.logoType === 'upload' && this.form[key]) {
                        formData.append(key, this.form[key]);
                    } else if (key === 'defaultLogo' && this.form.logoType === 'default' && this.form[key]) {
                        formData.append(key, this.form[key]);
                    } else if (key !== 'logo' && key !== 'defaultLogo' && this.form[key] !== null && this.form[key] !== '') {
                        formData.append(key, this.form[key]);
                    }
                });

                const response = await fetch('{{ route("qr.generate") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const blob = await response.blob();
                    this.qrImage = URL.createObjectURL(blob);
                    this.qrSaved = false; // Reset saved state when new QR is generated

                    // Apply custom styling if needed
                    this.$nextTick(() => {
                        this.applyCustomStyling();
                    });
                } else {
                    const errorData = await response.json();
                    console.error('QR Generation Error:', errorData);
                    // Don't show alert for validation errors, just log them
                    if (response.status !== 422) {
                        alert('Error generating QR code: ' + (errorData.error || 'Unknown error'));
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                // Only show alert for network errors, not validation errors
                if (!error.message.includes('422')) {
                    alert('Network error. Please check your connection and try again.');
                }
            } finally {
                this.loading = false;
            }
        },

        saveQR() {
            this.qrSaved = true;
        },

        async downloadQR(format) {
            if (!this.qrImage) return;

            try {
                const formData = new FormData();
                formData.append('format', format);

                // Add all form data
                Object.keys(this.form).forEach(key => {
                    if (key === 'logo' && this.form.logoType === 'upload' && this.form[key]) {
                        formData.append(key, this.form[key]);
                    } else if (key === 'defaultLogo' && this.form.logoType === 'default' && this.form[key]) {
                        formData.append(key, this.form[key]);
                    } else if (key !== 'logo' && key !== 'defaultLogo' && this.form[key] !== null && this.form[key] !== '') {
                        formData.append(key, this.form[key]);
                    }
                });

                const response = await fetch('{{ route("qr.download") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = URL.createObjectURL(blob);
                    this.downloadFile(url, `qrcode.${format}`);
                    URL.revokeObjectURL(url);
                } else {
                    const errorData = await response.json();
                    console.error('Download Error:', errorData);
                    alert('Error downloading QR code: ' + (errorData.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error downloading QR:', error);
                alert('Gagal download QR Code');
            }
        },

        downloadFile(url, filename) {
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        },

        async shareQR() {
            if (!this.qrImage) return;

            try {
                // Convert blob URL to actual blob
                const response = await fetch(this.qrImage);
                const blob = await response.blob();
                const file = new File([blob], 'qrcode.png', { type: 'image/png' });

                if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        title: 'QR Code - QR Anggurin',
                        text: 'QR Code yang dibuat dengan QR Anggurin',
                        files: [file]
                    });
                } else if (navigator.clipboard && navigator.clipboard.write) {
                    // Fallback: copy to clipboard
                    await navigator.clipboard.write([
                        new ClipboardItem({
                            'image/png': blob
                        })
                    ]);
                    alert('QR Code berhasil disalin ke clipboard!');
                } else {
                    // Final fallback: download
                    this.downloadFile(this.qrImage, 'qrcode.png');
                    alert('Browser tidak mendukung sharing. QR Code akan didownload.');
                }
            } catch (error) {
                console.error('Error sharing QR:', error);
                // Fallback to download
                this.downloadFile(this.qrImage, 'qrcode.png');
                alert('Gagal share QR Code. File akan didownload sebagai gantinya.');
            }
        },

        scrollToPreview() {
            const previewSection = document.getElementById('preview-section');
            if (previewSection) {
                previewSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        },

        setDefaultLogo() {
            this.form.defaultLogo = this.form.type;
        },

        getDefaultLogoSVG() {
            const logos = {
                text: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`,
                url: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`,
                sms: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`,
                whatsapp: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/></svg>`,
                phone: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`,
                email: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`,
                location: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`,
                wifi: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`,
                vcard: `<svg fill="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>`
            };
            return logos[this.form.type] || logos.text;
        },

        applyCustomStyling() {
            // Note: Advanced QR styling (eye shapes, data patterns) requires
            // specialized QR libraries or post-processing. For now, we'll add
            // visual feedback that these options are selected.

            const qrContainer = document.querySelector('.qr-container');
            if (!qrContainer) return;

            // Add styling classes based on selected options
            qrContainer.classList.remove('style-applied');

            if (this.form.inner_eye_style !== 'square' ||
                this.form.outer_eye_style !== 'square' ||
                this.form.data_style !== 'square') {
                qrContainer.classList.add('style-applied');

                // Add a subtle overlay to indicate custom styling is applied
                let overlay = qrContainer.querySelector('.style-overlay');
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.className = 'style-overlay';
                    overlay.style.cssText = `
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: linear-gradient(45deg, transparent 49%, rgba(19, 140, 121, 0.1) 50%, transparent 51%);
                        pointer-events: none;
                        border-radius: inherit;
                    `;
                    qrContainer.appendChild(overlay);
                }
            } else {
                const overlay = qrContainer.querySelector('.style-overlay');
                if (overlay) {
                    overlay.remove();
                }
            }
        }
    }
}
</script>
@endsection
