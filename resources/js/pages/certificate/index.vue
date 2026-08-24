<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Award,
    Plus,
    Search,
    Download,
    Eye,
    Ban,
    Trash2,
    FileCheck2,
    FileClock,
    FileX2,
    ChevronLeft,
    ChevronRight,
    AlertTriangle,
} from 'lucide-vue-next';
import { reactive, ref, watch } from 'vue';

import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineOptions({
    layout: AppSidebarLayout,
});

interface CertificateRow {
    id: number;
    certificate_number: string;
    recipient_name: string;
    recipient_email: string;
    event_name: string | null;
    course_name: string | null;
    status: 'draft' | 'published' | 'revoked';
    issued_at: string;
    download_count: number;
    verification_code: string;
}

interface PaginatedCertificates {
    data: CertificateRow[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    from: number | null;
    to: number | null;
    total: number;
}

interface Stats {
    total: number;
    published: number;
    draft: number;
    revoked: number;
}

const props = defineProps<{
    certificates: PaginatedCertificates;
    stats: Stats;
    filters: {
        search: string | null;
        status: string | null;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sertifikat',
        href: '/certificate',
    },
];

const filters = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
});

// State Modal Cabut
const showRevokeModal = ref(false);
const selectedCertificate = ref<CertificateRow | null>(null);
const revokeReason = ref('');

// State Modal Hapus
const showDeleteModal = ref(false);
const certificateToDeleteId = ref<number | null>(null);

let filterTimeout: ReturnType<typeof setTimeout> | undefined;

