<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\KasTransaksi;
use App\Models\PengajuanAnggaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnggaranController extends Controller
{
    /**
     * Daftar Pengajuan Anggaran Multi-Divisi.
     */
    public function index(Request $request)
    {
        $query = PengajuanAnggaran::with(['creator', 'approver', 'disburser']);

        // Filter Status
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected', 'cair'])) {
            $query->where('status', $request->status);
        }

        // Filter Divisi
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        $pengajuans = $query->latest('tanggal_pengajuan')->paginate(12)->withQueryString();

        // Ringkasan Anggaran
        $totalDiajukan  = (float) PengajuanAnggaran::sum('nominal_diajukan');
        $totalDicairkan = (float) PengajuanAnggaran::where('status', 'cair')->sum('nominal_disetujui');
        $pendingCount   = PengajuanAnggaran::where('status', 'pending')->count();
        $approvedCount  = PengajuanAnggaran::where('status', 'approved')->count();

        // Saldo Kas Saat Ini
        $currentKasSaldo = (float) (KasTransaksi::latest('id')->value('saldo_akhir') ?? 0);

        $daftarDivisi = [
            'iklan'          => 'Divisi Iklan',
            'sirkulasi'      => 'Divisi Sirkulasi',
            'sekum'          => 'Sekretariat Umum (Sekum)',
            'redaksi'        => 'Divisi Redaksi',
            'akuntansi'      => 'Divisi Akuntansi',
            'keuangan'       => 'Divisi Keuangan',
            'bank'           => 'Kas & Bank',
            'management_fee' => 'Management Fee',
        ];

        return view('pages.keuangan.anggaran.index', compact(
            'pengajuans',
            'totalDiajukan',
            'totalDicairkan',
            'pendingCount',
            'approvedCount',
            'currentKasSaldo',
            'daftarDivisi'
        ));
    }

    /**
     * Form Pengajuan Anggaran Baru.
     */
    public function create()
    {
        $daftarDivisi = [
            'iklan'          => 'Divisi Iklan',
            'sirkulasi'      => 'Divisi Sirkulasi',
            'sekum'          => 'Sekretariat Umum (Sekum)',
            'redaksi'        => 'Divisi Redaksi',
            'akuntansi'      => 'Divisi Akuntansi',
            'keuangan'       => 'Divisi Keuangan',
            'bank'           => 'Kas & Bank',
            'management_fee' => 'Management Fee',
        ];

        $period = date('Ym');
        $count = PengajuanAnggaran::where('no_pengajuan', 'like', "ANG-{$period}-%")->count() + 1;
        $noPengajuanSuggested = 'ANG-' . $period . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        return view('pages.keuangan.anggaran.create', compact('daftarDivisi', 'noPengajuanSuggested'));
    }

    /**
     * Simpan Pengajuan Anggaran Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_pengajuan'      => 'required|string|unique:pengajuan_anggaran,no_pengajuan',
            'divisi'            => 'required|in:iklan,sirkulasi,sekum,redaksi,akuntansi,keuangan,bank,management_fee',
            'tanggal_pengajuan' => 'required|date',
            'nominal_diajukan'  => 'required|numeric|min:1000',
            'keterangan'        => 'required|string|max:1000',
        ]);

        $pengajuan = PengajuanAnggaran::create([
            'no_pengajuan'      => $request->no_pengajuan,
            'divisi'            => $request->divisi,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'nominal_diajukan'  => $request->nominal_diajukan,
            'nominal_disetujui' => null,
            'status'            => 'pending',
            'keterangan'        => $request->keterangan,
            'created_by'        => Auth::id(),
        ]);

        return redirect()->route('keuangan.anggaran.index')
            ->with('success', "Pengajuan Anggaran {$pengajuan->no_pengajuan} berhasil didaftarkan dan menunggu persetujuan.");
    }

    /**
     * Setujui (Approve) Pengajuan Anggaran.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'nominal_disetujui' => 'required|numeric|min:1000',
        ]);

        $pengajuan = PengajuanAnggaran::findOrFail($id);
        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status Pending yang dapat disetujui.');
        }

        $pengajuan->update([
            'status'            => 'approved',
            'nominal_disetujui' => $request->nominal_disetujui,
            'approved_by'       => Auth::id(),
        ]);

        return back()->with('success', "Pengajuan Anggaran {$pengajuan->no_pengajuan} disetujui dengan nominal Rp " . number_format($request->nominal_disetujui, 0, ',', '.') . ".");
    }

    /**
     * Tolak (Reject) Pengajuan Anggaran.
     */
    public function reject(Request $request, $id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);
        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan berstatus Pending yang dapat ditolak.');
        }

        $pengajuan->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', "Pengajuan Anggaran {$pengajuan->no_pengajuan} telah ditolak.");
    }

    /**
     * Pencairan Anggaran oleh Kasir (Cash Out ke Buku Kas).
     */
    public function cairkan(Request $request, $id)
    {
        return DB::transaction(function () use ($id, $request) {
            $pengajuan = PengajuanAnggaran::lockForUpdate()->findOrFail($id);

            if ($pengajuan->status !== 'approved') {
                return back()->with('error', 'Hanya pengajuan berstatus Disetujui (Approved) yang dapat dicairkan.');
            }

            $nominal = (float) $pengajuan->nominal_disetujui;
            $saldoKas = (float) (KasTransaksi::lockForUpdate()->latest('id')->value('saldo_akhir') ?? 0);

            if ($saldoKas < $nominal) {
                return back()->with('error', 'Saldo Kas tidak mencukupi! Saldo saat ini Rp ' . number_format($saldoKas, 0, ',', '.') . ', dibutuhkan Rp ' . number_format($nominal, 0, ',', '.') . '.');
            }

            $userId = Auth::id();
            $tanggalCair = $request->input('tanggal_cair', Carbon::now()->toDateString());

            $pengajuan->update([
                'status'       => 'cair',
                'cair_by'      => $userId,
                'tanggal_cair' => $tanggalCair,
            ]);

            // Catat Mutasi Kas Keluar Otomatis
            $kategoriKas = 'Pencairan Anggaran ' . $pengajuan->nama_divisi;
            KasTransaksi::catatMutasi(
                'keluar',
                $kategoriKas,
                $nominal,
                'Pencairan Anggaran ' . $pengajuan->no_pengajuan . ' (' . $pengajuan->nama_divisi . '): ' . $pengajuan->keterangan,
                $pengajuan,
                $userId,
                $tanggalCair
            );

            return back()->with('success', "Dana Anggaran Rp " . number_format($nominal, 0, ',', '.') . " berhasil dicairkan! Mutasi Kas Keluar telah dicatat.");
        });
    }

    /**
     * Cetak Bukti Voucher Kas Keluar.
     */
    public function cetakVoucher($id)
    {
        $pengajuan = PengajuanAnggaran::with(['creator', 'approver', 'disburser'])->findOrFail($id);
        return view('pages.keuangan.anggaran.voucher', compact('pengajuan'));
    }
}
