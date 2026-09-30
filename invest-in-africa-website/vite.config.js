import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/zh.css', 'resources/js/app.js', 'resources/js/map.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Fonts and icons are emitted as files, never inlined, so they are cached by the CDN.
        assetsInlineLimit: 0,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
