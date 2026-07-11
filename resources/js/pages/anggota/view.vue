<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Users,
    ArrowLeft,
    Pencil,
    Mail,
    Phone,
    Building2,
    MapPin,
    CalendarDays,
    BadgeCheck,
    Clock,
} from '@lucide/vue';
import { dashboard } from '@/routes';

interface Anggota {
    id: number;
    nama: string;
    foto: string | null;
    email: string;
    no_telepon: string;
    jabatan: string;
    divisi: string;
    alamat: string | null;
    tanggal_bergabung: string;
    status: 'aktif' | 'tidak_aktif';
}

defineProps<{
    anggota: Anggota;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Anggota',
                href: '/anggota',
            },
            {
                title: 'Detail Anggota',
                href: '#',
            },
        ],
    },
});

const statusLabel: Record<Anggota['status'], string> = {
    aktif: 'Aktif',
    tidak_aktif: 'Tidak Aktif',
};

const statusStyle: Record<Anggota['status'], string> = {
    aktif: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
    tidak_aktif: 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
};

function formatTanggal(tanggal: string) {
    if (!tanggal) {
        return '-';
    }

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}
</script>

<template>
    <Head :title="`Detail Anggota - ${anggota.nama}`" />

    <div class="container mx-auto max-w-4xl py-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <Link
                    href="/anggota"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
                <h1 class="text-2xl font-bold">Detail Anggota</h1>
            </div>

            <Link
                :href="`/anggota/${anggota.id}/edit`"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 md:hidden"
            >
                <Pencil class="h-4 w-4" />
                Edit Anggota
            </Link>
        </div>

        <!-- Detail Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b bg-muted/40 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10"
                    >
                        <Users class="h-4 w-4 text-blue-500" />
                    </div>
                    <div>
                        <p class="font-medium">Data Anggota</p>
                        <p class="text-xs text-muted-foreground">
                            Informasi lengkap data anggota.
                        </p>
                    </div>
                </div>

                <span
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium"
                    :class="statusStyle[anggota.status]"
                >
                    <BadgeCheck class="h-3.5 w-3.5" />
                    {{ statusLabel[anggota.status] }}
                </span>
            </div>

            <div class="grid gap-5 px-6 py-6 sm:grid-cols-2">
                <!-- Foto + Nama -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Foto</p>
                    <div class="flex items-center gap-4">
                        <img
                            v-if="anggota.foto"
                            :src="`/storage/${anggota.foto}`"
                            :alt="anggota.nama"
                            class="h-20 w-20 rounded-full border object-cover"
                        />
                        <div
                            v-else
                            class="flex h-20 w-20 items-center justify-center rounded-full border bg-muted text-2xl font-semibold text-muted-foreground"
                        >
                            {{ anggota.nama.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">{{ anggota.nama }}</h2>
                            <p class="text-sm text-muted-foreground">{{ anggota.jabatan }}</p>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Email</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <Mail class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ anggota.email }}</span>
                    </div>
                </div>

                <!-- No Telepon -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">No Telepon</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <Phone class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ anggota.no_telepon }}</span>
                    </div>
                </div>

                <!-- Divisi -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Divisi</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <Building2 class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ anggota.divisi }}</span>
                    </div>
                </div>

                <!-- Tanggal Bergabung -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Tanggal Bergabung</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <CalendarDays class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ formatTanggal(anggota.tanggal_bergabung) }}</span>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Alamat</p>
                    <div class="flex items-start gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <MapPin class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ anggota.alamat || '-' }}</span>
                    </div>
                </div>

                <!-- Meta Info -->
                <div class="sm:col-span-2">
                    <div class="flex flex-wrap items-center gap-4 text-xs text-muted-foreground">
                        <span class="flex items-center gap-1.5">
                            <Clock class="h-3.5 w-3.5" />
                            Status: {{ statusLabel[anggota.status] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex flex-col-reverse gap-3 border-t bg-muted/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-end"
            >
                <Link
                    href="/anggota"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted"
                >
                    Kembali ke Daftar
                </Link>
                <Link
                    :href="`/anggota/${anggota.id}/edit`"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90"
                >
                    <Pencil class="h-4 w-4" />
                    Edit Anggota
                </Link>
            </div>
        </div>
    </div>
</template>
