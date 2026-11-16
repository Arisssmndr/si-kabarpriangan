# Buku Panduan Resmi Operasional & Pengembangan Sistem
## Sistem Informasi Manajemen Terpadu — Harian Umum Kabar Priangan
*(Koran Cetak • Media Online • Priangan TV)*

**Versi:** 2026 Terkini — Revisi Modul Kasir Terpadu (Security Hardening, Edit Inputan, Shift Lock & Calendar Datepicker)  
**Tanggal Rilis:** November 2026  
**Status Modul:** Kasir Terintegrasi, Iklan, Sirkulasi, Keuangan, Accounting & Administrator  

---

## 📌 Ringkasan Pembaruan Terkini (November 2026)

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
   Disediakan file `serve.bat` dan `dev.bat` yang mendeteksi otomatis *environment* Laragon.
7. **Penguncian Kas Harian Setelah Closing (November 2026):**
   Setelah kas hari ini ditutup (misal 6 November pukul 16.00), seluruh menu input KI/KO, Koreksi, dan Buka Shift pada tanggal tersebut **terkunci**. Tanggal yang sudah ditutup hanya bisa dibuka kembali lewat **Persetujuan Accounting**. Keesokan harinya (7 November) shift baru **terbuka otomatis** sehingga kasir langsung bisa input sampai kas ditutup.
8. **Fitur Edit Inputan Kasir (November 2026):**
   Admin kasir dapat mengoreksi inputan yang salah (nama pembayar, nominal, metode bayar, bank, no. referensi, uraian) selama transaksi belum ditutup. Menu ini dapat diakses langsung melalui menu sidebar **Utilitas Kontrol > Edit & Koreksi Transaksi**, tombol **Edit Transaksi** di header Dashboard, atau tombol **Edit** pada tabel transaksi terkait. Saldo piutang faktur/agen, total shift, dan mutasi kas ikut disesuaikan otomatis, dan setiap perubahan tercatat di jejak audit.
9. **Fitur Pilih Tanggal Kalender (Datepicker) di Seluruh Menu Pencarian (November 2026):**
   Seluruh formulir pencarian dan filter arsip (Buka Kembali Kas, Cetak Ulang Kuitansi, Riwayat Cetak, Realisasi BP, Komisi, Kas Bank, Koran Sisa, dan Laporan Kasbon) kini dilengkapi pemilih tanggal kalender (`<input type="date">`). Kasir tidak perlu mengetik format tanggal `YYYY-MM-DD` secara manual, cukup klik kalender dan pilih tanggal.
10. **Notifikasi & Dialog Modern:**
   Seluruh `alert()` / `confirm()` bawaan browser diganti dialog dan toast yang lebih ringkas. Aksi berisiko tinggi (Tutup Kas) memakai tema **merah**, bukan oranye.
11. **Perapian Dropdown (Select):**
   Ikon panah dropdown kini punya ruang sendiri di sisi kanan sehingga tidak menimpa teks (contoh: filter *Per Minggu (7 Hari)*), jelas terbaca di mode terang maupun gelap.

---

## BAB I: Petunjuk Menjalankan Aplikasi & Setup Environment

### 1.1. Prasyarat Sistem (Laragon Windows)
- **Web Server & Database:** Apache/Nginx & MySQL 8.x (Laragon).
- **PHP:** Versi 8.2 atau 8.3 (Wajib mengaktifkan ekstensi `extension=zip`, `extension=pdo_mysql`, dan `extension=gd` pada `php.ini`).
- **Node.js:** Versi 20.x atau lebih baru.
- **Nama Database:** `db_kabarpriangan`.

### 1.2. Menjalankan Server Laravel
Untuk menjalankan backend server Laravel pada port 8000:
- Cukup **klik ganda** file `serve.bat` pada folder utama project, ATAU
- Buka terminal PowerShell lalu jalankan:
  ```powershell
  php artisan serve
  ```
- Aplikasi dapat diakses di browser melalui alamat: `http://127.0.0.1:8000`

### 1.3. Menjalankan Frontend Build / Vite
- Untuk mode pengembangan (*Hot Reload*): jalankan `dev.bat` atau ketik `npm run dev`.
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

## BAB V: Panduan Lengkap Seluruh Menu Divisi Kasir (Sistem KAS)

Bagian ini memuat penjelasan mendalam mengenai **tujuan/fungsi setiap menu** dan **tata cara langkah demi langkah penggunaannya** khusus untuk personel **Divisi Kasir** (`kasir@kabarpriangan.co.id`). Seluruh penomoran dokumen kuitansi mengikuti standar historis DOS KAS.EXE (`KI-XX-YYYYMMXXXX` dan `KO-YYYYMMXXXX`) yang terintegrasi otomatis ke Buku Besar Akuntansi.

