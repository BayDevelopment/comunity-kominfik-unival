<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    FolderKanban,
    Plus,
    Search,
    Pencil,
    Trash2,
    Eye,
    ChevronLeft,
    ChevronRight,
    FolderX,
    SlidersHorizontal,
} from '@lucide/vue';
import { ref, watch } from 'vue';
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
        ],
    },
});

interface Project {
    id: number
    nama: string
    klien: string
    status: 'aktif' | 'selesai' | 'ditunda' | 'dibatalkan'
    mulai: string
    selesai: string | null
    pic: string
}

interface Paginated<T> {
    data: T[]
    current_page: number
    last_page: number
    from: number | null
    to: number | null
    total: number
}

const props = defineProps<{
    projects?: Paginated<Project>
    filters?: {
        search?: string
        status?: string
    }
}>();

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');

const statusConfig: Record<Project['status'], { label: string; class: string }> = {
    aktif: { label: 'Aktif', class: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20' },
    selesai: { label: 'Selesai', class: 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20' },
    ditunda: { label: 'Ditunda', class: 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20' },
    dibatalkan: { label: 'Dibatalkan', class: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20' },
};

function formatTanggal(value: string | null) {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

let debounceTimer: ReturnType<typeof setTimeout>;

function applyFilters() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/project', {
            search: search.value || undefined,
            status: status.value || undefined,
        }, {
            preserveState: true,
            replace: true,
        });
    }, 350);
}

watch([search, status], applyFilters);

function goToPage(page: number) {
    router.get('/project', {
        search: search.value || undefined,
        status: status.value || undefined,
        page,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function hapusProject(project: Project) {
    if (confirm(`Hapus project "${project.nama}"? Tindakan ini tidak dapat dibatalkan.`)) {
        router.delete(`/project/${project.id}`, {
            preserveScroll: true,
        });
    }
}
</script>

<template>

    <Head title="Project" />

    <div class="space-y-6 p-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">
                    Data Project
                </h1>

                <p class="text-sm text-muted-foreground">
                    Kelola seluruh project KOMINFIK dalam satu tempat.
                </p>
            </div>

            <Link
                href="/project/create"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90"
            >
                <Plus class="h-4 w-4" />
                Tambah Project
            </Link>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col gap-3 rounded-xl border bg-background p-4 shadow-sm sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama project atau klien..."
                    class="w-full rounded-lg border bg-background py-2.5 pl-9 pr-3 text-sm outline-none ring-offset-background transition-shadow placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                />
            </div>

            <div class="relative sm:w-52">
                <SlidersHorizontal class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <select
                    v-model="status"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pl-9 pr-3 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                >
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="selesai">Selesai</option>
                    <option value="ditunda">Ditunda</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">

            <div v-if="!projects || projects.data.length === 0" class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <FolderX class="h-6 w-6 text-muted-foreground" />
                </div>
                <div>
                    <p class="font-medium">Belum ada project</p>
                    <p class="text-sm text-muted-foreground">
                        Project yang ditambahkan akan muncul di sini.
                    </p>
                </div>
                <Link
                    href="/project/create"
                    class="mt-2 inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition-colors hover:bg-muted"
                >
                    <Plus class="h-4 w-4" />
                    Tambah Project Pertama
                </Link>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
                            <th class="px-6 py-3 font-medium">Project</th>
                            <th class="px-6 py-3 font-medium">Klien / Instansi</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Periode</th>
                            <th class="px-6 py-3 font-medium">PIC</th>
                            <th class="px-6 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="project in projects.data"
                            :key="project.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                        <FolderKanban class="h-4 w-4 text-blue-500" />
                                    </div>
                                    <span class="font-medium">{{ project.nama }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ project.klien }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                    :class="statusConfig[project.status].class"
                                >
                                    {{ statusConfig[project.status].label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ formatTanggal(project.mulai) }} — {{ formatTanggal(project.selesai) }}
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ project.pic }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <Link
                                        :href="`/project/${project.id}`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Lihat detail"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <Link
                                        :href="`/project/${project.id}/edit`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Edit project"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button
                                        type="button"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10"
                                        title="Hapus project"
                                        @click="hapusProject(project)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="projects && projects.data.length > 0"
                class="flex flex-col gap-3 border-t px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <p class="text-sm text-muted-foreground">
                    Menampilkan {{ projects.from }}–{{ projects.to }} dari {{ projects.total }} project
                </p>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="projects.current_page <= 1"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(projects.current_page - 1)"
                    >
                        <ChevronLeft class="h-4 w-4" />
                        Sebelumnya
                    </button>

                    <span class="px-2 text-sm text-muted-foreground">
                        {{ projects.current_page }} / {{ projects.last_page }}
                    </span>

                    <button
                        type="button"
                        :disabled="projects.current_page >= projects.last_page"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(projects.current_page + 1)"
                    >
                        Selanjutnya
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>

        </div>

    </div>
</template>
