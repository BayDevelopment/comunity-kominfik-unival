<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Pencil,
    Trash2,
    Check,
    X,
    Mail,
    Phone,
    MapPin,
    GraduationCap,
    FileText,
    Download,
    Calendar,
    UserCheck,
} from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { dashboard } from '@/routes';

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
    diproses_oleh: {
        id: number;
        name: string;
    } | null;
}

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
            {
                title: 'Detail Pendaftar',
                href: '/pendaftaran-anggota', // statis, tanpa id
            },
        ],
    },
});

const props = defineProps<{
    pendaftaran: PendaftaranAnggota;
}>();

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

function initials(nama: string): string {
    return nama
        .split(' ')
        .slice(0, 2)
        .map((n) => n.charAt(0).toUpperCase())
        .join('');
}

function formatTanggal(value: string | null): string {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

const processing = ref(false);

function terimaPendaftaran() {
    Swal.fire({
        title: 'Terima Pendaftar?',
        html: `Pendaftaran <b>"${props.pendaftaran.nama}"</b> akan diterima sebagai anggota.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Terima',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            processing.value = true;
            router.patch(
                `/pendaftaran-anggota/${props.pendaftaran.id}/terima`,
                {},
                {
                    preserveScroll: true,
                    onFinish: () => {
                        processing.value = false;
                    },
                },
            );
        }
    });
}

function tolakPendaftaran() {
    Swal.fire({
        title: 'Tolak Pendaftar?',
        html: `Pendaftaran <b>"${props.pendaftaran.nama}"</b> akan ditolak.`,
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
            processing.value = true;
            router.patch(
                `/pendaftaran-anggota/${props.pendaftaran.id}/tolak`,
                { catatan_admin: result.value || null },
                {
                    preserveScroll: true,
                    onFinish: () => {
                        processing.value = false;
                    },
                },
            );
        }
    });
}

const deleting = ref(false);

function hapusPendaftaran() {
    Swal.fire({
        title: 'Hapus Pendaftaran?',
        html: `Data pendaftaran <b>"${props.pendaftaran.nama}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
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
            router.delete(`/pendaftaran-anggota/${props.pendaftaran.id}`, {
                onFinish: () => {
                    deleting.value = false;
                },
            });
        }
    });
}
</script>

