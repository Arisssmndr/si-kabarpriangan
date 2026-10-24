<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Publikasi & Bukti Tayang - {{ $transaksi->nofakturonline }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm 10mm 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #111;
            background: #f1f5f9;
            margin: 0;
            padding: 20px 10px;
        }

        .sheet-container {
            max-width: 780px;
            margin: 0 auto;
        }

        .order-sheet {
            background: #fff;
            border: 2px solid #000;
            padding: 24px 28px;
            box-sizing: border-box;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Kop / Header */
        .header-title {
            text-align: center;
            font-size: 12.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-title .sub-title {
            font-size: 13.5pt;
            margin-top: 2px;
            display: block;
        }

        /* Section Rows */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .info-table td {
            padding: 3.5px 0;
            vertical-align: top;
        }

        .info-table .label-col {
            width: 175px;
            font-weight: bold;
            font-size: 10pt;
        }

        .info-table .colon-col {
            width: 16px;
            text-align: center;
            font-weight: bold;
        }

        .info-table .value-col {
            font-size: 10pt;
            border-bottom: 1px dotted #888;
        }

        /* Pernyataan & Checkboxes */
        .statement-text {
            font-size: 10pt;
            margin: 12px 0 8px 0;
            line-height: 1.4;
        }

        .section-label {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #333;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px 14px;
            margin: 6px 0 12px 0;
            padding: 8px 12px;
            border: 1px dashed #999;
            background: #fafafa;
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 9.5pt;
        }

        .check-box {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 1.5px solid #000;
            text-align: center;
            line-height: 12px;
            font-size: 10pt;
            font-weight: bold;
            vertical-align: middle;
            flex-shrink: 0;
        }

        .check-box.checked {
            background-color: #000;
            color: #fff;
        }

        /* Pembayaran Box */
        .payment-section {
            margin: 10px 0;
        }

        .payment-row {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            font-size: 10pt;
        }

        .payment-label {
            width: 150px;
        }

        .payment-amount {
            flex: 1;
            border-bottom: 1px dotted #888;
            padding-left: 8px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }

        /* Bentuk Publikasi */
        .bentuk-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 10px 0;
            font-size: 10pt;
        }

        /* Tanda Tangan */
        .signature-section {
            margin-top: 28px;
            display: flex;
            justify-content: space-between;
            text-align: center;
            page-break-inside: avoid;
        }

        .sig-col {
            width: 30%;
        }

        .sig-title {
            font-size: 9.5pt;
            margin-bottom: 55px;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 9.5pt;
        }

        .sig-sub {
            font-size: 8.5pt;
            color: #555;
            margin-top: 3px;
        }

        /* Digital Tearsheet Page */
        .tearsheet-page {
            page-break-before: always;
        }

        .tearsheet-header {
            text-align: center;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .tearsheet-header .instansi-title {
            font-size: 14pt;
            font-weight: 800;
            color: #0369a1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tearsheet-header .report-subtitle {
            font-size: 11pt;
            font-weight: bold;
            color: #334155;
            margin-top: 3px;
        }

        .tearsheet-header .report-desc {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 2px;
        }

        .tearsheet-meta-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 15px;
        }

        .url-box {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 8px;
            font-size: 9pt;
            word-break: break-all;
        }

        .screenshot-frame {
            border: 1.5px solid #000;
            padding: 6px;
            background: #fff;
            text-align: center;
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .screenshot-img {
            max-width: 100%;
            max-height: 480px;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* Tombol Cetak Browser */
        .no-print-bar {
            text-align: center;
            margin-bottom: 20px;
            position: sticky;
            top: 10px;
            z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 12px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-print {
            background: #0284c7;
            color: #fff;
            padding: 8px 18px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .btn-print:hover {
            background: #0369a1;
        }

        .btn-secondary {
            background: #f8fafc;
            color: #334155;
            padding: 8px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 12.5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .btn-back {
            background: #fff;
            color: #475569;
            padding: 7px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
            font-weight: 600;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .order-sheet {
                border: 2px solid #000 !important;
                padding: 12px 18px !important;
                margin-bottom: 0 !important;
                box-shadow: none !important;
            }
            .tearsheet-page {
                page-break-before: always !important;
            }
            .print-only-order .tearsheet-page {
                display: none !important;
            }
            .print-only-tearsheet .order-sheet:not(.tearsheet-page) {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('transaksionline.index') }}" class="btn-back">&larr; Kembali ke Index</a>
        <button onclick="window.print()" class="btn-print">&#128438; Cetak Dokumen Lengkap</button>
        <button onclick="printOnlySection('order')" class="btn-secondary">Hanya Lembar Order</button>
        @if($transaksi->url_berita_tayang || $transaksi->url_medsos_tayang || $transaksi->file_bukti_tayang)
            <button onclick="printOnlySection('tearsheet')" class="btn-secondary" style="border-color: #0284c7; color: #0284c7;">Hanya Bukti Tayang (Tearsheet SPJ)</button>
        @endif
    </div>

    @php
        $platforms = $transaksi->platforms_publikasi;
        if (!is_array($platforms)) {
            $platforms = array_filter(array_map('trim', explode(',', $transaksi->portal_iklanonline ?: '')));
        }
        $kategoris = $transaksi->kategori_produk_publikasi ?? [];
        if (!is_array($kategoris)) {
            $kategoris = [$kategoris];
        }
        $skema = $transaksi->skema_pembayaran ?: 'sekali_tayang';
        $bentuk = $transaksi->bentuk_publikasi ?: 'display';
        $hasTearsheet = $transaksi->url_berita_tayang || $transaksi->url_medsos_tayang || $transaksi->file_bukti_tayang || in_array($transaksi->status_produksi, ['tayang', 'selesai']);
    @endphp

    <div class="sheet-container" id="printableArea">
        <!-- ========================================== -->
        <!-- HALAMAN 1: FORMULIR ORDER PEMASANGAN PUBLIKASI -->
        <!-- ========================================== -->
        <div class="order-sheet" id="orderSection">
            <!-- Header / Judul Form Resmi Sesuai Bukti Fisik -->
            <div class="header-title">
                ORDER PEMASANGAN PUBLIKASI di
                <span class="sub-title">KABAR PRIANGAN GRUP</span>
            </div>

            <!-- No Faktur & Tanggal Cetak -->
            <div style="display: flex; justify-content: space-between; font-size: 9.5pt; margin-bottom: 12px; color: #333;">
                <span>No. Order: <strong>{{ $transaksi->nofakturonline }}</strong></span>
                <span>Tanggal Order: <strong>{{ $transaksi->tanggal_transaksionline?->translatedFormat('d F Y') ?? date('d F Y') }}</strong></span>
            </div>

            <!-- Data Pemesan / Instansi -->
            <table class="info-table">
                <tr>
                    <td class="label-col">NAMA INSTANSI</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><strong>{{ $transaksi->nama_pemasangonline }}</strong></td>
                </tr>
                <tr>
                    <td class="label-col">NO. SPK / PO DINAS</td>
                    <td class="colon-col">:</td>
                    <td class="value-col"><strong>{{ $transaksi->no_spk ?: '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="label-col">PRODUK / JASA</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $transaksi->produk_jasa ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">NAMA / JABATAN PIC</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">
                        {{ $transaksi->nama_pic ?: '-' }} 
                        @if($transaksi->jabatan_pic) ({{ $transaksi->jabatan_pic }}) @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-col">ALAMAT PEMASANG</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $transaksi->alamat_pemasangonline ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">NO. TLP / HP</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $transaksi->no_kontak_pemasang ?: ($transaksi->pelanggan?->no_telp ?: '-') }}</td>
                </tr>
            </table>

            <!-- Pernyataan Kesediaan Publikasi -->
            <div class="statement-text">
                Dengan ini menyatakan bahwa saya bersedia memasang publikasi di media online Kabar Priangan Grup:
            </div>

            <!-- 1. Saluran Media Publikasi (Distribution Channels) -->
            <div class="section-label">A. Saluran Media Publikasi:</div>
            <div class="checkbox-grid">
                <div class="check-item">
                    <span class="check-box {{ in_array('Portal Berita', $platforms) ? 'checked' : '' }}">
                        {!! in_array('Portal Berita', $platforms) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>Portal Berita</span>
                </div>
                <div class="check-item">
                    <span class="check-box {{ in_array('Podcast', $platforms) ? 'checked' : '' }}">
                        {!! in_array('Podcast', $platforms) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>Podcast</span>
                </div>
                <div class="check-item">
                    <span class="check-box {{ in_array('TikTok', $platforms) ? 'checked' : '' }}">
                        {!! in_array('TikTok', $platforms) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>TikTok</span>
                </div>
                <div class="check-item">
                    <span class="check-box {{ in_array('Instagram', $platforms) ? 'checked' : '' }}">
                        {!! in_array('Instagram', $platforms) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>Instagram</span>
                </div>
                <div class="check-item">
                    <span class="check-box {{ in_array('YouTube', $platforms) ? 'checked' : '' }}">
                        {!! in_array('YouTube', $platforms) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>YouTube</span>
                </div>
                <div class="check-item">
                    <span class="check-box {{ in_array('Priangan TV', $platforms) ? 'checked' : '' }}">
                        {!! in_array('Priangan TV', $platforms) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>Priangan TV</span>
                </div>
            </div>

            <!-- 2. Kategori Format Produk Publikasi (Standar Industri Pers) -->
            <div class="section-label">B. Kategori Format Produk Publikasi:</div>
            <div class="checkbox-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 10px;">
                <div class="check-item">
                    <span class="check-box {{ in_array('Advertorial Berita', $kategoris) ? 'checked' : '' }}">
                        {!! in_array('Advertorial Berita', $kategoris) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>Advertorial / Native Article (Liputan Redaksi)</span>
                </div>
                <div class="check-item">
                    <span class="check-box {{ in_array('Display Banner Ads', $kategoris) ? 'checked' : '' }}">
                        {!! in_array('Display Banner Ads', $kategoris) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>Display Banner Ads (Website Placement)</span>
                </div>
                <div class="check-item">
                    <span class="check-box {{ in_array('Social Media Feed/Reels', $kategoris) ? 'checked' : '' }}">
                        {!! in_array('Social Media Feed/Reels', $kategoris) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>Social Media Feed / Reels / Endorsement</span>
                </div>
                <div class="check-item">
                    <span class="check-box {{ in_array('Talkshow/Podcast Taping', $kategoris) ? 'checked' : '' }}">
                        {!! in_array('Talkshow/Podcast Taping', $kategoris) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>Talkshow / Podcast Taping (Video Studio)</span>
                </div>
                <div class="check-item" style="grid-column: span 2;">
                    <span class="check-box {{ in_array('Ucapan Selamat/Duka Cita', $kategoris) ? 'checked' : '' }}">
                        {!! in_array('Ucapan Selamat/Duka Cita', $kategoris) ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    <span>Ucapan Selamat / Duka Cita (Digital Greeting Card)</span>
                </div>
            </div>

            <!-- Skema Pembayaran -->
            <div class="payment-section">
                <div style="font-weight: bold; font-size: 10pt; margin-bottom: 5px;">C. Skema Pembayaran:</div>
                
                <div class="payment-row">
                    <div class="payment-label">
                        <span class="check-box {{ $skema === 'sekali_tayang' ? 'checked' : '' }}">
                            {!! $skema === 'sekali_tayang' ? '&#10003;' : '&nbsp;' !!}
                        </span>
                        &nbsp; 1 kali tayang
                    </div>
                    <div class="payment-amount">
                        Rp {{ $skema === 'sekali_tayang' ? number_format($transaksi->harga_transaksionline, 0, ',', '.') : '-' }}
                        @if($transaksi->total_muatiklanonline > 1 && $skema === 'sekali_tayang')
                            (x {{ $transaksi->total_muatiklanonline }} tayang)
                        @endif
                    </div>
                </div>

                <div class="payment-row">
                    <div class="payment-label">
                        <span class="check-box {{ $skema === 'paket' ? 'checked' : '' }}">
                            {!! $skema === 'paket' ? '&#10003;' : '&nbsp;' !!}
                        </span>
                        &nbsp; Paket Publikasi
                    </div>
                    <div class="payment-amount">
                        Rp {{ $skema === 'paket' ? number_format($transaksi->harga_transaksionline, 0, ',', '.') : '-' }}
                    </div>
                </div>
            </div>

            <!-- Tanggal Muat / Upload & Materi -->
            <table class="info-table" style="margin-top: 10px;">
                <tr>
                    <td class="label-col">Jadwal Muat / Tayang</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">
                        <strong>{{ $transaksi->tanggal_muatiklanonline?->translatedFormat('d F Y') ?? '-' }}</strong>
                        @if($transaksi->tanggal_selesai_tayang)
                            s/d <strong>{{ $transaksi->tanggal_selesai_tayang->translatedFormat('d F Y') }}</strong>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-col" style="padding-top: 6px;">Judul / Materi Iklan</td>
                    <td class="colon-col" style="padding-top: 6px;">:</td>
                    <td class="value-col" style="padding-top: 6px;">
                        <strong>{{ $transaksi->materi_iklan ?: '-' }}</strong>
                        @if($transaksi->url_materi_iklan)
                            <br><span style="font-size: 8.5pt; color: #555;">Bahan Mentah / Cloud: {{ $transaksi->url_materi_iklan }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-col" style="padding-top: 6px;">Status Alur Produksi</td>
                    <td class="colon-col" style="padding-top: 6px;">:</td>
                    <td class="value-col" style="padding-top: 6px;">
                        <strong>{{ $transaksi->status_produksi_label }}</strong>
                        @if($transaksi->catatan_redaksi)
                            <span style="font-size: 8.5pt; color: #64748b;">— Catatan: {{ $transaksi->catatan_redaksi }}</span>
                        @endif
                    </td>
                </tr>
            </table>

            <!-- Bentuk Publikasi -->
            <div class="bentuk-row">
                <div style="font-weight: bold; width: 175px;">Bentuk Publikasi:</div>
                
                <div class="check-item">
                    <span>Video</span>
                    <span class="check-box {{ $bentuk === 'video' ? 'checked' : '' }}">
                        {!! $bentuk === 'video' ? '&#10003;' : '&nbsp;' !!}
                    </span>
                </div>

                <div class="check-item">
                    <span>Display</span>
                    <span class="check-box {{ $bentuk === 'display' ? 'checked' : '' }}">
                        {!! $bentuk === 'display' ? '&#10003;' : '&nbsp;' !!}
                    </span>
                </div>

                <div class="check-item">
                    <span>Lainnya</span>
                    <span class="check-box {{ $bentuk === 'lainnya' ? 'checked' : '' }}">
                        {!! $bentuk === 'lainnya' ? '&#10003;' : '&nbsp;' !!}
                    </span>
                    @if($bentuk === 'lainnya' && $transaksi->bentuk_publikasi_lainnya)
                        <span style="font-size: 9pt;">({{ $transaksi->bentuk_publikasi_lainnya }})</span>
                    @endif
                </div>
            </div>

            <!-- Rekapitulasi Tagihan Resmi -->
            <div style="margin: 14px 0 8px 0; padding: 8px 12px; border: 1px solid #ccc; background: #fafafa; font-size: 9.5pt;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%;">Total Tagihan (+ PPN {{ $transaksi->ppn_persen ?? 12 }}%): <strong>Rp {{ number_format($transaksi->totaltagihan_transaksionline, 0, ',', '.') }}</strong></td>
                        <td style="width: 50%; text-align: right;">Jumlah Dibayar: <strong>Rp {{ number_format($transaksi->jumlahbayar_transaksionline, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top: 4px; border-top: 1px dashed #ddd; text-align: right;">
                            Sisa Piutang: <strong style="color: {{ $transaksi->piutang_transaksionline > 0 ? '#b91c1c' : '#15803d' }};">
                                Rp {{ number_format($transaksi->piutang_transaksionline, 0, ',', '.') }}
                                ({{ $transaksi->piutang_transaksionline > 0 ? 'BELUM LUNAS / TEMPO' : 'LUNAS' }})
                            </strong>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- 3 Kolom Tanda Tangan Sesuai Lembar Asli -->
            <div class="signature-section">
                <div class="sig-col">
                    <div class="sig-title">
                        Hormat saya (Pemesan),
                    </div>
                    <div class="sig-name">
                        ( {{ $transaksi->nama_pic ?: $transaksi->nama_pemasangonline }} )
                    </div>
                    <div class="sig-sub">Nama, Tandatangan, Cap</div>
                </div>

                <div class="sig-col">
                    <div class="sig-title">
                        Sektor Penjualan,
                    </div>
                    <div class="sig-name">
                        ( {{ $transaksi->sales_iklanonline }} )
                    </div>
                    <div class="sig-sub">Nama Pengorder / Sales AE</div>
                </div>

                <div class="sig-col">
                    <div class="sig-title">
                        Menyetujui,
                    </div>
                    <div class="sig-name">
                        ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                    </div>
                    <div class="sig-sub">Supervisor / Manajer Iklan</div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- HALAMAN 2: LAPORAN BUKTI TAYANG (DIGITAL TEARSHEET) -->
        <!-- Standar Industri Pers untuk Kelengkapan SPJ -->
        <!-- ========================================== -->
        @if($hasTearsheet)
        <div class="order-sheet tearsheet-page" id="tearsheetSection">
            <div class="tearsheet-header">
                <div class="instansi-title">KABAR PRIANGAN MULTIMEDIA NETWORK</div>
                <div class="report-subtitle">LAPORAN BUKTI TAYANG (DIGITAL TEARSHEET)</div>
                <div class="report-desc">Lampiran Bukti Tayang & Realisasi Publikasi Digital Untuk Berkas Penagihan SPJ</div>
            </div>

            <div class="tearsheet-meta-box">
                <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
                    <tr>
                        <td style="width: 20%; font-weight: bold; padding: 2px 0;">No. Faktur Order</td>
                        <td style="width: 2%;">:</td>
                        <td style="width: 28%; padding: 2px 0;">{{ $transaksi->nofakturonline }}</td>
                        <td style="width: 20%; font-weight: bold; padding: 2px 0;">Status Publikasi</td>
                        <td style="width: 2%;">:</td>
                        <td style="width: 28%; font-weight: bold; color: #0284c7; padding: 2px 0;">{{ strtoupper($transaksi->status_produksi_label) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; padding: 2px 0;">No. SPK / PO Dinas</td>
                        <td>:</td>
                        <td style="padding: 2px 0;"><strong>{{ $transaksi->no_spk ?: '-' }}</strong></td>
                        <td style="font-weight: bold; padding: 2px 0;">Tgl Realisasi Tayang</td>
                        <td>:</td>
                        <td style="padding: 2px 0;"><strong>{{ $transaksi->tanggal_realisasi_tayang?->translatedFormat('d F Y') ?? ($transaksi->tanggal_muatiklanonline?->translatedFormat('d F Y') ?? '-') }}</strong></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; padding: 2px 0;">Instansi Pemesan</td>
                        <td>:</td>
                        <td style="padding: 2px 0;"><strong>{{ $transaksi->nama_pemasangonline }}</strong></td>
                        <td style="font-weight: bold; padding: 2px 0;">Saluran Media</td>
                        <td>:</td>
                        <td style="padding: 2px 0;">{{ implode(', ', $platforms) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; padding: 2px 0;">Judul Materi</td>
                        <td>:</td>
                        <td colspan="4" style="padding: 2px 0;"><strong>{{ $transaksi->materi_iklan ?: '-' }}</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Tautan URL Berita & Medsos Live -->
            <div style="margin-bottom: 12px;">
                <div style="font-weight: bold; font-size: 10pt; margin-bottom: 6px; color: #1e293b;">Tautan Link Live Publikasi:</div>
                
                @if($transaksi->url_berita_tayang)
                    <div class="url-box">
                        <strong>&#127760; Link Artikel Portal Berita:</strong><br>
                        <a href="{{ $transaksi->url_berita_tayang }}" target="_blank" style="color: #0284c7; text-decoration: underline;">{{ $transaksi->url_berita_tayang }}</a>
                    </div>
                @else
                    <div class="url-box" style="color: #64748b;">
                        <em>Link artikel portal berita belum diinput.</em>
                    </div>
                @endif

                @if($transaksi->url_medsos_tayang)
                    <div class="url-box">
                        <strong>&#128241; Link Postingan Medsos (Instagram/YouTube/TikTok):</strong><br>
                        <a href="{{ $transaksi->url_medsos_tayang }}" target="_blank" style="color: #0284c7; text-decoration: underline;">{{ $transaksi->url_medsos_tayang }}</a>
                    </div>
                @endif

                @if($transaksi->catatan_redaksi)
                    <div style="font-size: 9pt; background: #fffbeb; border: 1px solid #fef3c7; padding: 6px 10px; border-radius: 4px; color: #92400e; margin-top: 6px;">
                        <strong>Catatan Redaksi:</strong> {{ $transaksi->catatan_redaksi }}
                    </div>
                @endif
            </div>

            <!-- Tangkapan Layar / Screenshot Bukti Tayang Digital Tearsheet -->
            <div style="font-weight: bold; font-size: 10pt; margin-top: 10px; margin-bottom: 4px; color: #1e293b;">
                Tangkapan Layar Bukti Siar / Digital Tearsheet:
            </div>
            <div class="screenshot-frame">
                @if($transaksi->file_bukti_tayang)
                    <img src="{{ asset('storage/' . $transaksi->file_bukti_tayang) }}" alt="Bukti Tayang Digital" class="screenshot-img">
                    <div style="font-size: 8pt; color: #64748b; margin-top: 4px;">
                        Dokumentasi Kliping Digital & Tearsheet Penayangan Resmi — Kabar Priangan
                    </div>
                @else
                    <div style="padding: 40px 20px; color: #94a3b8; font-style: italic;">
                        Screenshot bukti tayang belum diunggah. Lampiran ini dapat dilengkapi melalui menu "Input Bukti Tayang" pada daftar transaksi.
                    </div>
                @endif
            </div>

            <!-- Tanda Tangan Verifikasi Bukti Tayang untuk SPJ -->
            <div class="signature-section" style="margin-top: 20px;">
                <div class="sig-col">
                    <div class="sig-title">
                        Diverifikasi Oleh (Redaksi),
                    </div>
                    <div class="sig-name">
                        ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                    </div>
                    <div class="sig-sub">Redaktur Multimedia / Medsos</div>
                </div>

                <div class="sig-col">
                    <div class="sig-title">
                        Penanggung Jawab Iklan,
                    </div>
                    <div class="sig-name">
                        ( {{ $transaksi->sales_iklanonline }} )
                    </div>
                    <div class="sig-sub">Account Executive</div>
                </div>

                <div class="sig-col">
                    <div class="sig-title">
                        Tanda Terima Instansi (SPJ),
                    </div>
                    <div class="sig-name">
                        ( {{ $transaksi->nama_pic ?: 'Pejabat Pembuat Komitmen' }} )
                    </div>
                    <div class="sig-sub">Pejabat Humas / PPK Pemesan</div>
                </div>
            </div>
        </div>
        @endif

    </div>

    <script>
        function printOnlySection(section) {
            const body = document.body;
            body.classList.remove('print-only-order', 'print-only-tearsheet');
            if (section === 'order') {
                body.classList.add('print-only-order');
            } else if (section === 'tearsheet') {
                body.classList.add('print-only-tearsheet');
            }
            window.print();
            setTimeout(() => {
                body.classList.remove('print-only-order', 'print-only-tearsheet');
            }, 1000);
        }
    </script>

</body>
</html>
