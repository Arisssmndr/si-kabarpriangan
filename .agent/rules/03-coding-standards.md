# 03 — Standar Penulisan Kode

## Kode Utuh & Siap Jalan
- Tulis kode **secara utuh dan siap dijalankan**.
- DILARANG memotong kode dengan komentar placeholder seperti `// ... kode lainnya`, `// rest of code`, `/* sama seperti sebelumnya */`, atau `...`.
- Saat mengubah file, pastikan hasil akhir file lengkap dan valid secara sintaks. Sertakan semua `use`/`import`, route, migration, dan view yang dibutuhkan agar fitur langsung berfungsi.
- Jangan meninggalkan TODO untuk hal inti. Jangan membuat fungsi kosong.

## Validasi via Form Request
- Pisahkan validasi dari controller ke **Form Request** (`php artisan make:request StoreXxxRequest`).
- Definisikan `rules()`, `authorize()`, serta `messages()` / `attributes()` dalam **Bahasa Indonesia**.
- Controller memakai `$request->validated()`; jangan `$request->all()`.

```php
class StoreCashTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'transaction_date' => 'tanggal transaksi',
            'amount' => 'nominal',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Kolom :attribute wajib diisi.',
            'numeric' => 'Kolom :attribute harus berupa angka.',
        ];
    }
}
```

## Komponen Reusable
- Ekstrak elemen UI yang berulang (button, input, select, card, modal, badge, table, empty state, skeleton, pagination) menjadi **Blade component** di `resources/views/components/`.
- Gunakan props dan slot; jangan menyalin-tempel markup yang sama di banyak view.
- Logika berulang diekstrak ke Service/Action/Trait/Helper yang terarah.

## Gaya Kode
- Ikuti PSR-12 dan konvensi Laravel (`./vendor/bin/pint` bila tersedia).
- Nama deskriptif dalam Bahasa Inggris; hindari singkatan yang tidak jelas.
- Gunakan type hint dan return type pada method PHP.
- Satu tanggung jawab per class/method; hindari method panjang.
- Komentar hanya untuk menjelaskan "mengapa", bukan "apa".

## Sebelum Selesai
- Periksa konsistensi nama route, view, dan kolom database.
- Jalankan lint/test/build yang tersedia bila memungkinkan.
- Pastikan UI memiliki empty state, skeleton loading, serta state hover/focus/disabled (lihat `02-frontend-design.md`).
