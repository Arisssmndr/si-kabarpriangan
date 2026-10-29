<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                            HU. Priangan
                        </span>
                        <span class="text-xs text-slate-400">/</span>
                        <span class="text-xs font-semibold text-slate-600">Divisi Periklanan Koran</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">
                        Daftar Order Transaksi Iklan Koran
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('transaksikoran.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-extrabold rounded-xl shadow-xs hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Order Transaksi Baru</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
            </div>
        @endif

        <!-- Toolbar Pencarian & Filter -->
        <div class="bg-white border border-slate-300 rounded-2xl p-4 shadow-xs">
            <form method="GET" action="{{ route('transaksikoran.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pencarian Data Faktur
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no order, no faktur, nama pemasang, materi iklan, atau nama sales..."
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs text-black placeholder-gray-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Pembayaran
                    </label>
                    <select name="status" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs text-black focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="piutang" {{ request('status') === 'piutang' ? 'selected' : '' }}>Piutang Iklan (Belum Lunas)</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari Data
                    </button>
                    @if(request()->hasAny(['q', 'status']))
                        <a href="{{ route('transaksikoran.index') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-bold text-xs rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Transaksi Koran -->
        <div class="bg-white border border-slate-300 rounded-2xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50/90 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Transaksi Order HU. Priangan</h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">Total {{ $transaksikoran->total() }} lembar faktur tercatat</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-300 bg-slate-100 text-slate-800 uppercase tracking-wider text-[11px] whitespace-nowrap">
                            <th class="py-3.5 px-3 font-bold text-center w-10">No</th>
                            <th class="py-3.5 px-3 font-bold">No. Order / Faktur</th>
                            <th class="py-3.5 px-3 font-bold">Tanggal</th>
                            <th class="py-3.5 px-3 font-bold">Pemasang &amp; Kota</th>
                            <th class="py-3.5 px-3 font-bold">Materi Iklan</th>
                            <th class="py-3.5 px-3 font-bold">Ukuran</th>
                            <th class="py-3.5 px-3 font-bold text-right">Brutto</th>
                            <th class="py-3.5 px-3 font-bold text-right">Diskon</th>
                            <th class="py-3.5 px-3 font-bold text-right">Netto</th>
                            <th class="py-3.5 px-3 font-bold text-right">PPn (11%)</th>
                            <th class="py-3.5 px-3 font-bold text-right">Jumlah Dibayar</th>
                            <th class="py-3.5 px-3 font-bold text-right">Sisa Piutang</th>
                            <th class="py-3.5 px-3 font-bold text-center">Status</th>
                            <th class="py-3.5 px-3 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 whitespace-nowrap">
                        @forelse($transaksikoran as $key => $t)
                            @php
                                $brutto = (float) ($t->brutto_transaksikoran ?: ($t->harga_transaksikoran * ($t->total_muatkoran ?: 1)));
                                $diskonNominal = (float) ($t->diskon_transaksikoran ?? 0);
                                $diskonPersen = (float) ($t->diskon_persen ?: ($brutto > 0 ? round(($diskonNominal / $brutto) * 100, 2) : 0));
                                $netto = (float) ($t->netto_transaksikoran ?: max(0, $brutto - $diskonNominal));
                                $ppn = (float) ($t->ppn_transaksikoran ?? 0);
                                $materai = (float) ($t->materai_transaksikoran ?? 0);
                                $total = (float) ($t->totaltagihan_transaksikoran ?: ($netto + $ppn + $materai));
                                $bayar = (float) ($t->jumlahbayar_transaksikoran ?? 0);
                                $piutang = (float) ($t->piutang_transaksikoran ?? max(0, $total - $bayar));
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3 text-center text-gray-500 font-normal">
                                    {{ $transaksikoran->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-blue-900 font-mono">{{ $t->nofakturkoran }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $t->no_order ?: '-' }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-medium text-slate-900">{{ $t->tanggal_transaksikoran }}</div>
                                    <div class="text-[10px] text-slate-500">Muat: {{ $t->tanggal_muatkoran }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-bold uppercase text-slate-900">{{ $t->nama_pemasangkoran }}</div>
                                    <div class="text-[10px] font-semibold text-slate-500 uppercase">{{ $t->kota ?: 'TASIKMALAYA' }}</div>
                                </td>
                                <td class="py-3 px-3 max-w-xs truncate">
                                    <span class="font-medium text-slate-800" title="{{ $t->materi_iklan }}">
                                        {{ $t->materi_iklan ?: ($t->halaman_iklan . ' - ' . $t->warna_iklan) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-mono font-medium text-slate-700">
                                    {{ $t->ukuran_iklan }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-slate-700">
                                    Rp {{ number_format($brutto, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-slate-600">
                                    @if($diskonNominal > 0)
                                        <span>Rp {{ number_format($diskonNominal, 0, ',', '.') }}</span>
                                        <span class="block text-[10px] text-slate-400">({{ $diskonPersen }}%)</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($netto, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-slate-700">
                                    Rp {{ number_format($ppn, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-black text-blue-900">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-black {{ $piutang > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                    Rp {{ number_format($piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($piutang <= 0)
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            LUNAS
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            PIUTANG IKLAN
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('transaksikoran.cetak', $t->id) }}" 
                                           target="_blank"
                                           class="px-2.5 py-1 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 rounded-lg border border-slate-300 transition shadow-2xs">
                                            🖨️ Cetak
                                        </a>
                                        <button type="button" 
                                                onclick="openEditModal(this)"
                                                data-id="{{ $t->id }}"
                                                data-nofaktur="{{ $t->nofakturkoran }}"
                                                data-no_order="{{ $t->no_order }}"
                                                data-identitas="{{ $t->identitas }}"
                                                data-tanggal="{{ $t->tanggal_transaksikoran }}"
                                                data-nama="{{ $t->nama_pemasangkoran }}"
                                                data-alamat="{{ $t->alamat_pemasangkoran }}"
                                                data-kota="{{ $t->kota }}"
                                                data-kop="{{ $t->kop_iklan }}"
                                                data-merk="{{ $t->merk }}"
                                                data-materi="{{ $t->materi_iklan }}"
                                                data-id_iklan="{{ $t->id_iklankoran }}"
                                                data-halaman="{{ $t->halaman_iklan }}"
                                                data-warna="{{ $t->warna_iklan }}"
                                                data-ukuran="{{ $t->ukuran_iklan }}"
                                                data-sales="{{ $t->sales_iklankoran }}"
                                                data-tgl_muat="{{ $t->tanggal_muatkoran }}"
                                                data-total_muat="{{ $t->total_muatkoran }}"
                                                data-harga="{{ $t->harga_transaksikoran }}"
                                                data-diskon_persen="{{ $diskonPersen }}"
                                                data-diskon="{{ $diskonNominal }}"
                                                data-materai="{{ $materai }}"
                                                data-bayar="{{ $bayar }}"
                                                data-komisi="{{ $t->komisi_transaksikoran }}"
                                                data-insentif="{{ $t->insentif_transaksikoran }}"
                                                class="px-2.5 py-1 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition cursor-pointer">
                                            Ubah
                                        </button>
                                        <button type="button" 
                                                onclick="deleteTransaksi('{{ $t->id }}', '{{ $t->nofakturkoran }}')"
                                                class="px-2.5 py-1 text-xs font-bold text-red-600 bg-white hover:bg-red-50 rounded-lg border border-red-200 transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data order transaksi iklan koran. Klik "Order Transaksi Baru" untuk menambah data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksikoran->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $transaksikoran->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Edit Transaksi Koran (Sesuai Faktur Fisik HU. Priangan) -->
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 sticky top-0 z-10">
                <div>
                    <h3 id="editModalTitle" class="text-sm font-black text-slate-900 uppercase tracking-tight">
                        Ubah Transaksi Iklan Koran HU. Priangan
                    </h3>
                    <p class="text-[11px] text-slate-500 font-normal">Perbarui data order transaksi dan rincian nominal faktur</p>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-xs font-bold text-gray-500 hover:text-black px-2 py-1 cursor-pointer">
                    ✕ Tutup
                </button>
            </div>

            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- Baris 1: No Order, No Faktur & Identitas COA -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">No Order</label>
                        <input type="text" id="e_no_order" name="no_order" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-black" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">No Faktur</label>
                        <input type="text" id="e_nofaktur" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-xl text-xs font-mono font-bold text-blue-900 cursor-not-allowed" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Identitas (COA)</label>
                        <input type="text" id="e_identitas" name="identitas" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono text-black" />
                    </div>
                </div>

                <!-- Baris 2: Nama Pemasang, Alamat & Kota -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Nama Pemasang</label>
                        <input type="text" id="e_nama" name="nama_pemasangkoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold uppercase text-black" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Alamat</label>
                        <input type="text" id="e_alamat" name="alamat_pemasangkoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs uppercase text-black" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Kota</label>
                        <input type="text" id="e_kota" name="kota" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold uppercase text-black" />
                    </div>
                </div>

                <!-- Baris 3: Tanggal Transaksi, Tanggal Muat, Sales FO -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Tanggal Transaksi</label>
                        <input type="date" id="e_tanggal" name="tanggal_transaksikoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-black" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Tanggal Muat</label>
                        <input type="date" id="e_tgl_muat" name="tanggal_muatkoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-blue-900" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Front Office / Sales</label>
                        <input type="text" id="e_sales" name="sales_iklankoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold uppercase text-black" />
                    </div>
                </div>

                <!-- Baris 4: Materi Iklan, Kop, Merk -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Materi Iklan</label>
                        <input type="text" id="e_materi" name="materi_iklan" placeholder="Contoh: APBD PERUBAHAN 2026 SUDAH BERJALAN" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold uppercase text-black" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Merk</label>
                        <input type="text" id="e_merk" name="merk" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-black" />
                    </div>
                </div>

                <!-- Baris 5: Jenis Iklan, Halaman, Warna, Ukuran, Qty -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                    <div class="col-span-2 sm:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Jenis Iklan</label>
                        <select id="e_id_iklan" name="id_iklankoran" required class="w-full px-2.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-black">
                            @foreach($iklankoran as $ik)
                                <option value="{{ $ik->id }}">{{ $ik->jenis_iklankoran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Warna</label>
                        <select id="e_warna" name="warna_iklan" required class="w-full px-2 py-2 bg-white border border-slate-300 rounded-xl text-xs text-black">
                            <option value="Full Color (FC)">Full Color (FC)</option>
                            <option value="Hitam Putih (BW)">Hitam Putih (BW)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Ukuran</label>
                        <input type="text" id="e_ukuran" name="ukuran_iklan" placeholder="4 x 200 mm" class="w-full px-2.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Qty</label>
                        <input type="number" id="e_total_muat" name="total_muatkoran" min="1" required oninput="hitungEdit()" class="w-full px-2 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black" />
                    </div>
                </div>

                <!-- Baris 6: Rincian Keuangan Persis Slip Faktur -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-black text-slate-800 uppercase tracking-wider">Kalkulasi Finansial Slip Faktur</p>
                        <span class="text-[10px] text-blue-700 font-bold bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full">Brutto - Diskon + PPn 11%</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Harga Satuan (Rp)</label>
                            <input type="text" id="e_harga" name="harga_transaksikoran" required onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black font-mono" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Diskon (%)</label>
                            <input type="number" step="0.01" id="e_diskon_persen" name="diskon_persen" oninput="hitungEdit('persen')" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black font-mono" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Diskon (Rp)</label>
                            <input type="text" id="e_diskon" name="diskon_transaksikoran" onkeyup="formatInput(this, 'nominal')" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black font-mono" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Materai (Rp)</label>
                            <input type="text" id="e_materai" name="materai_transaksikoran" onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black font-mono" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs border-t border-slate-200">
                        <div>
                            <span class="text-gray-500 text-[10px] uppercase font-bold">Brutto:</span>
                            <p id="e_label_brutto" class="font-bold text-slate-800 text-xs font-mono">Rp 0</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-[10px] uppercase font-bold">Netto:</span>
                            <p id="e_label_netto" class="font-bold text-slate-900 text-xs font-mono">Rp 0</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-[10px] uppercase font-bold">PPn 11%:</span>
                            <p id="e_label_ppn" class="font-bold text-slate-700 text-xs font-mono">Rp 0</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-[10px] uppercase font-bold">Jumlah Yang Dibayar:</span>
                            <p id="e_label_total" class="font-black text-blue-900 text-sm font-mono">Rp 0</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200 items-end">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Jumlah Dibayar (DP / Pelunasan)</label>
                            <input type="text" id="e_bayar" name="jumlahbayar_transaksikoran" required onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black font-mono" />
                        </div>
                        <div>
                            <span class="text-gray-500 text-[10px] uppercase font-bold">Sisa Piutang:</span>
                            <p id="e_label_piutang" class="font-black text-red-600 text-sm font-mono">Rp 0</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 text-xs font-bold text-gray-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold rounded-xl shadow-md transition cursor-pointer">
                        Simpan Perubahan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function formatRupiah(num) {
            if (!num) return '0';
            return new Intl.NumberFormat('id-ID').format(num);
        }
        function cleanNumber(str) {
            if (!str) return 0;
            return parseFloat(str.toString().replace(/[^0-9]/g, '')) || 0;
        }
        function formatInput(el, mode) {
            const v = cleanNumber(el.value);
            el.value = formatRupiah(v);
            hitungEdit(mode);
        }

        function openEditModal(btn) {
            const d = btn.dataset;
            document.getElementById('editModalTitle').innerText = 'Ubah Order Transaksi ' + d.nofaktur;
            document.getElementById('editForm').action = "/transaksikoran/" + d.id;

            document.getElementById('e_nofaktur').value = d.nofaktur || '';
            document.getElementById('e_no_order').value = d.no_order || '';
            document.getElementById('e_identitas').value = d.identitas || '';
            document.getElementById('e_tanggal').value = d.tanggal || '';
            document.getElementById('e_nama').value = d.nama || '';
            document.getElementById('e_alamat').value = d.alamat || '';
            document.getElementById('e_kota').value = d.kota || 'SUMEDANG';
            document.getElementById('e_merk').value = d.merk || '';
            document.getElementById('e_materi').value = d.materi || '';
            document.getElementById('e_sales').value = d.sales || '';
            document.getElementById('e_id_iklan').value = d.id_iklan || '';
            document.getElementById('e_warna').value = d.warna || 'Full Color (FC)';
            document.getElementById('e_ukuran').value = d.ukuran || '';
            document.getElementById('e_tgl_muat').value = d.tgl_muat || '';
            document.getElementById('e_total_muat').value = d.total_muat || 1;

            document.getElementById('e_harga').value = formatRupiah(d.harga);
            document.getElementById('e_diskon_persen').value = d.diskon_persen || 0;
            document.getElementById('e_diskon').value = formatRupiah(d.diskon);
            document.getElementById('e_materai').value = formatRupiah(d.materai || 0);
            document.getElementById('e_bayar').value = formatRupiah(d.bayar || 0);

            hitungEdit();
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function hitungEdit(source) {
            const harga = cleanNumber(document.getElementById('e_harga').value);
            const qty = parseFloat(document.getElementById('e_total_muat').value) || 1;
            const brutto = harga * qty;

            let diskon = cleanNumber(document.getElementById('e_diskon').value);
            let persen = parseFloat(document.getElementById('e_diskon_persen').value) || 0;

            if (source === 'persen') {
                diskon = Math.round(brutto * (persen / 100));
                document.getElementById('e_diskon').value = formatRupiah(diskon);
            } else if (source === 'nominal') {
                persen = brutto > 0 ? Math.round((diskon / brutto) * 10000) / 100 : 0;
                document.getElementById('e_diskon_persen').value = persen;
            }

            const netto = Math.max(0, brutto - diskon);
            const ppn = Math.round(netto * 0.11);
            const materai = cleanNumber(document.getElementById('e_materai').value);
            const total = netto + ppn + materai;

            const bayar = cleanNumber(document.getElementById('e_bayar').value);
            const piutang = Math.max(0, total - bayar);

            document.getElementById('e_label_brutto').innerText = 'Rp ' + formatRupiah(Math.round(brutto));
            document.getElementById('e_label_netto').innerText = 'Rp ' + formatRupiah(Math.round(netto));
            document.getElementById('e_label_ppn').innerText = 'Rp ' + formatRupiah(Math.round(ppn));
            document.getElementById('e_label_total').innerText = 'Rp ' + formatRupiah(Math.round(total));

            const elPiutang = document.getElementById('e_label_piutang');
            elPiutang.innerText = 'Rp ' + formatRupiah(Math.round(piutang));
            if (piutang > 0) {
                elPiutang.className = 'font-black text-red-600 text-sm font-mono';
            } else {
                elPiutang.className = 'font-black text-emerald-600 text-sm font-mono';
            }
        }

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const nofaktur = document.getElementById('e_nofaktur').value;
            if (window.AppAlert) {
                AppAlert.confirmSave({
                    title: 'Simpan Perubahan Transaksi',
                    subtitle: 'Data faktur akan diperbarui',
                    target: nofaktur,
                    confirmText: 'Ya, Simpan'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Simpan perubahan data ' + nofaktur + '?')) {
                    form.submit();
                }
            }
        });

        async function deleteTransaksi(id, nofaktur) {
            const doDelete = async () => {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const res = await fetch('/transaksikoran/' + id, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });
                    if (window.AppAlert) {
                        AppAlert.success({
                            title: 'Berhasil Dihapus',
                            subtitle: 'Data telah dihapus dari sistem',
                            message: 'Transaksi ' + nofaktur + ' berhasil dihapus.'
                        }).then(() => location.reload());
                    } else {
                        alert('Transaksi ' + nofaktur + ' berhasil dihapus.');
                        location.reload();
                    }
                } catch (err) {
                    alert('Gagal menghapus data transaksi.');
                }
            };

            if (window.AppAlert) {
                AppAlert.confirmDelete({
                    title: 'Hapus Transaksi',
                    subtitle: 'Tindakan ini tidak dapat dibatalkan',
                    target: nofaktur,
                    confirmText: 'Ya, Hapus Transaksi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        doDelete();
                    }
                });
            } else {
                if (confirm('Hapus transaksi ' + nofaktur + '?')) {
                    doDelete();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