```
                    ┌──────────────────────────────────────────────┐
                    │          STRUKTUR MENU DIVISI KASIR          │
                    └──────────────────────┬───────────────────────┘
                                           │
         ┌───────────────────┬─────────────┴───────┬───────────────────┐
         ▼                   ▼                     ▼                   ▼
  1. DASHBOARD KASIR    2. KAS MASUK (KI)    3. KAS KELUAR (KO)    4. KASBON / UM
  - Target Fisik Laci   - Setoran Iklan      - Realisasi BP        - Input Kasbon
  - Ringkasan Harian    - Setoran Agen       - Komisi KH & AE      - Otorisasi Keuangan
  - Quick Shortcuts     - Koran Sisa/Eceran  - Setoran Kas Bank    - Selesaikan Kasbon
                        - Bank / Jasa Giro
                        - Piutang Langganan
                                           │
                        ┌──────────────────┴──────────────────┐
                        ▼                                     ▼
                5. LAPORAN & MUTASI                   6. UTILITAS KONTROL
                - Rekap KI & KO (DOS / Modern)        - Tutup Kas Harian (Closing)
                - Mutasi Kas Bank                     - Buku Kas Harian (BKU)
                - Mutasi per Akun COA                 - Edit & Koreksi Transaksi
                - Posisi Saldo Fisik Laci             - Cetak Ulang Kuitansi
                - Monitoring Kasbon Gantung           - Riwayat & Log Cetak
                - Laporan Harian (3 Tanda Tangan)     - Buka Kembali Kas (Re-open)
```

---

### 5.1. Dashboard Utama Kasir (`/kasir/dashboard`)

#### A. Untuk Apa Penggunaannya?
Sebagai pusat komando harian kasir untuk memantau kondisi keuangan kasir secara langsung (*real-time*). Menu ini menyajikan:
1. **Status Shift Kasir:** Memastikan apakah loket kasir saat ini berstatus **AKTIF (OPEN)** atau **DITUTUP (CLOSED)**.
2. **Target Fisik Laci Kas:** Menampilkan jumlah uang tunai fisik yang wajib ada di dalam laci kasir saat detik ini juga, dihitung secara otomatis dengan rumus:
   $$\text{Target Fisik Laci} = \text{Saldo Awal Shift} + \text{Total Kas Masuk Tunai} - \text{Total Kas Keluar Tunai} - \text{Kasbon Gantung}$$
3. **Widget Rekap Harian:** Total Kas Masuk (KI), Total Kas Keluar (KO), dan Kasbon Aktif/Pending.
4. **Tabel Transaksi Terakhir:** Menampilkan 10 transaksi mutasi kas terakhir hari ini agar kasir dapat memverifikasi input secara instan tanpa berpindah halaman.
5. **Tombol Aksi Cepat (*Quick Action*):** Akses instan 1-klik menuju form setoran iklan, sirkulasi, realisasi BP, kasbon, dan penutupan shift.

#### B. Cara Menggunakannya:
1. Setelah login menggunakan akun kasir, sistem langsung mengarahkan Anda ke halaman Dashboard ini.
2. **Periksa Status Shift di Pojok Kiri Atas:** Pastikan terdapat lencana hijau **"Shift Aktif"**. Jika shift belum dibuat, sistem otomatis menginisialisasi shift baru dengan saldo awal yang diambil dari saldo akhir fisik shift sebelumnya (atau modal awal default Rp 1.000.000).
3. **Periksa Angka "Target Fisik Kas":** Cocokkan secara berkala dengan perkiraan uang tunai di laci kasir.
4. Klik tombol pintasan warna hijau **"+ Setoran Iklan"** atau **"+ Setoran Agen"** jika ada pelanggan/agen yang datang menyetor uang di loket kasir.
5. Jika ingin melihat rincian mutasi hari ini, gulir layar ke bagian tabel **"Aktivitas Mutasi Kas Terbaru"**.

---

### 5.2. Kelompok Menu Kas Masuk (KI) — Bidang Keuangan

Seluruh uang yang diterima kasir (baik uang tunai laci maupun pencairan/transfer bank perusahaan) diinput melalui submenu **Kas Masuk (KI)** agar menghasilkan nomor Bukti Kas Masuk (BKM) resmi.
> **Aturan Cetak Kuitansi Penerimaan Kas (KI):** Pada seluruh lembar cetak satuan bukti penerimaan kas harian (pelunasan iklan, sirkulasi agen, koran sisa/tunai, maupun kas bank), kolom tanda tangan **HANYA untuk "Kasir Penerima"** (tanpa tanda tangan manajer/akuntansi pada lembar satuan kuitansi).

