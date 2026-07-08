<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    FolderKanban,
    ArrowLeft,
    Pencil,
    Building2,
    User,
    CalendarDays,
    FileText,
    Image as ImageIcon,
    Code2,
    Gauge,
    Clock,
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
                title: 'Project',
                href: '/project',
            },
            {
                title: 'Detail Project',
                href: '#',
            },
        ],
    },
});

interface Project {
    id: number;
    nama: string;
    gambar: string | null;
    deskripsi: string | null;
    klien: string | null;
    pic: string | null;
    teknologi: string | null;
    status: 'aktif' | 'selesai' | 'ditunda' | 'dibatalkan';
    progress: number;
    mulai: string | null;
    selesai: string | null;
    created_at?: string;
    updated_at?: string;
}

defineProps<{
    project: Project;
}>();

const statusStyles: Record<Project['status'], string> = {
    aktif: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
    selesai: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
    ditunda: 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
    dibatalkan: 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
};

const statusLabels: Record<Project['status'], string> = {
    aktif: 'Aktif',
    selesai: 'Selesai',
    ditunda: 'Ditunda',
    dibatalkan: 'Dibatalkan',
};

function formatDate(value: string | null): string {
    if (!value) {
        return '-';
    }

    return new Date(value).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function formatTeknologi(value: string | null): string[] {
    if (!value) {
        return [];
    }

    return value
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean);
}
</script>

<template>

    <Head :title="`Detail Project - ${project.nama}`" />

    <div class="container mx-auto max-w-4xl py-6">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <Link href="/project"
                    class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
                <h1 class="text-2xl font-bold">Detail Project</h1>
            </div>

            <Link :href="`/project/${project.id}/edit`"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 md:hidden">
                <Pencil class="h-4 w-4" />
                Edit Project
            </Link>

        </div>

        <!-- Detail Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b bg-muted/40 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                        <FolderKanban class="h-4 w-4 text-blue-500" />
                    </div>
                    <div>
                        <p class="font-medium">Informasi Project</p>
                        <p class="text-xs text-muted-foreground">
                            Detail lengkap mengenai project ini.
                        </p>
                    </div>
                </div>

                <span class="rounded-full px-3 py-1 text-xs font-medium" :class="statusStyles[project.status]">
                    {{ statusLabels[project.status] }}
                </span>
            </div>

            <div class="grid gap-5 px-6 py-6 sm:grid-cols-2">
                <!-- Gambar Project -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Gambar Project</p>

                    <img v-if="project.gambar" :src="project.gambar" :alt="project.nama"
                        class="h-48 w-full max-w-sm rounded-lg border object-cover" />

                    <div v-else
                        class="flex h-40 w-full max-w-sm flex-col items-center justify-center gap-2 rounded-lg border border-dashed bg-muted/20 px-6 py-8 text-center">
                        <ImageIcon class="h-6 w-6 text-muted-foreground" />
                        <span class="text-sm text-muted-foreground">
                            Tidak ada gambar
                        </span>
                    </div>
                </div>

                <!-- Nama Project -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Nama Project</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <FolderKanban class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ project.nama }}</span>
                    </div>
                </div>

                <!-- Klien -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Klien / Instansi</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <Building2 class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ project.klien || '-' }}</span>
                    </div>
                </div>

                <!-- PIC -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">PIC (Penanggung Jawab)</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <User class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ project.pic || '-' }}</span>
                    </div>
                </div>

                <!-- Teknologi -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Teknologi</p>
                    <div class="flex flex-wrap items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <Code2 class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <template v-if="formatTeknologi(project.teknologi).length">
                            <span v-for="tech in formatTeknologi(project.teknologi)" :key="tech"
                                class="rounded-full bg-background px-2.5 py-0.5 text-xs font-medium">
                                {{ tech }}
                            </span>
                        </template>
                        <span v-else>-</span>
                    </div>
                </div>

                <!-- Progress -->
                <div class="sm:col-span-2">
                    <div class="mb-1.5 flex items-center justify-between text-sm font-medium">
                        <span class="flex items-center gap-1.5">
                            <Gauge class="h-4 w-4 text-muted-foreground" />
                            Progress
                        </span>
                        <span class="text-muted-foreground">{{ project.progress }}%</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                        <div class="h-full rounded-full bg-primary transition-all"
                            :style="{ width: `${project.progress}%` }" />
                    </div>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Tanggal Mulai</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <CalendarDays class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ formatDate(project.mulai) }}</span>
                    </div>
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <p class="mb-1.5 text-sm font-medium">Tanggal Selesai</p>
                    <div class="flex items-center gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <CalendarDays class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ formatDate(project.selesai) }}</span>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="sm:col-span-2">
                    <p class="mb-1.5 text-sm font-medium">Deskripsi</p>
                    <div class="flex items-start gap-2 rounded-lg border bg-muted/20 px-3 py-2.5 text-sm">
                        <FileText class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />
                        <span class="whitespace-pre-line">{{ project.deskripsi || '-' }}</span>
                    </div>
                </div>

                <!-- Meta Info -->
                <div v-if="project.created_at || project.updated_at" class="sm:col-span-2">
                    <div class="flex flex-wrap items-center gap-4 text-xs text-muted-foreground">
                        <span v-if="project.created_at" class="flex items-center gap-1.5">
                            <Clock class="h-3.5 w-3.5" />
                            Dibuat: {{ formatDate(project.created_at) }}
                        </span>
                        <span v-if="project.updated_at" class="flex items-center gap-1.5">
                            <Clock class="h-3.5 w-3.5" />
                            Diperbarui: {{ formatDate(project.updated_at) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex flex-col-reverse gap-3 border-t bg-muted/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                <Link href="/project"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    Kembali ke Daftar
                </Link>
                <Link :href="`/project/${project.id}/edit`"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90">
                    <Pencil class="h-4 w-4" />
                    Edit Project
                </Link>
            </div>
        </div>
    </div>
</template>
