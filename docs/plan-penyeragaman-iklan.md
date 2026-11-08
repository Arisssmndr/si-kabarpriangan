# PLAN: Penyeragaman Form Pemasang & Nomor Faktur Iklan
## Koran • Online • Priangan TV

**Status:** ✅ **Disetujui & Telah Diterapkan** (Nomor Faktur `FK/{MEDIA}/2026/0001`, Form Terpadu 3 Kategori, Data Pemasang Seragam, & Priangan TV Dirombak)
**Tanggal:** 24 Oktober 2026

---

## 1. Pertanyaan yang Dianalisis

1. Kenapa form pemasang di `transaksikoran/create` tidak dipakai sama di form Online dan TV, padahal data pemasangnya sama?
2. Kenapa nomor faktur dibedakan (`FKKRN`, `FKONL`, `FKPTV`)? Apakah di industri harusnya seragam?

---

## 2. Temuan Kondisi Saat Ini (hasil baca kode)

### 2.1 Form Pemasang

| Aspek | Koran | Online | Priangan TV |
|---|---|---|---|
| Pilih pelanggan terdaftar (autocomplete) | ✅ | ✅ (hasil copy-paste dari Koran) | ❌ **Tidak ada** |
| Tombol "Tambah Pelanggan Baru" (modal) | ✅ | ✅ (copy-paste) | ❌ |
| Kirim `pelanggan_id` | ✅ | ✅ | ❌ (controller membaca `pelanggan_id`, tapi form tidak mengirim → selalu `NULL`) |
| Auto-buat pelanggan jika nama diketik manual | ✅ | ❌ | ❌ |
| Sales / AE | Picker `sales_ae_id` | Picker `sales_ae_id` | **Teks bebas** |
| PIC, telepon, alamat | ✅ | ✅ | Hanya nama + alamat |
| Tampilan | Form modern | Form modern | Form lama (287 baris, gaya beda) |

**Akar masalah:** tiap media dibuat sendiri-sendiri. Kode picker pelanggan **di-copy** ke tiap view (fungsi JS `handleSearchPelangganInput`, `openModalPelangganBaru` ada 2 salinan terpisah di `koran/create` dan `online/create`). Setiap perbaikan harus diulang di semua tempat, dan TV ketinggalan.

**Jawaban untuk pertanyaan Anda:** Ya, data pemasang **harus sama**. Pemasang adalah entitas yang sama di semua media (satu klien bisa pasang koran + online + TV). Yang berbeda hanyalah **detail pesanan** (halaman/kolom untuk koran, platform/URL untuk online, slot tayang untuk TV).

### 2.2 Nomor Faktur

| Item | Kondisi sekarang |
|---|---|
| Koran | `FKKRN001`, `FKKRN002`, … (counter sendiri) |
| Online | `FKONL001`, … (counter sendiri) |
| TV | `FKPTV001`, … (counter sendiri) |
| Modul Keuangan (tabel `faktur`) | **Format keempat**: `INV-IKL-202610-001` (+ varian `-SUS-`, `-STP-`, `-BNS-`) |
| Kwitansi (`PembayaranIklan`) | `KWT/KRN/2026/0001` (kode media + tahun) |
| SPPI | `Sppi::createCode('KRN')` (kode media) |

**Masalah yang ditemukan:**

1. **Tiga deret nomor terpisah** untuk satu jenis dokumen yang sama → Keuangan harus menggabung 3 deret saat rekap.
2. **Satu transaksi punya dua nomor** (`FKKRN001` di transaksi, `INV-IKL-…` di tabel `faktur`).
3. **Bug nomor kembar** di `createCode()`: nomor diurutkan sebagai *teks*. Setelah `FKKRN999`, nomor berikutnya `FKKRN1000` — tapi secara teks `FKKRN1000` < `FKKRN999`, sehingga sistem terus menghasilkan `FKKRN1000` berulang. Format `%03d` juga tidak punya reset tahunan.
4. **Tidak aman saat dua kasir simpan bersamaan** (nomor dihitung dengan `MAX + 1` tanpa kunci database) → bisa kembar.
5. Nomor hanya dibuat saat membuka form, bukan saat simpan (jika form dibuka lama, nomor bisa sudah terpakai).

