# Style Guide & UI/UX Requirement System — SiKAPRI

**Patokan Resmi Desain Antarmuka, Tipografi, Warna, dan Komponen (Acuan Audit Seluruh Halaman Role Iklan)**

Dokumen ini disusun berdasarkan hasil audit menyeluruh terhadap **51 file Blade pada modul/role Iklan** (31 halaman layar interaktif, 1 alias edit, 15 dokumen cetak, 4 partial) serta konfigurasi global pada:

- `tailwind.config.js`
- `resources/css/app.css`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/navigation.blade.php`
- `resources/views/components/action-dropdown.blade.php`

Dokumen ini menjadi **patokan tunggal (*Single Source of Truth*)** agar seluruh halaman di Divisi Iklan maupun divisi lain (Sirkulasi, Accounting, Keuangan/Faktur, Kasir, dan Admin) memiliki tampilan yang seragam, bersih, dan konsisten.

---

## 1. Ringkasan Cakupan Audit Role Iklan

| Kategori Halaman                             | Jumlah | Keterangan File                                                                                                                                                                                                                                                                                                                           |
| -------------------------------------------- | -----: | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Halaman Layar (`<x-app-layout>`)** |     31 | `dashboard`, 6 halaman `laporan/*`, 7 halaman `master/*` + `pages/master/sales-ae/index`, `memo/history`, `pengaturan/index`, `rekap_pracetak/index`, serta 13 halaman `transaksi/{koran,online,paket,priangan}/{index,create,show}` + `pembayaran_index` (+ `koran/edit.blade.php` alias `@include` ke `create`) |
| **Dokumen Cetak (*Print Views*)**    |     15 | 6 laporan cetak (`laporan/cetak*`), 2 pracetak (`rekap_pracetak/cetak`, `cetak_memo`), 4 cetak faktur media (`{koran,online,paket,priangan}/cetak`), `koran/kwitansi`, `koran/sppi`, `koran/lembar_kerja_pracetak`                                                                                                          |
| **Partials / Modal / Filter**          |      4 | `laporan/_nav_tabs`, `laporan/_period_filter`, `transaksi/partials/modal_pelanggan_baru`, `transaksi/partials/modal_sales_ae_baru`                                                                                                                                                                                                |

---

## 2. Sistem Font (*Font Family*)

### 2.1 Aturan Baku Font Layar & Dokumen

| Konteks Penggunaan                                           | Font Family Resmi                                                     | Konfigurasi / Class                               | Sumber                                                                                        |
| ------------------------------------------------------------ | --------------------------------------------------------------------- | ------------------------------------------------- | --------------------------------------------------------------------------------------------- |
| **UI Web Utama (Seluruh Layar)**                       | `"Plus Jakarta Sans", Figtree, sans-serif`                          | `font-sans` (default `<body>`)                | `tailwind.config.js`, Google/Bunny Fonts di `app.blade.php`                               |
| **Angka Finansial, Kode Faktur, Tanggal, & ID**        | Monospace sistem / `JetBrains Mono`                                 | `font-mono tabular-nums` atau `.font-tabular` | `app.css`, view transaksi & dashboard                                                       |
| **Dokumen Cetak Continuous Form (Dot-Matrix)**         | `'Courier New', Courier, monospace, 'Lucida Console'`               | CSS `body { font-family: ... }`                 | `laporan/cetak*.blade.php`, `transaksi/*/cetak.blade.php`                                 |
| **Dokumen Cetak Modern (A4 / A5)**                     | `'Plus Jakarta Sans', sans-serif` + `'JetBrains Mono', monospace` | `font-sans` & `font-mono` via `@vite`       | `kwitansi.blade.php`, `sppi.blade.php`, `cetak_memo.blade.php`                          |
| **Pratinjau Miniatur Koran *(Pengecualian Khusus)*** | Serif standar sistem                                                  | `font-serif`                                    | Khusus simulasi tata letak kolom koran di `transaksi/koran/create.blade.php` (25 pemakaian) |

> **Aturan Penyeragaman:**
>
> 1. Seluruh halaman layar **dilarang** memakai `font-serif` untuk elemen UI aplikasi.
> 2. Semua nilai uang (`Rp ...`), nomor faktur (`FK/...`), nomor kwitansi (`KWT/...`), kode pelanggan/sales, dan angka KPI wajib menggunakan `font-mono tabular-nums` (atau `font-tabular`) agar kolom angka rata vertikal.
> 3. Bobot font pada font utama dimuat untuk skala `400, 500, 600, 700` di `app.blade.php` (hindari ketergantungan pada `font-black` / `font-extrabold` untuk teks layar biasa).

---

## 3. Skala Ukuran Teks & Bobot Tipografi (*Type Scale & Weights*)

Berdasarkan audit seluruh view layar Role Iklan, berikut distribusi aktual dan **standar patokan baku** per elemen:



### 3.1 Frekuensi Aktual di Layar Iklan (31 Halaman + 4 Partial)

- `text-xs` (12px): **1.127 kali** — dominan untuk badge, table cell sekunder, subjudul mobile, label helper, dan tombol aksi padat.
- `text-sm` (14px): **732 kali** — dominan untuk isi tabel (`td`), label form filter, input, select, dropdown, dan tombol utama.
- `text-2xl` (24px): **50 kali** — khusus angka utama pada KPI Summary Cards.
- `text-xl` (20px): **40 kali** — standar judul utama halaman (`<h1>`).
- `text-base` (16px): **35 kali** — penekanan nilai ringkasan/kalkulasi & nomor faktur di header form.
- `text-lg` (18px): **18 kali** — variasi KPI card pada grid sempit (5 kolom) & separator.
- `text-[11px]` (11px): **30 kali** — micro-label / table metadata tertentu.
- `text-[4.5px]`–`text-[9.5px]` (4.5px–9.5px): **khusus** simulasi visual halaman koran mini pada `transaksi/koran/create.blade.php`.

### 3.2 Matriks Standar Tipografi Wajib (Patokan Seragam)

| Elemen UI                                             | Ukuran Teks (`font-size`)                                       | Bobot (`font-weight`)     | Class Standar Baku                                                                                                                                                                                     | Catatan Konsistensi                                                                                                                                                                        |
| ----------------------------------------------------- | ----------------------------------------------------------------- | --------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **Judul Halaman (`<h1>`)**                    | `20px` (`1.25rem`)                                            | `700` (`Bold`)          | `text-xl font-bold text-slate-900 tracking-tight truncate`                                                                                                                                           | 28/31 halaman sudah memakai `text-xl`. Tambah `font-mono` jika isi `<h1>` adalah nomor faktur (`show.blade.php`).                                                                  |
| **Subjudul Halaman (`<p>`)**                  | `12px` mobile / `14px` desktop                                | `400` (`Normal`)        | `text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl`                                                                                                                             | Seragam di seluruh header halaman Iklan.                                                                                                                                                   |
| **Judul Section / Card (`<h2>`)**             | `14px` (`0.875rem`)                                           | `700` (`Bold`)          | `text-sm font-bold text-slate-900`                                                                                                                                                                   | Konsisten di 24 pemakaian `<h2>`.                                                                                                                                                        |
| **Judul KPI Card (Eyebrow)**                    | `12px` (`0.75rem`)                                            | `600` (`Semibold`)      | `text-xs font-semibold text-slate-500 uppercase tracking-wider block`                                                                                                                                | Selalu uppercase + `tracking-wider`.                                                                                                                                                     |
| **Angka KPI Card Utama**                        | `24px` (`1.5rem`)                                             | `700` (`Bold`)          | `text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums`                                                                                                                                    | Gunakan `text-kp-blue-700` untuk metrik utama/omset, `text-slate-900` untuk metrik netral/volume. Untuk grid 5 kolom sempit (`aging_piutang`), boleh `text-lg xl:text-xl`.         |
| **Sub-keterangan KPI Card**                     | `12px` (`0.75rem`)                                            | `400` (`Normal`)        | `text-xs text-slate-500 mt-1 block`                                                                                                                                                                  | Bisa memakai `text-slate-400` atau `text-slate-500`.                                                                                                                                   |
| **Label Form Input**                            | `14px` *(filter)* / `12px` *(form)*                       | `600` (`Semibold`)      | Filter: `text-sm font-semibold text-slate-700 mb-1.5`Form padat: `text-xs font-semibold text-slate-700 mb-1.5`                                                                                     | Disarankan ke depan menstandarkan **semua `<label>` ke `text-xs font-semibold text-slate-700 mb-1.5`** atau memisahkan aturan toolbar (`text-sm`) vs form entri (`text-xs`). |
| **Teks Input, Select, Textarea**                | `14px` (`0.875rem`)                                           | `400` (`Normal`)        | `text-sm text-slate-900 placeholder:text-slate-400`                                                                                                                                                  | Berlaku juga untuk komponen Select2 (`14px` di `app.blade.php`).                                                                                                                       |
| **Header Kolom Tabel (`<thead> th`)**         | `13px` *(global CSS)*                                         | `700` (`Bold`)          | Dikunci global di `app.blade.php` (`font-size: 13px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.04em !important; color: #FFFFFF !important;`) | Tidak perlu override ukuran/warna di tiap blade; cukup pasang `py-3 px-3.5`.                                                                                                             |
| **Isi Sel Tabel (`<tbody> td`)**              | `14px` (`0.875rem`)                                           | `400` / `600` / `700` | Wrapper `<table class="w-full text-left text-sm border-collapse">`, `<tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">`                                                   | No. faktur/order: `font-mono font-bold text-kp-blue-700 text-sm`. Kolom sekunder/nomor urut: `text-slate-500 font-normal`.                                                             |
| **Tombol Header & Aksi Utama**                  | `12px` mobile / `14px` desktop                                | `700` (`Bold`)          | `text-xs sm:text-sm font-bold`                                                                                                                                                                       | Tombol aksi sekunder memakai `font-semibold`.                                                                                                                                            |
| **Tombol Filter / Submit Toolbar**              | `14px` (`0.875rem`)                                           | `700` / `600`           | Primary: `text-sm font-bold` \| Reset: `text-sm font-semibold`                                                                                                                                     | Sejajar tinggi `h-11` dengan input toolbar.                                                                                                                                              |
| **Badge / Pill Status & Alert**                 | `12px` (`0.75rem`)                                            | `600` (`Semibold`)      | `text-xs font-semibold`                                                                                                                                                                              | Hindari ukuran lebih kecil dari `12px` (`text-[11px]`/`text-[10px]`) di konten tabel/card utama.                                                                                     |
| **Sidebar Navigasi (`navigation.blade.php`)** | `12px` item / `10px` grup & pill                              | `600`–`700`            | Item: `text-xs font-semibold` (aktif: `font-bold`)Label Grup: `text-[10px] font-bold uppercase tracking-wider`                                                                                   | Khusus komponen navigasi sidebar global.                                                                                                                                                   |
| **Topbar & Footer (`app.blade.php`)**         | User `14px` (`text-sm`), Role & Footer `12px` (`text-xs`) | `600` / `400`           | User: `text-sm font-semibold text-slate-900`Footer: `text-xs text-slate-600 font-normal`                                                                                                           | Tetap seragam lintas seluruh role.                                                                                                                                                         |

