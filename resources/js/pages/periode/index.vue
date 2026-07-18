<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    Search,
    Pencil,
    Trash2,
    Eye,
    ChevronLeft,
    ChevronRight,
    FolderX,
    SlidersHorizontal,
    Users,
    Handshake,
    CalendarRange,
    ToggleLeft,
    ToggleRight,
    Loader2,
} from '@lucide/vue';
import Swal from 'sweetalert2';
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
                title: 'Periode Pendaftaran',
                href: '/periode-pendaftaran',
            },
        ],
    },
});

interface PeriodePendaftaran {
    id: number;
    jenis: 'anggota' | 'kerjasama';
    nama_periode: string | null;
    tanggal_mulai: string | null;
    tanggal_selesai: string | null;
    status: 'active' | 'nonactive';
    created_at: string;
}

// Konfigurasi Jenis
const jenisConfig: Record<PeriodePendaftaran['jenis'], { label: string; class: string; icon: any }> = {
    anggota: {
        label: 'Anggota',
        class: 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
        icon: Users,
    },
    kerjasama: {
        label: 'Kerjasama',
        class: 'bg-purple-50 text-purple-700 ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/20',
        icon: Handshake,
    },
};

// Konfigurasi Status
const statusConfig: Record<PeriodePendaftaran['status'], { label: string; class: string }> = {
    active: {
        label: 'Dibuka',
        class: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
    },
    nonactive: {
        label: 'Ditutup',
        class: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
    },
};

interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    periodes?: Paginated<PeriodePendaftaran>;
    filters?: {
        search?: string;
        jenis?: string;
        status?: string;
    };
}>();

const search = ref(props.filters?.search ?? '');
const jenis = ref(props.filters?.jenis ?? '');
const status = ref(props.filters?.status ?? '');

// Format tanggal
function formatDate(date: string | null): string {
    if (!date) {
        return '—';
    }

    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

let debounceTimer: ReturnType<typeof setTimeout>;

function applyFilters() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            '/periode-pendaftaran',
            {
                search: search.value || undefined,
                jenis: jenis.value || undefined,
                status: status.value || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 350);
}

watch([search, jenis, status], applyFilters);

