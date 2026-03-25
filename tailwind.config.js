import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primaryPurple: {
                    DEFAULT: '#7c3aed', // purple-600
                    light: '#a78bfa', // purple-400
                    dark: '#4c1d95', // purple-900
                },
                gold: {
                    DEFAULT: '#FFD700',
                    light: '#FFECB3',
                    dark: '#B8860B',
                },
            },
        },
    },

    plugins: [
        forms,
        require('daisyui'),
    ],
    daisyui: {
        themes: [
            {
                purplegold: {
                    ...require('daisyui/src/colors/themes')['[data-theme=light]'],
                    'primary': '#7c3aed',
                    'secondary': '#FFD700',
                    'accent': '#a78bfa',
                    'neutral': '#4c1d95',
                    'base-100': '#fff',
                    'info': '#a78bfa',
                    'success': '#FFD700',
                    'warning': '#FFD700',
                    'error': '#b91c1c',
                },
            },
            'dark',
        ],
    },
};
