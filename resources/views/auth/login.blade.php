<x-guest-layout>
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xl p-8 sm:p-9" x-data="{ showPass: false }">
        <!-- Logo Perusahaan di Dalam Form -->
        <div class="flex justify-center mb-3">
            <img src="{{ asset('images/kabarpriangan.png') }}" 
                 alt="Logo Kabar Priangan" 
                 class="h-14 w-auto object-contain" 
                 onerror="this.src='{{ asset('logo.png') }}'">
        </div>

        <!-- Teks Judul & Deskripsi dengan Warna Hitam Kontras Tinggi -->
        <div class="text-center mb-6">
            <h2 class="text-[17px] font-bold text-black tracking-tight">
                Login ke Akun Anda
            </h2>
            <p class="text-xs text-black font-medium mt-1">
                Silakan masukkan email dan kata sandi untuk mengakses sistem.
            </p>
        </div>

        <!-- Status Sesi -->
        <x-auth-session-status class="mb-4 text-xs text-emerald-800 bg-emerald-50 p-2.5 rounded-lg border border-emerald-300 font-semibold" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div class="space-y-1">
                <label for="email" class="block text-xs font-bold text-black">
                    Email
                </label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="Masukkan Email Anda"
                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm text-black placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs font-semibold text-red-600" />
            </div>

            <!-- Password dengan Tombol Lihat Password (Icon Mata) -->
            <div class="space-y-1">
                <label for="password" class="block text-xs font-bold text-black">
                    Password
                </label>
                <div class="relative">
                    <input id="password" 
                           :type="showPass ? 'text' : 'password'" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Masukkan Password"
                           class="w-full px-3.5 py-2.5 pr-11 bg-white border border-slate-300 rounded-lg text-sm text-black placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition" />
                    <button type="button" 
                            @click="showPass = !showPass" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-black/70 hover:text-black focus:outline-none transition cursor-pointer"
                            :title="showPass ? 'Sembunyikan password' : 'Lihat password'"
                            tabindex="-1">
                        <!-- Eye Open (Lihat) -->
                        <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <!-- Eye Slash (Sembunyikan) -->
                        <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs font-semibold text-red-600" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center pt-1">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember" 
                       class="w-4 h-4 text-kp-blue-600 border-slate-300 rounded focus:ring-kp-blue-500" />
                <label for="remember_me" class="ms-2 text-xs font-semibold text-black select-none cursor-pointer">
                    Ingat saya
                </label>
            </div>

            <!-- Tombol Login -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-2.5 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-lg shadow-sm transition duration-150 focus:outline-none focus:ring-2 focus:ring-kp-blue-400 focus:ring-offset-2">
                    Masuk ke Sistem
                </button>
            </div>
        </form>
    </div>

    @if ($errors->any() || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                AppAlert.error({
                    title: 'Login Gagal',
                    subtitle: 'Autentikasi tidak berhasil',
                    message: '{{ $errors->first() ?: session('error') }}',
                    buttonText: 'Coba Lagi'
                });
            });
        </script>
    @endif
</x-guest-layout>
