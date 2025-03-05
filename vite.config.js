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
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/register-form.js'],
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