#### 5.2.1. Input Setoran Iklan (`/kasir/penerimaan/iklan`)
- **Untuk Apa Penggunaannya?**
  Mencatat pelunasan tagihan faktur iklan yang diterbitkan oleh Divisi Iklan (iklan koran cetak, iklan portal berita online, maupun Priangan TV), baik pembayaran langsung oleh pemasang iklan di loket maupun setoran yang dibawa oleh Petugas Inkaso / Account Executive (AE). Menu ini juga secara otomatis menangani **pemotongan pajak instansi (WAPU)** bagi klien dinas/BUMN sehingga saldo laci kasir tidak mengalami selisih minus.
- **Cara Menggunakannya:**
  1. Buka menu **Kas Masuk (KI) > Input Setoran Iklan**.
  2. **Pencarian Faktur:**
     - Ketik nomor faktur iklan pada kolom pencarian (misal: `FK-202610...`), ATAU
     - Klik tombol **"Scan Barcode / QR Code"** untuk memindai lembar fisik surat penagihan/faktur menggunakan kamera laptop/ponsel atau pemindai laser USB.
  3. Data faktur akan otomatis dimuat ke formulir (Nama Klien, Kategori Media, Nilai Tagihan, dan Sisa Piutang).
  4. **Pajak WAPU Instansi (Jika Ada):**
     - Jika pemesan iklan adalah instansi dinas/pemerintah/BUMN, centang kotak opsi **"WAPU / Potong Pajak Instansi"**.
     - Sistem otomatis memisahkan Dasar Pengenaan Pajak (DPP), PPN (11%/12%), dan PPh 23 (2%). Kasir hanya perlu menagih dan memasukkan uang riil bersih (setelah dipotong pajak).
  5. Masukkan jumlah uang yang dibayarkan pada kolom **Nominal Bayar**.
  6. Pilih **Metode Pembayaran**: `Tunai` (masuk ke laci fisik), `Transfer Bank BJB`, `Transfer Bank BCA`, atau `QRIS`.
  7. Masukkan nama penagih pada kolom **Petugas Inkaso / Penyetor** dan nomor blanko (jika ada).
  8. Klik tombol **"Simpan & Cetak Kuitansi"**.
  9. Sistem menerbitkan nomor kuitansi resmi `KI-01-YYYYMMXXXX` dan otomatis membuka jendela pratinjau cetak BKM Iklan dengan **1 kolom tanda tangan: Kasir Penerima**.

#### 5.2.2. Input Setoran Koran Agen Sirkulasi (`/kasir/penerimaan/sirkulasi`)
- **Untuk Apa Penggunaannya?**
  Mencatat penerimaan setoran uang jatah koran harian dari para agen koran, pengecer, dan loper di seluruh wilayah distribusi Harian Umum Kabar Priangan (Tasikmalaya Kota/Kab, Ciamis, Banjar, Garut, dan Pangandaran), sekaligus memproses **pemotongan retur eksemplar koran sisa kemarin** berdasarkan dokumen resmi Berita Acara Retur (BAPR) ekspedisi.
- **Cara Menggunakannya:**
  1. Buka menu **Kas Masuk (KI) > Input Setoran Koran Agen**.
  2. Pilih nama Agen dari daftar pilihan dropdown atau ketik Kode Agen (misal: `AG-01-002`).
  3. Sistem langsung menampilkan rekap kewajiban sirkulasi agen tersebut dan batas plafon piutangnya.
  4. **Input Retur Koran Kemarin (BAPR):**
     - Jika agen membawa koran sisa cetak kemarin yang tidak laku terjual dan telah diverifikasi Bagian Ekspedisi/Gudang, masukkan jumlah lembar retur pada kolom **"Retur Eksemplar"**.
     - Sistem otomatis mengalikan jumlah retur dengan nilai kompensasi resmi (Rp 2.100 per eksemplar) dan memotong total tagihan agen secara transparan.
  5. Masukkan nominal uang yang disetorkan agen pada kolom **Nominal Setoran**.
  6. Pilih metode pembayaran (`Tunai` atau `Transfer Bank BJB Sirkulasi`).
  7. Masukkan nomor register BAPR fisik pada kolom **Nomor BAPR Ref** untuk kebutuhan audit sirkulasi.
  8. Klik tombol **"Simpan Setoran Agen"**. Sistem menerbitkan kuitansi BKM Sirkulasi (`KI-02-YYYYMMXXXX`) dengan tanda tangan tunggal **Kasir Penerima**.

#### 5.2.3. Penerimaan Tunai / Penerimaan Lain-lain & Koran Sisa (KI-14) (`/kasir/penerimaan/tunai`)
- **Untuk Apa Penggunaannya?**
  Melayani transaksi penerimaan tunai langsung dan penerimaan lain-lain di loket kasir (`KI-14`), termasuk **penjualan koran sisa cetak / eceran kiloan (Kg) maupun eksemplar (Eks)** yang telah diintegrasikan sebagai kategori langsung di dalam form Penerimaan Tunai (`KI-14`).
