# PANDUAN PENGGUNAAN & ALUR SISTEM DIVISI SIRKULASI
## SI-KP: Sistem Informasi Manajemen Harian Umum Kabar Priangan
*(Modernisasi Sistem Distribusi Koran Berbasis Standar DOS SIRK.EXE)*

---

## 1. PENDAHULUAN & KONSEP ALUR SISTEM SIRKULASI

### 1.1. Peran Divisi Sirkulasi
Divisi Sirkulasi adalah divisi operasional yang bertanggung jawab atas pengelolaan distribusi fisik surat kabar harian *Kabar Priangan*, mulai dari alokasi oplah cetak koran, pengiriman ke jaringan agen, pencatatan koran retur, hingga penagihan faktur penjualan koran yang terintegrasi langsung dengan kasir.

Sistem ini memodernisasi aplikasi legacy **DOS SIRK.EXE** (era 1990-an) ke dalam platform web modern (Laravel 11, Tailwind CSS, Alpine.js) dengan tetap mempertahankan istilah, logika bisnis, dan kebiasaan kerja tim sirkulasi.

---

### 1.2. Alur Besar Operasional Harian (Timeline 24 Jam)

Alur kerja sirkulasi berjalan dalam siklus harian berulang:

```
[FASE 1: MALAM H-1 (20:00 - 23:00 WIB) — PERSIAPAN CETAK & DISTRIBUSI]
1. Input / Copy Jatah H+1 (Menentukan alokasi eksemplar per agen untuk terbitan besok).
2. Tutup Jatah Harian (Mengunci/Locking data alokasi agar tidak berubah saat mesin cetak beroperasi).
   *Jika ada order susulan malam: Gunakan fitur "Minta Buka Kunci" ke supervisor.
3. Cetak dokumen distribusi pendukung dan label alamat drop point agen.

                    │
                    ▼

[FASE 2: DINI HARI / SUBUH (01:00 - 05:00 WIB) — EKSPEDISI ARMADA]
1. Koran keluar dari percetakan dihitung dan dibendel (50 eks per ikat rafia).
2. Pengepakan koran disesuaikan per jurusan armada pengiriman.
3. Armada distribusi subuh berangkat mengantarkan koran ke seluruh wilayah Priangan Timur.

                    │
                    ▼

[FASE 3: SIANG HARI (11:00 - 14:00 WIB) — RETUR FISIK & HITUNG NETT SALES]
1. Agen mengembalikan fisik koran yang tidak terjual (Retur).
2. Petugas sirkulasi menginput jumlah retur di menu "Hitung Data Penjualan".
3. Sistem secara otomatis menghitung:
   • Nett Sales (Terjual Bersih) = Oplah Kirim − Retur Fisik
   • Nilai Tagihan Rupiah        = Nett Sales × Tarif Agen

                    │
                    ▼

[FASE 4: SORE / AKHIR BULAN — PENAGIHAN FAKTUR & INTEGRASI KASIR]
1. Menerbitkan "Faktur Penjualan Agen" yang dilengkapi Kode Barcode QR Kasir.
2. Agen datang ke loket kasir membawa lembar faktur.
3. Kasir memindai Kode QR faktur -> Sistem kasir otomatis membaca data tagihan -> Status berubah LUNAS.
4. Merekapitulasi seluruh omzet dan piutang pada "Laporan Penjualan" dan "Laporan Pemasaran".
```

---

## 2. PANDUAN PENGGUNAAN FITUR PER MENU

Navigasi sistem Sirkulasi terbagi ke dalam **3 Kelompok Menu Operasional** (ditambah Dashboard & Fungsi Akun):

---

### A. DASHBOARD SIRKULASI (`/sirkulasi/dashboard`)
Halaman utama saat pengguna dengan role *sirkulasi* login ke dalam sistem.

* **Fungsi**: Memantau ringkasan performa distribusi koran hari ini secara real-time.
* **Fitur Utama**:
  1. **Kartu KPI Utama**: Menampilkan Total Koran Dikirim (Oplah), Total Retur, Persentase Retur (target $\le 10\%$), Nett Sales terjual, dan Total Nilai Tagihan.
  2. **Status Penguncian Hari Ini**: Indikator apakah jatah terbitan hari ini dalam kondisi *Terkunci (Locked)* atau *Terbuka (Unlocked)*.
  3. **Grafik Tren Distribusi**: Tren eksemplar kirim vs retur per wilayah selama 7 hari terakhir.
  4. **Pintasan Cepat (Quick Actions)**: Tombol pintas langsung ke Edit Jatah, Tutup Jatah, dan Cetak Faktur.

