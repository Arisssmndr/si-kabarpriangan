<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Piutang Iklan Bulanan - HU Kabar Priangan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 20px;
            background: #fff;
        }
        .header {
            border-bottom: 2px solid #0f172a;
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
            color: #0f172a;
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
        .summary-cards {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
        }
        .summary-card {
            flex: 1;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 10px;
            background: #f8fafc;
        }
        .summary-card .label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }
        .summary-card .value {
            font-size: 13px;
            font-weight: bold;
            margin-top: 2px;
            font-family: 'Courier New', Courier, monospace;
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
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .font-bold { font-weight: bold; }
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-space {
            height: 60px;
        }
        @media print {
            body { margin: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 16px; background: #be123c; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨 Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <!-- Header Perusahaan -->
    <div class="header">
        <div>
            <h1>HARIAN UMUM KABAR PRIANGAN</h1>
            <p>PT BERKAH PIKIRAN RAKYAT</p>
            <p>Jl. Dr. Soekardjo No. 58 Tasikmalaya - Jawa Barat | Telp: (0265) 331805</p>
        </div>
        <div style="text-align: right;">
            <p><strong>DIVISI KEUANGAN & KONTROL KREDIT</strong></p>
            <p>Modul Faktur: Piutang Iklan</p>
            <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
        </div>
    </div>

    <!-- Judul Dokumen -->
    <div class="title-box">
        <h2>LEMBAR REKAPITULASI PIUTANG IKLAN PER BULAN</h2>
        <span>Periode Rekap: {{ $periodeJudul }}</span>
    </div>

    <!-- Ringkasan Aging Piutang -->
    <div class="summary-cards">
        <div class="summary-card" style="border-left: 4px solid #e11d48;">
            <div class="label">Total Piutang Berjalan</div>
            <div class="value" style="color: #e11d48;">Rp {{ number_format($grandTotalPiutang, 0, ',', '.') }}</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">{{ $grandTotalPiutangCount }} Faktur Belum Lunas</div>
        </div>
        <div class="summary-card" style="border-left: 4px solid #059669;">
            <div class="label">1. Lancar (Belum Tempo)</div>
            <div class="value" style="color: #059669;">Rp {{ number_format($grandLancarNominal, 0, ',', '.') }}</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">Tagihan aman</div>
        </div>
        <div class="summary-card" style="border-left: 4px solid #d97706;">
            <div class="label">2. Perhatian (1 - 30 Hari)</div>
            <div class="value" style="color: #d97706;">Rp {{ number_format($grandPerhatianNominal, 0, ',', '.') }}</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">Lewat tempo s/d 1 bulan</div>
        </div>
        <div class="summary-card" style="border-left: 4px solid #dc2626;">
            <div class="label">3. Kritis (> 30 Hari)</div>
            <div class="value" style="color: #dc2626;">Rp {{ number_format($grandKritisNominal, 0, ',', '.') }}</div>
            <div style="font-size: 9px; color: #dc2626; margin-top: 2px;">Prioritas penagihan inkaso</div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi Piutang Bulanan -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>Bulan Faktur</th>
                <th class="text-center" style="width: 70px;">Jml Faktur</th>
                <th class="text-right">Total Tagihan (Rp)</th>
                <th class="text-right">Terbayar / DP (Rp)</th>
                <th class="text-right">Sisa Piutang (Rp)</th>
                <th class="text-right">Lancar (Rp)</th>
                <th class="text-right">Perhatian (Rp)</th>
                <th class="text-right">Kritis (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapBulanan as $idx => $row)
                @php
                    $namaBulan = \Carbon\Carbon::create()->month((int)$row->bulan)->translatedFormat('F');
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $namaBulan }} {{ $tahun }}</td>
                    <td class="text-center font-bold">{{ $row->total_faktur }}</td>
                    <td class="text-right font-mono">{{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #059669;">{{ number_format($row->total_terbayar, 0, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold" style="color: #e11d48;">{{ number_format($row->total_piutang, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #059669;">{{ number_format($row->nominal_lancar, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #d97706;">{{ number_format($row->nominal_perhatian, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($row->nominal_kritis, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data piutang iklan untuk periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($rekapBulanan->isNotEmpty())
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-center">GRAND TOTAL</td>
                    <td class="text-center">{{ $grandTotalPiutangCount }}</td>
                    <td class="text-right font-mono">{{ number_format($grandTotalTagihan, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #059669;">{{ number_format($grandTotalTerbayar, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #e11d48;">{{ number_format($grandTotalPiutang, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #059669;">{{ number_format($grandLancarNominal, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #d97706;">{{ number_format($grandPerhatianNominal, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($grandKritisNominal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <div class="signatures">
        <div class="signature-box">
            <p>Dibuat Oleh,</p>
            <div class="signature-space"></div>
            <p><strong>Administrasi Faktur & Piutang</strong></p>
            <p style="font-size: 9px; color: #64748b;">HU Kabar Priangan</p>
        </div>
        <div class="signature-box">
            <p>Diperiksa Oleh,</p>
            <div class="signature-space"></div>
            <p><strong>Supervisor Administrasi</strong></p>
            <p style="font-size: 9px; color: #64748b;">HU Kabar Priangan</p>
        </div>
        <div class="signature-box">
            <p>Mengetahui / Menyetujui,</p>
            <div class="signature-space"></div>
            <p><strong>Manajer Keuangan</strong></p>
            <p style="font-size: 9px; color: #64748b;">HU Kabar Priangan</p>
        </div>
    </div>

</body>
</html>
