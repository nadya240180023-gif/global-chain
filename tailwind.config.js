import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

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
                sans: ['Figtree', 'Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                blue: colors.amber, // Map all blues to amber/gold
                indigo: colors.orange, // Map indigo to warm orange/bronze
                violet: colors.orange,
                slate: {
                    50: '#FDFBF7', // Ivory / Bone White
                    100: '#F4F1EA', // Very soft warm beige
                    200: '#E6E0D4', // Light taupe
                    300: '#D5CDBE',
                    400: '#B0A591',
                    500: '#8A7E68',
                    600: '#675C47',
                    700: '#4D4434',
                    800: '#362F24',
                    900: '#211D15', // Deep rich espresso for the sidebar
                    950: '#14110C',
                },
            }
        },
    },

    plugins: [forms],
};
