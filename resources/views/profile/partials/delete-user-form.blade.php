<section>
    <div class="flex items-center gap-3.5 pb-5 border-b border-rose-100">
        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        <div>
            <h2 class="text-base font-bold text-rose-900 tracking-tight">
                Zona Tindakan Kritis (Penghapusan Akun)
            </h2>
            <p class="text-xs text-rose-700/80 mt-0.5">
                Penghapusan akun bersifat permanen. Seluruh hak akses Anda pada portal Kabar Priangan akan dicabut.
            </p>
        </div>
    </div>

    <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 rounded-xl bg-rose-50/60 border border-rose-200 text-xs text-rose-900">
        <div class="space-y-1">
            <p class="font-bold">Apakah Anda yakin ingin menonaktifkan / menghapus akun ini?</p>
            <p class="text-rose-700 text-[11px]">Pastikan tidak ada laporan sirkulasi, tagihan, atau data pending yang masih membutuhkan otorisasi akun Anda.</p>
        </div>
        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="shrink-0 px-4 py-2 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold rounded-xl shadow-xs transition-colors cursor-pointer text-xs">
            Hapus Akun Pengguna
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 text-rose-600 mb-4">
                <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Konfirmasi Penghapusan Akun
                    </h2>
                    <p class="text-xs text-slate-500">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Setelah akun Anda dihapus, semua data dan hak akses Anda akan dihapus secara permanen dari server Kabar Priangan. Silakan masukkan kata sandi Anda untuk memverifikasi tindakan ini.
            </p>

            <div class="mt-5">
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kata Sandi Akun
                </label>
                <input id="password"
                       name="password"
                       type="password"
                       class="w-full rounded-xl border-slate-300 shadow-2xs text-sm text-slate-900 focus:border-rose-500 focus:ring-rose-500 transition"
                       placeholder="Masukkan kata sandi untuk konfirmasi" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-xs" />
            </div>

            <div class="mt-6 flex justify-end gap-2.5">
                <button type="button" 
                        x-on:click="$dispatch('close')" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition cursor-pointer">
                    Batal
                </button>

                <button type="submit" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold rounded-xl text-xs shadow-xs transition cursor-pointer">
                    Ya, Hapus Akun Saya
                </button>
            </div>
        </form>
    </x-modal>
</section>
