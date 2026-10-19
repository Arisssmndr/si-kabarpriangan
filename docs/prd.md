# MASTER PRODUCT REQUIREMENTS DOCUMENT (PRD) — FULL POWER EDITION
# SIKAPRI — SISTEM INFORMASI KABAR PRIANGAN
**Cetak Biru Terpadu Rekayasa Perangkat Lunak, Manajemen Operasional & Arsitektur Keamanan Standar Industri Perusahaan Pers**
*(Surat Kabar Harian Cetak • Portal Berita Online • Priangan TV • Distribusi Sirkulasi • Keuangan & Kasir)*

---

## 1. DOKUMEN KONTROL & RINGKASAN EKSEKUTIF

| Parameter | Spesifikasi Resmi |
|---|---|
| **Nama Sistem** | **SiKAPRI** (Sistem Informasi Kabar Priangan) |
| **Institusi / Badan Usaha** | **PT Berkah Pikiran Rakyat — Harian Umum Kabar Priangan** (Pikiran Rakyat Media Network) |
| **Versi Dokumen** | **6.0 — Full Power Consolidated Master Edition** |
| **Klasifikasi Dokumen** | **INTERNAL CONFIDENTIAL — STRICTLY PROPRIETARY (SISTEM TERTUTUP)** |
| **Tanggal Pembaruan** | **Oktober 2026** |
| **Dasar Kebijakan** | Pengarahan Dosen Pembimbing Sistem Informasi, Manajemen Redaksi & Bisnis Kabar Priangan |
| **Tumpukan Teknologi (Tech Stack)** | PHP 8.2+ / Laravel 12.x Core / MySQL 8.0+ Enterprise Engine / TailwindCSS & Alpine.js / Blade Engine |

### 1.1 Visi & Tujuan Strategis
SiKAPRI dirancang sebagai sistem informasi manajemen pers terpadu (*Integrated Enterprise Newsroom & Publishing Information System*) untuk menggantikan sistem legacy (*IKL.EXE* berbasis desktop DOS/FoxPro/VB lama) dan mengotomatisasi pencatatan manual yang rentan terhadap *double-entry error*, kebocoran piutang agen, dan keterlambatan *deadline* cetak koran.

Sistem ini memadukan **5 Divisi Operasional + 1 Administrator**:
1. **Divisi Iklan**: Manajemen transaksi iklan cetak koran, Priangan TV, portal online, tarif mmk, dan pembagian insentif/komisi AE.
2. **Divisi Sirkulasi**: Manajemen distribusi fisik surat kabar, alokasi tiras per agen, surat jalan malam, rekonsiliasi retur siang, dan kartu piutang keagenan.
3. **Divisi Kasir**: Penerimaan setoran tunai/transfer, penerbitan kwitansi pembayaran sah, dan pencatatan buku kas harian.
4. **Divisi Billing**: Penerbitan faktur tagihan (*invoice*) berjangka (*kredit/tempo*) untuk instansi pemerintah (Dinas/Pemda) dan korporasi.
5. **Divisi Keuangan & Accounting**: Rekapitulasi omset harian/bulanan, validasi piutang iklan dan sirkulasi, serta pembukuan umum.
6. **Administrator**: Pengendali tata kelola sistem, manajemen hak akses RBAC, audit log keamanan, pemantauan backup, dan parameter cutoff operasional.

---

## 2. PRINSIP UTAMA KEAMANAN SIKAPRI: TRIAD CIA (ISO/IEC 27001)

Sesuai instruksi mutlak pembimbing sistem:
> *"Keamanan sistem informasi SiKAPRI bukan sekadar formalitas pengujian (pentest), karena celah pasti banyak jika tidak dirancang dengan benar sejak level arsitektur. Pemrograman berbasis data (PBO/aplikasi web) tidak boleh hanya sekadar 'program berjalan', tetapi harus aman (secure by design). Semua aspek keamanan ini wajib dimasukkan ke dalam laporan sistem dan modul magang/tugas akhir."*

```
                  ┌────────────────────────────────────────┐
                  │               CIA TRIAD                │
                  │        KEAMANAN SISTEM SIKAPRI         │
                  └───────────────────┬────────────────────┘
                                      │
         ┌────────────────────────────┼────────────────────────────┐
         │                            │                            │
         ▼                            ▼                            ▼
┌──────────────────┐        ┌──────────────────┐        ┌──────────────────┐
│ CONFIDENTIALITY  │        │    INTEGRITY     │        │   AVAILABILITY   │
│  (Kerahasiaan)   │        │   (Integritas)   │        │  (Ketersediaan)  │
├──────────────────┤        ├──────────────────┤        ├──────────────────┤
│• Privasi Transaksi│       │• Audit Trail Log │        │• Disaster Backups│
│• Enkripsi Sandi  │        │• Anti-tamper data│        │• High Uptime DB  │
│• RBAC per divisi │        │• Validasi Ketat  │        │• Cutoff Handling │
│• No public index │        │• Disable, no del │        │• Restore Testing │
└──────────────────┘        └──────────────────┘        └──────────────────┘
```

### 2.1 Confidentiality (Kerahasiaan / Keep Private)
- Menjaga privasi transaksi periklanan, data kontrak instansi pemerintah, tarif diskon khusus, dan laporan omset agar tidak bocor ke pihak kompetitor maupun publik.
- Hak akses diisolasi melalui middleware *Role-Based Access Control* (RBAC) di level database dan controller. Staf divisi satu tidak dapat mengakses modul divisi lainnya.
- Sistem bersifat internal tertutup dan dilarang terindeks mesin pencari (*anti-crawling*).

