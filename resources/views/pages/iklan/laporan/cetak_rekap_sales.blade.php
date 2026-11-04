@php
    $initialMode = $modeCetak ?: ($selectedAe ? 'per_sales' : 'semua_ae');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Iklan Penerbitan HU. Priangan - {{ $hariTanggal }}</title>
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
            max-width: 1420px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
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

        .btn-toggle {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #94a3b8;
        }
        .btn-toggle:hover { background: #e2e8f0; }

        .btn-toggle.active-mode {
            background: #0A72AC;
            color: #ffffff;
            border-color: #0A72AC;
        }

        /* Continuous Form Paper Simulation (14.875" x 11" Wide Continuous Sheet) */
        .paper-container {
            max-width: 1420px;
            margin: 0 auto 24px auto;
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

        /* Header Dokumen Continuous Form HU. Priangan */
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

        .dami-perolehan {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .dami-date {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0 6px 0;
        }

        /* Tabel Utama Continuous Form */
        table.dami-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.8pt;
            line-height: 1.25;
        }

        table.dami-table th {
            text-align: left;
            font-weight: bold;
            padding: 4px 3px;
            white-space: nowrap;
            vertical-align: bottom;
        }

        table.dami-table thead tr.thead-top th {
            border-top: 2px dashed #000;
        }

        table.dami-table thead tr.thead-bottom th {
            border-bottom: 1px dashed #000;
        }

        table.dami-table thead tr.thead-single th {
            border-top: 2px dashed #000;
            border-bottom: 1px dashed #000;
        }

        table.dami-table th.sub-group-header {
            border-bottom: 1px dashed #000;
            text-align: center !important;
        }

        table.dami-table td {
            padding: 2.5px 3px;
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
        .modern-col  { display: none; }
        body.show-modern .modern-col { display: table-cell; }

        /* Ringkasan Footer */
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
            font-size: 9.5pt;
            font-weight: bold;
            flex-wrap: wrap;
            gap: 8px;
        }

        /* Kolom Tanda Tangan */
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
                font-size: 8.2pt;
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
                font-size: 7.8pt;
            }

            table.dami-table tr.data-row:hover {
                background-color: transparent;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Navigasi & Aksi Layar -->
    <div class="screen-actions">
        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
            <a href="{{ route('laporan.rekap-sales', request()->query()) }}" class="btn-action btn-back">
                &larr; Kembali
            </a>
            <button type="button" onclick="setSheetMode('semua_ae')" id="btnModeSemuaAe" class="btn-action btn-toggle {{ $initialMode === 'semua_ae' ? 'active-mode' : '' }}">
                #3 Penjualan Semua AE (Sektor)
            </button>
            <button type="button" onclick="setSheetMode('per_sales')" id="btnModePerSales" class="btn-action btn-toggle {{ $initialMode === 'per_sales' ? 'active-mode' : '' }}">
                #1 Penjualan Per Sales / AE
            </button>
            <button type="button" onclick="setSheetMode('gabungan')" id="btnModeGabungan" class="btn-action btn-toggle {{ $initialMode === 'gabungan' ? 'active-mode' : '' }}">
                Gabungan (#3 &amp; #1)
            </button>
            <button type="button" onclick="toggleModernCols()" id="btnToggleModern" class="btn-action btn-toggle">
                + Kolom Standar Industri (Kas, Piutang, Faktur)
            </button>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 11px; color: #475569;">
                Form: <strong>Dot-Matrix 14.875" &times; 11" Continuous Sheet</strong>
            </span>
            <button type="button" onclick="window.print()" class="btn-action btn-print">
                🖨️ Cetak Dokumen Ini
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 1 (PRINT #3): LAPORAN PENJUALAN IKLAN SEMUA AE (SEKTOR PEROLEHAN)  -->
    <!-- ========================================================================= -->
    <div class="paper-container" id="sheetRekapSektor" style="{{ $initialMode === 'per_sales' ? 'display: none;' : '' }}">
        <div class="paper-inner">

            <!-- Header Dokumen (Sesuai Print #3) -->
            <div class="dami-header">
                <div class="dami-pt">PT. BERKAH PIKIRAN RAKYAT</div>
                <div class="dami-title-row">
                    <div class="dami-title">LAPORAN PENJUALAN IKLAN PENERBITAN HU. PRIANGAN</div>
                    <div class="dami-page">hal : 1</div>
                </div>
                <div class="dami-date">
                    HARI / TANGGAL : {{ $hariTanggal }}
                </div>
            </div>

            <!-- Tabel Rekap Penjualan Per Sektor Perolehan / AE (18 Kolom Sesuai Print #3 + Kolom Modern Opsional) -->
            <table class="dami-table">
                <thead>
                    <tr class="thead-top">
                        <th rowspan="2" class="text-center" style="width: 24px; border-bottom: 1px dashed #000;">No.</th>
                        <th rowspan="2" style="width: 160px; border-bottom: 1px dashed #000;">Sektor Perolehan</th>
                        <th colspan="4" class="sub-group-header">Order</th>
                        <th rowspan="2" class="text-right" style="width: 40px; border-bottom: 1px dashed #000;">Baris</th>
                        <th rowspan="2" class="text-right" style="width: 48px; border-bottom: 1px dashed #000;">Kolom<br>(mm)</th>
                        <th rowspan="2" class="text-right" style="width: 48px; border-bottom: 1px dashed #000;">Displ<br>(mm)</th>
                        <th rowspan="2" class="text-right" style="width: 52px; border-bottom: 1px dashed #000;">Total<br>Mili</th>
                        <th rowspan="2" class="text-right" style="width: 78px; border-bottom: 1px dashed #000;">Brutto</th>
                        <th rowspan="2" class="text-right" style="width: 68px; border-bottom: 1px dashed #000;">Reduksi</th>
                        <th rowspan="2" class="text-right" style="width: 78px; border-bottom: 1px dashed #000;">Netto</th>
                        <th rowspan="2" class="text-right" style="width: 66px; border-bottom: 1px dashed #000;">PPn</th>
                        <th rowspan="2" class="text-right" style="width: 54px; border-bottom: 1px dashed #000;">Materai</th>
                        <th rowspan="2" class="text-right" style="width: 82px; border-bottom: 1px dashed #000;">Jumlah</th>
                        <th rowspan="2" class="text-right" style="width: 68px; border-bottom: 1px dashed #000;">Insentif</th>
                        <th rowspan="2" class="text-right" style="width: 68px; border-bottom: 1px dashed #000;">Komisi</th>
                        <th rowspan="2" class="text-right modern-col" style="width: 76px; border-bottom: 1px dashed #000;">Terbayar</th>
                        <th rowspan="2" class="text-right modern-col" style="width: 76px; border-bottom: 1px dashed #000;">Piutang</th>
                        <th rowspan="2" class="text-right modern-col" style="width: 45px; border-bottom: 1px dashed #000;">%Porsi</th>
                    </tr>
                    <tr class="thead-bottom">
                        <th class="text-right" style="width: 28px;">Brs</th>
                        <th class="text-right" style="width: 28px;">Klm</th>
                        <th class="text-right" style="width: 28px;">Dsp</th>
                        <th class="text-right" style="width: 42px;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @php $noRekap = 1; @endphp
                    @forelse($rekapPerAe as $r)
                        <tr class="data-row">
                            <td class="text-center">{{ $noRekap++ }}</td>
                            <td class="font-bold uppercase">{{ Str::limit($r['sektor_label'], 25) }}</td>
                            <td class="text-right">{{ number_format($r['order_brs'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['order_klm'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['order_dsp'], 0, ',', '.') }}</td>
                            <td class="text-right font-bold">{{ number_format($r['count'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['vol_baris'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['vol_kolom_mm'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['vol_displ_mm'], 0, ',', '.') }}</td>
                            <td class="text-right font-bold">{{ number_format($r['total_mili'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['total_bruto'], 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['total_reduksi'], 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['total_netto'], 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['total_ppn'], 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['total_materai'], 2, ',', '.') }}</td>
                            <td class="text-right font-bold">{{ number_format($r['total_tagihan'], 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['total_insentif'], 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($r['total_komisi'], 2, ',', '.') }}</td>
                            <td class="text-right modern-col">{{ number_format($r['total_bayar'], 2, ',', '.') }}</td>
                            <td class="text-right modern-col">{{ number_format($r['total_piutang'], 2, ',', '.') }}</td>
                            <td class="text-right modern-col">{{ number_format($r['kontribusi_persen'] ?? 0, 1, ',', '.') }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="21" class="text-center" style="padding: 24px 8px; color: #64748b;">
                                --- Tidak ada data penjualan iklan pada periode {{ $hariTanggal }} ---
                            </td>
                        </tr>
                    @endforelse

                    <!-- Baris Ringkasan Jumlah... Sesuai Print #3 -->
                    <tr class="summary-row">
                        <td colspan="2" style="padding-left: 4px;">
                            Jumlah...
                        </td>
                        <td class="text-right">{{ number_format($grandTotal['order_brs'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['order_klm'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['order_dsp'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['count'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['vol_baris'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['vol_kolom_mm'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['vol_displ_mm'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['total_mili'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['total_bruto'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['total_reduksi'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['total_netto'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['total_ppn'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['total_materai'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['total_tagihan'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['total_insentif'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grandTotal['total_komisi'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right modern-col">{{ number_format($grandTotal['total_bayar'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right modern-col">{{ number_format($grandTotal['total_piutang'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right modern-col">100,0%</td>
                    </tr>
                </tbody>
            </table>

            <!-- Meta Data & Rekapitulasi Standar Industri -->
            <div class="dami-meta-row">
                <div>
                    Tasikmalaya, {{ now()->format('d-m-Y H:i:s') }}
                </div>
                <div>
                    Total Order: {{ number_format($grandTotal['count'] ?? 0, 0, ',', '.') }} Iklan &nbsp;|&nbsp;
                    Kas Masuk (Lunas): Rp {{ number_format($grandTotal['total_bayar'] ?? 0, 0, ',', '.') }} ({{ $grandTotal['rasio_lunas'] ?? 0 }}%) &nbsp;|&nbsp;
                    Sisa Piutang: Rp {{ number_format($grandTotal['total_piutang'] ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <!-- Tanda Tangan Resmi Sesuai Print #3 -->
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

    <!-- ========================================================================= -->
    <!-- LEMBAR 2 (PRINT #1): LAPORAN PENJUALAN IKLAN PER SALES / AE               -->
    <!-- ========================================================================= -->
    <div class="paper-container {{ $initialMode === 'gabungan' ? 'page-break' : '' }}" id="sheetRincianPerolehan" style="{{ $initialMode === 'semua_ae' ? 'display: none;' : '' }}">
        <div class="paper-inner">

            <!-- Header Dokumen (Sesuai Print #1: PEROLEHAN : ASEP LIANA / NAMA AE) -->
            <div class="dami-header">
                <div class="dami-pt">PT. BERKAH PIKIRAN RAKYAT</div>
                <div class="dami-title-row">
                    <div class="dami-title">LAPORAN PENJUALAN IKLAN PENERBITAN HU. PRIANGAN</div>
                    <div class="dami-page">hal : 1</div>
                </div>
                <div class="dami-perolehan">
                    PEROLEHAN : {{ $selectedAe ? strtoupper($selectedAe->nama_ae) : 'SEMUA SALES / ACCOUNT EXECUTIVE' }}
                </div>
                <div class="dami-date">
                    HARI / TANGGAL : {{ $hariTanggal }}
                </div>
            </div>

            <!-- Tabel Rincian Perolehan Pemasang (13 Kolom Resmi Print #1 + Kolom Standar Industri Opsional) -->
            <table class="dami-table">
                <thead>
                    <tr class="thead-single">
                        <th class="text-center" style="width: 26px;">No.</th>
                        <th class="modern-col" style="width: 82px;">No.Faktur</th>
                        <th class="modern-col" style="width: 62px;">Tgl.Muat</th>
                        <th style="width: 195px;">Nama Pemasang</th>
                        <th style="width: 130px;">Iklan</th>
                        @if(!$selectedAe)
                            <th style="width: 105px;">Sales / AE</th>
                        @endif
                        <th class="text-right" style="width: 34px;">Brs</th>
                        <th class="text-right" style="width: 34px;">Klm</th>
                        <th class="text-right" style="width: 38px;">Mm</th>
                        <th class="text-right" style="width: 48px;">Jml MM</th>
                        <th class="text-right" style="width: 82px;">Harga Iklan</th>
                        <th class="text-right" style="width: 60px;">Diskon</th>
                        <th class="text-right modern-col" style="width: 72px;">Reduksi</th>
                        <th class="text-right" style="width: 82px;">Netto</th>
                        <th class="text-right" style="width: 72px;">Ppn</th>
                        <th class="text-right" style="width: 58px;">Materai</th>
                        <th class="text-right" style="width: 88px;">Jumlah</th>
                        <th class="text-right modern-col" style="width: 78px;">Terbayar</th>
                        <th class="text-right modern-col" style="width: 78px;">Piutang</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sumBrs = 0;
                        $sumJmlMm = 0;
                        $sumHarga = 0;
                        $sumReduksi = 0;
                        $sumNetto = 0;
                        $sumPpn = 0;
                        $sumMaterai = 0;
                        $sumJumlah = 0;
                        $sumBayar = 0;
                        $sumPiutang = 0;
                    @endphp
                    @forelse($rincianList as $idx => $item)
                        @php
                            $sumBrs += $item->brs;
                            $sumJmlMm += $item->jml_mm;
                            $sumHarga += $item->harga_iklan;
                            $sumReduksi += $item->reduksi;
                            $sumNetto += $item->netto;
                            $sumPpn += $item->ppn;
                            $sumMaterai += $item->materai;
                            $sumJumlah += $item->jumlah;
                            $sumBayar += $item->bayar;
                            $sumPiutang += $item->piutang;
                        @endphp
                        <tr class="data-row">
                            <td class="text-center">{{ $idx + 1 }}.</td>
                            <td class="modern-col font-bold">{{ $item->nofaktur }}</td>
                            <td class="modern-col">{{ \Carbon\Carbon::parse($item->tanggal_muat ?? $item->tanggal)->format('d-m-y') }}</td>
                            <td class="font-bold uppercase" title="{{ $item->nama_pemasang }}">
                                {{ Str::limit($item->nama_pemasang, 28) }}
                            </td>
                            <td class="uppercase">
                                {{ Str::limit($item->jenis_iklan, 18) }}
                            </td>
                            @if(!$selectedAe)
                                <td class="uppercase">{{ Str::limit($item->sales_nama, 15) }}</td>
                            @endif
                            <td class="text-right">{{ $item->brs }}</td>
                            <td class="text-right">{{ $item->klm }}</td>
                            <td class="text-right">{{ $item->mm }}</td>
                            <td class="text-right">{{ number_format($item->jml_mm, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->harga_iklan, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->diskon_persen, 2, ',', '.') }} %</td>
                            <td class="text-right modern-col">{{ number_format($item->reduksi, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->netto, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->ppn, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->materai, 2, ',', '.') }}</td>
                            <td class="text-right font-bold">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                            <td class="text-right modern-col">{{ number_format($item->bayar, 2, ',', '.') }}</td>
                            <td class="text-right modern-col">{{ number_format($item->piutang, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="19" class="text-center" style="padding: 24px 8px; color: #64748b;">
                                --- Tidak ada rincian pemasangan iklan untuk perolehan ini pada periode {{ $hariTanggal }} ---
                            </td>
                        </tr>
                    @endforelse

                    <!-- Baris Ringkasan Jumlah ...................... Sesuai Print #1 -->
                    <tr class="summary-row">
                        <td colspan="1"></td>
                        <td class="modern-col"></td>
                        <td class="modern-col"></td>
                        <td colspan="{{ !$selectedAe ? 3 : 2 }}" style="padding-left: 2px;">
                            Jumlah ......................
                        </td>
                        <td class="text-right">{{ number_format($sumBrs, 0, ',', '.') }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ number_format($sumJmlMm, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($sumHarga, 2, ',', '.') }}</td>
                        <td></td>
                        <td class="text-right modern-col">{{ number_format($sumReduksi, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($sumNetto, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($sumPpn, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($sumMaterai, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($sumJumlah, 2, ',', '.') }}</td>
                        <td class="text-right modern-col">{{ number_format($sumBayar, 2, ',', '.') }}</td>
                        <td class="text-right modern-col">{{ number_format($sumPiutang, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Meta Data & Tanggal Cetak -->
            <div class="dami-meta-row">
                <div>
                    Tasikmalaya, {{ now()->format('d-m-Y H:i:s') }}
                </div>
                <div>
                    Total Perolehan: {{ count($rincianList) }} Order &nbsp;|&nbsp;
                    Kas Masuk (Lunas): Rp {{ number_format($sumBayar, 0, ',', '.') }} &nbsp;|&nbsp;
                    Sisa Piutang: Rp {{ number_format($sumPiutang, 0, ',', '.') }}
                </div>
            </div>

            <!-- Tanda Tangan Resmi Sesuai Print #1 -->
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

    <script>
        function setSheetMode(mode) {
            const elRekap = document.getElementById('sheetRekapSektor');
            const elRincian = document.getElementById('sheetRincianPerolehan');
            const btnSemua = document.getElementById('btnModeSemuaAe');
            const btnPerSales = document.getElementById('btnModePerSales');
            const btnGabungan = document.getElementById('btnModeGabungan');

            btnSemua.classList.remove('active-mode');
            btnPerSales.classList.remove('active-mode');
            btnGabungan.classList.remove('active-mode');

            if (mode === 'semua_ae') {
                elRekap.style.display = 'block';
                elRincian.style.display = 'none';
                elRincian.classList.remove('page-break');
                btnSemua.classList.add('active-mode');
            } else if (mode === 'per_sales') {
                elRekap.style.display = 'none';
                elRincian.style.display = 'block';
                elRincian.classList.remove('page-break');
                btnPerSales.classList.add('active-mode');
            } else {
                elRekap.style.display = 'block';
                elRincian.style.display = 'block';
                elRincian.classList.add('page-break');
                btnGabungan.classList.add('active-mode');
            }
        }

        function toggleModernCols() {
            document.body.classList.toggle('show-modern');
            const btn = document.getElementById('btnToggleModern');
            if (document.body.classList.contains('show-modern')) {
                btn.textContent = '✓ Sembunyikan Kolom Standar Industri';
            } else {
                btn.textContent = '+ Kolom Standar Industri (Kas, Piutang, Faktur)';
            }
        }
    </script>
</body>
</html>
