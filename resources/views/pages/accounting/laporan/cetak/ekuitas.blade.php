<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Ekuitas - {{ $periode ?? date('Y-m') }}</title>
    <style>
        @page {
            size: auto;
            margin: 8mm 10mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace, 'Lucida Console';
            font-size: 9.5pt;
            line-height: 1.3;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px 10px;
        }
        .screen-actions {
            max-width: 1000px;
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
            max-width: 1000px;
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
            font-size: 9pt;
            margin-bottom: 16px;
        }
        table.dami-table th {
            text-align: left;
            font-weight: bold;
            padding: 6px 4px;
            border-top: 2px dashed #000;
            border-bottom: 1px dashed #000;
        }
        table.dami-table td {
            padding: 4px 4px;
            vertical-align: top;
        }
        .subtotal-row td {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            font-weight: bold;
        }
        .grandtotal-row td {
            border-top: 2px solid #000;
            border-bottom: 3px double #000;
            font-weight: bold;
            font-size: 10pt;
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
            body { background: #fff; padding: 0; margin: 0; font-size: 8.5pt; }
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
        <a href="{{ route('accounting.laporan.ekuitas') }}" class="btn-action btn-back">
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
                <div class="sub-name">HARIAN UMUM KABAR PRIANGAN - DEPARTEMEN AKUNTANSI & KEUANGAN</div>
            </div>

            <!-- Judul Laporan -->
            <div class="header-title">
                <span class="title">LAPORAN PERUBAHAN EKUITAS (STATEMENT OF CHANGES IN EQUITY)</span>
                <span class="meta">PERIODE: {{ \Carbon\Carbon::parse(($periode ?? date('Y-m')) . '-01')->translatedFormat('F Y') }}</span>
            </div>

            <table class="dami-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">KOMPONEN EKUITAS</th>
                        <th class="text-right" style="width: 20%;">MODAL SAHAM</th>
                        <th class="text-right" style="width: 20%;">LABA DITAHAN</th>
                        <th class="text-right" style="width: 20%;">TOTAL EKUITAS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Saldo Awal Periode</td>
                        <td class="text-right font-mono font-bold">Rp {{ number_format($modalAwal, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">Rp 0</td>
                        <td class="text-right font-mono font-bold">Rp {{ number_format($modalAwal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Laba Bersih Tahun Berjalan</td>
                        <td class="text-right font-mono">-</td>
                        <td class="text-right font-mono">+ Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">+ Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Distribusi Dividen / Penarikan Pemilik (Prive)</td>
                        <td class="text-right font-mono">-</td>
                        <td class="text-right font-mono">- Rp {{ number_format($priveDividen, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">- Rp {{ number_format($priveDividen, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grandtotal-row">
                        <td>SALDO AKHIR EKUITAS</td>
                        <td class="text-right font-mono">Rp {{ number_format($modalAwal, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">Rp {{ number_format($labaBersih - $priveDividen, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">Rp {{ number_format($modalAkhir, 0, ',', '.') }}</td>
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
