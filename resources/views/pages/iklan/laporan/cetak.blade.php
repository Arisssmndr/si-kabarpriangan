<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Iklan - Kabar Priangan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 11px;
            color: #0f172a;
            background: #f8fafc;
            padding: 24px;
        }
        .page {
            max-width: 1080px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .no-print {
            max-width: 1080px;
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0A72AC;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            transition: all 0.15s;
            text-decoration: none;
        }
        .btn-white {
            background: #ffffff;
            color: #0A72AC;
        }
        .btn-white:hover {
            background: #f1f5f9;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Kop Surat */
        .kop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2.5px solid #0A72AC;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .kop-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .kop-logo-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #0A72AC;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            letter-spacing: -0.5px;
        }
        .kop-title h1 {
            font-size: 17px;
            font-weight: 800;
            color: #0A72AC;
            letter-spacing: -0.3px;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .kop-title p {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .kop-contact {
            text-align: right;
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Judul Laporan */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-header h2 {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-header .sub {
            font-size: 11px;
            color: #475569;
            margin-top: 4px;
        }

        /* Meta Info Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .meta-item .label {
            font-size: 9.5px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .meta-item .val {
            font-size: 11px;
            color: #0f172a;
            font-weight: 600;
            margin-top: 2px;
        }

        /* Summary KPI Cards */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .kpi-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            background: #ffffff;
        }
        .kpi-card .kpi-label {
            font-size: 9.5px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
        }
        .kpi-card .kpi-num {
            font-size: 14px;
            font-weight: 700;
            margin-top: 4px;
            font-variant-numeric: tabular-nums;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 24px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 8.5px;
            letter-spacing: 0.3px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; font-variant-numeric: tabular-nums; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: 600;
        }
        .badge-lunas { background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; }
        .badge-piutang { background: #f8fafc; color: #0f172a; border: 1px solid #cbd5e1; }

        .total-row td {
            background-color: #f8fafc;
            font-weight: 700;
            border-top: 2px solid #0f172a;
        }

        /* Tanda Tangan */
        .ttd-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 36px;
            page-break-inside: avoid;
        }
        .ttd-box {
            text-align: center;
        }
        .ttd-space {
            height: 64px;
        }
        .ttd-name {
            font-weight: 700;
            font-size: 11px;
            text-decoration: underline;
        }
        .ttd-role {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 2px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .page {
                border: none;
                box-shadow: none;
                padding: 10px 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Aksi (Hanya di Layar) -->
    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-weight: 700; font-size: 13px;">Pratinjau Cetak Laporan Iklan</span>
            <span style="font-size: 11px; opacity: 0.85;">&bull; Mode Siap Cetak (A4 / Letter)</span>
        </div>
        <div style="display: flex; items-center; gap: 8px;">
            <button onclick="window.print()" class="btn btn-white">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Sekarang
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                Tutup
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Resmi -->
    <div class="page">

        <!-- Kop Surat -->
        <div class="kop">
            <div class="kop-logo">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" style="height: 46px; width: auto; object-fit: contain;">
                <div class="kop-title">
                    <h1>Harian Umum Kabar Priangan</h1>
                    <p>PT. BERKAH PIKIRAN RAKYAT PRIANGAN &bull; UNIT BISNIS MULTIMEDIA</p>
                </div>
            </div>
            <div class="kop-contact">
                Jl. RE Martadinata No. 126, Kota Tasikmalaya 46133<br>
                Telp: (0265) 332211 | Fax: (0265) 332212<br>
                Portal: kabarpriangan.pikiran-rakyat.com
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="report-header">
            <h2>Laporan Rekapitulasi Pembukuan Transaksi Iklan</h2>
            <div class="sub">Periode: <strong>{{ $periodLabel }}</strong></div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div class="meta-item">
                <div class="label">Saluran Media</div>
                <div class="val">{{ strtoupper($media === 'all' ? 'Semua Saluran (Koran, Online, TV)' : $media) }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Status Pelunasan</div>
                <div class="val">{{ ucfirst($status) }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Tanggal Cetak</div>
                <div class="val">{{ \Carbon\Carbon::now()->translatedFormat('d F Y - H:i') }} WIB</div>
            </div>
            <div class="meta-item">
                <div class="label">Petugas / Kasir</div>
                <div class="val">{{ auth()->user()->name ?? 'Kasir Kabar Priangan' }}</div>
            </div>
        </div>

        <!-- Ringkasan Angka Finansial -->
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-label">Total Transaksi</div>
                <div class="kpi-num">{{ number_format($totalTransaksi) }} Faktur</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Omset (Bruto)</div>
                <div class="kpi-num">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Kas Masuk (Lunas)</div>
                <div class="kpi-num">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Sisa Piutang</div>
                <div class="kpi-num">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Rincian Tabel Transaksi -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 28px;">No</th>
                    <th style="width: 85px;">No. Faktur</th>
                    <th style="width: 65px;">Saluran</th>
                    <th style="width: 70px;">Tanggal</th>
                    <th>Nama Pemasang / Klien</th>
                    <th>Jenis / Paket Iklan</th>
                    <th class="text-right" style="width: 85px;">Total Tagihan</th>
                    <th class="text-right" style="width: 85px;">Kas Masuk</th>
                    <th class="text-right" style="width: 85px;">Piutang</th>
                    <th class="text-center" style="width: 65px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="font-mono font-medium">{{ $row->faktur }}</td>
                        <td>{{ $row->media_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td><strong>{{ $row->customer }}</strong></td>
                        <td>{{ $row->paket }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($row->bayar, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">
                            Rp {{ number_format($row->piutang, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($row->is_lunas)
                                <span class="badge badge-lunas">Lunas</span>
                            @else
                                <span class="badge badge-piutang">Piutang</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 24px; color: #64748b;">
                            Tidak ada transaksi yang tercatat pada rentang waktu dan parameter laporan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($items->isNotEmpty())
                <tfoot>
                    <tr class="total-row">
                        <td colspan="6" class="text-right" style="text-transform: uppercase;">Total Akumulasi:</td>
                        <td class="text-right">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                        <td class="text-center">-</td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <!-- Tanda Tangan Resmi -->
        <div class="ttd-wrap">
            <div class="ttd-box">
                <div>Mengetahui,</div>
                <div style="font-weight: 600;">Bagian Keuangan & Umum</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">( ............................................ )</div>
                <div class="ttd-role">NIP / Jabatan</div>
            </div>
            <div class="ttd-box">
                <div>Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: 600;">Petugas Kasir Iklan</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ auth()->user()->name ?? '( ............................................ )' }}</div>
                <div class="ttd-role">Kasir Pelaksana</div>
            </div>
        </div>

    </div>

</body>
</html>
