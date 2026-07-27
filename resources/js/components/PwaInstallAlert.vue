<script setup lang="ts">
import { Download } from 'lucide-vue-next'
import { usePwaInstall } from '@/composables/usePwaInstall'

const { showInstallAlert, installApp, dismissAlert } = usePwaInstall()
</script>

<template>
    <Transition name="fade">
        <div v-if="showInstallAlert" class="pwa-install-alert">
            <div class="content">
                <div class="icon">
                    <Download :size="22" />
                </div>

                <div class="text">
                    <h3>Install KOMINFIK</h3>
                    <p>
                        Pasang aplikasi KOMINFIK agar akses lebih cepat,
                        nyaman, dan dapat digunakan seperti aplikasi di perangkat
                        Anda.
                    </p>
                </div>
            </div>

            <div class="actions">
                <button class="btn-dismiss" @click="dismissAlert">
                    Nanti
                </button>

                <button class="btn-install" @click="installApp">
                    Install
                </button>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.pwa-install-alert {
    position: fixed;
    left: 20px;
    right: 20px;
    bottom: 24px;

    max-width: 430px;
    margin: auto;

    background: #ffffff;
    border: 1px solid #fed7aa;
    border-radius: 18px;

    padding: 18px;

    box-shadow:
        0 12px 32px rgba(0, 0, 0, .12),
        0 2px 8px rgba(249, 115, 22, .12);

    z-index: 999999;
}

.content {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.icon {
    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    border-radius: 14px;

    background: linear-gradient(135deg, #fb923c, #f97316);

    color: #fff;
}

.text h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #111827;
}

.text p {
    margin-top: 6px;
    font-size: 14px;
    line-height: 1.6;
    color: #6b7280;
}

.actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 18px;
}

.actions button {
    cursor: pointer;
    border: none;
    border-radius: 12px;
    padding: 10px 18px;
    font-size: 14px;
    font-weight: 600;
    transition: .25s;
}

.btn-install {
    color: #fff;
    background: linear-gradient(135deg, #fb923c, #f97316);
}

.btn-install:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(249, 115, 22, .35);
}

.btn-dismiss {
    background: #f9fafb;
    color: #4b5563;
    border: 1px solid #e5e7eb;
}

.btn-dismiss:hover {
    background: #f3f4f6;
}

.fade-enter-active,
.fade-leave-active {
    transition: all .35s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(24px);
}
</style>
