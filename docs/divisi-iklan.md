# DIVISI IKLAN — Blueprint Analisis & Rancangan Sistem
## Reverse Engineering IKL.EXE → SI-KP Modern (Laravel)
### HU. Kabar Priangan — Tasikmalaya

**Versi:** 1.0 — Analisis Mendalam Pasca-Observasi
**Tanggal:** Oktober 2026
**Sumber:** Rekaman wawancara, foto layar IKL.EXE, formulir fisik asli, standar industri pers

---

## 📸 APA YANG DILIHAT DI FOTO LAYAR IKL.EXE

### Foto 1 — Menu CETAK (Laporan Iklan)
Terlihat submenu CETAK terbuka:
- LAPORAN PENJUALAN → PER HARI/TANGGAL PESAN, PER TGL. MUAT, PER BULAN
- PEMBATALAN IKLAN → filter tanggal
- DARI PEMUATAN IKLAN
- INSENTIF & KOMISI AE → PEROLEHAN/SALES/TGL.MUAT, PEROLEHAN/SALES/BULAN
- REKAP SALES/TGL.MUAT ← highlighted/aktif
- REKAP SALES/BULAN
- KLASIFIKASI/BULAN

Filter tampil: PENJUALAN | BULAN: 10 | TAHUN: 2026 | Nama AE: [input]

> **Temuan penting:** `REKAP SALES/TGL.MUAT` adalah laporan yang dipakai tim pracetak sebagai **lembar dummy** / panduan tata letak halaman koran per tanggal terbit. Bukan sekadar laporan keuangan, tapi instrumen operasional produksi cetak harian.

### Foto 2 — Form ORDER TRANSAKSI IKLAN
Form input transaksi yang sedang aktif:

```
NOMOR ORDER: [auto]         NO.PEMASANG: [auto]
PEMASANG   : HOTEL ASRI
ALAMAT     : JL.HZ.MUSTOFA.KOMP.ASIA PLAZA
KOTA       : TASIKMALAYA
JENIS IKLAN: DISPLAY BONUS   | [B].BARIS  [K].KOLOM  [D].DISPLAY
TGL. PESAN : 13-10-2026      | JUMLAH PEMUATAN: 4 kali
KOP IKLAN  : HAL.5/SUMEDANG  | TGL.MUAT: 14-10-2026
KOP KOTA   : [input]         |           15-10-2026
UKURAN     : brs 2.0 klm 100 mm          16-10-2026
                                          17-10-2026
HARGA BRUTO: Rp. 0.00        HARGA Rp. 0.00
POT        : 0.00 % Rp. 0.00
PPN 11%    : Rp. 0.00
MATERAI    : Rp. 0.00
TOTAL      :                 [TI].Tunai  [K].Kredit
---------------------------------------------------
KOMISIONER :
KOMISI     : 0.00 % Rp. 0.00  1. [Materi Iklan Baris]
PEROLEHAN  : S67.SUHITA DEWI  2.
INSENTIF   : 0.00 % Rp. 0.00  3.
                               4. 5. 6. 7.
```

**Temuan kritis dari form ini:**
1. `DISPLAY BONUS` — ada tipe iklan bonus/barter yang nilai = Rp 0
2. `[TI].Tunai [K].Kredit` — shortcut keyboard; legacy kode 1=Kredit, 2=Tunai
3. `PEROLEHAN: S67.SUHITA DEWI` — Format kode AE: [S/K][Nomor] [Nama]
4. Multi-date di kolom kanan (4 tanggal muat berurutan)
5. Field Materi Iklan Baris (7 baris teks) di kanan bawah form yang sama
6. `PPN 11%` — sistem lama belum diupdate, sistem baru wajib 12% dari config
7. `MATERAI Rp. 0.00` — deprecated, tidak perlu ditampilkan

---

## 1. MASTER DATA (TABEL) — Analisis Mendalam

### 1.1 Tabel PEMASANG (Master Pengiklan/Klien)

**Field wajib dari sistem legacy + tambahan standar industri:**

| Field | Tipe | Keterangan |
|---|---|---|
| `kode_pemasang` | VARCHAR | Auto: PSG-YYYY-NNNN |
| `nama_pemasang` | VARCHAR(200) | Nama lengkap perorangan/perusahaan |
| `alamat` | TEXT | |
| `kota` | VARCHAR(100) | |
| `no_telepon` | VARCHAR(30) | |
| `jenis_akun` | ENUM | TUNAI / KREDIT |
| `credit_limit` | DECIMAL | Plafon kredit (jika KREDIT) |
| `npwp` | VARCHAR(30) | Wajib untuk e-Faktur klien korporat |
| `email` | VARCHAR(200) | Invoice digital & digital proof |
| `nama_pic` | VARCHAR(200) | Person in charge |
| `jabatan_pic` | VARCHAR(100) | Direktur / Kabag Humas / dst |
| `jenis_klien` | ENUM | PERORANGAN/UMKM/KORPORASI/BIRO_IKLAN/PEMDA_BUMN |
| `diskon_default` | DECIMAL | Diskon tetap yang sudah disepakati (%) |
| `saldo_piutang` | DECIMAL | Auto-kalkulasi dari transaksi belum lunas |
| `status_aktif` | BOOLEAN | Soft delete — tidak pernah hapus permanen |
| `catatan` | TEXT | Catatan internal |

