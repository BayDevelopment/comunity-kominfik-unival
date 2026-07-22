<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    BriefcaseBusiness,
    Building2,
    CalendarDays,
    Check,
    Clock,
    Download,
    FileText,
    Mail,
    MapPin,
    MessageSquareText,
    Pencil,
    Phone,
    Trash2,
    User,
    X,
} from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Pendaftaran Kerjasama',
                href: '/pendaftaran-kerjasama',
            },
            {
                title: 'Detail Kerjasama',
                href: '#',
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

const props = defineProps<{
    kerjasama: Kerjasama;
}>();

const processing = ref(false);
const deleting = ref(false);

const statusConfig: Record<
    Kerjasama['status'],
    { label: string; class: string; description: string }
> = {
    pending: {
        label: 'Pending',
        class: 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
        description: 'Pengajuan masih menunggu untuk ditinjau oleh admin.',
    },
    diproses: {
        label: 'Diproses',
        class: 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
        description: 'Pengajuan sedang ditinjau dan diproses oleh admin.',
    },
    disetujui: {
        label: 'Disetujui',
        class: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
        description: 'Pengajuan kerjasama telah disetujui.',
    },
    ditolak: {
        label: 'Ditolak',
        class: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
        description: 'Pengajuan kerjasama tidak dapat disetujui.',
    },
};

const jenisInstansiLabel: Record<Kerjasama['jenis_instansi'], string> = {
    kampus: 'Kampus',
    sma: 'SMA',
    smk: 'SMK',
    perusahaan: 'Perusahaan',
    lainnya: 'Lainnya',
};

const currentStatus = computed(() => statusConfig[props.kerjasama.status]);

function formatTanggal(value: string | null): string {
    if (!value) {
        return '—';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '—';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function initials(nama: string): string {
    return nama
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((item) => item.charAt(0).toUpperCase())
        .join('');
}

function fileUrl(path: string): string {
    if (/^(https?:\/\/|\/)/i.test(path)) {
        return path;
    }

    return `/storage/${path}`;
}

function fileName(path: string): string {
    const cleanPath = path.split('?')[0];

    return cleanPath.split('/').pop() || 'Dokumen';
}

function escapeHtml(value: string): string {
    return value.replace(/[&<>'"]/g, (character) => {
        const entities: Record<string, string> = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#039;',
            '"': '&quot;',
        };

        return entities[character];
    });
}

function prosesKerjasama(): void {
    Swal.fire({
        title: 'Proses Pengajuan?',
        html: `Pengajuan kerjasama dari <b>"${escapeHtml(props.kerjasama.nama_instansi)}"</b> akan ditandai sedang diproses.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        processing.value = true;
        router.patch(
            `/pendaftaran-kerjasama/${props.kerjasama.id}/proses`,
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    processing.value = false;
                },
            },
        );
    });
}

function setujuiKerjasama(): void {
    Swal.fire({
        title: 'Setujui Kerjasama?',
        html: `Pengajuan kerjasama dari <b>"${escapeHtml(props.kerjasama.nama_instansi)}"</b> akan disetujui.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        processing.value = true;
        router.patch(
            `/pendaftaran-kerjasama/${props.kerjasama.id}/terima`,
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    processing.value = false;
                },
            },
        );
    });
}

function tolakKerjasama(): void {
    Swal.fire({
        title: 'Tolak Kerjasama?',
        html: `Pengajuan kerjasama dari <b>"${escapeHtml(props.kerjasama.nama_instansi)}"</b> akan ditolak.`,
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Catatan (opsional)',
        inputPlaceholder: 'Alasan penolakan...',
        inputValue: props.kerjasama.catatan_admin ?? '',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        processing.value = true;
        router.patch(
            `/pendaftaran-kerjasama/${props.kerjasama.id}/tolak`,
            { catatan_admin: result.value || null },
            {
                preserveScroll: true,
                onFinish: () => {
                    processing.value = false;
                },
            },
        );
    });
}

