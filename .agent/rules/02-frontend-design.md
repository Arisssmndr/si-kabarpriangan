# 02 — Frontend Design (Anti-AI Basic)

## Gaya Visual
Clean, modern minimalist, high-craft UI. Referensi: **Linear**, **Stripe**, **Shadcn UI**.

## Larangan Keras
- DILARANG gradasi ungu-biru generik bawaan AI (mis. `from-purple-500 to-blue-500`, `from-indigo-* via-purple-*`).
- DILARANG drop-shadow tebal/berlebihan. Pakai border tipis; shadow hanya `shadow-sm` atau lebih halus (mis. `0 1px 2px rgba(0,0,0,.04)`).
- Hindari layout 3 kolom simetris yang membosankan (3 kartu fitur sejajar sama besar). Gunakan hierarki, asimetri, atau tabel/list padat informasi.
- Hindari emoji sebagai ikon, glassmorphism berlebihan, dan hiasan tanpa fungsi.

## Surface & Border
| Elemen | Nilai |
|---|---|
| Background utama | `#FAFAFA` |
| Surface / card | `#FFFFFF` |
| Border | `1px solid #E4E4E7` |
| Border radius | `6px–8px` (`rounded-md` / `rounded-lg`) |

## Palet Warna
- Monokrom / Slate bersih (`slate-50` … `slate-900`).
- Maksimal **1 warna aksen tegas** untuk tombol aksi utama: `#0F172A` (slate-900) **atau** `#2563EB` (blue-600). Pilih satu per project dan konsisten.
- Warna status (sukses, peringatan, bahaya) hanya dipakai secukupnya untuk badge/alert, dengan saturasi rendah.
- Teks utama `#0F172A`, teks sekunder `#64748B`.

## Tipografi
- UI: **Plus Jakarta Sans** atau **Inter** (pilih satu per project).
- Angka, nominal uang, ID, kode, dan semua kolom tabel numerik: **JetBrains Mono** dengan `font-variant-numeric: tabular-nums` (Tailwind: `font-mono tabular-nums`).
- Nominal uang rata kanan, format Rupiah (`Rp 1.250.000`).

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
```

```css
.num { font-family: 'JetBrains Mono', ui-monospace, monospace; font-variant-numeric: tabular-nums; }
```

## Komponen Wajib
### State Interaktif
Setiap tombol dan input wajib memiliki state yang jelas:
- `hover` — perubahan warna/background halus.
- `focus` — ring terlihat (mis. `focus-visible:ring-2 focus-visible:ring-offset-2 ring-slate-900` atau `ring-blue-600`).
- `disabled` — opacity turun, `cursor-not-allowed`, tanpa efek hover.
- Tambahkan juga state `loading` pada tombol submit.

### Empty State
Setiap halaman/tabel/list data wajib memiliki Empty State yang rapi: ikon garis sederhana, judul singkat, deskripsi satu kalimat, dan tombol aksi (bila relevan). Teks dalam Bahasa Indonesia, mis. "Belum ada data transaksi" / "Mulai dengan menambahkan transaksi pertama Anda."

### Skeleton Loading
Setiap halaman data wajib memiliki Skeleton Loading yang bentuknya meniru konten asli (baris tabel, card, header) dengan `animate-pulse` pada blok `bg-slate-100`/`bg-slate-200`. Jangan hanya spinner di tengah layar.

### Lainnya
- Tabel: header kecil uppercase/semibold berwarna slate-500, baris dibatasi border tipis, hover baris halus.
- Form: label di atas input, pesan error merah di bawah input, teks Bahasa Indonesia.
- Spacing konsisten (skala 4px), kontras teks memenuhi WCAG AA, responsif mobile-first.
- Ekstrak elemen berulang menjadi komponen Blade reusable (`<x-button>`, `<x-input>`, `<x-card>`, `<x-empty-state>`, `<x-skeleton>`).
