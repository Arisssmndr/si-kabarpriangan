<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;

class ClientDeviceInspector
{
    /**
     * Dapatkan alamat IP asli klien (mendukung proxy/reverse-proxy & resolusi IPv4 LAN lokal).
     */
    public static function resolveIp(?Request $request = null, ?string $fallbackIp = null): string
    {
        $request = $request ?? request();
        $candidates = [];

        if ($request) {
            foreach ([
                'HTTP_CF_CONNECTING_IP',
                'HTTP_X_REAL_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_CLIENT_IP',
            ] as $header) {
                $val = $request->server($header);
                if (! empty($val)) {
                    foreach (explode(',', (string) $val) as $part) {
                        $part = trim($part);
                        if (filter_var($part, FILTER_VALIDATE_IP)) {
                            $candidates[] = $part;
                        }
                    }
                }
            }

            if ($request->ip()) {
                $candidates[] = $request->ip();
            }
        }

        if ($fallbackIp) {
            $candidates[] = $fallbackIp;
        }

        foreach ($candidates as $ip) {
            if (! in_array($ip, ['127.0.0.1', '::1', 'localhost'], true)) {
                return $ip;
            }
        }

        // Jika diakses dari mesin lokal (127.0.0.1 / ::1), ambil alamat IPv4 LAN aktif perangkat
        $lanIp = self::getLocalLanIp();
        if ($lanIp && ! in_array($lanIp, ['127.0.0.1', '::1'], true)) {
            return $lanIp;
        }

        return $candidates[0] ?? '127.0.0.1';
    }

