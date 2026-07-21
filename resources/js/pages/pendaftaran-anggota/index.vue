<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    Search,
    Eye,
    Pencil,
    Trash2,
    Check,
    X,
    ChevronLeft,
    ChevronRight,
    FolderX,
    SlidersHorizontal,
} from 'lucide-vue-next';
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
                title: 'Pendaftaran Anggota',
                href: '/pendaftaran-anggota',
            },
        ],
    },
});

interface PendaftaranAnggota {
    id: number;
    nama: string;
    nim_nis: string;
    asal_instansi: string;
    jenjang: 'mahasiswa' | 'sma' | 'smk';
    jurusan_prodi: string | null;
    angkatan: string | null;
    email: string;
    no_telepon: string;
    alamat: string | null;
    alasan_bergabung: string | null;
    file_cv: string | null;
    foto: string | null;
    status: 'pending' | 'diterima' | 'ditolak';
    catatan_admin: string | null;
    tanggal_diproses: string | null;
    created_at: string;
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
    pendaftarans?: Paginated<PendaftaranAnggota>;
    filters?: {
        search?: string;
        status?: string;
        jenjang?: string;
    };
}>();

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');
const jenjang = ref(props.filters?.jenjang ?? '');

const statusConfig: Record<PendaftaranAnggota['status'], { label: string; class: string }> = {
    pending: {
        label: 'Pending',
        class: 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
    },
    diterima: {
        label: 'Diterima',
        class: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
    },
    ditolak: {
        label: 'Ditolak',
        class: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
    },
};

const jenjangLabel: Record<PendaftaranAnggota['jenjang'], string> = {
    mahasiswa: 'Mahasiswa',
    sma: 'SMA',
    smk: 'SMK',
};

function formatTanggal(value: string | null): string {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value));
}

function initials(nama: string): string {
    return nama
        .split(' ')
        .slice(0, 2)
        .map((n) => n.charAt(0).toUpperCase())
        .join('');
}

let debounceTimer: ReturnType<typeof setTimeout>;

function applyFilters() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            '/pendaftaran-anggota',
            {
                search: search.value || undefined,
                status: status.value || undefined,
                jenjang: jenjang.value || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 350);
}

watch([search, status, jenjang], applyFilters);

