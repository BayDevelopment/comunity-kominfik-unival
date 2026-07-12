import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import GlobalToaster from '@/components/GlobalToaster.vue';
import { initializeTheme } from '@/composables/useAppearance';

import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const publicPages = [
    'Welcome',
    'Home',
    'Project',
    'Anggota',
    'Layanan',
    'Join',
    'Kerjasama',
    'auth/Login',
    'auth/Register',
    'auth/ForgotPassword',
    'auth/ResetPassword',
];

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),

    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),

    layout: (name) => {
        switch (true) {
            case publicPages.includes(name):
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },

    progress: {
        color: '#4B5563',
    },

    setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
        .use(plugin)
        .mount(el);

    // Toaster independen, mount sekali untuk semua halaman
    const toasterEl = document.createElement('div');
    document.body.appendChild(toasterEl);
    createApp(GlobalToaster).mount(toasterEl);
},
});

initializeTheme();
initializeFlashToast();
