<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Penagihan Inkaso - Kabar Priangan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .logo-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 10px;
            color: #64748b;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            font-size: 10.5px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: ui-monospace, monospace;
        }
        .summary-box {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }
        .summary-table {
            width: 320px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .sig-col {
            text-align: center;
            width: 200px;
        }
        .sig-space {
            height: 60px;
        }
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn">🖨️ Cetak Dokumen</button>

    <div class="header">
        <div class="logo-box">
            <div>
                <div class="title">PT BERKAH KABAR PRIANGAN</div>
                <div class="subtitle">Divisi Keuangan &bull; Laporan Rekapitulasi Penagihan Piutang Inkaso</div>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-weight: bold; font-size: 11px;">REKAP INKASO</div>
            <div class="subtitle">Dicetak: {{ date('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="meta-box">
        <div>
            <strong>Periode Penugasan:</strong> {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}
        </div>
        <div>
            <strong>Petugas:</strong> {{ $petugas ? $petugas->name : 'Semua Petugas Inkaso' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th style="width: 85px;">No. Tugas</th>
                <th style="width: 75px;">Tanggal</th>
                <th>Petugas Kolektor</th>
                <th>No. Faktur &amp; Customer</th>
                <th class="text-right" style="width: 85px;">Target Tagihan</th>
                <th class="text-right" style="width: 85px;">Setoran Masuk</th>
                <th class="text-right" style="width: 85px;">Sisa Piutang</th>
                <th class="text-center" style="width: 65px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $idx => $p)
                @php
                    $tertagih = (float) $p->pembayaran->sum('nominal_bayar');
                    $sisa = $p->faktur ? (float) $p->faktur->sisa_piutang : 0;
                    $target = $p->faktur ? (float) $p->faktur->total_tagihan : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-mono">{{ $p->no_tugas }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal_tugas)->translatedFormat('d/m/Y') }}</td>
                    <td>{{ $p->petugas->name ?? '-' }}</td>
                    <td>
                        <strong>{{ $p->faktur->no_faktur ?? '-' }}</strong><br>
                        <span style="color: #64748b;">{{ $p->faktur->customer->nama_customer ?? '-' }}</span>
                    </td>
                    <td class="text-right font-mono">Rp {{ number_format($target, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: bold; color: #047857;">Rp {{ number_format($tertagih, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: bold; color: #b45309;">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                    <td class="text-center">
                        {{ $sisa == 0 ? 'LUNAS' : strtoupper($p->status_tugas) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada transaksi penugasan inkaso pada periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <th>Total Target Penugasan</th>
                <td class="text-right font-mono">Rp {{ number_format($totalTarget, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th style="color: #047857;">Total Setoran Tertagih (Kas Masuk)</th>
                <td class="text-right font-mono" style="font-weight: bold; color: #047857;">Rp {{ number_format($totalTertagih, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th style="color: #b45309;">Sisa Piutang Lapangan</th>
                <td class="text-right font-mono" style="font-weight: bold; color: #b45309;">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="signatures">
        <div class="sig-col">
            <div>Koordinator Inkaso,</div>
            <div class="sig-space"></div>
            <div>( ..................................... )</div>
        </div>
        <div class="sig-col">
            <div>Kasir Penerima Setoran,</div>
            <div class="sig-space"></div>
            <div>( ..................................... )</div>
        </div>
        <div class="sig-col">
            <div>Mengetahui,<br>Kepala Divisi Keuangan</div>
            <div class="sig-space"></div>
            <div>( ..................................... )</div>
        </div>
    </div>

</body>
</html>
