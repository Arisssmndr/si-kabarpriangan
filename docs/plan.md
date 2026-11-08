# Rencana Strategis & Roadmap SI-KP
## Sistem Informasi Manajemen — Harian Umum Kabar Priangan
*(Koran Cetak • Media Online • Priangan TV)*

**Versi:** 4.0 — Revisi Status Aktual Proyek
**Tanggal:** Oktober 2026
**Status Pengerjaan:** 🔴 Baru dimulai — Divisi Iklan saja

---

## ⚠️ STATUS AKTUAL PROYEK (14 Oktober 2026)

### Ringkasan Cepat

| Item | Keterangan |
|---|---|
| **Total Divisi** | 5 divisi |
| **Total Role** | 6 role (5 divisi + 1 Administrator) |
| **Divisi Diobservasi** | 2 dari 5 (Iklan ✅, Sirkulasi ✅ garis besar) |
| **Divisi Belum Diobservasi** | 3 divisi (Keuangan, Accounting, Kasir) |
| **Bagian Dikerjakan** | Divisi Iklan saja |
| **Tahap Observasi Iklan** | Garis besar — belum detail per menu |
| **Tahap Observasi Sirkulasi** | Garis besar — belum detail per menu |

### Scope Divisi & Role

```
SI-KP — 5 Divisi, 6 Role
├── [1] DIVISI IKLAN          → Role: Staff Iklan / AE      [✅ Diobservasi garis besar]
│                                                            [🔨 SEDANG DIKERJAKAN]
├── [2] DIVISI KEUANGAN       → Role: Staff Keuangan        [❌ Belum diobservasi]
│                                                            [⏳ Belum dikerjakan]
├── [3] DIVISI ACCOUNTING     → Role: Staff Accounting      [❌ Belum diobservasi]
│                                                            [⏳ Belum dikerjakan]
├── [4] DIVISI SIRKULASI      → Role: Staff Sirkulasi       [✅ Diobservasi garis besar]
│                                                            [⏳ Belum dikerjakan]
├── [5] DIVISI KASIR          → Role: Staff Kasir           [❌ Belum diobservasi]
│                                                            [⏳ Belum dikerjakan]
└── [*] LINTAS DIVISI         → Role: Administrator         [⏳ Belum dikerjakan]
```

> **Catatan:** Nama divisi sudah dikonfirmasi. Perlu wawancara detail untuk Keuangan, Accounting, dan Kasir.

### Catatan Observasi / Wawancara

| # | Divisi | Tanggal | Metode | Kedalaman | Keterangan |
|---|---|---|---|---|---|
| 1 | **Iklan** | Oktober 2026 | Wawancara langsung | Garis besar | Alur kerja umum, form fisik, double-entry problem |
| 2 | **Keuangan** | — | — | Belum | Jadwal wawancara belum ditentukan |
| 3 | **Accounting** | — | — | Belum | Jadwal wawancara belum ditentukan |
| 4 | **Sirkulasi** | Oktober 2026 | Wawancara langsung | Garis besar | Alur kerja umum saja, belum detail per menu |
| 5 | **Kasir** | — | — | Belum | Jadwal wawancara belum ditentukan |

### Yang Masih Perlu Dilakukan (Observasi)

- [ ] Wawancara lanjutan Divisi **Iklan** — detail tiap fitur/menu aplikasi
- [ ] Wawancara lanjutan Divisi **Sirkulasi** — detail tiap fitur/menu aplikasi
- [ ] Wawancara pertama Divisi **Keuangan**
- [ ] Wawancara pertama Divisi **Accounting**
- [ ] Wawancara pertama Divisi **Kasir**
- [ ] Konfirmasi detail role & hak akses setiap role (6 role)

---

---

## 1. Ringkasan Eksekutif & Latar Belakang

Sistem Informasi Manajemen Iklan Kabar Priangan (**SI KP**) dibangun untuk menggantikan sistem lama era 1990-an menjadi platform berbasis web modern (Laravel 11, MySQL). Kabar Priangan adalah grup media multimedia di **Priangan Timur** yang menaungi:

1. **Koran Cetak** — Harian Umum Kabar Priangan (terbitan harian).
2. **Media Online** — Portal berita digital dengan iklan display & advertorial.
3. **Priangan TV** — Saluran TV lokal dengan spot iklan, talkshow, dan blocking time program.

Terdapat dua saluran pemasangan iklan utama:
- **Front Office / Counter (Walk-In):** Pemasang datang langsung ke kantor, isi formulir kertas (2 jenis), lalu staf menginput ulang ke sistem *(masalah utama: double-entry)*.
- **Outdoor Sales / AE:** Tim sales mendatangi Pemda, BUMN, korporasi, dan agensi periklanan.

---

## 2. Akar Masalah: Kerja Dua Kali (Double-Entry Inefficiency)

Berdasarkan **formulir fisik asli** yang masih digunakan saat ini:

| Masalah | Dampak |
|---|---|
| Ketik ulang ~15 field dari kertas ke komputer | ±8-15 menit terbuang per order |
| Antri counter saat staf mengetik | Potensi klien pergi ke kompetitor |
| Naskah baris harus diketik ulang lagi di pracetak | Risiko typo nama/nomor di koran |
| Hitung tarif manual kalkulator | Salah hitung = selisih tagihan |
| PPN di form kertas masih 10% | Salah pajak, sudah harus 12% |
| Kertas bisa hilang di perjalanan FO ke Pracetak | Iklan batal terbit, komplain klien |

> Prinsip Solusi: "Staf Front Office input DATA SEKALI. Sistem otomatis hitung tarif, cetak kwitansi, kirim ke antrean pracetak, generate SPPI, dan catat ke laporan keuangan — tanpa ketik ulang."

---

## 3. Bedah 2 Formulir Fisik Asli Kabar Priangan

### 3.1. Formulir 1 — Iklan Baris (Classified Ads)

| # | Field di Form Kertas | Input Digital | Catatan |
|---|---|---|---|
| 1 | Grid kotak karakter (baris x kolom) | Textarea + live counter karakter/baris real-time | Fitur terpenting Form 1 |
| 2 | Jumlah Baris (centang: 3/4/5/6/7) | Otomatis terhitung dari textarea | Tidak perlu isi manual |
| 3 | Nama Perusahaan | Autocomplete dari master pelanggan | |
| 4 | Produk / Jasa | Text input | |
| 5 | Nama / Jabatan | Text input | |
| 6 | Alamat | Autocomplete dari master pelanggan | |
| 7 | No. Telp / Fax / HP | Text input | Nomor yang tampil di koran |
| 8 | Jumlah Pemuatan (kali terbit) | Number input | |
| 9 | Kop. Iklan / Kota | Dropdown (Tasikmalaya/Ciamis/Garut...) | |
| 10 | Edisi Tanggal Muat | Multi-date picker | Form fisik: centang angka 1-31 |
| 11 | Harga: baris x Rp. | Auto-kalkulasi | |
| 12 | PPN | Auto-kalkulasi (12%, bisa dikonfigurasi) | Form kertas masih 10% wajib update |
| 13 | Total Harga | Auto-kalkulasi | |
| 14 | Diskon & Formula | Input diskon + kalkulasi | Per jenis: umum / kuantitas / agensi |
| 15 | Tanda tangan | Status flow digital + nama petugas FO | |
| 16 | Kanal (AE/KORP/BIRO IKLAN/FO) | Dropdown wajib pilih | Menentukan tarif & komisi |

### 3.2. Formulir 2 — Iklan Kolom, Display & Advertorial