> ⚠️ **DILARANG HAPUS DATA PEMASANG** — narasumber menegaskan karena terkait historis piutang. Gunakan soft delete (`status_aktif = false`), bukan DELETE dari database.

**Fitur On-The-Fly saat Transaksi:**
Jika nama pemasang belum ada di database saat input transaksi → popup modal "Tambah Pemasang Baru" → isi nama + telepon + kota (minimal) → simpan ke master → transaksi dilanjutkan otomatis.

---

### 1.2 Tabel JENIS IKLAN (Master Produk, Tarif & Kategori)

Berdasarkan formulir legacy dan observasi Kabar Priangan, **warna (Full Color / Hitam Putih) menyatu langsung dengan Jenis/Tipe Iklan pada tabel master**, disertai ukuran baku (kolom × mm) dan langsung memiliki nominal harga di tabel master tersebut.

#### A. Analisis Komparasi: Model Kabar Priangan vs Standar Industri Pers Modern

| Parameter | Model Kabar Priangan (Unified Product Catalog / Flat SKU) | Model Industri Pers Konglomerasi (Kompas/Jawa Pos - Dynamic Rate Matrix) | Model Rancangan SI-KP 2026 (Smart Unified Catalog - Rekomendasi) |
|---|---|---|---|
| **Struktur Master** | Warna (FC/BW), jenis, ukuran standar, dan harga digabung dalam 1 baris entri master produk (SKU). | Base rate per mmk terpisah, dikalikan matriks: Pengali Warna (FC 1.8x, BW 1x) × Pengali Halaman (Hal 1 2x) × Hari (Weekend 1.2x). | **Mengadopsi Model Kabar Priangan** dengan field relasi cerdas: operator pilih produk, harga & dimensi langsung autofill, namun tetap fleksibel untuk custom mmk. |
| **Kecepatan Input** | ⚡ **Sangat Cepat**: Operator tinggal pilih nama produk, tarif & warna langsung otomatis terkunci. Nol risiko salah input tarif. | ⏱️ **Lambat**: Operator harus klik 4-5 dropdown (Jenis, Warna, Halaman, Hari, Posisi) untuk kalkulasi harga. | ⚡ **Cepat & Akurat**: Cukup 1 klik nama paket/jenis, kalkulator real-time langsung aktif. |
| **Kesesuaian Operasional** | **Sangat Pas untuk Koran Daerah**: Paket iklan koran lokal umumnya sudah terstandarisasi (misal: "Kolom BW 2x50", "Display FC", "Baris"). | Lebih cocok untuk koran nasional dengan ribuan kombinasi penempatan iklan programmatic. | **Pragmatis & Sesuai Kebutuhan**: Tidak over-engineered, sesuai ritme kerja kantor Kabar Priangan. |

#### B. Daftar Entri Master Produk Iklan Kabar Priangan:

| Kode SKU | Nama Produk Iklan | Tipe Dasar | Warna | Ukuran Default | Tipe Hitung | Nilai Master / Tarif |
|---|---|---|---|---|---|---|
| `BARIS` | Iklan Baris | BARIS | BW | Fleksibel (Min 2 baris) | PER_BARIS | Rp 22.750 / baris |
| `KLM_BW_1X50` | Iklan Kolom BW (1×50 mm) | KOLOM | BW | 1 kolom × 50 mm (50 mmk) | PAKET_FIXED | Rp 450.000 / muat |
| `KLM_BW_2X100` | Iklan Kolom BW (2×100 mm) | KOLOM | BW | 2 kolom × 100 mm (200 mmk) | PAKET_FIXED | Rp 1.600.000 / muat |
| `DISP_BW_CUST` | Iklan Display BW (Custom) | DISPLAY | BW | Custom (Max 7 kol × 540 mm) | PER_MMK | Rp 12.000 / mmk |
| `DISP_FC_CUST` | Iklan Display Full Color | DISPLAY | FC | Custom (Max 7 kol × 540 mm) | PER_MMK | Rp 24.000 / mmk |
| `DISP_BONUS` | Display Bonus / Barter | BONUS | BW/FC | Sesuai memo manajer | FIXED_ZERO | Rp 0 (Non-komersial) |
| `ADV_HAL_FC` | Advertorial 1 Halaman FC | ADVERTORIAL | FC | 7 kolom × 540 mm | PAKET_FIXED | Rp 25.000.000 / muat |
| `KUPING_H1_FC` | Kuping Halaman 1 FC | KHUSUS | FC | 2 kolom × 50 mm | PAKET_FIXED | Rp 3.500.000 / muat |

