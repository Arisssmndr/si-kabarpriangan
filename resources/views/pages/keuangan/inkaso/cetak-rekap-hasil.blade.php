<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Hasil Penugasan Inkaso - HU Kabar Priangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            margin: 3px 0 0;
            font-size: 13px;
            font-weight: normal;
        }
        .header p {
            margin: 3px 0 0;
            font-size: 10px;
            color: #475569;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #94a3b8;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
        }
        .badge-janji { background-color: #fef3c7; color: #92400e; }
        .badge-cicil { background-color: #dbeafe; color: #1e40af; }
        .badge-lunas { background-color: #dcfce7; color: #166534; }
        .badge-belum { background-color: #f1f5f9; color: #475569; }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .sign-box {
            width: 28%;
            text-align: center;
        }
        .sign-line {
            margin-top: 55px;
            border-bottom: 1px solid #334155;
            font-weight: bold;
        }
        @media print {
            body { padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 16px; background-color: #0284c7; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Cetak Dokumen
        </button>
    </div>

    <div class="header">
        <h1>HARIAN UMUM KABAR PRIANGAN</h1>
        <h2>REKAPITULASI HASIL PENUGASAN LAPANGAN (INKASO)</h2>
        <p>Jl. Dr. Sukardjo No. 51 Kota Tasikmalaya - Telp: (0265) 331234 | Bagian Penagihan &amp; Piutang Keuangan</p>
    </div>

    <div class="meta">
        <div>
            <strong>Periode Laporan:</strong> {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}<br>
            <strong>Petugas Inkaso:</strong> {{ $petugasNama }}
        </div>
        <div class="text-right">
            <strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB<br>
            <strong>Total Catatan:</strong> {{ $laporanList->count() }} Data
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th>No. Tugas &amp; Tgl</th>
                <th>Petugas Inkaso</th>
                <th>Klien / Agen &amp; Kontak</th>
                <th>No. Faktur</th>
                <th class="text-right">Sisa Tagihan</th>
                <th class="text-center">Hasil Kunjungan</th>
                <th class="text-right">Setoran Kas</th>
                <th>Catatan / Keterangan Lapangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanList as $idx => $t)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong class="font-mono">{{ $t->no_tugas }}</strong><br>
                        <span style="color: #64748b; font-size: 10px;">Tugas: {{ $t->tanggal_tugas->format('d/m/Y') }}</span><br>
                        <span style="color: #0284c7; font-size: 10px;">Lapor: {{ $t->tanggal_lapor ? $t->tanggal_lapor->format('d/m/Y') : '-' }}</span>
                    </td>
                    <td>
                        <strong>{{ $t->petugas->name ?? '-' }}</strong>
                    </td>
                    <td>
                        <strong>{{ $t->faktur->customer->nama_customer ?? '-' }}</strong><br>
                        <span style="font-size: 10px; color: #64748b;">{{ $t->faktur->customer->kota ?? '-' }} ({{ $t->faktur->customer->no_telepon ?? '-' }})</span>
                    </td>
                    <td class="font-mono">
                        {{ $t->faktur->no_faktur ?? '-' }}
                    </td>
                    <td class="text-right font-mono" style="font-weight: bold; color: #dc2626;">
                        Rp {{ number_format($t->faktur->sisa_piutang ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if($t->hasil_kunjungan === 'janji_bayar')
                            <span class="badge badge-janji">
                                Janji: {{ $t->tanggal_janji_bayar ? $t->tanggal_janji_bayar->format('d/m/Y') : '-' }}
                            </span>
                        @elseif($t->hasil_kunjungan === 'cicilan')
                            <span class="badge badge-cicil">
                                Cicilan
                            </span>
                        @elseif($t->hasil_kunjungan === 'lunas')
                            <span class="badge badge-lunas">
                                Lunas Penuh
                            </span>
                        @else
                            <span class="badge badge-belum">
                                {{ ucfirst(str_replace('_', ' ', $t->hasil_kunjungan ?? 'Belum Bayar')) }}
                            </span>
                        @endif
                    </td>
                    <td class="text-right font-mono" style="font-weight: bold; color: #16a34a;">
                        Rp {{ number_format($t->nominal_diterima ?? 0, 0, ',', '.') }}
                    </td>
                    <td style="font-size: 10px;">
                        {{ $t->keterangan_hasil ?: ($t->catatan ?: '-') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada riwayat laporan hasil penugasan pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($laporanList->count() > 0)
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="7" class="text-right" style="padding: 8px;">TOTAL NOMINAL SETORAN DITERIMA:</td>
                    <td class="text-right font-mono" style="color: #16a34a; font-size: 12px;">
                        Rp {{ number_format($laporanList->sum('nominal_diterima'), 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="signatures">
        <div class="sign-box">
            <span>Petugas Lapangan / Inkaso</span>
            <div class="sign-line">{{ $petugasNama !== 'Semua Petugas Inkaso' ? $petugasNama : '( Petugas Inkaso )' }}</div>
            <span style="font-size: 9px; color: #64748b;">Pelaksana Penagihan</span>
        </div>

        <div class="sign-box">
            <span>Pencatat Laporan (Keuangan)</span>
            <div class="sign-line">(Staf Piutang )</div>
            <span style="font-size: 9px; color: #64748b;">Administrasi Penagihan</span>
        </div>

        <div class="sign-box">
            <span>Mengetahui,</span>
            <div class="sign-line">( Manajer Keuangan )</div>
            <span style="font-size: 9px; color: #64748b;">HU Kabar Priangan</span>
        </div>
    </div>

</body>
</html>
