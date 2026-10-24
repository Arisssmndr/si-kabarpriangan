<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna & divisi internal dengan filter role & status.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $statusFilter = $request->input('status');
        $presenceFilter = $request->input('presence'); // 'all', 'online', 'offline'

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter && $roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($presenceFilter === 'online') {
            $query->where('last_seen_at', '>=', now()->subMinutes(3));
        } elseif ($presenceFilter === 'offline') {
            $query->where(function ($q) {
                $q->whereNull('last_seen_at')
                  ->orWhere('last_seen_at', '<', now()->subMinutes(3));
            });
        }

        $users = $query->latest()->paginate(12)->withQueryString();

        // Hitung statistik per divisi & per status siklus hidup akun + status kehadiran real-time
        $stats = [
            'total' => User::count(),
            'online' => User::where('last_seen_at', '>=', now()->subMinutes(3))->count(),
            'offline' => User::where(function ($q) {
                $q->whereNull('last_seen_at')->orWhere('last_seen_at', '<', now()->subMinutes(3));
            })->count(),
            'admin' => User::whereIn('role', ['administrator', 'admin'])->count(),
            'iklan' => User::where('role', 'iklan')->count(),
            'keuangan' => User::whereIn('role', ['keuangan', 'admin_keuangan', 'petugas_inkaso', 'kepala_divisi'])->count(),
            'sirkulasi' => User::where('role', 'sirkulasi')->count(),
            'kasir' => User::where('role', 'kasir')->count(),
            'billing' => User::where('role', 'billing')->count(),
            'accounting' => User::where('role', 'accounting')->count(),

            // Status Stats
            'active' => User::where('status', 'active')->count(),
            'review' => User::where('status', 'review')->count(),
            'disable' => User::where('status', 'disable')->count(),
        ];

        $rolesList = [
            'administrator' => 'Administrator (Superadmin)',
            'iklan' => 'Divisi Iklan (Koran, TV, Online)',
            'sirkulasi' => 'Divisi Sirkulasi (Distribusi & Agen)',
            'keuangan' => 'Divisi Keuangan',
            'kasir' => 'Divisi Kasir',
            'billing' => 'Divisi Billing (Penagihan/Faktur)',
            'accounting' => 'Divisi Accounting',
        ];

        return view('pages.admin.users.index', compact('users', 'stats', 'search', 'roleFilter', 'statusFilter', 'presenceFilter', 'rolesList'));
    }

    /**
     * Simpan pengguna / staf divisi baru dengan validasi password ketat.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:administrator,admin,iklan,keuangan,accounting,sirkulasi,kasir,billing'],
            'status' => ['required', 'string', 'in:active,review,disable'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/', // Huruf + Angka + Simbol
            ],
        ], [
            'name.required' => 'Nama lengkap staf wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'role.required' => 'Pilih divisi / wewenang staf.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter sesuai standar keamanan.',
            'password.regex' => 'Kata sandi wajib menggabungkan huruf, angka, dan karakter khusus/simbol (contoh pola: 1r12skabar17riangan).',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::record(
            'USER_CREATED',
            "Admin membuat akun staf baru: {$user->name} ({$user->email}) di divisi {$user->role_label} dengan status {$user->status}",
            ['user_id' => $user->id, 'role' => $user->role, 'status' => $user->status]
        );

        return redirect()->route('admin.users.index')->with('success', 'Akun staf baru berhasil ditambahkan ke divisi ' . $user->role_label . '.');
    }

    /**
     * Perbarui data pengguna, wewenang divisi & status akun.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:administrator,admin,iklan,keuangan,accounting,sirkulasi,kasir,billing'],
            'status' => ['required', 'string', 'in:active,review,disable'],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
        ], [
            'name.required' => 'Nama lengkap staf wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh staf lain.',
            'role.required' => 'Pilih divisi / wewenang staf.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.regex' => 'Kata sandi baru wajib menggabungkan huruf, angka, dan karakter khusus/simbol.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        // Jika status diubah ke active, reset status penguncian
        if ($validated['status'] === 'active' && $user->locked_until) {
            $userData['locked_until'] = null;
            $userData['failed_login_attempts'] = 0;
        }

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $oldStatus = $user->status;
        $user->update($userData);

        ActivityLog::record(
            'USER_UPDATED',
            "Admin memperbarui data staf {$user->name} (Status: {$oldStatus} -> {$user->status})",
            ['user_id' => $user->id, 'old_status' => $oldStatus, 'new_status' => $user->status]
        );

        return redirect()->route('admin.users.index')->with('success', 'Data akun staf ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Ubah status akun secara cepat (Active, Review, Disable).
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,review,disable'],
        ]);

        if ($user->id === auth()->id() && $validated['status'] === 'disable') {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif.');
        }

        $oldStatus = $user->status;
        $user->status = $validated['status'];

        if ($validated['status'] === 'active') {
            $user->locked_until = null;
            $user->failed_login_attempts = 0;
        }

        $user->save();

        ActivityLog::record(
            'USER_STATUS_CHANGED',
            "Admin mengubah status akun {$user->name} dari {$oldStatus} menjadi {$user->status}",
            ['user_id' => $user->id, 'old_status' => $oldStatus, 'new_status' => $user->status]
        );

        return redirect()->route('admin.users.index')->with('success', "Status akun staf {$user->name} berhasil diubah menjadi " . ucfirst($user->status) . '.');
    }

    /**
     * Hapus akun staf dengan perlindungan integritas (Zero Hard Delete on audit records).
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        // Cek keterkaitan data audit transaksi: dilarang hard-delete jika memiliki riwayat
        $hasActivity = ActivityLog::where('user_id', $user->id)->exists();
        $hasIklanKoran = DB::table('transaksikoran')->where('user_id', $user->id)->exists();
        $hasIklanPriangan = DB::table('transaksipriangan')->where('user_id', $user->id)->exists();
        $hasIklanOnline = DB::table('transaksionline')->where('user_id', $user->id)->exists();

        if ($hasActivity || $hasIklanKoran || $hasIklanPriangan || $hasIklanOnline) {
            // Ubah otomatis menjadi Disable demi mematuhi instruksi pembimbing
            $user->update(['status' => 'disable']);

            ActivityLog::record(
                'USER_DISABLED_PRESERVED',
                "Upaya hapus akun {$user->name} dialihkan menjadi DISABLE karena akun memiliki rekam jejak audit/transaksi finansial.",
                ['user_id' => $user->id]
            );

            return redirect()->route('admin.users.index')->with('warning', "Akun {$user->name} memiliki riwayat transaksi/audit sistem sehingga tidak boleh dihapus secara permanen demi integritas laporan. Akun telah otomatis dinonaktifkan (Disable).");
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::record(
            'USER_DELETED',
            "Akun staf {$userName} (tanpa riwayat transaksi) telah dihapus dari sistem oleh Admin.",
            ['user_id' => $id]
        );

        return redirect()->route('admin.users.index')->with('success', 'Akun staf ' . $userName . ' berhasil dihapus dari sistem.');
    }

    /**
     * Endpoint API JSON untuk pembaruan status online staf secara real-time di UI Admin.
     */
    public function getOnlineStatus(Request $request)
    {
        $users = User::select(['id', 'name', 'last_seen_at', 'last_login_at', 'last_login_ip'])->get();

        $data = $users->mapWithKeys(function ($u) {
            return [
                $u->id => [
                    'is_online' => $u->isOnline(),
                    'last_seen_text' => $u->last_seen_text,
                    'last_login' => $u->last_login_at ? $u->last_login_at->translatedFormat('d M Y, H:i') : 'Belum pernah login',
                ],
            ];
        });

        $onlineCount = $users->filter(fn($u) => $u->isOnline())->count();

        return response()->json([
            'online_count' => $onlineCount,
            'total_count' => $users->count(),
            'users' => $data,
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Putuskan sesi aktif staf secara paksa oleh Administrator (Force Logout).
     */
    public function forceLogout($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat memutuskan sesi akun Anda sendiri.');
        }

        \Illuminate\Support\Facades\Cache::forget('user-is-online-' . $user->id);
        $user->forceFill(['last_seen_at' => null])->saveQuietly();

        ActivityLog::record(
            'USER_FORCE_LOGOUT',
            "Administrator memutuskan sesi aktif staf {$user->name} ({$user->email})",
            ['target_user_id' => $user->id],
            auth()->user()
        );

        return redirect()->route('admin.users.index')->with('success', "Sesi aktif staf {$user->name} berhasil diputuskan. Status pengguna kini Offline.");
    }
}