- **Cara Menggunakannya:**
  1. Buka menu **Kas Masuk (KI) > Penerimaan Tunai / Lain-lain (KI-14)** lalu klik tombol **"Input Transaksi Tunai"**.
  2. Ketik nama pembeli/pengepul/pemohon pada kolom **Nama Pembeli / Pembayar**.
  3. Pilih **Kategori Transaksi (KI-14)**:
     - `Koran Sisa Cetak / Eceran Kiloan (KI-14)` — menampilkan kalkulator otomatis satuan `Kiloan (Kg)` atau `Eceran (Eks)` beserta input Berat/Jumlah dan Harga Satuan (COA `7.00.02.01.02`).
     - `Pembelian Koran Eceran Sisa Stock`
     - `Iklan Baris / Kolom Kecil`
     - `Penjualan Kertas Afval / Koran Bekas`
     - `Pendapatan Kasir Lainnya`
  4. Masukkan uraian transaksi dan nominal diterima (terisi otomatis jika menggunakan kalkulator Koran Sisa).
  5. Pilih metode pembayaran (`Tunai Kasir`, `Transfer QRIS`, `Transfer Bank BJB`, atau `Transfer Bank BCA`).
  6. Klik tombol **"Simpan & Cetak Nota Kasir"**. Sistem menerbitkan kuitansi resmi `KI-14-YYYYMMXXXX` dengan kolom tanda tangan tunggal **Kasir Penerima**.

#### 5.2.4. Penerimaan Kas Bank (KI-09) (`/kasir/penerimaan/bank`)
- **Untuk Apa Penggunaannya?**
  Mencatat penambahan uang tunai fisik ke dalam brankas kasir yang bersumber dari penarikan tunai rekening giro bank resmi perusahaan menggunakan Cek Tunai atau Bilyet Giro (`KI-09`).
- **Konfigurasi Rekening Bank Resmi:**
  - **Bank BJB Cabang Tasikmalaya:** Giro No. `05.00.01.006270.9` (COA `1.02.01.01.01`)
  - **Bank BCA Cabang Tasikmalaya:** Giro No. `054-0388-991` (COA `1.02.01.01.02`)
- **Cara Menggunakannya:**
  1. Buka menu **Kas Masuk (KI) > Penerimaan Kas Bank (KI-09)**.
  2. Pilih **Bank Sumber Rekening Giro**: `Bank BJB Tasikmalaya (Giro 05.00.01.006270.9)` atau `Bank BCA Tasikmalaya (Giro 054-0388-991)`.
  3. Masukkan nomor warkat cek pada kolom **No. Cek / Bilyet Giro** (misal: `CK-BJB-882910`).
  4. Masukkan jumlah uang tunai yang dicairkan pada kolom **Nominal Cair (Rp)** dan catatan keperluan kas.
  5. Klik tombol **"Simpan Penarikan Kas Bank (KI-09)"**.
  6. Lembar cetak Bukti Kas Masuk Bank (`KI-09`) hanya mencantumkan Nama Bank & Rekening, Nomor Cek, Nominal, dan tanda tangan **Kasir Penerima / Pembayar** (tanpa kolom nama/tanda tangan Petugas Penarik Cek).

---

### 5.3. Kelompok Menu Kas Keluar (KO) — Bidang Keuangan

Seluruh pengeluaran uang tunai laci wajib melalui pengeluaran resmi kas berstatus **Approved** (menu Kasbon dan menu Bank yang berdiri sendiri telah dihapus dan diintegrasikan langsung ke alur KI/KO Bidang Keuangan).

#### 5.3.1. Realisasi BP Anggaran (`/kasir/pengeluaran/realisasi-bp`)
- **Untuk Apa Penggunaannya?**
  Mencairkan dana belanja operasional divisi kantor (Redaksi liputan, Sirkulasi armada BBM, SDM/Umum, Iklan promosi, Listrik/PDAM/Internet, ATK) yang telah diverifikasi dan **disetujui (Approved)** oleh Bidang Keuangan. Seluruh pengeluaran kas wajib melalui dokumen resmi BP berstatus *approved*.
- **Cara Menggunakannya:**
  1. Buka menu **Kas Keluar (KO) > Realisasi BP Anggaran**.
  2. Sistem menampilkan daftar Bukti Pengeluaran yang berstatus hijau **"Approved Keuangan"**.
  3. Cari nomor BP atau nama pemohon, kemudian klik tombol **"Cairkan Uang"**.
  4. Ketik nama staf penerima uang fisik, pilih metode pencairan, lalu klik **"Cairkan Kas Keluar"**.
  5. Sistem menerbitkan voucher Bukti Kas Keluar (`KO-YYYYMMXXXX`), status BP berubah menjadi `dicairkan_kasir`, dan saldo uang fisik kasir berkurang secara otomatis.

