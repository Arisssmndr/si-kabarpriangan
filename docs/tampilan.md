# STANDAR DESAIN TAMPILAN & SISTEM DESAIN UI/UX
# SIKAPRI — SISTEM INFORMASI KABAR PRIANGAN
**Pedoman Desain Antarmuka, Standar Tipografi, Formulir Transaksi & Bukti Kwitansi Industri Pers**

---

## 1. PENDAHULUAN & PRINSIP DESAIN

Dokumen ini merupakan pedoman baku (*design system guideline*) antarmuka aplikasi **SiKAPRI (Sistem Informasi Kabar Priangan)**. Seluruh perancang, pengembang (*developer*), dan staf operasional wajib mematuhi panduan ini untuk memastikan konsistensi visual, keterbacaan tinggi (*high legibility*), dan profesionalisme sesuai standar industri surat kabar dan pers nasional.

### 4 Prinsip Inti UI/UX SiKAPRI:
1. **High Contrast & Readability First**: Teks operasional, nominal rupiah, dan label formulir wajib terbaca jelas tanpa menimbulkan kelelahan mata staf saat bekerja maraton menjelang *deadline cutoff* cetak koran.
2. **Keseragaman Format Transaksi & Kwitansi**: Komponen nota pesanan iklan, bukti bayar kasir, dan surat jalan sirkulasi memiliki tata letak (*layout*) yang seirama dan konsisten di seluruh modul.
3. **Pemberitahuan Interaktif yang Bersahabat**: Menggunakan bahasa Indonesia baku yang sopan, lugas, dan instruktif, menolak pesan galat teknis berbahasa Inggris yang membingungkan staf.
4. **Keamanan Visual**: Melindungi data sensitif pada antarmuka (seperti tombol intip kata sandi aman) dan memastikan tampilan siap cetak (*print-ready*) dengan resolusi tajam.

---

## 2. ATURAN BAKU TIPOGRAFI & KONTRAS WARNA (HIGH-CONTRAST PURE BLACK RULE)

### 2.1 Kebijakan Warna Hitam Pekat (Pure Black #000000)
> **ATURAN MUTLAK SISTEM:**
> Seluruh teks informasi, label formulir, deskripsi halaman, catatan operasional, dan nilai nominal transaksi **WAJIB MENGGUNAKAN WARNA HITAM PEKAT (PURE BLACK: `#000000` / CLASS `text-black`)**.

- **Alasan Teknis & Operasional**:
  - Pada lingkungan kerja percetakan dan kantor berita, staf sering melihat layar dengan kecerahan variabel atau di bawah pencahayaan ruangan yang terang.
  - Teks abu-abu pudar atau opasitas rendah (`text-slate-400`, `text-gray-500`, `opacity-70`) sering terlewat oleh staf, memicu kesalahan fatal dalam membaca ukuran milimeter kolom iklan atau nomor kontak pengiklan.

### 2.2 Larangan & Penggunaan yang Benar

| Komponen Tampilan | ❌ DILARANG (SALAH) | ✅ WAJIB DIGUNAKAN (BENAR) | Class Tailwind Standar |
|---|---|---|---|
| **Subjudul Deskripsi Halaman** | `text-slate-500`, `text-gray-400` (terlihat pudar/90%) | Hitam Pekat 100% Kontras Tinggi | `text-black font-medium` |
| **Label Form Input** | `text-slate-600`, `text-gray-500` | Hitam Pekat Jelas & Tegas | `text-black font-bold text-xs` / `text-sm` |
| **Teks Contoh Formulir** | *"Formulir pemesanan iklan cetak harian..."* (abu-abu pudar) | Hitam Asli (Pure Black) | `text-black font-semibold text-xs` |
| **Isi Tabel Data Transaksi** | `text-slate-600` | Hitam Pekat Khas Tinta Cetak Koran | `text-black font-normal` / `font-medium` |
| **Instruksi / Catatan Kaki** | `text-gray-400 italic` | Hitam Pekat Tegas | `text-black text-xs font-medium` |

