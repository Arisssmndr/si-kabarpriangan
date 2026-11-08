<?php

namespace App\Services;

use App\Models\DailyCutoff;
use App\Models\FakturQrPembayaran;
use App\Models\LogHitungAkuntansi;
use App\Models\MemoUnlock;
use App\Models\PeriodeAkuntansi;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FakturQrService
{
    public const FORMAT_VERSION = 'KP1';

    /**
     * Karakter jelas tanpa ambigu (tanpa 0/O dan 1/I) agar kode manual mudah diketik bila scanner rusak.
     */
    private const SAFE_ALPHABET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    /**
     * Normalisasi penyebutan kategori media ke enum tabel faktur_qr_pembayaran.
     */
    public static function normalizeKategori(string $mediaOrKategori): string
    {
        $clean = strtoupper(trim($mediaOrKategori));

        return match ($clean) {
            'KORAN', 'KRN'                => FakturQrPembayaran::KATEGORI_KORAN,
            'ONLINE', 'ONL'               => FakturQrPembayaran::KATEGORI_ONLINE,
            'TV', 'PTV', 'PRIANGAN'       => FakturQrPembayaran::KATEGORI_TV,
            default                       => throw new \InvalidArgumentException("Kategori transaksi tidak dikenali: {$mediaOrKategori}"),
        };
    }

    /**
     * Konversi kategori DB ke kode singkat di dalam QR (KRN / ONL / TV).
     */
    public static function kodeMedia(string $kategori): string
    {
        return match (self::normalizeKategori($kategori)) {
            FakturQrPembayaran::KATEGORI_KORAN  => 'KRN',
            FakturQrPembayaran::KATEGORI_ONLINE => 'ONL',
            FakturQrPembayaran::KATEGORI_TV     => 'TV',
        };
    }

    /**
     * Ekstraksi informasi standar dari model transaksi (Koran / Online / Priangan TV).
     */
    public static function extractDataTransaksi(object $transaksi, string $kategori): array
    {
        $kat = self::normalizeKategori($kategori);

        if ($kat === FakturQrPembayaran::KATEGORI_KORAN) {
            $qty = max(1, (int) ($transaksi->total_muatkoran ?? 1));
            $tarif = (int) ($transaksi->harga_transaksikoran ?? 0);
            $brutto = (int) ($transaksi->nilai_brutto ?? ($tarif * $qty));
            $diskon = (int) ($transaksi->nilai_diskon ?? ($transaksi->diskon_transaksikoran ?? 0));
            $netto = max(0, $brutto - $diskon);
            $ppnPersen = $transaksi->ppn_persen ?: \App\Models\SistemConfig::getPpnPersen();
            $ppn = (int) ($transaksi->ppn_transaksikoran ?? round($netto * ($ppnPersen / 100)));
            $totalTagihan = (float) ($transaksi->totaltagihan_transaksikoran ?? ($netto + $ppn));
            $nominalBayar = (float) ($transaksi->jumlahbayar_transaksikoran ?? 0);
            $sisaPiutang  = (float) max(0, $totalTagihan - $nominalBayar);

            return [
                'kategori'          => $kat,
                'transaksi_id'      => (int) $transaksi->id,
                'pelanggan_id'      => $transaksi->pelanggan_id,
                'no_faktur'         => trim((string) $transaksi->nofakturkoran),
                'nama_pemasang'     => trim((string) $transaksi->nama_pemasangkoran),
                'sales_nama'        => (string) ($transaksi->salesAe?->nama_ae ?: ($transaksi->sales_iklankoran ?: 'Front Office')),
                'tanggal_transaksi' => $transaksi->tanggal_transaksikoran ? Carbon::parse($transaksi->tanggal_transaksikoran)->toDateString() : now()->toDateString(),
                'tanggal_muat'      => $transaksi->tanggal_muatkoran ? Carbon::parse($transaksi->tanggal_muatkoran)->toDateString() : null,
                'total_tagihan'     => $totalTagihan,
                'nominal_bayar'     => $nominalBayar,
                'sisa_piutang'      => $sisaPiutang,
                'is_batal'          => (($transaksi->status_order ?? '') === 'Batal'),
            ];
        }

        if ($kat === FakturQrPembayaran::KATEGORI_ONLINE) {
            $qty = max(1, (int) ($transaksi->total_muatiklanonline ?? 1));
            $tarif = (int) ($transaksi->harga_transaksionline ?? 0);
            $skema = $transaksi->skema_pembayaran ?: 'sekali_tayang';
            $brutto = ($skema === 'paket') ? $tarif : ($tarif * $qty);
            $diskon = (int) ($transaksi->diskon_transaksionline ?? 0);
            $netto = max(0, $brutto - $diskon);
            $ppnPersen = $transaksi->ppn_persen ?: \App\Models\SistemConfig::getPpnPersen();
            $ppn = (int) ($transaksi->ppn_transaksionline ?? round($netto * ($ppnPersen / 100)));
            $totalTagihan = (float) ($transaksi->totaltagihan_transaksionline ?: ($netto + $ppn));
            $nominalBayar = (float) ($transaksi->jumlahbayar_transaksionline ?? 0);
            $sisaPiutang  = (float) max(0, $totalTagihan - $nominalBayar);

            return [
                'kategori'          => $kat,
                'transaksi_id'      => (int) $transaksi->id,
                'pelanggan_id'      => $transaksi->pelanggan_id,
                'no_faktur'         => trim((string) $transaksi->nofakturonline),
                'nama_pemasang'     => trim((string) $transaksi->nama_pemasangonline),
                'sales_nama'        => (string) ($transaksi->salesAe?->nama_ae ?: ($transaksi->sales_iklanonline ?: 'Front Office')),
                'tanggal_transaksi' => $transaksi->tanggal_transaksionline ? Carbon::parse($transaksi->tanggal_transaksionline)->toDateString() : now()->toDateString(),
                'tanggal_muat'      => $transaksi->tanggal_muatiklanonline ? Carbon::parse($transaksi->tanggal_muatiklanonline)->toDateString() : null,
                'total_tagihan'     => $totalTagihan,
                'nominal_bayar'     => $nominalBayar,
                'sisa_piutang'      => $sisaPiutang,
                'is_batal'          => str_contains((string) ($transaksi->catatan_redaksi ?? ''), '[DIBATALKAN:'),
            ];
        }

        // Priangan TV
        $qty = max(1, (int) ($transaksi->total_muatiklanpriangan ?? 1));
        $tarif = (int) ($transaksi->harga_transaksipriangan ?? 0);
        $brutto = $tarif * $qty;
        $diskon = (int) ($transaksi->diskon_transaksipriangan ?? 0);
        $netto = max(0, $brutto - $diskon);
        $ppnPersen = $transaksi->ppn_persen ?: \App\Models\SistemConfig::getPpnPersen();
        $ppn = (int) ($transaksi->ppn_transaksipriangan ?? round($netto * ($ppnPersen / 100)));
        $totalTagihan = (float) ($transaksi->totaltagihan_transaksipriangan ?: ($netto + $ppn));
        $nominalBayar = (float) ($transaksi->jumlahbayar_transaksipriangan ?? 0);
        $sisaPiutang  = (float) max(0, $totalTagihan - $nominalBayar);

        return [
            'kategori'          => $kat,
            'transaksi_id'      => (int) $transaksi->id,
            'pelanggan_id'      => $transaksi->pelanggan_id,
            'no_faktur'         => trim((string) $transaksi->nofakturpriangan),
            'nama_pemasang'     => trim((string) $transaksi->nama_pemasangpriangan),
            'sales_nama'        => (string) ($transaksi->salesAe?->nama_ae ?: ($transaksi->sales_iklanpriangan ?: 'Front Office')),
            'tanggal_transaksi' => $transaksi->tanggal_transaksipriangan ? Carbon::parse($transaksi->tanggal_transaksipriangan)->toDateString() : now()->toDateString(),
            'tanggal_muat'      => $transaksi->tanggal_muatiklanpriangan ? Carbon::parse($transaksi->tanggal_muatiklanpriangan)->toDateString() : null,
            'total_tagihan'     => $totalTagihan,
            'nominal_bayar'     => $nominalBayar,
            'sisa_piutang'      => $sisaPiutang,
            'is_batal'          => (strtolower((string) ($transaksi->status_tayang ?? '')) === 'batal'),
        ];
    }

    /**
     * Buat kode pengaman 12 karakter:
     * 6 karakter acak kriptografis + 6 karakter turunan HMAC-SHA256 atas (KP1|kodeMedia|noFaktur|nonce6).
     */
    public static function generateKodePengaman(string $kodeMedia, string $noFaktur): string
    {
        $alphabet = self::SAFE_ALPHABET;
        $maxIdx = strlen($alphabet) - 1;
        $nonce6 = '';
        for ($i = 0; $i < 6; $i++) {
            $nonce6 .= $alphabet[random_int(0, $maxIdx)];
        }

        $check6 = self::computeEmbeddedTag($kodeMedia, $noFaktur, $nonce6);

        return $nonce6 . $check6;
    }

    /**
     * Hitung 6 karakter HMAC pendek berbasis SAFE_ALPHABET untuk tertanam di ekor kode_pengaman.
     */
    public static function computeEmbeddedTag(string $kodeMedia, string $noFaktur, string $nonce6): string
    {
        $secret = (string) config('app.key', 'si-kabarpriangan-qr-secret');
        $rawBytes = hash_hmac('sha256', self::FORMAT_VERSION . "|{$kodeMedia}|{$noFaktur}|{$nonce6}", $secret, true);
        $alphabet = self::SAFE_ALPHABET;
        $len = strlen($alphabet);

        $tag = '';
        for ($i = 0; $i < 6; $i++) {
            $tag .= $alphabet[ord($rawBytes[$i]) % $len];
        }

        return $tag;
    }

    /**
     * Hitung tanda tangan penuh HMAC-SHA256 untuk disimpan di kolom signature.
     */
    public static function computeFullSignature(string $kodeMedia, string $noFaktur, string $kodePengaman): string
    {
        $secret = (string) config('app.key', 'si-kabarpriangan-qr-secret');
        $payload = sprintf('%s|%s|%s|%s', self::FORMAT_VERSION, $kodeMedia, $noFaktur, $kodePengaman);

        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Cek apakah transaksi ini memiliki QR yang SUDAH DISETOR (discan Kasir).
     */
    public static function getDisetorRecord(int $transaksiId, string $kategori): ?FakturQrPembayaran
    {
        $kat = self::normalizeKategori($kategori);

        return FakturQrPembayaran::where('kategori', $kat)
            ->where('transaksi_id', $transaksiId)
            ->where('status', FakturQrPembayaran::STATUS_DISETOR)
            ->latest('id')
            ->first();
    }

    /**
     * Cek apakah transaksi terkunci oleh tutup buku / penarikan jurnal penjualan Akuntansi / Cut-off.
     * Mengembalikan string alasan kunci bila terkunci, atau null bila masih terbuka.
     */
    public static function getAccountingOrCutoffLockReason(object $transaksi, string $kategori): ?string
    {
        $info = self::extractDataTransaksi($transaksi, $kategori);

        // 1. Cek periode tutup buku bulanan Akuntansi
        $datesToCheck = array_values(array_filter(array_unique([
            $info['tanggal_transaksi'],
            $info['tanggal_muat'],
        ])));

        foreach ($datesToCheck as $tgl) {
            $periodeKey = Carbon::parse($tgl)->format('Y-m');
            if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
                return "Periode akuntansi {$periodeKey} sudah ditutup (terkunci).";
            }
        }

        // 2. Cek apakah data penjualan tanggal tersebut sudah ditarik oleh Akuntansi (kecuali ada MemoUnlock OPEN)
        foreach ($datesToCheck as $tgl) {
            $hasOpenMemo = MemoUnlock::whereDate('tanggal_target', $tgl)
                ->where('status', 'OPEN')
                ->exists();

            if (!$hasOpenMemo && LogHitungAkuntansi::sudahDihitung('penjualan_iklan', $tgl)) {
                return "Data penjualan tanggal {$tgl} sudah ditarik ke Jurnal Akuntansi. Gunakan persetujuan Buka Data Cut-Off Akuntansi bila perlu koreksi.";
            }
        }

        // 3. Khusus Koran: ikuti juga aturan DailyCutoff / canEdit yang sudah berjalan
        if ($info['kategori'] === FakturQrPembayaran::KATEGORI_KORAN && method_exists($transaksi, 'canEdit')) {
            if (!$transaksi->canEdit()) {
                return $transaksi->getCutoffLockReason() ?: 'Transaksi sudah melewati batas cut-off.';
            }
        } elseif (!empty($info['tanggal_muat'])) {
            $hasOpenMemo = MemoUnlock::whereDate('tanggal_target', $info['tanggal_muat'])
                ->where('status', 'OPEN')
                ->exists();
            if (!$hasOpenMemo) {
                $cutoff = DailyCutoff::whereDate('tanggal_edisi', $info['tanggal_muat'])->first();
                if ($cutoff && $cutoff->is_closed) {
                    return "Tanggal tayang {$info['tanggal_muat']} sudah ditutup (Cut-off).";
                }
            }
        }

        return null;
    }

    /**
     * Validasi sebelum update/edit transaksi:
     * Menahan perubahan nominal bayar / tagihan / nama pemasang bila QR sudah DISETOR di Kasir,
     * atau bila transaksi sudah ditarik Akuntansi / melewati cut-off.
     */
    public static function guardBeforeUpdate(
        object $transaksi,
        string $kategori,
        float $newNominalBayar,
        string $newNamaPemasang,
        ?float $newTotalTagihan = null
    ): ?string {
        $lockReason = self::getAccountingOrCutoffLockReason($transaksi, $kategori);
        if ($lockReason !== null) {
            return $lockReason;
        }

        $info = self::extractDataTransaksi($transaksi, $kategori);
        $disetor = self::getDisetorRecord($info['transaksi_id'], $info['kategori']);
        if ($disetor) {
            $nominalBerubah = abs((float) $disetor->nominal_bayar - $newNominalBayar) > 0.01;
            $namaBerubah    = strcasecmp(trim($info['nama_pemasang']), trim($newNamaPemasang)) !== 0;
            $tagihanBerubah = $newTotalTagihan !== null && abs((float) $info['total_tagihan'] - $newTotalTagihan) > 0.01;

            if ($nominalBerubah || $namaBerubah || $tagihanBerubah) {
                $tglScan = $disetor->discan_pada ? $disetor->discan_pada->format('d-m-Y H:i') : '-';
                $rpSetor = number_format((float) $disetor->nominal_bayar, 0, ',', '.');
                return "Faktur {$info['no_faktur']} sudah discan/disetor di Kasir (Rp {$rpSetor} pada {$tglScan}). Perubahan nominal atau nama pemasang ditahan.";
            }
        }

        return null;
    }

    /**
     * Validasi sebelum pembatalan atau penghapusan transaksi:
     * Menahan pembatalan/hapus bila QR sudah DISETOR di Kasir atau periode terkunci.
     */
    public static function guardBeforeCancelOrDelete(object $transaksi, string $kategori): ?string
    {
        $info = self::extractDataTransaksi($transaksi, $kategori);

        $disetor = self::getDisetorRecord($info['transaksi_id'], $info['kategori']);
        if ($disetor) {
            $tglScan = $disetor->discan_pada ? $disetor->discan_pada->format('d-m-Y H:i') : '-';
            $rpSetor = number_format((float) $disetor->nominal_bayar, 0, ',', '.');
            return "Faktur {$info['no_faktur']} sudah discan/disetor di Kasir (Rp {$rpSetor} pada {$tglScan}). Transaksi tidak dapat dibatalkan/dihapus sebelum penerimaan kasir di-void oleh Akuntansi.";
        }

        $periodeKey = Carbon::parse($info['tanggal_transaksi'])->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return "Periode akuntansi {$periodeKey} sudah ditutup (terkunci).";
        }

        return null;
    }

    /**
     * Buat atau sinkronkan QR pembayaran untuk suatu transaksi (dipanggil saat store & update).
     * - Jika kredit penuh (nominal_bayar <= 0) atau transaksi batal: batalkan QR aktif yang ada, return null.
     * - Jika ada pembayaran tunai di muka (lunas maupun sebagian):
     *   - Bila QR sudah DISETOR: biarkan tetap DISETOR.
     *   - Bila ada QR AKTIF dan data finansial/identitas berubah: tandai QR lama BATAL lalu terbitkan QR baru.
     *   - Bila belum ada QR AKTIF: terbitkan QR AKTIF baru.
     */
    public static function generate(object $transaksi, string $kategori, ?int $userId = null): ?FakturQrPembayaran
    {
        $info = self::extractDataTransaksi($transaksi, $kategori);
        $actorId = $userId ?? Auth::id();

        if (empty($info['no_faktur']) || $info['transaksi_id'] <= 0) {
            return null;
        }

        // Jika sudah pernah disetor ke kasir, jangan terbitkan QR aktif ganda
        $disetor = self::getDisetorRecord($info['transaksi_id'], $info['kategori']);
        if ($disetor) {
            return $disetor;
        }

        // Kredit penuh (bayar 0) atau transaksi dibatalkan -> tidak mendapat QR
        if ($info['is_batal'] || $info['nominal_bayar'] <= 0) {
            self::batalkan(
                $info['transaksi_id'],
                $info['kategori'],
                $info['is_batal']
                    ? 'Transaksi iklan dibatalkan'
                    : 'Transaksi kredit penuh tanpa pembayaran di muka'
            );
            return null;
        }

        $activeQr = FakturQrPembayaran::where('kategori', $info['kategori'])
            ->where('transaksi_id', $info['transaksi_id'])
            ->where('status', FakturQrPembayaran::STATUS_AKTIF)
            ->latest('id')
            ->first();

        if ($activeQr) {
            $sameFaktur  = $activeQr->no_faktur === $info['no_faktur'];
            $sameNama    = strcasecmp(trim($activeQr->nama_pemasang), trim($info['nama_pemasang'])) === 0;
            $sameBayar   = abs((float) $activeQr->nominal_bayar - $info['nominal_bayar']) < 0.01;
            $sameTagihan = abs((float) $activeQr->total_tagihan - $info['total_tagihan']) < 0.01;
            $samePiutang = abs((float) $activeQr->sisa_piutang - $info['sisa_piutang']) < 0.01;

            if ($sameFaktur && $sameNama && $sameBayar && $sameTagihan && $samePiutang) {
                return $activeQr;
            }

            // Data berubah: batalkan seluruh QR AKTIF lama agar cetakan lama tidak bisa discan
            self::batalkan(
                $info['transaksi_id'],
                $info['kategori'],
                'Digantikan QR baru karena perubahan nominal/data faktur'
            );
        }

        $kodeMedia = self::kodeMedia($info['kategori']);
        $kodePengaman = self::generateKodePengaman($kodeMedia, $info['no_faktur']);

        // Pastikan unik terhadap no_faktur
        while (FakturQrPembayaran::where('no_faktur', $info['no_faktur'])->where('kode_pengaman', $kodePengaman)->exists()) {
            $kodePengaman = self::generateKodePengaman($kodeMedia, $info['no_faktur']);
        }

        $signature = self::computeFullSignature($kodeMedia, $info['no_faktur'], $kodePengaman);

        return FakturQrPembayaran::create([
            'kategori'      => $info['kategori'],
            'transaksi_id'  => $info['transaksi_id'],
            'no_faktur'     => $info['no_faktur'],
            'kode_pengaman' => $kodePengaman,
            'signature'     => $signature,
            'nama_pemasang' => $info['nama_pemasang'],
            'total_tagihan' => $info['total_tagihan'],
            'nominal_bayar' => $info['nominal_bayar'],
            'sisa_piutang'  => $info['sisa_piutang'],
            'status'        => FakturQrPembayaran::STATUS_AKTIF,
            'dibuat_oleh'   => $actorId,
            'dibuat_pada'   => now(),
        ]);
    }

    /**
     * Nonaktifkan (BATAL) seluruh QR berstatus AKTIF pada suatu transaksi.
     */
    public static function batalkan(int $transaksiId, string $kategori, string $alasan = 'Dibatalkan sistem'): int
    {
        $kat = self::normalizeKategori($kategori);

        return FakturQrPembayaran::where('kategori', $kat)
            ->where('transaksi_id', $transaksiId)
            ->where('status', FakturQrPembayaran::STATUS_AKTIF)
            ->update([
                'status'       => FakturQrPembayaran::STATUS_BATAL,
                'alasan_batal' => mb_substr($alasan, 0, 255),
                'updated_at'   => now(),
            ]);
    }

    /**
     * Batalkan berdasarkan nomor faktur (berguna untuk sinkronisasi dari FakturSyncService).
     */
    public static function batalkanByNoFaktur(string $noFaktur, string $alasan = 'Faktur dibatalkan/dihapus'): int
    {
        return FakturQrPembayaran::where('no_faktur', trim($noFaktur))
            ->where('status', FakturQrPembayaran::STATUS_AKTIF)
            ->update([
                'status'       => FakturQrPembayaran::STATUS_BATAL,
                'alasan_batal' => mb_substr($alasan, 0, 255),
                'updated_at'   => now(),
            ]);
    }

    /**
     * Ambil QR untuk halaman cetak faktur (termasuk lazy-generate otomatis untuk faktur lama yang sudah ada pembayaran).
     */
    public static function resolveForCetak(object $transaksi, string $kategori): ?FakturQrPembayaran
    {
        $info = self::extractDataTransaksi($transaksi, $kategori);

        if ($info['is_batal'] || $info['nominal_bayar'] <= 0) {
            return null;
        }

        // Jika sudah DISETOR di kasir, kembalikan record DISETOR tersebut (jangan buat QR AKTIF baru!)
        $disetor = self::getDisetorRecord($info['transaksi_id'], $info['kategori']);
        if ($disetor) {
            return $disetor;
        }

        // Ambil QR AKTIF atau buat otomatis (lazy generate untuk faktur lama)
        return self::generate($transaksi, $info['kategori']);
    }

    /**
     * Render SVG QR Code murni di server (tanpa internet/CDN), koreksi error level Q (25%), tajam untuk cetak.
     */
    public static function renderSvg(FakturQrPembayaran $qr, int $sizePx = 106): string
    {
        $prevReporting = error_reporting();
        error_reporting($prevReporting & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        try {
            $svg = QrCode::format('svg')
                ->errorCorrection('Q')
                ->margin(0)
                ->size($sizePx)
                ->color(0, 0, 0)
                ->backgroundColor(255, 255, 255)
                ->generate($qr->payload_qr);

            $svgStr = (string) $svg;
            // Hilangkan deklarasi header XML agar bersih saat di-embed inline di HTML5
            $svgStr = preg_replace('/<' . '\?xml[^>]*\?' . '>\s*/i', '', $svgStr) ?? $svgStr;

            return trim($svgStr);
        } finally {
            error_reporting($prevReporting);
        }
    }

    /**
     * Verifikasi hasil scan QR (atau input Kode Manual) untuk modul Kasir nanti.
     * Mendukung:
     *  1) Payload QR penuh : KP1|KRN|FK/2026/11/0004|A1B2C3D4E5F6
     *  2) Kode Manual      : FK/2026/11/0004-A1B2C3
     */
    public static function verifikasi(string $rawInput): array
    {
        $clean = trim($rawInput);
        if ($clean === '') {
            return [
                'valid'   => false,
                'code'    => 'EMPTY_INPUT',
                'message' => 'Kode QR atau Kode Manual tidak boleh kosong.',
            ];
        }

        $qr = null;

        if (str_starts_with(strtoupper($clean), self::FORMAT_VERSION . '|')) {
            $parts = explode('|', $clean);
            if (count($parts) !== 4) {
                return [
                    'valid'   => false,
                    'code'    => 'INVALID_FORMAT',
                    'message' => 'Format QR tidak sesuai standar Faktur Kabar Priangan.',
                ];
            }

            [, $kodeMediaRaw, $noFaktur, $kodePengaman] = $parts;
            $kodeMedia    = strtoupper(trim($kodeMediaRaw));
            $noFaktur     = trim($noFaktur);
            $kodePengaman = strtoupper(trim($kodePengaman));

            if (!in_array($kodeMedia, ['KRN', 'ONL', 'TV'], true) || strlen($kodePengaman) !== 12) {
                return [
                    'valid'   => false,
                    'code'    => 'INVALID_FORMAT',
                    'message' => 'Komponen kategori atau kode pengaman QR tidak valid.',
                ];
            }

            // Verifikasi kriptografis 6 karakter HMAC di ekor kode_pengaman sebelum query DB
            $nonce6 = substr($kodePengaman, 0, 6);
            $tag6   = substr($kodePengaman, 6, 6);
            $expectedTag6 = self::computeEmbeddedTag($kodeMedia, $noFaktur, $nonce6);

            if (!hash_equals($expectedTag6, $tag6)) {
                return [
                    'valid'   => false,
                    'code'    => 'INVALID_SIGNATURE',
                    'message' => 'QR Code tidak sah atau tanda tangan keamanan tidak cocok.',
                ];
            }

            $kategori = self::normalizeKategori($kodeMedia);
            $qr = FakturQrPembayaran::where('kategori', $kategori)
                ->where('no_faktur', $noFaktur)
                ->where('kode_pengaman', $kodePengaman)
                ->first();

            if ($qr) {
                $expectedFullSig = self::computeFullSignature($kodeMedia, $qr->no_faktur, $qr->kode_pengaman);
                if (!hash_equals($expectedFullSig, (string) $qr->signature)) {
                    return [
                        'valid'   => false,
                        'code'    => 'INVALID_SIGNATURE',
                        'message' => 'Integritas tanda tangan digital QR tidak valid.',
                    ];
                }
            }
        } elseif (preg_match('/^(.+)-([A-Za-z0-9]{6})$/', $clean, $matches)) {
            // Mode Kode Manual Cadangan: FK/2026/11/0004-A1B2C3
            $noFaktur = trim($matches[1]);
            $prefix6  = strtoupper(trim($matches[2]));

            $candidates = FakturQrPembayaran::where('no_faktur', $noFaktur)
                ->where('kode_pengaman', 'like', $prefix6 . '%')
                ->orderByDesc('id')
                ->get();

            foreach ($candidates as $cand) {
                if (str_starts_with(strtoupper($cand->kode_pengaman), $prefix6)) {
                    $expectedSig = self::computeFullSignature($cand->kode_media, $cand->no_faktur, $cand->kode_pengaman);
                    if (hash_equals($expectedSig, (string) $cand->signature)) {
                        $qr = $cand;
                        break;
                    }
                }
            }
        } else {
            return [
                'valid'   => false,
                'code'    => 'INVALID_FORMAT',
                'message' => 'Format QR / Kode Manual tidak dikenali.',
            ];
        }

        if (!$qr) {
            return [
                'valid'   => false,
                'code'    => 'NOT_FOUND',
                'message' => 'Data QR Faktur tidak ditemukan di sistem.',
            ];
        }

        if ($qr->status === FakturQrPembayaran::STATUS_DISETOR) {
            $tglScan = $qr->discan_pada ? $qr->discan_pada->format('d-m-Y H:i') : '-';
            return [
                'valid'   => false,
                'code'    => 'ALREADY_DEPOSITED',
                'message' => "Faktur {$qr->no_faktur} sudah pernah discan/disetor ke Kasir pada {$tglScan} (Anti Setor Ganda).",
                'qr'      => $qr,
            ];
        }

        if ($qr->status === FakturQrPembayaran::STATUS_BATAL) {
            return [
                'valid'   => false,
                'code'    => 'CANCELLED_QR',
                'message' => "QR Faktur {$qr->no_faktur} sudah tidak berlaku (BATAL). Gunakan cetakan faktur terbaru.",
                'qr'      => $qr,
            ];
        }

        $transaksi = $qr->resolveTransaksi();
        if (!$transaksi) {
            return [
                'valid'   => false,
                'code'    => 'TRANSACTION_NOT_FOUND',
                'message' => "Data transaksi untuk faktur {$qr->no_faktur} tidak ditemukan.",
                'qr'      => $qr,
            ];
        }

        $live = self::extractDataTransaksi($transaksi, $qr->kategori);
        if ($live['is_batal'] || $live['nominal_bayar'] <= 0) {
            return [
                'valid'   => false,
                'code'    => 'TRANSACTION_INVALID',
                'message' => "Faktur {$qr->no_faktur} sudah dibatalkan atau tidak memiliki pembayaran di muka.",
                'qr'      => $qr,
            ];
        }

        return [
            'valid'   => true,
            'code'    => 'OK',
            'message' => 'QR Faktur valid dan siap diproses di Kasir.',
            'data'    => [
                'qr_id'             => $qr->id,
                'kategori'          => $qr->kategori,
                'kode_media'        => $qr->kode_media,
                'media_type'        => match ($qr->kategori) {
                    FakturQrPembayaran::KATEGORI_KORAN  => 'koran',
                    FakturQrPembayaran::KATEGORI_ONLINE => 'online',
                    FakturQrPembayaran::KATEGORI_TV     => 'priangan',
                },
                'transaksi_id'      => $live['transaksi_id'],
                'pelanggan_id'      => $live['pelanggan_id'],
                'no_faktur'         => $live['no_faktur'],
                'tanggal_transaksi' => $live['tanggal_transaksi'],
                'nama_pemasang'     => $live['nama_pemasang'],
                'sales_nama'        => $live['sales_nama'],
                'total_tagihan'     => (float) $live['total_tagihan'],
                'nominal_setor'     => (float) $live['nominal_bayar'],
                'sisa_piutang'      => (float) $live['sisa_piutang'],
                'status_bayar_awal' => ((float) $live['sisa_piutang'] <= 0) ? 'LUNAS' : 'SEBAGIAN',
                'kode_manual'       => $qr->kode_manual,
            ],
            'qr'        => $qr,
            'transaksi' => $transaksi,
        ];
    }

    /**
     * Tandai QR sebagai DISETOR secara atomik (SELECT ... FOR UPDATE) saat Kasir menyimpan penerimaan.
     * Disiapkan untuk dipanggil modul Kasir Nurwan nanti.
     */
    public static function tandaiDisetor(int $qrId, ?int $kasirUserId = null, ?int $penerimaanKasirId = null): FakturQrPembayaran
    {
        return DB::transaction(function () use ($qrId, $kasirUserId, $penerimaanKasirId) {
            $qr = FakturQrPembayaran::where('id', $qrId)->lockForUpdate()->firstOrFail();

            if ($qr->status === FakturQrPembayaran::STATUS_DISETOR) {
                throw new \RuntimeException("Faktur {$qr->no_faktur} sudah pernah disetor ke Kasir sebelumnya.");
            }

            if ($qr->status !== FakturQrPembayaran::STATUS_AKTIF) {
                throw new \RuntimeException("QR Faktur {$qr->no_faktur} sudah berstatus {$qr->status} dan tidak dapat disetor.");
            }

            $qr->update([
                'status'              => FakturQrPembayaran::STATUS_DISETOR,
                'discan_pada'         => now(),
                'discan_oleh'         => $kasirUserId ?? Auth::id(),
                'penerimaan_kasir_id' => $penerimaanKasirId,
            ]);

            return $qr->fresh();
        });
    }
}
