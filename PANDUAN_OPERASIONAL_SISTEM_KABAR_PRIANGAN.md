# Buku Panduan Resmi Operasional & Pengembangan Sistem
## Sistem Informasi Manajemen Terpadu — Harian Umum Kabar Priangan
*(Koran Cetak • Media Online • Priangan TV)*

**Versi:** 2026 Terkini — Revisi Modul Kasir Terpadu (Standarisasi Laporan Dinamis, Filter Periode 3-Mode, Rekonsiliasi Pecahan & Riwayat Closing)  
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
   Seluruh formulir pencarian dan filter arsip (Buka Kembali Kas, Cetak Ulang Kuitansi, Riwayat Cetak, Realisasi BP, Komisi, Kas Bank, Koran Sisa, dan Tutup Kas Harian) dilengkapi pemilih tanggal kalender (`<input type="date">`). Kasir tidak perlu mengetik format tanggal secara manual, cukup klik kalender dan pilih tanggal.
10. **Notifikasi & Dialog Modern:**
   Seluruh `alert()` / `confirm()` bawaan browser diganti dialog dan toast yang lebih ringkas. Aksi berisiko tinggi (Tutup Kas) memakai tema **merah**.
11. **Perapian Dropdown (Select):**
   Ikon panah dropdown kini punya ruang sendiri di sisi kanan sehingga tidak menimpa teks (contoh: filter *Per Minggu (7 Hari)*), jelas terbaca di mode terang maupun gelap.
12. **Standarisasi Kop Laporan, Periode Tunggal & Identitas Pelaksana Dinamis:**
   Kop cetakan & laporan Rekapitulasi serta Mutasi Bank menggunakan **`HU KABAR PRIANGAN`**, label bulan/periode tampil tunggal tanpa kata `BULAN` ganda, serta unit pelaksana menyesuaikan role akun login (**`Divisi Kasir`** untuk role `kasir` dan **`Manager Keuangan`** untuk role Administrator / Keuangan) baik di layar, cetakan, maupun Export Excel (`.xls`).
13. **Filter Per Tanggal, Per Bulan, dan Per Tahun pada 4 Menu Laporan Kasir:**
   Menu **Mutasi Kas Bank**, **Mutasi per COA**, **Posisi Saldo Kas Fisik**, dan **Laporan Kas Harian (LHK)** kini dilengkapi pemilih mode periode seragam (`Per Tanggal`, `Per Bulan`, `Per Tahun`).
14. **Penghapusan Kasbon di Modul Kasir & Penyederhanaan LHK:**
   Komponen dan tampilan Kasbon dihapus dari Dashboard Kasir, Buku Kas Harian, Rekapitulasi Kas, Posisi Saldo Kas Fisik, dan Tutup Kas Harian (sidebar kasir tidak lagi memuat menu Kasbon). Tabel **Laporan Kas Harian (LHK)** diringkas menjadi total keseluruhan `Kas Masuk (KI)` dan `Kas Keluar (KO)` beserta jumlah transaksi dan selisih bersih, karena rincian per kanal sudah tersaji di menu *Rekapitulasi Kas*.
15. **Rekonsiliasi Langsung Per Pecahan & Riwayat Tutup Kas Per Tanggal:**
   Panel **Rekonsiliasi Langsung** pada *Tutup Kas Harian* menghitung dan menampilkan rincian live setiap nominal pecahan (misal `Rp 100.000 × 10 lbr → Rp 1.000.000`), total lembar kertas, dan status keseimbangan (`BALANCE / SEIMBANG` vs `TIDAK BALANCE`). Saat kas ditutup, rincian lembar per pecahan tersimpan di database sehingga tabel riwayat dapat difilter per tanggal dan ditinjau kembali lewat tombol **Lihat Rincian** sekaligus dibandingkan dengan tanggal kas lainnya.
