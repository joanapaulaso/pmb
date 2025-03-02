import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        viteStaticCopy({
            targets: [
                { src: 'resources/images/logo.png', dest: 'images' },
                // Add your MP4 file to be copied
                { src: 'resources/images/hero_bg_00_animation.mp4', dest: 'images' },
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
                appCss: 'resources/css/app.css',
            },
        },
    },
    assetsInclude: ['**/*.png', '**/*.jpg', '**/*.jpeg', '**/*.gif', '**/*.svg', '**/*.mp4'],
});
