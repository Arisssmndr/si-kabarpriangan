<x-guest-layout>
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xl p-8 sm:p-9">
        <!-- Logo Perusahaan di Dalam Form -->
        <div class="flex justify-center mb-3">
            <img src="{{ asset('images/kabarpriangan.png') }}" 
                 alt="Logo Kabar Priangan" 
                 class="h-14 w-auto object-contain" 
                 onerror="this.src='{{ asset('logo.png') }}'">
        </div>

        <!-- Teks Asli 1 Kalimat Sesuai Permintaan (Tanpa SIKAPRI / Teks Tambahan) -->
        <div class="text-center mb-6">
            <p class="text-[16px] font-semibold text-slate-800">
                Login to your Account
            </p>
            <span class="text-xs text-slate-400 block mt-1">
                Get started with our app, just start section and enjoy experience.
            </span>
        </div>

        <!-- Status Sesi -->
        <x-auth-session-status class="mb-4 text-xs text-green-700 bg-green-50 p-2.5 rounded-lg border border-green-200" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div class="space-y-1">
                <label for="email" class="block text-xs font-semibold text-slate-600">
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
                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-600" />
            </div>

            <!-- Password -->
            <div class="space-y-1">
                <label for="password" class="block text-xs font-semibold text-slate-600">
                    Password
                </label>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="Masukkan Password"
                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-600" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center pt-1">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember" 
                       class="w-4 h-4 text-kp-blue-600 border-slate-300 rounded focus:ring-kp-blue-500" />
                <label for="remember_me" class="ms-2 text-xs text-slate-600 select-none cursor-pointer">
                    Ingat saya
                </label>
            </div>

            <!-- Tombol Login -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-2.5 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-sm rounded-lg shadow-sm transition duration-150 focus:outline-none focus:ring-2 focus:ring-kp-blue-400 focus:ring-offset-2">
                    Login
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