### 2.3 Pengecualian Warna Fungsional (*Functional Color Accents*)
Warna selain hitam hanya diizinkan untuk kebutuhan indikator fungsional universal:
- 🔵 **Biru Resmi Kabar Priangan (`#1D4ED8` / `text-kp-blue-600` / `bg-kp-blue-600`)**: Tombol aksi utama (*primary button*), header kartu transaksi, tautan navigasi aktif, badge informasi.
- 🟢 **Hijau Finansial (`#15803D` / `text-emerald-700` / `bg-emerald-500`)**: Total penerimaan lunas, saldo kas masuk, status transaksi *LUNAS*, konfirmasi sukses.
- 🔴 **Merah Peringatan (`#DC2626` / `text-red-600` / `bg-red-500`)**: Potongan harga/diskon khusus, denda keterlambatan, status piutang *BELUM LUNAS*, tombol bahaya (*nonaktifkan akun*).
- 🟡 **Kuning Amber (`#D97706` / `text-amber-600` / `bg-amber-500`)**: Status transaksi *BOOKING / MENUNGGU REVIEW*, batas waktu mendekati cutoff.

---

## 3. STANDARISASI HALAMAN TRANSAKSI & KWITANSI PEMBAYARAN

### 3.1 Keselarasan Format Kwitansi / Bukti Order Antarmenu
Pengguna menginstruksikan agar tampilan seluruh jenis transaksi diselaraskan dengan tata kelola kwitansi resmi Kabar Priangan:
- **Transaksi Iklan Koran**
- **Transaksi Iklan Priangan TV**
- **Transaksi Iklan Online / Siber**
- **Transaksi Surat Jalan & Tagihan Agen Sirkulasi**

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

### 3.2 Tata Letak Formulir Pemesanan (Grid Layout Standar)
Formulir input transaksi wajib dirancang simetris dan rapi:
1. **Bagian 1: Identitas Order & Tanggal Tayang/Terbit** (Kop Kota, Jenis Iklan, Tanggal Order, Deadline Cetak).
2. **Bagian 2: Data Pengiklan & Perusahaan** (Nama Klien, No. Telp, Alamat, Kategori Instansi/Swasta/Perseorangan).
3. **Bagian 3: Spesifikasi Teknis Cetak / Siar** (Ukuran Kolom x Milimeter, Halaman Koran, Jenis Warna FC/BW, atau Durasi Detik untuk TV).
4. **Bagian 4: Kalkulasi Finansial Otomatis** (Tarif Dasar, Potongan Diskon, PPN 11%, Uang Muka DP, Sisa Pembayaran).
5. **Bagian 5: Pengunggahan Materi Iklan & Catatan Redaksi** (File PDF/TIFF siap cetak, teks naskah berita advertorial).

### 3.3 Penanganan Tata Letak Responsif & Pencegahan Elemen Bertumpuk (*Anti-Stacking*)
- Pada kasus kartu info atau ringkasan step (Step 1, Step 2, Step 3, Step 4):
  - Dilarang keras menggunakan posisi absolut tanpa perhitungan lebar responsif (`absolute right-0`).
  - Wajib menggunakan sistem **CSS Flexbox / CSS Grid** yang adaptif:
    ```html
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Step 1, 2, 3, 4 tersusun presisi tanpa bertumpukan -->
    </div>
    ```
  - Memastikan padding dan margin tidak saling tumpang tindih (*negative margin collision*).

---

## 4. STANDARISASI HALAMAN LOGIN & ELEMEN FORMULIR AUTENTIKASI

### 4.1 Elemen Visual Formulir Login
Formulir login SiKAPRI dirancang minimalis, berwibawa, dan aman:
1. **Logo Perusahaan**:
   - Menampilkan logo resmi Harian Umum Kabar Priangan di tengah atas form dengan dimensi `h-14 w-auto object-contain`.
   - Menggunakan fallback onerror terproteksi ke `asset('images/kabarpriangan.png')`.
2. **Tipografi Judul**:
   - Judul: *"Login ke Akun Anda"* (`text-[17px] font-bold text-black`).
   - Deskripsi: *"Silakan masukkan email dan kata sandi untuk mengakses sistem."* (`text-xs text-black font-medium`).
