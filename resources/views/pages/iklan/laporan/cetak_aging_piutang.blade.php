<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aging Piutang Iklan HU. Priangan - {{ $hariTanggal }}</title>
    <style>
        @page {
            size: auto;
            margin: 5mm 8mm;
        }

        body {
            font-family: 'Courier New', Courier, monospace, 'Lucida Console';
            font-size: 9.5pt;
            line-height: 1.28;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px 10px;
        }

        .screen-actions {
            max-width: 1380px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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

        /* Continuous Form Paper Simulation (14.875" x 11" Wide Continuous Sheet) */
        .paper-container {
            max-width: 1380px;
            margin: 0 auto;
            background: #fff;
            padding: 20px 28px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            position: relative;
            box-sizing: border-box;
            overflow-x: auto;
        }

        .paper-container::before {
            content: "";
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 10px;
            background-image: radial-gradient(circle, #94a3b8 2.5px, transparent 3px);
            background-size: 10px 20px;
            background-repeat: repeat-y;
            opacity: 0.35;
        }

        .paper-container::after {
            content: "";
            position: absolute;
            right: 8px;
            top: 0;
            bottom: 0;
            width: 10px;
            background-image: radial-gradient(circle, #94a3b8 2.5px, transparent 3px);
            background-size: 10px 20px;
            background-repeat: repeat-y;
            opacity: 0.35;
        }

        .paper-inner {
            margin: 0 14px;
        }

        .dami-header {
            margin-bottom: 6px;
        }

        .dami-pt {
            font-size: 11.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
        }

        .dami-title-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 2px;
        }

        .dami-title {
            font-size: 12.5pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .dami-page {
            font-size: 10.5pt;
            font-weight: bold;
        }

        .dami-date {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0 6px 0;
        }

        table.dami-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            line-height: 1.25;
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
            padding: 2.5px 3px;
            vertical-align: top;
            white-space: nowrap;
        }

        table.dami-table tr.bucket-header td {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            font-weight: bold;
            padding: 4px 3px;
            background: #f8fafc;
        }

        table.dami-table tr.subtotal-row td {
            border-top: 1px dotted #000;
            padding: 3px 3px 6px 3px;
            font-weight: bold;
        }

        .text-center { text-align: center !important; }
        .text-right  { text-align: right !important; }
        .font-bold   { font-weight: bold; }
        .uppercase   { text-transform: uppercase; }

        .summary-row td {
            border-top: 1px dashed #000;
            border-bottom: 2px dashed #000;
            padding: 5px 3px;
            font-weight: bold;
        }

        .dami-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: 8px;
            font-size: 10pt;
            font-weight: bold;
        }

        .signature-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            text-align: center;
            font-size: 10pt;
        }

        .sig-box {
            width: 240px;
        }

        .sig-space {
            height: 55px;
        }

        .sig-name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 4px;
            display: inline-block;
            min-width: 180px;
        }

        .sig-title {
            font-size: 8.5pt;
            margin-top: 2px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
                font-size: 8.5pt;
            }

            .screen-actions {
                display: none !important;
            }

            .paper-container {
                max-width: 100%;
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
                overflow: visible;
            }

            .paper-container::before,
            .paper-container::after {
                display: none !important;
            }

            .paper-inner {
                margin: 0;
            }

            table.dami-table {
                font-size: 8pt;
            }

            table.dami-table tr.bucket-header td {
                background: transparent;
            }
        }
    </style>