#### C. Desain Tabel `jenis_iklan`:
| Field | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT PK | |
| `kode` | VARCHAR(30) UNIQUE | Contoh: `BARIS`, `DISP_FC_CUST`, `KLM_BW_1X50` |
| `nama` | VARCHAR(150) | Nama lengkap produk iklan |
| `tipe_dasar` | ENUM | `BARIS`, `KOLOM`, `DISPLAY`, `ADVERTORIAL`, `BONUS` |
| `mode_warna` | ENUM | `BW` (Hitam Putih), `FC` (Full Color), `SPOT` |
| `cara_hitung` | ENUM | `PER_BARIS`, `PER_MMK`, `PAKET_FIXED`, `FIXED_ZERO` |
| `harga_dasar` | DECIMAL(15,2) | Tarif per baris, per mmk, atau harga flat per pemuatan |
| `is_fixed_size` | BOOLEAN | `true` = ukuran terkunci (paket), `false` = custom kolom & mm |
| `default_kolom` | INT (Nullable) | Nilai default lebar kolom |
| `default_tinggi_mm`| INT (Nullable) | Nilai default tinggi mm |
| `max_kolom` | INT | Default 7 (batas maksimum lebar koran) |
| `max_tinggi_mm` | INT | Default 540 (batas maksimum tinggi cetak koran) |
| `min_order` | INT | Minimal baris = 2, minimal pemuatan = 1 (atau 2 untuk promo) |
| `status_aktif` | BOOLEAN | Default true (soft delete) |

---

### 1.3 Tabel SALES / AE (Account Executive)

**Kode dari sistem legacy:**
- **Kode S** = Struktural (karyawan tetap iklan) → contoh: `S67 SUHITA DEWI`
- **Kode K** = Koresponden (wartawan/karyawan lain bawa iklan) → contoh: `K12 BUDI S`
- **FO** = Front Office (walk-in counter, tidak ada AE spesifik)

**Desain Tabel `sales_ae`:**
| Field | Tipe | Keterangan |
|---|---|---|
| `kode_ae` | VARCHAR(10) | S67, K12, FO |
| `nama_ae` | VARCHAR(200) | |
| `jenis_kode` | ENUM | STRUKTURAL / KORESPONDEN / FO |
| `user_id` | BIGINT FK | Link ke user login (nullable) |
| `tarif_insentif_default` | DECIMAL | % insentif default |
| `tarif_komisi_max` | DECIMAL | Max komisi (legacy: 20%) |
| `status_aktif` | BOOLEAN | |

---

### 1.4 Tabel KOP IKLAN (Rubrik Kategori Iklan Baris)

Rubrikasi khusus pengelompokan iklan baris pada lembar koran cetak:
Contoh: *Kehilangan, Otomotif/Mobil, Lowongan Kerja, Properti/Rumah Dijual, Jasa, Aneka Usaha*.

| Field | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT PK | |
| `kode` | VARCHAR(20) | OTO, HILANG, LOKER, RUMAH, dll |
| `nama_kop` | VARCHAR(100) | Nama rubrik |
| `deskripsi` | TEXT | Keterangan |
| `urutan` | INT | Urutan tampil di koran |
| `status_aktif` | BOOLEAN | Soft delete |

---

### 1.5 Tabel KOP KOTA (Penempatan Halaman Koran & Biro)

> 💡 **Klarifikasi Kritis:** `KOP KOTA` pada sistem Kabar Priangan adalah **Penempatan Halaman Koran & Wilayah Sirkulasi**. 
> Contoh nyata di form legacy: `HAL.5/SUMEDANG`. Artinya iklan tersebut di-booking khusus untuk terbit di **Halaman 5 (Edisi Sumedang)**. Harian Umum Kabar Priangan memiliki halaman khusus per wilayah di Priangan Timur (Tasikmalaya, Ciamis, Banjar, Garut, Sumedang).

| Field | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT PK | |
| `kode` | VARCHAR(20) | HAL1, HAL5_SMD, HAL7_TSK, HAL8_CMS, dll |
| `nomor_halaman` | INT | 1, 2, 3, ... max halaman edisi |
| `nama_penempatan` | VARCHAR(100) | "Halaman 5 - Sumedang", "Halaman 1 - Utama" |
| `wilayah_biro` | VARCHAR(100) | Sumedang, Tasikmalaya, Ciamis, Banjar, Garut, Umum |
| `surcharge_persen`| DECIMAL(5,2) | Biaya tambahan khusus penempatan premium (misal Hal 1: +50%) |
| `status_aktif` | BOOLEAN | Soft delete |

---

## 2. TRANSAKSI IKLAN — Alur, Aturan Bisnis & Engine Harga

### 2.1 Alur Input Order Lengkap