---

## 4. Sistem Warna (Aturan Proporsi **60–30–10**)

Seluruh tampilan layar Role Iklan mengikuti filosofi **korporat surat kabar modern (60–30–10)** yang didefinisikan di `tailwind.config.js`, `app.css`, dan `app.blade.php`.

### 4.1 Token Warna Resmi

#### A. 60% — Dominan Latar & Permukaan (*Canvas & Surfaces*)

| Token / Class                   | Nilai Hex   | Fungsi Baku                                                                                                                  |
| ------------------------------- | ----------- | ---------------------------------------------------------------------------------------------------------------------------- |
| `bg-slate-100`                | `#F1F5F9` | Latar belakang utama halaman (`<body>` di `app.blade.php`), tombol reset, badge status netral (`Kredit`, `Terkunci`) |
| `kp-canvas` (`bg-slate-50`) | `#F8FAFC` | Latar hover tabel (`hover:bg-slate-50/80`), hover tombol sekunder (`hover:bg-slate-50`), latar card minor                |
| `kp-surface` (`bg-white`)   | `#FFFFFF` | Permukaan utama seluruh Card Header, Card KPI, Card Toolbar, Card Tabel, dan Modal                                           |
| `border-slate-200`            | `#E2E8F0` | Garis tepi standar semua Card, Tabel container, Popover, Dropdown                                                            |
| `border-slate-300`            | `#CBD5E1` | Garis tepi Form Input, Select, Select2, dan tombol sekunder (`Batal`, `Reset`, `Kembali`, `Ubah`)                    |
| `border-slate-100`            | `#F1F5F9` | Garis pemisah internal card (`divide-slate-100`, `border-b border-slate-100`)                                            |

