import { ref, onMounted, onBeforeUnmount } from 'vue';

const deferredPrompt = ref<any>(null);
const showInstallAlert = ref(false);

function isStandalone() {
    return (
        window.matchMedia('(display-mode: standalone)').matches ||
        // fallback untuk iOS Safari (walau beforeinstallprompt tetap tidak akan fire di sana)
        (window.navigator as any).standalone === true
    );
}

export function usePwaInstall() {
    function handleBeforeInstallPrompt(e: Event) {
        e.preventDefault();
        deferredPrompt.value = e;
        showInstallAlert.value = true;
    }

    function handleAppInstalled() {
        showInstallAlert.value = false;
        deferredPrompt.value = null;
    }

    onMounted(() => {
        if (isStandalone()) {
            return;
        }

        window.addEventListener(
            'beforeinstallprompt',
            handleBeforeInstallPrompt,
        );

        window.addEventListener('appinstalled', handleAppInstalled);
    });

    async function installApp() {
        if (!deferredPrompt.value) return;

        deferredPrompt.value.prompt();
        const { outcome } = await deferredPrompt.value.userChoice;

        if (outcome === 'accepted') {
            console.log('User accepted the install prompt');
        } else {
            console.log('User dismissed the install prompt');
        }

        deferredPrompt.value = null;
        showInstallAlert.value = false;
    }

    function dismissAlert() {
        showInstallAlert.value = false;
    }

    return { showInstallAlert, installApp, dismissAlert };
}
