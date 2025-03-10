import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    // Definindo base para produção
    base: process.env.NODE_ENV === 'production' ? './' : '/',
    plugins: [
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
                // 'resources/js/post-scripts.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@quill': '/node_modules/quill'
        },
    },
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                appCss: 'resources/css/app.css',
                quill: 'resources/css/quill.snow.css',
            },
        },
    },
    assetsInclude: ['**/*.png', '**/*.jpg', '**/*.jpeg', '**/*.gif', '**/*.svg', '**/*.mp4'],
});
