<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-kp-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-kp-blue-700 active:bg-kp-blue-800 focus:outline-none focus:ring-2 focus:ring-kp-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer shadow-xs']) }}>
    {{ $slot }}
</button>
