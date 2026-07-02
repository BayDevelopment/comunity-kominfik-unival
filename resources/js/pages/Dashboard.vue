<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    BriefcaseBusiness,
    FolderKanban,
    Handshake,
    Users,
    BarChart3,
    CalendarDays,
} from '@lucide/vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

// Nanti data dari DashboardController
defineProps<{
    stats?: {
        project: number
        kerjasama: number
        layanan: number
        member: number
    }
}>()
</script>

<template>

    <Head title="Dashboard" />

    <div class="space-y-6 p-6">

        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold">
                Dashboard
            </h1>

            <p class="text-sm text-muted-foreground">
                Selamat datang di Dashboard KOMINFIK.
            </p>
        </div>

        <!-- Summary Card -->
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">

            <!-- Project -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-muted-foreground">
                        Total Project
                    </span>

                    <FolderKanban class="h-6 w-6 text-blue-500" />
                </div>

                <h2 class="mt-4 text-4xl font-bold">
                    {{ stats?.project ?? 0 }}
                </h2>
            </div>

            <!-- Kerjasama -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-muted-foreground">
                        Kerjasama
                    </span>

                    <Handshake class="h-6 w-6 text-green-500" />
                </div>

                <h2 class="mt-4 text-4xl font-bold">
                    {{ stats?.kerjasama ?? 0 }}
                </h2>
            </div>

            <!-- Layanan -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-muted-foreground">
                        Layanan
                    </span>

                    <BriefcaseBusiness class="h-6 w-6 text-orange-500" />
                </div>

                <h2 class="mt-4 text-4xl font-bold">
                    {{ stats?.layanan ?? 0 }}
                </h2>
            </div>

            <!-- Member -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-muted-foreground">
                        Member
                    </span>

                    <Users class="h-6 w-6 text-purple-500" />
                </div>

                <h2 class="mt-4 text-4xl font-bold">
                    {{ stats?.member ?? 0 }}
                </h2>
            </div>

        </div>

        <!-- Chart -->
        <div class="grid gap-6 lg:grid-cols-3">

            <div class="col-span-2 rounded-xl border bg-background p-6 shadow-sm">

                <div class="mb-4 flex items-center gap-2">

                    <BarChart3 class="h-5 w-5" />

                    <h2 class="font-semibold">
                        Grafik Data
                    </h2>

                </div>

                <div class="flex h-80 items-center justify-center rounded-lg border border-dashed">
                    Grafik akan ditampilkan di sini (Chart.js)
                </div>

            </div>

            <!-- Kegiatan Terbaru -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">

                <div class="mb-4 flex items-center gap-2">

                    <CalendarDays class="h-5 w-5 text-blue-500" />

                    <h2 class="font-semibold">
                        Kegiatan Terbaru
                    </h2>

                </div>

                <div class="space-y-4">

                    <div class="rounded-lg border p-3">
                        Belum ada kegiatan terbaru.
                    </div>

                    <div class="rounded-lg border p-3">
                        Belum ada kegiatan terbaru.
                    </div>

                    <div class="rounded-lg border p-3">
                        Belum ada kegiatan terbaru.
                    </div>

                </div>

            </div>

        </div>

    </div>
</template>
