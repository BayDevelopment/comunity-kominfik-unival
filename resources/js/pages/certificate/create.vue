<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Award,
    ArrowLeft,
    Loader2,
    Upload,
    X,
    AlertCircle,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface OptionItem {
    id: number;
    name: string;
}

defineOptions({
    layout: AppSidebarLayout,
});

const props = withDefaults(
    defineProps<{
        templates?: OptionItem[];
    }>(),
    {
        templates: () => [],
    },
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sertifikat',
        href: '/certificate',
    },
    {
        title: 'Terbitkan Sertifikat',
        href: '/certificate/create',
    },
];

const form = useForm({
    certificate_template_id: '' as number | '',
    recipient_name: '',
    recipient_email: '',
    event_name: '',
    course_name: '',
    description: '',
    signatory_name: '',
    signatory_role: '',
    signatory_signature_path: null as File | null,
    issued_at: new Date().toISOString().slice(0, 10),
    expired_at: '',
    status: 'published' as 'draft' | 'published',
});

// Cek keberadaan data templates secara aman
const hasTemplates = computed(() => {
    return Array.isArray(props.templates) && props.templates.length > 0;
});

// Tombol disabled jika sedang submit, template belum ada/dipilih, atau nama penerima kosong
const isSubmitDisabled = computed(() => {
    return (
        form.processing ||
        !hasTemplates.value ||
        !form.certificate_template_id ||
        !form.recipient_name.trim()
    );
});

// Preview gambar tanda tangan
const signaturePreview = ref<string | null>(null);

function handleSignatureUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    form.signatory_signature_path = file;

    if (file) {
        signaturePreview.value = URL.createObjectURL(file);
    } else {
        signaturePreview.value = null;
    }
}

function removeSignature() {
    form.signatory_signature_path = null;
    signaturePreview.value = null;

    const input = document.getElementById(
        'signatory_signature_path',
    ) as HTMLInputElement | null;
    if (input) input.value = '';
}

function submit() {
    if (isSubmitDisabled.value) return;

    form.post('/certificate', {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Terbitkan Sertifikat" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6">
        <!-- Back Button -->
        <Link
            href="/certificate"
            class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 transition hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300"
        >
            <ArrowLeft class="h-4 w-4" />
            Kembali ke daftar sertifikat
        </Link>

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
                        Terbitkan Sertifikat Baru
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Isi detail informasi penerima dan pilih template
                        sertifikat.
                    </p>
                </div>
            </div>

            <!-- Warning Banner bila Template Kosong -->
            <div
                v-if="!hasTemplates"
                class="mt-6 flex items-start gap-3 rounded-lg border border-amber-500/20 bg-amber-500/10 p-4 text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-400"
            >
                <AlertCircle
                    class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400"
                />
                <div class="text-sm">
                    <p class="font-semibold">
                        Template sertifikat belum tersedia
                    </p>
                    <p class="mt-0.5 opacity-90">
                        Belum ada data master template sertifikat di database.
                        Silakan buat master template terlebih dahulu.
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
                        :disabled="!hasTemplates"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground transition outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 disabled:cursor-not-allowed disabled:bg-muted disabled:opacity-60 dark:border-slate-700 dark:focus:border-orange-400"
                    >
                        <option value="" disabled>
                            {{
                                hasTemplates
                                    ? 'Pilih template sertifikat'
                                    : 'Template tidak tersedia'
                            }}
                        </option>
                        <option
                            v-for="template in props.templates"
                            :key="template.id"
                            :value="template.id"
                        >
                            {{ template.name }}
                        </option>
                    </select>

                    <p
                        v-if="!hasTemplates"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        Dropdown dikunci karena tidak ada template sertifikat
                        aktif.
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

                <!-- Status Publikasi -->
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

                    <div v-if="!signaturePreview">
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

                    <div
                        v-else
                        class="flex items-center gap-3 rounded-lg border bg-muted/30 p-3 dark:border-slate-700"
                    >
                        <img
                            :src="signaturePreview"
                            alt="Preview Tanda Tangan"
                            class="h-12 w-auto rounded border bg-white object-contain p-1"
                        />
                        <span
                            class="flex-1 truncate text-xs text-muted-foreground"
                        >
                            {{ form.signatory_signature_path?.name }}
                        </span>
                        <button
                            type="button"
                            @click="removeSignature"
                            class="rounded-md p-1.5 text-rose-500 transition hover:bg-rose-50 dark:hover:bg-rose-950/40 dark:hover:text-rose-400"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <p
                        v-if="form.errors.signatory_signature_path"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.signatory_signature_path }}
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="isSubmitDisabled"
                class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500 disabled:opacity-70 dark:bg-orange-500 dark:hover:bg-orange-600 dark:disabled:bg-slate-800 dark:disabled:text-slate-500"
            >
                <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{
                    form.processing
                        ? 'Menyimpan...'
                        : !hasTemplates
                          ? 'Tidak Bisa Menerbitkan (Template Kosong)'
                          : 'Terbitkan Sertifikat'
                }}
            </button>
        </form>
    </div>
</template>