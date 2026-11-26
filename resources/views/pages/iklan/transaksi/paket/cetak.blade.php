<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Paket Iklan - {{ $paket->nofaktur_paket }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            color: #000;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
        }
        .no-print-bar {
            max-width: 820px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-back {
            background: #fff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-print {
            background: #005792;
            color: #fff;
            border: none;
        }
        .paper {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            border: 1.5px solid #000;
            padding: 22px 26px;
            box-sizing: border-box;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .comp-title {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .comp-sub {
            font-size: 9pt;
            margin-top: 2px;
        }
        .doc-box {
            text-align: right;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .doc-no {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 3px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.info-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 9.5pt;
        }
        table.info-table td.lbl {
            width: 115px;
            font-weight: bold;
        }
        table.info-table td.colon {
            width: 12px;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 14px;
        }
        table.items-table th,
        table.items-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 9.5pt;
            vertical-align: top;
        }
        table.items-table th {
            background: #f8fafc;
            text-align: left;
            font-weight: bold;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 16px;
            align-items: start;
            margin-top: 10px;
        }
        .qr-box {
            border: 1px dashed #000;
            padding: 8px 10px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .sign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            text-align: center;
            margin-top: 16px;
            font-size: 9.5pt;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print-bar { display: none !important; }
            .paper { border: 1.5px solid #000; max-width: 100%; }
        }
    </style>
</head>
<body>
    @php
        \Carbon\Carbon::setLocale('id');
        $tot = (int) $paket->total_tagihan;
        $byr = (int) ($paket->jumlah_bayar ?? 0);
        $isTunai = ($paket->jenis_pembayaran === 'Tunai' || ($tot > 0 && $byr >= $tot));
        $noPerkiraan = \App\Http\Controllers\LaporanController::resolveNoPerkiraan('koran', $tot, $byr, (int) ($paket->pelanggan_id ?? $paket->id), $paket->jenis_pembayaran ?? null);
    @endphp

    <div class="no-print-bar">
        <a href="{{ route('transaksipaket.show', $paket->id) }}" class="btn-action btn-back">
            &larr; Kembali ke Detail Paket
        </a>
        <button type="button" onclick="window.print()" class="btn-action btn-print">
            Cetak Faktur Paket (1 Dokumen Terpadu)
        </button>
    </div>

    <div class="paper">
        <!-- Header Dokumen -->
        <div class="header-row">
            <div>
                <div class="comp-title">HARIAN UMUM KABAR PRIANGAN</div>
                <div class="comp-sub">DIVISI IKLAN CETAK, ONLINE &amp; PRIANGAN TV</div>
                <div class="comp-sub">Jl. Dr. Sukardjo No. 70 Tasikmalaya</div>
            </div>
            <div class="doc-box">
                <div class="doc-title">FAKTUR PAKET IKLAN LINTAS MEDIA</div>
                <div class="doc-no">NO: {{ $paket->nofaktur_paket }}</div>
                <div class="comp-sub">No. Perkiraan: {{ $noPerkiraan }}</div>
            </div>
        </div>

        <!-- Informasi Pemasang & Order -->
        <div class="info-grid">
            <table class="info-table">
                <tr>
                    <td class="lbl">Pemasang</td>
                    <td class="colon">:</td>
                    <td><strong>{{ strtoupper($paket->nama_pemasang) }}</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Alamat</td>
                    <td class="colon">:</td>
                    <td>{{ $paket->alamat_pemasang ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="lbl">PIC / Telp</td>
                    <td class="colon">:</td>
                    <td>{{ $paket->nama_pic ?: '-' }} {{ $paket->telepon_pemasang ? '/ ' . $paket->telepon_pemasang : '' }}</td>
                </tr>
            </table>

            <table class="info-table">
                <tr>
                    <td class="lbl">Tanggal Order</td>
                    <td class="colon">:</td>
                    <td>{{ \Carbon\Carbon::parse($paket->tanggal_transaksi)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td class="lbl">Nama Paket</td>
                    <td class="colon">:</td>
                    <td><strong>{{ strtoupper($paket->nama_paket ?: 'PAKET LINTAS MEDIA') }}</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Sales / AE</td>
                    <td class="colon">:</td>
                    <td>{{ strtoupper($paket->salesAe?->nama_ae ?: ($paket->sales_nama ?: 'FRONT OFFICE')) }}</td>
                </tr>
                <tr>
                    <td class="lbl">Pembayaran</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $isTunai ? 'TUNAI' : 'KREDIT' }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Tabel Rincian Saluran Media dalam 1 Faktur Paket -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 32px;" class="text-center">NO</th>
                    <th style="width: 120px;">SALURAN MEDIA</th>
                    <th>SPESIFIKASI &amp; JADWAL TERBIT / TAYANG</th>
                    <th style="width: 75px;" class="text-center">FREK</th>
                    <th style="width: 130px;" class="text-right">SUBTOTAL BRUTTO</th>
                </tr>
            </thead>
            <tbody>
                @php $noUrut = 1; @endphp
                @if($paket->transaksiCetak)
                    @php
                        $ctk = $paket->transaksiCetak;
                        $datesCtk = $ctk->jadwalMuat->pluck('tanggal_muat')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m/Y'))->all();
                        $bruttoCtk = (int) $ctk->harga_transaksikoran * max(1, (int) $ctk->total_muatkoran);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $noUrut++ }}</td>
                        <td>
                            <strong>IKLAN CETAK</strong><br>
                            <span style="font-size: 8.5pt;">Ref: {{ $ctk->nofakturkoran }}</span>
                        </td>
                        <td>
                            <strong>{{ $ctk->kategori_form }} - {{ $ctk->iklankoran?->nama_jenis ?: 'Reguler' }}</strong><br>
                            Ukuran: {{ $ctk->ukuran_iklan }} ({{ $ctk->warna_iklan }}) &bull; {{ $ctk->halaman_iklan }}<br>
                            Jadwal Terbit: {{ implode(', ', $datesCtk) }}
                            @if(count($datesCtk) > 1)
                                <br><em>(1x Muat Bernilai + {{ count($datesCtk) - 1 }}x Muat Bonus)</em>
                            @endif
                        </td>
                        <td class="text-center">{{ $ctk->total_muatkoran }}x</td>
                        <td class="text-right">Rp {{ number_format($bruttoCtk, 0, ',', '.') }}</td>
                    </tr>
                @endif

                @if($paket->transaksiOnline)
                    @php
                        $onl = $paket->transaksiOnline;
                        $bruttoOnl = (int) $onl->harga_transaksionline;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $noUrut++ }}</td>
                        <td>
                            <strong>IKLAN ONLINE</strong><br>
                            <span style="font-size: 8.5pt;">Ref: {{ $onl->nofakturonline }}</span>
                        </td>
                        <td>
                            <strong>{{ $onl->iklanonline?->nama_jenis ?: 'Publikasi Online' }}</strong><br>
                            Materi: {{ $onl->materi_iklan }}<br>
                            Portal: {{ $onl->portal_iklanonline ?: '-' }}<br>
                            Jadwal Tayang: {{ $onl->tanggal_muat_list ?: \Carbon\Carbon::parse($onl->tanggal_muatiklanonline)->format('Y-m-d') }}
                        </td>
                        <td class="text-center">{{ $onl->total_muatiklanonline }}x</td>
                        <td class="text-right">Rp {{ number_format($bruttoOnl, 0, ',', '.') }}</td>
                    </tr>
                @endif

                @if($paket->transaksiTv)
                    @php
                        $ptv = $paket->transaksiTv;
                        $bruttoPtv = (int) $ptv->harga_transaksipriangan * max(1, (int) $ptv->total_muatiklanpriangan);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $noUrut++ }}</td>
                        <td>
                            <strong>IKLAN TV</strong><br>
                            <span style="font-size: 8.5pt;">Ref: {{ $ptv->nofakturpriangan }}</span>
                        </td>
                        <td>
                            <strong>{{ $ptv->iklanpriangan?->nama_jenis ?: 'Program Priangan TV' }}</strong><br>
                            Materi: {{ $ptv->materi_iklan }} ({{ $ptv->durasi_detik }} dtk • {{ $ptv->jam_tayang }})<br>
                            Jadwal Siar: {{ $ptv->tanggal_muat_list ?: \Carbon\Carbon::parse($ptv->tanggal_muatiklanpriangan)->format('Y-m-d') }}
                        </td>
                        <td class="text-center">{{ $ptv->total_muatiklanpriangan }}x</td>
                        <td class="text-right">Rp {{ number_format($bruttoPtv, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Ringkasan Biaya & QR Kasir -->
        <div class="footer-grid">
            <div>
                @if($qrFaktur && $qrSvg)
                    <div class="qr-box">
                        <div>{!! $qrSvg !!}</div>
                        <div style="font-size: 8.5pt; line-height: 1.35;">
                            <strong>QR VERIFIKASI KASIR</strong><br>
                            Faktur: {{ $paket->nofaktur_paket }}<br>
                            Kode Manual: <strong>{{ $qrFaktur->kode_manual }}</strong><br>
                            Tunjukkan QR ini ke Loket Kasir.
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <table class="info-table">
                    <tr>
                        <td class="lbl">Total Brutto</td>
                        <td class="colon">:</td>
                        <td class="text-right">Rp {{ number_format($paket->nilai_brutto, 0, ',', '.') }}</td>
                    </tr>
                    @if($paket->nilai_diskon > 0)
                        <tr>
                            <td class="lbl">Diskon Paket</td>
                            <td class="colon">:</td>
                            <td class="text-right">- Rp {{ number_format($paket->nilai_diskon, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="lbl">Netto (DPP)</td>
                        <td class="colon">:</td>
                        <td class="text-right">Rp {{ number_format($paket->nilai_netto, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">PPN ({{ $paket->ppn_persen }}%)</td>
                        <td class="colon">:</td>
                        <td class="text-right">Rp {{ number_format($paket->nilai_ppn, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top: 1.5px solid #000; border-bottom: 1.5px solid #000;">
                        <td class="lbl" style="padding: 4px 0;">TOTAL TAGIHAN</td>
                        <td class="colon" style="padding: 4px 0;">:</td>
                        <td class="text-right" style="padding: 4px 0;"><strong>Rp {{ number_format($paket->total_tagihan, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="sign-grid">
            <div>
                Pemasang / Klien,<br><br><br><br>
                ( {{ strtoupper($paket->nama_pic ?: $paket->nama_pemasang) }} )
            </div>
            <div>
                Tasikmalaya, {{ \Carbon\Carbon::parse($paket->tanggal_transaksi)->format('d-m-Y') }}<br>
                Bagian Iklan HU. Kabar Priangan,<br><br><br>
                ( {{ strtoupper($paket->inputOleh->name ?? 'STAF DIVISI IKLAN') }} )
            </div>
        </div>
    </div>
</body>
</html>

