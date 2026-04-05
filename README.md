# IQRA' CREATIVE HOUSE - School Management Information System

<p align="center">
  <img src="https://via.placeholder.com/400x200?text=IQRA'+CREATIVE+HOUSE" alt="IQRA' Creative House Logo" width="400">
</p>

<p align="center">
  <strong>Sistem Informasi Manajemen Terpusat dan Terintegrasi untuk TK</strong>
  <br>
  <em>Transformasi dari Sistem Manual ke Sistem Otomatis dengan Centralized Data Management</em>
</p>

---

## 📋 Tentang Sistem

IQRA' CREATIVE HOUSE Information System adalah platform manajemen sekolah terintegrasi yang dirancang khusus untuk Taman Kanak-kanak (TK) modern. Sistem ini mengotomatisasi seluruh proses operasional, akademik, dan finansial dengan arsitektur terpusat untuk mengurangi human error dan meningkatkan efisiensi.

### 🎯 Visi

Menyediakan solusi teknologi yang mudah digunakan untuk meningkatkan akurasi data, efisiensi operasional, dan transparansi informasi di TK IQRA' CREATIVE HOUSE.

### 🚀 Fitur Utama

#### 1. **Manajemen Akademik** 📚

- Manajemen kelas dan mata pelajaran
- Sistem penilaian siswa (grading)
- Laporan kinerja siswa otomatis
- Manajemen guru (TK & Guru Ngaji)

#### 2. **Manajemen Keuangan** 💳

- Tracking SPP (Sumbangan Pembinaan Pendidikan) bulanan
- Manajemen biaya pendaftaran dengan sistem cicilan 3x
- Payment gateway integration
- Invoice generation otomatis
- Financial reporting & reconciliation

#### 3. **Sistem Kehadiran** 📍

- GPS-based attendance tracking dengan geofencing
- Real-time check-in/check-out
- Notifikasi otomatis ke orang tua
- Laporan kehadiran terperinci

#### 4. **Manajemen Tabungan Siswa** 🏦

- Digital passbook siswa
- Transaction tracking (setor/tarik)
- Automated passbook generation
- Savings analytics & reporting

#### 5. **Sistem Notifikasi** 📧

- Email notifications
- SMS notifications
- Push notifications
- Broadcast messages

#### 6. **Reporting & Analytics** 📊

- Real-time dashboards
- Academic performance analytics
- Financial forecasting
- Customizable reports

---

## 🏗️ Teknologi Stack

### Backend

- **Framework**: Laravel 11.x
- **Database**: MySQL 8.0+
- **API**: RESTful API dengan Laravel Sanctum
- **Authentication**: Multi-role authentication

### Architecture

- **Pattern**: Service Layer Architecture
- **Database Design**: Normalization (3NF)
- **ORM**: Eloquent
- **Repository Pattern**: For clean data access

### Frontend (Optional)

- **Framework**: React / Vue.js
- **Build Tool**: Vite
- **HTTP Client**: Axios

---

## 📊 Database Schema

Sistem menggunakan 15+ tabel yang terintegrasi mencakup:

- **Authentication & Users** - Users, Roles, Admin, Foundation Heads
- **Academic** - Teachers, Students, Classes, Grades, Subjects
- **Finance** - SPP Invoices, Payments, Registration Fees, Installments
- **Attendance** - Records, Geofence Zones
- **Savings** - Ledgers, Transactions, Student Passbooks

---

## 🚀 Getting Started

### Prerequisites

- PHP >= 8.1
- MySQL >= 8.0
- Composer
- Node.js >= 16

### Installation

```bash
# 1. Clone repository
git clone https://github.com/yourusername/ich-pendidikan.git
cd ich-pendidikan

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ich_pendidikan
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. Seed data (optional)
php artisan db:seed

# 7. Run development server
php artisan serve
npm run dev  # Terminal terpisah

# Server berjalan di http://localhost:8000
```

---

## 📚 API Documentation

### Authentication

```http
POST /api/register          # Register user baru
POST /api/login             # Login & get token
POST /api/logout            # Logout (protected)
```

### Academic

```http
GET /api/students           # Get all students
GET /api/grades?student_id=1  # Get student grades
POST /api/grades            # Record new grade
GET /api/classes            # Get all classes
```

### Finance

```http
GET /api/payments           # Get all payments
POST /api/payments          # Create payment
GET /api/invoices           # Get invoices
GET /api/invoices/{id}      # Get invoice detail
```

### Attendance

```http
POST /api/attendance        # Record attendance (check-in/out)
GET /api/attendance-report  # Get attendance report
```

### Savings

```http
GET /api/savings            # Get savings data
POST /api/savings/transactions  # Record transaction
GET /api/passbook/{id}      # Get student passbook
```

Lihat `/docs/API.md` untuk dokumentasi lengkap dengan request/response examples.

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/PaymentTest.php

# Run with coverage
php artisan test --coverage
```

---

## 🔐 Security

- ✅ Laravel Sanctum authentication dengan API tokens
- ✅ Role-based access control (RBAC) - 5 roles
- ✅ Input validation & sanitization
- ✅ CSRF protection
- ✅ Password hashing dengan bcrypt
- ✅ Rate limiting
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ Audit trails & logging

---

## 📈 Performance

- Query optimization dengan eager loading
- Database indexing on foreign keys & frequently searched columns
- Pagination untuk large datasets
- Caching strategies
- Response compression

---

## 🐛 Troubleshooting

### Vite Manifest Not Found

```bash
npm install
npm run build
php artisan serve
```

### Database Connection Error

Pastikan MySQL service running dan `.env` configuration benar.

### Permission Denied

```bash
chmod -R 777 storage/ bootstrap/cache/
```

Lihat `/docs/TROUBLESHOOTING.md` untuk masalah lebih lanjut.

---

## 📝 Contributing

Terima kasih telah mempertimbangkan kontribusi!

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📞 Support & Contact

- **Email**: support@iqracreativehouse.com
- **Phone**: +62-XXX-XXXX-XXXX
- **Issues**: https://github.com/yourusername/ich-pendidikan/issues
- **Website**: https://iqracreativehouse.com

---

## 📄 License

Licensed under the MIT license - lihat [LICENSE](LICENSE) untuk detail.

---

## 👏 Acknowledgments

- Laravel Community
- IQRA' CREATIVE HOUSE - Institusi pendidikan yang mendukung
- Tim development yang berkontribusi

---

<p align="center">
  <strong>Built with ❤️ for IQRA' CREATIVE HOUSE</strong>
  <br>
  <em>School Management Information System v1.0.0</em>
</p>

---

**Last Updated**: 2024  
**Version**: 1.0.0  
**Status**: In Development
