<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'email_attempted',
        'action',
        'ip_address',
        'user_agent',
        'description',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper cepat untuk mencatat aktivitas log sistem
     */
    public static function record(string $action, ?string $description = null, ?array $payload = null, ?User $user = null, ?string $email = null): self
    {
        $request = request();

        return self::create([
            'user_id' => $user?->id ?? auth()->id(),
            'email_attempted' => $email ?? $user?->email ?? auth()->user()?->email,
            'action' => $action,
            'ip_address' => $request ? $request->ip() : '127.0.0.1',
            'user_agent' => $request ? substr((string) $request->userAgent(), 0, 500) : null,
            'description' => $description,
            'payload' => $payload,
        ]);
    }
}
