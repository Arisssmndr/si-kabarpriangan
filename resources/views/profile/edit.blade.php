<x-app-layout>
    <div class="max-w-4xl space-y-6">
        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex items-center gap-3.5">
            <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                    Profil Pengguna
                </h1>
                <p class="text-xs font-normal text-slate-500 mt-0.5">
                    Pengaturan identitas akun, kata sandi, dan keamanan sistem
                </p>
            </div>
        </div>

        <div class="p-6 bg-white border border-slate-200 rounded-xl shadow-xs">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 bg-white border border-slate-200 rounded-xl shadow-xs">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 bg-white border border-slate-200 rounded-xl shadow-xs">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
