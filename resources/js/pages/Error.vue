<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Home, RefreshCw, ShieldAlert, ServerCrash, FileQuestion } from 'lucide-vue-next';
import { computed } from 'vue';

defineOptions({
    layout: false,
});

interface User {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    status: number;
    auth?: {
        user: User | null;
    };
}>();

const content = computed(() => ({
    403: {
        title: 'Akses Ditolak',
        description: 'Kamu tidak memiliki izin untuk mengakses halaman ini.',
        icon: ShieldAlert,
    },
    404: {
        title: 'Halaman Tidak Ditemukan',
        description: 'Maaf, halaman yang kamu cari tidak ada atau sudah dipindahkan.',
        icon: FileQuestion,
    },
    419: {
        title: 'Sesi Berakhir',
        description: 'Sesi kamu sudah kedaluwarsa. Silakan muat ulang halaman.',
        icon: RefreshCw,
    },
    429: {
        title: 'Terlalu Banyak Permintaan',
        description: 'Kamu melakukan terlalu banyak percobaan. Coba lagi sebentar lagi.',
        icon: RefreshCw,
    },
    500: {
        title: 'Terjadi Kesalahan Server',
        description: 'Ada yang tidak beres di server kami. Tim kami sudah diberi tahu.',
        icon: ServerCrash,
    },
    503: {
        title: 'Layanan Tidak Tersedia',
        description: 'Sistem sedang dalam pemeliharaan. Silakan coba lagi nanti.',
        icon: ServerCrash,
    },
}[props.status] ?? {
    title: 'Terjadi Kesalahan',
    description: 'Sesuatu yang tidak terduga terjadi.',
    icon: FileQuestion,
}));

const backHref = computed(() => (props.auth?.user ? '/dashboard' : '/'));
const backLabel = computed(() => (props.auth?.user ? 'Kembali ke Dashboard' : 'Kembali ke Beranda'));
</script>

<template>
    <Head :title="content.title" />

    <div class="flex min-h-screen flex-col items-center justify-center gap-6 bg-background px-6 text-center">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-muted">
            <component :is="content.icon" class="h-10 w-10 text-muted-foreground" />
        </div>

        <div class="space-y-2">
            <p class="text-sm font-medium text-muted-foreground">Error {{ status }}</p>
            <h1 class="text-3xl font-bold">{{ content.title }}</h1>
            <p class="max-w-md text-muted-foreground">{{ content.description }}</p>
        </div>

        <Link :href="backHref"
            class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90">
            <Home class="h-4 w-4" />
            {{ backLabel }}
        </Link>
    </div>
</template>
