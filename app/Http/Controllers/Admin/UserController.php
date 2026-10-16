<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna & divisi internal.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');

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

        $users = $query->latest()->paginate(12)->withQueryString();

        // Hitung statistik per divisi
        $stats = [
            'total' => User::count(),
            'admin' => User::whereIn('role', ['administrator', 'admin'])->count(),
            'iklan' => User::where('role', 'iklan')->count(),
            'keuangan' => User::whereIn('role', ['keuangan', 'admin_keuangan', 'petugas_inkaso', 'kepala_divisi'])->count(),
            'accounting' => User::where('role', 'accounting')->count(),
            'sirkulasi' => User::where('role', 'sirkulasi')->count(),
            'kasir' => User::where('role', 'kasir')->count(),
        ];

        $rolesList = [
            'administrator' => 'Administrator (Superadmin)',
            'iklan' => 'Divisi Iklan',
            'keuangan' => 'Divisi Keuangan',
            'accounting' => 'Divisi Accounting',
            'sirkulasi' => 'Divisi Sirkulasi',
            'kasir' => 'Divisi Kasir',
        ];

        return view('pages.admin.users.index', compact('users', 'stats', 'search', 'roleFilter', 'rolesList'));
    }

    /**
     * Simpan pengguna / staf divisi baru (Hanya Admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:administrator,admin,iklan,keuangan,accounting,sirkulasi,kasir'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'role.required' => 'Pilih divisi / wewenang pengguna.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun staf baru berhasil ditambahkan ke divisi ' . ucfirst($validated['role']) . '.');
    }

    /**
     * Perbarui data pengguna & wewenang divisi.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:administrator,admin,iklan,keuangan,accounting,sirkulasi,kasir'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan pengguna lain.',
            'role.required' => 'Pilih divisi / wewenang pengguna.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'Data akun staf ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Hapus akun staf dari sistem.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun staf ' . $userName . ' berhasil dihapus dari sistem.');
    }
}
