<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Faktur Sirkulasi - HU Kabar Priangan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 20px;
            background: #fff;
        }
        .header {
            border-bottom: 2px solid #312e81;
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #312e81;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #475569;
        }
        .title-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-box h2 {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #0f172a;
        }
        .title-box span {
            font-size: 11px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        th {
            background-color: #eef2ff;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #312e81;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .font-bold { font-weight: bold; }
        .total-row {
            background-color: #e0e7ff;
            font-weight: bold;
            border-top: 2px solid #312e81;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
        @media print {
            body { margin: 0; font-size: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <!-- Tombol Cetak / Kembali -->
    <div class="no-print" style="margin-bottom: 15px; display: flex; gap: 8px;">
        <button onclick="window.print()" style="padding: 6px 12px; background: #4338ca; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Dokumen (PDF)
        </button>
        <button onclick="window.close()" style="padding: 6px 12px; background: #e2e8f0; color: #334155; border: none; border-radius: 6px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <!-- Kop Surat Resmi -->
    <div class="header">
        <div>
            <h1>HU KABAR PRIANGAN</h1>
            <p>PT BERKAH PIKIRAN RAKYAT MEDIA</p>
            <p>Divisi Keuangan &amp; Sirkulasi Koran Harian</p>
            <p>Jl. SL Tobing No. 99, Kota Tasikmalaya - Telp: (0265) 334567</p>
        </div>
        <div style="text-align: right;">
            <p><strong>FORMULIR: FAK-SRK-01</strong></p>
            <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
            <p>Oleh: {{ auth()->user()->name ?? 'Administrator' }}</p>
        </div>
    </div>

    <div class="title-box">
        <h2>REKAPITULASI FAKTUR PENJUALAN SIRKULASI KORAN</h2>
        <span>Periode Distribusi: <strong>{{ $periodeJudul }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>{{ $mode === 'bulan' ? 'Bulan Distribusi' : 'Tanggal Faktur' }}</th>
                <th class="text-center" style="width: 70px;">Total Faktur</th>
                <th class="text-center" style="width: 60px;">Susulan</th>
                <th class="text-center" style="width: 60px;">Insidentil</th>
                <th class="text-right" style="width: 110px;">Nilai Tagihan</th>
                <th class="text-right" style="width: 110px;">Terbayar (Kas)</th>
                <th class="text-right" style="width: 110px;">Sisa Piutang</th>
                <th class="text-center" style="width: 70px;">% Tertagih</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $index => $row)
                @php
                    $pct = $row->total_tagihan > 0 ? round(($row->total_terbayar / $row->total_tagihan) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">
                        @if($mode === 'bulan')
                            {{ \Carbon\Carbon::create()->month((int)$row->bulan)->translatedFormat('F') }}
                        @else
                            {{ \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('d F Y') }}
                        @endif
                    </td>
                    <td class="text-center font-mono font-bold">{{ $row->total_faktur }}</td>
                    <td class="text-center font-mono">{{ $row->total_susulan ?? 0 }}</td>
                    <td class="text-center font-mono">{{ $row->total_insidentil ?? 0 }}</td>
                    <td class="text-right font-mono font-bold">Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($row->total_piutang, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $pct }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data rekapitulasi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($rekapData) > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-center uppercase font-bold">TOTAL KESELURUHAN</td>
                    <td class="text-center font-mono font-bold">{{ $grandFaktur }}</td>
                    <td class="text-center font-mono">{{ $rekapData->sum('total_susulan') }}</td>
                    <td class="text-center font-mono">{{ $rekapData->sum('total_insidentil') }}</td>
                    <td class="text-right font-mono font-bold">Rp {{ number_format($grandTagihan, 0, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold">Rp {{ number_format($grandTerbayar, 0, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold">Rp {{ number_format($grandPiutang, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ $grandTagihan > 0 ? round(($grandTerbayar / $grandTagihan) * 100, 1) : 0 }}%</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <div class="signatures">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p><strong>Manajer Keuangan</strong></p>
            <div class="signature-space"></div>
            <p><strong>( ......................................... )</strong></p>
        </div>

        <div class="signature-box">
            <p>Tasikmalaya, {{ now()->translatedFormat('d F Y') }}</p>
            <p><strong>Bagian Administrasi Sirkulasi</strong></p>
            <div class="signature-space"></div>
            <p><strong>( {{ auth()->user()->name ?? 'Petugas Sirkulasi' }} )</strong></p>
        </div>
    </div>

</body>
</html>