```
STAF FO/AE Buka Form Order Transaksi
       ↓
1. PILIH METODE BAYAR:
   [T] TUNAI  → Wajib setor kasir sebelum terbit (CBP Gate Aktif)
   [K] KREDIT → Validasi plafon kredit agen & persetujuan tempo
       ↓
2. PEMASANG / AGEN:
   ├── Autocomplete cepat (Ketik nama/telepon/kode)
   └── Belum ada? Klik [+ Tambah Pemasang] (Modal On-the-Fly tanpa reload)
       ↓
3. PILIH PRODUK IKLAN (UNIFIED CATALOG):
   ├── Pilih Produk: (Misal: Display FC / Kolom BW / Iklan Baris)
   ├── Mode Warna & Tarif Dasar langsung terisi otomatis dari master!
   ├── KOP KOTA (Penempatan Halaman, misal: HAL.5/SUMEDANG)
   ├── SPESIFIKASI UKURAN:
   │     • JIKA BARIS:
   │         - Input teks materi kalimat langsung ke form aplikasi
   │         - Live line counter: Validasi Minimal 2 Baris, Maksimal 7 Baris
   │     • JIKA KOLOM / DISPLAY:
   │         - Preset Modul / Custom: Lebar Kolom (Maksimal 7) × Tinggi (Maksimal 540 mm)
   │         - Live calculation: mmk = Kolom × Tinggi mm
   │         - Upload / lampirkan file materi desain (PDF/TIFF/JPG CMYK)
       ↓
4. PENJADWALAN MUAT (MULTI-DATE SINKRON):
   ├── Input Jumlah Pemuatan: N kali (misal: 3 kali)
   ├── Pilih Tanggal Muat di Kalender: Wajib tepat N tanggal (1-to-1 sync!)
   │   (Jika Jumlah = 3, maka Tanggal Muat tepat 3 tanggal terpilih)
       ↓
5. ENGINE KALKULASI HARGA REAL-TIME:
   ├── Hitung Bruto = Tarif Master × Dimensi × Jumlah Pemuatan
   ├── Diskon (%)   = Pemotongan harga disepakati
   ├── DPP          = Bruto - Diskon
   ├── PPN (Config) = DPP × 12% (dinamis dari sistem_config)
   └── Netto Tagih  = DPP + PPN
       ↓
6. DISTRIBUSI AE & KOMISI:
   ├── Pilih Kode AE (S67 Suhita Dewi, K12, FO) → Auto insentif nominal
   └── Komisioner Agen Luar (jika ada) → Komisi nominal
       ↓
7. SIMPAN TRANSAKSI:
   Generate Nomor Order: ORD/KORAN/2026/10/0001
   Cetak Surat Perintah Pemuatan Iklan (SPPI) & Kwitansi
```

---

### 2.2 Aturan Bisnis Kritis (Business Rules Enforcement)

#### Rule 1: Maksimal Kolom Koran = 7 Kolom
- Format koran harian Kabar Priangan menggunakan kisi (grid) **maksimal 7 kolom**.
- Validasi sistem: $1 \le \text{kolom} \le 7$.
- Input kolom > 7 otomatis ditolak oleh form dengan peringatan: *"Lebar iklan melebihi batas fisik halaman koran (Maksimal 7 Kolom)."*

#### Rule 2: Aturan Khusus Iklan Baris
1. **Teks Kalimat Diinput Langsung ke Form:**
   - Materi iklan baris berupa rangkaian kalimat yang diketik langsung ke dalam aplikasi oleh operator/front office.
   - Sistem menyediakan textarea cerdas dengan *visual word wrap simulator* (lebar 1 kolom baris koran = ±32 karakter per baris).
2. **Minimal Order Iklan Baris = 2:**
   - **Minimal 2 Baris Teks:** Pesanan iklan baris tidak boleh kurang dari 2 baris (jika teks hanya 1 baris, tetap dikenakan tarif minimum 2 baris).
   - **Minimal 2 Kali Tayang (opsional paket promo):** Jika ada ketentuan paket, frekuensi pemuatan minimum adalah 2 edisi.

#### Rule 3: Sinkronisasi 1-to-1 Jumlah Pemuatan & Tanggal Muat
- Jumlah pemuatan harus persis sama dengan jumlah tanggal yang dipilih pada jadwal muat:
  $$\text{Jumlah Pemuatan} = \text{Count}(\text{Tanggal Muat})$$
- Contoh: Jika `Jumlah Pemuatan = 3`, maka di kalender wajib dipilih tepat **3 tanggal muat** (misal: 15-10-2026, 16-10-2026, 17-10-2026).
- Form tidak dapat disimpan jika terjadi *mismatch* (misal pemuatan 3 tapi tanggal yang dipilih baru 2).

#### Rule 4: Formula Kalkulasi Harga (Pricing Engine)