16. **Pembersihan Label Shift Tanpa Nomor Sesi (`#5`):**
   Header halaman *Tutup Kas Harian* cukup menampilkan tanggal kas berjalan, dan header *Edit & Koreksi Kasir* menampilkan **`Shift Aktif`** tanpa akhiran nomor sesi `#...`.

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
| **Staf Divisi Kasir (Pak Eki)** | `kasir@kabarpriangan.co.id` | `password` | **Kasir Utama:** Penerimaan (KI), Pengeluaran (KO), Laporan Kas, dan Tutup Shift. |
| **Kasir Operasional Keuangan** | `kasir.keuangan@kabarpriangan.co.id` | `password` | **Kasir Backup:** Menangani loket darurat & operasional kas. |
| **Staf Divisi Keuangan** | `keuangan@kabarpriangan.co.id` | `password` | **Approval Keuangan:** Validasi bukti pengeluaran dan manajemen kas keuangan. |
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
| **AE** | *Account Executive* | Staf marketing/sales iklan yang mencari order iklan dan menerima insentif. |
| **KH** | *Koresponden Harian* | Wartawan kontributor daerah yang berhak menerima komisi berita/advertorial. |

---

## BAB V: Panduan Lengkap Seluruh Menu Divisi Kasir (Sistem KAS)

Bagian ini memuat penjelasan mendalam mengenai **tujuan/fungsi setiap menu** dan **tata cara langkah demi langkah penggunaannya** khusus untuk personel **Divisi Kasir** (`kasir@kabarpriangan.co.id`). Seluruh penomoran dokumen kuitansi mengikuti standar historis DOS KAS.EXE (`KI-XX-YYYYMMXXXX` dan `KO-YYYYMMXXXX`) yang terintegrasi otomatis ke Buku Besar Akuntansi.

```
                    ┌──────────────────────────────────────────┐
                    │        STRUKTUR MENU DIVISI KASIR        │
                    └────────────────────┬─────────────────────┘
                                         │
           ┌─────────────────────────────┼─────────────────────────────┐
           ▼                             ▼                             ▼
    1. DASHBOARD KASIR            2. KAS MASUK (KI)             3. KAS KELUAR (KO)
    - Saldo Fisik Brankas         - Setoran Iklan               - Realisasi BP
    - Ringkasan KI & KO           - Setoran Agen                - Komisi KH & AE
    - Aktivitas Mutasi Terbaru    - Tunai & Koran Sisa (KI-14)  - Setoran Kas Bank (KO-09)
                                  - Penerimaan Kas Bank (KI-09)
                                  - Piutang Langganan
                                         │
                         ┌───────────────┴───────────────┐
                         ▼                               ▼
                 4. LAPORAN & MUTASI             5. UTILITAS KONTROL
                 - Rekapitulasi Kas (KI & KO)    - Tutup Kas Harian (Closing & Riwayat)
                 - Mutasi Kas Bank (Tgl/Bln/Thn) - Buku Kas Harian (BKU)
                 - Mutasi per COA (Tgl/Bln/Thn)  - Edit & Koreksi Transaksi
                 - Posisi Saldo Kas Fisik        - Cetak Ulang Kuitansi
                 - Laporan Kas Harian (LHK)      - Riwayat & Log Cetak
                                                 - Buka Kembali Kas (Re-open)
```

---

### 5.1. Dashboard Utama Kasir (`/kasir/dashboard`)

#### A. Untuk Apa Penggunaannya?
Sebagai pusat komando harian kasir untuk memantau kondisi keuangan kasir secara langsung (*real-time*). Menu ini menyajikan:
1. **Status Shift Kasir:** Memastikan apakah loket kasir saat ini berstatus **AKTIF (OPEN)** atau **DITUTUP (CLOSED)**.
2. **Saldo Fisik Brankas:** Menampilkan jumlah uang tunai fisik yang wajib ada di dalam laci kasir saat detik ini juga, dihitung secara otomatis dengan rumus:
   `Target Fisik Laci = Saldo Awal Shift + Total Kas Masuk Tunai - Total Kas Keluar Tunai`
3. **Widget Rekap Harian:** Saldo Fisik Brankas, Kas Masuk Hari Ini (`KI`), dan Kas Keluar Hari Ini (`KO`).
4. **Tabel Transaksi Terakhir:** Menampilkan 10 transaksi mutasi kas terakhir hari ini agar kasir dapat memverifikasi input secara instan tanpa berpindah halaman.
5. **Tombol Aksi Cepat (*Quick Action*):** Akses instan 1-klik menuju form setoran iklan, sirkulasi, realisasi BP, edit transaksi, dan penutupan shift.

