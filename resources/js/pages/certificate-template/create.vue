<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Loader2, Save, Upload, X } from 'lucide-vue-next';
import { watch } from 'vue';

import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import * as certTemplateRoute from '@/routes/certificate-template';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sertifikat', href: '/certificate' },
    { title: 'Template Sertifikat', href: certTemplateRoute.index().url },
    { title: 'Buat Template', href: certTemplateRoute.create().url },
];

defineOptions({
    layout: AppSidebarLayout,
});

const form = useForm({
    name: '',
    orientation: 'landscape' as 'landscape' | 'portrait',
    width: 1920,
    height: 1080,
    is_active: true,
    background_image: null as File | null,
});

// Otomatis menyesuaikan dimensi standar saat orientasi berubah
watch(
    () => form.orientation,
    (newVal) => {
        if (newVal === 'landscape') {
            form.width = 1920;
            form.height = 1080;
        } else {
            form.width = 1080;
            form.height = 1920;
        }
    },
);

// Helper Validasi Gambar
function validateImage(file: File, fieldName: 'background_image') {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (!allowedTypes.includes(file.type)) {
        form.setError(
            fieldName,
            'Ekstensi file tidak sesuai. Harap unggah gambar dengan format JPG, JPEG, atau PNG.',
        );
        return false;
    }

    if (file.size > maxSize) {
        form.setError(
            fieldName,
            'Ukuran file terlalu besar. Maksimal ukuran file adalah 2MB.',
        );
        return false;
    }

    return true;
}

function handleFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    form.clearErrors('background_image');

    if (target.files && target.files[0]) {
        const file = target.files[0];
        if (validateImage(file, 'background_image')) {
            form.background_image = file;
        } else {
            target.value = '';
            form.background_image = null;
        }
    }
}

function removeFile() {
    form.background_image = null;
    form.clearErrors('background_image');
}

function submit() {
    form.post(certTemplateRoute.store().url, {
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Buat Template Sertifikat" />

    <div class="mx-auto max-w-4xl px-6 py-8">
        <div class="mb-8">
            <Link
                :href="certTemplateRoute.index().url"
                class="mb-3 inline-flex items-center gap-2 text-sm font-bold text-orange-600 hover:text-orange-500 dark:text-orange-400 dark:hover:text-orange-300"
            >
                <ArrowLeft class="h-4 w-4" />
                Kembali ke daftar template
            </Link>
            <h1 class="text-2xl font-black text-slate-950 dark:text-white">
                Buat Template Sertifikat
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Tambahkan rancangan template latar belakang serta ukuran canvas sertifikat.
            </p>
        </div>

        <form
            @submit.prevent="submit"
            class="space-y-6 rounded-[2rem] border border-orange-100 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <!-- Nama Template -->
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-900 dark:text-slate-200">
                    Nama Template
                </label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Contoh: Sertifikat Kelulusan Utama"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:focus:border-orange-400"
                />
                <div
                    v-if="form.errors.name"
                    class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400"
                >
                    {{ form.errors.name }}
                </div>
            </div>

            <!-- Grid Orientasi & Resolusi -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-900 dark:text-slate-200">
                        Orientasi
                    </label>
                    <select
                        v-model="form.orientation"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-orange-400"
                    >
                        <option value="landscape">Landscape</option>
                        <option value="portrait">Portrait</option>
                    </select>
                    <div
                        v-if="form.errors.orientation"
                        class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400"
                    >
                        {{ form.errors.orientation }}
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-900 dark:text-slate-200">
                        Lebar (px)
                    </label>
                    <input
                        v-model.number="form.width"
                        type="number"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-orange-400"
                    />
                    <div
                        v-if="form.errors.width"
                        class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400"
                    >
                        {{ form.errors.width }}
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-900 dark:text-slate-200">
                        Tinggi (px)
                    </label>
                    <input
                        v-model.number="form.height"
                        type="number"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-orange-400"
                    />
                    <div
                        v-if="form.errors.height"
                        class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400"
                    >
                        {{ form.errors.height }}
                    </div>
                </div>
            </div>

            <!-- Background Image Upload -->
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-900 dark:text-slate-200">
                    File Latar Belakang (Background)
                </label>
                <div class="flex items-center justify-center w-full">
                    <div v-if="form.background_image" class="relative flex w-full items-center justify-between rounded-2xl border border-orange-200 bg-orange-50/50 p-4 dark:border-orange-900/50 dark:bg-slate-800/40">
                        <div class="flex items-center gap-3">
                            <Upload class="h-6 w-6 text-orange-500 dark:text-orange-400" />
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ form.background_image.name }}
                                </p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">
                                    {{ (form.background_image.size / (1024 * 1024)).toFixed(2) }} MB
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="removeFile"
                            class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-700 dark:hover:text-slate-200"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <label
                        v-else
                        class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-orange-200 rounded-2xl cursor-pointer bg-orange-50/50 hover:bg-orange-50 transition dark:border-orange-900/50 dark:bg-slate-800/40 dark:hover:bg-slate-800/80"
                    >
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 px-4 text-center">
                            <Upload class="w-8 h-8 mb-2 text-orange-500 dark:text-orange-400" />
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                Klik untuk unggah gambar latar belakang
                            </p>
                            <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                                PNG, JPG (Maks. 2MB)
                            </p>
                        </div>
                        <input
                            type="file"
                            class="hidden"
                            accept="image/png, image/jpeg, image/jpg"
                            @change="handleFileChange"
                        />
                    </label>
                </div>
                <div
                    v-if="form.errors.background_image"
                    class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400"
                >
                    {{ form.errors.background_image }}
                </div>
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                <input
                    v-model="form.is_active"
                    type="checkbox"
                    id="is_active"
                    class="h-5 w-5 rounded border-slate-300 text-orange-600 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:checked:bg-orange-500"
                />
                <label for="is_active" class="text-sm font-bold text-slate-900 dark:text-slate-200">
                    Aktifkan template ini segera
                </label>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                <Link
                    :href="certTemplateRoute.index().url"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                >
                    Batal
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-orange-600/20 transition hover:bg-orange-700 disabled:opacity-50 dark:bg-orange-500 dark:hover:bg-orange-600"
                >
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                    <Save v-else class="h-4 w-4" />
                    Simpan Template
                </button>
            </div>
        </form>
    </div>
</template>