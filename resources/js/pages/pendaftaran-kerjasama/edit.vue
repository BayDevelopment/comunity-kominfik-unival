<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Building2,
    ArrowLeft,
    Loader2,
    Upload,
    FileText,
    X,
    ShieldCheck,
} from 'lucide-vue-next';
import { ref } from 'vue';
import { dashboard } from '@/routes';

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
}

const props = defineProps<{
    kerjasama: Kerjasama;
}>();

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
                title: 'Edit Kerjasama',
                href: '/pendaftaran-kerjasama', // statis, hindari referensi props di defineOptions
            },
        ],
    },
});

const form = useForm({
    _method: 'put',
    jenis_instansi: props.kerjasama.jenis_instansi,
    nama_instansi: props.kerjasama.nama_instansi,
    alamat: props.kerjasama.alamat ?? '',
    nama_pic: props.kerjasama.nama_pic,
    jabatan_pic: props.kerjasama.jabatan_pic ?? '',
    email_pic: props.kerjasama.email_pic,
    no_hp_pic: props.kerjasama.no_hp_pic,
    jenis_kerjasama: props.kerjasama.jenis_kerjasama ?? '',
    deskripsi_kerjasama: props.kerjasama.deskripsi_kerjasama ?? '',
    status: props.kerjasama.status,
    catatan_admin: props.kerjasama.catatan_admin ?? '',
    file_proposal: null as File | null,
    file_mou: null as File | null,
});

const proposalInput = ref<HTMLInputElement | null>(null);
const mouInput = ref<HTMLInputElement | null>(null);

function onProposalChange(e: Event) {
    form.file_proposal = (e.target as HTMLInputElement).files?.[0] ?? null;
}

function onMouChange(e: Event) {
    form.file_mou = (e.target as HTMLInputElement).files?.[0] ?? null;
}

function removeProposal() {
    form.file_proposal = null;

    if (proposalInput.value) {
        proposalInput.value.value = '';
    }
}

function removeMou() {
    form.file_mou = null;

    if (mouInput.value) {
        mouInput.value.value = '';
    }
}

const statusOptions: { value: Kerjasama['status']; label: string }[] = [
    { value: 'pending', label: 'Pending' },
    { value: 'diproses', label: 'Diproses' },
    { value: 'disetujui', label: 'Disetujui' },
    { value: 'ditolak', label: 'Ditolak' },
];

