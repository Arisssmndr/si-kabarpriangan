<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Iklan Koran - {{ $transaksi->nofakturkoran }}</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            padding: 30px;
            background: #fff;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #cbd5e1;
            padding: 30px;
            border-radius: 8px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0A72AC;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header-title h1 {
            margin: 0;
            font-size: 22px;
            color: #0A72AC;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .header-title p {
            margin: 3px 0;
            font-size: 11px;
            color: #64748b;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
        }
        .info-box h3 {
            margin: 0 0 8px 0;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
        }
        .info-table {
            width: 100%;
            font-size: 12px;
        }
        .info-table td {
            padding: 2px 0;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .table-data th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            text-transform: uppercase;
            padding: 10px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
        }
        .table-data td {
            padding: 10px;
            border: 1px solid #cbd5e1;
        }
        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .total-table {
            width: 350px;
            font-size: 12px;
        }
        .total-table td {
            padding: 4px 8px;
        }
        .total-table tr.grand-total td {
            font-weight: 800;
            font-size: 14px;
            color: #0A72AC;
            border-top: 2px solid #0A72AC;
            padding-top: 8px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            text-align: center;
        }
        .signature-box {
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin-top: 60px;
            margin-bottom: 5px;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background: #0A72AC;
            color: white;
            border: none;
            padding: 8px 18px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
        }
        @media print {
            body { padding: 0; }
            .container { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">Cetak Faktur</button>
        </div>

        <div class="header">
            <div class="header-title">
                <h1>Harian Umum Kabar Priangan</h1>
                <p>Media Harian Terbesar & Terpercaya di Priangan Timur</p>
                <p>Jl. Dr. Soekardjo No. 70, Tasikmalaya · Telp: (0265) 335300</p>
            </div>
            <div style="text-align: right;">
                <h2 style="margin: 0; font-size: 18px; color: #0A72AC;">FAKTUR IKLAN KORAN</h2>
                <p style="margin: 3px 0; font-size: 12px; font-weight: bold;">{{ $transaksi->nofakturkoran }}</p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>Identitas Pemasang</h3>
                <table class="info-table">
                    <tr><td width="30%"><strong>Nama:</strong></td><td>{{ $transaksi->nama_pemasangkoran }}</td></tr>
                    <tr><td><strong>Alamat:</strong></td><td>{{ $transaksi->alamat_pemasangkoran }}</td></tr>
                    <tr><td><strong>Marketing:</strong></td><td>{{ $transaksi->sales_iklankoran }}</td></tr>
                </table>
            </div>
            <div class="info-box">
                <h3>Detail Faktur</h3>
                <table class="info-table">
                    <tr><td width="40%"><strong>Tgl Transaksi:</strong></td><td>{{ $transaksi->tanggal_transaksikoran }}</td></tr>
                    <tr><td><strong>Tgl Terbit:</strong></td><td>{{ $transaksi->tanggal_muatkoran }}</td></tr>
                    <tr><td><strong>Total Edisi:</strong></td><td>{{ $transaksi->total_muatkoran }} kali terbit</td></tr>
                </table>
            </div>
        </div>

        <table class="table-data">
            <thead>
                <tr>
                    <th>Deskripsi Pemuatan Iklan</th>
                    <th>Penempatan</th>
                    <th>Warna</th>
                    <th>Ukuran</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $transaksi->iklankoran->jenis_iklankoran ?? 'Iklan Koran Cetak' }}</strong></td>
                    <td>{{ $transaksi->halaman_iklan }}</td>
                    <td>{{ $transaksi->warna_iklan }}</td>
                    <td>{{ $transaksi->ukuran_iklan ?? '-' }}</td>
                    <td style="text-align: right;">Rp {{ number_format($transaksi->harga_transaksikoran, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $transaksi->total_muatkoran }}</td>
                    <td style="text-align: right;">Rp {{ number_format($transaksi->harga_transaksikoran * $transaksi->total_muatkoran, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td>Total Omzet Kotor:</td>
                    <td style="text-align: right;">Rp {{ number_format($transaksi->harga_transaksikoran * $transaksi->total_muatkoran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Potongan Diskon:</td>
                    <td style="text-align: right;">Rp {{ number_format($transaksi->diskon_transaksikoran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Dasar Pengenaan Pajak (DPP):</td>
                    <td style="text-align: right;">Rp {{ number_format(max(0, $transaksi->totaltagihan_transaksikoran - $transaksi->ppn_transaksikoran), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>PPN (11%):</td>
                    <td style="text-align: right;">Rp {{ number_format($transaksi->ppn_transaksikoran, 0, ',', '.') }}</td>
                </tr>
                <tr class="grand-total">
                    <td>TOTAL TAGIHAN:</td>
                    <td style="text-align: right;">Rp {{ number_format($transaksi->totaltagihan_transaksikoran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Jumlah Pembayaran:</strong></td>
                    <td style="text-align: right; color: #166534; font-weight: bold;">Rp {{ number_format($transaksi->jumlahbayar_transaksikoran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Sisa Piutang:</strong></td>
                    <td style="text-align: right; color: {{ $transaksi->piutang_transaksikoran > 0 ? '#b91c1c' : '#166534' }}; font-weight: bold;">
                        Rp {{ number_format($transaksi->piutang_transaksikoran, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p>Pemasang / Klien</p>
                <div class="signature-line"></div>
                <p><strong>{{ $transaksi->nama_pemasangkoran }}</strong></p>
            </div>
            <div class="signature-box">
                <p>Petugas Kasir Iklan</p>
                <div class="signature-line"></div>
                <p><strong>{{ Auth::user()->name ?? 'Kasir Kabar Priangan' }}</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