const applyFilters = () => {
    if (filterTimeout) clearTimeout(filterTimeout);

    filterTimeout = setTimeout(() => {
    router.get(
        '/certificate',
        {
            search: filters.search || undefined,
            status: filters.status || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
    }, 350);
};

watch(filters, applyFilters);

function formatDate(date: string): string {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function statusStyle(status: CertificateRow['status']): string {
    return (
        {
            published:
                'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-900/40',
            draft: 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',
            revoked:
                'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-900/40',
        }[status] ?? ''
    );
}

function statusLabel(status: CertificateRow['status']): string {
    return (
        {
            published: 'Terbit',
            draft: 'Draf',
            revoked: 'Dicabut',
        }[status] ?? status
    );
}

function openRevokeModal(item: CertificateRow) {
    selectedCertificate.value = item;
    revokeReason.value = '';
    showRevokeModal.value = true;
}

function submitRevoke() {
    if (!selectedCertificate.value || !revokeReason.value.trim()) return;

    router.patch(
        `/certificate/${selectedCertificate.value.id}/revoke`,
        {
            revoke_reason: revokeReason.value.trim(),
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                showRevokeModal.value = false;
                selectedCertificate.value = null;
                revokeReason.value = '';
            },
        },
    );
}

function confirmDelete(id: number) {
    certificateToDeleteId.value = id;
    showDeleteModal.value = true;
}

function cancelDelete() {
    showDeleteModal.value = false;
    certificateToDeleteId.value = null;
}

function deleteCertificate() {
    if (certificateToDeleteId.value !== null) {
        router.delete(`/certificate/${certificateToDeleteId.value}`, {
            preserveScroll: true,
            onFinish: () => {
                cancelDelete();
            },
        });
    }
}
</script>

<template>
    <Head title="Manajemen Sertifikat" />

    <div class="space-y-6 p-4 sm:p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Sertifikat</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola data dan penerbitan sertifikat peserta.
                </p>
            </div>

            <Link
                href="/certificate/create"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600"
            >
                <Plus class="h-4 w-4" />
                Terbitkan Sertifikat
            </Link>
        </div>

        <!-- Stats Card -->
        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6 xl:grid-cols-4">
            <div class="rounded-xl border bg-background p-6 shadow-sm dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-muted-foreground">Total Sertifikat</span>
                    <Award class="h-6 w-6 text-orange-500 dark:text-orange-400" />
                </div>
                <h2 class="mt-4 text-4xl font-bold text-foreground">{{ stats.total }}</h2>
            </div>

            <div class="rounded-xl border bg-background p-6 shadow-sm dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-muted-foreground">Terbit</span>
                    <FileCheck2 class="h-6 w-6 text-emerald-500 dark:text-emerald-400" />
                </div>
                <h2 class="mt-4 text-4xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.published }}</h2>
            </div>

            <div class="rounded-xl border bg-background p-6 shadow-sm dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-muted-foreground">Draf</span>
                    <FileClock class="h-6 w-6 text-amber-500 dark:text-amber-400" />
                </div>
                <h2 class="mt-4 text-4xl font-bold text-amber-600 dark:text-amber-400">{{ stats.draft }}</h2>
            </div>

            <div class="rounded-xl border bg-background p-6 shadow-sm dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-muted-foreground">Dicabut</span>
                    <FileX2 class="h-6 w-6 text-rose-500 dark:text-rose-400" />
                </div>
                <h2 class="mt-4 text-4xl font-bold text-rose-600 dark:text-rose-400">{{ stats.revoked }}</h2>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-col gap-3 rounded-xl border bg-background p-4 shadow-sm dark:border-slate-800 lg:flex-row lg:items-center">
            <div class="relative flex-1">
                <Search class="pointer-events-none absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input
                    v-model="filters.search"
                    type="text"
                    placeholder="Cari nama, email, acara, atau nomor sertifikat..."
                    class="w-full rounded-lg border bg-background py-2 pr-4 pl-10 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                />
            </div>

            <div class="flex w-full lg:w-auto">
                <select
                    v-model="filters.status"
                    class="w-full rounded-lg border bg-background px-4 py-2 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400 lg:w-48"
                >
                    <option value="">Semua Status</option>
                    <option value="published">Terbit</option>
                    <option value="draft">Draf</option>
                    <option value="revoked">Dicabut</option>
                </select>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm dark:border-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="border-b bg-muted/50 text-muted-foreground dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4 font-semibold">No. Sertifikat</th>
                            <th class="px-6 py-4 font-semibold">Penerima</th>
                            <th class="px-6 py-4 font-semibold">Acara / Kursus</th>
                            <th class="px-6 py-4 font-semibold">Terbit</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Unduhan</th>
                            <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y dark:divide-slate-800">
                        <tr v-for="item in certificates.data" :key="item.id" class="transition hover:bg-muted/50">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-foreground">
                                {{ item.certificate_number }}
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-bold text-foreground">{{ item.recipient_name }}</p>
                                <p class="text-xs text-muted-foreground">{{ item.recipient_email || '-' }}</p>
                            </td>

                            <td class="px-6 py-4 text-muted-foreground">
                                {{ item.event_name || item.course_name || '—' }}
                            </td>

                            <td class="px-6 py-4 text-muted-foreground">
                                {{ formatDate(item.issued_at) }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold" :class="statusStyle(item.status)">
                                    {{ statusLabel(item.status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-muted-foreground">
                                {{ item.download_count }}x
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="`/certificate/${item.id}`" class="rounded-lg p-2 text-muted-foreground transition hover:bg-muted hover:text-foreground" title="Lihat detail">
                                        <Eye class="h-4 w-4" />
                                    </Link>

                                    <a :href="`/certificate/${item.id}/download`" target="_blank" rel="noopener noreferrer" class="rounded-lg p-2 text-muted-foreground transition hover:bg-muted hover:text-foreground" title="Unduh PDF">
                                        <Download class="h-4 w-4" />
                                    </a>

                                    <button v-if="item.status !== 'revoked'" type="button" @click="openRevokeModal(item)" class="rounded-lg p-2 text-muted-foreground transition hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-950/40 dark:hover:text-amber-400" title="Cabut sertifikat">
                                        <Ban class="h-4 w-4" />
                                    </button>

                                    <button type="button" @click="confirmDelete(item.id)" class="rounded-lg p-2 text-muted-foreground transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/40 dark:hover:text-rose-400" title="Hapus sertifikat">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="certificates.data.length === 0">
                            <td colspan="7" class="px-6 py-16 text-center">
                                <p class="text-sm font-semibold text-foreground">Belum ada sertifikat</p>
                                <p class="mt-1 text-xs text-muted-foreground">Coba ubah filter, atau terbitkan sertifikat baru.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="certificates.total > 0" class="flex flex-col gap-4 border-t px-6 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-muted-foreground">
                    Menampilkan {{ certificates.from }}–{{ certificates.to }} dari {{ certificates.total }} sertifikat
                </p>

                <div class="flex flex-wrap items-center gap-1">
                    <Link
                        v-for="(link, index) in certificates.links"
                        :key="index"
                        :href="link.url ?? '#'"
                        preserve-scroll
                        preserve-state
                        :class="[
                            'flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-xs font-semibold transition',
                            link.active ? 'bg-orange-600 text-white dark:bg-orange-500' : 'text-muted-foreground hover:bg-muted',
                            !link.url && 'pointer-events-none opacity-30',
                        ]"
                    >
                        <ChevronLeft v-if="index === 0" class="h-3.5 w-3.5" />
                        <ChevronRight v-else-if="index === certificates.links.length - 1" class="h-3.5 w-3.5" />
                        <span v-else v-html="link.label"></span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Custom Modal: Cabut Sertifikat -->
        <div v-if="showRevokeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-[2rem] border border-slate-100 bg-white p-6 text-center shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                    <AlertTriangle class="h-7 w-7" />
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Cabut Sertifikat Ini?</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Masukkan alasan pencabutan sertifikat <span class="font-semibold text-slate-700 dark:text-slate-200">{{ selectedCertificate?.certificate_number }}</span>:
                </p>

                <div class="mt-4 text-left">
                    <textarea v-model="revokeReason" rows="3" placeholder="Contoh: Kesalahan penulisan nama..." class="w-full rounded-2xl border border-slate-200 bg-background p-3 text-sm text-slate-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none dark:border-slate-700 dark:text-slate-100 dark:focus:border-orange-400"></textarea>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button @click="showRevokeModal = false; selectedCertificate = null; revokeReason = '';" type="button" class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Batal
                    </button>
                    <button @click="submitRevoke" type="button" :disabled="!revokeReason.trim()" class="flex-1 rounded-xl bg-amber-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-amber-600/20 transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-amber-500 dark:hover:bg-amber-600">
                        Ya, Cabut
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Modal: Hapus Sertifikat -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-[2rem] border border-slate-100 bg-white p-6 text-center shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400">
                    <AlertTriangle class="h-7 w-7" />
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Hapus Sertifikat Ini?</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Tindakan ini tidak dapat dibatalkan. Data sertifikat dan file PDF terkait akan dihapus permanen.
                </p>

                <div class="mt-6 flex items-center gap-3">
                    <button @click="cancelDelete" type="button" class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Batal
                    </button>
                    <button @click="deleteCertificate" type="button" class="flex-1 rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-rose-600/20 transition hover:bg-rose-700 dark:bg-rose-500 dark:hover:bg-rose-600">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>