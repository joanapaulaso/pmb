import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        viteStaticCopy({
            targets: [
                { src: 'resources/images/logo.png', dest: 'images' }, // Specific file
            ],
        }),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'], // Use array for multiple entry points
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
        // Remove or align rollupOptions.input to match laravel.input
        rollupOptions: {
            // Optionally, specify input here if needed, but it should match laravel.input
            input: {
                app: 'resources/js/app.js',
                appCss: 'resources/css/app.css', // Add CSS as a named entry
            },
        },
    },
});
