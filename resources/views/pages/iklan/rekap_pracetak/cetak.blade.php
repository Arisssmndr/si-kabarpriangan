<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dami Pemuatan Iklan HU. Priangan - Edisi {{ $tanggalDmy }}</title>
    <style>
        @page {
            size: auto;
            margin: 6mm 10mm;
        }

        body {
            font-family: 'Courier New', Courier, monospace, 'Lucida Console';
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px 10px;
        }

        .screen-actions {
            max-width: 960px;
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

        .btn-toggle {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #94a3b8;
        }
        .btn-toggle:hover { background: #e2e8f0; }

        /* Continuous Form Paper Simulation */
        .paper-container {
            max-width: 960px;
            margin: 0 auto;
            background: #fff;
            padding: 20px 30px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            position: relative;
            box-sizing: border-box;
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
            margin: 0 16px;
        }

        /* Header Dokumen Dami HU. Priangan */
        .dami-header {
            margin-bottom: 8px;
        }

        .dami-pt {
            font-size: 12pt;
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
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .dami-page {
            font-size: 11pt;
            font-weight: bold;
        }

        .dami-date {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0 6px 0;
        }

        .divider-double {
            border-top: 2px dashed #000;
            margin: 4px 0 8px 0;
        }

        .divider-single {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .divider-solid {
            border-top: 1px solid #000;
            margin: 4px 0;
        }

        /* Tabel Utama Dami */
        table.dami-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            line-height: 1.25;
        }

        table.dami-table th {
            text-align: left;
            font-weight: bold;
            padding: 4px 4px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            white-space: nowrap;
        }

        table.dami-table td {
            padding: 3px 4px;
            vertical-align: top;
        }

        table.dami-table tr.data-row:hover {
            background-color: #f8fafc;
        }

        .col-no    { width: 32px; text-align: center; }
        .col-oti   { width: 130px; }
        .col-nama  { width: 230px; }
        .col-jenis { width: 95px; }
        .col-num   { width: 45px; text-align: right; }
        .col-rubrik{ width: 125px; }
        .col-kota  { width: 120px; }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .font-bold   { font-weight: bold; }
        .uppercase   { text-transform: uppercase; }

        /* Detail Baris & Catatan Setter */
        .detail-row td {
            padding: 1px 4px 6px 36px !important;
            font-size: 9.5pt;
            color: #1e293b;
        }

        .box-naskah {
            background: #f8fafc;
            border: 1px dotted #94a3b8;
            padding: 3px 6px;
            margin-top: 2px;
            font-style: italic;
        }

        .badge-cbp {
            display: inline-block;
            font-weight: bold;
            color: #dc2626;
            margin-left: 4px;
        }

        /* Ringkasan Footer */
        .summary-row td {
            border-top: 1px dashed #000;
            border-bottom: 2px dashed #000;
            padding: 6px 4px;
            font-weight: bold;
        }

        .dami-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: 10px;
            font-size: 11pt;
            font-weight: bold;
        }

        /* Kolom Tanda Tangan */
        .signature-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            text-align: center;
            font-size: 10.5pt;
        }

        .sig-box {
            width: 250px;
        }

        .sig-space {
            height: 60px;
        }

        .sig-name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 4px;
            display: inline-block;
            min-width: 180px;
        }

        .sig-title {
            font-size: 9pt;
            margin-top: 2px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
                font-size: 10pt;
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
            }

            .paper-container::before,
            .paper-container::after {
                display: none !important;
            }

            .paper-inner {
                margin: 0;
            }

            table.dami-table tr.data-row:hover {
                background-color: transparent;
            }

            .box-naskah {
                background: transparent;
                border: 1px dotted #000;
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
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('iklan.rekap-pracetak.index', ['tanggal_muat' => $tanggalMuat]) }}" class="btn-action btn-back">
                &larr; Kembali ke Rekap
            </a>
            <button type="button" onclick="toggleDetails()" id="btnToggleDetail" class="btn-action btn-toggle">
                📝 Sembunyikan Teks Baris
            </button>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 11px; color: #475569;">
                Standar Form: <strong>Dot-Matrix 9.5" Continuous Sheet</strong>
            </span>
            <button type="button" onclick="window.print()" class="btn-action btn-print">
                🖨️ Cetak Dami HU. Priangan
            </button>
        </div>
    </div>

    <!-- Lembar Kertas Continuous Form -->
    <div class="paper-container">
        <div class="paper-inner">

            <!-- Header Dokumen Dami (Sesuai Foto 1 Dokumen Fisik Kabar Priangan) -->
            <div class="dami-header">
                <div class="dami-pt">PT. BERKAH PIKIRAN RAKYAT</div>
                <div class="dami-title-row">
                    <div class="dami-title">DAMI PEMUATAN IKLAN HU. PRIANGAN</div>
                    <div class="dami-page">hal : 1</div>
                </div>
                <div class="dami-date">
                    HARI / TANGGAL : {{ $hariTanggal }}
                </div>
            </div>

            <!-- Tabel Transaksi Pemuatan Iklan (Sesuai Kolom Foto 1) -->
            <table class="dami-table">
                <thead>
                    <tr>
                        <th class="col-no">NO.</th>
                        <th class="col-oti">OTI</th>
                        <th class="col-nama">NAMA PEMASANG</th>
                        <th class="col-jenis">JENIS IKLAN</th>
                        <th class="col-num">BRS</th>
                        <th class="col-num">KLM</th>
                        <th class="col-num">DSP</th>
                        <th class="col-num">MM</th>
                        <th class="col-rubrik">KOP IKLAN</th>
                        <th class="col-kota">KOP KOTA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flatItems as $idx => $item)
                        @php
                            $isBaris = ($item['kategori_form'] === 'BARIS');
                        @endphp
                        <tr class="data-row">
                            <td class="col-no">{{ $idx + 1 }}</td>
                            <td class="col-oti font-bold">
                                {{ $item['oti'] }}
                                @if($item['is_cbp_blocked'])
                                    <span class="badge-cbp">[CBP]</span>
                                @endif
                            </td>
                            <td class="col-nama font-bold uppercase truncate" title="{{ $item['pengiklan'] }}">
                                {{ Str::limit($item['pengiklan'], 28) }}
                            </td>
                            <td class="col-jenis uppercase">
                                {{ Str::limit($item['kategori_form'] ?: $item['produk'], 12) }}
                            </td>
                            <td class="col-num font-bold">
                                {{ $isBaris ? $item['jumlah_baris'] : '-' }}
                            </td>
                            <td class="col-num">
                                {{ !$isBaris ? $item['lebar_kolom'] : '-' }}
                            </td>
                            <td class="col-num">
                                {{ !$isBaris ? '1' : '-' }}
                            </td>
                            <td class="col-num">
                                {{ !$isBaris ? $item['tinggi_mm'] : '-' }}
                            </td>
                            <td class="col-rubrik uppercase">
                                {{ Str::limit($item['rubrik_kop'] ?? '-', 14) }}
                            </td>
                            <td class="col-kota uppercase font-bold">
                                {{ Str::limit($item['kop_kota'] ?? '-', 14) }}
                            </td>
                        </tr>

                        <!-- Sub-baris Detail Teks Baris / Materi Visual untuk Setter Layout -->
                        <tr class="detail-row setter-detail">
                            <td colspan="10">
                                @if($isBaris)
                                    <div class="box-naskah">
                                        &raquo; Teks: <strong>{{ $item['naskah_baris'] }}</strong>
                                        <span style="margin-left: 8px; color: #64748b;">(AE: {{ $item['sales'] }})</span>
                                    </div>
                                @else
                                    <div style="font-size: 9pt; color: #475569; padding-left: 4px;">
                                        &bull; Visual: <strong>{{ $item['warna'] }}</strong>
                                        • File: {{ $item['file_materi'] ? basename($item['file_materi']) : 'Setter Redaksi' }} ({{ $item['status_materi'] }})
                                        @if($item['keterangan'])
                                            • Ket: {{ $item['keterangan'] }}
                                        @endif
                                        • AE: {{ $item['sales'] }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center" style="padding: 24px 8px; color: #64748b;">
                                --- Tidak ada pemuatan iklan terdaftar untuk edisi {{ $hariTanggal }} ---
                            </td>
                        </tr>
                    @endforelse

                    <!-- Baris Ringkasan Jumlah Sesuai Foto 1 -->
                    <tr class="summary-row">
                        <td colspan="4" style="text-align: left; padding-left: 8px;">
                            Jumlah...
                        </td>
                        <td class="col-num">
                            {{ $totalBaris > 0 ? number_format($totalBaris, 0, ',', '.') : '-' }}
                        </td>
                        <td class="col-num">
                            {{ $totalKlm > 0 ? number_format($totalKlm, 0, ',', '.') : '-' }}
                        </td>
                        <td class="col-num">
                            {{ $totalDsp > 0 ? number_format($totalDsp, 0, ',', '.') : '-' }}
                        </td>
                        <td class="col-num">
                            {{ $totalMm > 0 ? number_format($totalMm, 0, ',', '.') : '-' }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Meta Data Order & Tanggal Cetak -->
            <div class="dami-meta-row">
                <div>
                    Jumlah Order... : {{ $totalIklan }} Order
                </div>
                <div>
                    Tasikmalaya, {{ now()->format('d-m-Y H:i:s') }}
                </div>
            </div>

            <!-- Tanda Tangan Penyerahan Fisik Lembar Dami ke Bagian Setter / Tata Letak -->
            <div class="signature-grid">
                <div class="sig-box">
                    <div>Front Office / Adm Iklan,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">( {{ Auth::user()->name ?? 'Petugas Front Office' }} )</div>
                    <div class="sig-title">Divisi Pemasaran & Iklan</div>
                </div>

                <div class="sig-box">
                    <div>Diterima Setter Layout,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">( ............................................ )</div>
                    <div class="sig-title">Bagian Tata Letak Halaman</div>
                </div>

                <div class="sig-box">
                    <div>Mengetahui Produksi Cetak,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">( ............................................ )</div>
                    <div class="sig-title">Percetakan HU Kabar Priangan</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleDetails() {
            const details = document.querySelectorAll('.setter-detail');
            const btn = document.getElementById('btnToggleDetail');
            let isHidden = false;

            details.forEach(el => {
                if (el.style.display === 'none') {
                    el.style.display = '';
                    isHidden = false;
                } else {
                    el.style.display = 'none';
                    isHidden = true;
                }
            });

            if (isHidden) {
                btn.textContent = '📝 Tampilkan Teks Baris';
            } else {
                btn.textContent = '📝 Sembunyikan Teks Baris';
            }
        }
    </script>
</body>
</html>