```
A. IKLAN BARIS:
   Bruto = MAX(jumlah_baris, 2) × tarif_per_baris × jumlah_pemuatan
   (Contoh: 3 baris × Rp 22.750 × 3 muat = Rp 204.750)

B. IKLAN KOLOM / DISPLAY (CUSTOM MMK):
   Total MMK = lebar_kolom × tinggi_mm
   Bruto     = Total MMK × tarif_per_mmk × jumlah_pemuatan
   (Contoh: 2 kol × 100 mm × Rp 24.000 (FC) × 3 muat = Rp 14.400.000)

C. IKLAN PAKET / MODUL TETAP:
   Bruto = tarif_paket_master × jumlah_pemuatan
   (Contoh: Paket Kuping H1 Rp 3.500.000 × 2 muat = Rp 7.000.000)

D. KALKULASI PAJAK & TOTAL AKHIR (SEMUA JENIS):
   Diskon Nominal = Bruto × (diskon_persen / 100)
   DPP            = Bruto - Diskon Nominal
   PPN Nominal    = DPP × (ppn_config_persen / 100)    [Tahun 2026 = 12%]
   TOTAL NETTO    = DPP + PPN Nominal
```

#### Rule 5: Multi-Date Scheduling (Jadwal Muat Independen)

Satu order transaksi iklan memiliki relasi `hasMany` ke tabel `order_jadwal_muat`. Setiap tanggal terbit memiliki siklus hidup (lifecycle) tersendiri untuk kebutuhan tim produksi/layout:
- Rekap Pracetak harian menarik data berdasarkan `tanggal_muat` spesifik.
- Jika edisi tanggal ke-2 dibatalkan karena kendala teknis percetakan, status tanggal tersebut bisa di-*reschedule* tanpa merusak tanggal muat lainnya.

**Tabel `order_jadwal_muat`:**
| Field | Tipe | Keterangan |
|---|---|---|
| `transaksi_id` | BIGINT FK | Link ke transaksi utama |
| `tanggal_muat` | DATE | Tanggal terbit edisi koran |
| `edisi_ke` | INT | Urutan pemuatan (1, 2, 3...) |
| `status_tayang` | ENUM | `TERJADWAL`, `SUDAH_TAYANG`, `RESCHEDULE`, `BATAL` |
| `bukti_muat_pdf` | VARCHAR(255) | Link scan/e-paper halaman terbit (nullable) |

#### D. Edit Transaksi (Reschedule)

Yang **bisa** diedit sebelum cut-off:
- Tanggal muat (reschedule)
- Ukuran/dimensi (jika belum layout)
- Teks materi (jika belum layout)
- Perolehan AE

Yang **TIDAK BISA** diedit setelah cut-off:
- Nama pemasang, jenis pembayaran, nominal yang sudah ditagihkan

Edit setelah cut-off → wajib prosedur **Memo Unlock** dari supervisor.

#### E. Aturan Pembatalan

| Jenis Iklan | Bisa Dibatalkan? | Mekanisme |
|---|---|---|
| Kredit (belum tayang) | ✅ Ya | Tombol Batalkan + alasan |
| Bonus/Barter (belum tayang) | ✅ Ya | Tombol Batalkan |
| Tunai/Cash (sudah bayar) | ❌ TIDAK langsung | Prosedur refund Kasir + memo manajemen |
| Sudah tayang | ❌ Tidak | Klaim kompensasi/run-on |

> ⚠️ **Rule dari narasumber:** Iklan cash tidak boleh dibatalkan manual karena sudah masuk pembukuan kas. Ini rule besi yang harus di-enforce di level sistem.

---

### 2.3 Tutup Transaksi Harian (Daily Cut-Off)

Setelah cut-off dijalankan:
- Staf tidak bisa tambah/ubah transaksi di tanggal tersebut
- Data "ditarik" oleh Akuntansi/Keuangan
- Order DRAFT yang belum bayar → EXPIRED
- Order ANTREAN → dikunci (read-only)
- Auto-generate Rekap Harian untuk pracetak & laporan

**Flow:**
```
Supervisor klik [Tutup Transaksi Hari Ini]
  → Konfirmasi modal
  → Lock semua order tanggal hari ini
  → Flag tutup_transaksi di jadwal_operasional = true
  → Generate rekap otomatis
```

**Notifikasi cut-off di form transaksi:**
- Sebelum jam cut-off: "Batas materi: [JAM] WIB. Iklan terbit esok [tanggal]."
- Setelah jam cut-off: "Sudah melewati batas. Iklan terbit [tanggal lusa]."
- Setelah flag tutup: "Transaksi hari ini sudah ditutup."

---

### 2.4 Buka/Kunci Aplikasi (Memo Unlock)

**Skenario:** Ada iklan susulan dari klien VIP setelah cut-off.

**Prosedur:**
1. Supervisor buka halaman Buka Kunci Transaksi
2. Input alasan + nomor memo (audit trail wajib)
3. Pilih scope: user tertentu atau tanggal tertentu
4. Sistem log: siapa, kapan, alasan
5. Setelah order susulan masuk → re-lock

**Tabel `memo_unlock`:**
```
id | tanggal_dikunci | dibuka_oleh | alasan | nomor_memo | dibuka_pada | ditutup_kembali_pada
```

