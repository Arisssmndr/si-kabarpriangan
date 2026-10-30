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
   Disediakan file `serve.bat` dan `dev.bat` yang mendeteksi otomatis *environment* Laragon.

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
                - Mutasi per Akun COA                 - Koreksi Transaksi (Void)
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

### 5.2. Kelompok Menu Kas Masuk (KI)

Seluruh uang yang diterima kasir (baik uang tunai laci maupun transfer ke bank perusahaan) diinput melalui submenu Kas Masuk (KI) agar menghasilkan nomor Bukti Kas Masuk (BKM) resmi.

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
  9. Sistem menerbitkan nomor kuitansi resmi `KI-01-YYYYMMXXXX` dan otomatis membuka jendela pratinjau cetak BKM Iklan berstandar rapi (*clean print*).

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
  8. Klik tombol **"Simpan Setoran Agen"**. Sistem menerbitkan kuitansi BKM Sirkulasi (`KI-02-YYYYMMXXXX`) dan mengurangi piutang sirkulasi agen yang bersangkutan.

#### 5.2.3. Input Koran Sisa & Eceran Loket (`/kasir/penerimaan/koran-sisa`)
- **Untuk Apa Penggunaannya?**
  Melayani transaksi tunai di loket kasir untuk penjualan koran retur edisi lama per kilogram (*afval* percetakan) kepada pengepul kertas daur ulang, serta penjualan eceran koran edisi hari ini langsung kepada pembaca umum yang membeli di kantor redaksi.
- **Cara Menggunakannya:**
  1. Buka menu **Kas Masuk (KI) > Input Koran Sisa & Eceran**.
  2. Ketik nama pembeli/pengepul pada kolom **Nama Pembeli / Rekanan** (misal: "Pengepul CV Berkah Kertas").
  3. Pilih **Jenis Satuan**:
     - `Kilogram (Kg)` untuk limbah sisa kertas retur gudang/percetakan.
     - `Eksemplar (Eks)` untuk koran edisi eceran loket.
  4. Masukkan **Jumlah Satuan** (misal: `45` kg) dan **Harga per Satuan** (misal: `Rp 3.500` per kg). Total tagihan dihitung otomatis oleh kalkulator sistem.
  5. Pilih metode pembayaran (`Tunai` atau `Transfer`).
  6. Klik tombol **"Simpan Penerimaan"**. Sistem mencatat kuitansi resmi `KI-14-YYYYMMXXXX` dengan akun COA `7.00.02.01.02` (Pendapatan Lain-lain Penjualan Sisa Cetak) dan menambahkan uang fisik ke laci kasir.

#### 5.2.4. Input Kas Bank / Jasa Giro (`/kasir/penerimaan/bank`)
- **Untuk Apa Penggunaannya?**
  Mencatat penambahan uang tunai fisik ke dalam laci kasir yang bersumber dari penarikan tunai rekening giro bank korporasi (Bank BJB / Bank BCA) menggunakan warkat Cek Tunai atau Bilyet Giro (*Cash Replenishment / Pengisian Saldo Kas Kecil*).
- **Cara Menggunakannya:**
  1. Buka menu **Kas Masuk (KI) > Input Bank / Jasa Giro**.
  2. Pilih **Bank Sumber**: `Bank BJB` (Giro Penerbitan) atau `Bank BCA`.
  3. Masukkan nomor warkat cek pada kolom **No. Cek / Warkat Giro** (misal: `CK-BJB-882910`).
  4. Masukkan jumlah uang tunai yang dicairkan pada kolom **Nominal Pencairan Cek**.
  5. Masukkan nama kasir/staf yang ditugaskan mengambil uang tunai dari teller bank pada kolom **Nama Penarik**.
  6. Tambahkan nomor blanko kasir dan catatan keperluan penarikan.
  7. Klik tombol **"Simpan Penerimaan Bank"**. Uang modal fisik di laci kasir otomatis bertambah dan tercatat kuitansi bank `KI-09-YYYYMMXXXX`.

