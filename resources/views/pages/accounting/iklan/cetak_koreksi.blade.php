<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Kredit - {{ $koreksi->no_bukti }}</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif; font-size: 12px; color: #1e293b; margin: 25px; line-height: 1.4; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0A72AC; padding-bottom: 12px; margin-bottom: 15px; }
        .logo-title { display: flex; align-items: center; gap: 12px; }
        .logo-title img { height: 38px; }
        .company-name { font-size: 15px; font-weight: bold; color: #0A72AC; letter-spacing: 0.5px; }
        .voucher-title { font-size: 15px; font-weight: bold; text-align: right; color: #0f172a; }
        .voucher-no { font-family: monospace; font-size: 13px; color: #64748b; font-weight: bold; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 11px; }
        .info-table td { padding: 4px 6px; vertical-align: top; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
        .data-table th, .data-table td { border: 1px solid #cbd5e1; padding: 7px 10px; text-align: left; }
        .data-table th { background: #f8fafc; font-weight: bold; font-size: 11px; color: #334155; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .signatures { display: flex; justify-content: space-between; margin-top: 35px; text-align: center; page-break-inside: avoid; }
        .sig-box { width: 23%; }
        .sig-line { border-bottom: 1px solid #1e293b; margin-top: 55px; font-weight: bold; }
        .sig-role { font-size: 10px; color: #64748b; margin-top: 3px; }
        @media print {
            .no-print { display: none; }
            body { margin: 10mm; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 7px 16px; background: #0A72AC; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px;">
            🖨️ Cetak Nota Kredit (CN)
        </button>
    </div>

    <!-- Header Dokumen -->
    <div class="header">
        <div class="logo-title">
            <img src="{{ asset('kabarpriangan.png') }}" alt="Logo">
            <div>
                <div class="company-name">PT BERKAH PIKIRAN RAKYAT</div>
                <div style="font-size: 10px; color: #64748b; font-weight: 600;">HARIAN UMUM KABAR PRIANGAN &bull; DIVISI AKUNTANSI & KEUANGAN</div>
            </div>
        </div>
        <div>
            <div class="voucher-title">NOTA KREDIT (CREDIT NOTE)</div>
            <div class="voucher-no">No: {{ $koreksi->no_bukti }}</div>
        </div>
    </div>

    <!-- Informasi Entitas & Referensi Faktur -->
    <table class="info-table">
        <tr>
            <td style="width: 16%;"><strong>Tanggal Dokumen</strong></td>
            <td style="width: 34%;">: {{ \Carbon\Carbon::parse($koreksi->tanggal_koreksi)->translatedFormat('d F Y') }}</td>
            <td style="width: 16%;"><strong>Saluran Media</strong></td>
            <td style="width: 34%;">: <span class="badge">{{ $koreksi->media_label }}</span></td>
        </tr>
        <tr>
            <td><strong>No. Faktur Ref</strong></td>
            <td>: <strong class="font-mono">{{ $koreksi->no_faktur }}</strong></td>
            <td><strong>Kategori Alasan</strong></td>
            <td>: <strong>{{ $koreksi->kategori_alasan_label }}</strong></td>
        </tr>
        <tr>
            <td><strong>Pemasang / Klien</strong></td>
            <td>: <strong>{{ $koreksi->nama_entitas ?: '-' }}</strong></td>
            <td><strong>Jurnal Posting GL</strong></td>
            <td>: <span class="font-mono">{{ $koreksi->jurnal->no_jurnal ?? 'Auto Posted GL' }}</span></td>
        </tr>
        <tr>
            <td><strong>Alamat Klien</strong></td>
            <td>: {{ $koreksi->alamat ?: 'Priangan Timur' }}</td>
            <td><strong>Nilai Faktur Awal</strong></td>
            <td>: <span class="font-mono">Rp {{ number_format($koreksi->nilai_penjualan, 0, ',', '.') }}</span></td>
        </tr>
    </table>

    <!-- Rincian Alokasi Pembalikan Piutang & PPN -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th style="width: 150px;">Kode Akun (COA)</th>
                <th>Keterangan Transaksi & Kronologi</th>
                <th style="width: 80px;" class="text-center">Posisi</th>
                <th style="width: 140px;" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $dppVal = (float) ($koreksi->dpp_koreksi > 0 ? $koreksi->dpp_koreksi : ($koreksi->jumlah_koreksi - $koreksi->ppn_koreksi));
                $ppnVal = (float) $koreksi->ppn_koreksi;
            @endphp
            <tr>
                <td class="text-center">1</td>
                <td class="font-mono">4.00.05.01.01.00.00001</td>
                <td>
                    <strong>Koreksi & Diskon Penjualan Iklan (DPP)</strong><br>
                    <span style="color: #64748b; font-size: 10px;">{{ $koreksi->keterangan }}</span>
                </td>
                <td class="text-center">DEBET</td>
                <td class="text-right font-mono">Rp {{ number_format($dppVal, 0, ',', '.') }}</td>
            </tr>
            @if($ppnVal > 0)
                <tr>
                    <td class="text-center">2</td>
                    <td class="font-mono">2.00.03.01.01.00.00001</td>
                    <td>
                        <strong>Pembalikan Utang Pajak PPN Keluaran</strong><br>
                        <span style="color: #64748b; font-size: 10px;">Proporsi PPN faktur asal yang disesuaikan</span>
                    </td>
                    <td class="text-center">DEBET</td>
                    <td class="text-right font-mono">Rp {{ number_format($ppnVal, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td class="text-center">{{ $ppnVal > 0 ? 3 : 2 }}</td>
                <td class="font-mono">{{ $koreksi->kode_perkiraan ?: '1.00.04.01.01.01.00001' }}</td>
                <td>
                    <strong>Piutang Usaha Iklan Umum</strong><br>
                    <span style="color: #64748b; font-size: 10px;">Pengurangan saldo piutang faktur {{ $koreksi->no_faktur }}</span>
                </td>
                <td class="text-center">KREDIT</td>
                <td class="text-right font-mono">Rp {{ number_format($koreksi->jumlah_koreksi, 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL NOTA KREDIT (PENGURANG PIUTANG)</td>
                <td class="text-right font-mono" style="font-size: 12px; color: #0f172a;">
                    Rp {{ number_format($koreksi->jumlah_koreksi, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-right" style="color: #64748b;">SISA SALDO PIUTANG FAKTUR SETELAH KOREKSI</td>
                <td class="text-right font-mono font-bold" style="color: {{ $koreksi->saldo_setelah_koreksi <= 0 ? '#15803d' : '#0f172a' }};">
                    @if($koreksi->saldo_setelah_koreksi <= 0)
                        LUNAS (Rp 0)
                    @else
                        Rp {{ number_format($koreksi->saldo_setelah_koreksi, 0, ',', '.') }}
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan Otorisasi Perusahaan Pers -->
    <div class="signatures">
        <div class="sig-box">
            <div>Dibuat Oleh,</div>
            <div class="sig-line">{{ $koreksi->user->name ?? 'Staf Akuntansi' }}</div>
            <div class="sig-role">Staf Akuntansi & GL</div>
        </div>
        <div class="sig-box">
            <div>Diperiksa Oleh,</div>
            <div class="sig-line">Kabag Akuntansi & Pajak</div>
            <div class="sig-role">Supervisor Akuntansi</div>
        </div>
        <div class="sig-box">
            <div>Disetujui Oleh,</div>
            <div class="sig-line">Manager Keuangan</div>
            <div class="sig-role">Finance & Accounting Manager</div>
        </div>
        <div class="sig-box">
            <div>Mengetahui / Menerima,</div>
            <div class="sig-line">{{ $koreksi->nama_entitas ?: 'Pemasang / Sales' }}</div>
            <div class="sig-role">Klien / Account Executive</div>
        </div>
    </div>
</body>
</html>