function goToPage(page: number) {
    router.get(
        '/pendaftaran-anggota',
        {
            search: search.value || undefined,
            status: status.value || undefined,
            jenjang: jenjang.value || undefined,
            page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

const processingId = ref<number | null>(null);

function terimaPendaftaran(item: PendaftaranAnggota) {
    Swal.fire({
        title: 'Terima Pendaftar?',
        html: `Pendaftaran <b>"${item.nama}"</b> akan diterima sebagai anggota.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Terima',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            processingId.value = item.id;
            router.patch(
                `/pendaftaran-anggota/${item.id}/terima`,
                {},
                {
                    preserveScroll: true,
                    onFinish: () => {
                        processingId.value = null;
                    },
                },
            );
        }
    });
}

function tolakPendaftaran(item: PendaftaranAnggota) {
    Swal.fire({
        title: 'Tolak Pendaftar?',
        html: `Pendaftaran <b>"${item.nama}"</b> akan ditolak.`,
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Catatan (opsional)',
        inputPlaceholder: 'Alasan penolakan...',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            processingId.value = item.id;
            router.patch(
                `/pendaftaran-anggota/${item.id}/tolak`,
                { catatan_admin: result.value || null },
                {
                    preserveScroll: true,
                    onFinish: () => {
                        processingId.value = null;
                    },
                },
            );
        }
    });
}

const deletingId = ref<number | null>(null);

function hapusPendaftaran(item: PendaftaranAnggota) {
    Swal.fire({
        title: 'Hapus Pendaftaran?',
        html: `Data pendaftaran <b>"${item.nama}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
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
            deletingId.value = item.id;
            router.delete(`/pendaftaran-anggota/${item.id}`, {
                preserveScroll: true,
                onFinish: () => {
                    deletingId.value = null;
                },
            });
        }
    });
}
</script>

<template>

    <Head title="Pendaftaran Anggota" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Pendaftaran Anggota</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola data pendaftar yang ingin bergabung menjadi anggota.
                </p>
            </div>

            <Link href="/pendaftaran-anggota/create"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90">
                <Plus class="h-4 w-4" />
                Tambah Pendaftar
            </Link>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col gap-3 rounded-xl border bg-background p-4 shadow-sm sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input v-model="search" type="text" placeholder="Cari nama, NIM/NIS, atau email..."
                    class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm ring-offset-background transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring" />
            </div>

            <div class="relative sm:w-48">
                <SlidersHorizontal
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <select v-model="jenjang"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring">
                    <option value="">Semua Jenjang</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="sma">SMA</option>
                    <option value="smk">SMK</option>
                </select>
            </div>

            <div class="relative sm:w-48">
                <SlidersHorizontal
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <select v-model="status"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div v-if="!pendaftarans || pendaftarans.data.length === 0"
                class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <FolderX class="h-6 w-6 text-muted-foreground" />
                </div>
                <div>
                    <p class="font-medium">Belum ada pendaftar</p>
                    <p class="text-sm text-muted-foreground">
                        Data pendaftar anggota akan muncul di sini.
                    </p>
                </div>
                <Link href="/pendaftaran-anggota/create"
                    class="mt-2 inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition-colors hover:bg-muted">
                    <Plus class="h-4 w-4" />
                    Tambah Pendaftar Pertama
                </Link>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b bg-muted/40 text-left text-xs tracking-wide text-muted-foreground uppercase">
                            <th class="px-6 py-3 font-medium">Pendaftar</th>
                            <th class="px-6 py-3 font-medium">Instansi / Jenjang</th>
                            <th class="px-6 py-3 font-medium">Kontak</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Tanggal Daftar</th>
                            <th class="px-6 py-3 text-right font-medium">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="item in pendaftarans.data" :key="item.id"
                            class="transition-colors hover:bg-muted/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div v-if="item.foto"
                                        class="h-9 w-9 shrink-0 overflow-hidden rounded-full bg-muted">
                                        <img :src="`/storage/${item.foto}`" :alt="item.nama"
                                            class="h-full w-full object-cover" />
                                    </div>
                                    <div v-else
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-semibold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                        {{ initials(item.nama) }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ item.nama }}</p>
                                        <p class="text-xs text-muted-foreground">
                                            {{ item.nim_nis }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-foreground">{{ item.asal_instansi }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ jenjangLabel[item.jenjang] }}
                                    <span v-if="item.jurusan_prodi"> · {{ item.jurusan_prodi }}</span>
                                </p>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                <p>{{ item.email }}</p>
                                <p class="text-xs">{{ item.no_telepon }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                    :class="statusConfig[item.status].class">
                                    {{ statusConfig[item.status].label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ formatTanggal(item.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button v-if="item.status === 'pending'" type="button"
                                        :disabled="processingId === item.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-emerald-50 hover:text-emerald-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-emerald-500/10"
                                        title="Terima pendaftar" @click="terimaPendaftaran(item)">
                                        <Check class="h-4 w-4" />
                                    </button>

                                    <button v-if="item.status === 'pending'" type="button"
                                        :disabled="processingId === item.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-rose-500/10"
                                        title="Tolak pendaftar" @click="tolakPendaftaran(item)">
                                        <X class="h-4 w-4" />
                                    </button>

                                    <Link :href="`/pendaftaran-anggota/${item.id}/edit`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Edit data pendaftar">
                                        <Pencil class="h-4 w-4" />
                                    </Link>

                                    <Link :href="`/pendaftaran-anggota/${item.id}`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Lihat detail">
                                        <Eye class="h-4 w-4" />
                                    </Link>

                                    <button type="button" :disabled="deletingId === item.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-rose-500/10"
                                        title="Hapus pendaftaran" @click="hapusPendaftaran(item)">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="pendaftarans && pendaftarans.data.length > 0"
                class="flex flex-col gap-3 border-t px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-muted-foreground">
                    Menampilkan {{ pendaftarans.from }}–{{ pendaftarans.to }} dari
                    {{ pendaftarans.total }} pendaftar
                </p>

                <div class="flex items-center gap-2">
                    <button type="button" :disabled="pendaftarans.current_page <= 1"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(pendaftarans.current_page - 1)">
                        <ChevronLeft class="h-4 w-4" />
                        Sebelumnya
                    </button>

                    <span class="px-2 text-sm text-muted-foreground">
                        {{ pendaftarans.current_page }} / {{ pendaftarans.last_page }}
                    </span>

                    <button type="button" :disabled="pendaftarans.current_page >= pendaftarans.last_page"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(pendaftarans.current_page + 1)">
                        Selanjutnya
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