#### 5.2.5. Input Piutang Langganan Koran (`/kasir/penerimaan/langganan`)
- **Untuk Apa Penggunaannya?**
  Mencatat pembayaran langganan koran harian korporasi, dinas pemerintah, sekolah, hotel, dan instansi swasta yang dibayarkan bulanan berdasarkan penagihan kolektor atau Surat Pesanan/SPK.
- **Cara Menggunakannya:**
  1. Buka menu **Kas Masuk (KI) > Input Piutang Langganan**.
  2. Masukkan **Nomor ID Pelanggan** dan **Nama Instansi / Kantor Pemesan**.
  3. Masukkan **Nomor SPK / PO / Kontrak Langganan** jika pemesan adalah dinas instansi.
  4. Masukkan jumlah uang yang dibayarkan pada kolom **Nominal Bayar**.
  5. Pilih metode pembayaran (`Tunai` atau `Transfer Bank BJB`).
  6. Klik **"Simpan Penerimaan Langganan"** untuk mencetak Bukti Kas Masuk langganan koran.

---

### 5.3. Kelompok Menu Kas Keluar (KO)

Seluruh pengeluaran uang tunai laci untuk membiayai operasional kantor wajib dicatat pada kelompok menu ini agar terbit Bukti Kas Keluar (BKK) bernomor `KO-YYYYMMXXXX`.

#### 5.3.1. Realisasi BP / Bukti Pengeluaran Kas Kecil (`/kasir/pengeluaran/realisasi-bp`)
- **Untuk Apa Penggunaannya?**
  Mencairkan dana belanja operasional divisi kantor (Redaksi liputan, Sirkulasi armada BBM, SDM/Umum, Iklan promosi, Listrik/PDAM/Internet, ATK) yang telah diverifikasi dan **disetujui (Approved)** oleh Bagian Anggaran dan Keuangan. Kasir dilarang mengeluarkan uang jika dokumen BP belum disetujui.
- **Cara Menggunakannya:**
  1. Buka menu **Kas Keluar (KO) > Realisasi BP**.
  2. Sistem menampilkan daftar Bukti Pengeluaran yang berstatus hijau **"Approved Keuangan"**.
  3. Cari nomor BP atau nama pemohon, kemudian klik tombol **"Cairkan Uang"**.
  4. Kotak dialog verifikasi pencairan akan muncul dengan menampilkan nominal anggaran yang disetujui (*Nominal ACC*).
  5. Ketik nama staf divisi yang datang mengambil uang di loket kasir pada kolom **Penerima Uang Fisik**.
  6. Pilih metode pencairan: `Kas Fisik (Laci Kasir)` atau `Transfer Bank BJB Operasional`.
  7. Masukkan catatan tambahan bila ada (misal: "Diserahkan pecahan 100 ribuan 5 lembar").
  8. Klik tombol **"Cairkan Kas Keluar"**.
  9. Sistem menerbitkan voucher Bukti Kas Keluar (`KO-03-YYYYMMXXXX` s/d `KO-06-YYYYMMXXXX`), status BP berubah menjadi `dicairkan_kasir`, dan saldo uang fisik kasir berkurang secara otomatis.

#### 5.3.2. Pencairan Komisi KH (20%) & Insentif AE (10%) (`/kasir/pengeluaran/komisi`)
- **Untuk Apa Penggunaannya?**
  Membayarkan hak komisi wartawan kontributor liputan daerah/Koresponden Harian (KH) sebesar 20% atas berita advertorial mitra humas pemda, atau insentif komisi sales marketing/Account Executive (AE) sebesar 10% atas perolehan order iklan. **Aturan baku sistem:** Komisi hanya dapat dicairkan jika faktur iklan yang bersangkutan telah berstatus **LUNAS (CBP/Lunas Piutang)**.
