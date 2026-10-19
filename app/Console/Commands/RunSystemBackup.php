<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\SystemBackup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RunSystemBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--frequency=daily} {--manual}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pencadangan database otomatis SiKAPRI terisolasi dengan kompresi dan checksum SHA-256 (Area 1)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $frequency = $this->option('frequency') ?: 'daily';
        $isManual = (bool) $this->option('manual');

        $this->info("Memulai proses pencadangan database SiKAPRI ({$frequency})...");

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "sikapri_backup_{$frequency}_{$timestamp}.sql";
        $filepath = "{$backupDir}/{$filename}";
        $gzFilename = "{$filename}.gz";
        $gzFilepath = "{$backupDir}/{$gzFilename}";

        try {
            // 1. Ekspor Database Menggunakan Native PDO Dump (Portabel di semua sistem)
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            $databaseName = $connection->getDatabaseName();
            $driver = $connection->getDriverName();

            $handle = fopen($filepath, 'w+');
            if (!$handle) {
                throw new \Exception("Gagal membuka file backup untuk penulisan: {$filepath}");
            }

            fwrite($handle, "-- SIKAPRI DATABASE BACKUP\n");
            fwrite($handle, "-- Standar Keamanan Area 1 (CIA Triad: Availability)\n");
            fwrite($handle, "-- Database: {$databaseName} (Driver: {$driver})\n");
            fwrite($handle, "-- Tanggal Eksekusi: " . now()->toDateTimeString() . "\n");
            fwrite($handle, "-- Frekuensi: {$frequency}\n");

            if ($driver === 'mysql') {
                fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");
                $tables = $connection->select('SHOW TABLES');
                $key = "Tables_in_{$databaseName}";
            } else {
                $tables = $connection->select("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            }

            foreach ($tables as $tableRow) {
                if ($driver === 'mysql') {
                    $tableName = $tableRow->$key ?? array_values((array)$tableRow)[0];
                    $createTableResult = $connection->select("SHOW CREATE TABLE `{$tableName}`");
                    $createTableSql = $createTableResult[0]->{'Create Table'} ?? '';
                } else {
                    $tableName = $tableRow->name;
                    $createTableSql = $tableRow->sql;
                }

                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                fwrite($handle, "{$createTableSql};\n\n");

                // Dump Baris Data
                $rows = $connection->table($tableName)->get();
                if ($rows->count() > 0) {
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array)$row as $val) {
                            if (is_null($val)) {
                                $values[] = 'NULL';
                            } else {
                                $values[] = $pdo->quote($val);
                            }
                        }
                        fwrite($handle, "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n");
                    }
                    fwrite($handle, "\n");
                }
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fwrite($handle, "-- AKHIR DARI CADANGAN DATABASE SIKAPRI\n");
            fclose($handle);

            // 2. Kompresi file SQL menjadi format .gz (Gzip)
            $gzHandle = gzopen($gzFilepath, 'w9');
            $fp = fopen($filepath, 'r');
            while (!feof($fp)) {
                gzwrite($gzHandle, fread($fp, 1024 * 512));
            }
            fclose($fp);
            gzclose($gzHandle);

            // Hapus file .sql uncompressed untuk menghemat kapasitas storage
            if (File::exists($filepath)) {
                File::delete($filepath);
            }

            // 3. Kalkulasi Ukuran & Checksum SHA-256
            $filesize = filesize($gzFilepath);
            $checksum = hash_file('sha256', $gzFilepath);

            // 4. Catat riwayat backup ke tabel system_backups
            $backupRecord = SystemBackup::create([
                'filename' => $gzFilename,
                'filepath' => "backups/{$gzFilename}",
                'filesize_bytes' => $filesize,
                'frequency' => $isManual ? 'manual' : $frequency,
                'checksum_sha256' => $checksum,
                'is_encrypted' => false,
                'status' => 'success',
                'notes' => 'Backup database berhasil dieksekusi secara terkompresi Gzip.',
            ]);

            // 5. Catat log keamanan
            ActivityLog::record(
                'BACKUP_DATABASE_SUCCESS',
                "Pencadangan database ({$gzFilename}) berhasil diselesaikan. Ukuran: " . $backupRecord->formatted_size . ", SHA-256: " . substr($checksum, 0, 16) . "...",
                ['filesize' => $filesize, 'checksum' => $checksum, 'frequency' => $frequency]
            );

            $this->info("Backup database berhasil! File tersimpan di: {$gzFilepath}");
            $this->info("Ukuran: " . $backupRecord->formatted_size . " | SHA-256: {$checksum}");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Gagal melakukan backup database: " . $e->getMessage());

            ActivityLog::record(
                'BACKUP_DATABASE_FAILED',
                "Gagal melakukan backup database: " . $e->getMessage(),
                ['error' => $e->getMessage()]
            );

            return Command::FAILURE;
        }
    }
}