| # | Field di Form Kertas | Input Digital | Catatan |
|---|---|---|---|
| 1-5 | Nama, Produk, Jabatan, Alamat, Telp | Autocomplete master pelanggan | |
| 6 | Jenis Iklan (Kolom/Display/Advertorial) | Radio button 3 pilihan | Menentukan rumus tarif |
| 7 | Jenis Harga (Normal/Paket) | Radio button | |
| 8 | Jenis Pembayaran (Tunai/Kredit) | Radio button | Kredit -> wajib ada CBP gate |
| 9 | Tanggal Pembayaran | Date input | Jatuh tempo jika kredit |
| 10 | Keterangan Materi Iklan | Textarea besar | Instruksi desain / info file |
| 11 | Ukuran: kolom x mm x Rp. | Kalkulator mmk terpadu — input kolom + mm -> harga otomatis | Fitur terpenting Form 2 |
| 12 | Diskon % | Number + auto-kalkulasi | Dibedakan: agensi / kuantitas / khusus |
| 13 | PPN | Auto-kalkulasi (12%, bisa dikonfigurasi) | |
| 14 | Materai | Checkbox (otomatis muncul jika nilai >= Rp 5 juta) | |
| 15 | Total | Auto-kalkulasi | |
| 16 | Jumlah Pemuatan | Number input | |
| 17 | Kop. Iklan / Kota | Dropdown | |
| 18 | Edisi Tanggal Muat | Multi-date picker | |
| 19 | Approval (Diterima & Disetujui) | Status transaksi + nama petugas | |
| 20 | Kanal (AE/KORP/BIRO IKLAN/FO) | Dropdown wajib | |

---

## 4. Alur Kerja End-to-End (Standar Industri Pers Indonesia)

```
TAHAP 1: ORDER & BOOKING
Klien Datang ke Counter
  -> Staf FO input Form Digital (Baris ATAU Kolom/Display/Adv)
  -> Sistem hitung tarif mmk / per baris + PPN 12% otomatis
  -> Sistem cek Advertising Deadline Cut-off (14.00 WIB)
  -> Sistem generate SPPI/IO PDF otomatis
  -> Klien tandatangani SPPI (bukti kontrak legal)

TAHAP 2: PEMBAYARAN (CBP GATE)
  -> Jika Tunai/Walk-in: WAJIB bayar lunas dulu -> baru masuk antrean pracetak
  -> Jika Kredit (Agensi/Pemda): manajer approve bypass CBP -> order masuk antrean
  -> Kasir cetak kwitansi digital

TAHAP 3: PRODUKSI & TRAFFIC
  -> Tim pracetak buka Dashboard Antrean
  -> Koordinasi halaman dengan redaksi (Ad-to-News ratio)
  -> Jika materi siap cetak: download file, layout langsung
  -> Jika butuh didesain: buat desain, kirim Digital Proof ke klien, tunggu approve
  -> Upload Bukti Muat setelah terbit (scan kliping / link URL / log TV)

TAHAP 4: PENAGIHAN & PELUNASAN
  -> Kirim Invoice + Lampiran Bukti Muat (Tear Sheet) ke klien
  -> Export data CSV untuk e-Faktur DJP
  -> Klien bayar (dipotong PPh 23 2% jika Pemda/PT)
  -> Kasir catat pelunasan -> piutang = 0
```

---

## 5. Empat Gap Kritis dari Analisis Industri (WAJIB Ditambahkan)

### 5.1. SPPI / Insertion Order — Dokumen Kontrak Legal

**Standar Industri:** Di seluruh perusahaan media Indonesia profesional (Kompas, PR Group, Jawa Pos), tidak ada satu pun iklan yang naik cetak tanpa SPPI yang ditandatangani klien.

**Risiko Tanpa SPPI:** Klien Pemda atau BUMN mengklaim "tidak pernah memesan iklan itu" — KP tidak punya bukti hukum untuk menagih.

**Konten Minimum SPPI Kabar Priangan:**
- Nomor SPPI (auto-generate, format: SPPI/KRN/2025/001)
- Nama, Alamat & NPWP Pemasang
- Spesifikasi Iklan (jenis, ukuran, warna, halaman, jadwal muat)
- Harga Bruto, Diskon, PPN, Total
- Klausul deadline materi dan klausul koreksi
- Tanda tangan Pemasang + Stempel & Tanda tangan Sektor Penjualan KP

**Implementasi di SI KP:**
- Setelah form disimpan -> auto-generate PDF SPPI
- Tambahkan field `status_sppi` (ENUM: Belum_Ditandatangani/Sudah_Ditandatangani)
- Iklan tidak boleh masuk antrean pracetak sebelum SPPI ditandatangani (kecuali klien dengan credit limit disetujui manajemen)