- **Cara Menggunakannya:**
  1. Buka menu **Kas Keluar (KO) > Komisi KH & Insentif AE**.
  2. Sistem otomatis memfilter dan menampilkan daftar faktur iklan lunas yang komisinya masih belum dicairkan (*unpaid commission*).
  3. Cari nomor faktur atau nama wartawan/sales.
  4. Klik tombol:
     - **"Cairkan KH (20%)"** untuk honor wartawan/kontributor liputan.
     - **"Cairkan AE (10%)"** untuk insentif sales marketing.
  5. Verifikasi nominal komisi terhitung (sistem menghitung 20% atau 10% dari nilai DPP iklan).
  6. Masukkan nama penerima yang menandatangani tanda terima dan pilih metode pembayaran (`Tunai` atau `Transfer`).
  7. Klik **"Proses Pencairan Komisi"**. Sistem mencetak voucher BKK komisi dan mengunci faktur tersebut agar tidak bisa dicairkan dua kali (*anti-double payment*).

#### 5.3.3. Setoran Kas ke Bank (`/kasir/setoran-bank`)
- **Untuk Apa Penggunaannya?**
  Mencatat penyetoran fisik uang tunai yang menumpuk di laci kasir (misal hasil setoran loper koran subuh dan setoran iklan siang hari) ke rekening bank operasional perusahaan (Bank BJB / Bank BCA) melalui teller bank untuk menjaga keamanan batas maksimal uang di kantor.
- **Cara Menggunakannya:**
  1. Buka menu **Kas Keluar (KO) > Setoran Kas ke Bank**.
  2. Pilih rekening bank korporasi tujuan pada dropdown **Bank Rekening Tujuan** (misal: `Bank BJB - Rekening Giro Operasional`).
  3. Masukkan jumlah uang tunai yang dibawa ke bank pada kolom **Nominal Setor Kas**.
  4. Masukkan **Nomor Slip Setoran Bank** yang dicap dan ditandatangani oleh teller bank.
  5. Ketik nama staf pembawa uang pada kolom keterangan.
  6. Klik tombol **"Simpan Setoran Kas ke Bank"**. Uang fisik kasir berkurang dan mutasi bank korporasi bertambah sebesar nilai yang disetor.

---

### 5.4. Modul Kasbon / Uang Muka Operasional (`/kasir/pengeluaran/kasbon`)

#### A. Untuk Apa Penggunaannya?
Mengakomodasi kebutuhan belanja operasional mendadak dan darurat dari berbagai divisi kantor yang belum sempat menerbitkan dokumen BP resmi (contoh: pembelian air mineral galon kantor, token listrik darurat malam hari, BBM dadakan liputan kecelakaan, servis printer rusak). Uang muka ini memotong kas fisik kasir sementara waktu, dan wajib diselesaikan dengan nota bon asli.

#### B. Cara Menggunakannya:

```
           ALUR OPERASIONAL KASBON / UANG MUKA
           
  [Staf Mengajukan Kasbon] ──▶ [Kasir Input Form Kasbon] ──▶ [Uang Diserahkan & Kas Fisik Berkurang]
                                                                        │
                                                                        ▼
  [Kasir Klik "Selesaikan Kasbon"] ◀── [Staf Menyerahkan Nota Asli & Uang Sisa Belanja]
```

1. **Pencatatan Kasbon Baru:**
   - Masuk ke menu **Kasbon / Uang Muka**.
   - Pada form sebelah kiri, masukkan:
     - **Nama Penerima Uang:** Staf yang meminjam/membawa uang belanja.
     - **Divisi:** Divisi pemohon (misal: Umum, Redaksi, Sirkulasi).
     - **Nominal Kasbon:** Jumlah rupiah yang diserahkan (minimal Rp 1.000).
     - **Keperluan Belanja:** Alasan jelas penggunaan uang.
     - **Status Otorisasi (ACC):**
       - Pilih `Langsung ACC (Operasional Kasir)` untuk kasbon kecil belanja harian laci.
       - Pilih `Menunggu ACC Keuangan` untuk nominal di atas plafon yang memerlukan persetujuan Manajer Keuangan.
   - Klik tombol **"Simpan Pengajuan Kasbon"**.
   - Saldo fisik kasir otomatis berkurang dan tercatat pada kartu *"Kasbon Gantung"*.