#### B. Cara Menggunakannya:
1. Setelah login menggunakan akun kasir, sistem langsung mengarahkan Anda ke halaman Dashboard ini.
2. **Periksa Status Shift di Bagian Header:** Pastikan tampil status **"Shift Aktif"**. Jika shift hari berjalan belum dibuat, sistem otomatis menginisialisasi shift baru dengan saldo awal yang diambil dari saldo akhir fisik shift sebelumnya (atau modal awal default Rp 1.000.000).
3. **Periksa Angka "Saldo Fisik Brankas":** Cocokkan secara berkala dengan uang tunai di laci kasir.
4. Klik tombol pintasan **"+ Setoran Iklan"** atau **"+ Setoran Agen"** jika ada pelanggan/agen yang datang menyetor uang di loket kasir.
5. Jika ingin melihat rincian mutasi hari ini, gulir layar ke bagian tabel **"Aktivitas Mutasi Kas Terbaru"**.

---

### 5.2. Kelompok Menu Kas Masuk (KI) — Divisi Kasir & Keuangan

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

### 5.3. Kelompok Menu Kas Keluar (KO) — Divisi Kasir & Keuangan

Seluruh pengeluaran uang tunai laci wajib melalui pengeluaran resmi kas berstatus **Approved** (menu Kasbon di modul Kasir telah dihapus seluruhnya dan seluruh alur pengeluaran langsung dikonsolidasikan melalui KI/KO).

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

### 5.4. Kelompok Menu Laporan & Mutasi — Identitas Role Dinamis & Filter 3 Mode

Seluruh laporan dan cetakan modul Kasir memakai kop **`HU KABAR PRIANGAN`** serta label unit pelaksana yang menyesuaikan akun pengguna yang sedang login (**`Divisi Kasir`** apabila login sebagai role `kasir`, dan **`Manager Keuangan`** apabila login sebagai `administrator`, `admin`, `keuangan`, `admin_keuangan`, atau `kepala_divisi`) baik di tampilan layar, cetakan resmi, tanda tangan, maupun file Excel.

#### 5.4.1. Rekapitulasi Kas Masuk & Keluar (`/kasir/laporan/rekap`)
- **Untuk Apa Penggunaannya?**
  Menyajikan laporan rekapitulasi mutasi kas masuk (`KI`) dan kas keluar (`KO`) beserta rincian per COA dan per divisi, lengkap dengan fitur cetak (*Tab Rekap KAS DOS* maupun *Tab Rincian Transaksi*) dan **Export Excel (`.xls`)** yang mengikuti parameter filter periode aktif.
- **Standar Kop & Periode Tunggal:**
  Kop laporan menampilkan **`HU KABAR PRIANGAN`**, label periode tunggal tanpa kata `BULAN` ganda (misal `BULAN: NOVEMBER 2026`), dan penanda unit pelaksana dinamis (`SIKAPRI - DIVISI KASIR` atau `SIKAPRI - MANAGER KEUANGAN`).
- **Rangkuman Bawah Tabel & Export Excel:**
  Pada bagian bawah laporan dan file hasil **Export Excel**, disajikan tabel rangkuman yang memuat:
  1. **Akumulasi Subtotal Penerimaan per Divisi (`KI`):** Divisi Iklan, Divisi Sirkulasi, dan Bidang Keuangan / Lain-lain.
  2. **Akumulasi Subtotal Pengeluaran per Divisi (`KO`):** Divisi Iklan, Divisi Sirkulasi, Divisi Umum & SDM, Divisi Redaksi, dan Bidang Keuangan (termasuk Setor Bank).
  3. **Rekonsiliasi Saldo Akhir Kas Fisik dan Bank yang Seimbang (`BALANCE`):** Menampilkan Saldo Akhir Kas Fisik Brankas, Mutasi Setoran Bank, serta Total Saldo Kas & Bank yang Seimbang (*Balance*).

#### 5.4.2. Mutasi Kas Bank (`/kasir/laporan/mutasi-bank`)
- **Untuk Apa Penggunaannya?**
  Menampilkan buku mutasi khusus transaksi perbankan (**Bank BJB Giro `05.00.01.006270.9`** & **Bank BCA Giro `054-0388-991`**), meliputi penerimaan transfer klien, penarikan cek (`KI-09`), dan setoran kas ke bank (`KO-09`).
