<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembatalan Iklan HU. Priangan - {{ $hariTanggal }}</title>
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
            max-width: 1280px;
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

        /* Continuous Form Paper Simulation */
        .paper-container {
            max-width: 1280px;
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
            padding: 3px 3px;
            vertical-align: top;
            white-space: nowrap;
        }

        table.dami-table tr.data-row:hover {
            background-color: #f8fafc;
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

            table.dami-table tr.data-row:hover {
                background-color: transparent;
            }
        }
    </style>
</head>
<body>

    <div class="screen-actions">
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('laporan.pembatalan', request()->query()) }}" class="btn-action btn-back">
                &larr; Kembali ke Laporan Pembatalan
            </a>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 11px; color: #475569;">
                Standar Form: <strong>Dot-Matrix 14.875" &times; 11" Continuous Sheet</strong>
            </span>
            <button type="button" onclick="window.print()" class="btn-action btn-print">
                🖨️ Cetak Laporan Pembatalan Iklan
            </button>
        </div>
    </div>

    <div class="paper-container">
        <div class="paper-inner">

            <div class="dami-header">
                <div class="dami-pt">PT. BERKAH PIKIRAN RAKYAT</div>
                <div class="dami-title-row">
                    <div class="dami-title">LAPORAN PEMBATALAN ORDER IKLAN HU. PRIANGAN</div>
                    <div class="dami-page">hal : 1</div>
                </div>
                <div class="dami-date">
                    PERIODE : {{ $hariTanggal }}
                </div>
            </div>

            <table class="dami-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 28px;">NO.</th>
                        <th style="width: 80px;">NO.ORDER</th>
                        <th style="width: 115px;">NO. FAKTUR</th>
                        <th style="width: 75px;">TGL ORDER</th>
                        <th style="width: 175px;">PEMASANG</th>
                        <th style="width: 110px;">JENIS IKLAN</th>
                        <th style="width: 110px;">SALES / AE</th>
                        <th class="text-right" style="width: 85px;">Nilai Batal</th>
                        <th style="width: 185px;">ALASAN PEMBATALAN</th>
                        <th style="width: 105px;">OTORISASI</th>
                        <th style="width: 95px;">WAKTU BATAL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembatalanList as $idx => $item)
                        <tr class="data-row">
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="font-bold">{{ $item->id ? (2026100000 + $item->id) : $item->nofakturkoran }}</td>
                            <td>{{ $item->nofakturkoran }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksikoran)->format('d-m-Y') }}</td>
                            <td class="font-bold uppercase">{{ Str::limit(strtoupper($item->pelanggan?->nama_instansi ?: $item->nama_pemasangkoran), 24) }}</td>
                            <td class="uppercase">{{ Str::limit(strtoupper($item->iklankoran?->nama_jenis ?: ($item->kategori_form ?: 'IKLAN')), 15) }}</td>
                            <td class="uppercase">{{ Str::limit(strtoupper($item->salesAe ? ($item->salesAe->kode_ae . ' ' . $item->salesAe->nama_ae) : ($item->sales_iklankoran ?? 'FO')), 16) }}</td>
                            <td class="text-right font-bold">{{ number_format($item->totaltagihan_transaksikoran ?? 0, 2, ',', '.') }}</td>
                            <td class="uppercase">{{ Str::limit(strtoupper($item->alasan_batal ?? '-'), 26) }}</td>
                            <td class="uppercase">{{ Str::limit(strtoupper($item->dibatalkanOleh?->name ?? 'ADMIN'), 14) }}</td>
                            <td>{{ $item->dibatalkan_at ? \Carbon\Carbon::parse($item->dibatalkan_at)->format('d-m-Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center" style="padding: 24px 8px; color: #64748b;">
                                --- Tidak ada transaksi pembatalan iklan pada periode {{ $hariTanggal }} ---
                            </td>
                        </tr>
                    @endforelse

                    <tr class="summary-row">
                        <td colspan="7" style="padding-left: 4px;">
                            Jumlah Pembatalan ({{ $pembatalanList->count() }} Order)...
                        </td>
                        <td class="text-right">{{ number_format($totalTagihan ?? 0, 2, ',', '.') }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>

            <div class="dami-meta-row">
                <div>
                    Tasikmalaya, {{ now()->format('d-m-Y H:i:s') }}
                </div>
                <div>
                    Total Order Dibatalkan : {{ $pembatalanList->count() }} Order
                </div>
            </div>

            <div class="signature-grid">
                <div class="sig-box">
                    <div>Front Office,</div>
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

