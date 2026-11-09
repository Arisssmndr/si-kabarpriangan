# Kontrak Integrasi QR Pembayaran Faktur Iklan -> Modul Kasir

Dokumen ini disiapkan khusus untuk penyambungan layar Scan QR di **Modul Kasir** setelah kode terbaru Kasir di-pull. Saat ini sisi **Iklan (Koran, Online, Priangan TV)** sudah menghasilkan QR Code dan menyimpannya ke tabel `faktur_qr_pembayaran`.

---

## 1. Format Isi QR & Kode Manual Cadangan

1. **Payload QR Code (2D Scanner / Kamera)**
   ```text
   KP1|{KRN|ONL|TV}|{NO_FAKTUR}|{KODE_PENGAMAN_12_DIGIT}
   ```
   Contoh:
   ```text
   KP1|KRN|FK/2026/11/0004|7K3P9W4M8Q2Z
   ```

2. **Kode Manual Cadangan (jika scanner bermasalah)**
   ```text
   {NO_FAKTUR}-{6_KARAKTER_AWAL_KODE_PENGAMAN}
   ```
   Contoh:
   ```text
   FK/2026/11/0004-7K3P9W
   ```

> Kedua format di atas diterima otomatis oleh satu fungsi yang sama: `FakturQrService::verifikasi($input)`.

---

## 2. Cara Memanggil Saat Kasir Scan QR (Read / Auto-Fill Form Kasir)

```php
use App\Services\FakturQrService;

$hasil = FakturQrService::verifikasi($request->input('kode_qr'));

if (!$hasil['valid']) {
    // Kembalikan pesan error ($hasil['message'])
    // Kode kemungkinan: EMPTY_INPUT, INVALID_FORMAT, INVALID_SIGNATURE,
    // NOT_FOUND, ALREADY_DEPOSITED (anti scan 2x), CANCELLED_QR, TRANSACTION_INVALID
    return response()->json([
        'success' => false,
        'code'    => $hasil['code'],
        'message' => $hasil['message'],
    ], 422);
}

$data = $hasil['data'];
/*
Contoh isi $data:
[
    'qr_id'             => 15,                       // Simpan di hidden input form kasir
    'kategori'          => 'KORAN',                  // KORAN | ONLINE | TV
    'kode_media'        => 'KRN',                    // KRN | ONL | TV
    'media_type'        => 'koran',                  // koran | online | priangan
    'transaksi_id'      => 24,
    'pelanggan_id'      => 17,
    'no_faktur'         => 'FK/2026/11/0004',
    'tanggal_transaksi' => '2026-11-06',
    'nama_pemasang'     => 'ASIA PLAZA TASIKMALAYA',
    'sales_nama'        => 'FRONT OFFICE',
    'total_tagihan'     => 68796.0,
    'nominal_setor'     => 30000.0,                  // Nominal uang fisik yang masuk ke Kasir (lunas atau DP sebagian)
    'sisa_piutang'      => 38796.0,                  // Sisa tunggakan di faktur (tetap urusan Inkaso bila > 0)
    'status_bayar_awal' => 'SEBAGIAN',               // LUNAS | SEBAGIAN
    'kode_manual'       => 'FK/2026/11/0004-7K3P9W',
]
*/
```

---

## 3. Cara Mengunci QR Setelah Penerimaan Kasir Disimpan (Anti Scan Ganda)

Saat staf Kasir menekan tombol **Simpan Penerimaan**, panggil `FakturQrService::tandaiDisetor(...)` di dalam `DB::transaction`:

```php
use App\Services\FakturQrService;

FakturQrService::tandaiDisetor(
    qrId: (int) $request->qr_id,
    kasirUserId: auth()->id(),
    penerimaanKasirId: $kasMasuk->id // ID tabel kas_masuk / penerimaan kasir
);
```

Setelah `tandaiDisetor` dipanggil:
- Status baris di `faktur_qr_pembayaran` berubah menjadi `DISETOR` (lengkap dengan `discan_pada`, `discan_oleh`, `penerimaan_kasir_id`).
- Bila faktur yang sama di-scan ulang kedua kalinya, `FakturQrService::verifikasi()` otomatis menolak dengan kode `ALREADY_DEPOSITED`.
- Di sisi Iklan/Akuntansi, nominal bayar & nama pemasang pada faktur yang sudah `DISETOR` otomatis terkunci agar tidak bisa diubah diam-diam.