#### 5.3.2. Pencairan Komisi KH & Insentif AE (Sales) (`/kasir/pengeluaran/komisi`)
- **Untuk Apa Penggunaannya?**
  Membayarkan hak Komisi Humas (KH) atau Insentif Account Executive (AE / Sales) atas penjualan iklan.
- **Ketentuan SOP Terbaru:**
  - **Tanpa Persentase Statis:** Tidak menggunakan label persentase statis (10% / 20%) karena nilai komisi bersifat fleksibel/fluktuatif dan ditarik langsung dari nominal komisi/insentif pada data penjualan iklan yang fakturnya sudah berstatus **LUNAS**.
  - **Tombol Aksi Tunggal:** Pada tabel antrean faktur lunas, cukup klik satu tombol ringkas **"Cairkan"**, lalu pilih jenis pencairan (`Komisi Humas (KH)` atau `Insentif AE / Sales`).
  - **4 Kolom Tanda Tangan Otorisasi Lengkap:** Lembar cetak Bukti Pencairan Komisi (`KO`) memuat 4 kolom tanda tangan resmi:
    1. **Penerima (AE / Sales)**
    2. **Manajer Keuangan**
    3. **Accounting**
    4. **Kasir**

#### 5.3.3. Setor Kas ke Bank (KO-09) (`/kasir/setoran-bank`)
- **Untuk Apa Penggunaannya?**
  Mencatat penyetoran fisik uang tunai dari brankas kasir ke rekening giro resmi perusahaan (**Bank BJB Giro: 05.00.01.006270.9** atau **Bank BCA Giro: 054-0388-991**).
- **Cara Menggunakannya:**
  1. Buka menu **Kas Keluar (KO) > Setor Kas ke Bank (KO-09)**.
  2. Pilih rekening bank tujuan (`Bank BJB` atau `Bank BCA`), masukkan **Nominal Disetor (Rp)** dan **Nomor Slip Teller**.
  3. Klik **"Simpan & Validasi Setoran"**.

---

### 5.4. Kelompok Menu Laporan & Mutasi — Bidang Keuangan

Seluruh pelaporan kas dan anggaran disatukan di bawah payung struktural **"Bidang Keuangan"** (tidak menggunakan label terpisah "Divisi Kasir" atau "Divisi Anggaran").

#### 5.4.1. Rekapitulasi Kas Masuk & Keluar (`/kasir/laporan/rekap`)
- **Untuk Apa Penggunaannya?**
  Menyajikan laporan rekapitulasi mutasi kas masuk (`KI`) dan kas keluar (`KO`) di bawah payung **Bidang Keuangan**, lengkap dengan fitur cetak dan **Export Excel (`.xls`)**.
- **Rangkuman Bawah Tabel & Export Excel:**
  Pada bagian paling bawah laporan dan file hasil **Export Excel**, disajikan tabel rangkuman yang memuat:
  1. **Akumulasi Subtotal Penerimaan per Divisi (`KI`):** Divisi Iklan, Divisi Sirkulasi, dan Bidang Keuangan / Lain-lain.
  2. **Akumulasi Subtotal Pengeluaran per Divisi (`KO`):** Divisi Iklan, Divisi Sirkulasi, Divisi Umum & SDM, Divisi Redaksi, dan Bidang Keuangan (termasuk Setor Bank).
  3. **Rekonsiliasi Saldo Akhir Kas Fisik dan Bank yang Seimbang (`BALANCE`):** Menampilkan Saldo Akhir Kas Fisik Brankas, Mutasi Setoran Bank, serta Total Saldo Kas & Bank yang Seimbang (*Balance*).

#### 5.4.2. Mutasi Kas Bank (`/kasir/laporan/mutasi-bank`)
- **Untuk Apa Penggunaannya?**
  Menampilkan buku mutasi khusus untuk transaksi perbankan (Bank BJB Giro `05.00.01.006270.9` & Bank BCA Giro `054-0388-991`), meliputi penerimaan transfer klien, penarikan cek (`KI-09`), dan setoran kas ke bank (`KO-09`).

#### 5.4.3. Mutasi per COA / Akun Akuntansi (`/kasir/laporan/perkiraan`)
- **Untuk Apa Penggunaannya?**
  Menampilkan jurnal mutasi kas yang telah dipetakan otomatis ke dalam nomor akun Chart of Accounts (COA) resmi Harian Umum Kabar Priangan sebelum ditarik oleh Accounting ke Buku Besar.

#### 5.4.4. Posisi Saldo Fisik Laci Kasir (`/kasir/laporan/posisi-kas`)
- **Untuk Apa Penggunaannya?**
  Menampilkan lembar kerja posisi kas riil detik ini sebelum kasir melakukan closing fisik di sore hari.

