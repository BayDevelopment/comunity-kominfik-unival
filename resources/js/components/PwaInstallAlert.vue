<script setup lang="ts">
import { usePwaInstall } from '@/composables/usePwaInstall'

const { showInstallAlert, installApp, dismissAlert } = usePwaInstall()
</script>

<template>
  <Transition name="fade">
    <div v-if="showInstallAlert" class="pwa-install-alert">
      <p>Install Komunitas Mahasiswa Informatika ke perangkat kamu?</p>

      <div class="actions">
        <button class="btn-install" @click="installApp">Install</button>
        <button class="btn-dismiss" @click="dismissAlert">Nanti saja</button>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.pwa-install-alert {
  position: fixed;
  bottom: 20px;
  left: 20px;
  right: 20px;
  max-width: 420px;
  margin: 0 auto;
  background: #1f2937;
  color: white;
  padding: 16px 20px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
  z-index: 999999;
}

.pwa-install-alert p {
  margin: 0 0 12px;
  font-size: 14px;
}

.actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
}

.actions button {
  padding: 8px 14px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
}

.btn-install {
  background: #3b82f6;
  color: white;
}

.btn-dismiss {
  background: transparent;
  color: #d1d5db;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
