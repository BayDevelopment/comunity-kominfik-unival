<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    BriefcaseBusiness,
    FolderKanban,
    Handshake,
    Users,
    BarChart3,
    CalendarDays,
    UserPlus,
} from '@lucide/vue';
import { computed } from 'vue';
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

interface Activity {
    type: 'project' | 'kerjasama' | 'anggota';
    title: string;
    description: string;
    time: string;
}

const props = defineProps<{
    stats?: {
        project: number;
        kerjasama: number;
        layanan: number;
        member: number;
    };
    chart?: {
        labels: string[];
        data: number[];
    };
    activities?: Activity[];
}>();

// Tinggi bar dihitung relatif terhadap nilai tertinggi di dataset (biar proporsional)
const maxChartValue = computed(() => {
    const data = props.chart?.data ?? [];

    return Math.max(...data, 1); // minimal 1 supaya tidak dibagi 0
});

function barHeight(value: number): string {
    return `${Math.max((value / maxChartValue.value) * 100, value > 0 ? 6 : 2)}%`;
}

const activityIcon: Record<Activity['type'], typeof FolderKanban> = {
    project: FolderKanban,
    kerjasama: Handshake,
    anggota: UserPlus,
};

const activityColor: Record<Activity['type'], string> = {
    project: 'bg-blue-50 text-blue-500 dark:bg-blue-500/10',
    kerjasama: 'bg-green-50 text-green-500 dark:bg-green-500/10',
    anggota: 'bg-purple-50 text-purple-500 dark:bg-purple-500/10',
};
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

                <div class="mb-6 flex items-center gap-2">

                    <BarChart3 class="h-5 w-5" />

                    <h2 class="font-semibold">
                        Project Baru per Bulan
                    </h2>

                </div>

                <!-- State kosong -->
                <div v-if="!chart || chart.labels.length === 0"
                    class="flex h-80 items-center justify-center rounded-lg border border-dashed text-sm text-muted-foreground">
                    Belum ada data untuk ditampilkan.
                </div>

                <!-- Bar chart sederhana (CSS, tanpa dependency tambahan) -->
                <div v-else class="flex h-80 items-end justify-between gap-3 px-2">
                    <div v-for="(label, index) in chart.labels" :key="label"
                        class="group flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span
                            class="text-xs font-semibold text-muted-foreground opacity-0 transition group-hover:opacity-100">
                            {{ chart.data[index] }}
                        </span>

                        <div class="relative flex w-full flex-1 items-end justify-center">
                            <div class="w-3/5 rounded-t-lg bg-gradient-to-t from-blue-500 to-blue-400 transition-all duration-500"
                                :style="{ height: barHeight(chart.data[index]) }"></div>
                        </div>

                        <span class="text-xs font-medium text-muted-foreground">
                            {{ label }}
                        </span>
                    </div>
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

                <!-- State kosong -->
                <div v-if="!activities || activities.length === 0"
                    class="rounded-lg border p-3 text-sm text-muted-foreground">
                    Belum ada kegiatan terbaru.
                </div>

                <div v-else class="max-h-[400px] space-y-3 overflow-y-auto overscroll-contain pr-2">
                    <div v-for="(activity, index) in activities" :key="index"
                        class="flex items-start gap-3 rounded-lg border p-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                            :class="activityColor[activity.type]">
                            <component :is="activityIcon[activity.type]" class="h-4 w-4" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">
                                {{ activity.title }}
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ activity.description }}
                            </p>
                            <p class="mt-1 text-[11px] text-muted-foreground">
                                {{ activity.time }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</template>
