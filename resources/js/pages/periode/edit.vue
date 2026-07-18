<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Save,
    Users,
    Handshake,
    CalendarRange,
    Type,
    Loader2,
} from '@lucide/vue';
import { computed } from 'vue';
import { dashboard } from '@/routes';

interface PeriodePendaftaran {
    id: number;
    jenis: 'anggota' | 'kerjasama';
    nama_periode: string | null;
    tanggal_mulai: string | null;
    tanggal_selesai: string | null;
    status: 'active' | 'nonactive';
    created_at: string;
}

interface FormPeriode {
    jenis: 'anggota' | 'kerjasama' | '';
    nama_periode: string;
    tanggal_mulai: string;
    tanggal_selesai: string;
    status: 'active' | 'nonactive';
}

const props = defineProps<{
    periode: PeriodePendaftaran;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Periode Pendaftaran', href: '/periode-pendaftaran' },
            { title: 'Edit Periode', href: '#' }, // statis, tidak boleh akses props di sini
        ],
    },
});


function toDateInput(date: string | null): string {
    return date ? date.slice(0, 10) : '';
}

const form = useForm<FormPeriode>({
    jenis: props.periode.jenis,
    nama_periode: props.periode.nama_periode ?? '',
    tanggal_mulai: toDateInput(props.periode.tanggal_mulai),
    tanggal_selesai: toDateInput(props.periode.tanggal_selesai),
    status: props.periode.status,
});

const jenisOptions = [
    { value: 'anggota', label: 'Anggota', icon: Users },
    { value: 'kerjasama', label: 'Kerjasama', icon: Handshake },
] as const;

function submit() {
    form.put(`/periode-pendaftaran/${props.periode.id}`, {
        preserveScroll: true,
    });
}


const namaTampilan = computed(() => props.periode.nama_periode || `#${props.periode.id}`);

const tanggalDibuat = computed(() =>
    new Date(props.periode.created_at).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }),
);
</script>

<template>

    <Head title="Edit Periode Pendaftaran" />

    <div class="mx-auto max-w-2xl space-y-6 p-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <Link href="/periode-pendaftaran"
                class="rounded-lg border p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold">Edit Periode Pendaftaran</h1>
                <p class="text-sm text-muted-foreground">
                    Perbarui detail periode
                    <span class="font-medium text-foreground">{{ namaTampilan }}</span>.
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <form @submit.prevent="submit" class="space-y-6 rounded-xl border bg-background p-6 shadow-sm">
            <!-- Jenis -->
            <div class="space-y-2">
                <label class="text-sm font-medium">
                    Jenis Periode <span class="text-rose-600">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <button v-for="opt in jenisOptions" :key="opt.value" type="button"
                        class="flex items-center gap-2 rounded-lg border px-4 py-3 text-sm font-medium transition-colors"
                        :class="form.jenis === opt.value
                            ? 'border-primary bg-primary/5 text-primary ring-1 ring-primary'
                            : 'hover:bg-muted'"
                        @click="form.jenis = opt.value">
                        <component :is="opt.icon" class="h-4 w-4" />
                        {{ opt.label }}
                    </button>
                </div>
                <p v-if="form.errors.jenis" class="text-xs text-rose-600">
                    {{ form.errors.jenis }}
                </p>
            </div>

            <!-- Nama Periode -->
            <div class="space-y-2">
                <label for="nama_periode" class="text-sm font-medium">
                    Nama Periode
                </label>
                <div class="relative">
                    <Type
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <input id="nama_periode" v-model="form.nama_periode" type="text" maxlength="100"
                        placeholder="Contoh: Pendaftaran Anggota Gelombang 1"
                        class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm outline-none transition-shadow placeholder:text-muted-foreground focus:ring-2 focus:ring-ring" />
                </div>
                <p class="text-xs text-muted-foreground">
                    Opsional. Maksimal 100 karakter.
                </p>
                <p v-if="form.errors.nama_periode" class="text-xs text-rose-600">
                    {{ form.errors.nama_periode }}
                </p>
            </div>

            <!-- Tanggal Mulai & Selesai -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="tanggal_mulai" class="text-sm font-medium">
                        Tanggal Mulai
                    </label>
                    <div class="relative">
                        <CalendarRange
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="tanggal_mulai" v-model="form.tanggal_mulai" type="date"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring" />
                    </div>
                    <p v-if="form.errors.tanggal_mulai" class="text-xs text-rose-600">
                        {{ form.errors.tanggal_mulai }}
                    </p>
                </div>

                <div class="space-y-2">
                    <label for="tanggal_selesai" class="text-sm font-medium">
                        Tanggal Selesai
                    </label>
                    <div class="relative">
                        <CalendarRange
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="tanggal_selesai" v-model="form.tanggal_selesai" type="date"
                            :min="form.tanggal_mulai || undefined"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring" />
                    </div>
                    <p v-if="form.errors.tanggal_selesai" class="text-xs text-rose-600">
                        {{ form.errors.tanggal_selesai }}
                    </p>
                </div>
            </div>

            <!-- Status -->
            <div class="space-y-2">
                <label class="text-sm font-medium">Status</label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button"
                        class="rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors"
                        :class="form.status === 'active'
                            ? 'border-emerald-600 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'
                            : 'hover:bg-muted'"
                        @click="form.status = 'active'">
                        Dibuka
                    </button>
                    <button type="button"
                        class="rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors"
                        :class="form.status === 'nonactive'
                            ? 'border-rose-600 bg-rose-50 text-rose-700 ring-1 ring-rose-600 dark:bg-rose-500/10 dark:text-rose-400'
                            : 'hover:bg-muted'"
                        @click="form.status = 'nonactive'">
                        Ditutup
                    </button>
                </div>
                <p v-if="form.errors.status" class="text-xs text-rose-600">
                    {{ form.errors.status }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between gap-3 border-t pt-4">
                <p class="text-xs text-muted-foreground">
                    Dibuat pada {{ tanggalDibuat }}
                </p>
                <div class="flex items-center gap-3">
                    <Link href="/periode-pendaftaran"
                        class="rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-60">
                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <Save v-else class="h-4 w-4" />
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