2. **Penyelesaian Kasbon (*Settlement*):**
   - Setelah staf selesai berbelanja dan menyerahkan kuitansi/nota bon toko asli serta uang kembalian (jika ada), buka kembali menu **Kasbon / Uang Muka**.
   - Pada tabel kasbon aktif (status *pending/menunggu_acc*), cari nama staf bersangkutan.
   - Klik tombol hijau **"Selesaikan Kasbon"**.
   - Masukkan nomor nota bukti belanja dan nominal riil yang terpakai.
   - Status kasbon berubah menjadi `lunas`, dan selisih uang kembalian otomatis kembali menambah fisik laci kasir.

---

### 5.5. Kelompok Menu Laporan & Rekonsiliasi

#### 5.5.1. Rekapitulasi Kas Masuk & Keluar (`/kasir/laporan/rekap`)
- **Untuk Apa Penggunaannya?**
  Melihat rekapitulasi harian seluruh transaksi kasir. Halaman ini menyediakan dua format tampilan:
  1. **Tab Klasik DOS KAS.EXE:** Menampilkan rekap mutasi berbasis kode bagian (Bagian 01 Iklan, 02 Sirkulasi, 03 Umum/SDM, 04 Akuntansi, 05 Keuangan, 06 Redaksi, 09 Bank, 14 Koran Sisa).
  2. **Tab Tabel Modern Interaktif:** Tabel dengan filter pencarian per divisi, rentang tanggal, dan pengelompokan akun.
  Dilengkapi fitur **Export CSV (Excel)** dan **Cetak Lembar Rekap Resmi**.
- **Cara Menggunakannya:**
  1. Buka menu **Laporan & Mutasi > Rekapitulasi Kas Masuk & Keluar**.
  2. Pilih tanggal yang ingin diperiksa (default: hari ini).
  3. Klik tab yang ingin digunakan (Klasik atau Modern).
  4. Klik tombol hijau **"Export CSV / Excel"** untuk mengunduh rekapitulasi ke spreadsheet.
  5. Klik tombol biru **"Cetak Lembar Rekapitulasi"** untuk mencetak dokumen fisik rangkap dua (satu untuk arsip kasir, satu diserahkan ke Bagian Akuntansi).

#### 5.5.2. Mutasi Kas Bank (`/kasir/laporan/mutasi-bank`)
- **Untuk Apa Penggunaannya?**
  Menampilkan buku mutasi khusus untuk transaksi perbankan (seluruh uang yang masuk via transfer Bank BJB/BCA, penerimaan cek giro, dan setoran kas ke bank). Sangat krusial untuk mencocokkan mutasi kasir dengan rekening koran perbankan (*bank reconciliation*).
- **Cara Menggunakannya:**
  1. Buka menu **Laporan & Mutasi > Mutasi Kas Bank**.
  2. Pilih filter bank yang diinginkan (`Semua Bank`, `Bank BJB`, atau `Bank BCA`).
  3. Periksa nomor referensi transfer dan jam masuk transaksi untuk memastikan tidak ada klaim pembayaran fiktif dari klien iklan/agen.

#### 5.5.3. Mutasi per COA / Akun Akuntansi (`/kasir/laporan/perkiraan`)
- **Untuk Apa Penggunaannya?**
  Menampilkan jurnal mutasi kas yang telah dipetakan otomatis ke dalam nomor akun Chart of Accounts (COA) resmi Harian Umum Kabar Priangan. Digunakan sebelum Bagian Akuntansi melakukan penarikan data (*pull journal*) ke Buku Besar.
- **Cara Menggunakannya:**
  1. Buka menu **Laporan & Mutasi > Mutasi per COA**.
  2. Filter berdasarkan kelompok akun atau tanggal shift.
  3. Periksa keseimbangan total debet dan kredit per akun perkiraan.

