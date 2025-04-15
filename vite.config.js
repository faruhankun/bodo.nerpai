import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                    'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            jquery: path.resolve(__dirname, 'node_modules/jquery'),
            select2: path.resolve(__dirname, 'node_modules/select2')
        }
    },
});
