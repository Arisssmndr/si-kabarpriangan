# Buku Panduan Resmi Operasional & Pengembangan Sistem
## Sistem Informasi Manajemen Terpadu — Harian Umum Kabar Priangan
*(Koran Cetak • Media Online • Priangan TV)*

**Versi:** 2026 Terkini — Revisi Full Integrated UI (Docked & Dark/Light Mode)  
**Tanggal Rilis:** Oktober 2026  
**Status Modul:** Kasir Terintegrasi, Iklan, Sirkulasi, Keuangan, Accounting & Administrator  

---

## 📌 Ringkasan Pembaruan Terkini (Oktober 2026)

1. **Tampilan Sidebar Docked Terpadu:**
   Sidebar navigasi menyatu penuh dengan tinggi layar (*h-screen*), tanpa celah mengambang (*floating gap*). Dilengkapi tombol **Perkecil / Perbesar Sidebar** yang dapat diakses di bagian topbar dan di samping logo brand. Pilihan lebar sidebar tersimpan otomatis di browser (`localStorage`).
2. **Mode Terang & Mode Gelap (Light / Dark Mode):**
   Tersedia tombol **Sun & Moon Switcher** di sebelah profil pengguna pada header atas. Mode gelap mengusung palet *Obsidian Midnight* (`#0b0f19`) yang elegan, bebas silau, dengan aksen *Cyan Teal* (`#2dd4bf`) dan *Amber Gold* (`#fbbf24`) untuk keterbacaan data keuangan yang jernih.
3. **Penyempurnaan Submenu Aktif:**
   Submenu aktif di mode gelap menggunakan latar *translucent emerald pill* lembut dengan teks hijau menyala (`#34d399`), menghilangkan tampilan putih silau pada versi sebelumnya.
4. **Scanner Barcode & QR Code Faktur:**
   Halaman kasir setoran iklan dan sirkulasi mendukung pemindaian langsung melalui kamera perangkat serta dukungan pembaca *Laser Barcode Scanner USB/Bluetooth*.
5. **Cetak Dokumen Bersih (Clean Print Standard):**
   Saat mencetak kuitansi BKM, BKK, atau lembar rekapitulasi, seluruh tombol web, header, navbar, dan background gelap otomatis dihilangkan (*CSS print-friendly*).