### 2.2 Integrity (Integritas / Keep Accurate)
- Menjamin data transaksi, pesanan iklan, dan distribusi koran akurat dan bebas dari manipulasi atau penghapusan yang disengaja maupun tidak disengaja.
- **Kebijakan Anti-Hard Delete**: Seluruh entitas penting (User, Pemasang, Agen, Order) menggunakan *Soft Deletes* atau status pembekuan (*Disable*). Dilarang keras melakukan `DELETE` permanen yang merusak riwayat finansial.
- Pencatatan seluruh mutasi data ke dalam tabel `activity_logs` (*immutable audit trail*).

### 2.3 Availability (Ketersediaan / Keep Available)
- Menjamin sistem selalu siap digunakan terutama menjelang jam kritis *deadline cetak* pukul 17:00 WIB (iklan) dan pengiriman koran pukul 01:00 - 06:00 WIB (sirkulasi).
- Menjalankan skema pencadangan (*scheduled automated backup*) berlapis ke penyimpanan terpisah (*offsite storage*).
- Menjalankan prosedur pengujian pemulihan (*restore test*) berkala untuk mengantisipasi kegagalan server.

---

## 3. LIMA AREA UTAMA KEAMANAN SIKAPRI (THE FIVE MAIN SECURITY PILLARS)

Kelima pilar ini wajib terintegrasi secara fungsional dalam kode program Laravel:

```
┌──────────────────────────────────────────────────────────────────────────────────┐
│                   5 PILAR UTAMA KEAMANAN APLIKASI SIKAPRI                         │
├─────────────────┬─────────────────┬──────────────────┬─────────────────┬─────────┤
│    AREA 1       │     AREA 2      │      AREA 3      │     AREA 4      │ AREA 5  │
│  PENCADANGAN    │    PROTEKSI     │     KONTROL      │   MANAJEMEN     │ ANTI    │
│     DATA        │     LOGIN       │  ADMINISTRATOR   │   PASSWORD &    │ PAPARAN │
│   (BACKUPS)     │ (RATE LIMITING) │  & RBAC DIVISI   │  KRIPTOGRAFI    │  WEB    │
└─────────────────┴─────────────────┴──────────────────┴─────────────────┴─────────┘
```

---

### 3.1 AREA 1: PENCADANGAN DATA & DISASTER RECOVERY (BACKUPS)

Data transaksi iklan dan keagenan koran adalah rekaman finansial resmi berstatus hukum. Kerusakan database tanpa cadangan terpisah berakibat fatal bagi perusahaan.

#### A. Skema Jadwal Cadangan Rutin (Hierarchy of Retention)
Pencadangan dijalankan secara otomatis menggunakan task scheduler (*Laravel Console Scheduler*):

| Frekuensi | Waktu Eksekusi | Target Data | Retensi Penyimpanan | Format & Proteksi |
|---|---|---|---|---|
| **Harian (Daily)** | Pkl 00:00 WIB (Tengah Malam) | Full Dump Database (`.sql`) | 14 Hari Terakhir | Gzip Terenkripsi AES-256 |
| **Mingguan (Weekly)** | Setiap Minggu Pkl 01:00 WIB | Database + File Bukti Order/PDF | 8 Minggu Terakhir | Tar.gz Terenkripsi |
| **Bulanan (Monthly)** | Akhir Bulan Pkl 23:59 WIB | Snapshot Finansial & Master Data | 24 Bulan | Zip Arsip Terproteksi |
| **Triwulanan (Per 3 Bulan)**| Akhir Triwulan (Mar, Jun, Sep, Des) | Rekapitulasi Pembukuan & Transaksi | 3 Tahun | Cold Storage Archive |
| **Semesteran (Per 6 Bulan)**| Akhir Juni & Akhir Desember | Evaluasi Piutang & Audit Agen | 5 Tahun | Cold Storage Archive |
| **Tahunan (Yearly)** | 31 Desember Tutup Buku | Master Arsip Tahunan SiKAPRI | Permanen (Arsip Perusahaan) | WORM Media / ISO Ready |

#### B. Penyimpanan Terpisah (Separate Copy / Offsite Storage)
- **Larangan Keras**: Salinan cadangan dilarang disimpan hanya pada disk/server yang sama dengan web aktif.
- **Mekanisme**: File dump database diekspor, dikompresi, dienkripsi dengan OpenSSL AES-256, dan dikirimkan otomatis ke secondary storage (NAS Server Lokal Kantor yang terisolasi atau bucket penyimpanan terenkripsi khusus).

#### C. SOP Pengujian Pemulihan (Disaster Recovery Restore Testing)
- Setiap akhir bulan, Administrator wajib menjalankan simulasi restore pada database *sandbox/staging* lokal untuk memastikan file backup valid, tidak korup, dan integritas relasi foreign keys terjaga 100%.

---

### 3.2 AREA 2: PROTEKSI LOGIN & PERCOBAAN GAGAL (LOGIN PROTECTION)

Pintu utama sistem dilindungi dari serangan *Brute Force*, *Dictionary Attack*, dan *Credential Stuffing*.

#### A. Rate Limiter Terisolasi (`email|ip_address`)
- Pembatasan upaya login diikat pada pasangan alamat email pengguna dan alamat IP pengirim (`$this->throttleKey() = Str::lower($email) . '|' . $ip`). Hal ini mencegah penguncian akun secara jahat (*denial-of-service*) dari IP asing yang berbeda.

#### B. Aturan Ketat 3 Kali Gagal (Three Failed Login Attempts Rule)

