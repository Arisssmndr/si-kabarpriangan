@props(['align' => 'right', 'width' => 'w-44'])

<div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
    <button type="button" 
            @click.stop="open = !open" 
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer select-none"
            title="Menu Aksi">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
        </svg>
    </button>
    <div x-show="open" 
         x-cloak 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute {{ $align === 'right' ? 'right-0' : 'left-0' }} mt-1 {{ $width }} bg-white border border-slate-200 rounded-xl shadow-lg z-50 py-1 text-left text-xs divide-y divide-slate-100"
         @click="open = false">
        {{ $slot }}
    </div>
</div>

