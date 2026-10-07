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
                ink: '#111111',
                crimson: {
                    DEFAULT: '#d01230',
                    dark: '#a50e26',
                },
                paper: '#f7f7f5',
                stoneish: '#5f5b57',
                line: '#e4e0d9',
                navy: '#151821',
            },
            fontFamily: {
                sans: ['"Source Sans 3"', ...defaultTheme.fontFamily.sans],
                display: ['Oswald', ...defaultTheme.fontFamily.sans],
            },
            maxWidth: {
                '6xl': '72rem',
            },
        },
    },

    plugins: [forms],
};
