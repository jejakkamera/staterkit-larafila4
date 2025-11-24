# Laravel Livewire Starter Kit - Database Backup Management System

Aplikasi web modern yang dibangun dengan Laravel 12 dan Livewire 3 untuk manajemen backup database dengan dukungan AI Assistant yang didukung OpenAI.

## 📋 Daftar Isi
- [Tentang Aplikasi](#tentang-aplikasi)
- [Fitur Utama](#fitur-utama)
- [Prasyarat Sistem](#prasyarat-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Teknologi & Library](#teknologi--library)
- [Struktur Proyek](#struktur-proyek)
- [Panduan Penggunaan](#panduan-penggunaan)

---

## 🎯 Tentang Aplikasi

Aplikasi ini adalah sistem manajemen yang komprehensif dengan fokus pada:

1. **Backup Database Otomatis** - Buat, kelola, dan pantau backup database dengan mudah
2. **AI Assistant** - Chat dengan OpenAI untuk mendapatkan rekomendasi backup management
3. **Dashboard Admin** - Interface admin yang intuitif untuk mengelola sistem
4. **User Management** - Sistem autentikasi dan role management
5. **Real-time Monitoring** - Pantau progress backup secara real-time

---

## ✨ Fitur Utama

### 1. Database Backup Management
- 📦 Buat backup database on-demand
- 📋 Tampilkan daftar semua backup
- ⬇️ Download backup files
- 🗑️ Hapus backup yang tidak diperlukan
- 📊 Pantau ukuran dan tanggal backup
- ⏱️ Progress tracking real-time

### 2. AI Assistant (Chat dengan OpenAI)
- 💬 Chat interaktif untuk mengelola backup
- 🤖 Intent detection (list, create, delete, check)
- 📚 Context-aware responses dengan MCP (Model Context Protocol)
- 🎯 Natural language commands
- 💾 Chat history persistence

### 3. Dashboard & UI
- 🎨 Dark mode support
- 📱 Responsive design
- ⚡ Real-time updates dengan Livewire
- 🧩 Component-based architecture
- 🎯 Breadcrumb navigation

### 4. User & Security
- 👤 User authentication (Fortify)
- 🔐 Role-based access control
- 🔒 Two-factor authentication support
- 👥 User impersonation (admin only)

---

## 🛠️ Prasyarat Sistem

### Server Requirements
- **PHP**: 8.2 atau lebih tinggi
- **Composer**: Terbaru
- **Node.js**: 18+ (untuk asset compilation)
- **npm**: 9+ (untuk package management)
- **Database**: SQLite (default) atau MySQL/PostgreSQL
- **MySQL/MariaDB**: Untuk backup (opsional tergantung DB yang digunakan)

### Software Required
- Git
- Terminal/Command Line
- Text Editor atau IDE (VS Code, PhpStorm, dll)

---

## 📥 Instalasi

### Langkah 1: Clone Repository
```bash
git clone https://github.com/jejakkamera/staterkit-larafila4.git
cd my-app
```

### Langkah 2: Install PHP Dependencies
```bash
composer install
```

### Langkah 3: Setup Environment File
```bash
cp .env.example .env
```

Atau gunakan script setup otomatis:
```bash
composer run-script setup
```

### Langkah 4: Generate Application Key
```bash
php artisan key:generate
```

### Langkah 5: Create Database File (untuk SQLite)
```bash
touch database/database.sqlite
```

### Langkah 6: Run Database Migrations
```bash
php artisan migrate
```

### Langkah 7: Install Node Dependencies
```bash
npm install
```

### Langkah 8: Compile Assets
```bash
npm run build
```

---

## ⚙️ Konfigurasi

### 1. Database Configuration (.env)

**SQLite (Default):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=/full/path/to/database.sqlite
```

**MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

**PostgreSQL:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=postgres
DB_PASSWORD=
```

### 2. OpenAI Configuration (.env)

```env
OPENAI_API_KEY=sk-xxx...xxx
```

Dapatkan API key dari: https://platform.openai.com/api-keys

### 3. Mail Configuration (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@example.com"
```

### 4. Session Configuration (.env)
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### 5. Queue Configuration (.env)
```env
QUEUE_CONNECTION=database
```

---

## 🚀 Menjalankan Aplikasi

### Development Mode (All-in-One)
```bash
composer run dev
```

Command ini akan menjalankan:
- Laravel Development Server (port 8000)
- Queue Worker
- Pail (Log Monitor)
- Vite Dev Server (Hot Reload)

### Atau Jalankan Secara Terpisah

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Queue Worker:**
```bash
php artisan queue:listen --tries=1
```

**Terminal 3 - Assets Dev Server:**
```bash
npm run dev
```

**Terminal 4 - Log Monitor (Opsional):**
```bash
php artisan pail --timeout=0
```

### Production Build
```bash
npm run build
```

---

## 📚 Teknologi & Library

### Backend (PHP/Laravel)

| Library | Version | Fungsi |
|---------|---------|--------|
| **Laravel Framework** | ^12.0 | Web framework utama |
| **Livewire** | ^3.0 | Reactive components |
| **Livewire Flux** | ^2.1.1 | UI component library |
| **Livewire Volt** | ^1.7.0 | Single-file components |
| **Filament** | ^4.0 | Admin panel & tables |
| **Laravel Fortify** | ^1.30 | Authentication scaffold |
| **OpenAI PHP** | ^0.18.0 | OpenAI API integration |
| **Carbon** | ^3.0 | Date/time handling |
| **Laravel Tinker** | ^2.10.1 | Interactive shell |

### Frontend (JavaScript/CSS)

| Library | Version | Fungsi |
|---------|---------|--------|
| **Tailwind CSS** | ^4.1.17 | Utility-first CSS framework |
| **Alpine.js** | ^3.x | Lightweight JS framework |
| **Axios** | ^1.7.4 | HTTP client |
| **Vite** | ^7.0.4 | Build tool & dev server |
| **Autoprefixer** | ^10.4.20 | CSS vendor prefixes |

### Testing & Development

| Library | Version | Fungsi |
|---------|---------|--------|
| **Pest** | ^4.1 | Testing framework |
| **Pest Laravel Plugin** | ^4.0 | Laravel testing integration |
| **Mockery** | ^1.6 | Mocking library |
| **Faker** | ^1.23 | Fake data generation |
| **Laravel Pint** | ^1.24 | Code style fixer |
| **Laravel Pail** | ^1.2.2 | Log monitoring |

---

## 📁 Struktur Proyek

```
my-app/
├── app/
│   ├── Actions/              # Action classes
│   ├── Helpers/              # Helper functions
│   ├── Http/                 # Controllers & Middleware
│   ├── Jobs/                 # Queue jobs
│   ├── Livewire/             # Livewire components
│   │   └── Admin/
│   │       └── DatabaseBackup.php   # Main backup component
│   ├── Models/               # Eloquent models
│   ├── Mcp/                  # Model Context Protocol
│   │   ├── Servers/
│   │   └── Tools/            # MCP tools
│   └── Providers/            # Service providers
├── bootstrap/                # Bootstrap files
├── config/                   # Configuration files
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── public/                   # Public assets
├── resources/
│   ├── css/                  # CSS files
│   ├── js/                   # JavaScript files
│   └── views/                # Blade templates
├── routes/                   # Route definitions
├── storage/                  # App storage (logs, cache, backups)
├── tests/                    # Test files
├── vendor/                   # Composer packages
├── .env.example              # Environment template
├── artisan                   # Laravel CLI
├── composer.json             # PHP dependencies
├── package.json              # Node dependencies
├── tailwind.config.js        # Tailwind configuration
├── vite.config.js            # Vite configuration
└── README.md                 # This file
```

---

## 💻 Panduan Penggunaan

### Login & Setup User
1. Buka aplikasi di browser: `http://localhost:8000`
2. Register akun baru atau login dengan credentials default
3. Masuk ke dashboard admin

### Membuat Backup Database
1. Navigasi ke: **Admin → Database Backup**
2. Klik tombol **"Create Backup Now"**
3. Tunggu progress bar selesai
4. Backup akan disimpan di: `storage/app/backups/`

### Menggunakan AI Assistant
1. Pada halaman Database Backup, klik tombol **"Ask AI"**
2. Ketik pertanyaan atau perintah, misalnya:
   - "Create a backup"
   - "List all backups"
   - "How much storage do backups use?"
   - "Delete the oldest backup"
3. AI akan merespons dengan saran atau aksi

### Download & Delete Backup
1. Di tabel "Available Backups"
2. Klik tombol **"Download"** untuk mengunduh file backup
3. Klik tombol **"Delete"** untuk menghapus backup

### Queue Management
Untuk melihat jobs yang antri:
```bash
php artisan queue:failed       # Lihat failed jobs
php artisan queue:retry all    # Retry semua failed jobs
php artisan queue:forget {id}  # Hapus specific job
```

### Testing
```bash
# Run semua tests
php artisan test

# Run tests dengan output verbose
php artisan test --verbose

# Run specific test file
php artisan test tests/Feature/BackupTest.php
```

---

## 🔑 Environment Variables

Key environment variables yang penting:

```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Queue & Session
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# AI & APIs
OPENAI_API_KEY=sk-...

# Mail
MAIL_MAILER=log
```

---

## 📖 Dokumentasi Lebih Lanjut

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/)
- [Filament Documentation](https://filamentphp.com/)
- [OpenAI PHP Documentation](https://github.com/openai-php/laravel)

---

## 🐛 Troubleshooting

### Error: "No such file or directory" (database.sqlite)
```bash
touch database/database.sqlite
php artisan migrate
```

### Error: "Call to undefined function"
```bash
composer dump-autoload
php artisan cache:clear
```

### Queue jobs tidak berjalan
```bash
# Pastikan queue listener berjalan
php artisan queue:listen

# Atau gunakan daemon
php artisan queue:work
```

### Assets tidak di-compile
```bash
npm run build
# atau untuk development
npm run dev
```

### Livewire component error
```bash
php artisan livewire:publish
php artisan cache:clear
php artisan config:clear
```

---

## 📄 License

MIT License - lihat file LICENSE untuk detail.

---

## 👨‍💻 Support & Contribution

Untuk bug reports, feature requests, atau kontribusi:
1. Buat issue di GitHub
2. Fork repository
3. Submit pull request

---

**Last Updated:** November 24, 2025  
**Version:** 1.0.0
