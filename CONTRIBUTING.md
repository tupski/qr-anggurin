# Contributing to QR Anggurin

Terima kasih atas minat Anda untuk berkontribusi pada QR Anggurin! Kami menyambut kontribusi dari semua orang.

## Cara Berkontribusi

### 1. Fork Repository
- Fork repository ini ke akun GitHub Anda
- Clone fork Anda ke komputer lokal

### 2. Setup Development Environment
```bash
# Clone repository
git clone https://github.com/YOUR_USERNAME/qr-anggurin.git
cd qr-anggurin

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate

# Build assets
npm run build
```

### 3. Buat Branch Baru
```bash
git checkout -b feature/nama-fitur-baru
```

### 4. Lakukan Perubahan
- Ikuti coding standards yang ada
- Tulis kode yang bersih dan terdokumentasi
- Tambahkan tests jika diperlukan

### 5. Testing
```bash
# Run tests
php artisan test

# Build assets
npm run build
```

### 6. Commit Changes
```bash
git add .
git commit -m "feat: deskripsi singkat perubahan"
```

### 7. Push dan Create Pull Request
```bash
git push origin feature/nama-fitur-baru
```

Kemudian buat Pull Request di GitHub.

## Coding Standards

### PHP/Laravel
- Ikuti PSR-12 coding standard
- Gunakan type hints
- Tulis docblocks untuk methods
- Gunakan meaningful variable names

### Frontend
- Gunakan Tailwind CSS untuk styling
- Ikuti Alpine.js best practices
- Pastikan responsive design

### Git Commit Messages
Gunakan format conventional commits:
- `feat:` untuk fitur baru
- `fix:` untuk bug fixes
- `docs:` untuk dokumentasi
- `style:` untuk formatting
- `refactor:` untuk refactoring
- `test:` untuk tests

## Jenis Kontribusi yang Diterima

### 🐛 Bug Reports
- Gunakan GitHub Issues
- Sertakan langkah reproduksi
- Sertakan screenshot jika perlu

### 💡 Feature Requests
- Diskusikan di GitHub Issues terlebih dahulu
- Jelaskan use case dan manfaatnya

### 🔧 Code Contributions
- Bug fixes
- New features
- Performance improvements
- UI/UX improvements

### 📚 Documentation
- README improvements
- Code comments
- API documentation

## Development Guidelines

### QR Code Features
- Pastikan kompatibilitas dengan berbagai QR readers
- Test dengan berbagai ukuran dan error correction levels
- Validasi input data

### UI/UX
- Maintain consistency dengan design yang ada
- Ensure accessibility
- Test di berbagai device sizes

### Performance
- Optimize image generation
- Minimize JavaScript bundle size
- Efficient database queries

## Questions?

Jika ada pertanyaan, silakan:
- Buka GitHub Issue
- Contact maintainer: [tupski](https://github.com/tupski)

Terima kasih atas kontribusi Anda! 🎉
