@extends('layouts.app')

@section('title', 'Generator QR Code - QR Anggurin')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Generator QR Code</h1>
            <p class="text-lg text-gray-600">Buat QR Code dengan kustomisasi lengkap</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="qrGenerator()" x-init="init()">
            <!-- Left Panel - Form -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form @submit.prevent="generateQR">
                    <!-- QR Type Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis QR Code</label>
                        <select x-model="form.type" @change="updateFormFields" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="text">Teks</option>
                            <option value="url">URL/Website</option>
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="phone">Telepon</option>
                            <option value="email">Email</option>
                            <option value="location">Lokasi</option>
                            <option value="wifi">WiFi</option>
                            <option value="vcard">VCard</option>
                        </select>
                    </div>

                    <!-- Dynamic Form Fields -->
                    <div class="mb-6" x-show="form.type === 'text' || form.type === 'url'">
                        <label class="block text-sm font-medium text-gray-700 mb-2" x-text="form.type === 'text' ? 'Teks' : 'URL'"></label>
                        <textarea x-model="form.content" @input="debouncedGenerate" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" :placeholder="form.type === 'text' ? 'Masukkan teks...' : 'https://example.com'"></textarea>
                    </div>

                    <!-- SMS Fields -->
                    <div x-show="form.type === 'sms'">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="text" x-model="form.phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="+628123456789">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                            <textarea x-model="form.message" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" placeholder="Pesan SMS..."></textarea>
                        </div>
                    </div>

                    <!-- WhatsApp Fields -->
                    <div x-show="form.type === 'whatsapp'">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp</label>
                            <input type="text" x-model="form.phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="628123456789">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                            <textarea x-model="form.message" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" placeholder="Pesan WhatsApp..."></textarea>
                        </div>
                    </div>

                    <!-- Phone Field -->
                    <div class="mb-6" x-show="form.type === 'phone'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" x-model="form.content" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="+628123456789">
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
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Kustomisasi</h3>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                                <input type="number" x-model="form.size" @input="debouncedGenerate" min="100" max="1000" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Margin</label>
                                <input type="number" x-model="form.margin" @input="debouncedGenerate" min="0" max="50" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Warna Depan</label>
                                <input type="color" x-model="form.foreground_color" @change="debouncedGenerate" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Warna Belakang</label>
                                <input type="color" x-model="form.background_color" @change="debouncedGenerate" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Error Correction</label>
                            <select x-model="form.error_correction" @change="debouncedGenerate" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="L">Low (7%)</option>
                                <option value="M">Medium (15%)</option>
                                <option value="Q">Quartile (25%)</option>
                                <option value="H">High (30%)</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                            <input type="file" @change="handleLogoUpload" accept="image/*" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200" :disabled="loading">
                        <span x-show="!loading">Generate QR Code</span>
                        <span x-show="loading">Generating...</span>
                    </button>
                </form>
            </div>

            <!-- Right Panel - Preview -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Preview</h3>
                <div class="flex items-center justify-center min-h-96 bg-gray-50 rounded-lg">
                    <div x-show="!qrImage" class="text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p>QR Code akan muncul di sini</p>
                    </div>
                    <img x-show="qrImage" :src="qrImage" alt="QR Code" class="max-w-full max-h-96">
                </div>

                <div x-show="qrImage" class="mt-4">
                    <a :href="qrImage" download="qrcode.png" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 block text-center">
                        Download QR Code
                    </a>
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
            logo: null
        },

        init() {
            // Generate initial QR code
            this.generateQR();
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
        },

        handleLogoUpload(event) {
            this.form.logo = event.target.files[0];
            this.debouncedGenerate();
        },

        debouncedGenerate() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                if (this.hasValidContent()) {
                    this.generateQR();
                }
            }, 500);
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
                    if (this.form[key] !== null && this.form[key] !== '') {
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
                } else {
                    alert('Error generating QR code');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error generating QR code');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