#### 5.4.5. Laporan Kas Harian (`/kasir/laporan-harian`)
- **Untuk Apa Penggunaannya?**
  Dokumen pertanggungjawaban harian Bidang Keuangan yang memuat ringkasan saldo awal, perincian mutasi masuk (`KI`) dan keluar (`KO`), serta posisi saldo akhir.

---

### 5.6. Kelompok Menu Utilitas & Kontrol Operasional

#### 5.6.1. Tutup Kas Harian (Closing Shift & Cash Opname) (`/kasir/utilitas/closing`)
- **Untuk Apa Penggunaannya?**
  Wajib dilakukan kasir setiap sore hari kerja (pukul 16.30 - 17.00 WIB) untuk mengunci transaksi hari ini melalui prosedur *cash count* / opname fisik lembaran uang kertas dan koin, mencocokkannya dengan target sistem komputer.
- **Cara Menggunakannya:**
  1. Buka laci kasir dan keluarkan seluruh uang tunai.
  2. Buka menu **Utilitas Kontrol > Tutup Kas Harian**.
  3. Kelompokkan dan hitung jumlah lembar uang per pecahan.
  4. Masukkan jumlah lembar pada kotak yang tersedia:
     - Jumlah lembar **Rp 100.000**
     - Jumlah lembar **Rp 50.000**
     - Jumlah lembar **Rp 20.000**
     - Jumlah lembar **Rp 10.000**
     - Jumlah lembar **Rp 5.000**
     - Jumlah lembar **Rp 2.000**
     - Jumlah lembar **Rp 1.000**
     - Total nilai **Uang Koin / Logam**
  5. **Sistem Menghitung Total Fisik dan Membandingkannya Secara Otomatis:**
     - **Status HIJAU (SEIMBANG / BALANCE):** Selisih Rp 0. Fisik laci tepat sama dengan pembukuan sistem.
     - **Status BIRU (SELISIH LEBIH / SURPLUS):** Uang fisik di laci lebih banyak daripada catatan komputer.
     - **Status MERAH (SELISIH KURANG / DEFISIT):** Uang fisik di laci kurang. Kasir **wajib** mengisi alasan pada kolom Berita Acara Selisih Kasir.
  6. Masukkan catatan penutupan shift jika diperlukan.
  7. Klik tombol merah **"Kunci Shift & Tutup Kas Harian"**, lalu konfirmasi pada dialog yang muncul.
  8. Shift terkunci (*Closed*), transaksi kasir hari ini selesai dan siap ditarik oleh akuntansi.
  9. **Aturan penguncian:** setelah closing, tanggal tersebut **tidak bisa diinput lagi** (menu KI, KO, Koreksi otomatis diarahkan ke halaman *Buka Kembali Kas*). Sistem **tidak** membuat shift baru di tanggal yang sama. Shift tanggal berikutnya terbuka otomatis saat hari berganti.

#### 5.6.2. Buku Kas Harian / BKU Kronologis (`/kasir/buku-kas`)
- **Untuk Apa Penggunaannya?**
  Menampilkan Buku Kas Umum (BKU) tradisional yang menyusun seluruh arus kas masuk dan keluar secara kronologis menit per menit sepanjang hari, dilengkapi saldo berjalan (*running balance*) di setiap baris transaksi.
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Buku Kas Harian**.
  2. Pilih tanggal yang ingin ditinjau.
  3. Telusuri kronologi transaksi untuk mendeteksi jam transaksi tertentu jika terjadi perbedaan perhitungan fisik kasir.

#### 5.6.3. Edit Inputan & Koreksi Transaksi / Pembatalan (Void) (`/kasir/utilitas/koreksi`)
- **Untuk Apa Penggunaannya?**
  Memperbaiki (*Edit*) atau membatalkan (*Void*) transaksi kasir yang salah input (contoh: salah ketik nominal bayar, salah nama pembayar, atau salah memilih metode transfer/tunai) pada shift yang masih terbuka, sebelum buku kas ditutup dan ditarik akuntansi. Transaksi yang sudah ditutup (*closed*) atau berstatus VOID **tidak dapat diedit**.
- **Cara Mengedit Inputan (disarankan jika hanya salah data):**
  1. Akses halaman edit melalui salah satu cara berikut:
     - Klik menu sidebar **Utilitas Kontrol > Edit & Koreksi Transaksi** (dengan lencana badge kuning **EDIT**).
     - Klik tombol cepat **"Edit Transaksi"** di topbar Dashboard Meja Kasir.
     - Klik tombol kuning **"Edit"** langsung pada kolom Aksi tabel mutasi transaksi (*Penjualan Koran Sisa*, *Penjualan Tunai*, dan *Buku Kas Harian*).
  2. Klik tombol kuning **"Edit KI"** (kas masuk) atau **"Edit KO"** (kas keluar) pada transaksi yang keliru.
  3. Modal popup formulir edit akan muncul. Ubah data yang salah: nama pihak pembayar/penerima, nominal (Rp), metode pembayaran (tunai/transfer), bank tujuan, no. referensi/cek, no. blanko, atau uraian.
  4. Isi **Alasan Koreksi** (wajib diisi untuk kepatuhan audit trail), lalu klik **"Simpan Perubahan"**.
  5. Sistem otomatis menyesuaikan selisih nominal pada saldo piutang faktur/agen, total KI/KO shift, dan mutasi kas secara *real-time*. Alasan koreksi, nama pengedit, dan waktu tersimpan di riwayat keterangan transaksi sebagai jejak audit permanen.