function goToPage(page: number) {
    router.get(
        '/periode-pendaftaran',
        {
            search: search.value || undefined,
            jenis: jenis.value || undefined,
            status: status.value || undefined,
            page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

const deletingId = ref<number | null>(null);
const togglingId = ref<number | null>(null);

const toggleStatus = (periode: PeriodePendaftaran) => {
    const akan = periode.status === 'active' ? 'ditutup' : 'dibuka';

    Swal.fire({
        title: `${periode.status === 'active' ? 'Tutup' : 'Buka'} Pendaftaran?`,
        html: `Pendaftaran <b>"${periode.nama_periode ?? jenisConfig[periode.jenis].label}"</b> akan ${akan}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Ya, ${periode.status === 'active' ? 'Tutup' : 'Buka'}`,
        cancelButtonText: 'Batal',
        confirmButtonColor: periode.status === 'active' ? '#e11d48' : '#059669',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            togglingId.value = periode.id;
            router.patch(
                `/periode-pendaftaran/${periode.id}/toggle-status`,
                {},
                {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => {
                        togglingId.value = null;
                    },
                },
            );
        }
    });
};

const hapusPeriode = (periode: PeriodePendaftaran) => {
    Swal.fire({
        title: 'Hapus Periode?',
        html: `Periode <b>"${periode.nama_periode ?? jenisConfig[periode.jenis].label}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusCancel: true,
    }).then((result) => {
        if (result.isConfirmed) {
            deletingId.value = periode.id;
            router.delete(`/periode-pendaftaran/${periode.id}`, {
                preserveScroll: true,
                onFinish: () => {
                    deletingId.value = null;
                },
            });
        }
    });
};
</script>

<template>

    <Head title="Periode Pendaftaran" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Periode Pendaftaran</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola periode buka/tutup pendaftaran anggota dan kerjasama.
                </p>
            </div>

            <Link href="/periode-pendaftaran/create"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90">
                <Plus class="h-4 w-4" />
                Tambah Periode
            </Link>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col gap-3 rounded-xl border bg-background p-4 shadow-sm sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input v-model="search" type="text" placeholder="Cari nama periode..."
                    class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm ring-offset-background transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring" />
            </div>

            <div class="relative sm:w-44">
                <SlidersHorizontal
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <select v-model="jenis"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring">
                    <option value="">Semua Jenis</option>
                    <option value="anggota">Anggota</option>
                    <option value="kerjasama">Kerjasama</option>
                </select>
            </div>

            <div class="relative sm:w-44">
                <SlidersHorizontal
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <select v-model="status"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring">
                    <option value="">Semua Status</option>
                    <option value="active">Dibuka</option>
                    <option value="nonactive">Ditutup</option>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div v-if="!periodes || periodes.data.length === 0"
                class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <FolderX class="h-6 w-6 text-muted-foreground" />
                </div>
                <div>
                    <p class="font-medium">Belum ada periode pendaftaran</p>
                    <p class="text-sm text-muted-foreground">
                        Periode yang ditambahkan akan muncul di sini.
                    </p>
                </div>
                <Link href="/periode-pendaftaran/create"
                    class="mt-2 inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition-colors hover:bg-muted">
                    <Plus class="h-4 w-4" />
                    Tambah Periode Pertama
                </Link>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b bg-muted/40 text-left text-xs tracking-wide text-muted-foreground uppercase">
                            <th class="px-6 py-3 font-medium">Nama Periode</th>
                            <th class="px-6 py-3 font-medium">Jenis</th>
                            <th class="px-6 py-3 font-medium">Tanggal Mulai</th>
                            <th class="px-6 py-3 font-medium">Tanggal Selesai</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 text-right font-medium">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="periode in periodes.data" :key="periode.id"
                            class="transition-colors hover:bg-muted/30">
                            <td class="px-6 py-4">
                                <span class="font-medium">
                                    {{ periode.nama_periode || '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                    :class="jenisConfig[periode.jenis].class">
                                    <component :is="jenisConfig[periode.jenis].icon" class="h-3 w-3" />
                                    {{ jenisConfig[periode.jenis].label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <CalendarRange class="h-3.5 w-3.5" />
                                    <span>{{ formatDate(periode.tanggal_mulai) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <CalendarRange class="h-3.5 w-3.5" />
                                    <span>{{ formatDate(periode.tanggal_selesai) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="statusConfig[periode.status].class">
                                        {{ statusConfig[periode.status].label }}
                                    </span>
                                    <button type="button" :disabled="togglingId === periode.id"
                                        class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground disabled:pointer-events-none disabled:opacity-40"
                                        :title="periode.status === 'active' ? 'Tutup pendaftaran' : 'Buka pendaftaran'"
                                        @click="toggleStatus(periode)">
                                        <Loader2 v-if="togglingId === periode.id" class="h-3.5 w-3.5 animate-spin" />
                                        <ToggleRight v-else-if="periode.status === 'active'"
                                            class="h-4 w-4 text-emerald-600" />
                                        <ToggleLeft v-else class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="`/periode-pendaftaran/${periode.id}`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Lihat detail">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <Link :href="`/periode-pendaftaran/${periode.id}/edit`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Edit periode">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button type="button" :disabled="deletingId === periode.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-rose-500/10"
                                        title="Hapus periode" @click="hapusPeriode(periode)">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="periodes && periodes.data.length > 0"
                class="flex flex-col gap-3 border-t px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-muted-foreground">
                    Menampilkan {{ periodes.from }}–{{ periodes.to }} dari
                    {{ periodes.total }} periode
                </p>

                <div class="flex items-center gap-2">
                    <button type="button" :disabled="periodes.current_page <= 1"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(periodes.current_page - 1)">
                        <ChevronLeft class="h-4 w-4" />
                        Sebelumnya
                    </button>

                    <span class="px-2 text-sm text-muted-foreground">
                        {{ periodes.current_page }} / {{ periodes.last_page }}
                    </span>

                    <button type="button" :disabled="periodes.current_page >= periodes.last_page"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(periodes.current_page + 1)">
                        Selanjutnya
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
