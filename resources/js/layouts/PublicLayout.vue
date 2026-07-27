<script setup lang="ts">
import Footer from '@/components/Footer.vue';
import Navbar from '@/components/Navbar.vue';
import PwaInstallAlert from '@/components/PwaInstallAlert.vue'
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900">
        <Navbar />

        <main>
            <slot />
            <PwaInstallAlert />
        </main>

        <Footer />
    </div>
</template>
