<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemBackup extends Model
{
    protected $fillable = [
        'filename',
        'filepath',
        'filesize_bytes',
        'frequency',
        'checksum_sha256',
        'is_encrypted',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'filesize_bytes' => 'integer',
            'is_encrypted' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Format ukuran file manusiawi (KB, MB, GB)
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->filesize_bytes;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
