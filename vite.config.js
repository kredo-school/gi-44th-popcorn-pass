import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/admin.js',
                // Admin
                'resources/js/admin/movie-showtimes.js',
                // Customer Chat
                'resources/js/customer/chat.js',
                // Admin Chat
                'resources/js/admin/chat.js',
                // Community Discussion
                'resources/css/community-discussion.css',
                'resources/js/community-discussion.js',
                // Recommendations (Feature 3)
                'resources/css/recommendations.css',
                'resources/js/recommendations.js',
            ],
            refresh: true,
        }),
    ],
});