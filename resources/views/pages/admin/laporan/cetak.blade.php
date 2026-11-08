<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Eksekutif Konsolidasi - {{ $namaMedia }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #111827;
        }

        body {
            background-color: #f8fafc;
            padding: 24px;
            font-size: 11pt;
            line-height: 1.4;
        }

        .sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .header-kop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2.5px solid #0A72AC;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .brand-info h1 {
            font-size: 16pt;
            font-weight: 800;
            color: #0A72AC;
            letter-spacing: -0.02em;
        }

        .brand-info p {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 2px;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h2 {
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e293b;
        }

        .doc-title .badge {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 700;
            margin-top: 4px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .summary-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            background: #f8fafc;
        }

        .summary-box .label {
            font-size: 8pt;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
        }

        .summary-box .val {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }

        table th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8pt;
            color: #334155;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 40px;
            padding-top: 16px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-space {
            height: 60px;
        }

        .no-print-bar {
            max-width: 820px;
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-print {
            background: #0A72AC;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 9pt;
            font-weight: 700;
            cursor: pointer;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .sheet {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('admin.laporan.index') }}" style="text-decoration: none; color: #475569; font-size: 9pt; font-weight: 600;">
            &larr; Kembali ke Dashboard Laporan
        </a>
        <button class="btn-print" onclick="window.print()">
            Cetak Dokumen Sekarang (Ctrl + P)
        </button>
    </div>

    <div class="sheet">
        
        <!-- Header Kop Surat -->
        <div class="header-kop">
            <div class="brand-info">
                <h1>{{ $namaMedia }}</h1>
                <p><strong>{{ $namaPerusahaan }}</strong></p>
                <p>{{ $alamatPerusahaan }}</p>
                <p>Priangan Timur: Tasikmalaya - Ciamis - Banjar - Garut - Pangandaran</p>
            </div>
            <div class="doc-title">
                <h2>Laporan Eksekutif</h2>
                <div class="badge">Periode: {{ $periodeLabel }}</div>
                <div style="font-size: 8pt; color: #64748b; margin-top: 4px;">
                    Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB
                </div>
            </div>
        </div>

        <!-- KPI Summary Grid -->
        <div class="summary-grid">
            <div class="summary-box">
                <div class="label">Total Omset Konsolidasi</div>
                <div class="val">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Realisasi Kas Masuk</div>
                <div class="val" style="color: #059669;">Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Sisa Piutang Berjalan</div>
                <div class="val" style="color: #e11d48;">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Collection Rate</div>
                <div class="val" style="color: #4f46e5;">{{ $collectionRate }}%</div>
            </div>
        </div>

        <!-- 1. Breakdown Saluran Media -->
        <div style="font-size: 9.5pt; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; color: #1e293b;">
            1. Rekapitulasi Pendapatan Berdasarkan Saluran Media
        </div>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 35px;">No</th>
                    <th>Saluran Publikasi</th>
                    <th class="text-center">Jml Order</th>
                    <th class="text-right">Total Omset (Rp)</th>
                    <th class="text-right">Realisasi Kas (Rp)</th>
                    <th class="text-right">Sisa Piutang (Rp)</th>
                    <th class="text-center">Pangsa (%)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="font-bold">Koran Cetak (Harian Umum Kabar Priangan)</td>
                    <td class="text-center">{{ $txKoran }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($omsetKoran, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($bayarKoran, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($piutangKoran, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ $pctKoran }}%</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td class="font-bold">Media Online (kabarpriangan.pikiran-rakyat.com)</td>
                    <td class="text-center">{{ $txOnline }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($omsetOnline, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($bayarOnline, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($piutangOnline, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ $pctOnline }}%</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td class="font-bold">Priangan TV (Siaran Televisi & Streaming)</td>
                    <td class="text-center">{{ $txTv }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($omsetTv, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($bayarTv, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($piutangTv, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ $pctTv }}%</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: 800;">
                    <td colspan="2" class="text-center">TOTAL KONSOLIDASI</td>
                    <td class="text-center">{{ $totalTransaksi }}</td>
                    <td class="text-right">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                    <td class="text-center">100%</td>
                </tr>
            </tfoot>
        </table>

        <!-- 2. Top Pemasang Iklan Terbesar -->
        <div style="font-size: 9.5pt; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; color: #1e293b; margin-top: 16px;">
            2. Daftar 10 Mitra Pemasang Iklan Terbesar
        </div>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 35px;">Rank</th>
                    <th>Nama Pemasang / Klien</th>
                    <th class="text-center">Frekuensi</th>
                    <th class="text-right">Nilai Transaksi (Rp)</th>
                    <th class="text-right">Sisa Piutang (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topPemasang as $idx => $p)
                    <tr>
                        <td class="text-center font-bold">{{ $idx + 1 }}</td>
                        <td class="font-bold">{{ $p['nama'] }}</td>
                        <td class="text-center">{{ $p['order_count'] }} kali</td>
                        <td class="text-right font-bold">Rp {{ number_format($p['omset'], 0, ',', '.') }}</td>
                        <td class="text-right">
                            @if($p['piutang'] <= 0)
                                <span style="color: #059669; font-weight: bold;">Lunas</span>
                            @else
                                Rp {{ number_format($p['piutang'], 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada transaksi di periode terpilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signature Area -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p><strong>General Manager / Direksi</strong></p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: 700;">( ...................................................... )</p>
                <p style="font-size: 8pt; color: #64748b;">Pimpinan Perusahaan</p>
            </div>
            <div class="signature-box">
                <p>{{ $kotaTerbit }}, {{ now()->translatedFormat('d F Y') }}</p>
                <p><strong>Manajer Keuangan &amp; Akuntansi</strong></p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: 700;">( ...................................................... )</p>
                <p style="font-size: 8pt; color: #64748b;">Bagian Keuangan &amp; Faktur</p>
            </div>
        </div>

    </div>

</body>
</html>
