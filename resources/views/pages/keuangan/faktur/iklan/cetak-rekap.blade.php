<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Faktur Iklan - HU Kabar Priangan</title>
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
            width: 220px;
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
        <button onclick="window.print()" style="padding: 6px 16px; background: #0f172a; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨 Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <!-- Kop Surat Formal Sesuai Sistem Asli Kabar Priangan -->
    <div class="header">
        <div>
            <h1>HU. KABAR PRIANGAN</h1>
            <p>PT. BERKAH PIKIRAN RAKYAT</p>
            <p>Jl. Dindingari / Jl. R.E. Martadinata No. 335 Tasikmalaya - Jawa Barat</p>
            <p>Telp: (0265) 335012 / Email: iklan@kabarpriangan.co.id</p>
        </div>
        <div style="text-align: right;">
            <p><strong>DIVISI KEUANGAN &amp; FAKTUR</strong></p>
            <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
            <p>Operator: {{ auth()->user()->name ?? 'Staf Faktur' }}</p>
        </div>
    </div>

    <div class="title-box">
        <h2>LEMBAR REKAPITULASI FAKTUR IKLAN</h2>
        <span>Kriteria: {{ $periodeJudul }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th>
                    @if($mode === 'tanggal')
                        Tanggal Faktur
                    @elseif(isset($group) && ($group === 'per_bulan' || $group === 'per_ae_bulan'))
                        Bulan
                    @elseif(isset($group) && ($group === 'per_tahun' || $group === 'per_ae_tahun'))
                        Tahun
                    @elseif(isset($group) && in_array($group, ['rekap_ae_bulan', 'rekap_ae_tahun']))
                        Account Executive (AE)
                    @elseif(isset($group) && $group === 'per_pemasang')
                        Pemasang / Klien
                    @else
                        Periode / Kategori
                    @endif
                </th>
                <th class="text-center" style="width: 80px;">Faktur Terbit</th>
                <th class="text-right" style="width: 130px;">Total Tagihan</th>
                <th class="text-right" style="width: 130px;">Tertagih (Kas)</th>
                <th class="text-right" style="width: 130px;">Sisa Piutang</th>
                <th class="text-center" style="width: 90px;">Status Lunas</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalFakturAll = 0;
                $totalTagihanAll = 0;
                $totalTerbayarAll = 0;
                $totalPiutangAll = 0;
            @endphp
            @forelse($rekapData as $index => $row)
                @php
                    $totalFakturAll += (int) $row->total_faktur;
                    $totalTagihanAll += (float) $row->total_tagihan;
                    $totalTerbayarAll += (float) $row->total_terbayar;
                    $totalPiutangAll += (float) $row->total_piutang;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        @if($mode === 'tanggal')
                            {{ \Carbon\Carbon::parse($row->periode_key)->translatedFormat('d F Y') }}
                        @elseif(isset($group) && ($group === 'per_bulan' || $group === 'per_ae_bulan'))
                            @php
                                $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                            @endphp
                            Bulan {{ $row->periode_key }} ({{ $namaBulan[(int)$row->periode_key] ?? '' }})
                        @elseif(isset($group) && $group === 'per_pemasang')
                            @php
                                $c = \App\Models\Customer::find($row->periode_key);
                            @endphp
                            {{ $c->nama_customer ?? 'Pemasang #' . $row->periode_key }}
                        @else
                            {{ $row->periode_key }}
                        @endif
                    </td>
                    <td class="text-center font-mono">{{ $row->total_faktur }}</td>
                    <td class="text-right font-mono font-bold">Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($row->total_piutang, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $row->jumlah_lunas }} Lunas / {{ $row->jumlah_piutang }} Piutang</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data rekapitulasi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($rekapData) > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-center uppercase">TOTAL KESELURUHAN</td>
                    <td class="text-center font-mono">{{ $totalFakturAll }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($totalTagihanAll, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($totalTerbayarAll, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($totalPiutangAll, 0, ',', '.') }}</td>
                    <td class="text-center">100% Terkonsolidasi</td>
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
            <p><strong>Bagian Administrasi Faktur</strong></p>
            <div class="signature-space"></div>
            <p><strong>( {{ auth()->user()->name ?? 'Staf Faktur' }} )</strong></p>
        </div>
    </div>

</body>
</html>
