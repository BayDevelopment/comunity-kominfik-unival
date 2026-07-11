<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Wrench,
    ArrowLeft,
    Pencil,
    Tag,
    Clock,
    FileText,
    Image as ImageIcon,
    DollarSign,
    CheckCircle,
    XCircle,
    Info,
} from '@lucide/vue';

import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Layanan',
                href: '/layanan',
            },
            {
                title: 'Detail Layanan',
                href: '#',
            },
        ],
    },
});

interface Layanan {
    id: number;
    nama: string;
    gambar: string | null;
    kategori: string | null;
    deskripsi: string | null;
    syarat: string[] | null;   // ✅ diubah dari string jadi string[]
    estimasi_waktu: string | null;
    biaya: number | null;
    biaya_formatted: string | null;
    gambar_url: string | null;
    status: 'aktif' | 'nonaktif';
    created_at: string | null;
    updated_at: string | null;
}

defineProps<{
    layanan: Layanan;
}>();

const statusStyles: Record<Layanan['status'], string> = {
    aktif: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
    nonaktif: 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
};

const statusLabels: Record<Layanan['status'], string> = {
    aktif: 'Aktif',
    nonaktif: 'Nonaktif',
};

function formatDate(value: string | null): string {
    if (!value) {
        return '-';
    }

    return new Date(value).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>

    <Head :title="`Detail Layanan - ${layanan.nama}`" />

    <div class="container mx-auto max-w-4xl py-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <Link href="/layanan"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
                <h1 class="text-2xl font-bold">Detail Layanan</h1>
            </div>

            <Link :href="`/layanan/${layanan.id}/edit`"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 md:hidden">
                <Pencil class="h-4 w-4" />
                Edit Layanan
            </Link>
        </div>

        <!-- Detail Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b bg-muted/40 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                        <Wrench class="h-4 w-4 text-blue-500" />
                    </div>
                    <div>
                        <p class="font-medium">Informasi Layanan</p>
                        <p class="text-xs text-muted-foreground">
                            Detail lengkap mengenai layanan ini.
                        </p>
                    </div>
                </div>

                <span class="rounded-full px-3 py-1 text-xs font-medium" :class="statusStyles[layanan.status]">
                    {{ statusLabels[layanan.status] }}
                </span>
            </div>

            <div class="grid gap-5 px-6 py-6 sm:grid-cols-2">
                <!-- Gambar Layanan -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Gambar Layanan</p>

                    <img v-if="layanan.gambar_url" :src="layanan.gambar_url ?? undefined" :alt="layanan.nama"
                        class="h-48 w-full max-w-sm rounded-lg border object-cover" />

                    <div v-else
                        class="flex h-40 w-full max-w-sm flex-col items-center justify-center gap-2 rounded-lg border border-dashed bg-muted/20 px-6 py-8 text-center">
                        <ImageIcon class="h-6 w-6 text-muted-foreground" />
                        <span class="text-sm text-muted-foreground">
                            Tidak ada gambar
                        </span>
                    </div>
                </div>

                <!-- Nama Layanan -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Nama Layanan</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <Wrench class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ layanan.nama }}</span>
                    </div>
                </div>

                <!-- Kategori -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Kategori</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <Tag class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ layanan.kategori || '-' }}</span>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Status</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <component :is="layanan.status === 'aktif' ? CheckCircle : XCircle" class="h-4 w-4 shrink-0"
                            :class="layanan.status === 'aktif' ? 'text-emerald-500' : 'text-rose-500'" />
                        <span>{{ statusLabels[layanan.status] }}</span>
                    </div>
                </div>

                <!-- Biaya -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Biaya</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <DollarSign class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                            {{ layanan.biaya_formatted || '-' }}
                        </span>
                    </div>
                </div>

                <!-- Estimasi Waktu -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Estimasi Waktu</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <Clock class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ layanan.estimasi_waktu || '-' }}</span>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Deskripsi</p>
                    <div class="flex items-start gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <FileText class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />
                        <span class="whitespace-pre-line">{{ layanan.deskripsi || '-' }}</span>
                    </div>
                </div>

                <!-- Syarat & Ketentuan -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Syarat & Ketentuan</p>
                    <div class="flex flex-wrap items-start gap-2 rounded-lg border bg-muted/20 px-3 py-2.5">
                        <Info v-if="!layanan.syarat?.length" class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />

                        <template v-if="layanan.syarat?.length">
                            <span v-for="(item, i) in layanan.syarat" :key="i"
                                class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                {{ item }}
                            </span>
                        </template>
                        <span v-else class="text-sm text-muted-foreground">-</span>
                    </div>
                </div>

                <!-- Meta Info -->
                <div v-if="layanan.created_at || layanan.updated_at" class="sm:col-span-2">
                    <div class="flex flex-wrap items-center gap-4 text-xs text-muted-foreground">
                        <span v-if="layanan.created_at" class="flex items-center gap-1.5">
                            <Clock class="h-3.5 w-3.5" />
                            Dibuat: {{ formatDate(layanan.created_at) }}
                        </span>
                        <span v-if="layanan.updated_at" class="flex items-center gap-1.5">
                            <Clock class="h-3.5 w-3.5" />
                            Diperbarui: {{ formatDate(layanan.updated_at) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex flex-col-reverse gap-3 border-t bg-muted/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                <Link href="/layanan"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    Kembali ke Daftar
                </Link>

                <Link :href="`/layanan/${layanan.id}/edit`"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90">
                    <Pencil class="h-4 w-4" />
                    Edit Layanan
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    .container {
        max-width: 100% !important;
        padding: 0 !important;
    }

    button,
    .button,
    a:not([href^="/layanan/"]) {
        display: none !important;
    }

    .bg-background {
        background-color: white !important;
    }

    .border {
        border-color: #e5e7eb !important;
    }

    .shadow-sm {
        box-shadow: none !important;
    }

    .dark\:bg-background {
        background-color: white !important;
    }
}
</style>