- **Filter Periode 3 Mode:** Mendukung filter berdasarkan rekening bank (`Semua`, `BJB`, `BCA`) serta pilihan **Per Tanggal**, **Per Bulan**, atau **Per Tahun**.

#### 5.4.3. Mutasi per COA / Akun Akuntansi (`/kasir/laporan/perkiraan`)
- **Untuk Apa Penggunaannya?**
  Menampilkan ringkasan mutasi kas yang telah dipetakan otomatis ke nomor akun Chart of Accounts (COA) resmi Harian Umum Kabar Priangan sebelum ditarik Bagian Akuntansi ke Buku Besar.
- **Filter Periode 3 Mode:** Mendukung filter **Per Tanggal**, **Per Bulan**, atau **Per Tahun**, serta kop cetak dinamis sesuai role pengguna.

#### 5.4.4. Posisi Saldo Kas Fisik & Bank (`/kasir/laporan/posisi-kas`)
- **Untuk Apa Penggunaannya?**
  Menampilkan lembar kerja rekonsiliasi kas fisik laci dengan perhitungan saldo sistem (`Saldo Awal + Total KI - Total KO` dibanding `Saldo Kas Fisik Tercatat`) tanpa pengurang kasbon, lengkap dengan kartu metrik KI, KO, dan mutasi bank.
- **Filter Periode 3 Mode:** Mendukung filter **Per Tanggal**, **Per Bulan**, atau **Per Tahun**, serta kop cetak dan tanda tangan dinamis sesuai role login.

#### 5.4.5. Laporan Kas Harian / LHK (`/kasir/laporan-harian`)
- **Untuk Apa Penggunaannya?**
  Dokumen pertanggungjawaban ringkas yang menampilkan:
  - **Bagian I — Rekonsiliasi Saldo Kas:** Saldo Awal, Total Keseluruhan Kas Masuk (`KI`), Total Keseluruhan Kas Keluar (`KO`), dan Saldo Akhir Kas Fisik.
  - **Bagian II — Ringkasan Total Keseluruhan Transaksi (`KI & KO`):** Menampilkan total keseluruhan nominal `Kas Masuk (KI)`, total keseluruhan nominal `Kas Keluar (KO)`, jumlah transaksi, serta `Selisih Mutasi Bersih (Total KI - Total KO)` secara singkat (rincian per kanal sudah tersedia pada menu *Rekapitulasi Kas*).
- **Filter Periode 3 Mode:** Mendukung filter **Per Tanggal**, **Per Bulan**, atau **Per Tahun**, serta tanda tangan & kop dinamis (`Divisi Kasir` / `Manager Keuangan`).

---

### 5.5. Kelompok Menu Utilitas & Kontrol Operasional

#### 5.5.1. Tutup Kas Harian (Closing Shift, Rekonsiliasi Pecahan & Riwayat Per Tanggal) (`/kasir/utilitas/closing`)
- **Untuk Apa Penggunaannya?**
  Dilakukan kasir pada penutupan hari kerja untuk mengunci transaksi hari tersebut melalui prosedur *cash opname* fisik lembaran uang kertas dan koin, mencocokkan rincian pecahan dengan saldo teoritis sistem, serta menyimpan arsip rincian lembar pecahan untuk ditinjau dan dibandingkan antar tanggal.
- **Cara Menggunakannya (Input Opname & Rekonsiliasi Langsung):**
  1. Buka menu **Utilitas Kontrol > Tutup Kas Harian** (header atas menampilkan tanggal kas berjalan).
  2. Keluarkan dan kelompokkan seluruh uang tunai fisik di laci kasir.
  3. Masukkan jumlah lembar/nilai pada kolom pecahan yang tersedia: **Rp 100.000**, **Rp 50.000**, **Rp 20.000**, **Rp 10.000**, **Rp 5.000**, **Rp 2.000**, **Rp 1.000**, dan **Total Koin Logam**.
  4. **Pantau Panel "Rekonsiliasi Langsung" di Sisi Kanan Secara Real-Time:**
     - Panel **Rincian Pecahan Terhitung** menampilkan otomatis setiap nominal yang diisi beserta hasil perkalian dan total lembar (contoh: `Rp 100.000 × 10 lbr → Rp 1.000.000`, `Rp 50.000 × 4 lbr → Rp 200.000`, dst.).
     - **Total Fisik Terhitung** dibandingkan langsung terhadap **Saldo Teoritis Sistem**:
       - **`BALANCE / SEIMBANG (TIDAK ADA SELISIH)`**: Selisih Rp 0 — fisik laci tepat sesuai saldo sistem.
       - **`TIDAK BALANCE (+ Rp ... LEBIH FISIK)`** atau **`TIDAK BALANCE (- Rp ... KURANG FISIK)`**: Terjadi selisih fisik laci.
  5. Isi **Catatan Serah Terima / Keterangan Tutup Kas** bila perlu.
  6. Klik tombol merah **"Kunci & Tutup Kas Harian"**, lalu konfirmasi pada dialog yang muncul.
  7. Shift terkunci (*Closed*), rincian jumlah lembar per pecahan tersimpan permanen pada database (`rincian_pecahan`), dan seluruh transaksi hari tersebut dikunci.
  8. **Aturan penguncian:** setelah closing, tanggal tersebut **tidak bisa diinput lagi** (menu KI, KO, Koreksi otomatis diarahkan ke halaman *Buka Kembali Kas*). Shift tanggal berikutnya terbuka otomatis saat hari berganti.
