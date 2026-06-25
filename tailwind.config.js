import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                // Warm editorial palette — "CareerHub" Anthropic-style DNA.
                canvas: '#FAF9F5',
                surface: '#FFFFFF',
                'surface-2': '#F4F1EA',
                hairline: '#E7E2D6',
                ink: '#1A1A18',
                muted: '#6E6A5E',
                clay: {
                    DEFAULT: '#C9785C',
                    strong: '#B4593B',
                    soft: '#F2E3DB',
                },
                sage: {
                    DEFAULT: '#6F7A63',
                    soft: '#E7EBE1',
                },
                positive: '#4B7B5A',
                caution: '#B5823A',
                danger: '#AE4B3B',
                // Dark surfaces for the few dark UI moments.
                'ink-canvas': '#1A1813',
                'ink-surface': '#221F18',
                'ink-border': '#353026',
            },
            fontFamily: {
                serif: ['Fraunces', ...defaultTheme.fontFamily.serif],
                sans: ['"Hanken Grotesk"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            borderRadius: {
                card: '10px',
                btn: '10px',
                input: '8px',
            },
            boxShadow: {
                soft: '0 1px 2px rgba(20,20,18,.05)',
                lift: '0 1px 2px rgba(20,20,18,.05), 0 12px 28px -16px rgba(20,20,18,.12)',
                'lift-lg': '0 1px 2px rgba(20,20,18,.05), 0 24px 48px -24px rgba(20,20,18,.18)',
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'toast-in': {
                    '0%': { opacity: '0', transform: 'translateY(-8px) scale(.98)' },
                    '100%': { opacity: '1', transform: 'translateY(0) scale(1)' },
                },
            },
            animation: {
                'fade-up': 'fade-up 220ms cubic-bezier(0.16, 1, 0.3, 1) both',
                'fade-in': 'fade-in 200ms ease-out both',
                'toast-in': 'toast-in 220ms cubic-bezier(0.16, 1, 0.3, 1) both',
            },
        },
    },

    plugins: [forms],
};
