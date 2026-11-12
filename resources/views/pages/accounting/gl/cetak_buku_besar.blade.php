<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Besar (General Ledger) - {{ $akun?->nama_perkiraan ?? $akunPilihan }}</title>
    <style>
        @page {
            size: auto;
            margin: 8mm 10mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace, 'Lucida Console';
            font-size: 9pt;
            line-height: 1.25;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px 10px;
        }
        .screen-actions {
            max-width: 1100px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            gap: 10px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-print {
            background: #0A72AC;
            color: white;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.12);
        }
        .btn-print:hover { background: #085a88; }
        .btn-back {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover { background: #f8fafc; }
        .paper-container {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            padding: 24px 32px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            box-sizing: border-box;
            position: relative;
        }
        .paper-container::before {
            content: "";
            position: absolute;
            left: 8px; top: 0; bottom: 0; width: 10px;
            background-image: radial-gradient(circle, #94a3b8 2.5px, transparent 3px);
            background-size: 10px 20px;
            background-repeat: repeat-y;
            opacity: 0.35;
        }
        .paper-container::after {
            content: "";
            position: absolute;
            right: 8px; top: 0; bottom: 0; width: 10px;
            background-image: radial-gradient(circle, #94a3b8 2.5px, transparent 3px);
            background-size: 10px 20px;
            background-repeat: repeat-y;
            opacity: 0.35;
        }
        .paper-inner { margin: 0 16px; }
        .header-kop { margin-bottom: 12px; }
        .header-kop .pt-name { font-size: 11.5pt; font-weight: bold; letter-spacing: 0.5px; margin: 0; }
        .header-kop .sub-name { font-size: 9.5pt; margin: 1px 0 6px 0; }
        .header-title {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            border-top: 2px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 6px 0;
            margin-bottom: 12px;
        }
        .header-title .title { font-size: 12.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; }
        .header-title .meta { font-size: 9.5pt; font-weight: bold; }
        table.dami-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 16px;
        }
        table.dami-table th {
            text-align: left;
            font-weight: bold;
            padding: 6px 4px;
            border-top: 2px dashed #000;
            border-bottom: 1px dashed #000;
            white-space: nowrap;
        }
        table.dami-table td {
            padding: 3px 4px;
            vertical-align: top;
        }
        .grandtotal-row td {
            border-top: 2px solid #000;
            border-bottom: 3px double #000;
            font-weight: bold;
            font-size: 9.5pt;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .signature-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 32px;
            text-align: center;
            font-size: 9.5pt;
        }
        .sig-box { width: 220px; }
        .sig-space { height: 50px; }
        .sig-name { font-weight: bold; border-top: 1px solid #000; padding-top: 4px; display: inline-block; min-width: 180px; }
        .sig-title { font-size: 8.5pt; margin-top: 2px; }
        @media print {
            body { background: #fff; padding: 0; margin: 0; font-size: 8pt; }
            .screen-actions { display: none !important; }
            .paper-container { max-width: 100%; border: none; box-shadow: none; padding: 0; }
            .paper-container::before, .paper-container::after { display: none; }
            .paper-inner { margin: 0; }
        }
    </style>
</head>
<body>

    <!-- Tombol Aksi Layar -->
    <div class="screen-actions">
        <a href="{{ route('accounting.gl.buku-besar', ['kode_perkiraan' => $akunPilihan]) }}" class="btn-action btn-back">
            &larr; Kembali ke Dashboard
        </a>
        <button onclick="window.print()" class="btn-action btn-print">
            🖨️ Cetak Dokumen Resmi
        </button>
    </div>

    <!-- Halaman Dokumen Cetak -->
    <div class="paper-container">
        <div class="paper-inner">
            
            <!-- Kop Kabar Priangan -->
            <div class="header-kop">
                <div class="pt-name">PT. BERKAH PIKIRAN RAKYAT</div>
                <div class="sub-name">HARIAN UMUM KABAR PRIANGAN - BUKU BESAR PEMBUKUAN (GENERAL LEDGER)</div>
            </div>

            <!-- Judul Laporan -->
            <div class="header-title">
                <span class="title">KARTU BUKU BESAR: {{ $akun?->kode_perkiraan ?? $akunPilihan }}</span>
                <span class="meta">{{ strtoupper($akun?->nama_perkiraan ?? 'REKENING BUKU BESAR') }}</span>
            </div>

            <table class="dami-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 4%;">NO</th>
                        <th style="width: 10%;">TANGGAL</th>
                        <th style="width: 15%;">NO. JURNAL</th>
                        <th style="width: 43%;">URAIAN TRANSAKSI / KETERANGAN</th>
                        <th class="text-right" style="width: 14%;">DEBET (RP)</th>
                        <th class="text-right" style="width: 14%;">KREDIT (RP)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutasiBuku as $key => $m)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($m->jurnalUmum?->tanggal ?? date('Y-m-d'))->format('d/m/Y') }}</td>
                            <td class="font-mono font-bold">{{ $m->jurnalUmum?->no_jurnal ?? '-' }}</td>
                            <td>{{ $m->jurnalUmum?->keterangan ?? '-' }}</td>
                            <td class="text-right font-mono font-bold">
                                {{ $m->debet > 0 ? number_format($m->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right font-mono font-bold">
                                {{ $m->kredit > 0 ? number_format($m->kredit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 20px;">Belum ada mutasi transaksi untuk akun buku besar ini.</td>
                        </tr>
                    @endforelse
                    <tr class="grandtotal-row">
                        <td colspan="4" class="text-right">TOTAL MUTASI PERIODE</td>
                        <td class="text-right font-mono">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="font-weight: bold; font-size: 10pt;">
                        <td colspan="4" class="text-right">SALDO AKHIR BUKU BESAR ({{ $totalDebet >= $totalKredit ? 'DEBET' : 'KREDIT' }})</td>
                        <td colspan="2" class="text-right font-mono">
                            Rp {{ number_format(abs($totalDebet - $totalKredit), 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Lembar Tanda Tangan Resmi 3 Kolom -->
            <div class="signature-grid">
                <div class="sig-box">
                    <div>Disusun Oleh,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ auth()->user()->name ?? 'Staf Akuntansi & GL' }}</div>
                    <div class="sig-title">Staf Akuntansi &amp; GL</div>
                </div>
                <div class="sig-box">
                    <div>Diperiksa Oleh,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">Manager Keuangan</div>
                    <div class="sig-title">Ka. Bagian Keuangan &amp; Akuntansi</div>
                </div>
                <div class="sig-box">
                    <div>Disetujui Oleh,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">Pimpinan Perusahaan</div>
                    <div class="sig-title">Direktur Utama</div>
                </div>
            </div>

            <div style="margin-top: 16px; font-size: 7.5pt; color: #555; text-align: right;">
                Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB | Sistem Informasi SIKAPRI
            </div>

        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