---

### 2.5 DP Insentif & Hitung Insentif

**DP Insentif:** Uang muka komisi AE sebelum transaksi lunas.

**Hitung Insentif:**
```
Insentif AE = Σ(Netto_per_order × insentif_persen) - DP_yang_sudah_dibayar
```

Output: Rekap per AE per bulan → export Excel untuk payroll.

---

## 3. LAPORAN — Standar Industri Pers

### 3.1 Laporan Operasional Harian

| Laporan | Filter | Pengguna |
|---|---|---|
| Laporan Penjualan per Tanggal Pesan | Tanggal order | Evaluasi omzet FO |
| **Rekap per Tanggal Muat (Dummy Pracetak)** | Tanggal terbit | **Pracetak — panduan layout halaman** |
| Laporan Pembatalan | Rentang tanggal | Monitoring pembatalan & refund |

### 3.2 Laporan Sales & Insentif

| Laporan | Filter |
|---|---|
| Rekap Sales per Tanggal Muat | Tanggal + Nama AE |
| Rekap Sales per Bulan | Bulan + Tahun |
| Klasifikasi Iklan per Bulan | Bulan + Tahun |
| Perolehan/Sales per Tgl. Muat | Tanggal + AE |
| DP Insentif | AE + Periode |

### 3.3 Laporan Tambahan Standar Industri

| Laporan | Keterangan |
|---|---|
| Laporan Aging Piutang | 0-30 / 31-60 / 61-90 / >90 hari per pemasang |
| Fill Rate Halaman Koran | % iklan vs editorial per edisi |
| Omzet per Media | Koran Cetak / Online / TV |
| Omzet per Jenis Iklan | Baris vs Kolom vs Display vs Advertorial |
| Export e-Faktur CSV | Format DJP untuk impor aplikasi e-Faktur Pajak |

---

## 4. REKAP PRACETAK — Laporan Paling Kritis Operasional

Laporan ini adalah instrumen operasional produksi cetak harian, bukan sekadar laporan keuangan.

**Isi Rekap per Tanggal Muat (Dummy Sheet):**
```
REKAP IKLAN TAYANG — EDISI: [TANGGAL]
HU. Kabar Priangan

HALAMAN 5 - SUMEDANG
  1. HOTEL ASRI     | Display FC | 2 kol × 100mm | file: [path]
  2. TOKO BUDI      | Iklan Baris | 3 baris       | "Dijual rumah type 36..."
  ...

HALAMAN 7 - TASIKMALAYA
  1. ...

TOTAL IKLAN: XX order | TOTAL NILAI: Rp XXX.XXX.XXX
```

Format: PDF cetak + tampilan web.

---

## 5. DATABASE — Tabel Utama `transaksikoran`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT PK | |
| `nomor_order` | VARCHAR(30) | Auto: ORD/KORAN/YYYY/NNNNNN |
| `pelanggan_id` | BIGINT FK | |
| `jenis_pembayaran` | ENUM | TUNAI / KREDIT |
| `status_kredit_disetujui` | BOOLEAN | Bypass CBP gate |
| `disetujui_kredit_oleh` | BIGINT FK | User supervisor |
| `sales_ae_id` | BIGINT FK | AE perolehan |
| `komisioner_id` | BIGINT FK | Nullable |
| `jenis_iklan_id` | BIGINT FK | |
| `kop_iklan_id` | BIGINT FK | |
| `kop_kota_id` | BIGINT FK | |
| `tgl_pesan` | DATE | |
| `jumlah_pemuatan` | INT | |
| `ukuran_baris` | INT | Untuk jenis BARIS |
| `ukuran_kolom` | INT | Untuk KOLOM/DISPLAY |
| `ukuran_tinggi_mm` | INT | Untuk KOLOM/DISPLAY |
| `jenis_warna` | ENUM | BW / FC |
| `materi_teks` | TEXT | Naskah iklan baris (7 baris) |
| `keterangan_materi` | TEXT | Instruksi desain |
| `status_materi` | ENUM | SIAP_CETAK / BUTUH_SETTING |
| `harga_bruto` | DECIMAL(15,2) | |
| `diskon_persen` | DECIMAL(5,2) | Total diskon |
| `dpp` | DECIMAL(15,2) | Dasar Pengenaan Pajak |
| `ppn_persen` | DECIMAL(5,2) | Dari sistem_config saat order dibuat |
| `ppn_nominal` | DECIMAL(15,2) | |
| `materai` | DECIMAL(10,2) | Default 0 (deprecated) |
| `total_netto` | DECIMAL(15,2) | |
| `insentif_persen` | DECIMAL(5,2) | |
| `insentif_nominal` | DECIMAL(15,2) | |
| `komisi_persen` | DECIMAL(5,2) | |
| `komisi_nominal` | DECIMAL(15,2) | |
| `status_order` | ENUM | Lihat lifecycle di bawah |
| `status_sppi` | ENUM | BELUM / SUDAH_DITANDATANGANI |
| `input_oleh_user_id` | BIGINT FK | |
| `catatan_internal` | TEXT | |
| `tutup_transaksi_at` | TIMESTAMP | Timestamp cut-off |

