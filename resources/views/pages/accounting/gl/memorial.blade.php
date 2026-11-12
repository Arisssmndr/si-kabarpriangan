<x-app-layout>
    <div class="space-y-5" x-data="{ activeTab: 'manual' }">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Jurnal Memorial & Realisasi UM
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            General Ledger
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencatatan memorial umum dan realisasi pendapatan atas uang muka iklan (PSAK 72).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('accounting.gl.jurnal') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                    &larr; Kembali ke Jurnal
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Tab Navigasi -->
        <div class="flex items-center gap-2 border-b border-slate-200">
            <button type="button" 
                    @click="activeTab = 'manual'"
                    :class="activeTab === 'manual' ? 'border-kp-blue-600 text-kp-blue-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium'"
                    class="px-4 py-2.5 text-xs border-b-2 transition cursor-pointer">
                1. Jurnal Memorial Manual
            </button>
            <button type="button" 
                    @click="activeTab = 'realisasi'"
                    :class="activeTab === 'realisasi' ? 'border-kp-blue-600 text-kp-blue-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium'"
                    class="px-4 py-2.5 text-xs border-b-2 transition cursor-pointer">
                2. Realisasi Uang Muka Iklan (PSAK 72)
            </button>
        </div>

        <!-- Form Tab 1: Jurnal Memorial Manual -->
        <div x-show="activeTab === 'manual'" class="max-w-3xl bg-white border border-slate-200 rounded-xl p-6 shadow-xs mx-auto">
            <form method="POST" action="{{ route('accounting.gl.memorial.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Jurnal</label>
                        <input type="date" 
                               name="tanggal" 
                               value="{{ date('Y-m-d') }}" 
                               required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Referensi / Dokumen</label>
                        <input type="text" 
                               name="referensi" 
                               placeholder="Contoh: MEMO-202611-01" 
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Uraian / Keterangan Transaksi</label>
                    <textarea name="keterangan" 
                              rows="2" 
                              required 
                              placeholder="Keterangan transaksi jurnal memorial..."
                              class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase">Posisi Pembukuan Berpasangan</h3>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Akun Debet</label>
                        <select name="akun_debet" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                            <option value="">-- Pilih Akun Debet --</option>
                            @foreach($daftarAkun as $akun)
                                <option value="{{ $akun->kode_perkiraan }}">
                                    {{ $akun->kode_perkiraan }} - {{ $akun->nama_perkiraan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Akun Kredit</label>
                        <select name="akun_kredit" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                            <option value="">-- Pilih Akun Kredit --</option>
                            @foreach($daftarAkun as $akun)
                                <option value="{{ $akun->kode_perkiraan }}">
                                    {{ $akun->kode_perkiraan }} - {{ $akun->nama_perkiraan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Nilai (Rp)</label>
                        <input type="number" 
                               name="nominal" 
                               placeholder="0" 
                               min="1" 
                               required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Simpan &amp; Posting Jurnal Memorial
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Tab 2: Realisasi Uang Muka Iklan -->
        <div x-show="activeTab === 'realisasi'" x-cloak class="max-w-3xl bg-white border border-slate-200 rounded-xl p-6 shadow-xs mx-auto">
            <div class="mb-4 p-3 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-xs text-kp-blue-800">
                <strong>Standar PSAK 72:</strong> Pencatatan realisasi pendapatan setelah iklan tayang/terbit. Sistem akan mendebet akun <em>Uang Muka Iklan (2.00.01.01.01.01.00001)</em> dan mengkredit <em>Pendapatan Iklan Koran (4.00.01.01.01.00001)</em>.
            </div>

            <form method="POST" action="{{ route('accounting.gl.realisasi-uang-muka') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Realisasi</label>
                        <input type="date" 
                               name="tanggal" 
                               value="{{ date('Y-m-d') }}" 
                               required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Faktur Iklan</label>
                        <input type="text" 
                               name="no_faktur" 
                               required
                               placeholder="Contoh: FI-202611-0001" 
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Realisasi (Rp)</label>
                    <input type="number" 
                           name="nominal" 
                           placeholder="0" 
                           min="1" 
                           required
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Posting Realisasi Uang Muka
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>

