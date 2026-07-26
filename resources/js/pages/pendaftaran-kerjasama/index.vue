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
    Clock,
    ChevronLeft,
    ChevronRight,
    FolderX,
    SlidersHorizontal,
    Building2,
} from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Kerjasama',
                href: '/pendaftaran-kerjasama',
            },
        ],
    },
});

interface Kerjasama {
    id: number;
    jenis_instansi: 'kampus' | 'sma' | 'smk' | 'perusahaan' | 'lainnya';
    nama_instansi: string;
    alamat: string | null;
    nama_pic: string;
    jabatan_pic: string | null;
    email_pic: string;
    no_hp_pic: string;
    jenis_kerjasama: string | null;
    deskripsi_kerjasama: string | null;
    file_proposal: string | null;
    file_mou: string | null;
    status: 'pending' | 'diproses' | 'disetujui' | 'ditolak';
    catatan_admin: string | null;
    tanggal_pengajuan: string;
    tanggal_diproses: string | null;
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
    kerjasamas?: Paginated<Kerjasama>;
    filters?: {
        search?: string;
        status?: string;
        jenis_instansi?: string;
    };
}>();

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');
const jenisInstansi = ref(props.filters?.jenis_instansi ?? '');

const statusConfig: Record<Kerjasama['status'], { label: string; class: string }> = {
    pending: {
        label: 'Pending',
        class: 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
    },
    diproses: {
        label: 'Diproses',
        class: 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
    },
    disetujui: {
        label: 'Disetujui',
        class: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
    },
    ditolak: {
        label: 'Ditolak',
        class: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
    },
};

const jenisInstansiLabel: Record<Kerjasama['jenis_instansi'], string> = {
    kampus: 'Kampus',
    sma: 'SMA',
    smk: 'SMK',
    perusahaan: 'Perusahaan',
    lainnya: 'Lainnya',
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
            '/pendaftaran-kerjasama',
            {
                search: search.value || undefined,
                status: status.value || undefined,
                jenis_instansi: jenisInstansi.value || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 350);
}

watch([search, status, jenisInstansi], applyFilters);

