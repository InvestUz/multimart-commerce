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
                gold: {
                    50: '#FFFAEB',
                    100: '#FFF5D6',
                    200: '#FFE9AD',
                    300: '#FFDB85',
                    400: '#FFCB5C',
                    500: '#FFB82E',
                    600: '#FA9E1A',
                    700: '#D1780E',
                    800: '#A8570B',
                    900: '#87430D',
                },
            },
        },
    },

    plugins: [forms],
};
