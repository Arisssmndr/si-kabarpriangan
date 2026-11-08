<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Saldo Awal Piutang Sirkulasi
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Sirkulasi
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencatatan dan pembaruan saldo awal piutang per agen koran.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    Total Piutang Awal: <strong class="text-slate-900 font-mono">Rp {{ number_format($totalPiutangAwal, 0, ',', '.') }}</strong>
                </div>
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

        <!-- Tabel Saldo Awal Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Agen Sirkulasi</h2>
                    <p class="text-xs text-slate-500">Master piutang agen untuk pembukuan periode berjalan.</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Kode Agen</th>
                            <th class="py-3 px-3.5">Nama Agen</th>
                            <th class="py-3 px-3.5">Wilayah</th>
                            <th class="py-3 px-3.5 text-center">Jatah Default</th>
                            <th class="py-3 px-3.5 text-right">Saldo Piutang</th>
                            <th class="py-3 px-3.5 text-center w-28">Update Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarAgen as $key => $agen)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $agen->kode_agen }}</td>
                                <td class="py-3 px-3.5">{{ $agen->nama_agen }}</td>
                                <td class="py-3 px-3.5">{{ $agen->wilayah ?: '-' }}</td>
                                <td class="py-3 px-3.5 text-center font-mono">{{ number_format($agen->jatah_default) }} eks</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($agen->saldo_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <form method="POST" action="{{ route('accounting.sirkulasi.saldo-awal.proses') }}" class="flex items-center justify-center gap-1.5">
                                        @csrf
                                        <input type="hidden" name="agen_id" value="{{ $agen->id }}">
                                        <input type="number" 
                                               name="saldo_piutang" 
                                               value="{{ (int) $agen->saldo_piutang }}" 
                                               class="w-24 h-8 px-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-mono text-right focus:bg-white focus:outline-none focus:border-kp-blue-600 transition" />
                                        <button type="submit" 
                                                title="Simpan Saldo"
                                                class="h-8 px-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-lg shadow-2xs transition cursor-pointer">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada data agen terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
