import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.ts',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),

        inertia({
            ssr: false,
        }),

        tailwindcss(),

        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),

        wayfinder({
            formVariants: true,
        }),

        VitePWA({
            registerType: 'autoUpdate',

            includeAssets: [
                'favicon.ico',
                'apple-touch-icon.png',
            ],

            manifest: {
                name: 'KOMINFIK',
                short_name: 'KOMINFIK',
                description: 'Aplikasi KOMINFIK',
                theme_color: '#f97316',
                background_color: '#ffffff',
                display: 'standalone',
                start_url: '/',
                scope: '/',

                icons: [
                    {
                        src: '/icon-192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/icon-512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: '/icon-512-maskable.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable',
                    },
                ],
            },

            workbox: {
                globPatterns: [
                    '**/*.{js,css,html,ico,png,svg,woff2}',
                ],

                navigateFallbackDenylist: [
                    /^\/api/,
                    /^\/login/,
                    /^\/logout/,
                ],
            },
        }),
    ],
});