**Status Lifecycle Order:**
```
DRAFT → MENUNGGU_BAYAR → ANTREAN_PRACETAK → SEDANG_SETTING
     → SIAP_TERBIT → TERBIT → DITAGIH → LUNAS
     → DIBATALKAN / KOMPLAIN / EXPIRED
```

---

## 6. FORM TRANSAKSI MODERN 2026 — Spesifikasi UI/UX (Dual-Pane Cockpit)

Untuk menggantikan antarmuka DOS dengan standar tercanggih 2026 yang cepat, bersih, dan memukau, form dirancang dengan model **Dual-Pane Split Screen (60% Smart Order Form : 40% Live Preview & Receipt)**:

### A. Panel Kiri (60%): Smart Order Builder
1. **Identitas Transaksi & Pembayaran:**
   - Segmented Button: `[ 💵 TUNAI ]` vs `[ 📄 KREDIT ]` (Hotkeys: `Alt+T` / `Alt+K`).
   - Autocomplete Pemasang: Input pencarian instan (nama/telepon/kode) + Tombol `[+ Tambah Baru]` modal on-the-fly.
   - Status Kredit Badge: Hijau (Aman) / Merah (Ada tunggakan piutang).
2. **Katalog Produk Iklan (Unified Product Selector):**
   - Grid Card Selector Produk: `[ Iklan Baris ]` `[ Kolom BW ]` `[ Display BW ]` `[ Display FC ]` `[ Advertorial ]` `[ Bonus ]`.
   - Begitu produk dipilih: **Mode Warna (BW/FC)** dan **Tarif Dasar** langsung otomatis terisi dari tabel master!
3. **Penempatan Halaman (KOP KOTA) & Rubrik:**
   - `KOP KOTA`: Dropdown Halaman Koran & Biro (contoh: `HAL.5/SUMEDANG`, `HAL.1 - UTAMA`, `HAL.7 - TASIKMALAYA`).
   - `KOP IKLAN`: Dropdown Rubrik (khusus Baris, misal: Kehilangan, Otomotif, Loker).
4. **Spesifikasi Dimensi & Editor Teks:**
   - **Jika Iklan Baris:**
     * Editor teks materi baris langsung di dalam form.
     * Karakter counter per baris (standar 32 karakter per baris).
     * Validasi otomatis: Minimal order **2 baris** (bisa muat hingga 7 baris).
   - **Jika Iklan Kolom / Display:**
     * Lebar Kolom: Input angka dengan **validasi ketat maksimal 7 kolom** ($1 \le \text{kolom} \le 7$).
     * Tinggi mm: Input tinggi milimeter (maksimal 540 mm).
     * Tombol Preset Cepat: `[1 klm × 50 mm]` `[2 klm × 100 mm]` `[1/4 Hal]` `[1/2 Hal]` `[1 Halaman Penuh]`.
     * Dropzone Upload File Materi: Upload PDF/TIFF/JPG CMYK 300dpi untuk tim pracetak.
5. **Jadwal Tayang (Multi-Date Interaktif dengan Aturan 1-to-1):**
   - Input `Jumlah Pemuatan`: Misal 3 kali.
   - Kalender Multi-Date: Klik tanggal-tanggal terbit di kalender mini.
   - **Validasi 1-to-1 Enforced:** Jumlah tanggal yang diklik harus persis sama dengan angka pemuatan ($N$ kali $= N$ tanggal terpilih).
6. **Perolehan Marketing (Sales / AE):**
   - Dropdown AE: Kode `S67` (Suhita Dewi), `K12`, atau `FO`.
   - Insentif % default (max 20%) → nominal rupiah otomatis terhitung.
   - Input Komisioner / Agen eksternal (opsional).

### B. Panel Kanan (40%): Live Preview & Struk Finansial Real-Time
1. **Interactive Newspaper Grid Simulator (Miniatur Halaman Koran):**
   - Menampilkan visual representasi halaman koran 7 kolom secara proporsional.
   - Jika operator mengisi 2 kolom × 100 mm, kotak biru di grid simulator otomatis merender posisi dan skala iklan tersebut di halaman koran.
2. **Kalkulator Finansial Transparan:**
   ```
   Tarif Dasar Master : Rp     24.000 / mmk (Display FC)
   Luas Iklan         : 200 mmk (2 klm × 100 mm)
   Frekuensi Tayang   : 3 kali
   ───────────────────────────────────────────────────
   Subtotal Bruto     : Rp 14.400.000
   Diskon (10%)       : -Rp 1.440.000
   DPP                : Rp 12.960.000
   PPN 12% (Config)   : Rp  1.555.200
   ───────────────────────────────────────────────────
   TOTAL TAGIHAN      : Rp 14.515.200
   ```
