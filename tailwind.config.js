import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                dash: {
                    yellow: "#fcca11",
                    light: "#4d4732",
                    gray: "#e5e7eb",
                    dark: "#1f2937",
                    navy: "#111827",
                    blue: "#2563eb",
                    blueDark: "#1d4ed8",
                    green: "#16a34a",
                    red: "#dc2626",
                    orange: "#f97316",
                },
            },
        },
    },

    plugins: [forms],
}