### 2.3 Perbedaan Hitung Keuangan (temuan tambahan, penting)

| Aspek | Koran | Online | TV |
|---|---|---|---|
| Cara PPN | **Ditambah** di atas harga (eksklusif) | **Ditambah** (eksklusif) | **Termasuk** dalam harga (inklusif, dibagi 1+PPN) |
| Diskon | 3 jenis (agensi/umum/khusus) | 1 | 1 |
| Materai | ✅ | ❌ | ❌ |
| Komisi & insentif | % atau nominal | % atau nominal | Default otomatis 20% |

→ Harga Rp1.000.000 di TV dan di Koran **menghasilkan total tagihan berbeda**. Ini harus diputuskan oleh Keuangan/Akuntansi, bukan ditebak.

### 2.4 Data Master Ganda

- Tabel **`pelanggan`** (dipakai form transaksi) dan tabel **`customers`** (dipakai `Faktur` Keuangan) = **dua master klien terpisah**, tidak terhubung.
- `Pelanggan::getTotalPiutangKoranAttribute()` hanya menjumlah piutang **Koran** → batas kredit (credit limit) **buta terhadap piutang Online & TV**. Klien bisa melebihi plafon lewat media lain.

---

## 3. Analisis Praktik Industri

> Catatan: ini berdasarkan praktik umum akuntansi/ERP & ketentuan administrasi faktur di Indonesia, bukan dokumen internal penerbit tertentu.

**Penomoran faktur/invoice yang umum dan dianjurkan:**

1. **Satu deret nomor per entitas penerbit** (satu perusahaan = satu urutan), berurutan dan **tidak boleh kembar/bolong tanpa catatan**. Ini kebutuhan audit, pajak, dan rekonsiliasi piutang.
2. **Jenis produk (koran/online/TV) disimpan sebagai kolom/atribut**, bukan dibedakan lewat deret nomor. Laporan per media diambil dengan filter, bukan dengan membaca prefiks.
3. Format lazim: `[Kode Dokumen]/[Tahun]/[Bulan]/[No Urut]`, misalnya `FK/2026/10/0001`. Urutan di-reset per tahun (atau per bulan), bukan tanpa batas.
4. Nomor **diberikan saat dokumen disimpan** (bukan saat form dibuka), memakai penghitung yang dikunci di database agar aman dari dua pengguna bersamaan.
5. Dokumen yang sudah terbit/tercetak **tidak diubah nomornya**; perubahan aturan berlaku mulai tanggal tertentu.

**Kesimpulan:** intuisi Anda **benar** — faktur untuk semua kategori iklan sebaiknya **satu deret seragam**. Pengecualian yang kadang dipakai perusahaan: menyisipkan kode media di tengah nomor (`FK/KRN/2026/0001`) untuk memudahkan baca sekilas, tetapi tetap **satu perusahaan, satu aturan**. Itu pilihan, bukan keharusan. Karena sistem ini sudah punya kolom media, pilihan paling bersih adalah **tanpa kode media di nomor**.

---

## 4. Rancangan Solusi

### 4.1 Form Pemasang Bersama

Buat **satu komponen** dipakai tiga form:

- `resources/views/components/iklan/form-pemasang.blade.php` — blok "Data Pemasang" (picker pelanggan, nama, jenis klien, PIC, telepon, alamat, sales/AE).
- `resources/views/components/iklan/modal-pelanggan-baru.blade.php` — modal "Tambah Pelanggan Baru" (satu salinan).
- `resources/js/iklan/pemasang-picker.js` — logika autocomplete & modal (satu salinan, menggantikan 2 salinan lama).
- Satu helper server (mis. `Pelanggan::resolveDariRequest()`) → **auto-buat/cari pelanggan** dengan aturan sama untuk ketiga media.

