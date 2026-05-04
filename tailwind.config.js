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
        container: {
            center: true,
            padding: '1rem',
        },
        extend: {
            colors: {
                primary: {
                    50: '#f0fdf9',
                    100: '#c6f7e6',
                    200: '#8ee7cb',
                    300: '#57d7b0',
                    400: '#2bb893',
                    500: '#0ea57b',
                    600: '#0b8f66',
                    700: '#0a6f4f',
                    800: '#09463a',
                    900: '#062c24',
                },
                accent: {
                    50: '#fff9f0',
                    100: '#fff0d6',
                    200: '#ffd9a8',
                    300: '#ffc57a',
                    400: '#ffb64d',
                    500: '#ffa700',
                    600: '#ff9800',
                    700: '#e07a00',
                    800: '#b55c00',
                    900: '#7a3b00',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'soft': '0 6px 18px rgba(16,24,40,0.08)',
                'soft-md': '0 10px 30px rgba(16,24,40,0.1)',
            },
            borderRadius: {
                'xl': '0.75rem',
            },
        },
    },

    plugins: [forms],
};
