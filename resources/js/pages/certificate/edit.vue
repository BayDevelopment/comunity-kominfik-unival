<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Award,
    ArrowLeft,
    Loader2,
    CheckCircle2,
    Upload,
    X,
} from 'lucide-vue-next';
import { ref } from 'vue';

import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface Option {
    id: number;
    name: string;
}

interface CertificateDetail {
    id: number;
    certificate_template_id: number;
    recipient_name: string;
    recipient_email: string | null;
    event_name: string | null;
    course_name: string | null;
    description: string | null;
    signatory_name: string | null;
    signatory_role: string | null;
    signatory_signature_path: string | null;
    signatory_signature_url: string | null;
    issued_at: string;
    expired_at: string | null;
    status: 'draft' | 'published' | 'revoked';
}

const props = defineProps<{
    certificate: CertificateDetail;
    templates: Option[];
}>();

defineOptions({
    layout: AppSidebarLayout,
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sertifikat', href: '/certificate' },
    {
        title: props.certificate.recipient_name,
        href: `/certificate/${props.certificate.id}`,
    },
    { title: 'Edit', href: `/certificate/${props.certificate.id}/edit` },
];

const form = useForm({
    _method: 'put',
    certificate_template_id: props.certificate.certificate_template_id,
    recipient_name: props.certificate.recipient_name,
    recipient_email: props.certificate.recipient_email ?? '',
    event_name: props.certificate.event_name ?? '',
    course_name: props.certificate.course_name ?? '',
    description: props.certificate.description ?? '',
    signatory_name: props.certificate.signatory_name ?? '',
    signatory_role: props.certificate.signatory_role ?? '',
    signatory_signature_path: null as File | null,
    issued_at: props.certificate.issued_at?.slice(0, 10) ?? '',
    expired_at: props.certificate.expired_at?.slice(0, 10) ?? '',
    status: props.certificate.status,
});

// Preview file tanda tangan baru (jika diunggah)
const newSignaturePreview = ref<string | null>(null);

function handleSignatureUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    form.signatory_signature_path = file;

    if (file) {
        newSignaturePreview.value = URL.createObjectURL(file);
    } else {
        newSignaturePreview.value = null;
    }
}

function removeNewSignature() {
    form.signatory_signature_path = null;
    newSignaturePreview.value = null;

    const input = document.getElementById(
        'signatory_signature_path',
    ) as HTMLInputElement | null;
    if (input) input.value = '';
}

