<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Transaksi Iklan - {{ $transaksi->nofakturkoran }}</title>
    <style>
        @page {
            size: auto;
            margin: 8mm 12mm;
        }

        body {
            font-family: 'Courier New', Courier, monospace, 'Lucida Console';
            font-size: 11pt;
            line-height: 1.35;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px 10px;
        }

        .screen-actions {
            max-width: 860px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-print {
            background: #0284c7;
            color: white;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-print:hover { background: #0369a1; }

        .btn-back {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover { background: #f8fafc; }

        /* Continuous Form Paper Simulation */
        .paper-container {
            max-width: 860px;
            margin: 0 auto;
            background: #fff;
            padding: 24px 32px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            position: relative;
            box-sizing: border-box;
        }

        /* Decorative tractor holes for continuous form aesthetic (screen only) */
        .paper-container::before, .paper-container::after {
            content: "";
            position: absolute;
            top: 15px;
            bottom: 15px;
            width: 14px;
            background-image: radial-gradient(circle, #94a3b8 35%, transparent 40%);
            background-size: 14px 20px;
            opacity: 0.35;
        }
        .paper-container::before { left: 8px; }
        .paper-container::after { right: 8px; }

        .paper-content {
            margin: 0 16px;
        }

        /* Header / Kop */
        .kop-header {
            text-align: center;
            padding-bottom: 6px;
        }

        .kop-header h1 {
            margin: 0;
            font-size: 15pt;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .kop-header p {
            margin: 2px 0;
            font-size: 8.5pt;
            letter-spacing: 0.2px;
        }

        .kop-header .divisi-badge {
            font-size: 9pt;
            font-weight: bold;
            display: inline-block;
            margin-top: 3px;
            padding: 1px 8px;
            border: 1px solid #000;
        }

        .dashed-line {
            border-bottom: 1.5px dashed #000;
            margin: 8px 0;
        }

        /* Grid Data (2 Kolom) */
        .grid-two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1.5px 0;
            vertical-align: top;
            font-size: 9.5pt;
        }

        .info-table td.lbl {
            width: 110px;
            white-space: nowrap;
        }

        .info-table td.colon {
            width: 14px;
            text-align: center;
            font-weight: bold;
        }

        .info-table td.val {
            font-weight: 600;
        }

        /* Spesifikasi & Keuangan Table */
        .finance-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Courier New', Courier, monospace;
        }

        .finance-table td {
            padding: 1.5px 0;
            font-size: 9.5pt;
        }

        .finance-table td.f-lbl {
            width: 130px;
            white-space: nowrap;
        }

        .finance-table td.f-colon {
            width: 14px;
            text-align: center;
            font-weight: bold;
        }

        .finance-table td.f-curr {
            width: 25px;
            text-align: left;
        }

        .finance-table td.f-val {
            text-align: right;
            font-weight: bold;
            white-space: nowrap;
        }

        .row-total td {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            font-weight: 900 !important;
            padding: 4px 0 !important;
            font-size: 10pt !important;
        }

        /* Signatures & Stempel */
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            position: relative;
        }

        .signature-box .date-loc {
            text-align: center;
            font-size: 9.5pt;
            margin-bottom: 4px;
        }

        .signature-box .role-title {
            font-size: 9.5pt;
            font-weight: bold;
            margin-bottom: 50px;
        }

        .signature-box .person-name {
            font-size: 9.5pt;
            font-weight: bold;
            text-decoration: underline;
        }


        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .screen-actions { display: none !important; }
            .paper-container {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .paper-container::before, .paper-container::after { display: none; }
            .paper-content { margin: 0; }
        }
    </style>
</head>
<body>

    <!-- Bar Aksi Layar -->
    <div class="screen-actions">
        <a href="{{ route('transaksikoran.index') }}" class="btn-action btn-back">
            <span>&larr;</span> Kembali ke Daftar Transaksi
        </a>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <span>🖨️</span> Cetak Order Transaksi
            </button>
        </div>
    </div>

    @php
        $qty = max(1, (int) ($transaksi->total_muatkoran ?? 1));
        $tarifSatuan = (int) ($transaksi->harga_transaksikoran ?? 0);
        $brutto = (int) ($transaksi->nilai_brutto ?? ($tarifSatuan * $qty));
        $diskon = (int) ($transaksi->nilai_diskon ?? ($transaksi->diskon_transaksikoran ?? 0));
        $persenDiskon = (float) ($transaksi->diskon_persen ?? ($brutto > 0 && $diskon > 0 ? round(($diskon / $brutto) * 100, 2) : 0));
        $netto = max(0, $brutto - $diskon);
        $ppnPersen = $transaksi->ppn_persen ?: \App\Models\SistemConfig::getPpnPersen();
        $ppn = (int) ($transaksi->nilai_ppn ?? round($netto * ($ppnPersen / 100)));
        $totalTagihan = (int) ($transaksi->totaltagihan_transaksikoran ?? ($netto + $ppn));
        $bayar = (int) ($transaksi->jumlahbayar_transaksikoran ?? 0);
        $piutang = max(0, $totalTagihan - $bayar);
        $isLunas = ($piutang <= 0 && $totalTagihan > 0);

        // Multi-Date Formatter
        $datesList = $transaksi->tanggal_muat_list;
        if (is_string($datesList)) {
            $datesList = array_filter(array_map('trim', explode(',', $datesList)));
        } elseif (!is_array($datesList)) {
            $datesList = [$transaksi->tanggal_muatkoran];
        }
        $formattedDates = array_map(function($d) {
            return \Carbon\Carbon::parse($d)->translatedFormat('d-m-Y');
        }, $datesList);
        $datesString = implode(', ', $formattedDates);
    @endphp

    <!-- Lembar Dokumen Kertas Continuous Form -->
    <div class="paper-container">
        <div class="paper-content">

            <!-- 1. KOP SURAT HU. PRIANGAN -->
            <div class="kop-header">
                <h1>ORDER TRANSAKSI IKLAN HU. PRIANGAN</h1>
                <p>JL. DINDINGARI RAYA NO.12 TLP. (0265)-335300, FAX. 335677 PERUM BUMI RESIK PANGLAYUNGAN TASIKMALAYA 46134</p>
                <div class="divisi-badge">DIVISI IKLAN CETAK (HU. KABAR PRIANGAN)</div>
            </div>

            <div class="dashed-line"></div>

            <!-- 2. DATA IDENTITAS PEMASANG & DOKUMEN (2 KOLOM) -->
            <div class="grid-two-col">
                <!-- Kolom Kiri: Pemasang -->
                <table class="info-table">
                    <tr>
                        <td class="lbl">Identitas</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $transaksi->pelanggan?->kode_pelanggan ?: ($transaksi->pelanggan?->npwp_nik ?: '1.00.04.01.01.' . str_pad($transaksi->pelanggan_id ?? 1, 5, '0', STR_PAD_LEFT)) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Nama</td>
                        <td class="colon">:</td>
                        <td class="val">{{ strtoupper($transaksi->nama_pemasangkoran) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Alamat</td>
                        <td class="colon">:</td>
                        <td class="val">{{ strtoupper($transaksi->alamat_pemasangkoran ?: '-') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Kota / Telp</td>
                        <td class="colon">:</td>
                        <td class="val">{{ strtoupper($transaksi->pelanggan?->kota ?: 'TASIKMALAYA') }} {{ $transaksi->pelanggan?->no_telp ? '· ' . $transaksi->pelanggan->no_telp : '' }}</td>
                    </tr>
                    @if($transaksi->nama_pic)
                    <tr>
                        <td class="lbl">Nama PIC</td>
                        <td class="colon">:</td>
                        <td class="val">{{ strtoupper($transaksi->nama_pic) }}</td>
                    </tr>
                    @endif
                </table>

                <!-- Kolom Kanan: Order Dokumen -->
                <table class="info-table">
                    <tr>
                        <td class="lbl">No. Order</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $transaksi->id ? (2026100000 + $transaksi->id) : $transaksi->nofakturkoran }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">No. Faktur</td>
                        <td class="colon">:</td>
                        <td class="val">{{ $transaksi->nofakturkoran }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Sales / AE</td>
                        <td class="colon">:</td>
                        <td class="val">{{ strtoupper($transaksi->salesAe?->nama_ae ?: ($transaksi->sales_iklankoran ?: 'FO / FRONT OFFICE')) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Jenis Iklan</td>
                        <td class="colon">:</td>
                        <td class="val">{{ strtoupper($transaksi->kategori_form . ' - ' . ($transaksi->iklankoran->nama_jenis ?? 'IKLAN KORAN')) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Tanggal Muat</td>
                        <td class="colon">:</td>
                        <td class="val">
                            @if(count($datesList) > 1)
                                {{ $datesString }} ({{ count($datesList) }}x Terbit)
                            @else
                                {{ \Carbon\Carbon::parse($transaksi->tanggal_muatkoran)->translatedFormat('d-m-Y') }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="dashed-line"></div>

            <!-- 3. DETAIL SPESIFIKASI MEDIA & KEUANGAN (2 KOLOM) -->
            <div class="grid-two-col">
                <!-- Kolom Kiri: Spesifikasi Media & Materi -->
                <div>
                    <table class="info-table">
                        <tr>
                            <td class="lbl">Kop Iklan</td>
                            <td class="colon">:</td>
                            <td class="val">{{ $transaksi->kopIklan->nama_kop ?? ($transaksi->produk_jasa ?: '-') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Kop Kota / Hal</td>
                            <td class="colon">:</td>
                            <td class="val">{{ $transaksi->kopKota->nama_penempatan ?? ($transaksi->halaman_iklan ?: 'HALAMAN DALAM') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Warna / Format</td>
                            <td class="colon">:</td>
                            <td class="val">{{ $transaksi->warna_iklan ?: 'BW (Hitam Putih)' }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Ukuran</td>
                            <td class="colon">:</td>
                            <td class="val">
                                @if($transaksi->kategori_form === 'BARIS')
                                    {{ $transaksi->jumlah_baris ?: 2 }} Baris
                                @else
                                    {{ $transaksi->lebar_kolom ?? 1 }} kol &times; {{ $transaksi->tinggi_mm ?? 1 }} mm ({{ ($transaksi->lebar_kolom ?? 1) * ($transaksi->tinggi_mm ?? 1) }} mmk)
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="lbl">Tarif Satuan</td>
                            <td class="colon">:</td>
                            <td class="val">Rp {{ number_format($tarifSatuan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Materi Iklan</td>
                            <td class="colon">:</td>
                            <td class="val" style="word-break: break-word;">
                                @if($transaksi->kategori_form === 'BARIS')
                                    {{ $transaksi->naskah_iklan_baris ?: '-' }}
                                @else
                                    {{ $transaksi->keterangan_materi ?: ($transaksi->produk_jasa ?: 'Materi display terlampir') }}
                                @endif
                            </td>
                        </tr>
                        @if($transaksi->file_materi)
                        <tr>
                            <td class="lbl">File Materi</td>
                            <td class="colon">:</td>
                            <td class="val" style="font-size: 8.5pt;">{{ basename($transaksi->file_materi) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <!-- Kolom Kanan: Tabel Keuangan Transparan -->
                <div>
                    <table class="finance-table">
                        <tr>
                            <td class="f-lbl">Brutto</td>
                            <td class="f-colon">:</td>
                            <td class="f-curr">Rp</td>
                            <td class="f-val">{{ number_format($brutto, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="f-lbl">Diskon {{ $persenDiskon > 0 ? $persenDiskon . ' %' : '' }}</td>
                            <td class="f-colon">:</td>
                            <td class="f-curr">Rp</td>
                            <td class="f-val">{{ number_format($diskon, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="f-lbl">Netto (DPP)</td>
                            <td class="f-colon">:</td>
                            <td class="f-curr">Rp</td>
                            <td class="f-val">{{ number_format($netto, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="f-lbl">PPn {{ $ppnPersen }} %</td>
                            <td class="f-colon">:</td>
                            <td class="f-curr">Rp</td>
                            <td class="f-val">{{ number_format($ppn, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="f-lbl">Materai</td>
                            <td class="f-colon">:</td>
                            <td class="f-curr">Rp</td>
                            <td class="f-val">0,00</td>
                        </tr>
                        <tr class="row-total">
                            <td class="f-lbl">TOTAL TAGIHAN</td>
                            <td class="f-colon">:</td>
                            <td class="f-curr">Rp</td>
                            <td class="f-val">{{ number_format($totalTagihan, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="f-lbl">Jumlah Dibayar</td>
                            <td class="f-colon">:</td>
                            <td class="f-curr">Rp</td>
                            <td class="f-val">{{ number_format($bayar, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="f-lbl">Sisa Piutang</td>
                            <td class="f-colon">:</td>
                            <td class="f-curr">Rp</td>
                            <td class="f-val">{{ number_format($piutang, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="dashed-line"></div>

            <!-- 4. TANDA TANGAN LEGALITAS & CAP BASAH (PERSIS FOTO 2) -->
            <div class="signature-grid">
                <!-- Kolom Pemasang -->
                <div class="signature-box" style="padding-top: 18px;">
                    <div class="role-title">Pemasang / Instansi,</div>
                    <div class="person-name">( {{ strtoupper($transaksi->nama_pic ?: $transaksi->nama_pemasangkoran) }} )</div>
                </div>

                <!-- Kolom Front Office dengan Stempel Cap Bulat Otomatis -->
                <div class="signature-box">
                    <div class="date-loc">Tasikmalaya, {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksikoran)->translatedFormat('d-m-Y') }}</div>
                    <div class="role-title">Front Office,</div>


                    <div class="person-name">( {{ strtoupper($transaksi->inputOleh?->name ?: 'FRONT OFFICE') }} )</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
