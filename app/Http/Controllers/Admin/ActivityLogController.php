<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan riwayat log aktivitas dan peristiwa keamanan sistem (Area 2 & CIA Triad).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $actionFilter = $request->input('action');

        $query = ActivityLog::with('user')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email_attempted', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($actionFilter && $actionFilter !== 'all') {
            $query->where('action', $actionFilter);
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => ActivityLog::count(),
            'logins_today' => ActivityLog::where('action', 'LOGIN_SUCCESS')->whereDate('created_at', today())->count(),
            'failed_today' => ActivityLog::whereIn('action', ['LOGIN_FAILED_1ST', 'LOGIN_FAILED_2ND', 'LOGIN_FAILED'])->whereDate('created_at', today())->count(),
            'lockouts' => ActivityLog::where('action', 'ACCOUNT_LOCKED_15M')->count(),
        ];

        $actionsList = [
            'LOGIN_SUCCESS' => 'Login Berhasil',
            'LOGIN_FAILED_1ST' => 'Gagal Login (Ke-1)',
            'LOGIN_FAILED_2ND' => 'Gagal Login (Ke-2)',
            'ACCOUNT_LOCKED_15M' => 'Akun Dikunci (15 Menit)',
            'LOGIN_REJECTED_DISABLED' => 'Login Ditolak (Akun Disable)',
            'USER_CREATED' => 'Akun Dibuat',
            'USER_UPDATED' => 'Akun Diperbarui',
            'USER_STATUS_CHANGED' => 'Status Akun Diubah',
            'USER_DISABLED_PRESERVED' => 'Hapus Ditolak (Diubah Disable)',
            'BACKUP_DATABASE_SUCCESS' => 'Pencadangan Berhasil',
        ];

        return view('pages.admin.activity_logs.index', compact('logs', 'stats', 'search', 'actionFilter', 'actionsList'));
    }
}
