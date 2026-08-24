<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Award,
    ArrowLeft,
    Loader2,
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

defineOptions({
    layout: AppSidebarLayout,
});

defineProps<{
    templates: Option[];
    programs: Option[];
}>();

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
    certificate_program_id: '' as number | '',
    recipient_name: '',
    recipient_email: '',
    event_name: '',
    course_name: '',
    description: '',
    issued_at: new Date().toISOString().slice(0, 10),
    expired_at: '',
    signed_by: '',
    signatory_name: '',
    signature_image: null as File | null,
    status: 'published' as 'draft' | 'published',
});

// Preview gambar tanda tangan sebelum di-submit
const signaturePreview = ref<string | null>(null);

function handleSignatureUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    form.signature_image = file;

    if (file) {
        signaturePreview.value = URL.createObjectURL(file);
    } else {
        signaturePreview.value = null;
    }
}

function removeSignature() {
    form.signature_image = null;
    signaturePreview.value = null;

    const input = document.getElementById('signature_image') as HTMLInputElement | null;
    if (input) input.value = '';
}

function submit() {
    form.post('/certificate', {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Terbitkan Sertifikat" />

    <div class="mx-auto max-w-3xl space-y-6 p-6">
        <!-- Back -->
        <Link
            href="/certificate"
            class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 transition hover:text-orange-700"
        >
            <ArrowLeft class="h-4 w-4" />
            Kembali ke daftar sertifikat
        </Link>

        <!-- Form Card -->
        <form
            @submit.prevent="submit"
            class="rounded-xl border bg-background p-6 shadow-sm md:p-8"
        >
            <!-- Header -->
            <div class="flex items-center gap-4 border-b pb-6">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500/10 text-orange-600"
                >
                    <Award class="h-6 w-6" stroke-width="2.2" />
                </div>

                <div>
                    <h1 class="text-xl font-bold">
                        Terbitkan Sertifikat Baru
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Isi detail informasi penerima dan template sertifikat.
                    </p>
                </div>
            </div>

            <!-- Fields Grid -->
            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                <!-- Template -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Template Sertifikat
                        <span class="text-rose-500">*</span>
                    </label>

                    <select
                        v-model="form.certificate_template_id"
                        required
                        :disabled="templates.length === 0"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 disabled:cursor-not-allowed disabled:bg-muted disabled:opacity-60"
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
                        Belum ada template tersedia. Harap tambahkan template terlebih dahulu.
                    </p>

                    <p
                        v-if="form.errors.certificate_template_id"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.certificate_template_id }}
                    </p>
                </div>

                <!-- Program -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Program / Kegiatan
                    </label>

                    <select
                        v-model="form.certificate_program_id"
                        :disabled="programs.length === 0"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 disabled:cursor-not-allowed disabled:bg-muted disabled:opacity-60"
                    >
                        <option value="">Tanpa program spesifik</option>
                        <option
                            v-for="program in programs"
                            :key="program.id"
                            :value="program.id"
                        >
                            {{ program.name }}
                        </option>
                    </select>

                    <p
                        v-if="programs.length === 0"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        Belum ada data program yang tersedia.
                    </p>

                    <p
                        v-if="form.errors.certificate_program_id"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.certificate_program_id }}
                    </p>
                </div>

                <!-- Nama Penerima -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Nama Penerima
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        v-model="form.recipient_name"
                        type="text"
                        required
                        placeholder="Nama lengkap peserta"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
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
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Email Penerima
                    </label>

                    <input
                        v-model="form.recipient_email"
                        type="email"
                        placeholder="Dipakai peserta saat cek sertifikat"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                    />

                    <p
                        v-if="form.errors.recipient_email"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.recipient_email }}
                    </p>
                </div>

                <!-- Nama Kegiatan -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Nama Kegiatan
                    </label>

                    <input
                        v-model="form.event_name"
                        type="text"
                        placeholder="Contoh: Coding Camp 2026 Batch 1"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                    />

                    <p
                        v-if="form.errors.event_name"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.event_name }}
                    </p>
                </div>

                <!-- Materi / Kursus -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Materi / Kursus
                    </label>

                    <input
                        v-model="form.course_name"
                        type="text"
                        placeholder="Contoh: Dasar Pemrograman Web"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
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
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Keterangan Sertifikat
                    </label>

                    <textarea
                        v-model="form.description"
                        rows="4"
                        placeholder="Contoh: telah menyelesaikan pelatihan selama 40 jam dengan predikat Baik"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                    ></textarea>

                    <p
                        v-if="form.errors.description"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>

                <!-- Tanggal Terbit -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Tanggal Terbit
                    </label>

                    <input
                        v-model="form.issued_at"
                        type="date"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                    />

                    <p
                        v-if="form.errors.issued_at"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.issued_at }}
                    </p>
                </div>

                <!-- Tanggal Kedaluwarsa -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Berlaku Sampai
                        <span class="font-normal text-muted-foreground">(opsional)</span>
                    </label>

                    <input
                        v-model="form.expired_at"
                        type="date"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                    />

                    <p
                        v-if="form.errors.expired_at"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.expired_at }}
                    </p>
                </div>

                <!-- Ditandatangani Oleh -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Ditandatangani oleh
                    </label>

                    <input
                        v-model="form.signed_by"
                        type="text"
                        placeholder="Contoh: Ketua Panitia"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                    />

                    <p
                        v-if="form.errors.signed_by"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.signed_by }}
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Status
                        <span class="text-rose-500">*</span>
                    </label>

                    <select
                        v-model="form.status"
                        required
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                    >
                        <option value="draft">
                            Draf — belum bisa diunduh publik
                        </option>
                        <option value="published">
                            Terbit — langsung bisa dicek & diunduh
                        </option>
                    </select>

                    <p
                        v-if="form.errors.status"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.status }}
                    </p>
                </div>

                <!-- Nama Resmi Penanda Tangan -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Nama Resmi Penanda Tangan
                        <span class="font-normal text-muted-foreground">(opsional)</span>
                    </label>

                    <input
                        v-model="form.signatory_name"
                        type="text"
                        placeholder="Contoh: Hery Firmansyah"
                        class="w-full rounded-lg border bg-background px-4 py-2.5 text-sm text-foreground outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                    />

                    <p class="mt-1.5 text-xs text-muted-foreground">
                        Kalau diisi, nama ini yang tampil di sertifikat (bukan "Ditandatangani oleh" di atas).
                    </p>

                    <p
                        v-if="form.errors.signatory_name"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.signatory_name }}
                    </p>
                </div>

                <!-- Gambar Tanda Tangan -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-foreground">
                        Gambar Tanda Tangan
                        <span class="font-normal text-muted-foreground">(opsional, PNG/JPG maks 2MB)</span>
                    </label>

                    <div v-if="!signaturePreview">
                        <label
                            for="signature_image"
                            class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed px-4 py-4 text-sm font-medium text-muted-foreground transition hover:border-orange-500 hover:text-orange-600"
                        >
                            <Upload class="h-4 w-4" />
                            Klik untuk upload gambar tanda tangan
                        </label>
                        <input
                            id="signature_image"
                            type="file"
                            accept="image/png,image/jpeg,image/jpg"
                            class="hidden"
                            @change="handleSignatureUpload"
                        />
                    </div>

                    <div
                        v-else
                        class="flex items-center gap-3 rounded-lg border bg-muted/30 p-3"
                    >
                        <img
                            :src="signaturePreview"
                            alt="Preview tanda tangan"
                            class="h-12 w-auto rounded border bg-white object-contain p-1"
                        />
                        <span class="flex-1 truncate text-xs text-muted-foreground">
                            {{ form.signature_image?.name }}
                        </span>
                        <button
                            type="button"
                            @click="removeSignature"
                            class="rounded-md p-1.5 text-rose-500 transition hover:bg-rose-50"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <p
                        v-if="form.errors.signature_image"
                        class="mt-1.5 text-xs font-semibold text-rose-500"
                    >
                        {{ form.errors.signature_image }}
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="form.processing || templates.length === 0"
                class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <Loader2
                    v-if="form.processing"
                    class="h-4 w-4 animate-spin"
                />
                {{
                    form.processing
                        ? 'Menyimpan...'
                        : 'Terbitkan Sertifikat'
                }}
            </button>
        </form>
    </div>
</template>