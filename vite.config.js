import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite'
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        tailwindcss(),
        viteStaticCopy({
            targets: [
                { src: 'resources/images/logo.png', dest: 'images' },
                { src: 'resources/images/hero_bg_00_animation.mp4', dest: 'images' },
            ],
        }),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/quill.snow.css',
                'resources/js/app.js',
            ],
            refresh: [
                'resources/views/**', // Watch Blade templates
                'resources/views/components/**', // Watch Blade templates
                'resources/css/**',  // Watch CSS files
                'resources/js/**',   // Watch JS files
                'tailwind.config.js', // Watch Tailwind config
                'postcss.config.js',  // Watch PostCSS config
            ],
        }),
    ],
    resolve: {
        alias: {
            '@quill': '/node_modules/quill',
        },
    },
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
        // Removed rollupOptions.input to avoid conflicts with laravel-vite-plugin
    },
    assetsInclude: ['**/*.png', '**/*.jpg', '**/*.jpeg', '**/*.gif', '**/*.svg', '**/*.mp4'],
});