</head>
<body>

    <div class="screen-actions">
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('laporan.aging-piutang', request()->query()) }}" class="btn-action btn-back">
                &larr; Kembali ke Laporan Aging Piutang
            </a>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 11px; color: #475569;">
                Standar Form: <strong>Dot-Matrix 14.875" &times; 11" Continuous Sheet (Wide)</strong>
            </span>
            <button type="button" onclick="window.print()" class="btn-action btn-print">
                🖨️ Cetak Laporan Aging Piutang
            </button>
        </div>
    </div>

    <div class="paper-container">
        <div class="paper-inner">

            <div class="dami-header">
                <div class="dami-pt">PT. BERKAH PIKIRAN RAKYAT</div>
                <div class="dami-title-row">
                    <div class="dami-title">LAPORAN DAFTAR UMUR PIUTANG (AGING PIUTANG) IKLAN HU. PRIANGAN</div>
                    <div class="dami-page">hal : 1</div>
                </div>
                <div class="dami-date">
                    POSISI PER TANGGAL : {{ $hariTanggal }}
                </div>
            </div>

            <table class="dami-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 28px;">NO.</th>
                        <th style="width: 78px;">NO.ORDER</th>
                        <th style="width: 145px;">NO. PERKIRAAN</th>
                        <th style="width: 72px;">TANGGAL</th>
                        <th style="width: 175px;">PEMASANG</th>
                        <th style="width: 165px;">ALAMAT</th>
                        <th style="width: 75px;">MEDIA</th>
                        <th style="width: 115px;">SALES / AE</th>
                        <th class="text-right" style="width: 50px;">UMUR</th>
                        <th class="text-right" style="width: 90px;">Total Tagihan</th>
                        <th class="text-right" style="width: 85px;">Sudah Bayar</th>
                        <th class="text-right" style="width: 95px;">Sisa Piutang</th>
                    </tr>
                </thead>
                <tbody>
                    @php $noGlobal = 1; @endphp
                    @foreach($agingBuckets as $key => $bucket)
                        <tr class="bucket-header">
                            <td colspan="12">
                                &raquo; KELOMPOK UMUR PIUTANG : {{ strtoupper($bucket['label']) }} ({{ count($bucket['items']) }} FAKTUR)
                            </td>
                        </tr>
                        @forelse($bucket['items'] as $item)
                            <tr class="data-row">
                                <td class="text-center">{{ $noGlobal++ }}</td>
                                <td class="font-bold">{{ $item['no_order'] }}</td>
                                <td>{{ $item['no_perkiraan'] }}</td>
                                <td>{{ $item['tanggal'] }}</td>
                                <td class="font-bold uppercase">{{ Str::limit($item['pelanggan'], 24) }}</td>
                                <td class="uppercase">{{ Str::limit($item['alamat'], 22) }}</td>
                                <td class="uppercase">{{ $item['media'] }}</td>
                                <td class="uppercase">{{ Str::limit($item['sales'], 16) }}</td>
                                <td class="text-right">{{ $item['umur_hari'] }} hr</td>
                                <td class="text-right">{{ number_format($item['total_tagihan'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['terbayar'], 2, ',', '.') }}</td>
                                <td class="text-right font-bold">{{ number_format($item['piutang'], 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" style="padding: 4px 12px; color: #64748b;">
                                    -- Tidak ada faktur menunggak pada kategori {{ $bucket['label'] }} --
                                </td>
                            </tr>
                        @endforelse
                        @if(count($bucket['items']) > 0)
                            <tr class="subtotal-row">
                                <td colspan="9" class="text-right" style="padding-right: 12px;">
                                    Sub-Jumlah {{ $bucket['label'] }} :
                                </td>
                                <td class="text-right">{{ number_format($bucket['total_tagihan'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($bucket['total_bayar'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($bucket['total'], 2, ',', '.') }}</td>
                            </tr>
                        @endif
                    @endforeach

                    <tr class="summary-row">
                        <td colspan="9" style="padding-left: 4px;">
                            Jumlah Total Piutang Keseluruhan ({{ count($flatItems) }} Faktur)...
                        </td>
                        <td class="text-right">{{ number_format($totalTagihanKeseluruhan ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalTerbayarKeseluruhan ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalPiutangKeseluruhan ?? 0, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="dami-meta-row">
                <div>
                    Tasikmalaya, {{ now()->format('d-m-Y H:i:s') }}
                </div>
                <div>
                    Total Sisa Piutang Aktif : Rp {{ number_format($totalPiutangKeseluruhan ?? 0, 2, ',', '.') }}
                </div>
            </div>

            <div class="signature-grid">
                <div class="sig-box">
                    <div>Front Office / Penagihan,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">( {{ strtoupper(Auth::user()->name ?? 'IMAN NURJAMAN') }} )</div>
                    <div class="sig-title">Divisi Pemasaran &amp; Iklan</div>
                </div>

                <div class="sig-box">
                    <div>Asmen.Iklan,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">( SHINTA DEWI, SH. M.H )</div>
                    <div class="sig-title">Asisten Manajer Iklan</div>
                </div>

                <div class="sig-box">
                    <div>Mengetahui,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">( ............................................ )</div>
                    <div class="sig-title">Pimpinan / Manajer Keuangan</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>

