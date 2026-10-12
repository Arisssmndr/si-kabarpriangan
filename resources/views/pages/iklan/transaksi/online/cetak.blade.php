<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur - {{ $transaksi->nofakturonline }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #333;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #eee;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        /* Header / Kop Surat */
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #2d3748;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            color: #718096;
        }

        /* Info Faktur */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-box h3 {
            margin-top: 0;
            font-size: 16px;
            text-decoration: underline;
        }

        .info-table td {
            padding: 2px 10px 2px 0;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Tabel Rincian */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .invoice-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Total Section */
        .total-section {
            width: 100%;
            border-top: 2px solid #333;
        }

        .total-row td {
            padding: 8px 12px;
            font-weight: bold;
        }

        /* Footer / Tanda Tangan */
        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .signature-box {
            height: 80px;
            /* Ruang untuk tanda tangan */
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 0 auto;
        }

        /* Status Stamp */
        .status-stamp {
            display: inline-block;
            padding: 5px 15px;
            border: 2px solid;
            font-weight: bold;
            font-size: 16px;
            transform: rotate(-5deg);
            margin-top: 10px;
        }

        .lunas {
            border-color: green;
            color: green;
        }

        .belum {
            border-color: red;
            color: red;
        }

        /* Print Configuration */
        @media print {
            .no-print {
                display: none !important;
            }

            .container {
                border: none;
                box-shadow: none;
                padding: 0;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()"
            style="background-color: #3182ce; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-size: 16px;">
            Cetak Faktur
        </button>
    </div>

    <div class="container">

        <div class="header">
            <h1>HARIAN UMUM KABAR PRIANGAN</h1>
            <p>Jl. Dr. Sukarjo No.70, Tawangsari, Kec. Tawang, Kab. Tasikmalaya, Jawa Barat 46112</p>
            <p>Telepon : Redaksi 0265-7525756, Iklan/Sirkulasi 0265-335300 | hukabarpriangan@gmail.com</p>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>TAGIHAN KEPADA:</h3>
                <table class="info-table">
                    <tr>
                        <td class="font-bold">Nama</td>
                        <td>: {{ $transaksi->nama_pemasangonline }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Alamat</td>
                        <td>: {{ $transaksi->alamat_pemasangonline }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Sales</td>
                        <td>: {{ $transaksi->sales_iklanonline }}</td>
                    </tr>
                </table>
            </div>
            <div class="info-box">
                <h3>DETAIL FAKTUR:</h3>
                <table class="info-table">
                    <tr>
                        <td class="font-bold">No Faktur</td>
                        <td>: {{ $transaksi->nofakturonline }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Tanggal</td>
                        <td>: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksionline)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Status</td>
                        <td>:
                            @if ($transaksi->piutang_transaksionline <= 0)
                                <span style="color: green; font-weight: bold;">LUNAS</span>
                            @else
                                <span style="color: red; font-weight: bold;">BELUM LUNAS</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 45%">Deskripsi</th>
                    <th class="text-center" style="width: 15%">Harga</th>
                    <th class="text-center" style="width: 10%">Total Muat</th>
                    <th class="text-right" style="width: 25%">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <strong>{{ $transaksi->iklanonline->jenis_iklanonline ?? 'Iklan Umum' }}</strong><br>
                        <small>Portal: {{ $transaksi->portal_iklanonline }}</small><br>
                        <small>Tanggal Muat:
                            {{ \Carbon\Carbon::parse($transaksi->tanggal_muatiklanonline)->format('d/m/Y') }}</small>
                    </td>
                    <td class="text-center">{{ number_format($transaksi->harga_transaksionline, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $transaksi->total_muatiklanonline }}</td>

                    {{-- Harga x Qty --}}
                    @php
                        $subtotal_item = $transaksi->harga_transaksionline * $transaksi->total_muatiklanonline;
                    @endphp
                    <td class="text-right">{{ number_format($subtotal_item, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div style="width: 40%; margin-left: auto;">
            <table class="invoice-table" style="margin-top: 0;">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">{{ number_format($subtotal_item, 0, ',', '.') }}</td>
                </tr>

                @if ($transaksi->diskon_transaksionline > 0)
                    <tr>
                        <td>Diskon</td>
                        <td class="text-right" style="color: red;">-
                            {{ number_format($transaksi->diskon_transaksionline, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <tr>
                    <td>PPN (11%)</td>
                    <td class="text-right">{{ number_format($transaksi->ppn_transaksionline, 0, ',', '.') }}</td>
                </tr>

                <tr style="border-top: 2px solid #333; font-size: 16px; background-color: #f8f9fa;">
                    <td><strong>TOTAL TAGIHAN</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($transaksi->totaltagihan_transaksionline, 0, ',', '.') }}</strong></td>
                </tr>

                <tr>
                    <td>Dibayar (DP)</td>
                    <td class="text-right">{{ number_format($transaksi->jumlahbayar_transaksionline, 0, ',', '.') }}
                    </td>
                </tr>

                <tr>
                    <td><strong>SISA PIUTANG</strong></td>
                    <td class="text-right"
                        style="color: {{ $transaksi->piutang_transaksionline > 0 ? 'red' : 'green' }};">
                        <strong>Rp {{ number_format($transaksi->piutang_transaksionline, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div>
                <p>Penerima,</p>
                <div class="signature-box"></div>
                <div class="signature-line">{{ $transaksi->nama_pemasangonline }}</div>
            </div>
            <div>
                <p>Hormat Kami,</p>
                <div class="signature-box">
                    @if ($transaksi->piutang_transaksionline <= 0)
                        <div class="status-stamp lunas">LUNAS</div>
                    @endif
                </div>
                <div class="signature-line">Admin Keuangan</div>
            </div>
        </div>

    </div>

</body>

</html>
