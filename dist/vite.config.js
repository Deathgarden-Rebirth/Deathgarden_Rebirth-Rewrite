import { defineConfig } from 'vite';
import vue from "@vitejs/plugin-vue";
import laravel from 'laravel-vite-plugin';
import { glob } from 'glob';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...glob.sync('resources/css/**/.scss')
            ],
            refresh: true,
        }),
    ],
});
