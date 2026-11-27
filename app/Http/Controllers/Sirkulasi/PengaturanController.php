<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Models\AuditLogAkuntansi;
use App\Models\DailyCutoff;
use App\Models\SistemConfig;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PengaturanController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Halaman Tarif & Pengaturan Operasional Sirkulasi.
     */
    public function index()
    {
        $settings = $this->sirkulasi->getConfig();
        $isPastCutoff = now()->format('H:i') >= $settings['jam_cutoff'];

        return view('pages.sirkulasi.pengaturan.index', compact('settings', 'isPastCutoff'));
    }

    /**
     * Simpan perubahan Tarif & Pengaturan Operasional Sirkulasi.
     */
    public function update(Request $request)
    {
        $cleanNumber = fn($val) => (int) preg_replace('/[^0-9]/', '', (string) $val);

        $request->merge([
            'sirkulasi_harga_agen_besar' => $cleanNumber($request->input('sirkulasi_harga_agen_besar')),
            'sirkulasi_harga_eceran'     => $cleanNumber($request->input('sirkulasi_harga_eceran')),
            'sirkulasi_harga_langganan'  => $cleanNumber($request->input('sirkulasi_harga_langganan')),
            'sirkulasi_harga_cover_het'  => $cleanNumber($request->input('sirkulasi_harga_cover_het')),
        ]);

        $validated = $request->validate([
            'sirkulasi_harga_agen_besar'   => 'required|integer|min:100',
            'sirkulasi_harga_eceran'       => 'required|integer|min:100',
            'sirkulasi_harga_langganan'    => 'required|integer|min:100',
            'sirkulasi_harga_cover_het'    => 'required|integer|min:100',
            'sirkulasi_isi_bendel'         => 'required|integer|min:10|max:500',
            'sirkulasi_jam_cutoff'         => 'required|date_format:H:i',
            'sirkulasi_batas_retur_persen' => 'required|numeric|min:0|max:100',
            'sirkulasi_rasio_langganan'    => 'required|numeric|min:0|max:100',
            'sirkulasi_rasio_konsinyasi'   => 'required|numeric|min:0|max:100',
            'sirkulasi_rasio_gratis'       => 'required|numeric|min:0|max:100',
        ]);

        $totalRasio = (float) $validated['sirkulasi_rasio_langganan']
            + (float) $validated['sirkulasi_rasio_konsinyasi']
            + (float) $validated['sirkulasi_rasio_gratis'];

        if (abs($totalRasio - 100) > 0.01) {
            return back()->withInput()->withErrors([
                'sirkulasi_rasio_langganan' => "Total komposisi rasio default (Langganan + Konsinyasi + Gratis) harus tepat 100% (saat ini {$totalRasio}%).",
            ]);
        }

        $labels = [
            'sirkulasi_harga_agen_besar'   => 'Tarif nett Agen Besar (Kategori 11) per eksemplar',
            'sirkulasi_harga_eceran'       => 'Tarif jual konsinyasi/eceran koran per eksemplar',
            'sirkulasi_harga_langganan'    => 'Tarif langganan rutin per eksemplar',
            'sirkulasi_harga_cover_het'    => 'Harga Eceran Tertinggi (HET) cover koran',
            'sirkulasi_isi_bendel'         => 'Jumlah eksemplar per ikat/bendel pengepakan percetakan',
            'sirkulasi_jam_cutoff'         => 'Batas waktu (Cut-Off) input oplah distribusi harian',
            'sirkulasi_batas_retur_persen' => 'Batas toleransi maksimal retur konsinyasi agen (%)',
            'sirkulasi_rasio_langganan'    => 'Persentase rasio default jatah Langganan (%)',
            'sirkulasi_rasio_konsinyasi'   => 'Persentase rasio default jatah Konsinyasi (%)',
            'sirkulasi_rasio_gratis'       => 'Persentase rasio default jatah Gratis (%)',
        ];

        foreach ($validated as $key => $val) {
            SistemConfig::set($key, $val, $labels[$key] ?? null);
        }

        $this->reopenAutoClosedTodayIfNeeded($validated['sirkulasi_jam_cutoff']);

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            $operator = auth()->user()?->name ?? 'Admin Sirkulasi';
            AuditLogAkuntansi::catat('PENGATURAN_SIRKULASI', 'SIRKULASI', "Perbaruan tarif & operasional sirkulasi (Cut-off {$validated['sirkulasi_jam_cutoff']} WIB) oleh {$operator}");
        }

        return redirect()->route('sirkulasi.pengaturan.index')
            ->with('success', 'Tarif koran dan pengaturan operasional Sirkulasi berhasil diperbarui dan langsung berlaku.');
    }

    /**
     * Update Cepat Jam Cut-Off Harian Sirkulasi oleh Admin / Manajer.
     */
    public function updateCutoffTime(Request $request)
    {
        $validated = $request->validate([
            'sirkulasi_jam_cutoff' => 'required|date_format:H:i',
        ]);

        $newCutoff = $validated['sirkulasi_jam_cutoff'];
        SistemConfig::set('sirkulasi_jam_cutoff', $newCutoff, 'Batas waktu (Cut-Off) input oplah distribusi harian');

        $this->reopenAutoClosedTodayIfNeeded($newCutoff);

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            $operator = auth()->user()?->name ?? 'Admin Sirkulasi';
            AuditLogAkuntansi::catat('PENGATURAN_CUTOFF', 'SIRKULASI', "Perubahan jam Cut-Off sirkulasi harian menjadi {$newCutoff} WIB oleh {$operator}");
        }

        return back()->with('success', "Jam Cut-Off Sirkulasi Harian berhasil diperbarui menjadi {$newCutoff} WIB.");
    }

    protected function reopenAutoClosedTodayIfNeeded(string $newCutoff): void
    {
        $currentTime = Carbon::now()->format('H:i');
        $todayStr = Carbon::today()->toDateString();

        if ($newCutoff <= $currentTime) {
            return;
        }

        if (Schema::hasTable('daily_cutoffs')) {
            $cutoffRow = DailyCutoff::whereDate('tanggal_edisi', $todayStr)->first();
            if ($cutoffRow && str_contains((string) $cutoffRow->catatan, 'Sistem Otomatis')) {
                $cutoffRow->update(['is_closed' => false]);
            }
        }

        $lockedBy = session('locked_by_' . $todayStr, Cache::get('locked_by_' . $todayStr, ''));
        if (str_contains((string) $lockedBy, 'Otomatis')) {
            session()->forget('locked_harian_' . $todayStr);
            session()->forget('locked_at_' . $todayStr);
            session()->forget('locked_by_' . $todayStr);
            Cache::forget('locked_harian_' . $todayStr);
            Cache::forget('locked_at_' . $todayStr);
            Cache::forget('locked_by_' . $todayStr);
        }
    }
}
