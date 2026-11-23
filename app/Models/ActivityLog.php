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
        $resolvedIp = \App\Support\ClientDeviceInspector::resolveIp($request);
        $userAgent = $request ? substr((string) $request->userAgent(), 0, 500) : null;

        if (is_array($payload) && isset($payload['ip']) && in_array($payload['ip'], ['127.0.0.1', '::1'], true)) {
            $payload['ip'] = $resolvedIp;
        }

        if ($description && in_array($request?->ip(), ['127.0.0.1', '::1'], true)) {
            $description = str_replace(['127.0.0.1', '::1'], $resolvedIp, $description);
        }

        return self::create([
            'user_id' => $user?->id ?? auth()->id(),
            'email_attempted' => $email ?? $user?->email ?? auth()->user()?->email,
            'action' => $action,
            'ip_address' => $resolvedIp,
            'user_agent' => $userAgent,
            'description' => $description,
            'payload' => $payload,
        ]);
    }


    /**
     * Alamat IP yang sudah diresolv ke IP LAN jika loopback.
     */
    public function getResolvedIpAttribute(): string
    {
        if ($this->ip_address && ! in_array($this->ip_address, ['127.0.0.1', '::1'], true)) {
            return $this->ip_address;
        }

        return \App\Support\ClientDeviceInspector::resolveIp(null, $this->ip_address ?: '127.0.0.1');
    }

    /**
     * Nama perangkat & sistem operasi dari User-Agent.
     */
    public function getDeviceNameAttribute(): string
    {
        return \App\Support\ClientDeviceInspector::resolveDeviceName($this->user_agent, $this->ip_address);
    }

    /**
     * Nama & versi Web Browser dari User-Agent.
     */
    public function getBrowserNameAttribute(): string
    {
        return \App\Support\ClientDeviceInspector::resolveBrowser($this->user_agent);
    }
}
