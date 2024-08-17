import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { glob } from 'glob';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js',
                ...glob.sync('resources/css/**/*.scss'),
                ...glob.sync('resources/js/**/*.js')
            ],
            refresh: true,
        }),
    ],
});
