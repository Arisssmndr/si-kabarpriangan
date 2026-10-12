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
        ];
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
            'keuangan' => 'Divisi Keuangan',
            'accounting' => 'Divisi Accounting',
            'sirkulasi' => 'Divisi Sirkulasi',
            'kasir' => 'Divisi Kasir',
            default => ucfirst($this->role ?? 'Staf'),
        };
    }
}