- **Cara Membatalkan (Void) jika transaksi harus dibatalkan seluruhnya:**
  1. Buka menu **Utilitas Kontrol > Edit & Koreksi Transaksi**.
  2. Temukan transaksi yang keliru pada daftar transaksi shift terbuka.
  3. Klik tombol merah **"Void"**.
  4. Kotak konfirmasi alasan akan muncul. Ketik alasan pembatalan secara jelas (misal: "Salah ketik nominal pelunasan iklan dari Rp 500.000 menjadi Rp 5.000.000").
  5. Klik **"Konfirmasi Void"**.
  6. Nilai uang transaksi otomatis dihapus dari saldo kasir, status faktur dikembalikan ke posisi belum bayar, dan riwayat void tersimpan secara permanen untuk jejak audit keamanan. Transaksi berstatus VOID tidak dapat diubah lagi.

#### 5.6.4. Cetak Ulang Kuitansi & Dokumen Sah (`/kasir/utilitas/cetak-ulang`)
- **Untuk Apa Penggunaannya?**
  Mencetak ulang lembaran kuitansi BKM atau voucher BKK yang sah jika lembar cetak sebelumnya hilang oleh pembayar, sobek, terkena tumpahan air, atau printer kasir mengalami macet kertas (*paper jam*).
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Cetak Ulang Kuitansi**.
  2. Cari dokumen menggunakan kolom pencarian (nomor KI/KO, nama pihak, uraian) atau **Pilih Tanggal** kalender untuk langsung menyaring dokumen pada tanggal transaksi tertentu tanpa perlu mengetik tanggal manual.
  3. Filter berdasarkan Jenis Dokumen jika diperlukan (Semua, Kas Masuk KI, atau Kas Keluar KO).
  4. Klik tombol printer **"Cetak Ulang"**. Sistem menampilkan kembali lembar cetak resmi dengan cap sah duplikat.

#### 5.6.5. Riwayat Cetak Dokumen Kasir (`/kasir/riwayat-cetak`)
- **Untuk Apa Penggunaannya?**
  Melihat daftar audit log seluruh dokumen kuitansi yang pernah dicetak oleh divisi kasir, siapa staf kasir yang mencetaknya, dan waktu pencetakan.
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Riwayat Cetak**.
  2. Gunakan kolom pencarian, **Pilih Tanggal** kalender, dan filter jenis dokumen (Semua, BKM, Kwitansi, BKK).
  3. Klik tombol **Detail Dokumen** untuk meninjau arsip digital bukti kas.

#### 5.6.6. Otorisasi Buka Kembali Kas (Re-open Shift) (`/kasir/utilitas/buka-kas`)
- **Untuk Apa Penggunaannya?**
  Membuka kembali shift kasir yang sudah ditutup, baik untuk **hari ini** (misal ada koreksi atau pelunasan susulan setelah closing pukul 16.00) maupun **tanggal sebelumnya** (misal 2 November). Tanpa persetujuan, tanggal yang sudah ditutup tidak bisa diinput apa pun. Pembukaan kembali wajib mendapat **Persetujuan Accounting / Manajer Keuangan** untuk mencegah kecurangan (*fraud*).
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Buka Kembali Kas** (atau saat kas sudah ditutup, sistem otomatis mengarahkan ke sini).
  2. Gunakan filter pencarian: ketik nama kasir/alasan, atau cukup klik **Pilih Tanggal** pada kolom kalender untuk mencari tanggal shift yang ingin dibuka tanpa perlu mengetik format `YYYY-MM-DD`.
  3. Temukan baris shift tanggal yang berstatus *Closed*, klik **"Ajukan Buka Kembali Kas"**.
  4. Isi alasan / berita acara (minimal 10 karakter), lalu kirim ke Accounting.
  5. Accounting menyetujui langsung di akunnya, atau mendampingi kasir memasukkan email & password Accounting/Manajer Keuangan.
  6. Setelah disetujui, shift kembali **Aktif (Open)** dan kasir bisa mengoreksi/menginput pada tanggal tersebut.
  7. Selesai koreksi, lakukan **Tutup Kas Harian** lagi. Tanggal itu terkunci kembali.
  8. **Hari berikutnya** tidak perlu persetujuan: shift tanggal baru terbuka otomatis saat hari berganti.