---

### B. 1. KELOMPOK MENU TABEL (MASTER DATA)
Digunakan untuk mengelola data acuan dasar yang menjadi fondasi seluruh perhitungan distribusi.

#### 1. Wilayah Pemasaran (`/sirkulasi/tabel/wilayah`)
* **Fungsi**: Mendata kode dan nama wilayah jangkauan edar surat kabar di Priangan Timur (Kode 01 s/d 08: Kota Tasikmalaya, Kab. Tasikmalaya, Ciamis, Banjar, Garut, Pangandaran, dll).
* **Fitur**: Melihat daftar wilayah, koordinator lapangan per wilayah, dan target sebaran oplah.

#### 2. Pengepakan & Rute Armada (`/sirkulasi/tabel/pengepakan`)
* **Fungsi**: Mendata jurusan distribusi armada pengiriman subuh.
* **Fitur**: Informasi kode jurusan (misal: *01 Bag Tasik - Ciamis - Banjar*), nama sopir ekspedisi, nomor plat armada, dan urutan drop point pembagian koran.

#### 3. Jenis Agen & Tarif (`/sirkulasi/tabel/jenis-agen`)
* **Fungsi**: Mengatur kategori keagenan dan harga beli koran:
  * **Kode 11 (Agen Besar)**: Harga khusus agen distributor utama (Rp2.800/eks).
  * **Kode 99 (Sub Agen / Kios)**: Harga sub-distributor / pengecer (Rp3.000/eks).
  * **Eceran Umum**: Harga jual resmi konsumen (Rp4.000/eks).
* **Fitur**: Memastikan tarif harga terikat otomatis ke sistem perhitungan tagihan tanpa risiko salah ketik harga.

#### 4. Data Agen (`/sirkulasi/tabel/agen`)
* **Fungsi**: Buku induk seluruh mitra agen koran, sub-agen, dan loper.
* **Fitur**:
  * Input & edit data agen (Nama agen, nama pemilik, alamat kios, nomor HP/WA, wilayah, dan jurusan armada).
  * Menentukan **Jatah Default Harian** (alokasi rutin harian agen).
  * Mengatur **Batas Limit Piutang (Kredit)** dan melihat saldo piutang berjalan.
  * Fitur **Toggle Status (Aktif / Terkunci)**: Agen yang menunggak dapat dinonaktifkan sementara dari pengiriman koran.

---

### C. 2. KELOMPOK MENU JATAH (OPERASIONAL HARIAN H+1)
Jantung operasional sirkulasi harian untuk menentukan eksemplar koran yang akan dicetak dan dikirim.

#### 1. Edit Data Penjualan (`/sirkulasi/jatah/edit`)
* **Fungsi**: Mengatur jumlah eksemplar koran per agen untuk tanggal terbitan tertentu (default: edisi besok / H+1).
* **Cara Menggunakan**:
  1. Pilih tanggal terbitan yang ingin diatur.
  2. Sistem menampilkan tabel matriks seluruh agen lengkap dengan jatah default-nya.
  3. Ubah angka jatah pada kolom input untuk agen yang meminta penambahan atau pengurangan koran.
  4. Klik tombol **"Simpan Perubahan Jatah"**. Total oplah akan otomatis diperbarui.

#### 2. Copy Data Penjualan (`/sirkulasi/jatah/copy`)
* **Fungsi**: Menyalin alokasi jatah dari hari sebelumnya secara instan tanpa perlu mengetik ulang satu per satu (*fitur favorit warisan DOS SIRK.EXE*).
* **Cara Menggunakan**:
  1. Pilih **Tanggal Sumber (Asal)**: misalnya edisi kemarin atau edisi hari yang sama minggu lalu.
  2. Pilih **Tanggal Target (Tujuan)**: edisi besok yang sedang disiapkan.
  3. Klik **"Proses Duplikasi Jatah"**. Seluruh data agen dan angka oplah otomatis tersalin rapi.

