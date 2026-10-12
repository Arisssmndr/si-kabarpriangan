<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur - {{ $transaksi->nofakturpriangan }}</title>
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

        /* Footer / Tanda Tangan */
        .footer-date {
            text-align: left;
            margin-top: 50px;
            margin-bottom: 20px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .footer-col {
            flex: 1;
            padding: 0 10px;
        }

        .signature-box {
            height: 90px; /* Ruang kosong untuk tanda tangan */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
            font-weight: bold;
        }

        /* Status Stamp */
        .status-stamp {
            display: inline-block;
            padding: 5px 15px;
            border: 2px solid;
            font-weight: bold;
            font-size: 16px;
            transform: rotate(-5deg);
        }

        .lunas {
            border-color: green;
            color: green;
        }

        .belum {
            border-color: red;
            color: red;
        }

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

            .footer {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
            }

            .footer-col {
                flex: 1 !important;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()"
            style="background-color: #3182ce; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">
            Cetak Faktur
        </button>
    </div>

    <div class="container">

        <div class="header">
            <h1>PRIANGAN TV</h1>
            <p>
                Jl. Dr. Soekarjo Nomor 70 Kelurahan Tawangsari Kecamatan Tawang </p>
            <p>Kota Tasikmalaya Jawa Barat (HU. Kabar
                Priangan) Jawa Barat 46112 </p>
            <p>Telp: 082260030311 | Email:
                priangantv2024@gmail.com</p>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>TAGIHAN KEPADA:</h3>
                <table class="info-table">
                    <tr>
                        <td class="font-bold">Nama</td>
                        <td>: {{ $transaksi->nama_pemasangpriangan }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Alamat</td>
                        <td>: {{ $transaksi->alamat_pemasangpriangan }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Sales</td>
                        <td>: {{ $transaksi->sales_iklanpriangan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="info-box">
                <h3>DETAIL FAKTUR:</h3>
                <table class="info-table">
                    <tr>
                        <td class="font-bold">No Faktur</td>
                        <td>: {{ $transaksi->nofakturpriangan }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Tanggal</td>
                        <td>: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksipriangan)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Status</td>
                        <td>:
                            @if ($transaksi->piutang_transaksipriangan <= 0)
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
                    <th class="text-center" style="width: 10%">Qty</th>
                    <th class="text-right" style="width: 25%">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <strong>{{ $transaksi->iklanpriangan->jenis_iklanpriangan ?? 'Iklan Umum' }}</strong><br>

                        <small>Tanggal Muat:
                            {{ \Carbon\Carbon::parse($transaksi->tanggal_muatiklanpriangan)->format('d/m/Y') }}</small>
                    </td>

                    <td class="text-center">{{ number_format($transaksi->harga_transaksipriangan, 0, ',', '.') }}</td>

                    @php
                        $qty = $transaksi->total_muatiklanpriangan ?? 1;
                        $subtotal_item = $transaksi->harga_transaksipriangan * $qty;
                    @endphp
                    <td class="text-center">{{ $qty }}</td>

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

                @if ($transaksi->diskon_transaksipriangan > 0)
                    <tr>
                        <td>Diskon</td>
                        <td class="text-right" style="color: red;">-
                            {{ number_format($transaksi->diskon_transaksipriangan, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <tr>
                    <td>PPN (11%)</td>
                    <td class="text-right">{{ number_format($transaksi->ppn_transaksipriangan ?? 0, 0, ',', '.') }}
                    </td>
                </tr>

                <tr style="border-top: 2px solid #333; font-size: 16px; background-color: #f8f9fa;">
                    <td><strong>TOTAL TAGIHAN</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($transaksi->totaltagihan_transaksipriangan ?? $subtotal_item + ($transaksi->ppn_transaksipriangan ?? 0), 0, ',', '.') }}</strong>
                    </td>
                </tr>

                <tr>
                    <td>Dibayar (DP)</td>
                    <td class="text-right">{{ number_format($transaksi->jumlahbayar_transaksipriangan, 0, ',', '.') }}
                    </td>
                </tr>

                <tr>
                    <td><strong>SISA PIUTANG</strong></td>
                    <td class="text-right"
                        style="color: {{ $transaksi->piutang_transaksipriangan > 0 ? 'red' : 'green' }};">
                        <strong>Rp {{ number_format($transaksi->piutang_transaksipriangan, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tanggal diletakkan di sebelah kiri -->
        <div class="footer-date">
            Tasikmalaya, {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksipriangan)->translatedFormat('d F Y') }}
        </div>

        <!-- Bagian 3 Kolom Tanda Tangan -->
        <div class="footer">
            <!-- Kolom Kiri -->
            <div class="footer-col">
                <p>Admin Iklan,</p>
                <div class="signature-box"></div>
                <div class="signature-line">Heni</div>
            </div>
            
            <!-- Kolom Tengah -->
            <div class="footer-col">
                <p>Manager Iklan,</p>
                <div class="signature-box">
                    @if ($transaksi->piutang_transaksipriangan <= 0)
                        <div class="status-stamp lunas">LUNAS</div>
                    @endif
                </div>
                <div class="signature-line">Asep Liana</div>
            </div>
            
            <!-- Kolom Kanan -->
            <div class="footer-col">
                <p>Senior Manager Opr & Business,</p>
                <div class="signature-box"></div>
                <div class="signature-line">Helma Apriyanti</div>
            </div>
        </div>

    </div>

</body>

</html>