6. **Starter Script Instan:**
   Disediakan file [serve.bat](file:///c:/laragon/www/si-kabarpriangan/serve.bat) dan [dev.bat](file:///c:/laragon/www/si-kabarpriangan/dev.bat) yang mendeteksi otomatis *environment* Laragon.

---

## BAB I: Petunjuk Menjalankan Aplikasi & Setup Environment

### 1.1. Prasyarat Sistem (Laragon Windows)
- **Web Server & Database:** Apache/Nginx & MySQL 8.x (Laragon).
- **PHP:** Versi 8.2 atau 8.3 (Wajib mengaktifkan ekstensi `extension=zip`, `extension=pdo_mysql`, dan `extension=gd` pada `php.ini`).
- **Node.js:** Versi 20.x atau lebih baru.
- **Nama Database:** `db_kabarpriangan`.

### 1.2. Menjalankan Server Laravel
Untuk menjalankan backend server Laravel pada port 8000:
- Cukup **klik ganda** file [serve.bat](file:///c:/laragon/www/si-kabarpriangan/serve.bat) pada folder utama project, ATAU
- Buka terminal PowerShell lalu jalankan:
  ```powershell
  php artisan serve
  ```
- Aplikasi dapat diakses di browser melalui alamat: `http://127.0.0.1:8000`

### 1.3. Menjalankan Frontend Build / Vite
- Untuk mode pengembangan (*Hot Reload*): jalankan [dev.bat](file:///c:/laragon/www/si-kabarpriangan/dev.bat) atau ketik `npm run dev`.
- Untuk kompilasi produksi (*Production Build*):
  ```powershell
  npm run build
  ```

---

## BAB II: Akses Masuk & Daftar Akun Resmi Sistem

Akses halaman login melalui `http://127.0.0.1:8000/login`. Seluruh akun default menggunakan password: `password`.

| Nama Pengguna | Email Login | Password | Peran & Akses Utama |
| :--- | :--- | :--- | :--- |
| **Staf Divisi Kasir (Pak Eki)** | `kasir@kabarpriangan.co.id` | `password` | **Kasir Utama:** Penerimaan (KI), Pengeluaran (KO), Kasbon, dan Tutup Shift. |
| **Kasir Operasional Keuangan** | `kasir.keuangan@kabarpriangan.co.id` | `password` | **Kasir Backup:** Menangani loket darurat & operasional kas. |
| **Staf Divisi Keuangan** | `keuangan@kabarpriangan.co.id` | `password` | **Approval Keuangan:** Validasi bukti pengeluaran dan ACC kasbon. |
| **Admin Keuangan (Ibu Eli)** | `admin.keuangan@kabarpriangan.co.id` | `password` | **Manajer Keuangan:** Otorisasi buka kas (*re-open shift*) dan audit kas. |
| **Super Administrator** | `admin@kabarpriangan.co.id` | `password` | **Administrator:** Hak akses penuh seluruh modul dan konfigurasi master. |
| **Staf Divisi Iklan** | `iklan@kabarpriangan.co.id` | `password` | **Front Office Iklan:** Input order koran cetak, online, dan TV. |
| **Staf Divisi Sirkulasi** | `sirkulasi@kabarpriangan.co.id` | `password` | **Sirkulasi:** Distribusi jatah agen, retur koran, dan eksemplar sisa. |
| **Staf Divisi Accounting** | `accounting@kabarpriangan.co.id` | `password` | **Akuntansi:** Jurnal umum, penarikan data mutasi kasir ke Buku Besar. |

---

## BAB III: Panduan Antarmuka Modern (Docked Sidebar, Light & Dark Mode)

### 3.1. Sidebar Docked & Toggle Collapse
- Sidebar terintegrasi langsung di sebelah kiri tanpa floating margin.
- Klik tombol **Perkecil** di topbar atau ikon panah di samping logo untuk mengubah tampilan menjadi *icon-only*. Mode ini memberikan ruang kerja yang sangat luas untuk melihat tabel faktur dan kuitansi.
- Status sidebar (*expanded* atau *collapsed*) otomatis disimpan di `localStorage` peramban.

### 3.2. Switcher Mode Terang & Gelap
- Tombol **Mode Terang / Mode Gelap** terletak di pojok kanan atas topbar.
- **Mode Terang:** Latar canvas *slate-100* lembut yang tidak membuat silau, kartu putih ber-shadow tipis.
- **Mode Gelap:** Latar *Obsidian Midnight* (`#0b0f19`), kartu dark slate (`#121929`), teks putih tegas (`#f8fafc`), dan nomor tagihan/faktur beraksen cerah kontras tinggi (*Cyan Teal* dan *Amber Gold*).

---

## BAB IV: Kamus Istilah & Singkatan Perkasiran

| Singkatan | Kepanjangan | Makna Operasional |
| :--- | :--- | :--- |
| **KI** | *Kas Masuk (Cash In)* | Seluruh penerimaan kas fisik maupun transfer bank (Kuitansi: `KI-XX-YYYYMMXXXX`). |
| **KO** | *Kas Keluar (Cash Out)* | Seluruh pengeluaran uang kas untuk operasional dan realisasi (Voucher: `KO-YYYYMMXXXX`). |
| **BKM** | *Bukti Kas Masuk* | Tanda terima sah yang dicetak kasir saat menerima pembayaran. |
| **BKK** | *Bukti Kas Keluar* | Voucher sah saat kasir menyerahkan uang belanja ke pemohon. |
| **BP** | *Bukti Pengeluaran* | Pengajuan anggaran belanja divisi sebelum dicairkan kasir. |
| **COA** | *Chart of Accounts* | Bagan akun akuntansi resmi agar transaksi terhubung otomatis ke Buku Besar. |
| **WAPU** | *Wajib Pungut Pajak* | Instansi pemerintah/BUMN yang memotong PPN & PPh 23 untuk disetor sendiri ke kas negara. |
| **CBP** | *Cash Before Print* | Kebijakan wajib lunas sebelum koran dicetak untuk klien iklan lepas. |
| **BAPR** | *Berita Acara Retur* | Bukti fisik retur koran sisa kemarin dari agen gudang (Rp 2.100 per eksemplar). |
| **AE** | *Account Executive* | Staf marketing/sales iklan yang mencari order iklan dan menerima insentif 10%. |
| **KH** | *Koresponden Harian* | Wartawan kontributor daerah yang berhak menerima komisi berita/advertorial ~20%. |

---

## BAB V: Operasional Transaksi Kas Masuk (KI)

### 5.1. Input Setoran Iklan (`/kasir/penerimaan/iklan`)
- **Pencarian Faktur / Barcode:** Ketik nomor faktur atau gunakan **Scanner Kamera** untuk memindai barcode/QR code pada lembar cetak faktur.
- **Potongan Pajak WAPU:** Centang opsi WAPU jika pemesan adalah dinas/pemda. Sistem otomatis memisahkan DPP, PPN (11%/12%), dan PPh 23 (2%) sehingga nilai uang riil yang diterima kasir sesuai fisik tanpa selisih laci.
- **Pilihan Metode:** Pilih *Tunai* (laci kasir) atau *Transfer Bank BJB / BCA*.
- **Cetak BKM:** Klik *Simpan & Cetak* untuk menerbitkan Bukti Kas Masuk resmi.

### 5.2. Input Setoran Koran Agen (`/kasir/penerimaan/sirkulasi`)
- Pilih nama agen koran dari daftar wilayah (Tasikmalaya, Ciamis, Banjar, Garut, Pangandaran).
- Masukkan lembar retur koran sisa kemarin berdasarkan dokumen BAPR (otomatis terpotong Rp 2.100 / eks).
- Masukkan uang yang disetor dan simpan kuitansi tanda terima setoran.

### 5.3. Penjualan Koran Sisa & Eceran (`/kasir/penerimaan/koran-sisa`)
- Melayani pembelian tunai di loket untuk koran sisa cetak per kilogram, koran eceran, dan limbah kertas percetakan (*afval*).

### 5.4. Kas Bank & Jasa Giro (`/kasir/penerimaan/bank`)
- Mencatat penarikan tunai dari rekening giro Bank BJB/BCA menggunakan warkat cek untuk menambah modal uang fisik di laci kasir.

---

## BAB VI: Operasional Transaksi Kas Keluar (KO)

### 6.1. Realisasi BP (Bukti Pengeluaran Kas Kecil)
- Masuk ke menu `/kasir/pengeluaran/realisasi-bp`.
- Temukan pengajuan yang sudah berstatus **Approved Keuangan**.
- Klik tombol **Cairkan**, input nama staf yang mengambil uang tunai, pilih metode pencairan (*Kas Fisik* atau *Transfer BJB*), lalu klik **Cairkan Kas Keluar** untuk mencetak voucher BKK.

### 6.2. Pencairan Komisi KH (20%) & Insentif AE (10%)
- Masuk ke menu `/kasir/pengeluaran/komisi`.
- Sistem hanya menampilkan faktur yang sudah berstatus **LUNAS**.
- Klik **Cairkan KH (20%)** untuk hak wartawan humas atau **Cairkan AE (10%)** untuk insentif sales marketing.
- Sistem menerbitkan nomor pengeluaran KO dan mencetak voucher pencairan komisi.

---

## BAB VII: Modul Kasbon / Uang Muka Operasional

Digunakan saat ada kebutuhan belanja mendesak yang belum sempat diajukan melalui BP (misal: pembelian air galon kantor):
1. **Input Kasbon Baru:** Buka menu `/kasir/pengeluaran/kasbon`, masukkan nama pemohon, nominal, divisi, dan alasan pengeluaran.
2. **Approval (ACC):** Kasbon dapat di-ACC langsung oleh Manajer Keuangan atau disimpan dalam status *menunggu_acc*.
3. **Pengurang Kas Fisik Otomatis:** Setiap kasbon berstatus *pending* otomatis menjadi faktor pengurang pada target fisik laci kasir:
   $$\text{Target Fisik Laci} = \text{Saldo Awal} + \text{Total KI} - \text{Total KO} - \text{Kasbon Gantung}$$
4. **Penyelesaian Kasbon:** Setelah pemohon menyerahkan nota belanja asli, klik **Selesaikan Kasbon** agar kasbon berstatus lunas.

---

## BAB VIII: Laporan, Rekonsiliasi, & Tutup Shift (Closing)

### 8.1. Laporan Rekapitulasi Kasir (`/kasir/laporan/rekap`)
- Menyediakan dua mode tampilan: **Tab Format Klasik DOS KAS.EXE** dan **Tab Tabel Interaktif Per Divisi**.
- Dilengkapi tombol **Export CSV (Excel)** dan **Cetak Lembar Rekap Resmi**.

### 8.2. Tutup Kas Harian (Closing Shift) (`/kasir/utilitas/closing`)
- Wajib dilakukan kasir setiap sore hari (16.30 - 17.00 WIB).
- Hitung lembaran uang fisik di laci per pecahan (Rp 100.000 s/d Rp 1.000 dan koin).
- Masukkan jumlah lembar ke form closing. Sistem otomatis memvalidasi status:
  - **SEIMBANG / BALANCE:** Selisih Rp 0 (Tanda hijau).
  - **SELISIH LEBIH / SURPLUS:** Tanda biru.
  - **SELISIH KURANG / DEFISIT:** Tanda merah (Wajib mengisi Berita Acara).
- Klik **Tutup Shift Kasir** untuk mengunci transaksi hari ini agar aman ditarik oleh akuntansi.

### 8.3. Otorisasi Buka Kembali Kas (Re-open Shift) (`/kasir/utilitas/buka-kas`)
- Jika shift telah ditutup sore hari namun ada transaksi darurat malam hari, kasir dapat mengajukan permohonan buka kembali kas.
- Shift akan terbuka kembali setelah disetujui Manajer Keuangan (Ibu Eli) via tombol approval atau input password otorisasi.

---

## BAB IX: Standard Operating Procedure (SOP) Harian Kasir

| Jam Kerja | Kegiatan Utama | Tindakan di Sistem Aplikasi |
| :--- | :--- | :--- |
| **07.30 - 08.00** | Buka Loket & Modal Awal | Hitung uang kembalian di laci, login akun kasir, pastikan status shift Aktif. |
| **08.00 - 11.30** | Pelayanan Setoran Sirkulasi | Terima setoran agen, periksa retur BAPR, simpan kuitansi KI Sirkulasi. |
| **11.30 - 14.30** | Setoran Iklan & Realisasi BP | Terima pelunasan iklan (scan barcode), cairkan BP yang approved keuangan. |
| **14.30 - 16.00** | Kasbon Darurat & Komisi Sales | Catat kasbon mendesak dengan ACC Keuangan, cairkan komisi faktur lunas. |
| **16.00 - 16.45** | Opname Fisik & Closing | Hitung fisik uang per lembar, input ke Tutup Shift Kasir, pastikan balance. |
| **16.45 - 17.00** | Cetak Rekap & Serah Terima | Cetak Laporan Kas Harian (3 tanda tangan), arsipkan bukti kas fisik ke map. |

---

*Dokumen ini diterbitkan resmi oleh Harian Umum Kabar Priangan — Divisi Operasional Kasir & Divisi Keuangan.*

