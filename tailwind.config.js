import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                // 60% Dominant Brand Blue family (Kabar Priangan Identity)
                'kp-blue': {
                    50: '#F0F7FD',
                    100: '#DDEEFA',
                    200: '#C2E0F6',
                    300: '#97CCEF',
                    400: '#64B0E5',
                    500: '#1C92DB',
                    600: '#0A72AC', // Main Official Logo Blue
                    700: '#085C8D',
                    800: '#094E75',
                    900: '#0C4262',
                },
                // 30% Clean Newspaper Surface (No dark black backgrounds)
                'kp-canvas': '#F8FAFC', // Crisp clean paper canvas
                'kp-surface': '#FFFFFF', // Crisp white paper cards & tables
                'kp-border': '#E2E8F0',  // Subtle sheet hairline divider
                'kp-text': '#111827',    // High contrast black text (crisp readability)
                'kp-muted': '#4B5563',   // High contrast charcoal text

                // 10% Action Accent (Brand Blue unified, no random orange)
                'kp-accent': {
                    DEFAULT: '#0A72AC',
                    hover: '#085C8D',
                    light: '#DDEEFA',
                    text: '#0A72AC',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