#### B. 30% — Tipografi Netral & Kontras

| Class              | Nilai Hex   | Fungsi Baku                                                                                                     |
| ------------------ | ----------- | --------------------------------------------------------------------------------------------------------------- |
| `text-slate-900` | `#0F172A` | Judul halaman (`<h1>`), judul section (`<h2>`), nilai input, teks utama bernilai penting                    |
| `text-slate-800` | `#1E293B` | Teks pesan sistem netral, nilai penekanan sekunder, teks tombol abu-abu                                         |
| `text-slate-700` | `#334155` | Label form (`<label>`), isi sel tabel utama (`<tbody>`), teks tombol sekunder, item aktif non-blue          |
| `text-slate-600` | `#475569` | Label sub-info, footer, keterangan tanggal/petugas sekunder                                                     |
| `text-slate-500` | `#64748B` | Subjudul halaman (`<p>`), label KPI card (eyebrow), nomor urut tabel, ikon netral                             |
| `text-slate-400` | `#94A3B8` | Placeholder input (`placeholder:text-slate-400`), satuan KPI (`Faktur`, `Order`, `mmk`), metadata minor |
| `text-slate-300` | `#CBD5E1` | Pemisah visual (`                                                                                               |

> **Catatan Audit vs `docs/tampilan.md` lama:**
> Dokumen lama (`docs/tampilan.md`) sempat merekomendasikan warna hitam mutlak (`text-black` / `#000000`) untuk semua teks layar. Namun dalam implementasi produksi saat ini di seluruh modul Iklan, Sirkulasi, dan Accounting, **standar yang berlaku nyata di layar** adalah palet **`slate-900` s/d `slate-500`** untuk menjaga hierarki visual modern, sementara **`#000000` (`text-black`)** khusus digunakan pada **dokumen cetak resmi (*print sheet*)**.

#### C. 10% — Warna Brand Resmi (`kp-blue`) & Aksen Korporat

| Token / Class                         | Nilai Hex   | Fungsi Baku                                                                                                                                                          |
| ------------------------------------- | ----------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `kp-blue-600` (`kp-accent`)       | `#0A72AC` | **Warna Primer Resmi**: tombol aksi utama (`bg-kp-blue-600`), header tabel (`thead` global), border focus input (`focus:border-kp-blue-600`), ikon utama |
| `kp-blue-700` (`kp-accent.hover`) | `#085C8D` | Hover tombol utama (`hover:bg-kp-blue-700`), teks tautan/nomor faktur (`text-kp-blue-700`), angka KPI finansial utama, teks badge aktif                          |
| `kp-blue-800`                       | `#094E75` | State `active:bg-kp-blue-800` pada tombol utama, teks status terbuka/notifikasi                                                                                    |
| `kp-blue-50`                        | `#F0F7FD` | Latar badge status positif/aktif (`Tunai`, `Siap Terbit`, `Terbuka`), latar alert sukses (`session('success')`), pilihan filter aktif                        |
| `kp-blue-200`                       | `#C2E0F6` | Border badge/alert `kp-blue-50`                                                                                                                                    |
| `kp-blue-100`                       | `#DDEEFA` | `selection:bg-kp-blue-100`, highlight sekunder                                                                                                                     |
| `#0E527B`                           | `#0E527B` | Warna solid Sidebar Navigasi (`bg-[#0E527B]`), border sidebar `#0B4365`, teks item aktif sidebar (`text-[#0E527B]` di atas `bg-white`)                       |
| `#FFB900`                           | `#FFB900` | Garis aksen emas korporat tepat di bawah header tabel global (`border-bottom: 3.5px solid #FFB900 !important`)                                                     |

#### D. Warna Semantik Terbatas (Hanya untuk Validasi & Status Kritis)

| Kategori                                        | Class Resmi                                                                                             | Kapan Boleh Dipakai                                                                                                                                                     |
| ----------------------------------------------- | ------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Required / Error / Hapus / Sorot Form** | `text-rose-500` (tanda `*`), `text-rose-600`/`700`/`800`, `bg-rose-50`, `border-rose-200` | Tanda bintang field wajib (`*`), pesan error validasi Laravel (`errors->any()`), tombol `Keluar`/hapus di dropdown, animasi `.sorot-error-active` (`#f43f5e`) |
| **Aksi Kritis Netral (Cut-Off / Kunci)**  | `bg-slate-800 hover:bg-slate-900 text-white`                                                          | Tombol `Cut-Off Sekarang` atau aksi otorisasi penguncian tanpa membuat UI terlalu warna-warni                                                                         |
| **Status Positif / Sukses / Tunai**       | `bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200`                                                   | **Wajib pakai `kp-blue`**, bukan `emerald`/`green`, untuk menjaga disiplin 60–30–10 pada halaman daftar & dashboard                                       |
| **Status Menunggu / Pending Memo**        | `bg-amber-50 text-amber-700 border-amber-200`                                                         | Diperbolehkan terbatas hanya untuk indikator status *menunggu otorisasi* bila `slate`/`kp-blue` tidak cukup membedakan                                            |

---

## 5. Spesifikasi Komponen UI Baku

### 5.1 Struktur Halaman (*Page Shell*)

```html
<x-app-layout>
    <div class="space-y-5">
        <!-- 1. Header Halaman -->
        <!-- 2. Flash Alert (jika ada) -->
        <!-- 3. KPI Summary Cards (opsional, grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4) -->
        <!-- 4. Toolbar Pencarian & Filter -->
        <!-- 5. Card Tabel / Konten Utama -->
    </div>
</x-app-layout>
```

- Container utama di `app.blade.php`: `max-w-7xl mx-auto`, padding `p-4 sm:p-6 lg:p-8`.
- Jarak antar blok vertikal: **`space-y-5`** (pada halaman form entri kompleks diperbolehkan `space-y-6`).

### 5.2 Header Halaman (*Page Header Card*)

Standar wajib di semua halaman Iklan:

```html
<div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-3.5 min-w-0">
        <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                Judul Halaman
            </h1>
            <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                Deskripsi singkat halaman dalam bahasa Indonesia baku.
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
        <!-- Tombol aksi h-10 rounded-xl -->
    </div>
</div>
```

### 5.3 Tombol (*Buttons*)

| Varian                                               | Tinggi              | Radius         | Class Baku                                                                                                                                                                                                            |
| ---------------------------------------------------- | ------------------- | -------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Primary CTA (Header / Modal)**               | `h-10`            | `rounded-xl` | `inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap`       |
| **Primary Submit (Toolbar Filter)**            | `h-11`            | `rounded-xl` | `h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer whitespace-nowrap`                |
| **Secondary Outline (Kembali / Ubah / SPPI)**  | `h-10`            | `rounded-xl` | `inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-2xs transition cursor-pointer whitespace-nowrap` |
| **Reset / Neutral Fill**                       | `h-11` / `h-10` | `rounded-xl` | `h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center`                                                     |
| **Dark Lock Action (Cut-Off)**                 | `h-10`            | `rounded-xl` | `inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap`                     |
| **Aksi Baris Tabel (`<x-action-dropdown>`)** | `w-8 h-8`         | `rounded-lg` | Menggunakan komponen `<x-action-dropdown>` dengan panel `bg-white border border-slate-200 rounded-xl shadow-lg text-xs`.                                                                                          |

### 5.4 Kontrol Input & Filter Toolbar

- **Input & Select Toolbar**: `w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs`
- **Tombol Mode Periode (`_period_filter.blade.php`)**:
  - Aktif: `bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs`
  - Non-aktif: `bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium`
  - Ukuran: `h-9 px-3.5 rounded-lg text-sm border transition cursor-pointer`

### 5.5 Tabel Data (*Global Brand Table*)

Styling header tabel **diatur terpusat** di `resources/views/layouts/app.blade.php` & `resources/css/app.css` melalui kelas `body.is-iklan-view main.is-iklan-view` (serta `is-sirkulasi-view` & `is-accounting-view`):

- **Latar `<thead>`**: `#0A72AC` (`!important`)
- **Garis Bawah `<thead>`**: `3.5px solid #FFB900` (`!important`)
- **Teks `<thead> th` & seluruh child-nya**: `#FFFFFF` (`!important`), `13px`, `font-weight: 700`, `uppercase`, `letter-spacing: 0.04em`, `white-space: nowrap`, padding vertikal `0.625rem` (`py-2.5`–`py-3`)
- **Container Tabel**: `bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden` dengan wrapper `overflow-x-auto min-h-[300px]` (scrollbar disembunyikan via `.overflow-x-auto::-webkit-scrollbar { display: none }`).
- **Baris Tabel (`<tbody>`)**: `divide-y divide-slate-100 whitespace-nowrap text-slate-700`, hover `hover:bg-slate-50/80 transition`, padding sel `py-3.5 px-3.5` (atau `py-3 px-3.5`).
- **Pengecualian Tabel Polos / Cetak**: Tambahkan class `.no-brand-table` (atau bungkus dalam `.print-document-sheet` / `.report-print-container`) jika tabel tidak boleh memakai header biru.

### 5.6 Radius & Bayangan (*Elevation*)

- **Card & Input & Tombol Utama**: `rounded-xl` (702 kali — standar utama; hindari mencampur `rounded-2xl` pada header halaman seperti di `pengaturan/index.blade.php`).
- **Sub-elemen (tombol kecil, badge pill, item popover)**: `rounded-lg` atau `rounded-md`.
- **Pill Penuh**: `rounded-full` (khusus indikator dot status atau avatar lingkaran).
- **Shadow**: `shadow-xs` untuk card & tombol utama, `shadow-2xs` untuk input/alert/badge ringan, `shadow-lg` / `shadow-xl` hanya untuk dropdown & popover/modal.

---

## 6. Standar Dokumen Cetak (*Print Views*)

Di modul Iklan terdapat **dua standar cetak resmi** yang harus dibedakan secara tegas:

| Jenis Cetak                                 | Contoh File                                                                                                                        | Font & Ukuran Baku                                                                                                                                                                                  | Warna & Latar                                                                                                         | Toolbar Layar (`no-print`)                                                                                                                    |
| ------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------- |
| **A. Continuous Form / Dot-Matrix**   | `transaksi/{koran,online,paket,priangan}/cetak`, `laporan/cetak*`, `rekap_pracetak/cetak`                                    | `'Courier New', Courier, monospace, 'Lucida Console'`• Laporan tabel lebar: **`9.5pt`** (`line-height: 1.28`)• Faktur/Order/Memo Pracetak: **`11pt`** (`line-height: 1.35`) | Kertas `#fff`, teks `#000`, backdrop layar `#e2e8f0`, border `#cbd5e1`                                        | Tombol `btn-print`: `#0A72AC` (hover `#085a88`), `12px` `sans-serif` bold; `btn-back`: `#fff` border `#cbd5e1` teks `#334155` |
| **B. Dokumen Resmi Modern (A4 / A5)** | `sppi.blade.php` (A4), `cetak_memo.blade.php` (A4), `kwitansi.blade.php` (A5 landscape), `lembar_kerja_pracetak.blade.php` | `'Plus Jakarta Sans', sans-serif` + `'JetBrains Mono', monospace` (`font-mono`) via `@vite(['resources/css/app.css', 'resources/js/app.js'])`                                               | Layar `bg-slate-100`, kertas `bg-white`, saat `@media print` wajib `#ffffff` dan teks kontras hitam/slate-900 | Toolbar card `bg-white border border-slate-200 rounded-xl`, tombol cetak `bg-kp-blue-600 text-white`                                        |

---

## 7. Temuan Inkonsistensi pada Audit & Wajib Penyeragaman

Saat audit 51 file di Role Iklan ditemukan beberapa deviasi minor dari patokan di atas yang perlu diselaraskan agar benar-benar 100% seragam:

1. **Sisa Warna Legacy (`gray-*`, `red-*`, `emerald-*`, `amber-*`, `sky-*`, `purple-*`)**:
   - `pages/iklan/transaksi/priangan/index.blade.php`: masih memakai `text-gray-700`, `text-gray-600`, `text-gray-500`, `bg-gray-100`, dan `text-red-600` (seharusnya `slate-*` dan `rose-600`/`kp-blue-*`).
   - `pages/iklan/transaksi/{koran,online,paket,priangan}/create.blade.php` & `kwitansi.blade.php`: masih menggunakan beberapa kelas `emerald-*`, `amber-*`, `sky-*`, `purple-*` pada simulasi/indikator non-error. Untuk halaman form baru/revisi, prioritaskan `kp-blue`, `slate`, dan `rose` (hanya `amber` bila menandai *warning/pending*).
2. **Variasi Ukuran Judul Halaman (`<h1>`)**:
   - `laporan/aging_piutang.blade.php`, `laporan/klasifikasi.blade.php`, dan `laporan/pembatalan.blade.php` memakai `text-xl sm:text-2xl`, sedangkan 28 halaman layar lainnya memakai **`text-xl`**. Patokan resmi adalah **`text-xl`**.
3. **Variasi Radius Card Header**:
   - `pengaturan/index.blade.php` memakai `rounded-2xl p-5 sm:p-6`, sedangkan 30 halaman lain memakai **`rounded-xl p-4 sm:p-5`**. Patokan resmi adalah **`rounded-xl p-4 sm:p-5`**.
4. **Penggunaan Tailwind CDN pada Dokumen Cetak**:
   - `transaksi/koran/kwitansi.blade.php` dan `transaksi/koran/lembar_kerja_pracetak.blade.php` masih memuat `https://cdn.tailwindcss.com`, sedangkan `sppi.blade.php` dan `cetak_memo.blade.php` sudah memakai `@vite(['resources/css/app.css', 'resources/js/app.js'])` (aman offline/intranet). Dokumen cetak modern wajib dimigrasikan ke `@vite`.
5. **Typo Class Minor**:
   - `pages/iklan/transaksi/koran/index.blade.php` baris 65 memiliki typo class `bordier` (seharusnya `border`).
6. **Sinkronisasi Route List `$isIklanView` di `app.blade.php`**:
   - Pastikan seluruh route di grup Iklan (termasuk `laporan.*` saat diakses role Iklan) selalu tercakup dalam `$isIklanView` pada `resources/views/layouts/app.blade.php`.

---

## 8. Checklist QA Sebelum Rilis Halaman Baru (Semua Divisi)

Gunakan daftar periksa berikut setiap kali membuat atau merapikan halaman di divisi mana pun agar seragam dengan patokan Role Iklan:

- [ ] Font utama layar menggunakan `Plus Jakarta Sans` (`font-sans` default) dan seluruh nilai nominal/kode/nomor dokumen menggunakan `font-mono tabular-nums`.
- [ ] Header halaman memakai struktur baku: logo `h-9 sm:h-10`, `<h1>` dengan `text-xl font-bold text-slate-900 tracking-tight`, subjudul `<p>` dengan `text-xs sm:text-sm font-normal text-slate-500`.
- [ ] Warna mengikuti rasio **60–30–10**: latar `slate-100`/`white`, teks `slate-900`–`slate-500`, aksen utama `kp-blue-600`/`700` (`#0A72AC`), error/wajib `rose-500`/`600`. Tidak memakai `gray-*` atau `red-*`.
- [ ] Tinggi tombol header `h-10 rounded-xl`, input & tombol toolbar filter `h-11 rounded-xl`, card `bg-white border border-slate-200 rounded-xl shadow-xs`.
- [ ] Tabel data tidak menimpa warna header global (tetap mengikuti aturan `#0A72AC` + border bawah `#FFB900` + teks putih `#FFFFFF` `13px` bold uppercase).
- [ ] Dokumen cetak dot-matrix memakai `'Courier New', Courier, monospace` (`9.5pt` laporan / `11pt` faktur); dokumen cetak modern memakai `@vite` + `Plus Jakarta Sans`.
