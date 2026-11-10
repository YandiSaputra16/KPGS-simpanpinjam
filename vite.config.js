
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/kpgs.css',
                'resources/css/kpgs.theme.css',
                'resources/js/app.js',
                'resources/js/kpgs.js',
            ],
            refresh: true,
        }),
    ],
});
