<?php

use App\Support\ClientDeviceInspector;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'last_login_device')) {
                $table->string('last_login_device', 191)->nullable()->after('last_login_ip');
            }
            if (! Schema::hasColumn('users', 'last_login_browser')) {
                $table->string('last_login_browser', 191)->nullable()->after('last_login_device');
            }
            if (! Schema::hasColumn('users', 'last_login_user_agent')) {
                $table->string('last_login_user_agent', 500)->nullable()->after('last_login_browser');
            }
        });

        // Backfill data IP LAN, nama perangkat, dan web browser dari riwayat ActivityLog yang sudah ada
        if (Schema::hasTable('activity_logs')) {
            $lanIp = ClientDeviceInspector::getLocalLanIp() ?: '127.0.0.1';

            // Perbarui IP loopback pada activity_logs ke IP LAN perangkat jika tersedia
            if ($lanIp !== '127.0.0.1') {
                DB::table('activity_logs')
                    ->whereIn('ip_address', ['127.0.0.1', '::1'])
                    ->update(['ip_address' => $lanIp]);
            }

            $users = DB::table('users')->get();
            foreach ($users as $u) {
                $latestLog = DB::table('activity_logs')
                    ->where('user_id', $u->id)
                    ->where('action', 'LOGIN_SUCCESS')
                    ->orderByDesc('created_at')
                    ->first();

                if ($latestLog) {
                    $resolvedIp = ClientDeviceInspector::resolveIp(null, $latestLog->ip_address ?: $u->last_login_ip);
                    $device = ClientDeviceInspector::resolveDeviceName($latestLog->user_agent, $resolvedIp);
                    $browser = ClientDeviceInspector::resolveBrowser($latestLog->user_agent);

                    DB::table('users')->where('id', $u->id)->update([
                        'last_login_at' => $u->last_login_at ?: $latestLog->created_at,
                        'last_login_ip' => $resolvedIp,
                        'last_login_device' => $device !== '-' ? $device : null,
                        'last_login_browser' => $browser !== '-' ? $browser : null,
                        'last_login_user_agent' => $latestLog->user_agent,
                    ]);
                } elseif ($u->last_login_ip && in_array($u->last_login_ip, ['127.0.0.1', '::1'], true)) {
                    DB::table('users')->where('id', $u->id)->update([
                        'last_login_ip' => $lanIp,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            foreach (['last_login_device', 'last_login_browser', 'last_login_user_agent'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $cols[] = $col;
                }
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
