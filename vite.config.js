import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/backend/js/main.js',
                'resources/backend/js/pusher.js',
                'resources/backend/css/app.css',

                'resources/backend/dashboard/index.tsx',
                'resources/backend/campaign/index.tsx',
                'resources/backend/seller/index.tsx',
                'resources/backend/admin-chat/index.tsx',
                'resources/backend/seller-chat/index.tsx',
                'resources/backend/pos/index.tsx',
                'resources/backend/order/index.tsx',
                'resources/frontend/index.tsx',
                'resources/frontend/index.css',

                'resources/installation/index.tsx',
                'resources/installation/index.css',
            ],
            refresh: false,
        }),
        react(),
        tsconfigPaths(),
    ],
    server: {
        watch: {
            ignored: ['**/.env*', '**/RouteServiceProvider.php'],
        },
    },
});
