import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './config/**/*.php',
    ],

    safelist: [
        // Classes do Quill
        { pattern: /ql-/ },
        { pattern: /^ql-size-/ },
        { pattern: /^ql-font-/ },
        { pattern: /^ql-align-/ },
        { pattern: /^ql-bg-/ },
        { pattern: /^ql-color-/ },
        { pattern: /^ql-indent-/ },

        // Classes do Tailwind Typography para exibição de conteúdo
        { pattern: /^prose/ },

        // Classes específicas
        'ql-size-small', 'ql-size-large', 'ql-size-huge',
        'ql-font-serif', 'ql-font-monospace',
        'ql-align-center', 'ql-align-right', 'ql-align-justify',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            opacity: {
                '85': '0.85',
            },
            backgroundImage: {
                'hero-bg': "url('/resources/images/hero_bg.webp')",
            }
        },
    },

    plugins: [forms, typography],
    mode: 'jit',
};
