# QR Anggurin - Generator & Scanner QR Code Gratis

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red.svg" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

QR Anggurin adalah aplikasi web gratis untuk membuat dan memindai QR Code dengan berbagai fitur kustomisasi lengkap. Dibangun dengan Laravel 12 dan Tailwind CSS untuk pengalaman pengguna yang optimal.

## 🚀 Fitur Utama

### Generator QR Code
- **Berbagai Jenis QR Code**: Teks, URL, SMS, WhatsApp, Telepon, Email, Lokasi, WiFi, dan VCard
- **Kustomisasi Lengkap**: 
  - Warna foreground dan background
  - Ukuran QR Code (100px - 1000px)
  - Margin (0px - 50px)
  - Error correction level (L, M, Q, H)
  - Logo/branding dengan upload gambar
- **Preview Real-time**: Melihat perubahan QR Code secara langsung
- **Layout Desktop**: Panel kiri untuk kustomisasi, panel kanan untuk preview

### Scanner QR Code
- **Multiple Input Methods**: Upload file, URL gambar, dan kamera (coming soon)
- **Auto Detection**: Mendeteksi jenis QR Code secara otomatis
- **Action Buttons**: Copy, buka URL, telepon, kirim email sesuai jenis QR

### Interface
- **Responsive Design**: Optimal untuk desktop dan mobile
- **Modern UI**: Menggunakan Tailwind CSS dan Alpine.js
- **User Friendly**: Interface yang intuitif dan mudah digunakan

## 🛠️ Teknologi

- **Backend**: Laravel 12.x
- **Frontend**: Tailwind CSS, Alpine.js
- **QR Code Library**: Endroid QR Code
- **Database**: MySQL
- **Build Tool**: Vite

## 📋 Persyaratan Sistem

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL 8.0+
- Web server (Apache/Nginx)

## 🚀 Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/tupski/qr-anggurin.git
   cd qr-anggurin
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   Edit file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=
   DB_USERNAME=
   DB_PASSWORD=
   ```

5. **Database Migration**
   ```bash
   php artisan migrate
   ```

6. **Build Assets**
   ```bash
   npm run build
   ```

7. **Run Application**
   ```bash
   php artisan serve
   ```

   Aplikasi akan berjalan di `http://localhost:8000`

## 🎯 Penggunaan

### Membuat QR Code
1. Kunjungi halaman Generator
2. Pilih jenis QR Code yang diinginkan
3. Isi data yang diperlukan
4. Sesuaikan kustomisasi (warna, ukuran, logo, dll)
5. QR Code akan ter-generate secara otomatis
6. Download QR Code dalam format PNG

### Scan QR Code
1. Kunjungi halaman Scanner
2. Pilih metode input (Upload file atau URL gambar)
3. Upload gambar QR Code atau masukkan URL
4. Klik "Scan QR Code"
5. Lihat hasil scan dengan action buttons yang tersedia

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan baca [CONTRIBUTING.md](CONTRIBUTING.md) untuk panduan lengkap.

## 📝 License

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

## 👨‍💻 Developer

Dikembangkan dengan ❤️ oleh [tupski](https://github.com/tupski)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - Web framework
- [Endroid QR Code](https://github.com/endroid/qr-code) - QR Code generation
- [Tailwind CSS](https://tailwindcss.com) - CSS framework
- [Alpine.js](https://alpinejs.dev) - JavaScript framework

---

**QR Anggurin** - Gratis untuk semua! 🎉