```
[ Percobaan Login Masuk ]
           │
           ├── Percobaan 1: Password Salah ────────> Catat Failure di Log (Sisa kuota: 2)
           │
           ├── Percobaan 2: Password Salah ────────> Catat Failure di Log (Sisa kuota: 1, Alert Peringatan)
           │
           └── Percobaan 3: Password Salah ────────> AMBANG BATAS TERCAPAI (THRESHOLD)
                                                            │
                                                            ▼
                                          ┌───────────────────────────────────┐
                                          │  AKUN DIKUNCI SEMENTARA 15 MENIT  │
                                          │  (TEMPORARY LOCKOUT 900 DETIK)    │
                                          │  • Blokir Autentikasi             │
                                          │  • Kirim Alert ke Security Log    │
                                          │  • Tampilkan Pesan Jeda Resmi     │
                                          └───────────────────────────────────┘
```

1. **Percobaan 1 (Gagal)**: Sistem mencatat log kegagalan. Tampilan UI: *"Email atau kata sandi tidak cocok. Silakan periksa kembali."*
2. **Percobaan 2 (Gagal)**: Sistem mencatat kegagalan kedua. Tampilan UI: *"Kata sandi salah. Peringatan: Akun akan terkunci sementara jika gagal 1 kali lagi."*
3. **Percobaan 3 (Threshold)**: Sistem mengunci autentikasi selama **15 Menit (900 detik)**. Permintaan login berikutnya langsung di-reject dengan respon HTTP 429 dan pesan resmi: *"Terlalu banyak percobaan login yang gagal. Demi keamanan sistem, akses ke akun ini ditangguhkan sementara selama 15 menit. Silakan coba kembali nanti atau hubungi Administrator."*

#### C. Deteksi Aktivitas Mencurigakan (Security Activity Logs)
Seluruh peristiwa autentikasi dicatat dalam tabel `activity_logs`: waktu mikrodetik, user_id, email, IP address, user-agent, dan aksi (`LOGIN_SUCCESS`, `LOGIN_FAILED`, `ACCOUNT_LOCKED_15M`, `ACCOUNT_DISABLED`).

---

### 3.3 AREA 3: KONTROL ADMINISTRATOR & HAK AKSES (ADMIN CONTROL & RBAC)

#### A. Konsol Pengawasan Pengguna Terpusat (`/admin/users`)
Administrator memiliki hak istimewa untuk meninjau seluruh akun pegawai, divisi, IP login terakhir, dan waktu aktivitas terakhir secara transparan.

#### B. Siklus Hidup Akun Tiga Tingkat (Account Lifecycle Management)

| Status Akun | Hak Akses Sistem | Prosedur Operasional |
|---|---|---|
| **Active** | Akses penuh sesuai wewenang divisi | Staf aktif yang bertugas normal. |
| **Review** | Akses terbatas / mode audit | Akun ditandai karena aktivitas anomali (misal: login tengah malam, anomali input order). Memerlukan verifikasi admin. |
| **Disable** | Akses DITOLAK TOTAL (Sesi Ditutup) | Akun staf yang cuti, mutasi, keluar, atau terindikasi disusupi. Seluruh sesi aktif langsung diputus (*session terminated*). |

#### C. Kebijakan Anti-Hard Delete (Strict Deletion Policy)
- **Larangan Mutlak**: Akun staf dilarang dihapus secara permanen (`DELETE FROM users`) apabila telah memiliki riwayat transaksi (nota pesanan iklan, kwitansi kasir, surat jalan sirkulasi).
- **Prosedur Baku**: Administrator wajib menggunakan opsi **Disable (Nonaktifkan)**. Akun tidak dapat login, namun rekam jejak tanda tangan digital pada transaksi masa lalu tetap utuh dan sah secara hukum audit.

#### D. Jatah Pembagian Akun Divisi (Role Quotas)
Sesuai arahan pembimbing, alokasi akun staf utama dikonfirmasi sebagai berikut:
1. **Divisi Sirkulasi**: 1 Akun Staf Operasional Sirkulasi
2. **Divisi Keuangan**: 1 Akun Staf Rekapitulasi Keuangan
3. **Divisi Kasir**: 1 Akun Staf Kasir Penerimaan
4. **Divisi Billing**: 1 Akun Staf Penagihan / Invoice
5. **Divisi Iklan**: 1 Akun Staf Periklanan & AE
6. **Administrator**: Pengelola Master, Backup, dan Keamanan Sistem

---

### 3.4 AREA 4: MANAJEMEN KATA SANDI & KRIPTOGRAFI (PASSWORDS & CRYPTOGRAPHY)

#### A. Algoritma Hashing Modern
- Dilarang keras menyimpan password dalam format *plain text*, `MD5`, `SHA-1`, atau hash sederhana tanpa *salt*.
- Standar Wajib: Menggunakan algoritma **Bcrypt** (work factor 12) atau **Argon2id** melalui abstraksi `Hash::make()` bawaan Laravel.

#### B. Standar Kompleksitas Kata Sandi (Password Complexity Standard)
- Minimal 8 - 16 karakter.
- Wajib menggabungkan huruf besar, huruf kecil, angka, dan karakter khusus/simbol.
- Dilarang menggunakan pola yang mudah ditebak seperti `kabarpriangan123..`, `admin123`.
- Contoh pola acak substitusi fonetik yang dianjurkan pembimbing: `1r12skabar17riangan` atau `Kp#Pri4ng4n!2026`.

#### C. Larangan Fitur Publik "Lupa Password" (No Public Password Reset)
- **Aturan Tegas**: Halaman login publik hanya memuat input email, password, tombol mata, dan tombol submit. **DILARANG** menyediakan link publik *"Lupa Kata Sandi?"*.
- **Alasan**: Fitur reset publik via email merupakan pintu utama eksploitasi peretasan akun internal (*Account Takeover & Email Hijacking*).
- **Prosedur Resmi**: Reset kata sandi hanya boleh dilakukan secara manual tatap muka oleh Administrator melalui panel Admin.