#### 5.5.4. Posisi Saldo Fisik Laci Kasir (`/kasir/laporan/posisi-kas`)
- **Untuk Apa Penggunaannya?**
  Menampilkan lembar kerja posisi kas riil detik ini. Lembar ini memecah komponen saldo awal modal, penerimaan tunai, pengeluaran tunai, dan potongan uang muka kasbon yang masih menggantung. Menjadi rujukan utama kasir sebelum melakukan closing fisik di sore hari.
- **Cara Menggunakannya:**
  1. Buka menu **Laporan & Mutasi > Posisi Saldo Fisik**.
  2. Perhatikan baris paling bawah: **"Saldo Bersih Fisik Laci Saat Ini"**. Angka inilah yang wajib cocok dengan lembaran uang kertas di laci kasir Anda.

#### 5.5.5. Monitoring Kasbon / Uang Muka Gantung (`/kasir/laporan/kasbon`)
- **Untuk Apa Penggunaannya?**
  Memantau seluruh uang muka kerja operasional yang telah dikeluarkan kasir namun belum diserahkan bukti nota belanjanya oleh staf/wartawan pemohon.
- **Cara Menggunakannya:**
  1. Buka menu **Laporan & Mutasi > Monitoring Uang Muka / Kasbon**.
  2. Lihat daftar staf pemegang kasbon yang melewati batas waktu pertanggungjawaban (lebih dari 1x24 jam).
  3. Lakukan konfirmasi atau cetak surat pemberitahuan nota bon kepada divisi terkait.

#### 5.5.6. Laporan Kas Harian 3 Tanda Tangan (`/kasir/laporan-harian`)
- **Untuk Apa Penggunaannya?**
  Dokumen pertanggungjawaban yuridis resmi kasir di penghujung hari operasional. Berisi ringkasan saldo awal, seluruh perincian mutasi masuk dan keluar, posisi saldo akhir, serta menyediakan **3 kolom tanda tangan pejabat berwenang**:
  1. **Disusun Oleh:** Staf Kasir Pelaksana (Pak Eki).
  2. **Diperiksa Oleh:** Bagian Pembukuan / Accounting.
  3. **Disetujui Oleh:** Kepala Bagian Keuangan / Manajer Keuangan (Ibu Eli).
- **Cara Menggunakannya:**
  1. Buka menu **Laporan & Mutasi > Laporan Kas Harian**.
  2. Pastikan transaksi hari ini sudah selesai diinput seluruhnya.
  3. Klik tombol **"Cetak Laporan Kas Harian Resmi"**.
  4. Cetak dokumen ke kertas format A4/Folio, tanda tangani pada kolom kasir, lalu bawa bersama fisik uang setoran dan map bukti transaksi ke Bagian Keuangan.

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
  7. Klik tombol merah **"Tutup Shift Kasir Hari Ini"**.
  8. Shift terkunci (*Closed*), transaksi kasir hari ini selesai dan siap ditarik oleh akuntansi.

#### 5.6.2. Buku Kas Harian / BKU Kronologis (`/kasir/buku-kas`)
- **Untuk Apa Penggunaannya?**
  Menampilkan Buku Kas Umum (BKU) tradisional yang menyusun seluruh arus kas masuk dan keluar secara kronologis menit per menit sepanjang hari, dilengkapi saldo berjalan (*running balance*) di setiap baris transaksi.
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Buku Kas Harian**.
  2. Pilih tanggal yang ingin ditinjau.
  3. Telusuri kronologi transaksi untuk mendeteksi jam transaksi tertentu jika terjadi perbedaan perhitungan fisik kasir.