3. **Field Kata Sandi dengan Tombol Mata (*Eye Icon Toggle*)**:
   - Diwajibkan menyertakan tombol interaktif SVG ikon mata di sisi kanan field input password.
   - Menggunakan Alpine.js (`x-data="{ showPass: false }"`) untuk peralihan cepat antara `:type="showPass ? 'text' : 'password'"` tanpa reload halaman.
   - Menyertakan tooltip aksesibilitas: *"Lihat password"* dan *"Sembunyikan password"*.
4. **Penghapusan Fitur Publik yang Berbahaya**:
   - Tidak ada tautan publik *"Lupa Kata Sandi?"*.
   - Tidak ada tautan pendaftaran mandiri *"Daftar Akun Baru"*.
5. **Tombol Masuk Utama**:
   - Menggunakan tombol bersudut melengkung halus (`rounded-lg`), latar biru Kabar Priangan (`bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800`), dengan bayangan lembut (*shadow-sm*), teks tebal putih kontras tinggi.

---

## 5. SISTEM ALERT & POP-UP INTERAKTIF (SWEETALERT2 & APPALERT)

### 5.1 Format Dialog Standar Indonesia
Semua pesan sukses, peringatan, maupun galat validasi wajib menggunakan komponen modal terpusat `AppAlert` berbasis SweetAlert2 yang selaras:

#### A. Alert Galat Autentikasi / Salah Password
```javascript
AppAlert.error({
    title: 'Login Tidak Berhasil',
    subtitle: 'Autentikasi Ditolak',
    message: 'Email atau kata sandi yang Anda masukkan tidak sesuai dengan data sistem. Silakan periksa kembali.',
    buttonText: 'Coba Lagi'
});
```

#### B. Alert Akun Terkunci (Rate Limit Lockout 15 Menit)
```javascript
AppAlert.warning({
    title: 'Akses Akun Ditangguhkan',
    subtitle: 'Batas Percobaan Tercapai (3x Gagal)',
    message: 'Demi menjaga keamanan akun dan sistem pers, login ditangguhkan sementara selama 15 menit. Silakan coba kembali nanti atau hubungi Administrator.',
    buttonText: 'Mengerti'
});
```

#### C. Alert Konfirmasi Tindakan Berbahaya (Disable Akun)
```javascript
AppAlert.confirm({
    title: 'Konfirmasi Penonaktifan Akun',
    message: 'Apakah Anda yakin ingin menonaktifkan akun staf ini? Pengguna tidak akan dapat login kembali hingga diaktifkan oleh Administrator.',
    confirmText: 'Ya, Nonaktifkan Akun',
    cancelText: 'Batalkan',
    confirmColor: '#DC2626'
});
```

---

## 6. STANDAR CETAK DOKUMEN & CETAK KORAN (PRINT STYLESHEET GUIDELINE)

Setiap halaman cetak nota, kwitansi, atau formulir order koran wajib memiliki media query cetak khusus:

```css
@media print {
    /* Sembunyikan elemen navigasi dan tombol aksi */
    nav, aside, header, .no-print, button, .action-buttons {
        display: none !important;
    }
    
    /* Pastikan warna teks tercetak 100% hitam pekat */
    body, p, span, h1, h2, h3, td, th, label {
        color: #000000 !important;
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Border tabel tajam untuk printer dot matrix atau laserjet */
    table, th, td {
        border-color: #000000 !important;
    }
}
```

---

## 7. RINGKASAN CHECKLIST KUALITAS TAMPILAN (QA AUDIT)

Sebelum rilis atau demonstrasi ke pembimbing, pastikan setiap halaman lolos uji:
- [x] Tidak ada teks abu-abu pudar di deskripsi dan label form (wajib `text-black`).
- [x] Field password memiliki tombol mata intip yang berfungsi lancar.
- [x] Halaman login tidak memiliki tautan lupa password publik.
- [x] Grid/kartu statistik tidak saling tumpang tindih pada resolusi layar 1366x768 (laptop standar kantor) maupun 1920x1080.
- [x] Format kwitansi dan bukti order seragam antara Iklan Koran, Priangan TV, dan Sirkulasi.
- [x] Alert kesalahan menggunakan Bahasa Indonesia lugas dan solutif.