function submit() {
    // POST + _method: 'put' — WAJIB untuk form dengan file upload,
    // karena PUT asli tidak bisa membawa multipart/form-data dengan benar di browser.
    form.post(`/pendaftaran-kerjasama/${props.kerjasama.id}`, {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>

    <Head title="Edit Kerjasama" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <Link href="/pendaftaran-kerjasama"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border transition-colors hover:bg-muted">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold">Edit Kerjasama</h1>
                    <p class="text-sm text-muted-foreground">
                        Perbarui data pengajuan kerjasama {{ kerjasama.nama_instansi }}.
                    </p>
                </div>
            </div>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <!-- Data Instansi -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                        <Building2 class="h-4 w-4 text-blue-500" />
                    </div>
                    <div>
                        <h2 class="font-semibold">Data Instansi</h2>
                        <p class="text-xs text-muted-foreground">
                            Informasi instansi yang mengajukan kerjasama
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Jenis Instansi <span
                                class="text-rose-500">*</span></label>
                        <select v-model="form.jenis_instansi"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.jenis_instansi }">
                            <option value="kampus">Kampus</option>
                            <option value="sma">SMA</option>
                            <option value="smk">SMK</option>
                            <option value="perusahaan">Perusahaan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        <p v-if="form.errors.jenis_instansi" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.jenis_instansi }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Nama Instansi <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.nama_instansi" type="text" placeholder="Nama instansi / perusahaan"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.nama_instansi }" />
                        <p v-if="form.errors.nama_instansi" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.nama_instansi }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Alamat</label>
                        <textarea v-model="form.alamat" rows="3" placeholder="Alamat lengkap instansi"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.alamat }"></textarea>
                        <p v-if="form.errors.alamat" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.alamat }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Data PIC -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-semibold">Data Penanggung Jawab (PIC)</h2>
                    <p class="text-xs text-muted-foreground">
                        Kontak yang bisa dihubungi terkait pengajuan ini
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Nama PIC <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.nama_pic" type="text" placeholder="Nama lengkap"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.nama_pic }" />
                        <p v-if="form.errors.nama_pic" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.nama_pic }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Jabatan PIC</label>
                        <input v-model="form.jabatan_pic" type="text" placeholder="Contoh: Kepala Humas"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.jabatan_pic }" />
                        <p v-if="form.errors.jabatan_pic" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.jabatan_pic }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Email PIC <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.email_pic" type="email" placeholder="nama@instansi.com"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.email_pic }" />
                        <p v-if="form.errors.email_pic" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.email_pic }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">No. HP PIC <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.no_hp_pic" type="text" placeholder="08xxxxxxxxxx"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.no_hp_pic }" />
                        <p v-if="form.errors.no_hp_pic" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.no_hp_pic }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detail Kerjasama -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-semibold">Detail Kerjasama</h2>
                    <p class="text-xs text-muted-foreground">
                        Jenis dan deskripsi kerjasama yang diajukan
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Jenis Kerjasama</label>
                        <input v-model="form.jenis_kerjasama" type="text"
                            placeholder="Contoh: Magang, Penelitian, Sponsorship"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.jenis_kerjasama }" />
                        <p v-if="form.errors.jenis_kerjasama" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.jenis_kerjasama }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Deskripsi Kerjasama</label>
                        <textarea v-model="form.deskripsi_kerjasama" rows="4"
                            placeholder="Jelaskan detail rencana kerjasama..."
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.deskripsi_kerjasama }"></textarea>
                        <p v-if="form.errors.deskripsi_kerjasama" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.deskripsi_kerjasama }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Berkas -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-semibold">Berkas Pendukung</h2>
                    <p class="text-xs text-muted-foreground">
                        Kosongkan jika tidak ingin mengganti berkas
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <!-- Proposal -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">File Proposal</label>
                        <div v-if="form.file_proposal"
                            class="relative flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                <FileText class="h-5 w-5 text-blue-500" />
                            </div>
                            <div class="flex-1 truncate text-sm text-muted-foreground">
                                {{ form.file_proposal.name }}
                            </div>
                            <button type="button"
                                class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10"
                                @click="removeProposal">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <div v-else-if="kerjasama.file_proposal"
                            class="relative flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                <FileText class="h-5 w-5 text-blue-500" />
                            </div>
                            <a :href="`/storage/${kerjasama.file_proposal}`" target="_blank"
                                class="flex-1 truncate text-sm text-primary underline-offset-2 hover:underline">
                                Lihat proposal saat ini
                            </a>
                            <label
                                class="cursor-pointer rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-muted">
                                <Upload class="h-4 w-4" />
                                <input ref="proposalInput" type="file" accept=".pdf,.doc,.docx" class="hidden"
                                    @change="onProposalChange" />
                            </label>
                        </div>
                        <label v-else
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed px-4 py-6 text-center transition-colors hover:bg-muted/40">
                            <Upload class="h-5 w-5 text-muted-foreground" />
                            <span class="text-sm text-muted-foreground">Klik untuk unggah proposal</span>
                            <input ref="proposalInput" type="file" accept=".pdf,.doc,.docx" class="hidden"
                                @change="onProposalChange" />
                        </label>
                        <p v-if="form.errors.file_proposal" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.file_proposal }}
                        </p>
                    </div>

                    <!-- MoU -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">File Draft MoU</label>
                        <div v-if="form.file_mou"
                            class="relative flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                <FileText class="h-5 w-5 text-blue-500" />
                            </div>
                            <div class="flex-1 truncate text-sm text-muted-foreground">
                                {{ form.file_mou.name }}
                            </div>
                            <button type="button"
                                class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10"
                                @click="removeMou">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <div v-else-if="kerjasama.file_mou"
                            class="relative flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                <FileText class="h-5 w-5 text-blue-500" />
                            </div>
                            <a :href="`/storage/${kerjasama.file_mou}`" target="_blank"
                                class="flex-1 truncate text-sm text-primary underline-offset-2 hover:underline">
                                Lihat MoU saat ini
                            </a>
                            <label
                                class="cursor-pointer rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-muted">
                                <Upload class="h-4 w-4" />
                                <input ref="mouInput" type="file" accept=".pdf,.doc,.docx" class="hidden"
                                    @change="onMouChange" />
                            </label>
                        </div>
                        <label v-else
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed px-4 py-6 text-center transition-colors hover:bg-muted/40">
                            <Upload class="h-5 w-5 text-muted-foreground" />
                            <span class="text-sm text-muted-foreground">Klik untuk unggah MoU</span>
                            <input ref="mouInput" type="file" accept=".pdf,.doc,.docx" class="hidden"
                                @change="onMouChange" />
                        </label>
                        <p v-if="form.errors.file_mou" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.file_mou }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status & Verifikasi (Admin) -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-500/10">
                        <ShieldCheck class="h-4 w-4 text-amber-500" />
                    </div>
                    <div>
                        <h2 class="font-semibold">Status & Verifikasi</h2>
                        <p class="text-xs text-muted-foreground">
                            Status pengajuan dan catatan internal
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Status</label>
                        <select v-model="form.status"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.status }">
                            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                        <p v-if="form.errors.status" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.status }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Catatan Admin</label>
                        <textarea v-model="form.catatan_admin" rows="3"
                            placeholder="Catatan internal terkait pengajuan ini..."
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.catatan_admin }"></textarea>
                        <p v-if="form.errors.catatan_admin" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.catatan_admin }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <Link href="/pendaftaran-kerjasama"
                    class="rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-60">
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</template>