function goToPage(page: number) {
    router.get(
        '/pendaftaran-kerjasama',
        {
            search: search.value || undefined,
            status: status.value || undefined,
            jenis_instansi: jenisInstansi.value || undefined,
            page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

const processingId = ref<number | null>(null);

function prosesKerjasama(item: Kerjasama) {
    Swal.fire({
        title: 'Proses Pengajuan?',
        html: `Pengajuan kerjasama dari <b>"${item.nama_instansi}"</b> akan ditandai sedang diproses.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            processingId.value = item.id;
            router.patch(
                `/pendaftaran-kerjasama/${item.id}/proses`,
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

function setujuiKerjasama(item: Kerjasama) {
    Swal.fire({
        title: 'Setujui Kerjasama?',
        html: `Pengajuan kerjasama dari <b>"${item.nama_instansi}"</b> akan disetujui.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            processingId.value = item.id;
            router.patch(
                `/pendaftaran-kerjasama/${item.id}/terima`,
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

function tolakKerjasama(item: Kerjasama) {
    Swal.fire({
        title: 'Tolak Kerjasama?',
        html: `Pengajuan kerjasama dari <b>"${item.nama_instansi}"</b> akan ditolak.`,
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
                `/pendaftaran-kerjasama/${item.id}/tolak`,
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

function hapusKerjasama(item: Kerjasama) {
    Swal.fire({
        title: 'Hapus Pengajuan?',
        html: `Data kerjasama dari <b>"${item.nama_instansi}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
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
            router.delete(`/pendaftaran-kerjasama/${item.id}`, {
                preserveScroll: true,
                onFinish: () => {
                    deletingId.value = null;
                },
            });
        }
    });
}

function reloadData() {
    router.reload({
        only: ['kerjasamas'],
        preserveScroll: true,
        preserveState: true,
    });
}

let echoDebounce: ReturnType<typeof setTimeout>;

function debouncedReload() {
    clearTimeout(echoDebounce);
    echoDebounce = setTimeout(reloadData, 300);
}

onMounted(() => {
    window.Echo.private('kerjasama')
        .listen('.created', debouncedReload)
        .listen('.updated', debouncedReload)
        .listen('.deleted', debouncedReload);
});

onUnmounted(() => {
    window.Echo.leave('kerjasama');
    clearTimeout(echoDebounce);
});
</script>

<template>

    <Head title="Kerjasama" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Pengajuan Kerjasama</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola pengajuan kerjasama dari instansi mitra.
                </p>
            </div>

            <Link href="/pendaftaran-kerjasama/create"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90">
                <Plus class="h-4 w-4" />
                Tambah Kerjasama
            </Link>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col gap-3 rounded-xl border bg-background p-4 shadow-sm sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input v-model="search" type="text" placeholder="Cari nama instansi, PIC, atau email..."
                    class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm ring-offset-background transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring" />
            </div>

            <div class="relative sm:w-48">
                <SlidersHorizontal
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <select v-model="jenisInstansi"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring">
                    <option value="">Semua Jenis Instansi</option>
                    <option value="kampus">Kampus</option>
                    <option value="sma">SMA</option>
                    <option value="smk">SMK</option>
                    <option value="perusahaan">Perusahaan</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="relative sm:w-48">
                <SlidersHorizontal
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <select v-model="status"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="diproses">Diproses</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div v-if="!kerjasamas || kerjasamas.data.length === 0"
                class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <FolderX class="h-6 w-6 text-muted-foreground" />
                </div>
                <div>
                    <p class="font-medium">Belum ada pengajuan kerjasama</p>
                    <p class="text-sm text-muted-foreground">
                        Pengajuan kerjasama dari instansi akan muncul di sini.
                    </p>
                </div>
                <Link href="/pendaftaran-kerjasama/create"
                    class="mt-2 inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition-colors hover:bg-muted">
                    <Plus class="h-4 w-4" />
                    Tambah Kerjasama Pertama
                </Link>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b bg-muted/40 text-left text-xs tracking-wide text-muted-foreground uppercase">
                            <th class="px-6 py-3 font-medium">Instansi</th>
                            <th class="px-6 py-3 font-medium">PIC</th>
                            <th class="px-6 py-3 font-medium">Jenis Kerjasama</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Tanggal Pengajuan</th>
                            <th class="px-6 py-3 text-right font-medium">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="item in kerjasamas.data" :key="item.id"
                            class="transition-colors hover:bg-muted/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                        <Building2 class="h-4 w-4 text-blue-500" />
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ item.nama_instansi }}</p>
                                        <p class="text-xs text-muted-foreground">
                                            {{ jenisInstansiLabel[item.jenis_instansi] }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-muted text-xs font-semibold text-muted-foreground">
                                        {{ initials(item.nama_pic) }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ item.nama_pic }}</p>
                                        <p class="text-xs text-muted-foreground">
                                            {{ item.jabatan_pic ?? item.email_pic }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ item.jenis_kerjasama ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                    :class="statusConfig[item.status].class">
                                    {{ statusConfig[item.status].label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ formatTanggal(item.tanggal_pengajuan) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button v-if="item.status === 'pending'" type="button"
                                        :disabled="processingId === item.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-blue-50 hover:text-blue-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-blue-500/10"
                                        title="Proses pengajuan" @click="prosesKerjasama(item)">
                                        <Clock class="h-4 w-4" />
                                    </button>
                                    <button v-if="item.status === 'pending' || item.status === 'diproses'"
                                        type="button" :disabled="processingId === item.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-emerald-50 hover:text-emerald-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-emerald-500/10"
                                        title="Setujui kerjasama" @click="setujuiKerjasama(item)">
                                        <Check class="h-4 w-4" />
                                    </button>
                                    <button v-if="item.status === 'pending' || item.status === 'diproses'"
                                        type="button" :disabled="processingId === item.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-rose-500/10"
                                        title="Tolak kerjasama" @click="tolakKerjasama(item)">
                                        <X class="h-4 w-4" />
                                    </button>

                                    <Link :href="`/pendaftaran-kerjasama/${item.id}/edit`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Edit data kerjasama">
                                        <Pencil class="h-4 w-4" />
                                    </Link>

                                    <Link :href="`/pendaftaran-kerjasama/${item.id}`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Lihat detail">
                                        <Eye class="h-4 w-4" />
                                    </Link>

                                    <button type="button" :disabled="deletingId === item.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-rose-500/10"
                                        title="Hapus pengajuan" @click="hapusKerjasama(item)">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="kerjasamas && kerjasamas.data.length > 0"
                class="flex flex-col gap-3 border-t px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-muted-foreground">
                    Menampilkan {{ kerjasamas.from }}–{{ kerjasamas.to }} dari
                    {{ kerjasamas.total }} pengajuan
                </p>

                <div class="flex items-center gap-2">
                    <button type="button" :disabled="kerjasamas.current_page <= 1"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(kerjasamas.current_page - 1)">
                        <ChevronLeft class="h-4 w-4" />
                        Sebelumnya
                    </button>

                    <span class="px-2 text-sm text-muted-foreground">
                        {{ kerjasamas.current_page }} / {{ kerjasamas.last_page }}
                    </span>

                    <button type="button" :disabled="kerjasamas.current_page >= kerjasamas.last_page"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(kerjasamas.current_page + 1)">
                        Selanjutnya
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
