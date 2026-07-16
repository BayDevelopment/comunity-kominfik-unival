<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Clock,
    Globe,
    Monitor,
    Smartphone,
    MapPin,
    History,
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
                title: 'Activity Log',
                href: '/activity-log',
            },
        ],
    },
});

interface LoginLog {
    id: number;
    created_at: string;
    ip: string;
    browser: string;
    platform: string;
    device: string;
    location: string;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    logs: Paginated<LoginLog>;
}>();

function goToPage(url: string | null) {
    if (!url) {
        return;
    }

    window.location.href = url;
}
</script>

<template>

    <Head title="Activity Log" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold">Activity Log</h1>
            <p class="text-sm text-muted-foreground">
                Riwayat login ke akun kamu, termasuk IP, browser, dan lokasi.
            </p>
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div v-if="props.logs.data.length === 0"
                class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <History class="h-6 w-6 text-muted-foreground" />
                </div>
                <div>
                    <p class="font-medium">Belum ada riwayat login</p>
                    <p class="text-sm text-muted-foreground">
                        Aktivitas login kamu akan muncul di sini.
                    </p>
                </div>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b bg-muted/40 text-left text-xs tracking-wide text-muted-foreground uppercase">
                            <th class="px-6 py-3 font-medium">Waktu</th>
                            <th class="px-6 py-3 font-medium">IP Address</th>
                            <th class="px-6 py-3 font-medium">Browser</th>
                            <th class="px-6 py-3 font-medium">Platform</th>
                            <th class="px-6 py-3 font-medium">Device</th>
                            <th class="px-6 py-3 font-medium">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="log in props.logs.data" :key="log.id" class="transition-colors hover:bg-muted/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <Clock class="h-3.5 w-3.5" />
                                    {{ log.created_at }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                    <Globe class="h-3 w-3" />
                                    {{ log.ip }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ log.browser }}
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ log.platform }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-muted-foreground">
                                    <Smartphone v-if="log.device === 'Mobile'" class="h-3.5 w-3.5" />
                                    <Monitor v-else class="h-3.5 w-3.5" />
                                    {{ log.device }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-1.5 text-muted-foreground">
                                    <MapPin class="h-3.5 w-3.5" />
                                    {{ log.location }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="props.logs.data.length > 0 && props.logs.links.length > 3"
                class="flex flex-col gap-3 border-t px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-muted-foreground">
                    Menampilkan {{ props.logs.data.length }} aktivitas
                </p>

                <div class="flex flex-wrap items-center gap-1">
                    <a v-for="(link, i) in props.logs.links" :key="i" :href="link.url ?? undefined" v-html="link.label"
                        class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium transition-colors"
                        :class="{
                            'bg-primary text-primary-foreground': link.active,
                            'border hover:bg-muted': !link.active && link.url,
                            'pointer-events-none border text-muted-foreground/40':
                                !link.url,
                        }" @click.prevent="goToPage(link.url)" />
                </div>
            </div>
        </div>
    </div>
</template>
