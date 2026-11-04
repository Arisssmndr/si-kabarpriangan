<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemuatan Iklan HU. Priangan - {{ $hariTanggal }}</title>
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

        .btn-toggle {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #94a3b8;
        }
        .btn-toggle:hover { background: #e2e8f0; }

        /* Continuous Form Paper Simulation (14.875" x 11" Wide Continuous Sheet) */
        .paper-container {
            max-width: 1420px;
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

        .dami-date-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin: 2px 0 6px 0;
        }

        .dami-date {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
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
        .summary-row-top td {
            border-top: 1px dashed #000;
            padding: 5px 3px 2px 3px;
            font-weight: bold;
        }

        .summary-row-bottom td {
            border-bottom: 2px dashed #000;
            padding: 1px 3px 5px 3px;
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
        }
    </style>
</head>
<body>

    <!-- Bar Navigasi & Aksi Layar -->
    <div class="screen-actions">
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('laporan.index', request()->query()) }}" class="btn-action btn-back">
                &larr; Kembali ke Laporan Pemuatan
            </a>
            <button type="button" onclick="toggleModernCols()" id="btnToggleModern" class="btn-action btn-toggle">
                + Tampilkan Kolom Standar Industri (Tgl, AE, Kas &amp; Piutang)
            </button>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 11px; color: #475569;">
                Standar Form: <strong>Dot-Matrix 14.875" &times; 11" Continuous Sheet (Dokumen Resmi #4)</strong>
            </span>
            <button type="button" onclick="window.print()" class="btn-action btn-print">
                🖨️ Cetak Laporan Pemuatan HU. Priangan
            </button>
        </div>
    </div>

    <!-- Lembar Kertas Continuous Form -->
    <div class="paper-container">
        <div class="paper-inner">

            <!-- Header Dokumen (Sesuai Dokumen Fisik Print #4: LAPORAN PEMUATAN IKLAN HU. PRIANGAN) -->
            <div class="dami-header">
                <div class="dami-pt">PT. BERKAH PIKIRAN RAKYAT</div>
                <div class="dami-title-row">
                    <div class="dami-title">LAPORAN PEMUATAN IKLAN HU. PRIANGAN</div>
                    <div class="dami-page">hal : 1</div>
                </div>
                <div class="dami-date-row">
                    <div class="dami-date">
                        TANGGAL MUAT {{ $hariTanggal }}
                    </div>
                    <div style="font-size: 9pt; font-weight: bold;">
                        MEDIA: {{ $media == 'all' ? 'SEMUA MEDIA' : strtoupper($media) }} | STATUS: {{ strtoupper($status) }}
                    </div>
                </div>
            </div>

            <!-- Tabel Pemuatan Iklan (16 Kolom Resmi Print #4 + Kolom Standar Industri Opsional) -->
            <table class="dami-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 26px;">NO.</th>
                        <th style="width: 74px;">NO.ORDER</th>
                        <th class="modern-col" style="width: 65px;">TGL.MUAT</th>
                        <th style="width: 138px;">NO. PERKIRAAN</th>
                        <th style="width: 160px;">PEMASANG</th>
                        <th style="width: 165px;">ALAMAT</th>
                        <th style="width: 100px;">JENIS</th>
                        <th class="modern-col" style="width: 85px;">SALES/AE</th>
                        <th class="text-right" style="width: 35px;">BARIS</th>
                        <th class="text-right" style="width: 35px;">KOLOM</th>
                        <th class="text-right" style="width: 35px;">DISPL</th>
                        <th class="text-right" style="width: 40px;">MILI</th>
                        <th class="text-right" style="width: 74px;">Brutto</th>
                        <th class="text-right" style="width: 68px;">Reduksi</th>
                        <th class="text-right" style="width: 74px;">Netto</th>
                        <th class="text-right" style="width: 64px;">PPn</th>
                        <th class="text-right" style="width: 54px;">Materai</th>
                        <th class="text-right" style="width: 78px;">Jumlah</th>
                        <th class="text-right modern-col" style="width: 74px;">Terbayar</th>
                        <th class="text-right modern-col" style="width: 74px;">Piutang</th>
                        <th class="text-center modern-col" style="width: 52px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $row)
                        <tr class="data-row">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="font-bold">{{ $row->no_order }}</td>
                            <td class="modern-col">{{ \Carbon\Carbon::parse($row->tanggal_muat ?? $row->tanggal)->format('d-m-y') }}</td>
                            <td>{{ $row->no_perkiraan }}</td>
                            <td class="font-bold uppercase" title="{{ $row->customer }}">
                                {{ Str::limit(strtoupper($row->customer), 22) }}
                            </td>
                            <td class="uppercase" title="{{ $row->alamat }}">
                                {{ Str::limit(strtoupper($row->alamat), 23) }}
                            </td>
                            <td class="uppercase">
                                {{ Str::limit(strtoupper($row->paket), 15) }}
                            </td>
                            <td class="uppercase modern-col">
                                {{ Str::limit(strtoupper($row->sales), 12) }}
                            </td>
                            <td class="text-right">{{ $row->baris ?? 0 }}</td>
                            <td class="text-right">{{ $row->kolom ?? 0 }}</td>
                            <td class="text-right">{{ $row->displ ?? 0 }}</td>
                            <td class="text-right">{{ number_format($row->mili ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($row->brutto ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($row->reduksi ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($row->netto ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($row->ppn ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($row->materai ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right font-bold">{{ number_format($row->total ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right modern-col">{{ number_format($row->bayar ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right modern-col">{{ number_format($row->piutang ?? 0, 2, ',', '.') }}</td>
                            <td class="text-center modern-col">{{ $row->status_label ?? ($row->is_lunas ? 'LUNAS' : 'PIUTANG') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="21" class="text-center" style="padding: 24px 8px; color: #64748b;">
                                --- Tidak ada data pemuatan/penjualan iklan pada periode {{ $hariTanggal }} ---
                            </td>
                        </tr>
                    @endforelse

                    <!-- Baris Ringkasan Jumlah & Jumlah Order Sesuai Dokumen Fisik Print #4 -->
                    <tr class="summary-row-top">
                        <td colspan="2" style="padding-left: 4px;">
                            Jumlah...
                        </td>
                        <td class="modern-col"></td>
                        <td colspan="3"></td>
                        <td class="modern-col"></td>
                        <td class="text-right">{{ number_format($totalBaris ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalKolom ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalDispl ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalMili ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalBrutto ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalReduksi ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalNetto ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalPpn ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalMaterai ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalOmset ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right modern-col">{{ number_format($totalKasMasuk ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right modern-col">{{ number_format($totalPiutang ?? 0, 2, ',', '.') }}</td>
                        <td class="modern-col"></td>
                    </tr>
                    <tr class="summary-row-bottom">
                        <td colspan="2" style="padding-left: 4px;">
                            Jumlah Order...
                        </td>
                        <td class="modern-col"></td>
                        <td colspan="3"></td>
                        <td class="modern-col"></td>
                        <td class="text-right">{{ number_format($orderBaris ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($orderKolom ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($orderDispl ?? 0, 0, ',', '.') }}</td>
                        <td colspan="7"></td>
                        <td class="modern-col" colspan="3"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Meta Data Order & Rekapitulasi Standar Industri -->
            <div class="dami-meta-row">
                <div>
                    Tasikmalaya, {{ now()->format('d-m-Y H:i:s') }}
                </div>
                <div>
                    Total Order: {{ $totalTransaksi }} (Berbayar: {{ $orderKomersial ?? 0 }} | Bonus: {{ $orderBonus ?? 0 }}) &nbsp;|&nbsp;
                    Kas Masuk (Lunas): Rp {{ number_format($totalKasMasuk ?? 0, 0, ',', '.') }} ({{ $rasioLunas ?? 0 }}%) &nbsp;|&nbsp;
                    Sisa Piutang: Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <!-- Tanda Tangan Resmi Sesuai Dokumen Fisik Kabar Priangan -->
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
        function toggleModernCols() {
            document.body.classList.toggle('show-modern');
            const btn = document.getElementById('btnToggleModern');
            if (document.body.classList.contains('show-modern')) {
                btn.textContent = '✓ Sembunyikan Kolom Standar Industri (Kembali ke 16 Kolom Klasik IKL.EXE)';
            } else {
                btn.textContent = '+ Tampilkan Kolom Standar Industri (Tgl, AE, Kas & Piutang)';
            }
        }
    </script>
</body>
</html>