function hapusKerjasama(): void {
    Swal.fire({
        title: 'Hapus Pengajuan?',
        html: `Data kerjasama dari <b>"${escapeHtml(props.kerjasama.nama_instansi)}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusCancel: true,
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        deleting.value = true;
        router.delete(`/pendaftaran-kerjasama/${props.kerjasama.id}`, {
            onSuccess: () => {
                router.visit('/pendaftaran-kerjasama');
            },
            onFinish: () => {
                deleting.value = false;
            },
        });
    });
}
</script>

<template>
    <Head :title="`Detail Kerjasama - ${kerjasama.nama_instansi}`" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex items-start gap-4">
                <Link
                    href="/pendaftaran-kerjasama"
                    class="mt-1 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border bg-background text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    title="Kembali ke daftar kerjasama"
                >
                    <ArrowLeft class="h-5 w-5" />
                </Link>

                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-bold">{{ kerjasama.nama_instansi }}</h1>
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                            :class="currentStatus.class"
                        >
                            {{ currentStatus.label }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Detail pengajuan kerjasama dan informasi instansi mitra.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                <button
                    v-if="kerjasama.status === 'pending'"
                    type="button"
                    :disabled="processing"
                    class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-700 transition-colors hover:bg-blue-100 disabled:pointer-events-none disabled:opacity-50 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-500/20"
                    @click="prosesKerjasama"
                >
                    <Clock class="h-4 w-4" />
                    Proses
                </button>

                <button
                    v-if="kerjasama.status === 'pending' || kerjasama.status === 'diproses'"
                    type="button"
                    :disabled="processing"
                    class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-100 disabled:pointer-events-none disabled:opacity-50 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20"
                    @click="setujuiKerjasama"
                >
                    <Check class="h-4 w-4" />
                    Setujui
                </button>

                <button
                    v-if="kerjasama.status === 'pending' || kerjasama.status === 'diproses'"
                    type="button"
                    :disabled="processing"
                    class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-medium text-rose-700 transition-colors hover:bg-rose-100 disabled:pointer-events-none disabled:opacity-50 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20"
                    @click="tolakKerjasama"
                >
                    <X class="h-4 w-4" />
                    Tolak
                </button>

                <Link
                    :href="`/pendaftaran-kerjasama/${kerjasama.id}/edit`"
                    class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted"
                >
                    <Pencil class="h-4 w-4" />
                    Edit
                </Link>

                <button
                    type="button"
                    :disabled="deleting"
                    class="inline-flex items-center gap-2 rounded-lg border border-rose-200 px-4 py-2.5 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 disabled:pointer-events-none disabled:opacity-50 dark:border-rose-500/20 dark:hover:bg-rose-500/10"
                    @click="hapusKerjasama"
                >
                    <Trash2 class="h-4 w-4" />
                    Hapus
                </button>
            </div>
        </div>

        <!-- Status Summary -->
        <div class="rounded-xl border bg-background p-5 shadow-sm">
            <div class="flex items-start gap-4">
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                    :class="{
                        'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400':
                            kerjasama.status === 'pending',
                        'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400':
                            kerjasama.status === 'diproses',
                        'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400':
                            kerjasama.status === 'disetujui',
                        'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400':
                            kerjasama.status === 'ditolak',
                    }"
                >
                    <Clock v-if="kerjasama.status === 'pending' || kerjasama.status === 'diproses'" class="h-5 w-5" />
                    <Check v-else-if="kerjasama.status === 'disetujui'" class="h-5 w-5" />
                    <X v-else class="h-5 w-5" />
                </div>

                <div>
                    <p class="font-semibold">Status: {{ currentStatus.label }}</p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ currentStatus.description }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <!-- Informasi Instansi -->
                <section class="overflow-hidden rounded-xl border bg-background shadow-sm">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                            <Building2 class="h-4 w-4 text-blue-500" />
                        </div>
                        <div>
                            <h2 class="font-semibold">Informasi Instansi</h2>
                            <p class="text-xs text-muted-foreground">Data instansi yang mengajukan kerjasama.</p>
                        </div>
                    </div>

                    <div class="grid gap-6 p-6 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-medium tracking-wide text-muted-foreground uppercase">Nama Instansi</p>
                            <p class="mt-1.5 font-medium">{{ kerjasama.nama_instansi }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium tracking-wide text-muted-foreground uppercase">Jenis Instansi</p>
                            <p class="mt-1.5">{{ jenisInstansiLabel[kerjasama.jenis_instansi] }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium tracking-wide text-muted-foreground uppercase">Alamat</p>
                            <div class="mt-1.5 flex items-start gap-2">
                                <MapPin class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />
                                <p class="whitespace-pre-line">{{ kerjasama.alamat ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Informasi PIC -->
                <section class="overflow-hidden rounded-xl border bg-background shadow-sm">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-500/10">
                            <User class="h-4 w-4 text-violet-500" />
                        </div>
                        <div>
                            <h2 class="font-semibold">Informasi PIC</h2>
                            <p class="text-xs text-muted-foreground">Kontak penanggung jawab dari instansi mitra.</p>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="mb-6 flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-muted text-sm font-bold text-muted-foreground">
                                {{ initials(kerjasama.nama_pic) }}
                            </div>
                            <div>
                                <p class="font-semibold">{{ kerjasama.nama_pic }}</p>
                                <p class="text-sm text-muted-foreground">{{ kerjasama.jabatan_pic ?? 'Jabatan tidak dicantumkan' }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <a
                                :href="`mailto:${kerjasama.email_pic}`"
                                class="flex items-center gap-3 rounded-lg border p-4 transition-colors hover:bg-muted/50"
                            >
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                    <Mail class="h-4 w-4 text-blue-500" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-muted-foreground">Email</p>
                                    <p class="truncate text-sm font-medium">{{ kerjasama.email_pic }}</p>
                                </div>
                            </a>

                            <a
                                :href="`tel:${kerjasama.no_hp_pic}`"
                                class="flex items-center gap-3 rounded-lg border p-4 transition-colors hover:bg-muted/50"
                            >
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-500/10">
                                    <Phone class="h-4 w-4 text-emerald-500" />
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Nomor HP</p>
                                    <p class="text-sm font-medium">{{ kerjasama.no_hp_pic }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Detail Kerjasama -->
                <section class="overflow-hidden rounded-xl border bg-background shadow-sm">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-500/10">
                            <BriefcaseBusiness class="h-4 w-4 text-emerald-500" />
                        </div>
                        <div>
                            <h2 class="font-semibold">Detail Kerjasama</h2>
                            <p class="text-xs text-muted-foreground">Ruang lingkup dan penjelasan pengajuan.</p>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">
                        <div>
                            <p class="text-xs font-medium tracking-wide text-muted-foreground uppercase">Jenis Kerjasama</p>
                            <p class="mt-1.5">{{ kerjasama.jenis_kerjasama ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium tracking-wide text-muted-foreground uppercase">Deskripsi Kerjasama</p>
                            <p class="mt-1.5 whitespace-pre-line leading-7 text-muted-foreground">
                                {{ kerjasama.deskripsi_kerjasama ?? 'Tidak ada deskripsi kerjasama.' }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Catatan Admin -->
                <section
                    v-if="kerjasama.catatan_admin"
                    class="overflow-hidden rounded-xl border bg-background shadow-sm"
                >
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-500/10">
                            <MessageSquareText class="h-4 w-4 text-amber-500" />
                        </div>
                        <div>
                            <h2 class="font-semibold">Catatan Admin</h2>
                            <p class="text-xs text-muted-foreground">Catatan tindak lanjut atau alasan keputusan.</p>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="whitespace-pre-line leading-7 text-muted-foreground">
                            {{ kerjasama.catatan_admin }}
                        </p>
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <!-- Timeline -->
                <section class="overflow-hidden rounded-xl border bg-background shadow-sm">
                    <div class="flex items-center gap-3 border-b px-5 py-4">
                        <CalendarDays class="h-5 w-5 text-muted-foreground" />
                        <h2 class="font-semibold">Informasi Waktu</h2>
                    </div>

                    <div class="space-y-5 p-5">
                        <div>
                            <p class="text-xs font-medium tracking-wide text-muted-foreground uppercase">Tanggal Pengajuan</p>
                            <p class="mt-1.5 text-sm font-medium">{{ formatTanggal(kerjasama.tanggal_pengajuan) }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium tracking-wide text-muted-foreground uppercase">Tanggal Diproses</p>
                            <p class="mt-1.5 text-sm font-medium">{{ formatTanggal(kerjasama.tanggal_diproses) }}</p>
                        </div>
                    </div>
                </section>

                <!-- Dokumen -->
                <section class="overflow-hidden rounded-xl border bg-background shadow-sm">
                    <div class="flex items-center gap-3 border-b px-5 py-4">
                        <FileText class="h-5 w-5 text-muted-foreground" />
                        <div>
                            <h2 class="font-semibold">Dokumen</h2>
                            <p class="text-xs text-muted-foreground">Berkas pendukung pengajuan.</p>
                        </div>
                    </div>

                    <div class="space-y-3 p-5">
                        <a
                            v-if="kerjasama.file_proposal"
                            :href="fileUrl(kerjasama.file_proposal)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-3 rounded-lg border p-3 transition-colors hover:bg-muted/50"
                        >
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 dark:bg-rose-500/10">
                                <FileText class="h-5 w-5 text-rose-500" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-muted-foreground">Proposal</p>
                                <p class="truncate text-sm font-medium">{{ fileName(kerjasama.file_proposal) }}</p>
                            </div>
                            <Download class="h-4 w-4 shrink-0 text-muted-foreground" />
                        </a>

                        <div
                            v-else
                            class="flex items-center gap-3 rounded-lg border border-dashed p-3 text-muted-foreground"
                        >
                            <FileText class="h-5 w-5" />
                            <p class="text-sm">Proposal belum diunggah.</p>
                        </div>

                        <a
                            v-if="kerjasama.file_mou"
                            :href="fileUrl(kerjasama.file_mou)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-3 rounded-lg border p-3 transition-colors hover:bg-muted/50"
                        >
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                <FileText class="h-5 w-5 text-blue-500" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-muted-foreground">MoU</p>
                                <p class="truncate text-sm font-medium">{{ fileName(kerjasama.file_mou) }}</p>
                            </div>
                            <Download class="h-4 w-4 shrink-0 text-muted-foreground" />
                        </a>

                        <div
                            v-else
                            class="flex items-center gap-3 rounded-lg border border-dashed p-3 text-muted-foreground"
                        >
                            <FileText class="h-5 w-5" />
                            <p class="text-sm">MoU belum diunggah.</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
