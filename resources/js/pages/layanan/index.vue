<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Wrench,
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
                title: 'Layanan',
                href: '/layanan',
            },
        ],
    },
});

interface Layanan {
    id: number;
    nama: string;
    kategori: string;
    status: 'aktif' | 'nonaktif';
    biaya: number | null;
    deskripsi: string | null;
}

interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    layanans?: Paginated<Layanan>;
    filters?: {
        search?: string;
        status?: string;
    };
}>();

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');

const statusConfig: Record<Layanan['status'], { label: string; class: string }> = {
    aktif: {
        label: 'Aktif',
        class: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
    },
    nonaktif: {
        label: 'Nonaktif',
        class: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
    },
};

// ✅ FUNGSI FORMAT HARGA YANG SUDAH DIPERBAIKI
function formatHarga(value: string | number | null): string {
    // Handle null, undefined, atau empty
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    let numericValue: number;

    if (typeof value === 'string') {
        // Bersihkan string dari semua karakter non-digit
        const cleaned = value.replace(/[^0-9]/g, '');
        numericValue = parseInt(cleaned, 10);
    } else {
        numericValue = value;
    }

    // Cek apakah hasilnya valid
    if (isNaN(numericValue) || numericValue <= 0) {
        return '—';
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(numericValue);
}

let debounceTimer: ReturnType<typeof setTimeout>;

function applyFilters() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            '/layanan',
            {
                search: search.value || undefined,
                status: status.value || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 350);
}

watch([search, status], applyFilters);

function goToPage(page: number) {
    router.get(
        '/layanan',
        {
            search: search.value || undefined,
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

const hapusLayanan = (layanan: Layanan) => {
    Swal.fire({
        title: 'Hapus Layanan?',
        html: `Layanan <b>"${layanan.nama}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
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
            deletingId.value = layanan.id;
            router.delete(`/layanan/${layanan.id}`, {
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

    <Head title="Layanan" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Data Layanan</h1>

                <p class="text-sm text-muted-foreground">
                    Kelola seluruh layanan yang tersedia.
                </p>
            </div>

            <Link href="/layanan/create"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90">
                <Plus class="h-4 w-4" />
                Tambah Layanan
            </Link>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col gap-3 rounded-xl border bg-background p-4 shadow-sm sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input v-model="search" type="text" placeholder="Cari nama atau kategori layanan..."
                    class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm ring-offset-background transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring" />
            </div>

            <div class="relative sm:w-52">
                <SlidersHorizontal
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <select v-model="status"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div v-if="!layanans || layanans.data.length === 0"
                class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <FolderX class="h-6 w-6 text-muted-foreground" />
                </div>
                <div>
                    <p class="font-medium">Belum ada layanan</p>
                    <p class="text-sm text-muted-foreground">
                        Layanan yang ditambahkan akan muncul di sini.
                    </p>
                </div>
                <Link href="/layanan/create"
                    class="mt-2 inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition-colors hover:bg-muted">
                    <Plus class="h-4 w-4" />
                    Tambah Layanan Pertama
                </Link>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b bg-muted/40 text-left text-xs tracking-wide text-muted-foreground uppercase">
                            <th class="px-6 py-3 font-medium">Layanan</th>
                            <th class="px-6 py-3 font-medium">Kategori</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Harga</th>
                            <th class="px-6 py-3 text-right font-medium">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="layanan in layanans.data" :key="layanan.id"
                            class="transition-colors hover:bg-muted/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                        <Wrench class="h-4 w-4 text-blue-500" />
                                    </div>
                                    <span class="font-medium">{{
                                        layanan.nama
                                        }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ layanan.kategori }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                    :class="statusConfig[layanan.status].class">
                                    {{ statusConfig[layanan.status].label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ formatHarga(layanan.biaya) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="`/layanan/${layanan.id}`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Lihat detail">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <Link :href="`/layanan/${layanan.id}/edit`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Edit layanan">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button type="button" :disabled="deletingId === layanan.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-rose-500/10"
                                        title="Hapus layanan" @click="hapusLayanan(layanan)">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="layanans && layanans.data.length > 0"
                class="flex flex-col gap-3 border-t px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-muted-foreground">
                    Menampilkan {{ layanans.from }}–{{ layanans.to }} dari
                    {{ layanans.total }} layanan
                </p>

                <div class="flex items-center gap-2">
                    <button type="button" :disabled="layanans.current_page <= 1"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(layanans.current_page - 1)">
                        <ChevronLeft class="h-4 w-4" />
                        Sebelumnya
                    </button>

                    <span class="px-2 text-sm text-muted-foreground">
                        {{ layanans.current_page }} / {{ layanans.last_page }}
                    </span>

                    <button type="button" :disabled="layanans.current_page >= layanans.last_page"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(layanans.current_page + 1)">
                        Selanjutnya
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
