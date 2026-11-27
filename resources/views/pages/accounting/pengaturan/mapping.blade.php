<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Mapping Akun COA Otomatis
                        </h1>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Dynamic GL Routing
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Konfigurasi pemetaan kode perkiraan default untuk penarikan Iklan, Sirkulasi (Kredit), Kas/Bank, Hutang Vendor, dan Penutupan Buku.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('accounting.pengaturan.coa') }}"
                   class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-xs sm:text-sm font-semibold inline-flex items-center gap-2 transition shadow-xs">
                    <span>Kelola Master COA</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('accounting.pengaturan.mapping.update') }}" class="space-y-5">
            @csrf

            @foreach($mappings->groupBy('kategori_modul') as $modul => $items)
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                            Modul {{ strtoupper($modul) }}
                        </h2>
                        <span class="text-xs font-semibold text-slate-500 font-mono">{{ $items->count() }} Mapping</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($items as $map)
                            <div class="p-4 sm:px-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 hover:bg-slate-50/50 transition">
                                <div class="min-w-0 lg:w-1/2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-bold text-slate-900">{{ $map->label }}</span>
                                        <span class="text-xs font-mono px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200">{{ $map->mapping_key }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $map->keterangan ?: 'Akun perkiraan standar untuk transaksi modul ini.' }}</p>
                                </div>

                                <div class="lg:w-1/2">
                                    <select name="mappings[{{ $map->id }}]"
                                            class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 transition">
                                        @foreach($daftarAkun as $akun)
                                            <option value="{{ $akun->kode_perkiraan }}" {{ $map->kode_perkiraan === $akun->kode_perkiraan ? 'selected' : '' }}>
                                                {{ $akun->kode_perkiraan }} — {{ $akun->nama_perkiraan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-xl text-sm font-bold shadow-xs transition cursor-pointer">
                    Simpan Konfigurasi Mapping COA
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
