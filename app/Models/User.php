<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'foto',
        'telepon',
        'status',
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
        'last_login_device',
        'last_login_browser',
        'last_login_user_agent',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'failed_login_attempts' => 'integer',
        ];
    }

    /**
     * URL Foto Profil Pengguna (Mendukung folder public uploads maupun storage).
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            if (file_exists(public_path('uploads/avatars/' . $this->foto))) {
                return asset('uploads/avatars/' . $this->foto);
            }
            if (file_exists(storage_path('app/public/avatars/' . $this->foto))) {
                return asset('storage/avatars/' . $this->foto);
            }
        }
        return null;
    }

    /**
     * Cek apakah pengguna saat ini sedang aktif (Online) di dalam aplikasi.
     * Standar: Ada di Cache dengan TTL 3 menit ATAU last_seen_at dalam 3 menit terakhir.
     */
    public function isOnline(): bool
    {
        return Cache::has('user-is-online-' . $this->id) 
            || ($this->last_seen_at && $this->last_seen_at->greaterThanOrEqualTo(now()->subMinutes(3)));
    }

    /**
     * Label ramah manusia untuk status terakhir aktif staf.
     */
    public function getLastSeenTextAttribute(): string
    {
        if ($this->isOnline()) {
            return 'Aktif Sekarang';
        }

        if (! $this->last_seen_at) {
            return 'Nonaktif';
        }

        return 'Nonaktif (' . $this->last_seen_at->diffForHumans() . ')';
    }

    /**
     * Format jam & tanggal terakhir admin/staf login ke website (WIB).
     */
    public function getFormattedLastLoginAttribute(): string
    {
        return \App\Support\ClientDeviceInspector::formatLoginDateTime($this->last_login_at);
    }

    /**
     * Alamat IP login terakhir yang sudah diresolv (LAN / Client IP).
     */
    public function getResolvedLastLoginIpAttribute(): string
    {
        if (! $this->last_login_at && ! $this->last_login_ip) {
            return '-';
        }

        if ($this->last_login_ip && ! in_array($this->last_login_ip, ['127.0.0.1', '::1'], true)) {
            return $this->last_login_ip;
        }

        return \App\Support\ClientDeviceInspector::resolveIp(null, $this->last_login_ip);
    }

    /**
     * Nama perangkat & OS saat login terakhir.
     */
    public function getResolvedLastLoginDeviceAttribute(): string
    {
        if (! empty($this->last_login_device)) {
            return $this->last_login_device;
        }

        if (! empty($this->last_login_user_agent)) {
            return \App\Support\ClientDeviceInspector::resolveDeviceName($this->last_login_user_agent, $this->last_login_ip);
        }

        return '-';
    }

    /**
     * Nama & versi Web Browser saat login terakhir.
     */
    public function getResolvedLastLoginBrowserAttribute(): string
    {
        if (! empty($this->last_login_browser)) {
            return $this->last_login_browser;
        }

        if (! empty($this->last_login_user_agent)) {
            return \App\Support\ClientDeviceInspector::resolveBrowser($this->last_login_user_agent);
        }

        return '-';
    }

    /**
     * Relasi ke Activity Logs
     */
    public function activityLogs()
    {
        return $this->hasMany(\App\Models\ActivityLog::class);
    }

    /**
     * Cek apakah status user adalah Active
     */
    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    /**
     * Cek apakah status user adalah Review
     */
    public function isReview(): bool
    {
        return ($this->status ?? 'active') === 'review';
    }

    /**
     * Cek apakah status user adalah Disable
     */
    public function isDisable(): bool
    {
        return ($this->status ?? 'active') === 'disable';
    }

    /**
     * Cek apakah user sedang terkunci sementara
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Cek apakah user memiliki satu atau beberapa role tertentu.
     */
    public function hasRole(string|array $roles): bool
    {
        $currentRole = strtolower(trim((string) $this->role));
        $roles = is_array($roles) ? $roles : func_get_args();

        foreach ($roles as $role) {
            if ($currentRole === strtolower(trim($role))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah user adalah Administrator (Superadmin).
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('administrator', 'admin');
    }

    /**
     * Cek apakah user berhak mengakses divisi tertentu.
     * Administrator selalu memiliki akses ke semua divisi.
     */
    public function canAccessDivision(string $division): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->hasRole($division);
    }

    /**
     * Label nama divisi yang rapi untuk tampilan UI.
     */
    public function getRoleLabelAttribute(): string
    {
        $normalized = strtolower(trim((string) $this->role));
        return match ($normalized) {
            'administrator', 'admin' => 'Administrator',
            'iklan' => 'Divisi Iklan',
            'faktur' => 'Divisi Faktur',
            'anggaran' => 'Divisi Anggaran',
            'keuangan', 'admin_keuangan' => 'Divisi Keuangan',
            'accounting' => 'Divisi Accounting',
            'sirkulasi' => 'Divisi Sirkulasi',
            'kasir' => 'Divisi Kasir',
            'petugas_inkaso', 'inkaso' => 'Petugas Inkaso',
            'kepala_divisi' => 'Kepala Divisi',
            default => ucfirst($this->role ?? 'Staf'),
        };
    }

    /**
     * Label unit pelaksana untuk kop laporan & cetakan modul Kasir.
     */
    public function kasirReportUnitLabel(): string
    {
        $normalized = strtolower(trim((string) $this->role));

        return match ($normalized) {
            'kasir' => 'Divisi Kasir',
            'administrator', 'admin', 'keuangan', 'admin_keuangan', 'kepala_divisi' => 'Manager Keuangan',
            default => $this->role_label,
        };
    }

    public function shiftKasirs()
    {
        return $this->hasMany(ShiftKasir::class, 'kasir_id');
    }

    public function kasMasuks()
    {
        return $this->hasMany(KasMasuk::class, 'user_id');
    }

    public function kasKeluars()
    {
        return $this->hasMany(KasKeluar::class, 'user_id');
    }
}