---

### 5.2. Cash Before Print (CBP) Gate — Gerbang Kasir

**Standar Industri SOP Pembayaran:**

| Jenis Klien | Kebijakan |
|---|---|
| Walk-in / UMKM / Perorangan | Wajib lunas sebelum iklan masuk pracetak |
| Agensi Terverifikasi | Net 30-60 hari (ada credit limit disetujui manajemen) |
| Instansi Pemda / BUMN | Invoice pasca-tayang, sesuai mekanisme APBD |

**Implementasi di SI KP:**
- Status order baru default = Menunggu_Pembayaran
- Sistem mengunci order dari antrean pracetak jika status = Menunggu_Pembayaran DAN jenis_pembayaran = Tunai
- Setelah kasir catat pembayaran lunas -> status otomatis berubah -> order masuk antrean
- Untuk klien kredit: field `disetujui_kredit_oleh` (user manajer) sebagai syarat bypass CBP gate
- Alert jika total piutang klien melebihi `credit_limit` di master pelanggan

---

### 5.3. Advertising Deadline & Cut-off Harian

**Standar Industri:** Koran harian memiliki jam cut-off iklan ketat (biasanya 14.00-16.00 WIB) untuk terbit esok hari.

**Implementasi di SI KP:**
- Setting `jam_cutoff_iklan` di tabel `sistem_config` (default: 14:00, bisa diubah admin)
- Saat form transaksi dibuka, sistem tampilkan notifikasi:
  - Sebelum cut-off: "Order hari ini bisa terbit esok [tanggal]. Batas materi: 14.00 WIB."
  - Setelah cut-off: "Sudah melewati batas. Iklan akan terbit [tanggal lusa]."
- Tanggal muat default otomatis disesuaikan dengan status cut-off
- Dashboard Antrean Pracetak tampilkan countdown deadline per order

---

### 5.4. Export e-Faktur Pajak (Format DJP)

**Standar Industri:** Sebagai PKP, KP wajib menerbitkan Faktur Pajak Elektronik (e-Faktur) untuk setiap transaksi kena PPN.

**Konsekuensi:** Klien korporat/BUMN/Pemda tidak bisa mengkreditkan PPN masukan tanpa e-Faktur resmi. KP berisiko sanksi administrasi DJP.

**Implementasi di SI KP:**
- Tombol "Export e-Faktur (CSV)" di menu Laporan Keuangan
- Format CSV sesuai standar DJP yang bisa diimpor langsung ke aplikasi e-Faktur
- Field wajib di transaksi untuk e-Faktur: NPWP Pembeli, Nama Pembeli, Alamat, Kode JKP (jasa periklanan), DPP, PPN, Tanggal Faktur

---

## 6. Mapping Field Form Fisik -> Database (Action Items)

### Kolom BARU di `transaksikoran`:

| Field | Tipe | Keterangan |
|---|---|---|
| `pelanggan_id` | BIGINT FK | Refactor dari teks nama bebas |
| `input_oleh_user_id` | BIGINT FK | Staf FO yang menginput |
| `kanal_order` | ENUM | AE/KORP/BIRO_IKLAN/FO |
| `kategori_form` | ENUM | BARIS/KOLOM/DISPLAY/ADVERTORIAL |
| `naskah_iklan_baris` | TEXT | Isi naskah khusus baris |
| `jumlah_baris` | INT | Dihitung otomatis |
| `lebar_kolom` | INT | Satuan kolom (kolom/display) |
| `tinggi_mm` | INT | Satuan milimeter (kolom/display) |
| `jenis_harga` | ENUM | Normal/Paket |
| `jenis_pembayaran` | ENUM | Tunai/Kredit |
| `jatuh_tempo_pembayaran` | DATE | Jika kredit |
| `keterangan_materi` | TEXT | Instruksi desain / info file |
| `status_materi` | ENUM | Siap_Cetak/Butuh_Setting |
| `kop_iklan_kota` | VARCHAR | Edisi kota/wilayah |
| `tanggal_muat_list` | JSON | Multi-date terbit |
| `diskon_agensi` | DECIMAL | Biro iklan 15-20% |
| `diskon_umum` | DECIMAL | Diskon kuantitas/frekuensi |
| `diskon_khusus` | DECIMAL | Persetujuan manajemen |
| `materai` | DECIMAL | Nullable, muncul jika >= 5 juta |
| `ppn_persen` | DECIMAL | Default 12%, bisa dikonfigurasi |
| `status_order` | ENUM | Lifecycle lengkap |
| `status_sppi` | ENUM | Belum_Ditandatangani/Sudah |
| `disetujui_kredit` | BOOLEAN | Bypass CBP gate |
| `disetujui_kredit_oleh` | BIGINT FK | User manajer yang approve |
| `produk_jasa` | VARCHAR | Deskripsi produk diiklankan |