function submit() {
    // Menggunakan POST + _method: put untuk mendukung upload FormData di Laravel
    form.post(`/certificate/${props.certificate.id}`, {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Edit Sertifikat" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6">
        <!-- Back Button -->
        <Link
            :href="`/certificate/${certificate.id}`"
            class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 transition hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300"
        >
            <ArrowLeft class="h-4 w-4" />
            Kembali ke detail sertifikat
        </Link>

        <!-- Success Alert -->
        <div
            v-if="form.recentlySuccessful"
            class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 text-emerald-800 shadow-sm dark:border-emerald-900/40 dark:bg-emerald-500/10 dark:text-emerald-400"
        >
            <CheckCircle2 class="h-5 w-5 shrink-0" />
            <div class="text-sm font-semibold">
                Perubahan berhasil disimpan.
            </div>
        </div>

        <!-- Warning Note -->
        <div
            v-if="
                form.status === 'published' &&
                certificate.status === 'published'
            "
            class="rounded-xl border border-amber-200 bg-amber-50/60 p-4 text-xs font-semibold text-amber-800 shadow-sm dark:border-amber-900/40 dark:bg-amber-500/10 dark:text-amber-400"
        >
            Catatan: Mengubah data sertifikat yang sudah terbit akan memperbarui
            dokumen PDF saat diunduh kembali.
        </div>

        <!-- Form Card -->
        <form
            @submit.prevent="submit"
            class="rounded-xl border bg-background p-6 shadow-sm md:p-8 dark:border-slate-800"
        >
            <!-- Header -->
            <div
                class="flex items-center gap-4 border-b pb-6 dark:border-slate-800"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500/10 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400"
                >
                    <Award class="h-6 w-6" stroke-width="2.2" />
                </div>

                <div>
                    <h1 class="text-xl font-bold text-foreground">
                        Edit Sertifikat
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Perbarui informasi penerima dan konfigurasi sertifikat.
                    </p>
                </div>
            </div>

            <!-- Fields Grid -->
            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                <!-- Template Sertifikat -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Template Sertifikat
                        <span class="text-rose-500">*</span>
                    </label>

                    <select
                        v-model="form.certificate_template_id"
                        required
                        :disabled="templates.length === 0"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 disabled:cursor-not-allowed disabled:bg-muted disabled:opacity-60 dark:border-slate-700 dark:focus:border-orange-400"
                    >
                        <option value="" disabled>Pilih template</option>
                        <option
                            v-for="template in templates"
                            :key="template.id"
                            :value="template.id"
                        >
                            {{ template.name }}
                        </option>
                    </select>

                    <p
                        v-if="templates.length === 0"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        Belum ada template tersedia. Harap tambahkan template
                        terlebih dahulu.
                    </p>

                    <p
                        v-if="form.errors.certificate_template_id"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.certificate_template_id }}
                    </p>
                </div>

                <!-- Nama Penerima -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Nama Penerima
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        v-model="form.recipient_name"
                        type="text"
                        required
                        placeholder="Nama lengkap peserta"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    />

                    <p
                        v-if="form.errors.recipient_name"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.recipient_name }}
                    </p>
                </div>

                <!-- Email Penerima -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Email Penerima
                    </label>

                    <input
                        v-model="form.recipient_email"
                        type="email"
                        placeholder="email@example.com"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    />

                    <p
                        v-if="form.errors.recipient_email"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.recipient_email }}
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Status
                        <span class="text-rose-500">*</span>
                    </label>

                    <select
                        v-model="form.status"
                        required
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    >
                        <option value="draft">Draf (Draft)</option>
                        <option value="published">Terbit (Published)</option>
                        <option value="revoked">Dicabut (Revoked)</option>
                    </select>

                    <p
                        v-if="form.errors.status"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.status }}
                    </p>
                </div>

                <!-- Nama Event -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Nama Event
                    </label>

                    <input
                        v-model="form.event_name"
                        type="text"
                        placeholder="Contoh: Webinar National Tech"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    />

                    <p
                        v-if="form.errors.event_name"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.event_name }}
                    </p>
                </div>

                <!-- Nama Kursus / Materi -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Nama Kursus / Materi
                    </label>

                    <input
                        v-model="form.course_name"
                        type="text"
                        placeholder="Contoh: Master Microservices Laravel"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    />

                    <p
                        v-if="form.errors.course_name"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.course_name }}
                    </p>
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Keterangan Sertifikat
                    </label>

                    <textarea
                        v-model="form.description"
                        rows="3"
                        placeholder="Deskripsi singkat pencapaian..."
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    ></textarea>

                    <p
                        v-if="form.errors.description"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>

                <!-- Nama Penanda Tangan -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Nama Penanda Tangan
                    </label>

                    <input
                        v-model="form.signatory_name"
                        type="text"
                        placeholder="Contoh: Alex Ferguson, M.T."
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    />

                    <p
                        v-if="form.errors.signatory_name"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.signatory_name }}
                    </p>
                </div>

                <!-- Jabatan Penanda Tangan -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Jabatan Penanda Tangan
                    </label>

                    <input
                        v-model="form.signatory_role"
                        type="text"
                        placeholder="Contoh: Chief Executive Officer / Instructor"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    />

                    <p
                        v-if="form.errors.signatory_role"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.signatory_role }}
                    </p>
                </div>

                <!-- Tanggal Terbit -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Tanggal Terbit
                    </label>

                    <input
                        v-model="form.issued_at"
                        type="date"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    />

                    <p
                        v-if="form.errors.issued_at"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.issued_at }}
                    </p>
                </div>

                <!-- Tanggal Expired -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Berlaku Sampai
                    </label>

                    <input
                        v-model="form.expired_at"
                        type="date"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:focus:border-orange-400"
                    />

                    <p
                        v-if="form.errors.expired_at"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.expired_at }}
                    </p>
                </div>

                <!-- Gambar Tanda Tangan -->
                <div class="md:col-span-2">
                    <label
                        class="mb-2 block text-sm font-semibold text-foreground"
                    >
                        Gambar Tanda Tangan
                    </label>

                    <!-- Preview file baru yang dipilih -->
                    <div
                        v-if="newSignaturePreview"
                        class="flex items-center gap-3 rounded-lg border bg-muted/30 p-3 dark:border-slate-700"
                    >
                        <img
                            :src="newSignaturePreview"
                            alt="Preview Tanda Tangan Baru"
                            class="h-12 w-auto rounded border bg-white object-contain p-1"
                        />
                        <span
                            class="flex-1 truncate text-xs text-muted-foreground"
                        >
                            {{ form.signatory_signature_path?.name }} (File baru)
                        </span>
                        <button
                            type="button"
                            @click="removeNewSignature"
                            class="rounded-md p-1.5 text-rose-500 transition hover:bg-rose-50 dark:hover:bg-rose-950/40 dark:hover:text-rose-400"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <!-- Tanda tangan yang sudah tersimpan sebelumnya -->
                    <div
                        v-else-if="certificate.signatory_signature_url"
                        class="space-y-2"
                    >
                        <div
                            class="flex items-center gap-3 rounded-lg border bg-muted/30 p-3 dark:border-slate-700"
                        >
                            <img
                                :src="certificate.signatory_signature_url"
                                alt="Tanda Tangan Tersimpan"
                                class="h-12 w-auto rounded border bg-white object-contain p-1"
                            />
                            <span class="flex-1 text-xs text-muted-foreground">
                                Tanda tangan tersimpan
                            </span>
                        </div>

                        <label
                            for="signatory_signature_path"
                            class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-input px-4 py-2.5 text-xs font-medium text-muted-foreground transition hover:border-orange-500 hover:text-orange-600 dark:border-slate-700 dark:hover:border-orange-400 dark:hover:text-orange-400"
                        >
                            <Upload class="h-3.5 w-3.5" />
                            Ganti gambar tanda tangan
                        </label>
                        <input
                            id="signatory_signature_path"
                            type="file"
                            accept="image/png,image/jpeg,image/jpg"
                            class="hidden"
                            @change="handleSignatureUpload"
                        />
                    </div>

                    <!-- Upload baru (jika belum ada tanda tangan sebelumnya) -->
                    <div v-else>
                        <label
                            for="signatory_signature_path"
                            class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-input px-4 py-3 text-sm font-medium text-muted-foreground transition hover:border-orange-500 hover:text-orange-600 dark:border-slate-700 dark:hover:border-orange-400 dark:hover:text-orange-400"
                        >
                            <Upload class="h-4 w-4" />
                            Upload Tanda Tangan (PNG Transparan)
                        </label>
                        <input
                            id="signatory_signature_path"
                            type="file"
                            accept="image/png,image/jpeg,image/jpg"
                            class="hidden"
                            @change="handleSignatureUpload"
                        />
                    </div>

                    <p
                        v-if="form.errors.signatory_signature_path"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.signatory_signature_path }}
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-end gap-3">
                <Link
                    :href="`/certificate/${certificate.id}`"
                    class="rounded-xl border border-input bg-background px-5 py-2.5 text-sm font-semibold text-foreground transition hover:bg-muted dark:border-slate-700"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing || templates.length === 0"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-orange-500 dark:hover:bg-orange-600"
                >
                    <Loader2
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                    />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
            </div>
        </form>
    </div>
</template>