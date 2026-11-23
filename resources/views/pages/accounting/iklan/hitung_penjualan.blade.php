<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Bersih (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Hitung Penjualan Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Iklan
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                        Perhitungan omset harian dan pembentukan jurnal piutang multi-kanal.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($sudahDihitung)
                    <div class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 px-3 py-2 rounded-xl border border-kp-blue-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Sudah Dihitung ({{ $logHitung?->nomor_jurnal ?? 'Terekam GL' }})</span>
                    </div>
                @else
                    <div class="text-xs font-semibold text-slate-700 bg-slate-100 px-3 py-2 rounded-xl border border-slate-300 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span>Belum Dihitung</span>
                    </div>
                @endif
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

        <!-- Metric Cards (Report Info di Atas Filter) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jumlah Transaksi</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($totalCount) }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Omset Bruto</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalBruto, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">PPN 11%</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalPpn, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Piutang Netto</span>
                <div class="text-lg font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalNetto, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Mandiri (Format Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.iklan.hitung-penjualan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q', $q ?? '') }}" 
                           placeholder="Ketik no faktur, pemasang, atau akun..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Kanal Media
                    </label>
                    <select name="kanal" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ request('kanal', $kanal ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kanal</option>
                        <option value="koran" {{ request('kanal', $kanal ?? '') === 'koran' ? 'selected' : '' }}>Iklan Koran</option>
                        <option value="online" {{ request('kanal', $kanal ?? '') === 'online' ? 'selected' : '' }}>Iklan Online</option>
                        <option value="priangan" {{ request('kanal', $kanal ?? '') === 'priangan' ? 'selected' : '' }}>Priangan TV</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Status Pembayaran
                    </label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ request('status', $status ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="lunas" {{ request('status', $status ?? '') === 'lunas' ? 'selected' : '' }}>Lunas / UM</option>
                        <option value="piutang" {{ request('status', $status ?? '') === 'piutang' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tanggal Transaksi
                    </label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ $tanggalPilihan }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'kanal', 'status']) || request('tanggal') != date('Y-m-d'))
                        <a href="{{ route('accounting.iklan.hitung-penjualan') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tombol Aksi Posting GL (Terpisah Mandiri) -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
            <div class="text-xs text-slate-500">
                Data faktur harian tanggal <span class="font-mono font-bold text-slate-800">{{ \Carbon\Carbon::parse($tanggalPilihan)->isoFormat('D MMMM Y') }}</span>
            </div>
            <div>
                @if(!$sudahDihitung)
                    <form method="POST" action="{{ route('accounting.iklan.proses-hitung-penjualan') }}">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                        <button type="submit" 
                                onclick="return confirmSubmit(this, event, {
                                    title: 'Hitung &amp; Posting GL?',
                                    text: 'Jalankan hitung penjualan iklan tanggal {{ $tanggalPilihan }}? Data jurnal otomatis akan dibentuk ke Buku Besar.',
                                    theme: 'blue',
                                    icon: 'journal',
                                    confirmText: 'Ya, Hitung &amp; Posting'
                                })"
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <span>Hitung &amp; Posting GL</span>
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 border border-slate-200 text-slate-600 font-semibold text-xs rounded-xl">
                        <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Data Tanggal Ini Sudah Diposting ke GL</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Pure Tabel Data Faktur (Tanpa Header Campuran) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">No. Perkiraan</th>
                            <th class="py-3 px-3.5">Pemasang</th>
                            <th class="py-3 px-3.5">Jenis Iklan</th>
                            <th class="py-3 px-3.5 text-right">Tarif Bruto</th>
                            <th class="py-3 px-3.5 text-right">PPN</th>
                            <th class="py-3 px-3.5 text-right">Total Netto</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-sm">
                        @forelse($daftarFaktur as $key => $f)
                            @php
                                $noFaktur = $f->nofakturkoran ?? $f->no_faktur ?? '-';
                                $tgl = $f->tanggal_muatkoran ?? $f->tanggal_transaksikoran ?? $f->tanggal;
                                $pemasang = $f->nama_pemasangkoran ?? $f->nama_pemasang ?? $f->nama_pemohon ?? '-';
                                $kategori = $f->jenis_iklan ?? ($f->kategori_form ?? 'Koran');
                                $bruto = (float) ($f->tarif_bruto ?? $f->harga_transaksikoran ?? $f->subtotal ?? 0);
                                $ppn = (float) ($f->ppn ?? $f->ppn_transaksikoran ?? 0);
                                $netto = (float) ($f->total_netto ?? $f->totaltagihan_transaksikoran ?? $f->total_akhir ?? $bruto);
                                $piutang = (float) ($f->piutang ?? $f->piutang_transaksikoran ?? 0);
                                $isLunas = $f->is_lunas ?? ($piutang <= 0);
                                $tipeAkun = $f->tipe_akun_display ?? ($isLunas ? 'Uang Muka' : 'Piutang');
                                $kodePerkiraan = $f->kode_perkiraan_display ?? ($isLunas ? '2.00.01.01.01.01.00001' : '1.00.04.01.01.01.00001');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-slate-400 font-mono text-xs">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $noFaktur }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d/m/Y') : '-' }}</td>
                                <td class="py-3 px-3.5 font-mono text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-semibold" title="Akun {{ $tipeAkun }} Iklan">
                                        {{ $kodePerkiraan }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 max-w-xs truncate text-slate-800">{{ $pemasang }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ $kategori }}</td>
                                <td class="py-3 px-3.5 text-right font-mono text-slate-700">Rp {{ number_format($bruto, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right font-mono text-slate-700">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($netto, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $isLunas ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                        {{ $isLunas ? 'Lunas (UM)' : 'Piutang' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <x-action-dropdown align="right">
                                        <a href="{{ route('accounting.iklan.belanja', ['q' => $pemasang]) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span>Riwayat Belanja</span>
                                        </a>
                                        @if(isset($f->kanal) && $f->kanal === 'koran' && isset($f->id))
                                            <a href="{{ route('transaksikoran.show', $f->id) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Faktur Koran</span>
                                            </a>
                                        @endif
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-8 text-center text-slate-400 text-sm">
                                    Tidak ada data transaksi iklan pada filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>