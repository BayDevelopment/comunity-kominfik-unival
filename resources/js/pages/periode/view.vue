<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Pencil,
    Trash2,
    Users,
    Handshake,
    CalendarRange,
    Clock,
    ToggleLeft,
    ToggleRight,
    Loader2,
} from '@lucide/vue';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';
import { dashboard } from '@/routes';

// ---------------------------------------------------------------------------
// Types
// ---------------------------------------------------------------------------

interface PeriodePendaftaran {
    id: number;
    jenis: 'anggota' | 'kerjasama';
    nama_periode: string | null;
    tanggal_mulai: string | null;
    tanggal_selesai: string | null;
    status: 'active' | 'nonactive';
    created_at: string;
}

// ---------------------------------------------------------------------------
// Props & layout
// ---------------------------------------------------------------------------

const props = defineProps<{
    periode: PeriodePendaftaran;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Periode Pendaftaran', href: '/periode-pendaftaran' },
            { title: 'Detail Periode', href: '#' },
        ],
    },
});

// ---------------------------------------------------------------------------
// Konfigurasi tampilan
// ---------------------------------------------------------------------------

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

// ---------------------------------------------------------------------------
// Display helpers
// ---------------------------------------------------------------------------

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

const namaTampilan = computed(() => props.periode.nama_periode || jenisConfig[props.periode.jenis].label);

const tanggalDibuat = computed(() =>
    new Date(props.periode.created_at).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }),
);

// Durasi periode dalam hari, kalau kedua tanggal terisi
const durasiHari = computed(() => {
    if (!props.periode.tanggal_mulai || !props.periode.tanggal_selesai) {
        return null;
    }

    const mulai = new Date(props.periode.tanggal_mulai);
    const selesai = new Date(props.periode.tanggal_selesai);
    const diff = Math.round((selesai.getTime() - mulai.getTime()) / (1000 * 60 * 60 * 24));

    return diff >= 0 ? diff + 1 : null;
});

// ---------------------------------------------------------------------------
// Actions
// ---------------------------------------------------------------------------

const togglingStatus = ref(false);
const deleting = ref(false);

function toggleStatus() {
    const akan = props.periode.status === 'active' ? 'ditutup' : 'dibuka';

    Swal.fire({
        title: `${props.periode.status === 'active' ? 'Tutup' : 'Buka'} Pendaftaran?`,
        html: `Pendaftaran <b>"${namaTampilan.value}"</b> akan ${akan}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Ya, ${props.periode.status === 'active' ? 'Tutup' : 'Buka'}`,
        cancelButtonText: 'Batal',
        confirmButtonColor: props.periode.status === 'active' ? '#e11d48' : '#059669',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            togglingStatus.value = true;
            router.patch(
                `/periode-pendaftaran/${props.periode.id}/toggle-status`,
                {},
                {
                    preserveScroll: true,
                    onFinish: () => {
                        togglingStatus.value = false;
                    },
                },
            );
        }
    });
}

function hapusPeriode() {
    Swal.fire({
        title: 'Hapus Periode?',
        html: `Periode <b>"${namaTampilan.value}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
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
            deleting.value = true;
            router.delete(`/periode-pendaftaran/${props.periode.id}`, {
                onFinish: () => {
                    deleting.value = false;
                },
            });
        }
    });
}
</script>

<template>

    <Head :title="`Detail - ${namaTampilan}`" />

    <div class="mx-auto max-w-3xl space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <Link href="/periode-pendaftaran"
                    class="rounded-lg border p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold">{{ namaTampilan }}</h1>
                    <p class="text-sm text-muted-foreground">
                        Detail periode pendaftaran.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Link :href="`/periode-pendaftaran/${periode.id}/edit`"
                    class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    <Pencil class="h-4 w-4" />
                    Edit
                </Link>
                <button type="button" :disabled="deleting"
                    class="inline-flex items-center gap-2 rounded-lg border border-rose-200 px-4 py-2.5 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 disabled:pointer-events-none disabled:opacity-50 dark:border-rose-900 dark:hover:bg-rose-500/10"
                    @click="hapusPeriode">
                    <Loader2 v-if="deleting" class="h-4 w-4 animate-spin" />
                    <Trash2 v-else class="h-4 w-4" />
                    Hapus
                </button>
            </div>
        </div>

        <!-- Status Card -->
        <div class="flex items-center justify-between rounded-xl border bg-background p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-medium ring-1 ring-inset"
                    :class="jenisConfig[periode.jenis].class">
                    <component :is="jenisConfig[periode.jenis].icon" class="h-4 w-4" />
                    {{ jenisConfig[periode.jenis].label }}
                </span>
                <span class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium ring-1 ring-inset"
                    :class="statusConfig[periode.status].class">
                    {{ statusConfig[periode.status].label }}
                </span>
            </div>

            <button type="button" :disabled="togglingStatus"
                class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-50"
                @click="toggleStatus">
                <Loader2 v-if="togglingStatus" class="h-4 w-4 animate-spin" />
                <template v-else>
                    <ToggleRight v-if="periode.status === 'active'" class="h-4 w-4 text-emerald-600" />
                    <ToggleLeft v-else class="h-4 w-4" />
                </template>
                {{ periode.status === 'active' ? 'Tutup Pendaftaran' : 'Buka Pendaftaran' }}
            </button>
        </div>

        <!-- Detail Card -->
        <div class="rounded-xl border bg-background shadow-sm">
            <div class="border-b px-6 py-4">
                <h2 class="font-semibold">Informasi Periode</h2>
            </div>

            <dl class="divide-y">
                <div class="grid grid-cols-1 gap-1 px-6 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm text-muted-foreground">Nama Periode</dt>
                    <dd class="text-sm font-medium sm:col-span-2">
                        {{ periode.nama_periode || '—' }}
                    </dd>
                </div>

                <div class="grid grid-cols-1 gap-1 px-6 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm text-muted-foreground">Tanggal Mulai</dt>
                    <dd class="flex items-center gap-2 text-sm font-medium sm:col-span-2">
                        <CalendarRange class="h-3.5 w-3.5 text-muted-foreground" />
                        {{ formatDate(periode.tanggal_mulai) }}
                    </dd>
                </div>

                <div class="grid grid-cols-1 gap-1 px-6 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm text-muted-foreground">Tanggal Selesai</dt>
                    <dd class="flex items-center gap-2 text-sm font-medium sm:col-span-2">
                        <CalendarRange class="h-3.5 w-3.5 text-muted-foreground" />
                        {{ formatDate(periode.tanggal_selesai) }}
                    </dd>
                </div>

                <div v-if="durasiHari !== null" class="grid grid-cols-1 gap-1 px-6 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm text-muted-foreground">Durasi</dt>
                    <dd class="text-sm font-medium sm:col-span-2">
                        {{ durasiHari }} hari
                    </dd>
                </div>

                <div class="grid grid-cols-1 gap-1 px-6 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm text-muted-foreground">Dibuat Pada</dt>
                    <dd class="flex items-center gap-2 text-sm font-medium sm:col-span-2">
                        <Clock class="h-3.5 w-3.5 text-muted-foreground" />
                        {{ tanggalDibuat }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</template>
