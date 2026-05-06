import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],

    input: [
    'resources/css/app.css',
    'resources/css/quizquest.css', // ← tambahkan ini
    'resources/js/app.js',
],


    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