    /**
     * Ambil IPv4 LAN mesin host saat ini.
     */
    public static function getLocalLanIp(): ?string
    {
        static $cachedLanIp = null;
        if ($cachedLanIp !== null) {
            return $cachedLanIp ?: null;
        }

        $hostname = @gethostname();
        if ($hostname) {
            $resolved = @gethostbyname($hostname);
            if ($resolved && $resolved !== $hostname && filter_var($resolved, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $cachedLanIp = $resolved;
                return $cachedLanIp;
            }
        }

        $cachedLanIp = '';
        return null;
    }

    /**
     * Ambil nama komputer/host lokal jika akses berasal dari mesin ini.
     */
    public static function getLocalHostName(): ?string
    {
        static $cachedHost = null;
        if ($cachedHost !== null) {
            return $cachedHost ?: null;
        }

        $hostname = @gethostname();
        $cachedHost = ($hostname && $hostname !== 'localhost') ? $hostname : '';

        return $cachedHost ?: null;
    }

    /**
     * Parse nama perangkat & sistem operasi dari User-Agent dan Client Hints.
     */
    public static function resolveDeviceName(?string $userAgent, ?string $ip = null, ?Request $request = null): string
    {
        $ua = trim((string) $userAgent);
        if ($ua === '') {
            return '-';
        }

        $os = 'Perangkat tidak dikenal';
        $arch = '';

        if (preg_match('/Win64|x64|WOW64|x86_64|amd64/i', $ua)) {
            $arch = '64-bit';
        } elseif (preg_match('/ARM64|aarch64/i', $ua)) {
            $arch = 'ARM64';
        }

        if (preg_match('/Windows NT 10\.0/i', $ua)) {
            $os = 'Windows 10/11' . ($arch ? " ({$arch})" : '');
        } elseif (preg_match('/Windows NT 6\.3/i', $ua)) {
            $os = 'Windows 8.1' . ($arch ? " ({$arch})" : '');
        } elseif (preg_match('/Windows NT 6\.2/i', $ua)) {
            $os = 'Windows 8' . ($arch ? " ({$arch})" : '');
        } elseif (preg_match('/Windows NT 6\.1/i', $ua)) {
            $os = 'Windows 7' . ($arch ? " ({$arch})" : '');
        } elseif (preg_match('/Android\s+([0-9\.]+)(?:;\s*([^;\)]+))?/i', $ua, $m)) {
            $ver = $m[1] ?? '';
            $model = isset($m[2]) ? trim(str_replace('Build/', '', $m[2])) : '';
            $os = 'Android ' . $ver . ($model && ! preg_match('/^(K|wv|Mobile)$/i', $model) ? " ({$model})" : '');
        } elseif (preg_match('/iPhone(?:\s+OS\s+([0-9_]+))?/i', $ua, $m)) {
            $ver = ! empty($m[1]) ? ' ' . str_replace('_', '.', $m[1]) : '';
            $os = 'Apple iPhone (iOS' . $ver . ')';
        } elseif (preg_match('/iPad/i', $ua)) {
            $os = 'Apple iPad (iPadOS)';
        } elseif (preg_match('/Macintosh|Mac OS X\s*([0-9_\.]+)?/i', $ua, $m)) {
            $os = 'Apple Mac (macOS)';
        } elseif (preg_match('/CrOS/i', $ua)) {
            $os = 'Chromebook (ChromeOS)';
        } elseif (preg_match('/Ubuntu/i', $ua)) {
            $os = 'Ubuntu Linux' . ($arch ? " ({$arch})" : '');
        } elseif (preg_match('/Linux/i', $ua)) {
            $os = 'Linux' . ($arch ? " ({$arch})" : '');
        }

        // Cek apakah ada model perangkat dari Client Hints header
        $modelHint = $request ? trim((string) $request->header('Sec-CH-UA-Model'), '" ') : '';
        if ($modelHint !== '' && ! str_contains($os, $modelHint)) {
            $os = $modelHint . ' • ' . $os;
        }

        // Jika diakses dari mesin host lokal, sertakan nama komputer (misal DESKTOP-9TCKD9U)
        $lanIp = self::getLocalLanIp();
        $isLocalMachine = $ip === null
            || in_array($ip, ['127.0.0.1', '::1', 'localhost'], true)
            || ($lanIp && $ip === $lanIp);

        if ($isLocalMachine && preg_match('/Windows|Mac|Linux/i', $os)) {
            $hostName = self::getLocalHostName();
            if ($hostName && ! str_starts_with($os, $hostName)) {
                return $hostName . ' • ' . $os;
            }
        }

        return $os;
    }

    /**
     * Parse nama dan versi Web Browser dari User-Agent.
     */
    public static function resolveBrowser(?string $userAgent, ?Request $request = null): string
    {
        $ua = trim((string) $userAgent);
        if ($ua === '') {
            return '-';
        }

        if (preg_match('/EdgA?\/([0-9]+\.[0-9]+)/i', $ua, $m)) {
            return 'Microsoft Edge ' . $m[1];
        }
        if (preg_match('/OPR\/([0-9]+\.[0-9]+)|Opera\/([0-9]+\.[0-9]+)/i', $ua, $m)) {
            $ver = ! empty($m[1]) ? $m[1] : ($m[2] ?? '');
            return 'Opera ' . $ver;
        }
        if (preg_match('/Brave\/([0-9]+\.[0-9]+)/i', $ua, $m)) {
            return 'Brave Browser ' . $m[1];
        }
        if (preg_match('/Vivaldi\/([0-9]+\.[0-9]+)/i', $ua, $m)) {
            return 'Vivaldi ' . $m[1];
        }
        if (preg_match('/SamsungBrowser\/([0-9]+\.[0-9]+)/i', $ua, $m)) {
            return 'Samsung Internet ' . $m[1];
        }
        if (preg_match('/UCBrowser\/([0-9]+\.[0-9]+)/i', $ua, $m)) {
            return 'UC Browser ' . $m[1];
        }
        if (preg_match('/Firefox\/([0-9]+\.[0-9]+)|FxiOS\/([0-9]+\.[0-9]+)/i', $ua, $m)) {
            $ver = ! empty($m[1]) ? $m[1] : ($m[2] ?? '');
            return 'Mozilla Firefox ' . $ver;
        }
        if (preg_match('/Chrome\/([0-9]+\.[0-9]+)|CriOS\/([0-9]+\.[0-9]+)/i', $ua, $m)) {
            $ver = ! empty($m[1]) ? $m[1] : ($m[2] ?? '');
            return 'Google Chrome ' . $ver;
        }
        if (preg_match('/Version\/([0-9]+\.[0-9]+).*Safari/i', $ua, $m)) {
            return 'Apple Safari ' . $m[1];
        }
        if (preg_match('/Safari\/([0-9]+\.[0-9]+)/i', $ua, $m)) {
            return 'Apple Safari';
        }
        if (preg_match('/MSIE\s+([0-9]+\.[0-9]+)|Trident.*rv:([0-9]+\.[0-9]+)/i', $ua, $m)) {
            $ver = ! empty($m[1]) ? $m[1] : ($m[2] ?? '');
            return 'Internet Explorer ' . $ver;
        }

        return substr($ua, 0, 40);
    }

    /**
     * Format tanggal & jam login terakhir ke Bahasa Indonesia (WIB).
     */
    public static function formatLoginDateTime($dateTime): string
    {
        if (! $dateTime) {
            return 'Belum pernah login';
        }

        $carbon = $dateTime instanceof CarbonInterface
            ? $dateTime->copy()
            : Carbon::parse($dateTime);

        $carbon = $carbon->timezone(config('app.timezone', 'Asia/Jakarta'));

        $bulanIndo = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $tgl = $carbon->format('d');
        $bln = $bulanIndo[(int) $carbon->format('n')] ?? $carbon->format('M');
        $thn = $carbon->format('Y');
        $jam = $carbon->format('H:i:s');

        return "{$tgl} {$bln} {$thn}, {$jam} WIB";
    }
}