3. **Action Bar:**
   - `[ Simpan Draft (F2) ]`
   - `[ Simpan & Cetak SPPI (F9) ]`
   - `[ Kirim Bukti Order ke WhatsApp Klien ]`

---

## 7. MODAL TAMBAH PEMASANG ON-THE-FLY

```
┌─ Pemasang Baru ─────────────────────────────────┐
│                                                   │
│  Nama Pemasang*  : [____________________________] │
│  Jenis Akun*     : ● TUNAI  ○ KREDIT             │
│  Telepon*        : [____________________________] │
│  Kota*           : [____________________________] │
│  Alamat          : [____________________________] │
│  Email           : [____________________________] │
│                                                   │
│  [Batal]              [Simpan & Gunakan →]        │
└───────────────────────────────────────────────────┘
```

Setelah disimpan: data masuk ke tabel `pelanggan`, form terisi otomatis, tampilkan badge kecil "Pemasang Baru" di sebelah nama.

---

## 8. CHECKLIST IMPLEMENTASI — PRIORITAS

### 🔴 PRIORITAS TINGGI (Core Flow)

- [ ] Buat/lengkapi tabel master: `pelanggan`, `sales_ae`, `kop_iklan`, `kop_kota`
- [ ] Buat tabel `jenis_iklan` dengan 6 kode + tarif
- [ ] Form transaksi wizard 4 langkah
- [ ] Autocomplete pemasang + fallback on-the-fly create
- [ ] Toggle Tunai/Kredit + CBP gate logic
- [ ] Kalkulasi otomatis Bruto → DPP → PPN (dari config) → Netto
- [ ] Multi-date picker + tabel `order_jadwal_muat`
- [ ] PPN dari `sistem_config`, bukan hardcode

### 🟡 PRIORITAS MENENGAH (Operasional Harian)

- [ ] Daily Cut-Off: tombol tutup transaksi + lock mechanism
- [ ] Notifikasi cut-off di form (jam config)
- [ ] Laporan Dummy Pracetak (PDF rekap per tanggal muat)
- [ ] Laporan Harian per tanggal pesan + per tanggal muat
- [ ] Rekap Sales/AE per tanggal muat + per bulan
- [ ] Laporan Pembatalan + Klasifikasi

### 🟢 PRIORITAS LANJUTAN (Compliance & Lengkap)

- [ ] Memo Unlock: audit log buka kunci cut-off
- [ ] SPPI PDF: auto-generate bukti order formal
- [ ] DP Insentif: pencatatan advance komisi AE
- [ ] Hitung Insentif: rekap per AE per bulan + export
- [ ] Export e-Faktur CSV (format DJP)
- [ ] Laporan Aging Piutang
- [ ] Sinkronisasi ke modul Accounting

---

## 9. CATATAN TEKNIS KRITIS

```php
// PPN — WAJIB dari config, bukan hardcode
// SALAH:
$ppn = $dpp * 0.11;

// BENAR:
$ppn_persen = SistemConfig::get('ppn_persen', 12);
$ppn = $dpp * ($ppn_persen / 100);
```

```php
// Pemasang — SELALU soft delete
// SALAH:
Pelanggan::find($id)->delete();

// BENAR:
Pelanggan::find($id)->update(['status_aktif' => false]);
```

```
// Kode AE — Format Standar
S67 SUHITA DEWI   → Struktural
K12 BUDI SANTOSO  → Koresponden
FO                → Front Office (tanpa AE spesifik)
```

---

## 10. UPDATE STATUS OBSERVASI (untuk PLAN.md)

| Item | Status Sebelumnya | Status Setelah Analisis Ini |
|---|---|---|
| Observasi Iklan | Garis besar | ✅ DETAIL LENGKAP terdokumentasi |
| CBP Gate rule | Belum detail | ✅ Tunai=CBP aktif, Kredit=bypass+approval |
| Jenis iklan | Belum detail | ✅ 6 jenis: Baris, Kolom, Display BW/FC, Advertorial, Bonus |
| Master AE | Belum detail | ✅ Kode S+K+FO, max komisi 20% |
| PPN | Belum konfirmasi | ⚠️ Legacy 11%, sistem baru WAJIB 12% dari config |
| Materai | Belum konfirmasi | ✅ Deprecated = Rp 0 |
| Multi-date muat | Belum detail | ✅ Tabel `order_jadwal_muat` terpisah |
| On-the-fly pemasang | Belum ada | ✅ Modal popup saat transaksi |
| Dummy pracetak | Belum tahu | ✅ Laporan operasional paling kritis |
| Pembatalan Tunai | Belum detail | ✅ Tidak bisa langsung — prosedur refund Kasir |

---

*Dokumen ini adalah sumber kebenaran tunggal untuk pengembangan Divisi Iklan.*
*Update setiap ada klarifikasi baru dari pengguna atau narasumber.*
*Tanggal terakhir update: Oktober 2026*