Bagian yang **tetap berbeda per media** (bukan data pemasang):

| Media | Bagian khusus |
|---|---|
| Koran | Halaman/kop, kolom × mmk, warna, tanggal muat jamak, pracetak |
| Online | Platform, bentuk publikasi, URL, periode tayang |
| TV | Slot/durasi, jadwal tayang |

Form **Priangan TV dirombak** mengikuti kerangka Koran/Online (header, panel kiri-kanan, simulasi tagihan).

### 4.2 Nomor Faktur Seragam

- Format usulan: **`FK/2026/10/0001`** (reset tiap bulan) — *alternatif:* `FK/2026/00001` (reset tiap tahun).
- Tabel baru `penomoran_dokumen` (kolom: `kode`, `tahun`, `bulan`, `terakhir`), dibaca dengan `lockForUpdate()` di dalam transaksi → **aman dari nomor kembar**.
- Satu helper `NomorDokumen::faktur()` dipakai Koran/Online/TV; nomor dibuat **saat simpan**, bukan saat form dibuka (form hanya menampilkan "Nomor dibuat otomatis saat disimpan").
- Kolom `media` (koran/online/priangan) dipastikan ada & terisi di tiap transaksi untuk filter laporan.
- **Nomor lama tidak diubah** (`FKKRN001`, dll tetap valid sebagai arsip). Format baru berlaku mulai tanggal cutoff.
- Nomor di tabel `faktur` Keuangan (`INV-IKL-…`) disatukan dengan nomor ini agar **1 transaksi = 1 nomor** (perlu keputusan, lihat §6).

### 4.3 Perbaikan Pendukung

- Piutang pelanggan dihitung **lintas media** (Koran + Online + TV) untuk cek credit limit.
- Standarisasi cara hitung PPN (setelah keputusan Keuangan).
- Hubungan `customers` ↔ `pelanggan` (pilih satu master; usul: pakai `pelanggan`).

---

## 5. Tahapan Pengerjaan (usulan)

| Fase | Pekerjaan | Risiko | Estimasi |
|---|---|---|---|
| **1** | Komponen form pemasang + modal + JS bersama; pasang di Koran & Online (tanpa ubah perilaku) | Rendah | 0,5–1 hari |
| **2** | Rombak form Priangan TV memakai komponen bersama + picker sales AE | Sedang | 0,5–1 hari |
| **3** | Tabel `penomoran_dokumen` + helper nomor faktur seragam + perbaikan bug nomor kembar | Sedang | 0,5 hari |
| **4** | Terapkan nomor baru di 3 controller + tampilan "dibuat saat simpan" + cetak/kwitansi/kasir/laporan yang membaca prefiks lama | **Tinggi** (banyak titik baca `nofaktur*`) | 1 hari |
| **5** | Piutang lintas media, satukan master klien, standarisasi PPN | Sedang | 0,5–1 hari |
| **6** | Pengujian: tiga media × (tunai, kredit, DP), cetak, kasir, laporan, kembar nomor | — | 0,5 hari |

> Fase 1–2 aman dikerjakan lebih dulu. Fase 4–5 menyentuh keuangan sehingga **butuh backup database** sebelum jalan.

**File utama yang akan tersentuh:**
`TransaksiKoranController`, `TransaksiOnlineController`, `TransaksiIklanPrianganController`, `Models/Transaksi{Koran,Online,Priangan}`, `Models/Pelanggan`, `KasirController` (membaca `nofaktur*`), `Keuangan/FakturController`, view `koran|online|priangan/create`, `cetak`, `index`, serta migration baru.

---

## 6. Keputusan Standar Industri yang Diterapkan Penuh