#### D. Multi-Factor Authentication (MFA) & Password Manager
- Akun Administrator didorong mengaktifkan verifikasi 2 langkah (MFA/TOTP via Google Authenticator).
- Form login mendukung atribut kepatuhan W3C (`autocomplete="current-password"`) agar staf dapat menyimpan kredensial rumit pada *Browser Password Manager* resmi.

---

### 3.5 AREA 5: PENCEGAHAN PAPARAN WEB & SEO BLOCKING (WEB EXPOSURE)

SiKAPRI adalah sistem aplikasi internal kantor berita, bukan portal publik. Seluruh halaman sama sekali tidak boleh terindeks oleh robot pencari Google, Bing, atau perayap web lainnya.

#### A. Konfigurasi `public/robots.txt`
```txt
# SIKAPRI - INTERNAL MEDIA ENTERPRISE SYSTEM
# DILARANG MENGINDEKS SELURUH DIREKTORI SISTEM
User-agent: *
Disallow: /
```

#### B. Injeksi Meta Tag Anti-Index di Semua Layout Blade
```html
<!-- Proteksi Ketat Anti-Index Mesin Pencari -->
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
<meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
```

#### C. HTTP Header Response: `X-Robots-Tag`
Sistem menyertakan middleware yang menambahkan header HTTP:
```http
X-Robots-Tag: noindex, nofollow, noarchive, nosnippet
```
Dokumen PDF cetak (Kwitansi, Bukti Order, Surat Jalan) yang dibuka di browser otomatis terproteksi dari *indexing* mesin pencari.

#### D. Kebijakan Subdomain & Larangan Google Search Console
- Dilarang mendaftarkan URL aplikasi internal ke Google Search Console.
- Dilarang memasang skrip pelacak publik pihak ketiga (Google Analytics, Meta Pixel).

---

## 4. ARSITEKTUR INFRASTRUKTUR, JARINGAN & TOPOLOGI

```
                       ARSITEKTUR TOPOLOGI REKOMENDASI PEMBIMBING
                     
    [ Komputer Kantor KP ]     [ Staf Lapangan / Wartawan ]
           │                                 │
           └────────────────┬────────────────┘
                            │ (HTTPS Port 443 Terenkripsi SSL)
                            ▼
              ┌───────────────────────────┐
              │   CLOUD VPS WEB SERVER    │
              │  (Frontend SiKAPRI App)   │
              │ • Nginx Reverse Proxy     │
              │ • PHP-FPM 8.2+ Enterprise │
              │ • Blade UI & Tailwind     │
              └─────────────┬─────────────┘
                            │
                            │ Terowongan Aman (VPN WireGuard / Private Link / Whitelist)
                            ▼
              ┌───────────────────────────┐
              │   DATABASE SERVER LOKAL   │
              │   (Kantor Kabar Priangan) │
              │ • MySQL Enterprise Engine │
              │ • Port 3306 Tertutup WAN  │
              │ • Storage RAID Mirroring  │
              │ • Live Daily Backup Local │
              └───────────────────────────┘
```

1. **Frontend / Aplikasi di Cloud VPS**:
   - Memastikan staf iklan, koresponden luar kota, dan manajer sirkulasi dapat mengakses sistem dengan cepat dan fleksibel dari perangkat apapun.
2. **Database di Jaringan Server Kantor Lokal**:
   - Menjamin seluruh data keuangan, database pelanggan koran, dan omset tetap tersimpan fisik di kantor Harian Umum Kabar Priangan. Port database 3306 ditutup total dari internet publik dan hanya menerima koneksi terowongan VPN/WireGuard dari Cloud VPS.
3. **Mitigasi Jika Terpaksa Menggunakan Hosting Bersama (Shared/Single VPS)**:
   - Database diikat ketat pada `127.0.0.1` (*localhost*).
   - Hak akses file `.env` dikunci `chmod 600`.
   - File dump cadangan database dienkripsi AES-256 dan disinkronisasikan ke penyimpanan sekunder terpisah.

---

## 5. MODUL OPERASIONAL DIVISI IKLAN (ADVERTISING DIVISION)

*Hasil Analisis Mendalam & Reverse Engineering Sistem IKL.EXE Legacy ke Web Modern SI-KP*

### 5.1 Struktur Master Data Divisi Iklan

#### A. Master Pemasang / Klien Iklan (`pemasang`)
- `kode_pemasang`: Auto-generate format `PSG-YYYY-NNNN`.
- `nama_pemasang`, `alamat`, `kota`, `no_telepon`, `email`, `npwp`, `nama_pic`, `jabatan_pic`.
- `jenis_akun`: `TUNAI` / `KREDIT` (dengan field `credit_limit`).
- `saldo_piutang`: Terkalkulasi otomatis dari transaksi belum lunas.
- `status_aktif`: Soft delete (Dilarang hapus permanen data klien).
- *Fitur On-the-Fly*: Staf dapat menambah pemasang baru langsung dari modal saat input transaksi.

#### B. Master Produk & Tarif Iklan (`jenis_iklan`)
Mengadopsi **Smart Unified Product Catalog (SKU)** standar Kabar Priangan:

