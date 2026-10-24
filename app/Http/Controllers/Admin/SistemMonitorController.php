<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SistemMonitorController extends Controller
{
    /**
     * Dashboard Pemantauan Kesehatan Sistem & Live Log Viewer (System Health Monitor)
     */
    public function index()
    {
        // 1. Data Environment & Server Runtime
        $serverInfo = [
            'php_version'       => PHP_VERSION,
            'laravel_version'   => app()->version(),
            'app_env'           => config('app.env', 'production'),
            'app_debug'         => config('app.debug', false),
            'app_timezone'      => config('app.timezone', 'UTC'),
            'server_os'         => PHP_OS . ' (' . php_uname('m') . ')',
            'server_software'   => $_SERVER['SERVER_SOFTWARE'] ?? (php_uname('s') . ' ' . php_uname('r')),
            'memory_limit'      => ini_get('memory_limit'),
            'memory_usage'      => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'memory_peak'       => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'max_execution_time'=> ini_get('max_execution_time') . 's',
            'upload_max_filesize'=> ini_get('upload_max_filesize'),
            'post_max_size'     => ini_get('post_max_size'),
        ];

        // 2. Pemeriksaan Koneksi & Latensi Database
        $dbStatus = [
            'connected' => false,
            'driver'    => config('database.default'),
            'database'  => config('database.connections.' . config('database.default') . '.database'),
            'latency'   => 0,
            'tables_cnt'=> 0,
            'db_size'   => 'N/A',
        ];

        try {
            $t0 = microtime(true);
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $t0) * 1000, 2);

            $dbStatus['connected'] = true;
            $dbStatus['latency'] = $latency;

            // Hitung tabel jika MySQL / SQLite
            $tables = DB::select('SHOW TABLES');
            $dbStatus['tables_cnt'] = count($tables);

            // Perkiraan ukuran DB
            $dbName = $dbStatus['database'];
            $sizeResult = DB::select("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb 
                FROM information_schema.TABLES 
                WHERE table_schema = ?
            ", [$dbName]);

            if (!empty($sizeResult) && isset($sizeResult[0]->size_mb)) {
                $dbStatus['db_size'] = $sizeResult[0]->size_mb . ' MB';
            }
        } catch (\Throwable $e) {
            $dbStatus['connected'] = false;
            $dbStatus['error'] = $e->getMessage();
        }

        // 3. Storage & Disk Health
        $baseDir = base_path();
        $freeDisk = @disk_free_space($baseDir);
        $totalDisk = @disk_total_space($baseDir);

        $diskInfo = [
            'free_bytes'    => $freeDisk !== false ? $freeDisk : 0,
            'total_bytes'   => $totalDisk !== false ? $totalDisk : 0,
            'free_formatted'=> $freeDisk !== false ? round($freeDisk / 1024 / 1024 / 1024, 2) . ' GB' : 'N/A',
            'total_formatted'=> $totalDisk !== false ? round($totalDisk / 1024 / 1024 / 1024, 2) . ' GB' : 'N/A',
            'used_pct'      => ($totalDisk > 0 && $freeDisk !== false) ? round((($totalDisk - $freeDisk) / $totalDisk) * 100, 1) : 0,
            'storage_path'  => storage_path(),
        ];

        // 4. Parser Log Sistem (Laravel Logs Viewer)
        $logPath = storage_path('logs/laravel.log');
        $logExists = File::exists($logPath);
        $logSize = $logExists ? round(File::size($logPath) / 1024, 2) : 0; // in KB
        $logEntries = [];

        if ($logExists && $logSize > 0) {
            // Baca potongan terakhir berkas log untuk menghemat RAM
            $maxBytes = 250 * 1024; // baca max 250KB terakhir
            $fileSize = File::size($logPath);
            $handle = fopen($logPath, 'r');
            
            if ($handle) {
                if ($fileSize > $maxBytes) {
                    fseek($handle, $fileSize - $maxBytes);
                }
                $content = fread($handle, $maxBytes);
                fclose($handle);

                // Regex parser untuk log Laravel: [YYYY-MM-DD HH:MM:SS] env.LEVEL: Message
                $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.([A-Z]+): (.*?)(?=(?:\n\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])|\Z)/s';
                if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                    // Ambil dari yang paling baru (reverse)
                    $reversed = array_reverse($matches);
                    $count = 0;
                    foreach ($reversed as $m) {
                        if ($count >= 25) break; // Batasi 25 entri terakhir
                        
                        $rawBody = trim($m[4]);
                        $lines = explode("\n", $rawBody);
                        $shortMessage = $lines[0] ?? '';
                        $stackTrace = count($lines) > 1 ? implode("\n", array_slice($lines, 1)) : null;

                        $logEntries[] = [
                            'timestamp' => $m[1],
                            'env'       => $m[2],
                            'level'     => strtoupper($m[3]),
                            'message'   => $shortMessage,
                            'trace'     => $stackTrace,
                        ];
                        $count++;
                    }
                }
            }
        }

        // 5. Pengguna Terdaftar & Status Login Terkini
        $recentUsers = User::orderBy('updated_at', 'desc')->take(8)->get();

        return view('pages.admin.sistem.index', compact(
            'serverInfo',
            'dbStatus',
            'diskInfo',
            'logExists',
            'logSize',
            'logEntries',
            'recentUsers'
        ));
    }

    /**
     * Bersihkan cache aplikasi, konfigurasi, dan kompilasi view (Maintenance Toolkit)
     */
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');

            ActivityLog::record(
                'SYSTEM_CLEAR_CACHE',
                'Administrator menjalankan pembersihan cache sistem dan kompilasi view',
                ['triggered_by' => auth()->user()?->name]
            );

            return redirect()->route('admin.sistem.index')
                ->with('success', 'Cache aplikasi, konfigurasi, dan blade template berhasil dibersihkan!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.sistem.index')
                ->with('error', 'Gagal membersihkan cache: ' . $e->getMessage());
        }
    }

    /**
     * Unduh berkas laravel.log
     */
    public function downloadLog()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            return redirect()->route('admin.sistem.index')->with('error', 'Berkas log tidak ditemukan.');
        }

        ActivityLog::record('SYSTEM_DOWNLOAD_LOG', 'Administrator mengunduh berkas log sistem');
        return response()->download($logPath, 'laravel-log-' . date('Y-m-d_His') . '.log');
    }

    /**
     * Kosongkan berkas log sistem
     */
    public function clearLog(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        ActivityLog::record('SYSTEM_CLEAR_LOG', 'Administrator mengosongkan berkas log laravel.log');

        return redirect()->route('admin.sistem.index')
            ->with('success', 'Berkas log sistem berhasil dikosongkan!');
    }
}
