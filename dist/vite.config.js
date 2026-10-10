import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import { globSync } from 'node:fs';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // The other files in resources/css are partials imported by app.css, not entries
                'resources/css/app.css',
                ...globSync('resources/css/components/**/*.css'),
                ...globSync('resources/js/**/*.js'),
            ],
            refresh: true,
            fonts: [
                bunny('Exo 2', {
                    weights: [400, 500, 600, 700, 800],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