| Kode SKU | Nama Produk Iklan | Tipe Dasar | Warna | Ukuran Baku | Rumus Perhitungan | Tarif Standar |
|---|---|---|---|---|---|---|
| `BARIS` | Iklan Baris Koran | BARIS | BW | Fleksibel (Min 2 baris) | `baris × tarif` | Rp 22.750 / baris |
| `KLM_BW_1X50` | Kolom BW (1×50 mm) | KOLOM | BW | 1 kol × 50 mm (50 mmk) | Paket Flat | Rp 450.000 / muat |
| `KLM_BW_2X100` | Kolom BW (2×100 mm) | KOLOM | BW | 2 kol × 100 mm (200 mmk)| Paket Flat | Rp 1.600.000 / muat |
| `DISP_BW_CUST` | Display BW (Custom mmk)| DISPLAY | BW | Custom (Max 7 kol × 540 mm) | `kolom × mm × tarif` | Rp 12.000 / mmk |
| `DISP_FC_CUST` | Display Full Color | DISPLAY | FC | Custom (Max 7 kol × 540 mm) | `kolom × mm × tarif` | Rp 24.000 / mmk |
| `DISP_BONUS` | Display Bonus / Barter | BONUS | BW/FC | Sesuai Memo Manajer | Flat Zero | Rp 0 (Non-komersial)|
| `ADV_HAL_FC` | Advertorial 1 Halaman FC| ADVERTORIAL| FC | 7 kol × 540 mm (3.780 mmk)| Paket Flat | Rp 25.000.000 / muat |
| `TV_SPOT_30` | Spot Iklan Priangan TV | PRIANGAN_TV| AV | Durasi 30 Detik | Per Spot Tayang | Rp 150.000 / tayang |
| `DIGITAL_BAN` | Banner Portal Online | DIGITAL | FC | 728 × 90 px | Per Bulan / CPM | Rp 1.500.000 / bln |

#### C. Master Sales & Account Executive (`sales_ae`)
- `kode_ae`: Format legacy berstatus resmi:
  - **S** = Struktural (Karyawan tetap Divisi Iklan), misal: `S67 SUHITA DEWI`.
  - **K** = Koresponden (Wartawan/biro daerah membawa iklan), misal: `K12 BUDI S`.
  - **FO** = Front Office (Walk-in counter langsung di kantor redaksi).
- `tarif_insentif_default` (%) dan `tarif_komisi_max` (%).

#### D. Master Rubrik Baris (`kop_iklan`)
Rubrikasi khusus penempatan iklan baris pada koran: *Otomotif/Mobil, Lowongan Kerja, Properti/Tanah Dijual, Kehilangan/STNK, Jasa, Aneka Usaha*.

#### E. Master Halaman & Edisi Wilayah (`kop_kota`)
Penempatan halaman khusus wilayah Priangan Timur: *Halaman 1 (Utama), Halaman 5 (Edisi Sumedang), Halaman 7 (Edisi Tasikmalaya), Halaman 8 (Edisi Ciamis/Banjar), Halaman Garut*. Dilengkapi field `surcharge_persen` untuk penempatan premium.

### 5.2 Alur Transaksi & Kalkulator Finansial Iklan
1. **Input Multi-Pemuatan**: Satu nomor order transaksi dapat dijadwalkan terbit pada beberapa tanggal sekaligus (*multi-date schedule*).
2. **Kalkulator Finansial Otomatis**:
   - $\text{Total mmk} = \text{Jumlah Kolom} \times \text{Tinggi Milimeter}$
   - $\text{Harga Bruto} = \text{Total mmk} \times \text{Tarif Dasar} \times \text{Jumlah Pemuatan}$
   - $\text{Potongan Diskon (Rp)} = \text{Harga Bruto} \times (\text{Persen Diskon} / 100)$
   - $\text{DPP (Dasar Pengenaan Pajak)} = \text{Harga Bruto} - \text{Potongan Diskon}$
   - $\text{PPN 11\%} = \text{DPP} \times 0.11$ (disiapkan parameter konfigurasi fleksibel)
   - $\text{Total Akhir} = \text{DPP} + \text{PPN}$
3. **Batas Waktu Operasional (Cutoff Deadline Pkl 17:00 WIB)**:
   - Seluruh transaksi iklan yang akan terbit esok hari wajib disetujui (*locked*) sebelum pukul 17:00 WIB.
   - Pukul 17:00 WIB, sistem menghasilkan laporan **Rekap Sales / Tanggal Muat** yang diserahkan ke bagian Pracetak (*Layout Artist*) sebagai **Lembar Dummy Tata Letak Koran**.

---

## 6. MODUL OPERASIONAL DIVISI SIRKULASI (CIRCULATION DIVISION)

*Tulang Punggung Distribusi Fisik Surat Kabar Harian Kabar Priangan*

### 6.1 Terminologi Standar Industri Pers Sirkulasi
- **Oplah / Tiras**: Total jumlah eksemplar koran yang dicetak percetakan pada satu edisi tanggal terbit.
- **Eksemplar**: Satuan unit 1 buah surat kabar.
- **Agen Utama**: Mitra distribusi tingkat 1 (wilayah kota/kabupaten).
- **Sub-Agen & Loper**: Pengecer tingkat akhir (kios pinggir jalan dan pengantar koran ke rumah).
- **Pelanggan Langganan**: Pembaca instansi, kantor, atau rumah tangga yang membayar di muka bulanan.
- **Surat Jalan (SJ)**: Dokumen sah muatan distribusi malam: nomor edisi, daftar agen tujuan, jumlah eksemplar, driver pengirim.
- **Retur**: Koran tidak terjual kemarin yang dikembalikan agen pada siang hari berikutnya.
- **Nett Sales (Penjualan Bersih)**: $\text{Nett Sales} = \text{Eksemplar Kirim} - \text{Eksemplar Retur}$.
- **Piutang Keagenan**: Nilai tagihan koran terjual: $\text{Tagihan} = \text{Nett Sales} \times \text{Harga Agen}$.

### 6.2 Alur Kerja Harian Distribusi 24 Jam (4 Shift Kerja)