### Tabel BARU yang harus dibuat:

| Tabel | Fungsi |
|---|---|
| `pelanggan` | Master CRM klien & agensi (credit_limit, npwp, dll) |
| `sppi` | Dokumen kontrak legal SPPI / Insertion Order (PDF path) |
| `materi_pracetak` | Antrean & tracking desain di tim pracetak |
| `pembayaran_iklan` | Riwayat pembayaran cicilan/termin per faktur |
| `bukti_muat` | Lampiran kliping koran / link URL / log TV |
| `sistem_config` | Key-value: jam_cutoff, ppn_persen, tarif_mmk, dll |

---

## 7. Status Lifecycle Order (Lengkap)

```
Draft
  -> Menunggu_Bayar         (CBP gate aktif untuk Tunai)
  -> Menunggu_SPPI          (jika belum ditandatangani)
  -> Antrean_Pracetak       (sudah bayar/kredit disetujui + SPPI ok)
  -> Menunggu_Proof         (jika butuh desain)
  -> Proof_Dikirim          (digital proof terkirim ke klien)
  -> Proof_Disetujui        (klien approve)
  -> Sedang_Setting         (tim pracetak layout)
  -> Siap_Terbit            (siap masuk halaman koran)
  -> Terbit                 (iklan sudah tercetak/tayang)
  -> Ditagih                (invoice dikirim ke klien)
  -> Lunas                  (pembayaran diterima penuh)
  -> Komplain               (ada masalah, perlu investigasi)
```

---

## 8. Roadmap Implementasi Bertahap

> **Lingkup Saat Ini:** Hanya Divisi Iklan. Divisi lain akan ditambahkan ke roadmap setelah wawancara detail selesai.

### 🔨 Fase 1 — Divisi Iklan: Fondasi Digital & Operasional FO *(SEDANG BERJALAN)*

| # | Fitur | Status |
|---|---|---|
| 1.1 | Migrasi: tambah semua kolom baru di `transaksikoran` | ✅ DONE |
| 1.2 | Buat tabel `pelanggan` (Master CRM) + CRUD + autocomplete | 🔨 IN PROGRESS |
| 1.3 | Form A: Iklan Baris Digital (live counter, tarif otomatis, multi-date) | ⏳ TODO |
| 1.4 | Form B: Kolom/Display/Adv Digital (kalkulator mmk, warna BW/FC) | ⏳ TODO |
| 1.5 | CBP Gate: blokir antrean sampai bayar lunas (jika Tunai) | ⏳ TODO |
| 1.6 | Auto-generate PDF SPPI setelah transaksi disimpan | ⏳ TODO |
| 1.7 | Advertising Deadline Notifikasi (cut-off 14.00 WIB, configurable) | ⏳ TODO |
| 1.8 | Cetak kwitansi kasir digital | ⏳ TODO |
| 1.9 | Tabel & form `pembayaran_iklan` (cicilan/termin) | ⏳ TODO |
| 1.10 | PPN configurable via `sistem_config` (tidak hardcode) | ⏳ TODO |

### ⏸️ Fase 2 — Divisi Iklan: Pracetak, Bukti Muat & Penagihan *(Menunggu Fase 1 Selesai)*

