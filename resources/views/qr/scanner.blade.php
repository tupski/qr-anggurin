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
                    <button @click="method = 'upload'" :class="method === 'upload' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700'" class="p-4 rounded-lg border-2 border-transparent hover:border-primary-300 transition duration-200">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload File
                    </button>
                    <button @click="method = 'url'" :class="method === 'url' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700'" class="p-4 rounded-lg border-2 border-transparent hover:border-primary-300 transition duration-200">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        URL Gambar
                    </button>
                    <button @click="method = 'camera'" :class="method === 'camera' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700'" class="p-4 rounded-lg border-2 border-transparent hover:border-primary-300 transition duration-200">
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
                <label class="block text-sm font-semibold text-gray-700 mb-3">Upload Gambar QR Code</label>
                <div class="relative group">
                    <!-- Upload Area -->
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-300 hover:border-primary-500 hover:bg-primary-50/50 group-hover:shadow-lg"
                         :class="selectedFile ? 'border-primary-500 bg-primary-50/30' : ''"
                         @dragover.prevent="$event.dataTransfer.dropEffect = 'copy'"
                         @drop.prevent="handleFileDrop($event)">

                        <!-- Upload Icon -->
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center transition-transform duration-300 group-hover:scale-110">
                            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>

                        <!-- Upload Text -->
                        <div class="space-y-2">
                            <h4 class="text-lg font-semibold text-gray-900">Upload QR Code</h4>
                            <p class="text-sm text-gray-600">Drag & drop file atau klik untuk memilih</p>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG, GIF, BMP, WEBP (Max 10MB)</p>
                        </div>

                        <!-- Upload Button -->
                        <input type="file" @change="handleFileUpload" accept="image/*" class="hidden" x-ref="fileInput">
                        <button type="button" @click="$refs.fileInput.click()"
                                class="mt-4 inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Pilih File
                        </button>

                        <!-- File Preview -->
                        <div x-show="selectedFile" class="mt-4 p-4 bg-white rounded-lg border border-primary-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedFile?.name || 'QR Code file'"></p>
                                        <p class="text-xs text-gray-500" x-text="selectedFile ? (selectedFile.size / 1024 / 1024).toFixed(2) + ' MB' : ''"></p>
                                    </div>
                                </div>
                                <button type="button" @click="selectedFile = null; $refs.fileInput.value = ''; result = null; error = null;"
                                        class="text-red-500 hover:text-red-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- URL Input -->
            <div x-show="method === 'url'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">URL Gambar QR Code</label>
                <input type="url" x-model="imageUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://example.com/qrcode.png">
            </div>

            <!-- Camera -->
            <div x-show="method === 'camera'" class="mb-6">
                <div class="bg-gray-100 rounded-lg p-4">
                    <div x-show="!cameraActive" class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <button @click="startCamera" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium">
                            Aktifkan Kamera
                        </button>
                    </div>

                    <div x-show="cameraActive" class="text-center">
                        <video x-ref="cameraVideo" class="w-full max-w-md mx-auto rounded-lg mb-4" autoplay playsinline></video>
                        <div class="flex gap-2 justify-center">
                            <button @click="stopCamera" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium">
                                Stop Kamera
                            </button>
                            <button @click="captureFromCamera" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium">
                                Capture & Scan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scan Button (only for upload and URL methods) -->
            <button x-show="method !== 'camera'" @click="scanQR" :disabled="loading || !canScan" class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 mb-6">
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
                    <div class="bg-white p-4 rounded-md border max-h-40 overflow-y-auto">
                        <p x-text="result?.data || 'No data'" class="break-all whitespace-pre-wrap text-sm"></p>
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
        cameraActive: false,
        cameraStream: null,

        get canScan() {
            return (this.method === 'upload' && this.selectedFile) ||
                   (this.method === 'url' && this.imageUrl);
        },

        handleFileUpload(event) {
            this.selectedFile = event.target.files[0];
            this.result = null;
            this.error = null;
        },

        handleFileDrop(event) {
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    this.selectedFile = file;
                    this.result = null;
                    this.error = null;
                }
            }
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

                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();

                    if (data.success) {
                        this.result = data;
                        this.error = null;
                    } else {
                        this.error = data.message || 'Gagal scan QR Code';
                        this.result = null;
                    }
                } else {
                    // Response is not JSON (probably HTML error page)
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    this.error = 'Server error. Please check the console for details.';
                    this.result = null;
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
        },

        async startCamera() {
            try {
                this.cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment' // Use back camera if available
                    }
                });
                this.$refs.cameraVideo.srcObject = this.cameraStream;
                this.cameraActive = true;
                this.error = null;
            } catch (error) {
                console.error('Camera error:', error);
                this.error = 'Tidak dapat mengakses kamera. Pastikan browser memiliki izin kamera.';
            }
        },

        stopCamera() {
            if (this.cameraStream) {
                this.cameraStream.getTracks().forEach(track => track.stop());
                this.cameraStream = null;
            }
            this.cameraActive = false;
        },

        async captureFromCamera() {
            if (!this.cameraActive || !this.$refs.cameraVideo) {
                this.error = 'Kamera tidak aktif';
                return;
            }

            try {
                // Create canvas to capture frame
                const canvas = document.createElement('canvas');
                const video = this.$refs.cameraVideo;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);

                // Convert to blob
                canvas.toBlob(async (blob) => {
                    if (blob) {
                        // Create FormData and scan
                        this.loading = true;
                        this.result = null;
                        this.error = null;

                        try {
                            const formData = new FormData();
                            formData.append('method', 'upload');
                            formData.append('file', blob, 'camera-capture.jpg');

                            const response = await fetch('{{ route("qr.scan") }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });

                            const contentType = response.headers.get('content-type');

                            if (contentType && contentType.includes('application/json')) {
                                const data = await response.json();

                                if (data.success) {
                                    this.result = data;
                                    this.error = null;
                                } else {
                                    this.error = data.message || 'Gagal scan QR Code dari kamera';
                                    this.result = null;
                                }
                            } else {
                                this.error = 'Server error saat scan dari kamera';
                                this.result = null;
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            this.error = 'Terjadi kesalahan saat scan dari kamera';
                        } finally {
                            this.loading = false;
                        }
                    }
                }, 'image/jpeg', 0.8);

            } catch (error) {
                console.error('Capture error:', error);
                this.error = 'Gagal capture gambar dari kamera';
            }
        }
    }
}
</script>
@endsection