```
[ 22:00 - 02:00 ] SHIFT MALAM: Pra-Distribusi
├── Konfirmasi Tiras Cetak ke Pracetak
├── Generate & Cetak Surat Jalan (SJ) per Agen
└── Sorting bundel koran (isi 25/50 eks) di gudang
        │
        ▼
[ 02:00 - 07:00 ] SHIFT DINI HARI: Distribusi Aktif
├── Armada berangkat (Luar kota Tasikmalaya jalan lebih awal)
├── Drop point agen utama (Tasik, Ciamis, Banjar, Garut, Sumedang)
└── 06:00 WIB: Koran sudah di tangan pembaca & kios
        │
        ▼
[ 08:00 - 12:00 ] SHIFT PAGI: Administrasi & Penagihan
├── Input konfirmasi penerimaan agen ke sistem
├── Rekapitulasi surat jalan terkirim
└── Kolektor menagih agen yang jatuh tempo
        │
        ▼
[ 12:00 - 17:00 ] SHIFT SIANG: Rekonsiliasi Retur & Rekapitulasi
├── Agen menyetor fisik koran retur (edisi kemarin)
├── Petugas gudang menghitung fisik & input retur ke sistem
├── Sistem auto-kalkulasi: Nett Sales = Kirim - Retur
└── Sistem memperbarui Rekening Koran Agen & Piutang Berjalan
```

### 6.3 Modul Fungsional Sirkulasi
1. **Master Agen (`agen_sirkulasi`)**: Database agen utama, sub-agen, loper, drop point, plafon kredit limit, dan alokasi tiras standar.
2. **Transaksi Distribusi & Surat Jalan (`surat_jalan_sirkulasi`)**: Pencatatan nomor SJ, tanggal edisi, driver, armada, kuota eksemplar per agen.
3. **Pencatatan Retur Koran (`retur_sirkulasi`)**: Validasi jumlah fisik koran retur, persentase retur terhadap alokasi (indikator efisiensi distribusi).
4. **Kartu Rekening Koran Agen (`piutang_agen`)**: Buku besar mutasi per agen (debet: kirim koran baru, kredit: potongan retur & setoran uang kasir).

---

## 7. MODUL FINANSIAL LINTAS DIVISI (KEUANGAN, KASIR & BILLING)

1. **Modul Kasir (`transaksi_kasir`)**:
   - Menerima pelunasan tunai atau bukti transfer bank dari klien iklan maupun agen sirkulasi.
   - Menerbitkan **Kwitansi Pembayaran Resmi** bernomor seri unik dengan logo PT Berkah Pikiran Rakyat.
   - Menyusun Buku Kas Harian yang diserahkan ke bagian Keuangan saat tutup kas.
2. **Modul Billing / Faktur Tagihan (`billing_invoice`)**:
   - Menerbitkan faktur tagihan berjangka (*invoice*) untuk pengiklan dinas pemerintah atau korporasi yang menggunakan sistem tempo/kredit.
   - Pengawasan umur piutang (*Aging Receivables: 0-30 hari, 31-60 hari, >60 hari*).
3. **Modul Keuangan & Accounting**:
   - Rekapitulasi omset gabungan (Iklan Cetak + Iklan TV + Digital + Sirkulasi Koran).
   - Validasi potongan diskon, insentif AE, dan komisi agen sebelum pencairan dana.

---

## 8. STANDAR DESAIN TAMPILAN UI/UX & BUKTI KWITANSI

*Pedoman Baku Visual & Kenyamanan Operasional Staf Redaksi & Keuangan*

### 8.1 Aturan Baku Teks Hitam Pekat (High-Contrast Pure Black #000000)
- **Instruksi Mutlak**: Seluruh label formulir, deskripsi halaman, catatan operasional, dan nilai nominal transaksi wajib menggunakan warna hitam asli (`#000000` / class `text-black font-semibold/bold`).
- **Larangan**: Dilarang menggunakan warna teks abu-abu pudar (`text-gray-400`, `text-slate-500`, opacity rendah) pada teks petunjuk penting (seperti deskripsi *"Formulir pemesanan iklan cetak harian Kabar Priangan."*).
- **Aksen Warna Fungsional**:
  - 🔵 **Biru Kabar Priangan (`bg-kp-blue-600` / `#1D4ED8`)**: Tombol utama (*submit/simpan*), header transaksi, navigasi aktif.
  - 🟢 **Hijau Finansial (`text-emerald-700` / `bg-emerald-500`)**: Status *LUNAS*, penerimaan kas masuk.
  - 🔴 **Merah Peringatan (`text-red-600` / `bg-red-500`)**: Potongan diskon, status *BELUM LUNAS*, tombol bahaya (*Disable Akun*).
  - 🟡 **Kuning Amber (`text-amber-600`)**: Status transaksi *BOOKING / MENUNGGU REVIEW*.

### 8.2 Keselarasan Format Kwitansi & Bukti Order di Seluruh Divisi
Seluruh dokumen bukti transaksi (Iklan Koran, Priangan TV, Sirkulasi, dan Kasir) wajib seragam:

