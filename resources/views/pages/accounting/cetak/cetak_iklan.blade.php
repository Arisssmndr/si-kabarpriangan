<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Sub-Ledger Iklan ({{ ucfirst($tab) }}) - HU Kabar Priangan</title>
    <style>
        @page {
            size: auto;
            margin: 6mm 8mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace, 'Lucida Console';
            font-size: 8.5pt;
            line-height: 1.25;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px 10px;
        }
        .screen-actions {
            max-width: 1300px;
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
            max-width: 1300px;
            margin: 0 auto;
            background: #fff;
            padding: 20px 28px;
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
        .header-kop { margin-bottom: 8px; }
        .header-kop .pt-name { font-size: 11pt; font-weight: bold; letter-spacing: 0.5px; margin: 0; }
        .header-kop .sub-name { font-size: 9pt; margin: 1px 0 4px 0; }
        .header-title {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            border-top: 2px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            margin-bottom: 8px;
        }
        .header-title .title { font-size: 11.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; }
        .header-title .meta { font-size: 9pt; font-weight: bold; }
        table.dami-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 12px;
        }
        table.dami-table th {
            text-align: left;
            font-weight: bold;
            padding: 5px 3px;
            border-top: 2px dashed #000;
            border-bottom: 1px dashed #000;
            white-space: nowrap;
        }
        table.dami-table td {
            padding: 3px 3px;
            vertical-align: top;
            white-space: nowrap;
        }
        .grandtotal-row td {
            border-top: 2px solid #000;
            border-bottom: 3px double #000;
            font-weight: bold;
            font-size: 8.5pt;
            padding-top: 4px;
            padding-bottom: 4px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .signature-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            text-align: center;
            font-size: 9pt;
        }
        .sig-box { width: 220px; }
        .sig-space { height: 45px; }
        .sig-name { font-weight: bold; border-top: 1px solid #000; padding-top: 4px; display: inline-block; min-width: 180px; }
        .sig-title { font-size: 8pt; margin-top: 2px; }
        @media print {
            body { background: #fff; padding: 0; margin: 0; font-size: 7.5pt; }
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
        <a href="{{ route('accounting.cetak.iklan', request()->all()) }}" class="btn-action btn-back">
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
                <div class="sub-name">HARIAN UMUM KABAR PRIANGAN - SUB-LEDGER DIVISI IKLAN</div>
            </div>

            <!-- Judul Laporan -->
            <div class="header-title">
                <span class="title">LAPORAN MUTASI KARTU PIUTANG IKLAN: {{ strtoupper(str_replace('_', ' ', $tab)) }}</span>
                <span class="meta">PERIODE: {{ \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') }} S/D {{ \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') }}</span>
            </div>

            <table class="dami-table">
                <thead>
                    @if($tab === 'penjualan')
                        <tr>
                            <th class="text-center" style="width: 4%;">NO</th>
                            <th style="width: 14%;">NO. FAKTUR</th>
                            <th style="width: 10%;">TANGGAL</th>
                            <th style="width: 12%;">NO. PERKI</th>
                            <th style="width: 26%;">PEMASANG / KLIEN</th>
                            <th class="text-center" style="width: 8%;">AKUN</th>
                            <th class="text-right" style="width: 12%;">TAGIHAN BRUTO</th>
                            <th class="text-right" style="width: 6%;">PPN</th>
                            <th class="text-right" style="width: 12%;">TOTAL NETTO</th>
                        </tr>
                    @elseif($tab === 'pembayaran')
                        <tr>
                            <th class="text-center" style="width: 4%;">NO</th>
                            <th style="width: 14%;">NO. KWITANSI</th>
                            <th style="width: 10%;">TGL BAYAR</th>
                            <th style="width: 14%;">NO. FAKTUR</th>
                            <th style="width: 10%;">TGL FAKTUR</th>
                            <th style="width: 24%;">PEMASANG</th>
                            <th style="width: 8%;">METODE</th>
                            <th class="text-right" style="width: 6%;">PPH 23</th>
                            <th class="text-right" style="width: 14%;">JUMLAH BAYAR</th>
                        </tr>
                    @elseif($tab === 'piutang')
                        <tr>
                            <th class="text-center" style="width: 4%;">NO</th>
                            <th style="width: 16%;">NO. FAKTUR</th>
                            <th style="width: 10%;">TGL MUAT</th>
                            <th style="width: 14%;">NO. PERKI</th>
                            <th style="width: 28%;">NAMA PEMASANG</th>
                            <th class="text-center" style="width: 8%;">AKUN</th>
                            <th class="text-right" style="width: 10%;">TOTAL FAKTUR</th>
                            <th class="text-right" style="width: 14%;">SISA PIUTANG</th>
                        </tr>
                    @else
                        <tr>
                            <th class="text-center" style="width: 4%;">NO</th>
                            <th style="width: 16%;">NO. BUKTI / FAKTUR</th>
                            <th style="width: 12%;">TANGGAL</th>
                            <th style="width: 38%;">KETERANGAN / PEMASANG</th>
                            <th class="text-right" style="width: 15%;">NILAI TRANSAKSI</th>
                            <th class="text-right" style="width: 15%;">SALDO / SISA</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @forelse($data as $key => $row)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            @if($tab === 'penjualan')
                                <td class="font-mono font-bold">{{ $row->no_faktur }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                <td class="font-mono">{{ $row->kode_perkiraan_display ?? '-' }}</td>
                                <td>{{ $row->pelanggan?->nama_pelanggan ?? $row->nama_pemohon }}</td>
                                <td class="text-center">{{ $row->tipe_akun_display ?? 'Piutang' }}</td>
                                <td class="text-right font-mono">{{ number_format($row->nilai_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right font-mono">{{ number_format($row->ppn_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right font-mono font-bold">{{ number_format($row->totaltagihan_transaksikoran ?? 0, 0, ',', '.') }}</td>
                            @elseif($tab === 'pembayaran')
                                <td class="font-mono font-bold">{{ $row->no_kwitansi ?? ('BKM-' . $row->id) }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal_bayar)->format('d/m/Y') }}</td>
                                <td class="font-mono">{{ $row->transaksiKoran?->no_faktur ?? '-' }}</td>
                                <td>{{ $row->transaksiKoran ? \Carbon\Carbon::parse($row->transaksiKoran->tanggal_transaksikoran)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $row->transaksiKoran?->nama_pemohon ?? '-' }}</td>
                                <td>{{ strtoupper($row->metode_pembayaran ?: 'Tunai') }}</td>
                                <td class="text-right font-mono">{{ number_format($row->pph23 ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right font-mono font-bold">{{ number_format($row->nominal_bayar ?? 0, 0, ',', '.') }}</td>
                            @elseif($tab === 'piutang')
                                <td class="font-mono font-bold">{{ $row->no_faktur }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                <td class="font-mono">{{ $row->kode_perkiraan_display ?? '-' }}</td>
                                <td>{{ $row->pelanggan?->nama_pelanggan ?? $row->nama_pemohon }}</td>
                                <td class="text-center">{{ $row->tipe_akun_display ?? 'Piutang' }}</td>
                                <td class="text-right font-mono">{{ number_format($row->totaltagihan_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right font-mono font-bold">{{ number_format($row->piutang_transaksikoran ?? 0, 0, ',', '.') }}</td>
                            @else
                                <td class="font-mono font-bold">{{ $row->no_faktur ?? ($row->nomor_bukti ?? '-') }}</td>
                                <td>{{ isset($row->tanggal_transaksikoran) ? \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $row->nama_pemohon ?? ($row->alasan ?? '-') }}</td>
                                <td class="text-right font-mono">{{ number_format($row->totaltagihan_transaksikoran ?? ($row->jumlah_koreksi ?? 0), 0, ',', '.') }}</td>
                                <td class="text-right font-mono font-bold">{{ number_format($row->piutang_transaksikoran ?? 0, 0, ',', '.') }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada catatan data pada periode ini.</td>
                        </tr>
                    @endforelse
                    <tr class="grandtotal-row">
                        <td colspan="{{ $tab === 'penjualan' ? 8 : ($tab === 'pembayaran' ? 8 : ($tab === 'piutang' ? 7 : 5)) }}" class="text-right">
                            TOTAL REKAPITULASI
                        </td>
                        <td class="text-right font-mono">
                            Rp {{ number_format($totalNominal, 0, ',', '.') }}
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
