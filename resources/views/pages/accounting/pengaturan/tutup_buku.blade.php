<x-app-layout>
    <div class="space-y-5" x-data="{ openBukaModal: false, selectedPeriode: '' }">

        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Tutup Buku Periode (Closing)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Periode Lock
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Penguncian transaksi periode bulanan/tahunan untuk mencegah perubahan data lampau.
                    </p>
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Checklist & Action Tutup Buku -->
            <div class="lg:col-span-5 bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">
                    Proses Tutup Buku Bulanan
                </h2>

                <div class="space-y-3">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full {{ ($unpostedSummary['iklan_unpulled'] ?? 0) === 0 ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-amber-50 text-amber-800 border-amber-200' }} border flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                            {{ ($unpostedSummary['iklan_unpulled'] ?? 0) === 0 ? '✓' : '!' }}
                        </span>
                        <div>
                            <div class="text-xs font-bold text-slate-900">1. Penarikan Penjualan &amp; Pembayaran</div>
                            <div class="text-xs text-slate-500">
                                Pending iklan: <span class="font-mono font-bold">{{ $unpostedSummary['iklan_unpulled'] ?? 0 }}</span> &bull;
                                Kas masuk belum GL: <span class="font-mono font-bold">{{ ($unpostedSummary['kas_masuk_iklan_unpulled'] ?? 0) + ($unpostedSummary['kas_masuk_sirkulasi_unpulled'] ?? 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full {{ ($unpostedSummary['beban_unpulled_count'] ?? 0) === 0 ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-amber-50 text-amber-800 border-amber-200' }} border flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                            {{ ($unpostedSummary['beban_unpulled_count'] ?? 0) === 0 ? '✓' : '!' }}
                        </span>
                        <div>
                            <div class="text-xs font-bold text-slate-900">2. Verifikasi Beban Kasir &amp; Draft Jurnal</div>
                            <div class="text-xs text-slate-500">
                                BKK pending GL: <span class="font-mono font-bold">{{ $unpostedSummary['beban_unpulled_count'] ?? 0 }}</span> &bull;
                                Jurnal Draft: <span class="font-mono font-bold">{{ $unpostedSummary['draft_jurnal_count'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full {{ ($isGlBalanced ?? true) ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-rose-50 text-rose-700 border-rose-200' }} border flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                            {{ ($isGlBalanced ?? true) ? '✓' : '×' }}
                        </span>
                        <div>
                            <div class="text-xs font-bold text-slate-900">3. Keseimbangan General Ledger ({{ $periodeKey ?? date('Y-m') }})</div>
                            <div class="text-xs text-slate-500 font-mono">
                                D: Rp {{ number_format($glTotalDebet ?? 0, 0, ',', '.') }} | K: Rp {{ number_format($glTotalKredit ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('accounting.pengaturan.tutup-buku.proses') }}" class="pt-3 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Periode Bulan yang Ditutup</label>
                        <input type="month"
                               name="periode_key"
                               value="{{ $periodeKey ?? date('Y-m') }}"
                               required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <label class="flex items-start gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="post_closing_entry" value="1" class="mt-0.5 rounded border-slate-300 text-kp-blue-600">
                            <span>Buat <strong>Jurnal Penutup (Closing Entry)</strong> Laba/Rugi ke Ekuitas secara otomatis</span>
                        </label>
                        @if(($unpostedSummary['draft_jurnal_count'] ?? 0) > 0)
                            <label class="flex items-start gap-2 text-xs font-semibold text-amber-800 cursor-pointer">
                                <input type="checkbox" name="force_close" value="1" class="mt-0.5 rounded border-amber-400 text-amber-600">
                                <span>Paksa tutup meski masih ada {{ $unpostedSummary['draft_jurnal_count'] }} jurnal Draft</span>
                            </label>
                        @endif
                    </div>

                    <button type="submit"
                            onclick="return confirmSubmit(this, event, {
                                title: 'Kunci &amp; Tutup Buku Periode?',
                                text: 'PERINGATAN: Tutup buku akan mengunci seluruh transaksi jurnal pada periode ini. Data tidak dapat diubah kembali kecuali dengan otorisasi buka periode.',
                                theme: 'amber',
                                icon: 'warn',
                                confirmText: 'Ya, Kunci &amp; Tutup Buku'
                            })"
                            class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <span>Kunci &amp; Tutup Buku Periode</span>
                    </button>
                </form>
            </div>

            <!-- Pure Tabel Status Periode Pembukuan -->
            <div class="lg:col-span-7 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center">No</th>
                                <th class="py-3 px-3.5">Bulan &amp; Tahun</th>
                                <th class="py-3 px-3.5">Status</th>
                                <th class="py-3 px-3.5">Tanggal Tutup</th>
                                <th class="py-3 px-3.5">Ditutup Oleh</th>
                                <th class="py-3 px-3.5 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($periodeList as $key => $p)
                                @php
                                    $pKey = $p->periode_key ?: sprintf('%04d-%02d', $p->tahun, $p->bulan);
                                    $isLocked = ($p->status === 'terkunci');
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3.5 text-center text-xs text-slate-400 font-mono">{{ $key + 1 }}</td>
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $pKey }}</td>
                                    <td class="py-3 px-3.5">
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $isLocked ? 'bg-slate-100 text-slate-700 border border-slate-300' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                                            {{ $isLocked ? 'Terkunci (Closed)' : 'Aktif (Open)' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 font-mono text-slate-600">{{ $p->ditutup_pada ? \Carbon\Carbon::parse($p->ditutup_pada)->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="py-3 px-3.5 text-slate-800">{{ $p->ditutupOleh?->name ?? ($isLocked ? 'Administrator' : '-') }}</td>
                                    <td class="py-3 px-3.5 text-center">
                                        @if($isLocked)
                                            <x-action-dropdown>
                                                <button type="button"
                                                        @click="selectedPeriode = '{{ $pKey }}'; openBukaModal = true"
                                                        class="w-full flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition text-left cursor-pointer font-medium">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                    <span>Buka Periode</span>
                                                </button>
                                            </x-action-dropdown>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                        Belum ada riwayat periode pembukuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Buka Periode Terkontrol -->
        <div x-show="openBukaModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="openBukaModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white rounded-xl shadow-xl p-6 border border-slate-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Buka Periode Akuntansi Terkunci</h3>
                        <button type="button" @click="openBukaModal = false" class="text-slate-400 hover:text-slate-600 text-lg leading-none cursor-pointer">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('accounting.pengaturan.tutup-buku.buka') }}" class="space-y-4 mt-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Periode</label>
                            <input type="text" name="periode_key" x-model="selectedPeriode" readonly class="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-900" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pembukaan Periode (Wajib Audit Trail)</label>
                            <textarea name="alasan" rows="3" required minlength="5" placeholder="Contoh: Koreksi bukti kas keluar &amp; penyesuaian pajak akhir bulan" class="w-full p-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition"></textarea>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="submit" class="flex-1 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                Konfirmasi Buka Periode
                            </button>
                            <button type="button" @click="openBukaModal = false" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>