```
┌────────────────────────────────────────────────────────────────────────┐
│                      KOP RESMI HARIAN UMUM KABAR PRIANGAN              │
│                          PT BERKAH PIKIRAN RAKYAT                      │
│            Jl. Siluman No. 24, Tasikmalaya - Telp: (0265) 331xxx        │
├────────────────────────────────────────────────────────────────────────┤
│ BUKTI ORDER / KWITANSI PEMBAYARAN                    No: ORD-202610-001│
│ Tanggal Cetak / Terbit: 19 Oktober 2026              Status: LUNAS     │
├────────────────────────────────────────────────────────────────────────┤
│ Data Pemasang / Klien:                                                 │
│ • Nama: Dinas Komunikasi dan Informatika Kota Tasikmalaya              │
│ • NPWP / Kontak: 01.xxx.xxx.x-xxx.000 / 0812xxxxxxxx                   │
├────────────────────────────────────────────────────────────────────────┤
│ Rincian Pesanan:                                                       │
│ 1. Iklan Ucapan Selamat (Koran Cetak)                                  │
│    - Ukuran: 4 Kolom x 150 mmk = 600 mmk                              │
│    - Halaman: Halaman Depan (Utama)                                    │
│    - Tarif Per mmk: Rp 25.000                                          │
│    - Subtotal                                            Rp 15.000.000 │
│    - Potongan / Diskon Keagenan (10%)                   -Rp  1.500.000 │
│    - DPP                                                 Rp 13.500.000 │
│    - PPN 11%                                            +Rp  1.485.000 │
│    ─────────────────────────────────────────────────────────────────── │
│    TOTAL AKHIR PEMBAYARAN                                Rp 14.985.000 │
├────────────────────────────────────────────────────────────────────────┤
│ Tanda Tangan Pengesahan:                                               │
│                                                                        │
│    [ Pemasang / Rekanan ]     [ Petugas Iklan ]     [ Kasir Keuangan ] │
│         (              )         (              )       (            ) │
└────────────────────────────────────────────────────────────────────────┘
```

### 8.3 Interaktivitas Form & Dialog Bahasa Indonesia Bersahabat
1. **Field Password dengan Tombol Mata (*Eye Toggle*)**: Menggunakan SVG icon responsif berbasis Alpine.js (`:type="showPass ? 'text' : 'password'"`).
2. **Peringatan Dialog Ramah (SweetAlert2 / AppAlert)**: Dilarang menggunakan pesan galat bahasa Inggris default. Seluruh alert wajib menggunakan bahasa Indonesia yang jelas, sopan, dan solutif.
3. **Pencegahan Elemen Bertumpuk (*Anti-Stacking*)**: Menggunakan sistem CSS Grid fleksibel (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4`) pada kartu ringkasan langkah 1, 2, 3, dan 4 agar tidak saling tumpang tindih pada resolusi monitor apapun.

---

## 9. RANCANGAN STRUKTUR BASIS DATA TERPADU (MASTER DATABASE SCHEMA)

### 9.1 Tabel Keamanan & Manajemen Akun (`users`, `activity_logs`, `system_backups`)

```sql
-- Penambahan Kolom Status & Proteksi Login pada Tabel USERS
ALTER TABLE `users`
  ADD COLUMN `status` ENUM('active', 'review', 'disable') DEFAULT 'active' AFTER `role`,
  ADD COLUMN `failed_login_attempts` TINYINT UNSIGNED DEFAULT 0 AFTER `status`,
  ADD COLUMN `locked_until` TIMESTAMP NULL AFTER `failed_login_attempts`,
  ADD COLUMN `last_login_at` TIMESTAMP NULL AFTER `locked_until`,
  ADD COLUMN `last_login_ip` VARCHAR(45) NULL AFTER `last_login_at`;

-- Tabel Security Activity Logs
CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `email_attempted` VARCHAR(255) NULL,
  `action` VARCHAR(50) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` TEXT NULL,
  `description` TEXT NULL,
  `payload` JSON NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  INDEX (`action`, `created_at`),
  INDEX (`ip_address`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- Tabel Riwayat Backup Sistem
CREATE TABLE `system_backups` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `filepath` VARCHAR(500) NOT NULL,
  `filesize_bytes` BIGINT NOT NULL,
  `frequency` ENUM('daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'yearly', 'manual') NOT NULL,
  `checksum_sha256` VARCHAR(64) NOT NULL,
  `is_encrypted` TINYINT(1) DEFAULT 1,
  `status` ENUM('success', 'failed', 'verified') DEFAULT 'success',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);
```

### 9.2 Tabel Operasional Divisi Iklan

```sql
-- Master Klien Pemasang
CREATE TABLE `pemasang` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `kode_pemasang` VARCHAR(50) UNIQUE NOT NULL,
  `nama_pemasang` VARCHAR(200) NOT NULL,
  `alamat` TEXT NULL,
  `kota` VARCHAR(100) NULL,
  `no_telepon` VARCHAR(50) NULL,
  `jenis_akun` ENUM('tunai', 'kredit') DEFAULT 'tunai',
  `credit_limit` DECIMAL(15,2) DEFAULT 0,
  `saldo_piutang` DECIMAL(15,2) DEFAULT 0,
  `status_aktif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);