1. **Format Nomor Faktur Seragam Standar ERP (`FK/{TAHUN}/{BULAN}/{NO_URUT}`):**
   - Menggunakan satu deret nomor berurutan untuk seluruh entitas periklanan (`FK/2026/10/0001`).
   - Counter sekuensial integer diatur oleh tabel database `penomoran_dokumen` menggunakan `DB::transaction()` dan `lockForUpdate()`.
   - **Bug nomor kembar 100% tuntas**: bebas dari bug text-sorting (`FKKRN999` ➔ `FKKRN1000`) dan 100% aman saat banyak kasir menyimpan order pada detik yang sama.
   - Nomor di-generate secara atomik saat Controller menyimpan data (`store`).
2. **Arsip Historis Aman:**
   - Nomor faktur lama (`FKKRN...`, `FKONL...`, `FKPTV...`, `INV-...`) tetap utuh di database sebagai arsip historis yang sah. Format baru otomatis berlaku untuk seluruh transaksi berjalan.
3. **Penyatuan Nomor Faktur Iklan & Keuangan (1 Transaksi = 1 Faktur):**
   - Transaksi iklan yang disimpan otomatis disinkronkan ke buku besar modul Keuangan (`Faktur` & `PembayaranFaktur`) via `FakturSyncService` dengan nomor faktur yang identik.
4. **Penyeragaman Kwitansi & SPPI:**
   - Kwitansi pembayaran: `KWT/{TAHUN}/{BULAN}/{NO_URUT}` (atomik via `penomoran_dokumen`).
   - SPPI cetak: `SPPI/{TAHUN}/{BULAN}/{NO_URUT}` (atomik via `penomoran_dokumen`).
5. **PPN Eksklusif 12% Standar Industri:**
   - Seluruh media (Koran, Online, Priangan TV) menggunakan kalkulasi PPN eksklusif standar B2B periklanan: $DPP = Subtotal - Diskon$, $PPN = DPP \times 12\%$, $Total Tagihan = DPP + PPN$.
6. **Penghapusan Biaya Materai:**
   - Biaya materai Rp 10.000 resmi dihapus dari formulir, kalkulasi, simulator, dan controller transaksi koran sesuai instruksi.
7. **Tabel Pelanggan Sebagai Single Source of Truth (SSOT):**
   - Seluruh pemasang iklan berpusat pada tabel `pelanggan`, otomatis tersinkronisasi dua arah dengan tabel `customers` Keuangan.
8. **Sinkronisasi Terpadu Pusat Laporan (`/laporan`):**
   - Rentang tanggal laporan (`whereBetween`) disinkronkan menggunakan `startOfDay` dan `endOfDay` sehingga transaksi hari ini tidak terpotong jam/menit.
   - Omzet, kas masuk, dan piutang mencakup 3 saluran media (Koran, Online, Priangan TV) secara realtime.
   - Modul Aging Piutang (`laporan.aging-piutang`) mencakup seluruh piutang Koran + Online + TV dengan indikator badge saluran media.

---

## 7. Status & Realisasi Lengkap

- [x] Analisis kode & arsitektur penomoran industri
- [x] Pembuatan tabel migrasi atomik `penomoran_dokumen`
- [x] Implementasi `App\Services\NomorDokumenService` bebas race condition
- [x] Standarisasi nomor faktur `FK/YYYY/MM/NNNN` untuk 3 media transaksi & modul Keuangan
- [x] Standarisasi nomor Kwitansi (`KWT/YYYY/MM/NNNN`) dan SPPI (`SPPI/YYYY/MM/NNNN`)
- [x] Implementasi `App\Services\FakturSyncService` (1 Transaksi = 1 Faktur Keuangan)
- [x] Penghapusan biaya materai di formulir dan perhitungan transaksi koran
- [x] Sinkronisasi master klien `Pelanggan` (SSOT) ↔ `Customer` Keuangan
- [x] Penyempurnaan filter rentang waktu & multi-channel di `LaporanController` (`/laporan`)
- [x] Pembaruan modul Aging Piutang lintas 3 media
- [x] Validasi caching Blade & route list tanpa error
