<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Sirkulasi Bulanan - {{ $faktur->no_faktur }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #0f172a;
            background: #f8fafc;
            padding: 24px;
        }
        .container {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px 36px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .no-print {
            max-width: 820px;
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            background: #0A72AC;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-print:hover {
            background: #085a88;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-back:hover {
            background: #e2e8f0;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2.5px solid #0A72AC;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .header-left img {
            height: 48px;
            width: auto;
        }
        .company-title {
            font-size: 16px;
            font-weight: 800;
            color: #0A72AC;
            letter-spacing: -0.2px;
        }
        .company-sub {
            font-size: 10px;
            color: #475569;
            margin-top: 2px;
            line-height: 1.3;
        }
        .header-right {
            text-align: right;
        }
        .doc-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
        }
        .doc-no {
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            color: #0A72AC;
            margin-top: 3px;
        }
        .badge-status {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-lunas { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-sebagian { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-belum { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .meta-col table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-col td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .meta-label {
            width: 110px;
            color: #64748b;
            font-weight: 500;
        }
        .meta-sep {
            width: 12px;
            color: #94a3b8;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 600;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-data th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 7px 10px;
            font-size: 10px;
            font-weight: 700;
            color: #334155;
            text-align: left;
            text-transform: uppercase;
        }
        .table-data td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            font-size: 11px;
            color: #1e293b;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-mono { font-family: monospace; }
        .font-bold { font-weight: 700; }

        .total-box {
            background: #f8fafc;
            border: 1.5px solid #0A72AC;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }
        .total-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 16px;
            align-items: center;
        }
        .terbilang-box {
            font-style: italic;
            color: #334155;
            line-height: 1.4;
            font-size: 11px;
            border-right: 1px dashed #cbd5e1;
            padding-right: 16px;
        }
        .nominal-summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .nominal-summary td {
            padding: 3px 0;
            font-size: 11px;
        }
        .nominal-summary .grand-total td {
            padding-top: 6px;
            border-top: 1.5px solid #0A72AC;
            font-size: 13px;
            font-weight: 800;
            color: #0A72AC;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 28px;
            text-align: center;
        }
        .sig-block {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 10px;
            background: #ffffff;
        }
        .sig-title {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 50px;
        }
        .sig-name {
            font-weight: 700;
            font-size: 11px;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
        }
        .sig-sub {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Lampiran Harian */
        .page-break {
            page-break-before: always;
            margin-top: 32px;
            border-top: 2px dashed #cbd5e1;
            padding-top: 24px;
        }
        .lampiran-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .table-lampiran {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }
        .table-lampiran th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            font-weight: 700;
            color: #334155;
            text-align: center;
        }
        .table-lampiran td {
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            color: #1e293b;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .container {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
                border-top: none;
                padding-top: 0;
                margin-top: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi & Print (Hanya Tampil di Layar) -->
    <div class="no-print">
        <a href="{{ route('accounting.sirkulasi.faktur-bulanan', ['bulan' => $faktur->periode_bulan]) }}" class="btn-back">
            &larr; Kembali ke Daftar Faktur Bulanan
        </a>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-print">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Lembar Faktur & Lampiran
            </button>
        </div>
    </div>

    <div class="container">
        <!-- 1. HEADER RESMI PERUSAHAAN (KOP KORPORAT) -->
        <div class="header">
            <div class="header-left">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan">
                <div>
                    <div class="company-title">PT BERKAH PIKIRAN RAKYAT</div>
                    <div class="company-sub">
                        <strong>HARIAN UMUM KABAR PRIANGAN</strong> &bull; Grup Pikiran Rakyat<br>
                        Jl. Pancasila No. 34 Tasikmalaya 46112 &bull; Telp. (0265) 331444, 331445<br>
                        NPWP: 01.996.152.4-425.000 &bull; Divisi Keuangan & Akuntansi
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="doc-title">FAKTUR SIRKULASI BULANAN</div>
                <div class="doc-no">{{ $faktur->no_faktur }}</div>
                <div>
                    @if($faktur->status_pembayaran === 'lunas')
                        <span class="badge-status badge-lunas">&#10003; LUNAS</span>
                    @elseif($faktur->status_pembayaran === 'sebagian')
                        <span class="badge-status badge-sebagian">&bull; TERBAYAR SEBAGIAN</span>
                    @else
                        <span class="badge-status badge-belum">&bull; BELUM LUNAS</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. DATA IDENTITAS & PERIODE PENAGIHAN -->
        <div class="meta-grid">
            <div class="meta-col">
                <table>
                    <tr>
                        <td class="meta-label">Ditagihkan Kepada</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val">{{ $faktur->nama_agen }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Kode / ID Agen</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val font-mono">{{ $faktur->kode_agen }} (ID: {{ $faktur->agen_id }})</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Klasifikasi Agen</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val">{{ $faktur->agen && $faktur->agen->kategori === '11' ? 'Agen Utama (Besar)' : 'Sub-Agen / Pengecer' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Wilayah / Rute</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val">{{ $faktur->agen->wilayah ?? '-' }} / {{ $faktur->agen->rute_kode ?? 'R-01' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Alamat / HP</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val">{{ $faktur->agen->alamat ?? '-' }} ({{ $faktur->agen->telepon ?? '-' }})</td>
                    </tr>
                </table>
            </div>

            <div class="meta-col">
                <table>
                    <tr>
                        <td class="meta-label">Periode Tagihan</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val">
                            {{ \Carbon\Carbon::parse($faktur->periode_bulan . '-01')->translatedFormat('F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tanggal Faktur</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val">
                            {{ \Carbon\Carbon::parse($faktur->tanggal_faktur)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">Jatuh Tempo</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val" style="color: #b91c1c;">
                            {{ \Carbon\Carbon::parse($faktur->jatuh_tempo)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">No. Ref BAPR</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val font-mono">{{ $faktur->no_bapr ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Metode Billing</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-val">Konsolidasi 1 Bulan Kalender (End-of-Month)</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- 3. TABEL REKONSILIASI PENJUALAN SIRKULASI -->
        <div class="section-title">
            <span>Rekonsiliasi Distribusi & Tagihan Koran Bersih</span>
            <span style="font-size: 10px; color: #64748b; font-weight: normal;">Standar PSAK Industri Pers</span>
        </div>

        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">No</th>
                    <th>Deskripsi Layanan Sirkulasi</th>
                    <th style="width: 100px;" class="text-center">Volume (Eks)</th>
                    <th style="width: 110px;" class="text-right">Tarif Dasar</th>
                    <th style="width: 140px;" class="text-right">Jumlah Rupiah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center font-bold">1</td>
                    <td>
                        <strong>Pengiriman Koran Harian Kabar Priangan (Gross Delivery)</strong><br>
                        <span style="font-size: 10px; color: #64748b;">
                            Akumulasi pengiriman subuh selama bulan {{ \Carbon\Carbon::parse($faktur->periode_bulan . '-01')->translatedFormat('F Y') }} (Lihat lembar lampiran rincian harian)
                        </span>
                    </td>
                    <td class="text-center font-mono font-bold">{{ number_format($faktur->total_oplah_kirim, 0, ',', '.') }} eks</td>
                    <td class="text-right font-mono">Rp {{ number_format($faktur->tarif_per_eks, 0, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold">Rp {{ number_format($faktur->total_tagihan_kotor, 0, ',', '.') }}</td>
                </tr>

                <tr>
                    <td class="text-center font-bold">2</td>
                    <td>
                        <strong>Koreksi Retur Koran Tidak Terjual (Credit Note Retur)</strong><br>
                        <span style="font-size: 10px; color: #64748b;">
                            Verifikasi fisik retur edisi berjalan disetujui Bagian Akuntansi (No. BAPR: {{ $faktur->no_bapr ?: 'N/A' }})
                        </span>
                    </td>
                    <td class="text-center font-mono font-bold" style="color: #b91c1c;">
                        -{{ number_format($faktur->total_retur_eks, 0, ',', '.') }} eks
                    </td>
                    <td class="text-right font-mono">Rp {{ number_format($faktur->tarif_per_eks, 0, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold" style="color: #b91c1c;">
                        (Rp {{ number_format($faktur->total_potongan_retur, 0, ',', '.') }})
                    </td>
                </tr>

                <tr style="background: #f8fafc;">
                    <td colspan="2" style="font-weight: 700; text-transform: uppercase;">
                        Total Volume Terjual Bersih (Net Sold Copies)
                    </td>
                    <td class="text-center font-mono font-bold" style="color: #0A72AC;">
                        {{ number_format($faktur->oplah_bersih, 0, ',', '.') }} eks
                    </td>
                    <td colspan="2" class="text-right font-mono text-xs text-slate-500">
                        Oplah Kirim Kotor dikurangi Retur Disetujui
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- 4. RINGKASAN TOTAL TAGIHAN BERSIH & TERBILANG -->
        <div class="total-box">
            <div class="total-grid">
                <div class="terbilang-box">
                    <strong style="color: #0f172a; display: block; margin-bottom: 3px; font-style: normal; font-size: 10px; text-transform: uppercase;">Terbilang:</strong>
                    @php
                        function terbilangFaktur($angka) {
                            $angka = abs((float) $angka);
                            $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
                            $terbilang = '';
                            if ($angka < 12) {
                                $terbilang = ' ' . $baca[$angka];
                            } elseif ($angka < 20) {
                                $terbilang = terbilangFaktur($angka - 10) . ' Belas';
                            } elseif ($angka < 100) {
                                $terbilang = terbilangFaktur(floor($angka / 10)) . ' Puluh' . terbilangFaktur($angka % 10);
                            } elseif ($angka < 200) {
                                $terbilang = ' Seratus' . terbilangFaktur($angka - 100);
                            } elseif ($angka < 1000) {
                                $terbilang = terbilangFaktur(floor($angka / 100)) . ' Ratus' . terbilangFaktur($angka % 100);
                            } elseif ($angka < 2000) {
                                $terbilang = ' Seribu' . terbilangFaktur($angka - 1000);
                            } elseif ($angka < 1000000) {
                                $terbilang = terbilangFaktur(floor($angka / 1000)) . ' Ribu' . terbilangFaktur($angka % 1000);
                            } elseif ($angka < 1000000000) {
                                $terbilang = terbilangFaktur(floor($angka / 1000000)) . ' Juta' . terbilangFaktur($angka % 1000000);
                            } elseif ($angka < 1000000000000) {
                                $terbilang = terbilangFaktur(floor($angka / 1000000000)) . ' Milyar' . terbilangFaktur(fmod($angka, 1000000000));
                            }
                            return $terbilang;
                        }
                    @endphp
                    "{{ trim(terbilangFaktur($faktur->total_tagihan_bersih)) }} Rupiah"
                </div>

                <div class="nominal-summary">
                    <table>
                        <tr>
                            <td>Subtotal Tagihan Kotor</td>
                            <td class="text-right font-mono">Rp {{ number_format($faktur->total_tagihan_kotor, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Potongan Retur Fisik</td>
                            <td class="text-right font-mono" style="color: #b91c1c;">-Rp {{ number_format($faktur->total_potongan_retur, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="grand-total">
                            <td>TOTAL TAGIHAN BERSIH</td>
                            <td class="text-right font-mono">Rp {{ number_format($faktur->total_tagihan_bersih, 0, ',', '.') }}</td>
                        </tr>
                        @if((float)$faktur->total_terbayar > 0)
                        <tr>
                            <td style="color: #0369a1; padding-top: 4px;">Total Kas Disetor / Dibayar</td>
                            <td class="text-right font-mono font-bold" style="color: #0369a1; padding-top: 4px;">Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700;">Sisa Tagihan / Piutang</td>
                            <td class="text-right font-mono font-bold" style="color: #b91c1c;">Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div style="font-size: 10px; color: #64748b; line-height: 1.4; margin-bottom: 20px;">
            <strong>Catatan Pembayaran & Verifikasi:</strong><br>
            1. Pembayaran tagihan dapat disetor tunai di Kasir Redaksi Kabar Priangan atau via transfer Rekening <strong>Bank BJB Cabang Tasikmalaya No. Rek: 001.002.34567.89</strong> a.n. <strong>PT Berkah Pikiran Rakyat</strong>.<br>
            2. Harap cantumkan Nomor Faktur <strong>{{ $faktur->no_faktur }}</strong> pada berita transfer untuk rekonsiliasi kas otomatis.<br>
            3. Pengaduan atau koreksi oplah selambat-lambatnya 7 (tujuh) hari kalender sejak faktur diterbitkan.
        </div>

        <!-- 5. TANDA TANGAN 3 PIHAK OTORISASI -->
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-title">Diterima & Disetujui Oleh,</div>
                <div class="sig-name">{{ $faktur->nama_agen }}</div>
                <div class="sig-sub">Agen / Pelanggan Koran</div>
            </div>

            <div class="sig-block">
                <div class="sig-title">Divisi Sirkulasi & Ekspedisi,</div>
                <div class="sig-name">Koordinator Armada Sirkulasi</div>
                <div class="sig-sub">Verifikasi Oplah Kirim Harian</div>
            </div>

            <div class="sig-block">
                <div class="sig-title">Divisi Akuntansi & Keuangan,</div>
                <div class="sig-name">Asman / Kasie Akuntansi</div>
                <div class="sig-sub">Otorisasi & Pengesahan Faktur</div>
            </div>
        </div>

        <!-- 6. LEMBAR LAMPIRAN: RINCIAN PENGIRIMAN HARIAN SEBULAN PENUH (STATEMENT OF DELIVERY) -->
        <div class="page-break">
            <div class="lampiran-header">
                <div>
                    <h3 style="font-size: 13px; font-weight: 800; color: #0A72AC; text-transform: uppercase;">
                        Lampiran Faktur: Statement of Daily Deliveries
                    </h3>
                    <p style="font-size: 10px; color: #64748b;">
                        Rincian pengiriman koran harian subuh &bull; Periode: {{ \Carbon\Carbon::parse($faktur->periode_bulan . '-01')->translatedFormat('F Y') }} &bull; Ref Faktur: {{ $faktur->no_faktur }}
                    </p>
                </div>
                <div style="text-align: right;">
                    <span class="font-mono" style="font-size: 11px; font-weight: 700; color: #334155;">{{ $faktur->nama_agen }} ({{ $faktur->kode_agen }})</span>
                </div>
            </div>

            <table class="table-lampiran">
                <thead>
                    <tr>
                        <th style="width: 30px;">Tgl</th>
                        <th style="width: 80px;">Hari</th>
                        <th style="width: 140px;">No. Dokumen Logistik</th>
                        <th style="width: 90px;">Oplah Kirim</th>
                        <th>Keterangan Distribusi</th>
                        <th style="width: 110px;">Status Ekspedisi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rincian = $faktur->rincian_harian ?? [];
                        $daysInMonth = \Carbon\Carbon::parse($faktur->periode_bulan . '-01')->daysInMonth;
                    @endphp
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $dateStr = sprintf('%s-%02d', $faktur->periode_bulan, $d);
                            $carbonDate = \Carbon\Carbon::parse($dateStr);
                            $row = $rincian[$d] ?? null;
                            $oplahHarian = $row['oplah'] ?? ($faktur->total_oplah_kirim > 0 ? (int)round($faktur->total_oplah_kirim / $daysInMonth) : 0);
                            $noSJ = $row['no_sj'] ?? sprintf('SJ-%s-%02d', $carbonDate->format('Ymd'), $faktur->agen_id % 99 + 1);
                        @endphp
                        <tr style="{{ $carbonDate->isSunday() ? 'background: #fffbeb;' : '' }}">
                            <td class="text-center font-bold font-mono">{{ sprintf('%02d', $d) }}</td>
                            <td class="text-center">{{ $carbonDate->translatedFormat('l') }}</td>
                            <td class="text-center font-mono">{{ $noSJ }}</td>
                            <td class="text-center font-mono font-bold">{{ number_format($oplahHarian, 0, ',', '.') }} eks</td>
                            <td>Distribusi Edisi Koran {{ $carbonDate->format('d/m/Y') }} - Armada Rute {{ $faktur->agen->rute_kode ?? 'R-01' }}</td>
                            <td class="text-center" style="color: #0369a1; font-weight: 600;">&#10003; Terkirim Fisik</td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: 700;">
                        <td colspan="3" class="text-right" style="padding: 6px 10px; text-transform: uppercase;">
                            Total Akumulasi Distribusi Harian:
                        </td>
                        <td class="text-center font-mono font-bold" style="color: #0A72AC; font-size: 10px;">
                            {{ number_format($faktur->total_oplah_kirim, 0, ',', '.') }} eks
                        </td>
                        <td colspan="2" style="font-size: 9px; color: #64748b; font-style: italic;">
                            * Total akumulasi sinkron dengan Gross Delivery pada muka faktur utama.
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</body>
</html>