-- Master Produk & Tarif Iklan
CREATE TABLE `jenis_iklan` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `kode` VARCHAR(50) UNIQUE NOT NULL,
  `nama` VARCHAR(150) NOT NULL,
  `tipe_dasar` ENUM('baris', 'kolom', 'display', 'advertorial', 'priangan_tv', 'digital', 'bonus') NOT NULL,
  `mode_warna` ENUM('bw', 'fc', 'spot', 'av') NOT NULL,
  `cara_hitung` ENUM('per_baris', 'per_mmk', 'paket_fixed', 'fixed_zero') NOT NULL,
  `harga_dasar` DECIMAL(15,2) NOT NULL,
  `status_aktif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);

-- Transaksi Pesanan Iklan
CREATE TABLE `order_iklan` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nomor_order` VARCHAR(50) UNIQUE NOT NULL,
  `pemasang_id` BIGINT UNSIGNED NOT NULL,
  `sales_ae_id` BIGINT UNSIGNED NULL,
  `jenis_iklan_id` BIGINT UNSIGNED NOT NULL,
  `kop_kota_id` BIGINT UNSIGNED NULL,
  `jumlah_kolom` INT NULL,
  `tinggi_mm` INT NULL,
  `total_mmk` INT NULL,
  `harga_bruto` DECIMAL(15,2) NOT NULL,
  `potongan_persen` DECIMAL(5,2) DEFAULT 0,
  `potongan_rp` DECIMAL(15,2) DEFAULT 0,
  `dpp` DECIMAL(15,2) NOT NULL,
  `ppn_rp` DECIMAL(15,2) DEFAULT 0,
  `total_akhir` DECIMAL(15,2) NOT NULL,
  `status_pembayaran` ENUM('lunas', 'belum_lunas', 'dp', 'bonus') DEFAULT 'belum_lunas',
  `status_produksi` ENUM('draft', 'siap_muat', 'sudah_muat', 'batal') DEFAULT 'draft',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`pemasang_id`) REFERENCES `pemasang`(`id`),
  FOREIGN KEY (`jenis_iklan_id`) REFERENCES `jenis_iklan`(`id`)
);
```

### 9.3 Tabel Operasional Divisi Sirkulasi

```sql
-- Master Agen Distribusi Sirkulasi
CREATE TABLE `agen_sirkulasi` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `kode_agen` VARCHAR(50) UNIQUE NOT NULL,
  `nama_agen` VARCHAR(200) NOT NULL,
  `wilayah_kota` VARCHAR(100) NOT NULL,
  `alamat_drop_point` TEXT NOT NULL,
  `no_telepon` VARCHAR(50) NOT NULL,
  `alokasi_harian_default` INT UNSIGNED NOT NULL DEFAULT 0,
  `harga_per_eksemplar` DECIMAL(10,2) NOT NULL,
  `credit_limit` DECIMAL(15,2) DEFAULT 0,
  `saldo_piutang` DECIMAL(15,2) DEFAULT 0,
  `status_aktif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);

-- Transaksi Distribusi & Surat Jalan
CREATE TABLE `surat_jalan_sirkulasi` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nomor_surat_jalan` VARCHAR(50) UNIQUE NOT NULL,
  `tanggal_edisi` DATE NOT NULL,
  `nama_driver` VARCHAR(100) NOT NULL,
  `plat_kendaraan` VARCHAR(20) NOT NULL,
  `total_eksemplar_kirim` INT UNSIGNED NOT NULL,
  `status_pengiriman` ENUM('loading', 'dalam_perjalanan', 'diterima_agen', 'selesai') DEFAULT 'loading',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);

-- Transaksi Rekonsiliasi Retur Koran
CREATE TABLE `retur_sirkulasi` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `agen_id` BIGINT UNSIGNED NOT NULL,
  `tanggal_edisi_koran` DATE NOT NULL,
  `tanggal_setor_retur` DATE NOT NULL,
  `jumlah_retur_eksemplar` INT UNSIGNED NOT NULL,
  `nilai_potongan_retur` DECIMAL(15,2) NOT NULL,
  `status_validasi` ENUM('menunggu_hitung', 'divalidasi_gudang', 'masuk_piutang') DEFAULT 'menunggu_hitung',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`agen_id`) REFERENCES `agen_sirkulasi`(`id`)
);
```

---

## 10. ROADMAP EKSEKUSI & RENCANA KERJA TAHAP DEMI TAHAP

| Fase | Target Utama | Rincian Tindakan Teknis | Status |
|---|---|---|---|
| **FASE 1** | **Konsolidasi PRD & Penataan Folder Dokumentasi** | Satukan seluruh berkas `.md` terpisah ke dalam Master PRD Full Power di `docs/prd.md`, bersihkan root directory dari file tercecer, perbarui indeks `README.md`. | ✅ **SELESAI** |
| **FASE 2** | **SEO Blocking & Isolasi Sistem Tertutup** | Konfigurasi `public/robots.txt` (`Disallow: /`), injeksi `<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">` pada semua file layout Blade, serta middleware header `X-Robots-Tag`. | ⏳ Siap Dikerjakan |
| **FASE 3** | **Proteksi Autentikasi 3x Gagal & Lockout 15 Menit** | Modifikasi `LoginRequest.php` untuk membatasi 3 percobaan gagal dengan jeda 15 menit (900 detik), custom error SweetAlert2 berbahasa Indonesia, pencatatan log kegagalan. | ⏳ Siap Dikerjakan |
| **FASE 4** | **Database Migration & User Lifecycle Management** | Migrasi kolom `status`, `failed_login_attempts`, `locked_until`, `last_login_at`, dan tabel `activity_logs`. Perbarui model `User.php`. | ⏳ Siap Dikerjakan |
| **FASE 5** | **Panel Kontrol Admin (User Review & Disable)** | Perbarui controller `UserController.php` dan tampilan `/admin/users` untuk aksi Active, Review, Disable, serta terapkan *Zero Hard Delete Policy* pada akun bertransaksi. | ⏳ Siap Dikerjakan |
| **FASE 6** | **Otomatisasi Backup Data & Scheduler** | Pembuatan perintah artisan `backup:run-database` dengan kompresi gzip, retensi harian/mingguan/bulanan, dan SOP pengujian pemulihan (*restore test*). | ⏳ Siap Dikerjakan |

---

*Dokumen PRD Full Power Edition ini adalah cetak biru tunggal (*single source of truth*) pengembangan sistem informasi SiKAPRI Harian Umum Kabar Priangan. Seluruh instruksi pembimbing, spesifikasi divisi iklan, sirkulasi, keuangan, serta standar UI/UX telah terintegrasi 100%.*