- **Cara Melihat Riwayat & Membandingkan Pecahan Antar Tanggal:**
  1. Gulir ke tabel **Riwayat Tutup Kas & Arsip Rincian Pecahan** di bagian bawah halaman.
  2. Gunakan **Filter Tanggal** (pilih tanggal pada input kalender lalu klik **Tampilkan Tanggal**) untuk menyaring riwayat pada tanggal tertentu (misal `15 November`), atau klik **Semua Tanggal** untuk mereset filter.
  3. Klik tombol **"Lihat Rincian"** pada kolom **Detail Kas** untuk membuka jendela rincian lengkap sesi tanggal tersebut: ringkasan Saldo Awal, Total KI, Total KO, Saldo Teoritis, Saldo Fisik, Selisih, catatan serah terima, serta tabel jumlah lembar dan subtotal seluruh pecahan (`Rp 100.000` hingga `Koin Logam`).
  4. Gunakan pilihan **"Bandingkan dengan Tanggal Kas Lain"** di dalam modal rincian untuk melihat perbandingan jumlah lembar dan selisih nominal per pecahan antara tanggal terpilih dan hari berikutnya/sebelumnya secara berdampingan.

#### 5.5.2. Buku Kas Harian / BKU Kronologis (`/kasir/buku-kas`)
- **Untuk Apa Penggunaannya?**
  Menampilkan Buku Kas Umum (BKU) yang menyusun seluruh arus kas masuk dan keluar secara kronologis sepanjang shift, dilengkapi ringkasan Saldo Awal Shift, Total Kas Masuk, Total Kas Keluar, dan Target Kas Fisik bersih di laci.
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Buku Kas Harian**.
  2. Telusuri kronologi transaksi untuk memverifikasi alur mutasi kas sepanjang hari.

#### 5.5.3. Edit Inputan & Koreksi Transaksi / Pembatalan (Void) (`/kasir/utilitas/koreksi`)
- **Untuk Apa Penggunaannya?**
  Memperbaiki (*Edit*) atau membatalkan (*Void*) transaksi kasir yang salah input (contoh: salah ketik nominal bayar, salah nama pembayar, atau salah memilih metode transfer/tunai) pada shift aktif (**Shift Aktif**), sebelum buku kas ditutup dan ditarik akuntansi. Transaksi yang sudah ditutup (*closed*) atau berstatus VOID **tidak dapat diedit**.
- **Cara Mengedit Inputan (disarankan jika hanya salah data):**
  1. Akses halaman edit melalui salah satu cara berikut:
     - Klik menu sidebar **Utilitas Kontrol > Edit & Koreksi Transaksi**.
     - Klik tombol cepat **"Edit Transaksi"** di topbar Dashboard Meja Kasir.
     - Klik tombol **"Edit"** langsung pada kolom Aksi tabel mutasi transaksi (*Penjualan Koran Sisa*, *Penjualan Tunai*, dan *Buku Kas Harian*).
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

