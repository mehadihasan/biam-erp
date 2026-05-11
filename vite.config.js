import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: {
                'resources/css/app.css': 'resources/css/app.css',
                'resources/js/app.js': 'resources/js/app.js',
                'resources/css/welcome.css': 'resources/css/welcome.css',
                'resources/js/welcome.js': 'resources/js/welcome.js',
                'resources/css/bcs-cadre.css': 'resources/css/bcs-cadre.css',
                'resources/js/bcs-cadre.js': 'resources/js/bcs-cadre.js',
                'resources/css/filament/admin/theme.css': 'resources/css/filament/admin/theme.css',
            },
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
