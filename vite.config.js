import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/sass/admin.scss',
                'resources/js/app.js',
                'resources/js/admin.js',
                // !! USE ↑ FILES like "import './~~~~/~~~.js'; or @import "../css/~~~~/~~~.css";" !!

                // Admin
                'resources/js/admin/movie-showtimes.js',

                // Customer Chat
                'resources/js/customer/chat.js',

                // Admin Chat
                'resources/js/admin/chat.js',
                // Community Discussion
                'resources/css/community-discussion.css',
                'resources/js/community-discussion.js',
            ],
            refresh: true,
        }),
    ],
});