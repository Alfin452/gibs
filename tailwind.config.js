import defaultTheme from 'tailwindcss/defaultTheme';

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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Tema Utama: Keppel (#3ab09e)
                primary: {
                    50: '#f0f9f8',
                    100: '#d8f0ec',
                    200: '#b4e1da',
                    300: '#83ccc2',
                    400: '#4fb0a4',
                    500: '#3ab09e', // Base Keppel
                    600: '#278b7d',
                    700: '#226f65',
                    800: '#1f5952',
                    900: '#1b4a45',
                    950: '#0d2d2a',
                },
                // Tema Sekunder: Coral / Warm Orange (Komplementer Keppel)
                secondary: {
                    50: '#fff8f1',
                    100: '#ffeede',
                    200: '#ffd9b9',
                    300: '#ffbc8a',
                    400: '#ff944e',
                    500: '#ff731f', // Base Orange/Coral
                    600: '#f05b0f',
                    700: '#c7420b',
                    800: '#9e3412',
                    900: '#7f2e12',
                    950: '#451507',
                }
            }
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
    ],
};