---

## BAB VI: Standard Operating Procedure (SOP) Alur Kerja Harian Kasir

Untuk menjaga ketertiban administrasi dan kepatuhan audit keuangan internal Harian Umum Kabar Priangan, setiap personel kasir wajib mengikuti jadwal alur kerja baku berikut:

```
┌───────────────────────────────────────────────────────────────────────────────────────┐
│                           RUTINITAS HARIAN DIVISI KASIR                               │
├─────────────────────┬─────────────────────────────────────────────────────────────────┤
│    WAKTU (WIB)      │ KEGIATAN & MENU SISTEM YANG DIGUNAKAN                           │
├─────────────────────┼─────────────────────────────────────────────────────────────────┤
│ 07.30 - 08.00       │ PERSIAPAN AWAL & MODAL LACI                                     │
│                     │ - Buka brankas, hitung modal uang receh/kembalian di laci kas. │
│                     │ - Login akun kasir@kabarpriangan.co.id.                         │
│                     │ - Buka [Dashboard Kasir] pastikan shift berstatus "Aktif".      │
├─────────────────────┼─────────────────────────────────────────────────────────────────┤
│ 08.00 - 11.30       │ PELAYANAN SETORAN AGEN SIRKULASI                                │
│                     │ - Terima loper & agen koran yang menyetor uang pagi.            │
│                     │ - Periksa bukti retur fisik BAPR dari ekspedisi gudang.         │
│                     │ - Input setoran di menu [Setoran Koran Agen Sirkulasi].         │
│                     │ - Serahkan kuitansi BKM bertanda tangan sah ke agen.            │
├─────────────────────┼─────────────────────────────────────────────────────────────────┤
│ 11.30 - 14.30       │ PELAYANAN IKLAN & REALISASI BUKTI PENGELUARAN (BP)              │
│                     │ - Terima pelunasan iklan (scan barcode faktur cetak).           │
│                     │ - Periksa opsi WAPU jika pemesan instansi/pemda.                │
│                     │ - Input pelunasan di menu [Setoran Iklan].                      │
│                     │ - Buka menu [Realisasi BP], cairkan uang belanja operasional    │
│                     │   yang sudah berstatus "Approved Keuangan".                     │
├─────────────────────┼─────────────────────────────────────────────────────────────────┤
│ 14.30 - 16.00       │ PENCAIRAN KOMISI KH/AE, KASBON, & SETORAN BANK                  │
│                     │ - Layani pencairan kasbon darurat via [Kasbon / Uang Muka].     │
│                     │ - Cairkan komisi wartawan/sales via [Komisi KH & Insentif AE].  │
│                     │ - Jika fisik laci melampaui batas aman (> Rp 15.000.000), setor │
│                     │   ke teller Bank BJB dan input di menu [Setoran Kas ke Bank].   │
│                     │ - JIKA SALAH INPUT: Gunakan menu [Edit & Koreksi Transaksi]     │
│                     │   atau tombol Edit pada tabel untuk memperbaiki sebelum closing.│
├─────────────────────┼─────────────────────────────────────────────────────────────────┤
│ 16.00 - 16.45       │ CASH OPNAME & CLOSING HARIAN                                    │
│                     │ - Periksa posisi uang di menu [Posisi Saldo Fisik].             │
│                     │ - Hitung seluruh lembar uang fisik di laci kasir.               │
│                     │ - Buka menu [Tutup Kas Harian (Closing Shift)], input per       │
│                     │   pecahan Rp 100k s/d koin, pastikan status "SEIMBANG/BALANCE". │
│                     │ - Klik tombol merah "Kunci Shift & Tutup Kas Harian".           │
│                     │ - PENGUNCIAN OTOMATIS: Tanggal hari ini terkunci dari inputan   │
│                     │   baru. Pembukaan kembali wajib via [Buka Kembali Kas] atas     │
│                     │   persetujuan resmi Accounting.                                 │
├─────────────────────┼─────────────────────────────────────────────────────────────────┤
│ 16.45 - 17.00       │ CETAK REKAPITULASI & SERAH TERIMA DOKUMEN                       │
│                     │ - Buka menu [Laporan Kas Harian], cetak lembar resmi 3 tanda    │
│                     │   tangan (Kasir, Accounting, Kabag Keuangan).                   │
│                     │ - Susun fisik uang, slip bank, dan nota BKM/BKK ke dalam map    │
│                     │   setoran harian, lalu serahkan ke Bagian Keuangan.             │
└─────────────────────┴─────────────────────────────────────────────────────────────────┘
```

---

*Dokumen ini diterbitkan resmi oleh Harian Umum Kabar Priangan — Divisi Operasional Kasir & Divisi Keuangan.*
