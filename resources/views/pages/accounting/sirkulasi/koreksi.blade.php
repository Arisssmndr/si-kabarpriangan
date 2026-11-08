<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Koreksi Retur Sirkulasi (CN-SIR)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                            Kalkulator Retur & Kartu Mutasi
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Penerbitan nota kredit retur koran agen dengan kalkulator eksemplar dan mutasi saldo berjalan real-time.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    Format Bukti: <strong class="text-slate-900 font-mono">{{ $nextNoBukti }}</strong>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-sky-50 border border-[#0E527B]/30 text-[#0E527B] rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5" x-data="{
            agenId: '',
            loadingAgen: false,
            sisaPiutangLalu: 0,
            penjualanBulanIni: 0,
            pembayaranBulanIni: 0,
            koreksiSebelumnya: 0,
            saldoBerjalanDb: 0,
            eksemplar: 0,
            harga: 3500,
            get totalKoreksi() {
                return (this.eksemplar || 0) * (this.harga || 0);
            },
            get saldoAkhirSimulasi() {
                if (!this.agenId) return 0;
                return (this.saldoBerjalanDb || 0) - this.totalKoreksi;
            },
            formatRupiah(num) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
            },
            onAgenChange() {
                if (!this.agenId) {
                    this.sisaPiutangLalu = 0;
                    this.penjualanBulanIni = 0;
                    this.pembayaranBulanIni = 0;
                    this.koreksiSebelumnya = 0;
                    this.saldoBerjalanDb = 0;
                    return;
                }
                this.loadingAgen = true;
                fetch(`/accounting/sirkulasi/agen/${this.agenId}/saldo`)
                    .then(res => res.json())
                    .then(data => {
                        this.loadingAgen = false;
                        if (data.success) {
                            this.sisaPiutangLalu = data.sisa_piutang_lalu || 0;
                            this.penjualanBulanIni = data.penjualan_bulan_ini || 0;
                            this.pembayaranBulanIni = data.pembayaran_bulan_ini || 0;
                            this.koreksiSebelumnya = data.koreksi_bulan_ini || 0;
                            this.saldoBerjalanDb = data.saldo_berjalan || 0;
                        }
                    })
                    .catch(() => {
                        this.loadingAgen = false;
                    });
            }
        }">
            <!-- Form Input Retur dengan Exemplar Calculator -->
            <div class="lg:col-span-5 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center justify-between">
                    <span>Input Retur Koran Agen</span>
                    <span class="text-[10px] font-semibold text-[#0E527B] bg-sky-50 px-2 py-0.5 rounded-md border border-sky-200">CN-SIR</span>
                </h2>

                <form method="POST" action="{{ route('accounting.sirkulasi.koreksi.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Bukti CN-SIR</label>
                        <input type="text" 
                               name="nomor_bukti" 
                               value="{{ $nextNoBukti }}" 
                               readonly
                               class="w-full h-10 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-700 cursor-not-allowed" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Agen Sirkulasi</label>
                        <select name="agen_id" 
                                x-model="agenId"
                                @change="onAgenChange()"
                                required 
                                class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-[#0E527B] focus:ring-1 focus:ring-[#0E527B] transition">
                            <option value="">-- Pilih Agen --</option>
                            @foreach($daftarAgen as $agen)
                                <option value="{{ $agen->id }}">
                                    {{ $agen->kode_agen }} - {{ $agen->nama_agen }} (Wilayah: {{ $agen->wilayah ?: 'Tasikmalaya' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Live Mutasi Saldo Agen Card (Standar Layar DOS KARTU.EXE Foto 12.22.23) -->
                    <template x-if="agenId">
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2 text-xs">
                            <div class="flex items-center justify-between font-bold text-slate-900 border-b border-slate-200 pb-1.5">
                                <span class="text-[11px] uppercase tracking-wide text-[#0E527B]">Kartu Mutasi Berjalan Agen</span>
                                <span x-show="loadingAgen" class="text-[10px] text-slate-400">Loading...</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>1. Sisa Piutang Lalu:</span>
                                <span class="font-mono font-semibold text-slate-900" x-text="formatRupiah(sisaPiutangLalu)"></span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>2. Penjualan Bln Ini:</span>
                                <span class="font-mono font-semibold text-slate-900" x-text="formatRupiah(penjualanBulanIni)"></span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>3. Setoran Kas Bln Ini:</span>
                                <span class="font-mono font-semibold text-emerald-700" x-text="formatRupiah(pembayaranBulanIni)"></span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>4. Koreksi Sebelumnya:</span>
                                <span class="font-mono font-semibold text-rose-700" x-text="formatRupiah(koreksiSebelumnya)"></span>
                            </div>
                            <div class="flex justify-between text-slate-600 font-semibold border-t border-slate-200 pt-1.5">
                                <span>Saldo Berjalan Saat Ini:</span>
                                <span class="font-mono font-bold text-slate-900" x-text="formatRupiah(saldoBerjalanDb)"></span>
                            </div>
                            <div class="flex justify-between text-slate-800 font-bold bg-amber-50 p-2 rounded-lg border border-amber-200">
                                <span class="text-amber-900">Saldo Akhir Pasca Retur:</span>
                                <span class="font-mono text-[#0E527B]" x-text="formatRupiah(saldoAkhirSimulasi)"></span>
                            </div>
                        </div>
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Retur</label>
                        <input type="date" 
                               name="tanggal" 
                               value="{{ date('Y-m-d') }}" 
                               required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-[#0E527B] focus:ring-1 focus:ring-[#0E527B] transition" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jumlah Eks</label>
                            <input type="number" 
                                   name="jumlah_eksemplar" 
                                   x-model.number="eksemplar"
                                   min="1" 
                                   placeholder="0"
                                   required
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-[#0E527B] focus:ring-1 focus:ring-[#0E527B] transition" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Harga/Eks (Rp)</label>
                            <input type="number" 
                                   name="harga_eksemplar" 
                                   x-model.number="harga"
                                   min="1" 
                                   required
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-[#0E527B] focus:ring-1 focus:ring-[#0E527B] transition" />
                        </div>
                    </div>

                    <!-- Exemplar Calculator Box -->
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="text-[11px] font-semibold text-amber-800">Total Potongan Retur (CN-SIR):</div>
                        <div class="text-base font-black font-mono text-[#0E527B] mt-0.5" x-text="formatRupiah(totalKoreksi)">
                            Rp 0
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan / No. BAPR</label>
                        <textarea name="alasan" 
                                  rows="2" 
                                  required 
                                  placeholder="Berita acara retur koran sisa edisi..."
                                  class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-[#0E527B] focus:ring-1 focus:ring-[#0E527B] transition"></textarea>
                    </div>

                    <div class="pt-1">
                        <button type="submit" 
                                class="w-full h-10 bg-[#0E527B] hover:bg-[#0b4263] active:bg-[#093550] text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                            Terbitkan Nota Kredit CN-SIR
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Riwayat CN-SIR -->
            <div class="lg:col-span-7 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Riwayat Nota Kredit (CN-SIR)</h2>
                        <p class="text-xs text-slate-500">Daftar koreksi retur agen yang telah diposting ke kartu piutang.</p>
                    </div>
                </div>

                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-[11px] whitespace-nowrap uppercase">
                            <tr>
                                <th class="py-2.5 px-3 w-10 text-center">No</th>
                                <th class="py-2.5 px-3">No. Bukti CN</th>
                                <th class="py-2.5 px-3">Nama Agen</th>
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3 text-right">Nilai Retur</th>
                                <th class="py-2.5 px-3">Keterangan</th>
                                <th class="py-2.5 px-3 text-center w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($daftarCn as $key => $cn)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-2.5 px-3 text-center text-slate-400">{{ $key + 1 }}</td>
                                    <td class="py-2.5 px-3 font-bold text-slate-900 font-mono">{{ $cn->nomor_bukti }}</td>
                                    <td class="py-2.5 px-3 font-medium text-slate-900">{{ $cn->nama_entitas }}</td>
                                    <td class="py-2.5 px-3">{{ \Carbon\Carbon::parse($cn->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($cn->nominal_koreksi, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3 max-w-xs truncate text-slate-500">{{ $cn->keterangan }}</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <a href="{{ route('accounting.sirkulasi.koreksi.cetak', $cn->id) }}" 
                                           target="_blank"
                                           class="text-xs font-semibold text-[#0E527B] hover:underline">
                                            Cetak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400">
                                        Belum ada penerbitan nota kredit retur sirkulasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
