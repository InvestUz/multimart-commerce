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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#3B82F6',
                    hover: '#2563EB',
                    light: '#DBEAFE',
                    dark: '#1E40AF',
                },
                secondary: {
                    DEFAULT: '#10B981',
                    light: '#D1FAE5',
                },
                accent: {
                    DEFAULT: '#F59E0B',
                    hover: '#D97706',
                },
            },
        },
    },

    plugins: [forms],
};