| # | Fitur | Status |
|---|---|---|
| 2.1 | Dashboard Antrean Pracetak (paperless, naskah baris copy-paste) | ⏳ TODO |
| 2.2 | Status tracking desain (Menunggu/Sedang/Siap Terbit) | ⏳ TODO |
| 2.3 | Digital Proof: kirim pratinjau ke klien, klien approve sebelum cetak | ⏳ TODO |
| 2.4 | Upload Bukti Muat (kliping PDF / link URL / log TV) | ⏳ TODO |
| 2.5 | Export CSV e-Faktur format DJP | ⏳ TODO |
| 2.6 | Catatan PPh 23 (potongan 2% untuk Pemda/BUMN) | ⏳ TODO |
| 2.7 | Relasi Sales ke User + komisi berbasis pelunasan (bukan dari faktur) | ⏳ TODO |
| 2.8 | Laporan Aging Piutang (0-30 / 31-60 / 61-90 / >90 hari) | ⏳ TODO |

### ⏸️ Fase 3 — Divisi Sirkulasi *(Menunggu Wawancara Detail)*

> Belum bisa didetailkan. Kebutuhan fitur masih garis besar dari observasi awal.

| # | Fitur | Status |
|---|---|---|
| 3.x | *(Akan diisi setelah wawancara detail Divisi Sirkulasi)* | 🔍 PERLU OBSERVASI |

### ⏸️ Fase 4 — Divisi Lainnya *(Belum Diobservasi)*

> Menunggu jadwal wawancara ke Divisi Keuangan, Accounting, dan Kasir.

| # | Divisi | Status |
|---|---|---|
| 4.x | **Keuangan** | ❌ Belum diobservasi |
| 4.x | **Accounting** | ❌ Belum diobservasi |
| 4.x | **Kasir** | ❌ Belum diobservasi |

### ⏸️ Fase 5 — Administrator & Lintas Divisi

| # | Fitur | Status |
|---|---|---|
| 5.1 | Role & Permission Management (6 role) | ⏳ TODO |
| 5.2 | Dashboard Admin (ringkasan semua divisi) | ⏳ TODO |
| 5.3 | Manajemen User & Akun | ⏳ TODO |
| 5.4 | Konfigurasi Sistem (PPN, cut-off, dll) | ⏳ TODO |

### 🚀 Fase 6 — Enterprise Features *(Jangka Panjang)*

| # | Fitur | Status |
|---|---|---|
| 6.1 | Paket Bundling 3-in-1 (1 kontrak = Koran + Online + TV) | ⏳ TODO |
| 6.2 | Kalender Kapasitas Halaman (cegah over-booking cetak) | ⏳ TODO |
| 6.3 | Sales Performance Dashboard (target vs realisasi per AE) | ⏳ TODO |
| 6.4 | Modul Komplain & Kompensasi (run-on correction) | ⏳ TODO |
| 6.5 | Laporan Manajemen (fill rate, omset per kanal, per media) | ⏳ TODO |

---

## 9. Catatan Teknis Penting

**PPN — Jangan Hardcode:**
Simpan di tabel `sistem_config` dengan key `ppn_persen`. Nilai sekarang 12% (berlaku 1 Januari 2025). Bisa berubah kapan saja tanpa edit kode.

**Diskon — 3 Jenis Berbeda:**
- `diskon_agensi`: Otomatis 15-20% jika kanal = BIRO_IKLAN.
- `diskon_umum`: Diskon kuantitas/frekuensi.
- `diskon_khusus`: Input manual, hanya oleh role Manajer/Admin.

**Kanal Order — Konsekuensi Berbeda:**
- FO: CBP wajib, tarif normal, tidak ada diskon agensi.
- AE: Komisi individual, CBP wajib kecuali kredit disetujui.
- KORP: Harga paket korporat, credit limit disetujui manajemen.
- BIRO IKLAN: Diskon agensi 15-20% otomatis, pembayaran net 30-60 hari.

**Advertising Deadline:**
Konfigurasi `jam_cutoff_iklan = 14:00` di `sistem_config`. Bisa diubah admin tanpa coding.

**e-Faktur Export — Field Wajib:**
NPWP Penjual, NPWP Pembeli, Nama Pembeli, Alamat, Kode JKP (jasa iklan), DPP, PPN, tanggal faktur, nomor seri faktur pajak.
