# SIKAPRI — SISTEM INFORMASI KABAR PRIANGAN
### Harian Umum Kabar Priangan (PT Berkah Pikiran Rakyat)
*(Koran Cetak Harian • Portal Media Online • Priangan TV • Distribusi Sirkulasi • Keuangan & Kasir)*

Aplikasi Sistem Informasi Manajemen Pers Terpadu (*Integrated Newsroom, Advertising & Circulation Enterprise System*) untuk pengelolaan operasional surat kabar regional Priangan Timur (Tasikmalaya, Ciamis, Banjar, Garut, Sumedang).

---

## 📖 DOKUMENTASI SISTEM (MASTER SPECIFICATIONS)

Seluruh dokumen analisis kebutuhan, cetak biru arsitektur keamanan, standar desain tampilan, dan spesifikasi divisi telah **DISATUKAN** secara lengkap ke dalam direktori [`docs/`](file:///d:/SI%20KP/docs/):

- 📘 **[docs/prd.md](file:///d:/SI%20KP/docs/prd.md)** — **MASTER PRD (FULL POWER EDITION)**: Cetak biru tunggal komprehensif yang memadukan instruksi keamanan pembimbing (Prinsip CIA Triad, 5 Pilar Keamanan: Backup, Lockout 3x Gagal 15 Menit, RBAC & Status User Active/Review/Disable, Password Kriptografi Argon2id/Bcrypt, SEO Anti-Indexing), Arsitektur Cloud VPS & Local DB, Modul Lengkap Divisi Iklan, Divisi Sirkulasi, Divisi Kasir & Keuangan, Standar Tampilan UI/UX (Pure Black #000000 & Kwitansi Resmi), serta Skema Master Database SQL.
- 🎨 **[docs/tampilan.md](file:///d:/SI%20KP/docs/tampilan.md)** — **Standar Desain Tampilan & UI/UX**: Pedoman baku visual, tipografi hitam pekat kontras tinggi, keselarasan format kwitansi, dan tombol intip kata sandi.
- 📋 **[docs/README.md](file:///d:/SI%20KP/docs/README.md)** — Indeks direktori dokumentasi.

---

## 🛡️ RINGKASAN PRINSIP KEAMANAN UTAMA (CIA TRIAD)

1. **Confidentiality (Kerahasiaan)**: Proteksi privasi data kontrak iklan, tarif keagenan sirkulasi, dan laporan omset. Isolasi akses via RBAC dan blokir total perayap mesin pencari (*noindex, nofollow, robots.txt Disallow: /*).
2. **Integrity (Integritas)**: *Zero Hard Delete Policy* pada akun dan transaksi yang memiliki riwayat audit finansial. Pencatatan setiap aksi penting ke tabel `activity_logs`.
3. **Availability (Ketersediaan)**: Skema otomatisasi pencadangan data (*automated backups*) berkala ke penyimpanan sekunder (*offsite storage*) dan kesiapan data menjelang cutoff operasional jam 17:00 WIB.

---

## 👥 MATRIKS ALOKASI PERAN DIVISI (ROLE QUOTAS)

- **Divisi Sirkulasi** (1 Akun)
- **Divisi Keuangan** (1 Akun)
- **Divisi Kasir** (1 Akun)
- **Divisi Billing** (1 Akun)
- **Divisi Iklan** (1 Akun)
- **Administrator** (Superadmin pengelola sistem, user, backup, dan audit keamanan)

---

## 🚀 PANDUAN MENJALANKAN SISTEM SECARA LOKAL

```bash
# 1. Pastikan dependensi PHP & Composer terinstal
composer install

# 2. Pastikan file konfigurasi .env siap
cp .env.example .env
php artisan key:generate

# 3. Jalankan migrasi basis data
php artisan migrate

# 4. Install & build aset frontend
npm install
npm run build

# 5. Jalankan server lokal
php artisan serve
```
