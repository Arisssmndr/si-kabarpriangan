<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'status',
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
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
            'failed_login_attempts' => 'integer',
        ];
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
            'keuangan', 'admin_keuangan' => 'Divisi Keuangan',
            'accounting' => 'Divisi Accounting',
            'sirkulasi' => 'Divisi Sirkulasi',
            'kasir' => 'Divisi Kasir',
            'petugas_inkaso' => 'Petugas Inkaso',
            'kepala_divisi' => 'Kepala Divisi',
            default => ucfirst($this->role ?? 'Staf'),
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