<template>

    <Head :title="`Detail - ${pendaftaran.nama}`" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <Link href="/pendaftaran-anggota"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border transition-colors hover:bg-muted">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold">Detail Pendaftar</h1>
                    <p class="text-sm text-muted-foreground">
                        Informasi lengkap data pendaftaran anggota.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button v-if="pendaftaran.status === 'pending'" type="button" :disabled="processing"
                    class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 shadow-sm transition-colors hover:bg-emerald-100 disabled:pointer-events-none disabled:opacity-60 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400"
                    @click="terimaPendaftaran">
                    <Check class="h-4 w-4" />
                    Terima
                </button>
                <button v-if="pendaftaran.status === 'pending'" type="button" :disabled="processing"
                    class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-medium text-rose-700 shadow-sm transition-colors hover:bg-rose-100 disabled:pointer-events-none disabled:opacity-60 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400"
                    @click="tolakPendaftaran">
                    <X class="h-4 w-4" />
                    Tolak
                </button>
                <Link :href="`/pendaftaran-anggota/${pendaftaran.id}/edit`"
                    class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    <Pencil class="h-4 w-4" />
                    Edit
                </Link>
                <button type="button" :disabled="deleting"
                    class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 disabled:pointer-events-none disabled:opacity-60 dark:hover:bg-rose-500/10"
                    @click="hapusPendaftaran">
                    <Trash2 class="h-4 w-4" />
                    Hapus
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Kolom Kiri: Profil -->
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-xl border bg-background p-6 text-center shadow-sm">
                    <div v-if="pendaftaran.foto" class="mx-auto h-24 w-24 overflow-hidden rounded-full bg-muted">
                        <img :src="`/storage/${pendaftaran.foto}`" :alt="pendaftaran.nama"
                            class="h-full w-full object-cover" />
                    </div>
                    <div v-else
                        class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-blue-50 text-2xl font-semibold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                        {{ initials(pendaftaran.nama) }}
                    </div>

                    <h2 class="mt-4 text-lg font-semibold">{{ pendaftaran.nama }}</h2>
                    <p class="text-sm text-muted-foreground">{{ pendaftaran.nim_nis }}</p>

                    <span
                        class="mt-3 inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                        :class="statusConfig[pendaftaran.status].class">
                        {{ statusConfig[pendaftaran.status].label }}
                    </span>

                    <div class="mt-5 space-y-3 border-t pt-5 text-left text-sm">
                        <div class="flex items-center gap-2 text-muted-foreground">
                            <Mail class="h-4 w-4 shrink-0" />
                            <span class="truncate">{{ pendaftaran.email }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-muted-foreground">
                            <Phone class="h-4 w-4 shrink-0" />
                            <span>{{ pendaftaran.no_telepon }}</span>
                        </div>
                        <div v-if="pendaftaran.alamat" class="flex items-start gap-2 text-muted-foreground">
                            <MapPin class="mt-0.5 h-4 w-4 shrink-0" />
                            <span>{{ pendaftaran.alamat }}</span>
                        </div>
                    </div>
                </div>

                <!-- File CV -->
                <div class="rounded-xl border bg-background p-6 shadow-sm">
                    <h3 class="mb-3 text-sm font-semibold">Berkas CV</h3>
                    <a v-if="pendaftaran.file_cv" :href="`/storage/${pendaftaran.file_cv}`" target="_blank"
                        class="flex items-center gap-3 rounded-lg border bg-muted/30 p-3 transition-colors hover:bg-muted/50">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                            <FileText class="h-5 w-5 text-blue-500" />
                        </div>
                        <span class="flex-1 text-sm font-medium">Lihat / Unduh CV</span>
                        <Download class="h-4 w-4 text-muted-foreground" />
                    </a>
                    <p v-else class="text-sm text-muted-foreground">
                        Pendaftar belum mengunggah CV.
                    </p>
                </div>
            </div>

            <!-- Kolom Kanan: Detail -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Data Akademik -->
                <div class="rounded-xl border bg-background p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-2">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                            <GraduationCap class="h-4 w-4 text-blue-500" />
                        </div>
                        <h3 class="font-semibold">Data Akademik</h3>
                    </div>

                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs text-muted-foreground">Asal Instansi</dt>
                            <dd class="mt-1 text-sm font-medium">{{ pendaftaran.asal_instansi }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted-foreground">Jenjang</dt>
                            <dd class="mt-1 text-sm font-medium">{{ jenjangLabel[pendaftaran.jenjang] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted-foreground">Jurusan / Prodi</dt>
                            <dd class="mt-1 text-sm font-medium">{{ pendaftaran.jurusan_prodi ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted-foreground">Angkatan</dt>
                            <dd class="mt-1 text-sm font-medium">{{ pendaftaran.angkatan ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Alasan Bergabung -->
                <div class="rounded-xl border bg-background p-6 shadow-sm">
                    <h3 class="mb-3 font-semibold">Alasan Bergabung</h3>
                    <p class="text-sm leading-relaxed whitespace-pre-line text-muted-foreground">
                        {{ pendaftaran.alasan_bergabung || 'Tidak ada catatan dari pendaftar.' }}
                    </p>
                </div>

                <!-- Riwayat Verifikasi -->
                <div class="rounded-xl border bg-background p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-2">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-500/10">
                            <UserCheck class="h-4 w-4 text-amber-500" />
                        </div>
                        <h3 class="font-semibold">Riwayat Verifikasi</h3>
                    </div>

                    <div v-if="pendaftaran.status === 'pending'"
                        class="rounded-lg border border-dashed p-4 text-center text-sm text-muted-foreground">
                        Pendaftaran ini belum diproses.
                    </div>

                    <dl v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs text-muted-foreground">Diproses Oleh</dt>
                            <dd class="mt-1 text-sm font-medium">
                                {{ pendaftaran.diproses_oleh?.name ?? '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted-foreground">Tanggal Diproses</dt>
                            <dd class="mt-1 flex items-center gap-1.5 text-sm font-medium">
                                <Calendar class="h-3.5 w-3.5 text-muted-foreground" />
                                {{ formatTanggal(pendaftaran.tanggal_diproses) }}
                            </dd>
                        </div>
                        <div v-if="pendaftaran.catatan_admin" class="sm:col-span-2">
                            <dt class="text-xs text-muted-foreground">Catatan Admin</dt>
                            <dd class="mt-1 rounded-lg bg-muted/40 p-3 text-sm">
                                {{ pendaftaran.catatan_admin }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Metadata -->
                <div class="rounded-xl border bg-background p-6 shadow-sm">
                    <h3 class="mb-3 text-sm font-semibold text-muted-foreground">Informasi Lainnya</h3>
                    <p class="text-xs text-muted-foreground">
                        Didaftarkan pada {{ formatTanggal(pendaftaran.created_at) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
