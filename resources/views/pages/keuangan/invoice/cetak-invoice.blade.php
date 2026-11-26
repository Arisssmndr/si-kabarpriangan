<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVOICE - {{ $invoice->no_invoice }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 18mm 15mm 18mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
        }

        .paper-invoice {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 16mm 20mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
        }

        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .paper-invoice {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .no-print {
                display: none !important;
            }
        }

        /* Tombol Cetak Navigasi */
        .toolbar {
            max-width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #1e293b;
        }
        .btn-primary {
            background: #0f172a;
            color: #fff;
            border-color: #0f172a;
        }

        /* HEADER KOP KABAR PRIANGAN SESUAI FOTO 1 */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }
        .kop-brand {
            width: 50%;
        }
        .kop-brand .kop-subtitle {
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0;
            color: #000;
            text-transform: uppercase;
        }
        .kop-brand .kop-title {
            font-size: 26pt;
            font-weight: 900;
            letter-spacing: -0.5px;
            margin: 1px 0 0 0;
            color: #000;
            font-family: "Times New Roman", Times, Georgia, serif;
        }
        .kop-brand .kop-tagline {
            font-size: 8.5pt;
            font-weight: bold;
            font-style: italic;
            color: #444;
            margin-top: 2px;
        }
        .kop-info {
            width: 50%;
            text-align: right;
            font-size: 8pt;
            line-height: 1.35;
            color: #111;
        }
        .kop-info .grup-pr {
            font-weight: bold;
            font-size: 8.5pt;
            margin-top: 3px;
        }

        .kop-divider {
            border-bottom: 2px solid #000;
            margin-bottom: 18px;
        }

        /* TITLE INVOICE */
        .invoice-title {
            font-size: 16pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            margin: 0 0 10px 0;
            text-decoration: none;
        }

        /* METADATA (NO, TO, ATTN) */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            margin-bottom: 16px;
        }
        .meta-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }
        .meta-label {
            width: 55px;
            font-weight: normal;
        }
        .meta-colon {
            width: 15px;
        }
        .meta-val {
            font-weight: normal;
        }
        .meta-no {
            font-weight: bold;
            text-decoration: underline;
        }

        /* TABEL RINCIAN */
        .invoice-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 12px;
        }
        .invoice-items th, .invoice-items td {
            border: 1px solid #000;
            padding: 8px 10px;
        }
        .invoice-items th {
            text-align: center;
            font-weight: bold;
            background: #fff;
        }
        .invoice-items td.col-no {
            width: 5%;
            text-align: center;
            vertical-align: top;
        }
        .invoice-items td.col-desc {
            width: 55%;
            text-align: left;
            vertical-align: top;
            line-height: 1.45;
        }
        .invoice-items td.col-qty {
            width: 6%;
            text-align: center;
            vertical-align: top;
        }
        .invoice-items td.col-price {
            width: 17%;
            text-align: right;
            vertical-align: top;
        }
        .invoice-items td.col-total {
            width: 17%;
            text-align: right;
            vertical-align: top;
        }

        /* SUMMARY DPP, PPN 11%, TOTAL */
        .invoice-items td.summary-label {
            text-align: right;
            font-weight: bold;
            padding: 4px 10px;
            border: 1px solid #000;
        }
        .invoice-items td.summary-val {
            text-align: right;
            font-weight: bold;
            padding: 4px 10px;
            border: 1px solid #000;
        }
        .invoice-items .no-border {
            border-left: none !important;
            border-bottom: none !important;
            border-top: none !important;
        }

        /* TERBILANG */
        .sum-of {
            font-size: 10pt;
            margin: 12px 0 16px 0;
            font-style: italic;
        }
        .sum-of .label {
            font-style: italic;
            margin-right: 8px;
        }
        .sum-of .val {
            font-weight: bold;
            font-style: normal;
        }

        /* NOTE TRANSFER */
        .notes-section {
            font-size: 9.5pt;
            line-height: 1.45;
            margin-bottom: 25px;
        }
        .notes-section .note-header {
            font-weight: normal;
            margin-bottom: 4px;
        }
        .notes-section .bank-name {
            font-weight: normal;
            text-transform: uppercase;
        }
        .notes-section .bank-norek {
            font-weight: normal;
            margin-bottom: 4px;
        }
        .notes-section .contact-info {
            margin-top: 8px;
        }

        /* TANDA TANGAN */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .signature-table td {
            vertical-align: top;
            padding: 0;
        }
        .signature-box {
            width: 45%;
            margin-left: auto;
            text-align: left;
            font-size: 10pt;
        }
        .signature-space {
            height: 65px;
        }
        .signee-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signee-title {
            font-size: 9.5pt;
            color: #111;
        }

        /* FOOTER SOSIAL MEDIA */
        .footer-socmed {
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            margin-top: 30px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            font-size: 9pt;
            color: #334155;
            font-weight: bold;
        }
        .socmed-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi Toolbar (Non-Printable) -->
    <div class="toolbar no-print">
        <a href="{{ route('keuangan.invoice.show', $invoice->id) }}" class="btn">
            ← Kembali ke Detail Invoice
        </a>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('keuangan.invoice.cetak-kwitansi', $invoice->id) }}" target="_blank" class="btn">
                🧾 Buka Lembar Kwitansi
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Cetak Invoice (Print / PDF)
            </button>
        </div>
    </div>

    <!-- Lembar Fisik Dokumen INVOICE -->
    <div class="paper-invoice">

        <!-- KOP SURAT RESMI KABAR PRIANGAN (SESUAI FOTO 1) -->
        <table class="kop-table">
            <tr>
                <td class="kop-brand">
                    <div class="kop-subtitle">HARIAN UMUM</div>
                    <div class="kop-title">Kabar Priangan</div>
                    <div class="kop-tagline">Terbaik &amp; Berpengaruh</div>
                </td>
                <td class="kop-info">
                    <div>Jl. Dr. Soekardjo No.70 Kota Tasikmalaya</div>
                    <div>Telp. (0265) 335300</div>
                    <div>email redaksi : hukabarpriangan@gmail.com | iklan : ad_priangan@yahoo.com</div>
                    <div>Online : http://www.kabar-priangan.com</div>
                    <div class="grup-pr">Grup Pikiran Rakyat</div>
                </td>
            </tr>
        </table>

        <!-- Garis Pemisah Kop -->
        <div class="kop-divider"></div>

        <!-- JUDUL DOKUMEN -->
        <div class="invoice-title">INVOICE</div>

        <!-- METADATA DOKUMEN -->
        <table class="meta-table">
            <tr>
                <td class="meta-label">No</td>
                <td class="meta-colon">:</td>
                <td class="meta-val"><span class="meta-no">{{ $invoice->no_invoice }}</span></td>
            </tr>
            <tr>
                <td class="meta-label">To</td>
                <td class="meta-colon">:</td>
                <td class="meta-val">
                    <strong>{{ $invoice->kepada }}</strong><br>
                    {{ $invoice->alamat }}
                </td>
            </tr>
            @if($invoice->attn)
            <tr>
                <td class="meta-label">Att</td>
                <td class="meta-colon">:</td>
                <td class="meta-val">{{ $invoice->attn }}</td>
            </tr>
            @endif
        </table>

        <!-- TABEL RINCIAN ITEM -->
        <table class="invoice-items">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 55%;">Description</th>
                    <th style="width: 6%;">Qty</th>
                    <th style="width: 17%;">Unit Price<br>(Rp)</th>
                    <th style="width: 17%;">Total Amount<br>(Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="col-no">1</td>
                    <td class="col-desc">
                        {!! nl2br(e($invoice->deskripsi)) !!}
                    </td>
                    <td class="col-qty">{{ $invoice->qty }}</td>
                    <td class="col-price">{{ number_format($invoice->unit_price, 0, ',', '.') }}</td>
                    <td class="col-total">{{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                <!-- Summary DPP, PPN 11%, Grand Total -->
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td class="summary-label">DPP</td>
                    <td class="summary-val">{{ number_format($invoice->dpp, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td class="summary-label">Ppn 11%</td>
                    <td class="summary-val">{{ number_format($invoice->ppn_nominal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border"></td>
                    <td class="summary-label">Total</td>
                    <td class="summary-val">{{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- SUM OF / TERBILANG -->
        <div class="sum-of">
            <span class="label">sum of</span>
            <span class="val">{{ $invoice->terbilang }}</span>
        </div>

        <!-- CATATAN INSTRUKSI PEMBAYARAN BANK -->
        <div class="notes-section">
            <div class="note-header">Note : Pembayaran mohon ditransfer ke :</div>
            {!! nl2br(e($invoice->catatan_transfer)) !!}
        </div>

        <!-- TANDA TANGAN RESMI -->
        <table class="signature-table">
            <tr>
                <td style="width: 55%;"></td>
                <td style="width: 45%;">
                    <div class="signature-box">
                        <div>{{ $invoice->kota_terbit }}, {{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->translatedFormat('d F Y') : date('d F Y') }}</div>
                        <div class="signature-space"></div>
                        <div class="signee-name">{{ $invoice->nama_penandatangan }}</div>
                        <div class="signee-title">{{ $invoice->jabatan_penandatangan }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- FOOTER SOSIAL MEDIA RESMI -->
        <div class="footer-socmed">
            <div class="socmed-item">
                <span>🎵</span>
                <span>@kabarpriangancom</span>
            </div>
            <div class="socmed-item">
                <span>📘</span>
                <span>@kabarpriangancom</span>
            </div>
            <div class="socmed-item">
                <span>📷</span>
                <span>@kabarpriangancom</span>
            </div>
        </div>

    </div>

</body>
</html>
