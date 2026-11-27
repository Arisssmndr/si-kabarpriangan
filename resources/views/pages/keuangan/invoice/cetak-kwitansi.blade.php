<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KWITANSI - {{ $invoice->no_invoice }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    <style>
        @page {
            size: A4 portrait; /* Atau half-letter */
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 10.5pt;
            color: #000;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
        }

        .paper-kwitansi {
            width: 210mm;
            min-height: 140mm;
            margin: 0 auto;
            background: #fff;
            padding: 12mm 16mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
        }

        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .paper-kwitansi {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .no-print {
                display: none !important;
            }
        }

        /* Toolbar Navigasi */
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
            background: #047857;
            color: #fff;
            border-color: #047857;
        }

        /* HEADER KWITANSI */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .kop-left .sub {
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #222;
        }
        .kop-left .title {
            font-size: 24pt;
            font-weight: 900;
            letter-spacing: -0.5px;
            margin: 0;
            font-family: "Times New Roman", Times, Georgia, serif;
            color: #000;
        }
        .kop-left .tagline {
            font-size: 7.5pt;
            font-weight: 900;
            letter-spacing: 1px;
            color: #333;
            text-transform: uppercase;
        }
        .kop-left .address {
            font-size: 8pt;
            color: #222;
            margin-top: 2px;
        }

        .header-title-right {
            text-align: right;
            font-size: 28pt;
            font-weight: 900;
            letter-spacing: 1px;
            color: #000;
            font-family: Arial, "Helvetica Neue", sans-serif;
        }

        /* FORM FIELD BOXES SESUAI FOTO 2 */
        .box-container {
            width: 100%;
            margin-bottom: 15px;
        }

        /* 1. SUDAH TERIMA DARI & NO */
        .row-terima {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 10px;
        }
        .field-labeled-box {
            position: relative;
            border: 1px solid #000;
            padding: 8px 12px;
            margin-top: 6px;
        }
        .field-labeled-box .label-floating {
            position: absolute;
            top: -9px;
            left: 12px;
            background: #fff;
            padding: 0 6px;
            font-size: 8.5pt;
            font-weight: 900;
            color: #000;
        }
        .box-terima-dari {
            flex: 1;
            max-width: 60%;
            min-height: 52px;
        }
        .box-terima-dari .cust-name {
            font-weight: bold;
            font-size: 11pt;
            color: #000;
        }
        .box-terima-dari .cust-address {
            font-size: 9.5pt;
            color: #111;
            margin-top: 2px;
        }
        .no-invoice-box {
            font-size: 11pt;
            padding-top: 10px;
            white-space: nowrap;
        }
        .no-invoice-box .label {
            font-weight: bold;
        }
        .no-invoice-box .val {
            font-weight: bold;
        }

        /* 2. TERBILANG BOX */
        .box-terbilang {
            width: 100%;
            margin-top: 10px;
            min-height: 40px;
            background: #e2e8f0 !important;
            display: flex;
            align-items: center;
        }
        .box-terbilang .val-terbilang {
            font-weight: bold;
            font-size: 11pt;
            color: #000;
            font-style: italic;
        }

        /* 3. UNTUK PEMBAYARAN BOX */
        .box-untuk-pembayaran {
            width: 100%;
            margin-top: 14px;
            min-height: 75px;
            line-height: 1.45;
            font-size: 10.5pt;
        }

        /* BOTTOM SECTION */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .bottom-table td {
            vertical-align: top;
            padding: 0;
        }

        .jumlah-box-wrapper {
            width: 55%;
        }
        .jumlah-box {
            display: inline-flex;
            align-items: center;
            border: 2px solid #000;
            background: #e2e8f0 !important;
            padding: 6px 14px;
            font-weight: 900;
            font-size: 13pt;
            letter-spacing: 0.5px;
            color: #000;
        }
        .jumlah-box .cur {
            margin-right: 12px;
            font-style: italic;
            font-family: "Times New Roman", serif;
            font-size: 14pt;
        }
        .disclaimer {
            font-size: 8.5pt;
            font-style: italic;
            color: #333;
            margin-top: 10px;
            line-height: 1.35;
        }

        .ttd-box {
            width: 45%;
            text-align: right;
            padding-right: 15px;
            font-size: 10pt;
        }
        .ttd-date {
            margin-bottom: 5px;
        }
        .signature-space {
            height: 65px;
        }
        .ttd-name {
            font-weight: bold;
            font-size: 11pt;
            color: #000;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Toolbar Cetak (Non-Printable) -->
    <div class="toolbar no-print">
        <a href="{{ route('keuangan.invoice.show', $invoice->id) }}" class="btn">
            ← Kembali ke Detail Invoice
        </a>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('keuangan.invoice.cetak-invoice', $invoice->id) }}" target="_blank" class="btn">
                📄 Buka Lembar Invoice
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Cetak Kwitansi (Print / PDF)
            </button>
        </div>
    </div>

    <!-- Lembar Fisik Dokumen KWITANSI (SESUAI FOTO 2) -->
    <div class="paper-kwitansi">

        <!-- HEADER KOP KWITANSI -->
        <table class="header-table">
            <tr>
                <td class="kop-left" style="width: 60%;">
                    <div class="sub">HARIAN UMUM</div>
                    <div class="title">Kabar Priangan</div>
                    <div class="tagline">TERBAIK &amp; BERPENGARUH</div>
                    <div class="address">Jl. Dr. Soekardjo No. 70 Kota Tasikmalaya</div>
                </td>
                <td class="header-title-right" style="width: 40%;">
                    KWITANSI
                </td>
            </tr>
        </table>

        <!-- FIELD FORM SECTION -->
        <div class="box-container">
            
            <!-- 1. SUDAH TERIMA DARI & NO -->
            <div class="row-terima">
                <div class="field-labeled-box box-terima-dari">
                    <span class="label-floating">Sudah terima dari</span>
                    <div class="cust-name">{{ $invoice->kepada }}</div>
                    <div class="cust-address">{{ $invoice->alamat }}</div>
                </div>

                <div class="no-invoice-box">
                    <span class="label">No. :</span>
                    <span class="val">{{ $invoice->no_invoice }}</span>
                </div>
            </div>

            <!-- 2. TERBILANG -->
            <div class="field-labeled-box box-terbilang">
                <span class="label-floating" style="background: transparent;">Terbilang</span>
                <div class="val-terbilang">{{ $invoice->terbilang }}</div>
            </div>

            <!-- 3. UNTUK PEMBAYARAN -->
            <div class="field-labeled-box box-untuk-pembayaran">
                <span class="label-floating">Untuk Pembayaran</span>
                <div>{!! nl2br(e($invoice->deskripsi)) !!}</div>
            </div>

        </div>

        <!-- BAGIAN BAWAH (JUMLAH & TANDA TANGAN) -->
        <table class="bottom-table">
            <tr>
                <td class="jumlah-box-wrapper">
                    <div style="margin-bottom: 6px; font-weight: bold; font-size: 10pt;">
                        JUMLAH :
                    </div>
                    <div class="jumlah-box">
                        <span class="cur">Rp</span>
                        <span>{{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="disclaimer">
                        Pembayaran melalui Cek / GB dianggap sah kalau sudah dapat diuangkan
                    </div>
                </td>

                <td class="ttd-box">
                    <div class="ttd-date">
                        {{ $invoice->kota_terbit }}, {{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->translatedFormat('d F Y') : date('d F Y') }}
                    </div>
                    <div class="signature-space"></div>
                    <div class="ttd-name">{{ $invoice->nama_penandatangan }}</div>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>
