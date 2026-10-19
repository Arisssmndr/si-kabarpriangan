<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Opname Faktur Iklan - HU Kabar Priangan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 20px;
            background: #fff;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #475569;
        }
        .title-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-box h2 {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #0f172a;
        }
        .title-box span {
            font-size: 11px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .font-bold { font-weight: bold; }
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-space {
            height: 60px;
        }
        @media print {
            body { margin: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 16px; background: #6b21a8; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨 Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <!-- Header Perusahaan -->
    <div class="header">
        <div>
            <h1>HARIAN UMUM KABAR PRIANGAN</h1>
            <p>PT BERKAH PIKIRAN RAKYAT</p>
            <p>Jl. Dr. Soekardjo No. 58 Tasikmalaya - Jawa Barat | Telp: (0265) 331805</p>
        </div>
        <div style="text-align: right;">
            <p><strong>DIVISI KEUANGAN & KONTROL KREDIT</strong></p>
            <p>Modul Faktur: Stok Opname & Rekap Seri Iklan</p>
            <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
        </div>
    </div>

    <!-- Judul Dokumen -->
    <div class="title-box">
        <h2>LEMBAR STOK OPNAME &amp; REKAPITULASI FAKTUR IKLAN</h2>
        <span>{{ $periodeJudul }}</span>
    </div>

    {{-- KONTEN SESUAI TAB --}}
    @if($tab === 'per_ae_bulan')
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>No. Seri Faktur</th>
                    <th>Tanggal</th>
                    <th>Customer / Pemasang</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-right">Nilai Tagihan (Rp)</th>
                    <th class="text-right">Terbayar (Rp)</th>
                    <th class="text-right">Sisa Piutang (Rp)</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $totT = 0; $totB = 0; $totP = 0; @endphp
                @forelse($fakturs as $idx => $faktur)
                    @php 
                        $totT += $faktur->total_tagihan; 
                        $totB += $faktur->total_terbayar; 
                        $totP += $faktur->sisa_piutang; 
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-mono font-bold">{{ $faktur->no_faktur }}</td>
                        <td>{{ $faktur->tanggal_faktur ? $faktur->tanggal_faktur->translatedFormat('d/m/Y') : '-' }}</td>
                        <td>
                            <strong>{{ $faktur->customer->nama_customer ?? '-' }}</strong>
                            <div style="font-size: 9px; color: #64748b;">{{ $faktur->customer->alamat ?? '-' }}</div>
                        </td>
                        <td class="text-center">{{ ucfirst($faktur->tipe_faktur) }}</td>
                        <td class="text-right font-mono">{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #059669;">{{ number_format($faktur->total_terbayar, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</td>
                        <td class="text-center">{{ ucfirst($faktur->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data faktur.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($fakturs->isNotEmpty())
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" class="text-center">TOTAL</td>
                        <td class="text-right font-mono">{{ number_format($totT, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #059669;">{{ number_format($totB, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($totP, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>

    @elseif(in_array($tab, ['per_ae_tahun', 'per_bulan']))
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>Bulan</th>
                    <th class="text-center">Jml Faktur</th>
                    <th>Rentang Seri Terbit</th>
                    <th class="text-center">Reguler</th>
                    <th class="text-center">Stopper</th>
                    <th class="text-center">Bonus</th>
                    <th class="text-right">Total Tagihan (Rp)</th>
                    <th class="text-right">Terbayar (Rp)</th>
                    <th class="text-right">Sisa Piutang (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $totF = 0; $totT = 0; $totB = 0; $totP = 0; @endphp
                @forelse($rekapData as $idx => $row)
                    @php
                        $namaBulan = \Carbon\Carbon::create()->month((int)$row->bulan)->translatedFormat('F');
                        $totF += $row->total_faktur;
                        $totT += $row->total_tagihan;
                        $totB += $row->total_terbayar;
                        $totP += $row->total_piutang;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-bold">{{ $namaBulan }} {{ $tahun }}</td>
                        <td class="text-center font-bold">{{ $row->total_faktur }}</td>
                        <td class="font-mono" style="font-size: 10px;">{{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}</td>
                        <td class="text-center">{{ $row->total_reguler }}</td>
                        <td class="text-center">{{ $row->total_stopper }}</td>
                        <td class="text-center">{{ $row->total_bonus }}</td>
                        <td class="text-right font-mono">{{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #059669;">{{ number_format($row->total_terbayar, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($row->total_piutang, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($rekapData->isNotEmpty())
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" class="text-center">TOTAL</td>
                        <td class="text-center">{{ $totF }}</td>
                        <td colspan="4"></td>
                        <td class="text-right font-mono">{{ number_format($totT, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #059669;">{{ number_format($totB, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($totP, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>

    @elseif(in_array($tab, ['rekap_ae_bulan', 'rekap_ae_tahun']))
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>Account Executive (AE)</th>
                    <th class="text-center">Jml Faktur</th>
                    <th>Rentang Seri Faktur</th>
                    <th class="text-center">Reguler</th>
                    <th class="text-center">Stopper</th>
                    <th class="text-center">Bonus</th>
                    <th class="text-right">Total Omset (Rp)</th>
                    <th class="text-right">Terbayar (Rp)</th>
                    <th class="text-right">Sisa Piutang (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $totF = 0; $totT = 0; $totB = 0; $totP = 0; @endphp
                @forelse($rekapData as $idx => $row)
                    @php
                        $totF += $row->total_faktur;
                        $totT += $row->total_tagihan;
                        $totB += $row->total_terbayar;
                        $totP += $row->total_piutang;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-bold">{{ $row->sales_nama }}</td>
                        <td class="text-center font-bold">{{ $row->total_faktur }}</td>
                        <td class="font-mono" style="font-size: 10px;">{{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}</td>
                        <td class="text-center">{{ $row->total_reguler }}</td>
                        <td class="text-center">{{ $row->total_stopper }}</td>
                        <td class="text-center">{{ $row->total_bonus }}</td>
                        <td class="text-right font-mono font-bold">{{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #059669;">{{ number_format($row->total_terbayar, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($row->total_piutang, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($rekapData->isNotEmpty())
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                        <td class="text-center">{{ $totF }}</td>
                        <td colspan="4"></td>
                        <td class="text-right font-mono">{{ number_format($totT, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #059669;">{{ number_format($totB, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($totP, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>

    @elseif($tab === 'per_pemasang')
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>Nama Customer / Pemasang</th>
                    <th>Perusahaan / Instansi</th>
                    <th>AE Penanggung Jawab</th>
                    <th class="text-center">Jml Faktur</th>
                    <th>Rentang Seri</th>
                    <th class="text-right">Total Tagihan (Rp)</th>
                    <th class="text-right">Terbayar (Rp)</th>
                    <th class="text-right">Sisa Piutang (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $totT = 0; $totB = 0; $totP = 0; @endphp
                @forelse($rekapData as $idx => $row)
                    @php
                        $totT += $row->total_tagihan;
                        $totB += $row->total_terbayar;
                        $totP += $row->total_piutang;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-bold">{{ $row->customer->nama_customer ?? '-' }}</td>
                        <td>{{ $row->customer->alamat ?? '-' }}</td>
                        <td>{{ $row->daftar_ae ?: 'Umum/Kasir' }}</td>
                        <td class="text-center font-bold">{{ $row->total_faktur }}</td>
                        <td class="font-mono" style="font-size: 10px;">{{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}</td>
                        <td class="text-right font-mono font-bold">{{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #059669;">{{ number_format($row->total_terbayar, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($row->total_piutang, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data pemasang.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($rekapData->isNotEmpty())
                <tfoot>
                    <tr class="total-row">
                        <td colspan="6" class="text-center">TOTAL KESELURUHAN</td>
                        <td class="text-right font-mono">{{ number_format($totT, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #059669;">{{ number_format($totB, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($totP, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>

    @elseif($tab === 'per_tahun')
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>Tahun Buku</th>
                    <th class="text-center">Total Faktur</th>
                    <th>Rentang Seri Terbit</th>
                    <th class="text-center">AE Aktif</th>
                    <th class="text-center">Jml Pemasang</th>
                    <th class="text-right">Total Tagihan (Rp)</th>
                    <th class="text-right">Terbayar (Rp)</th>
                    <th class="text-right">Sisa Piutang (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapData as $idx => $row)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-bold">Tahun {{ $row->tahun }}</td>
                        <td class="text-center font-bold">{{ $row->total_faktur }}</td>
                        <td class="font-mono" style="font-size: 10px;">{{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}</td>
                        <td class="text-center">{{ $row->total_ae }} AE</td>
                        <td class="text-center">{{ $row->total_pemasang }} Client</td>
                        <td class="text-right font-mono font-bold">{{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #059669;">{{ number_format($row->total_terbayar, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #dc2626;">{{ number_format($row->total_piutang, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <!-- Tanda Tangan Pengesahan -->
    <div class="signatures">
        <div class="signature-box">
            <p>Dibuat Oleh,</p>
            <div class="signature-space"></div>
            <p><strong>Administrasi Faktur & Iklan</strong></p>
            <p style="font-size: 9px; color: #64748b;">HU Kabar Priangan</p>
        </div>
        <div class="signature-box">
            <p>Diperiksa Oleh,</p>
            <div class="signature-space"></div>
            <p><strong>Supervisor Administrasi</strong></p>
            <p style="font-size: 9px; color: #64748b;">HU Kabar Priangan</p>
        </div>
        <div class="signature-box">
            <p>Mengetahui / Menyetujui,</p>
            <div class="signature-space"></div>
            <p><strong>Manajer Keuangan</strong></p>
            <p style="font-size: 9px; color: #64748b;">HU Kabar Priangan</p>
        </div>
    </div>

</body>
</html>
