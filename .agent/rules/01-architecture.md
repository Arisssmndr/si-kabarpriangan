# 01 — Arsitektur & Stack

## Stack Standar
- **Backend:** PHP 8.2+ dengan Laravel (versi stabil terbaru yang dipakai project).
- **Database:** MySQL / MariaDB (engine InnoDB, charset `utf8mb4`).
- **Frontend:** Blade + Tailwind CSS (utama) atau Bootstrap (hanya jika project sudah memakainya), dengan Alpine.js untuk interaktivitas ringan.
- Ikuti stack yang sudah ada di project lama. Jangan mengganti framework atau library tanpa diminta.

## Aturan Bahasa
| Area | Bahasa |
|---|---|
| Teks antarmuka (UI), label form, tombol, placeholder, pesan validasi, notifikasi, empty state | **Bahasa Indonesia** natural dan profesional |
| Nama tabel, kolom database, migration, model, variabel, fungsi, class, route, nama file | **Bahasa Inggris** |

Contoh:
- Tabel `cash_transactions`, kolom `transaction_date`, route `cash-transactions.index`.
- Label UI: "Tanggal Transaksi", tombol "Simpan Perubahan", pesan: "Kolom nama wajib diisi."
- Pesan validasi Laravel wajib diterjemahkan ke Bahasa Indonesia (`lang/id/validation.php`, `APP_LOCALE=id`).

## Struktur & Konvensi Laravel
- Tabel: `snake_case` jamak. Model: `PascalCase` tunggal. Foreign key: `{model}_id`.
- Controller tipis: validasi di Form Request, logika bisnis di Service/Action class bila kompleks.
- Gunakan Route Model Binding, resource controller, dan named route.
- Gunakan migration, seeder, dan factory. Jangan mengubah skema database secara manual.
- Kredensial dan konfigurasi hanya lewat `.env` / `config()`. Jangan hardcode.

## Optimasi Query (Wajib)
- Gunakan **Eloquent ORM**; query builder/raw hanya untuk laporan kompleks.
- **Cegah N+1 query:** selalu gunakan eager loading (`with()`, `load()`, `withCount()`, `withSum()`) saat mengakses relasi di loop atau di view.
- Pilih kolom seperlunya (`select()`), jangan `select *` untuk tabel besar.
- Gunakan `paginate()` / `cursor()` / `chunk()` untuk data banyak. Jangan `all()` pada tabel besar.
- Tambahkan index pada kolom yang sering dipakai di `where`, `orderBy`, dan foreign key.
- Gunakan `DB::transaction()` untuk operasi multi-tabel yang harus atomik.
- Pertimbangkan `Model::preventLazyLoading(! app()->isProduction())` di `AppServiceProvider` untuk mendeteksi N+1 saat development.
- Jangan menjalankan query di dalam Blade view.

## Keamanan
- Semua input divalidasi di server. Gunakan `$request->validated()`.
- Lindungi mass assignment dengan `$fillable` yang eksplisit.
- Gunakan `@csrf`, authorization (Policy/Gate), dan escaping Blade `{{ }}` secara default.
