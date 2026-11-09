<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Kredit Retur Sirkulasi - {{ $koreksi->nomor_bukti }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #1e293b; margin: 20px; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0E527B; padding-bottom: 10px; margin-bottom: 15px; }
        .logo-title { display: flex; align-items: center; gap: 12px; }
        .logo-title img { height: 36px; }
        .company-name { font-size: 16px; font-weight: bold; color: #0E527B; }
        .voucher-title { font-size: 15px; font-weight: bold; text-align: right; }
        .voucher-no { font-family: monospace; font-size: 13px; color: #64748b; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 4px 6px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        .data-table th { background: #f8fafc; font-weight: bold; font-size: 11px; }
        .text-right { text-align: right; }
        .signatures { display: flex; justify-content: space-between; margin-top: 40px; text-align: center; }
        .sig-box { width: 28%; }
        .sig-line { border-bottom: 1px solid #1e293b; margin-top: 50px; font-weight: bold; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 6px 14px; background: #0E527B; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak Nota Kredit (CN-SIR)
        </button>
    </div>

    <div class="header">
        <div class="logo-title">
            <img src="{{ asset('kabarpriangan.png') }}" alt="Logo">
            <div>
                <div class="company-name">PT BERKAH PIKIRAN RAKYAT</div>
                <div style="font-size: 10px; color: #64748b;">HARIAN UMUM KABAR PRIANGAN - DIVISI AKUNTANSI</div>
            </div>
        </div>
        <div>
            <div class="voucher-title">NOTA KREDIT RETUR (CN-SIR)</div>
            <div class="voucher-no">No: {{ $koreksi->nomor_bukti }}</div>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 15%;"><strong>Tanggal</strong></td>
            <td style="width: 35%;">: {{ \Carbon\Carbon::parse($koreksi->tanggal)->format('d F Y') }}</td>
            <td style="width: 15%;"><strong>Divisi</strong></td>
            <td style="width: 35%;">: Sirkulasi Koran</td>
        </tr>
        <tr>
            <td><strong>Nama Agen</strong></td>
            <td>: {{ $koreksi->nama_entitas }}</td>
            <td><strong>Akun Debet</strong></td>
            <td>: {{ $koreksi->kode_akun_debet }} - Retur & Koreksi Sirkulasi (CN-SIR)</td>
        </tr>
        <tr>
            <td><strong>Wilayah/Rute</strong></td>
            <td>: {{ $koreksi->alamat ?: '-' }}</td>
            <td><strong>Akun Kredit</strong></td>
            <td>: {{ $koreksi->kode_akun_kredit }} - Piutang Usaha Koran Sirkulasi</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">No</th>
                <th>Keterangan / Rincian Retur Eksemplar</th>
                <th style="width: 160px; text-align: right;">Jumlah Nilai Retur</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>{{ $koreksi->keterangan }}</td>
                <td class="text-right" style="font-weight: bold; font-family: monospace;">
                    Rp {{ number_format($koreksi->nominal_koreksi, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL PENGURANG PIUTANG</th>
                <th class="text-right" style="font-family: monospace; font-size: 13px;">
                    Rp {{ number_format($koreksi->nominal_koreksi, 0, ',', '.') }}
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <div>Dibuat Oleh,</div>
            <div class="sig-line">{{ auth()->user()->name ?? 'Staf Akuntansi' }}</div>
            <div style="font-size: 10px; color: #64748b;">Accounting</div>
        </div>
        <div class="sig-box">
            <div>Petugas Retur / Sirkulasi,</div>
            <div class="sig-line">( .................................................. )</div>
            <div style="font-size: 10px; color: #64748b;">Sirkulasi & Distribusi</div>
        </div>
        <div class="sig-box">
            <div>Disetujui Oleh,</div>
            <div class="sig-line">Manager Keuangan</div>
            <div style="font-size: 10px; color: #64748b;">Finance & Accounting Manager</div>
        </div>
    </div>
</body>
</html>
