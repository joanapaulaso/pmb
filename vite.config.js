import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy'; // Use named import

export default defineConfig({
    plugins: [
        viteStaticCopy({
            targets: [
                { src: 'resources/images/*', dest: 'images' }, // Copies all images to public/images
            ],
        }),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
            },
        },
    },
});