#### 5.5.4. Cetak Ulang Kuitansi & Dokumen Sah (`/kasir/utilitas/cetak-ulang`)
- **Untuk Apa Penggunaannya?**
  Mencetak ulang lembaran kuitansi BKM atau voucher BKK yang sah jika lembar cetak sebelumnya hilang oleh pembayar, sobek, terkena tumpahan air, atau printer kasir mengalami macet kertas (*paper jam*).
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Cetak Ulang Kuitansi**.
  2. Cari dokumen menggunakan kolom pencarian (nomor KI/KO, nama pihak, uraian) atau **Pilih Tanggal** kalender untuk langsung menyaring dokumen pada tanggal transaksi tertentu tanpa perlu mengetik tanggal manual.
  3. Filter berdasarkan Jenis Dokumen jika diperlukan (Semua, Kas Masuk KI, atau Kas Keluar KO).
  4. Klik tombol printer **"Cetak Ulang"**. Sistem menampilkan kembali lembar cetak resmi dengan cap sah duplikat.

#### 5.5.5. Riwayat Cetak Dokumen Kasir (`/kasir/riwayat-cetak`)
- **Untuk Apa Penggunaannya?**
  Melihat daftar audit log seluruh dokumen kuitansi yang pernah dicetak oleh divisi kasir, siapa staf kasir yang mencetaknya, dan waktu pencetakan.
- **Cara Menggunakannya:**
  1. Buka menu **Utilitas Kontrol > Riwayat Cetak**.
  2. Gunakan kolom pencarian, **Pilih Tanggal** kalender, dan filter jenis dokumen (Semua, BKM, Kwitansi, BKK).
  3. Klik tombol **Detail Dokumen** untuk meninjau arsip digital bukti kas.

#### 5.5.6. Otorisasi Buka Kembali Kas (Re-open Shift) (`/kasir/utilitas/buka-kas`)
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
│ 14.30 - 16.00       │ PENCAIRAN KOMISI KH/AE & SETORAN BANK                           │
│                     │ - Cairkan komisi wartawan/sales via [Komisi KH & Insentif AE].  │
│                     │ - Jika fisik laci melampaui batas aman (> Rp 15.000.000), setor │
│                     │   ke teller Bank BJB/BCA dan input di menu [Setoran Kas Bank].  │
│                     │ - JIKA SALAH INPUT: Gunakan menu [Edit & Koreksi Transaksi]     │
│                     │   atau tombol Edit pada tabel untuk memperbaiki sebelum closing.│
├─────────────────────┼─────────────────────────────────────────────────────────────────┤
│ 16.00 - 16.45       │ CASH OPNAME & CLOSING HARIAN                                    │
│                     │ - Periksa posisi uang di menu [Posisi Saldo Kas Fisik].         │
│                     │ - Hitung seluruh lembar uang fisik di laci kasir.               │
│                     │ - Buka menu [Tutup Kas Harian (Closing Shift)], input jumlah    │
│                     │   lembar per pecahan Rp 100k s/d koin, periksa rincian di       │
│                     │   [Rekonsiliasi Langsung] hingga status "BALANCE / SEIMBANG".   │
│                     │ - Klik tombol merah "Kunci & Tutup Kas Harian". Rincian pecahan │
│                     │   otomatis tersimpan ke arsip riwayat (bisa dilihat kembali    │
│                     │   via Filter Tanggal & tombol [Lihat Rincian]).                 │
│                     │ - PENGUNCIAN OTOMATIS: Tanggal hari ini terkunci dari inputan   │
│                     │   baru. Pembukaan kembali wajib via [Buka Kembali Kas] atas     │
│                     │   persetujuan resmi Accounting.                                 │
├─────────────────────┼─────────────────────────────────────────────────────────────────┤
│ 16.45 - 17.00       │ CETAK REKAPITULASI & SERAH TERIMA DOKUMEN                       │
│                     │ - Buka menu [Laporan Kas Harian] (ringkasan total KI/KO) atau   │
│                     │   [Rekapitulasi Kas] (detail per pos), cetak dokumen resmi      │
│                     │   sesuai identitas unit login (Divisi Kasir / Manager Keuangan).│
│                     │ - Susun fisik uang, slip bank, dan nota BKM/BKK ke dalam map    │
│                     │   setoran harian, lalu serahkan ke Bagian Keuangan.             │
└─────────────────────┴─────────────────────────────────────────────────────────────────┘
```

---

*Dokumen ini diterbitkan resmi oleh Harian Umum Kabar Priangan — Divisi Kasir & Manager Keuangan.*
