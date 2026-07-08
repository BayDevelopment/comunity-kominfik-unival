<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    UserCircle,
    Plus,
    Search,
    Pencil,
    Trash2,
    Eye,
    ChevronLeft,
    ChevronRight,
    FolderOpen,
    SlidersHorizontal,
    Phone,
    MapPin,
    Building2,
} from '@lucide/vue';
import Swal from 'sweetalert2';
import { ref, watch, computed } from 'vue';
import { dashboard } from '@/routes';

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
        ],
    },
});

interface Anggota {
    id: number;
    nama: string;
    foto: string | null;
    email: string;
    no_telepon: string | null;
    jabatan: string;
    divisi: string | null;
    alamat: string | null;
    tanggal_bergabung: string | null;
    status: 'aktif' | 'nonaktif';
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
    anggotas?: Paginated<Anggota>;
    filters?: {
        search?: string;
        status?: string;
        divisi?: string;
    };
}>();

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');
const divisi = ref(props.filters?.divisi ?? '');

const statusConfig: Record<
    Anggota['status'],
    { label: string; class: string }
> = {
    aktif: {
        label: 'Aktif',
        class: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
    },
    nonaktif: {
        label: 'Nonaktif',
        class: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
    },
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

function getInitials(nama: string) {
    if (!nama) {
        return '?';
    }

    return nama
        .split(' ')
        .map((word) => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

function getFotoUrl(foto: string | null): string | undefined {
    if (!foto) {
        return undefined;
    }

    // Jika foto sudah berupa URL lengkap
    if (foto.startsWith('http://') || foto.startsWith('https://')) {
        return foto;
    }

    // Jika foto hanya nama file, sesuaikan dengan path penyimpanan
    return `/storage/${foto}`;
}

// Computed untuk data anggota
const anggotaList = computed(() => props.anggotas?.data ?? []);
const hasData = computed(() => anggotaList.value.length > 0);

let debounceTimer: ReturnType<typeof setTimeout>;

function applyFilters() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            '/anggota',
            {
                search: search.value || undefined,
                status: status.value || undefined,
                divisi: divisi.value || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 350);
}

watch([search, status, divisi], applyFilters);

function goToPage(page: number) {
    router.get(
        '/anggota',
        {
            search: search.value || undefined,
            status: status.value || undefined,
            divisi: divisi.value || undefined,
            page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

const deletingId = ref<number | null>(null);

const hapusAnggota = (anggota: Anggota) => {
    Swal.fire({
        title: 'Hapus Anggota?',
        html: `Anggota <b>"${anggota.nama}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
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
            deletingId.value = anggota.id;
            router.delete(`/anggota/${anggota.id}`, {
                preserveScroll: true,
                onFinish: () => {
                    deletingId.value = null;
                },
            });
        }
    });
};

// Handle error image dengan function terpisah
const handleImageError = (event: Event, nama: string) => {
    const img = event.target as HTMLImageElement;
    img.style.display = 'none';
    const parent = img.parentElement;

    if (parent) {
        const fallback = document.createElement('div');
        fallback.className =
            'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-semibold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400';
        fallback.textContent = getInitials(nama);
        parent.appendChild(fallback);
    }
};
</script>

<template>
    <Head title="Anggota" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold">Data Anggota</h1>

                <p class="text-sm text-muted-foreground">
                    Kelola seluruh anggota komunitas KOMINFIK.
                </p>
            </div>

            <Link
                href="/anggota/create"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90"
            >
                <Plus class="h-4 w-4" />
                Tambah Anggota
            </Link>
        </div>

        <!-- Filter Bar -->
        <div
            class="flex flex-col gap-3 rounded-xl border bg-background p-4 shadow-sm sm:flex-row sm:items-center"
        >
            <div class="relative flex-1">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama, email, atau no telepon..."
                    class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm ring-offset-background transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                />
            </div>

            <div class="relative sm:w-48">
                <SlidersHorizontal
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <select
                    v-model="status"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring"
                >
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="relative sm:w-48">
                <Building2
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <select
                    v-model="divisi"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring"
                >
                    <option value="">Semua Divisi</option>
                    <option value="IT">IT</option>
                    <option value="HRD">HRD</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Finance">Finance</option>
                    <option value="Operasional">Operasional</option>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div
                v-if="!hasData"
                class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-muted"
                >
                    <FolderOpen class="h-6 w-6 text-muted-foreground" />
                </div>
                <div>
                    <p class="font-medium">Belum ada anggota</p>
                    <p class="text-sm text-muted-foreground">
                        Anggota yang ditambahkan akan muncul di sini.
                    </p>
                </div>
                <Link
                    href="/anggota/create"
                    class="mt-2 inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition-colors hover:bg-muted"
                >
                    <Plus class="h-4 w-4" />
                    Tambah Anggota Pertama
                </Link>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b bg-muted/40 text-left text-xs tracking-wide text-muted-foreground uppercase"
                        >
                            <th class="px-6 py-3 font-medium">Anggota</th>
                            <th class="px-6 py-3 font-medium">Kontak</th>
                            <th class="px-6 py-3 font-medium">Jabatan</th>
                            <th class="px-6 py-3 font-medium">Divisi</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Bergabung</th>
                            <th class="px-6 py-3 text-right font-medium">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="anggota in anggotaList"
                            :key="anggota.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar dengan foto -->
                                    <div
                                        v-if="getFotoUrl(anggota.foto)"
                                        class="h-9 w-9 shrink-0 overflow-hidden rounded-lg"
                                    >
                                        <img
                                            :src="getFotoUrl(anggota.foto)"
                                            :alt="anggota.nama"
                                            class="h-full w-full object-cover"
                                            @error="
                                                (event) =>
                                                    handleImageError(
                                                        event,
                                                        anggota.nama,
                                                    )
                                            "
                                        />
                                    </div>
                                    <div
                                        v-else
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-semibold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"
                                    >
                                        <UserCircle class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <span class="font-medium">{{
                                            anggota.nama
                                        }}</span>
                                        <span
                                            v-if="anggota.alamat"
                                            class="mt-0.5 flex items-center gap-1 text-xs text-muted-foreground"
                                        >
                                            <MapPin class="h-3 w-3" />
                                            {{ anggota.alamat }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-0.5">
                                    <div class="text-muted-foreground">
                                        {{ anggota.email }}
                                    </div>
                                    <div
                                        v-if="anggota.no_telepon"
                                        class="flex items-center gap-1 text-xs text-muted-foreground"
                                    >
                                        <Phone class="h-3 w-3" />
                                        {{ anggota.no_telepon }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ anggota.jabatan }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    v-if="anggota.divisi"
                                    class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400"
                                >
                                    {{ anggota.divisi }}
                                </span>
                                <span v-else class="text-muted-foreground"
                                    >—</span
                                >
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                    :class="statusConfig[anggota.status].class"
                                >
                                    {{ statusConfig[anggota.status].label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ formatTanggal(anggota.tanggal_bergabung) }}
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Link
                                        :href="`/anggota/${anggota.id}`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Lihat detail"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <Link
                                        :href="`/anggota/${anggota.id}/edit`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Edit anggota"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button
                                        type="button"
                                        :disabled="deletingId === anggota.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-rose-500/10"
                                        title="Hapus anggota"
                                        @click="hapusAnggota(anggota)"
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
                v-if="hasData"
                class="flex flex-col gap-3 border-t px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <p class="text-sm text-muted-foreground">
                    Menampilkan {{ props.anggotas?.from ?? 0 }}–{{
                        props.anggotas?.to ?? 0
                    }}
                    dari {{ props.anggotas?.total ?? 0 }} anggota
                </p>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        :disabled="(props.anggotas?.current_page ?? 1) <= 1"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage((props.anggotas?.current_page ?? 1) - 1)"
                    >
                        <ChevronLeft class="h-4 w-4" />
                        Sebelumnya
                    </button>

                    <span class="px-2 text-sm text-muted-foreground">
                        {{ props.anggotas?.current_page ?? 1 }} /
                        {{ props.anggotas?.last_page ?? 1 }}
                    </span>

                    <button
                        type="button"
                        :disabled="
                            (props.anggotas?.current_page ?? 1) >=
                            (props.anggotas?.last_page ?? 1)
                        "
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage((props.anggotas?.current_page ?? 1) + 1)"
                    >
                        Selanjutnya
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>