#### 5.6.3. Koreksi Transaksi / Pembatalan (Void) (`/kasir/utilitas/koreksi`)
- **Untuk Apa Penggunaannya?**
  Membatalkan (*Void*) transaksi kasir yang salah input (contoh: salah ketik nominal bayar, salah memilih nama agen, atau salah klik metode transfer menjadi tunai) pada hari shift yang masih aktif sebelum buku kas ditutup dan ditarik akuntansi.
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Koreksi Transaksi**.
  2. Temukan transaksi yang keliru pada daftar transaksi hari ini.
  3. Klik tombol merah **"Void / Batalkan Transaksi"**.
  4. Kotak konfirmasi alasan akan muncul. Ketik alasan pembatalan secara jelas (misal: "Salah ketik nominal pelunasan iklan dari Rp 500.000 menjadi Rp 5.000.000").
  5. Klik **"Konfirmasi Void"**.
  6. Nilai uang transaksi otomatis dihapus dari saldo kasir, status faktur dikembalikan ke posisi belum bayar, dan riwayat void tersimpan secara permanen untuk jejak audit keamanan.

#### 5.6.4. Cetak Ulang Kuitansi & Dokumen Sah (`/kasir/utilitas/cetak-ulang`)
- **Untuk Apa Penggunaannya?**
  Mencetak ulang lembaran kuitansi BKM atau voucher BKK yang sah jika lembar cetak sebelumnya hilang oleh pembayar, sobek, terkena tumpahan air, atau printer kasir mengalami macet kertas (*paper jam*).
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Cetak Ulang Kuitansi**.
  2. Cari nomor kuitansi (misal: `KI-01-2026100001` atau `KO-03-2026100002`) atau cari berdasarkan nama pelanggan.
  3. Klik tombol printer **"Cetak Ulang"**. Sistem menampilkan kembali lembar cetak resmi dengan cap sah duplikat.

#### 5.6.5. Riwayat Cetak Dokumen Kasir (`/kasir/riwayat-cetak`)
- **Untuk Apa Penggunaannya?**
  Melihat daftar audit log seluruh dokumen kuitansi yang pernah dicetak oleh divisi kasir, siapa staf kasir yang mencetaknya, dan waktu pencetakan.
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Riwayat Cetak**.
  2. Gunakan filter jenis dokumen (BKM Iklan, BKM Sirkulasi, BKM Kas Bank, BKK Pengeluaran BP).
  3. Klik tombol **Detail Dokumen** untuk meninjau arsip digital bukti kas.

#### 5.6.6. Otorisasi Buka Kembali Kas (Re-open Shift) (`/kasir/utilitas/buka-kas`)
- **Untuk Apa Penggunaannya?**
  Membuka kembali shift kasir yang telah ditutup sore hari jika terjadi transaksi darurat di malam hari sebelum koran cetak (misal ada pelunasan iklan dinas mendadak pukul 20.00 WIB dengan syarat wajib cetak Bukti Kas Masuk malam itu juga). Pembukaan kembali ini memerlukan persetujuan (*approval*) dari Manajer Keuangan untuk mencegah kecurangan (*fraud*).
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Buka Kembali Kas**.
  2. Temukan baris shift hari ini yang sudah berstatus *Closed*.
  3. Klik tombol **"Ajukan Buka Kembali Kas"**.
  4. Masukkan alasan darurat mengapa shift perlu dibuka kembali.
  5. Manajer Keuangan (Ibu Eli) dapat menyetujui permohonan tersebut secara langsung di akun Keuangan atau kasir memasukkan PIN otorisasi supervisor.
  6. Setelah disetujui, status shift kembali menjadi **Aktif (Open)** dan kasir dapat menginput transaksi tambahan malam hari, kemudian melakukan closing ulang setelah selesai.

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
├─────────────────────┼─────────────────────────────────────────────────────────────────┤
│ 16.00 - 16.45       │ CASH OPNAME & CLOSING HARIAN                                    │
│                     │ - Periksa posisi uang di menu [Posisi Saldo Fisik].             │
│                     │ - Hitung seluruh lembar uang fisik di laci kasir.               │
│                     │ - Buka menu [Tutup Kas Harian (Closing Shift)], input per       │
│                     │   pecahan Rp 100k s/d koin, pastikan status "SEIMBANG/BALANCE". │
│                     │ - Klik tombol "Tutup Shift Kasir Hari Ini".                     │
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