#### 3. Tutup Penjualan Harian & Bulanan (`/sirkulasi/jatah/tutup`)
* **Fungsi**: Mengunci transaksi (*Locking*) agar data tidak dapat diubah-ubah lagi setelah diserahkan ke bagian percetakan dan keuangan.
* **Fitur Unggulan — Mekanisme Kunci & Buka Kunci**:
  * **Tombol Kunci Jatah**: Saat batas waktu input malam tiba (misal: 22.00 WIB), operator menekan tombol ini. Status berubah menjadi **TERKUNCI (LOCKED)**.
  * **Fitur "Minta Buka Kunci" (Request Unlock)**: Jika pada tengah malam terdapat order susulan darurat dari agen/loper, operator tidak bisa langsung mengedit sembarangan. Operator harus menekan tombol **"Minta Buka Kunci"** yang akan tercatat di sistem sebagai pengajuan ke Supervisor/Manajer Sirkulasi. Setelah dibuka, revisi dilakukan, lalu dikunci kembali.

#### 4. Hitung Data Penjualan (`/sirkulasi/jatah/hitung`)
* **Fungsi**: Memproses kalkulasi akhir penjualan setelah koran beredar dan koran retur diterima di siang hari.
* **Cara Menggunakan**:
  1. Pilih tanggal edisi yang ingin dihitung.
  2. Masukkan jumlah koran retur fisik yang dikembalikan oleh agen.
  3. Klik **"Proses Hitung Data Penjualan"**.
  4. Sistem otomatis mengkalkulasi:
     * $\text{Nett Sales} = \text{Kirim} - \text{Retur}$
     * Nilai Tagihan Bersih
     * Akumulasi per wilayah dan jurusan armada.

---

### D. 3. KELOMPOK MENU CETAK (LAPORAN & DOKUMEN RESMI)
Menghasilkan dokumen resmi untuk kebutuhan arsip, penagihan agen, laporan ke pimpinan, dan integrasi kasir.

#### 1. Laporan Pemasaran (`/sirkulasi/cetak/pemasaran`)
* **Fungsi**: Rekapitulasi sebaran koran berdasarkan sudut pandang pemasaran.
* **Tersedia 5 Tab Format Cetak**:
  1. **Agen Besar**: Laporan distribusi khusus agen distributor utama (Kode 11).
  2. **Sub Agen**: Laporan distribusi khusus jaringan kios/pengecer kecil (Kode 99).
  3. **Rekapitulasi Wilayah**: Komparasi oplah dan retur per kabupaten/kota (Tasik, Ciamis, Banjar, Garut, Pangandaran).
  4. **Rekapitulasi Jurusan**: Rekapitulasi muatan koran per armada/rute pengiriman subuh.
  5. **Audit Oplah Tahunan**: Rekapitulasi pergerakan oplah bulanan sepanjang tahun untuk kebutuhan verifikasi audit pers (SPS).
* **Fitur Cetak**: Dilengkapi tombol **"Cetak Laporan"** dengan format landscape siap print/PDF.

#### 2. Laporan Penjualan (`/sirkulasi/cetak/penjualan`)
* **Fungsi**: Rekapitulasi omzet dan piutang penjualan koran bulanan seluruh agen.
* **Data yang Disajikan**:
  * Kode Agen, Nomor Faktur Resmi, Nama Kios/Agen, Wilayah.
  * Total Kirim (Eks), Total Retur (Eks), Nett Sales (Eks Terjual).
  * Harga Satuan, Harga Bayar (Total Tagihan), Saldo Piutang Berjalan, dan Status Pembayaran (Lancar/Terkunci).
  * **Kolom Barcode Kasir**: Menampilkan mini barcode QR scannable di setiap baris agen.
* **Fitur Cetak Khusus**:
  * Diformat presisi untuk kertas **A4 Landscape**.
  * Dilengkapi lembar tanda tangan 3 pihak: *Manajer Sirkulasi*, *Bagian Kasir/Penerimaan*, dan *Staf Penjualan Sirkulasi*.
  * Bebas dari elemen yang mengganggu saat dicetak.

