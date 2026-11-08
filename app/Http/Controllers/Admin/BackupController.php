<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemBackup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Tampilkan katalog arsip cadangan basis data (Area 1).
     */
    public function index()
    {
        $backups = SystemBackup::latest()->paginate(15);

        $stats = [
            'total' => SystemBackup::count(),
            'total_size' => SystemBackup::sum('filesize_bytes'),
            'last_backup' => SystemBackup::latest()->first(),
            'verified_count' => SystemBackup::where('status', 'verified')->count(),
        ];

        return view('pages.admin.backups.index', compact('backups', 'stats'));
    }

    /**
     * Eksekusi pencadangan manual seketika.
     */
    public function createManual()
    {
        try {
            $exitCode = Artisan::call('backup:database', [
                '--frequency' => 'manual',
                '--manual' => true,
            ]);

            if ($exitCode === 0) {
                return redirect()->route('admin.backups.index')->with('success', 'Pencadangan database manual berhasil dibuat dan diarsipkan dengan proteksi Gzip.');
            }

            return redirect()->route('admin.backups.index')->with('error', 'Pencadangan manual gagal dieksekusi. Periksa log sistem.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Galat saat membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Unduh file cadangan database.
     */
    public function download($id)
    {
        $backup = SystemBackup::findOrFail($id);
        $fullPath = storage_path('app/' . $backup->filepath);

        if (!File::exists($fullPath)) {
            return redirect()->route('admin.backups.index')->with('error', 'Berkas backup tidak ditemukan di storage server.');
        }

        ActivityLog::record(
            'BACKUP_DOWNLOADED',
            "Administrator mengunduh berkas cadangan database: {$backup->filename}",
            ['backup_id' => $backup->id, 'filename' => $backup->filename]
        );

        return response()->download($fullPath, $backup->filename, [
            'Content-Type' => 'application/gzip',
        ]);
    }

    /**
     * Verifikasi integritas checksum SHA-256 (Restore Dry-Run Verification).
     */
    public function verify($id)
    {
        $backup = SystemBackup::findOrFail($id);
        $fullPath = storage_path('app/' . $backup->filepath);

        if (!File::exists($fullPath)) {
            $backup->update(['status' => 'failed', 'notes' => 'File hilang dari storage.']);
            return redirect()->route('admin.backups.index')->with('error', 'File tidak ditemukan di disk server.');
        }

        $calculatedChecksum = hash_file('sha256', $fullPath);

        if ($calculatedChecksum === $backup->checksum_sha256) {
            $backup->update([
                'status' => 'verified',
                'notes' => 'Integritas SHA-256 terverifikasi 100% cocok pada ' . now()->toDateTimeString() . '.',
            ]);

            ActivityLog::record(
                'BACKUP_VERIFIED',
                "Verifikasi integritas file backup {$backup->filename} BERHASIL (SHA-256 Valid)",
                ['backup_id' => $backup->id]
            );

            return redirect()->route('admin.backups.index')->with('success', "Integritas berkas {$backup->filename} berhasil diverifikasi. Checksum SHA-256 cocok 100%.");
        } else {
            $backup->update([
                'status' => 'failed',
                'notes' => 'Peringatan: Checksum SHA-256 tidak cocok! Kemungkinan file mengalami kerusakan.',
            ]);

            ActivityLog::record(
                'BACKUP_VERIFICATION_FAILED',
                "PERINGATAN: File backup {$backup->filename} korup/checksum tidak cocok!",
                ['backup_id' => $backup->id]
            );

            return redirect()->route('admin.backups.index')->with('error', "Peringatan! Checksum file tidak cocok dengan data awal.");
        }
    }
}
