const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require("tailwindcss/colors")

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{vue,js,jsx}',
        './node_modules/vue-tailwind-datepicker/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Comfortaa', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                dark: {
                    'eval-0': '#000000',
                    'eval-1': '#0F0F0F',
                    'eval-2': '#202020',
                    'eval-3': '#676767',
                    'eval-4': '#989898',
                },
                "vtd-secondary": colors.zinc,
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
    ],
}