#### 3. Faktur Agen & QR Kasir (`/sirkulasi/cetak/faktur`)
* **Fungsi**: Lembar faktur resmi satuan per agen yang diserahkan sebagai tagihan koran.
* **Fitur Utama**:
  * Kop resmi surat kabar Kabar Priangan (PT Berkah Kabar Priangan - Pikiran Rakyat Group).
  * Rincian tagihan: Oplah Kirim, Retur, Nett Sales, Tarif Agen, dan Total Bayar.
  * **KODE BARCODE QR KASIR**:
    * Kode QR berukuran jelas yang memuat payload terenkripsi: Nomor Faktur, Nama Agen, dan Total Nominal Bayar.
    * Terhubung langsung dengan endpoint sistem `/api/faktur/{no_faktur}`.
    * Kasir dapat langsung men-scan kode ini menggunakan barcode scanner untuk menyelesaikan transaksi pembayaran tanpa perlu mengetik ulang nominal.

#### 4. Nama & Alamat Agen (`/sirkulasi/cetak/alamat-agen`)
* **Fungsi**: Mencetak buku direktori alamat lengkap seluruh agen, sub-agen, dan loper.
* **Kegunaan**: Diberikan kepada sopir armada baru, kurir susulan, atau petugas penagih lapangan (inkaso) agar mengetahui lokasi tepat drop point dan kontak agen.

---

### E. 4. MENU FUNGSI & SELESAI
1. **Ganti Password Operator (`/profile`)**: Mengubah sandi akun staf sirkulasi secara berkala untuk keamanan data.
2. **Keluar / Selesai (Logout)**: Menutup sesi login sirkulasi secara aman.

---

## 3. FITUR-FITUR UNGGULAN SISTEM SIRKULASI

| No | Fitur Unggulan | Manfaat Nyata bagi Perusahaan |
|---|---|---|
| **1** | **Integrasi Barcode QR Kasir** | Mengeliminasi kesalahan nominal saat agen membayar koran di kasir. Kasir cukup scan QR di lembar faktur agen, data langsung terisi otomatis. |
| **2** | **Sistem Locking & Request Unlock** | Mencegah kecurangan atau manipulasi oplah setelah cetak. Setiap perubahan susulan malam tercatat dan harus melalui izin atasan. |
| **3** | **Otomatisasi Hitung Nett Sales** | Menghilangkan hitungan manual kalkulator. Rumus $\text{Kirim} - \text{Retur} = \text{Nett Sales}$ langsung menghasilkan nilai tagihan rupiah per detik. |
| **4** | **Copy Jatah Harian Cepat** | Menghemat waktu operasional malam staf sirkulasi dari yang sebelumnya 30–45 menit menjadi hanya 5 detik via duplikasi pola kemarin. |
| **5** | **Format Cetak Presisi (Print-Ready)** | Seluruh laporan dan faktur sudah disesuaikan dengan ukuran standar printer kantor (A4 Portrait & Landscape) tanpa terpotong. |
| **6** | **Kompatibilitas Standar DOS SIRK.EXE** | Istilah kode agen (00/11/99), pembagian wilayah (01-08), dan alur jatah tetap sama seperti sistem lama sehingga staf senior tidak kesulitan beradaptasi. |

---

## 4. CHECKLIST PRAKTIK SAAT DEMO DI KANTOR KABAR PRIANGAN

Saat melakukan demo langsung di hadapan pimpinan atau staf sirkulasi:
1. **Buka Dashboard**: Tunjukkan KPI ringkasan eksemplar hari ini.
2. **Buka Menu Tabel**: Perlihatkan data Wilayah Priangan Timur dan Master Agen beserta tarifnya.
3. **Simulasikan Operasional Malam**:
   * Buka **Copy Data Penjualan** -> Salin data jatah.
   * Buka **Edit Data Penjualan** -> Tunjukkan kemudahan mengubah angka oplah agen.
   * Buka **Tutup Penjualan** -> Tunjukkan tombol *Kunci Data* dan fitur *Minta Buka Kunci*.
4. **Simulasikan Retur Siang**:
   * Buka **Hitung Data Penjualan** -> Input angka retur -> Tunjukkan nett sales langsung terkalkulasi otomatis.
5. **Simulasikan Penagihan Faktur & QR Kasir**:
   * Buka **Faktur Agen** -> Tunjukkan lembar faktur ber-kop resmi dan Kode QR Kasir.
   * Jelaskan bahwa kasir cukup scan kode tersebut untuk mencatat uang masuk tanpa ketik manual.
6. **Buka Laporan Penjualan & Pemasaran**:
   * Tunjukkan tampilan rekapitulasi bulanan dan tekan Ctrl + P untuk membuktikan hasil cetak rapi dan pas 1 lembar A4 landscape.
