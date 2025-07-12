import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path';


export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/app.jsx',
            ],
            refresh: true,
        }),
        react(
        //{
        //     babel: {
        //         plugins: [
        //             ['import', {
        //                 libraryName: 'antd',
        //                 libraryDirectory: 'es',
        //                 style: true,
        //             }, 'antd'],
        //         ],
        //     },
        // }
        ),
    ],
    resolve: {
        alias: {
            jquery: path.resolve(__dirname, 'node_modules/jquery'),
            select2: path.resolve(__dirname, 'node_modules/select2'),
        }
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    utility: ['axios', 'dayjs'],
                }
            }
        },
        chunkSizeWarningLimit: 1000,
    }
});
