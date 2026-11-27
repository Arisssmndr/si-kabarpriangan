@props([
    'readerId',
    'tempReaderId',
])

<!-- Pilihan Scan Kamera & Unggah Gambar -->
<div class="border border-slate-200 rounded-xl p-3.5 bg-slate-50 space-y-2.5">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Kamera (Live Scan)</span>
        </span>
        <button type="button"
                @click="cameraActive ? stopCamera() : startCamera()"
                :disabled="isStartingCamera"
                class="h-8 px-3 text-xs font-semibold rounded-lg border transition cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-1.5"
                :class="cameraActive ? 'bg-white text-slate-700 border-slate-300 hover:bg-slate-100' : 'bg-kp-blue-600 text-white border-kp-blue-600 hover:bg-kp-blue-700'">
            <span x-text="isStartingCamera ? 'Menghubungkan...' : (cameraActive ? 'Matikan Kamera' : 'Nyalakan Kamera')"></span>
        </button>
    </div>

    <!-- Kontrol Orientasi & Pilihan Perangkat Kamera -->
    <div x-show="cameraActive" class="flex flex-wrap items-center justify-between gap-2 pt-1">
        <div class="flex items-center gap-1.5 min-w-0 flex-1" x-show="availableCameras.length > 0">
            <label for="select-{{ $readerId }}" class="sr-only">Pilih Kamera</label>
            <select id="select-{{ $readerId }}"
                    x-model="selectedCameraId"
                    @change="switchCamera($event.target.value)"
                    :disabled="isStartingCamera || availableCameras.length <= 1"
                    class="h-8 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-lg px-2.5 pr-7 truncate max-w-full flex-1 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-500 disabled:bg-slate-100 disabled:text-slate-600">
                <template x-for="cam in availableCameras" :key="cam.id">
                    <option :value="cam.id" :selected="cam.id === selectedCameraId" x-text="cam.label"></option>
                </template>
            </select>
            <button type="button"
                    x-show="availableCameras.length > 1"
                    @click="cycleCamera()"
                    :disabled="isStartingCamera"
                    title="Pindah ke perangkat kamera berikutnya"
                    class="h-8 px-2.5 text-xs font-semibold rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 transition cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed shrink-0 inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Ganti Kamera</span>
            </button>
        </div>

        <button type="button"
                @click="toggleCameraFlip()"
                :disabled="isStartingCamera"
                :title="isCameraFlipped ? 'Anti-Mirror aktif (membalik tampilan webcam agar tulisan normal). Klik untuk mode bawaan kamera.' : 'Mode bawaan kamera aktif. Klik untuk membalik horizontal jika gambar terlihat terbalik.'"
                class="h-8 px-2.5 text-xs font-semibold rounded-lg border transition cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed shrink-0 inline-flex items-center gap-1.5"
                :class="isCameraFlipped ? 'bg-sky-50 text-sky-700 border-sky-300 hover:bg-sky-100' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-100'">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span x-text="isCameraFlipped ? 'Anti-Mirror: Aktif' : 'Anti-Mirror: Mati'"></span>
        </button>
    </div>

    <div x-show="cameraActive" class="rounded-xl overflow-hidden bg-slate-900 p-2 shadow-inner space-y-1.5">
        <div x-show="isStartingCamera" class="py-10 flex flex-col items-center justify-center gap-2 text-slate-300 text-xs">
            <svg class="w-5 h-5 animate-spin text-sky-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Mengaktifkan kamera...</span>
        </div>
        <div id="{{ $readerId }}"
             class="w-full overflow-hidden rounded-lg max-w-[360px] mx-auto qr-camera-reader"
             :class="{ 'is-flipped': isCameraFlipped }"></div>
        <p class="text-[11px] text-center text-slate-400 px-2 pb-0.5">
            Jika gambar masih terbalik, klik tombol <strong class="text-slate-200">Anti-Mirror</strong> atau pilih perangkat via <strong class="text-slate-200">Ganti Kamera</strong>.
        </p>
    </div>

    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
        <span class="text-slate-500">Unggah berkas screenshot QR?</span>
        <label class="h-8 px-3 font-semibold rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 cursor-pointer transition flex items-center gap-1">
            <span>Pilih Gambar QR</span>
            <input type="file" accept="image/*" @change="scanUploadedImage($event)" class="hidden">
        </label>
    </div>
    <div id="{{ $tempReaderId }}" style="display:none;"></div>
</div>
