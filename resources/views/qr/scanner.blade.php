@extends('layouts.app')

@section('title', 'Scanner QR Code - QR Anggurin')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Scanner QR Code</h1>
            <p class="text-lg text-gray-600">Scan QR Code dengan upload file, URL, atau kamera</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6" x-data="qrScanner()">
            <!-- Method Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Scan</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button @click="method = 'upload'" :class="method === 'upload' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'" class="p-4 rounded-lg border-2 border-transparent hover:border-indigo-300 transition duration-200">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload File
                    </button>
                    <button @click="method = 'url'" :class="method === 'url' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'" class="p-4 rounded-lg border-2 border-transparent hover:border-indigo-300 transition duration-200">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        URL Gambar
                    </button>
                    <button @click="method = 'camera'" :class="method === 'camera' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'" class="p-4 rounded-lg border-2 border-transparent hover:border-indigo-300 transition duration-200">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Kamera
                    </button>
                </div>
            </div>

            <!-- Upload File -->
            <div x-show="method === 'upload'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar QR Code</label>
                <input type="file" @change="handleFileUpload" accept="image/*" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- URL Input -->
            <div x-show="method === 'url'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">URL Gambar QR Code</label>
                <input type="url" x-model="imageUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://example.com/qrcode.png">
            </div>

            <!-- Camera -->
            <div x-show="method === 'camera'" class="mb-6">
                <div class="bg-gray-100 rounded-lg p-4 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-600">Fitur kamera akan segera tersedia</p>
                    <p class="text-sm text-gray-500 mt-2">Sementara gunakan upload file atau URL gambar</p>
                </div>
            </div>

            <!-- Scan Button -->
            <button @click="scanQR" :disabled="loading || !canScan" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 mb-6">
                <span x-show="!loading">Scan QR Code</span>
                <span x-show="loading">Scanning...</span>
            </button>

            <!-- Results -->
            <div x-show="result" class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Hasil Scan</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis QR Code</label>
                    <span x-text="result?.type || 'Unknown'" class="inline-block bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium"></span>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
                    <div class="bg-white p-4 rounded-md border">
                        <p x-text="result?.data || 'No data'" class="break-all"></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button @click="copyToClipboard" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        Copy
                    </button>
                    <button x-show="result?.type === 'url'" @click="openUrl" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        Buka URL
                    </button>
                    <button x-show="result?.type === 'phone'" @click="callPhone" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        Telepon
                    </button>
                    <button x-show="result?.type === 'email'" @click="sendEmail" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        Kirim Email
                    </button>
                </div>
            </div>

            <!-- Error Message -->
            <div x-show="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-700" x-text="error"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function qrScanner() {
    return {
        method: 'upload',
        loading: false,
        result: null,
        error: null,
        imageUrl: '',
        selectedFile: null,
        
        get canScan() {
            return (this.method === 'upload' && this.selectedFile) || 
                   (this.method === 'url' && this.imageUrl) ||
                   (this.method === 'camera');
        },
        
        handleFileUpload(event) {
            this.selectedFile = event.target.files[0];
            this.result = null;
            this.error = null;
        },
        
        async scanQR() {
            if (!this.canScan) return;
            
            this.loading = true;
            this.result = null;
            this.error = null;
            
            try {
                const formData = new FormData();
                formData.append('method', this.method);
                
                if (this.method === 'upload' && this.selectedFile) {
                    formData.append('file', this.selectedFile);
                } else if (this.method === 'url' && this.imageUrl) {
                    formData.append('url', this.imageUrl);
                }
                
                const response = await fetch('{{ route("qr.scan") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.result = data;
                } else {
                    this.error = data.message || 'Gagal scan QR Code';
                }
            } catch (error) {
                console.error('Error:', error);
                this.error = 'Terjadi kesalahan saat scan QR Code';
            } finally {
                this.loading = false;
            }
        },
        
        copyToClipboard() {
            if (this.result?.data) {
                navigator.clipboard.writeText(this.result.data).then(() => {
                    alert('Konten berhasil disalin!');
                });
            }
        },
        
        openUrl() {
            if (this.result?.data) {
                window.open(this.result.data, '_blank');
            }
        },
        
        callPhone() {
            if (this.result?.data) {
                window.location.href = this.result.data;
            }
        },
        
        sendEmail() {
            if (this.result?.data) {
                window.location.href = this.result.data;
            }
        }
    }
}
</script>
@endsection
