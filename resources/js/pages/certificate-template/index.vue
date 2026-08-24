<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Award,
    Plus,
    LayoutTemplate,
    Trash2,
    CheckCircle2,
    XCircle,
    ArrowLeft,
    AlertTriangle,
} from 'lucide-vue-next';
import { ref } from 'vue';

import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';

interface CertificateTemplate {
    id: number;
    name: string;
    slug: string;
    background_url: string | null;
    orientation: 'landscape' | 'portrait';
    width: number;
    height: number;
    is_active: boolean;
    created_at: string;
}

defineProps<{
    templates: CertificateTemplate[];
}>();

defineOptions({
    layout: AppSidebarLayout,
    breadcrumbs: [
        { title: 'Sertifikat', href: '/certificate' },
        { title: 'Template Sertifikat', href: '/certificate-template' },
    ],
});

const showDeleteModal = ref(false);
const templateToDeleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    templateToDeleteId.value = id;
    showDeleteModal.value = true;
}

function cancelDelete() {
    showDeleteModal.value = false;
    templateToDeleteId.value = null;
}

function deleteTemplate() {
    if (templateToDeleteId.value !== null) {
        router.delete(`/certificate-template/${templateToDeleteId.value}`, {
            preserveScroll: true,
            onFinish: () => {
                cancelDelete();
            },
        });
    }
}
</script>

<template>
    <Head title="Template Sertifikat" />

    <div class="mx-auto max-w-7xl px-6 py-8">
        <!-- Header & Action Button -->
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <Link
                    href="/certificate"
                    class="mb-3 inline-flex items-center gap-2 text-sm font-bold text-orange-700 hover:text-orange-800"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali ke daftar sertifikat
                </Link>
                <h1 class="text-2xl font-black text-slate-950">Template Sertifikat</h1>
                <p class="text-sm text-slate-500">
                    Kelola desain tata letak dan latar belakang sertifikat Anda.
                </p>
            </div>

            <Link
                v-if="templates.length > 0"
                href="/certificate-template/create"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-5 py-3 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30"
            >
                <Plus class="h-4 w-4" stroke-width="2.5" />
                Buat Template Baru
            </Link>
        </div>

        <!-- Empty State -->
        <div
            v-if="templates.length === 0"
            class="flex flex-col items-center justify-center rounded-[2rem] border border-dashed border-orange-200 bg-white p-12 text-center shadow-sm"
        >
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-orange-50 text-orange-600 mb-4">
                <LayoutTemplate class="h-8 w-8" />
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum ada template sertifikat</h3>
            <p class="mt-1 max-w-sm text-sm text-slate-500">
                Silakan buat template baru terlebih dahulu agar Anda dapat mulai menerbitkan sertifikat.
            </p>
            <Link
                href="/certificate-template/create"
                class="mt-6 inline-flex items-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-orange-700"
            >
                <Plus class="h-4 w-4" />
                Buat Template
            </Link>
        </div>

        <!-- Grid Templates -->
        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="template in templates"
                :key="template.id"
                class="group flex flex-col justify-between overflow-hidden rounded-[2rem] border border-orange-100 bg-white shadow-sm shadow-orange-100/40 transition hover:shadow-md"
            >
                <!-- Preview Image / Background Banner -->
                <div class="relative h-48 w-full bg-slate-100 overflow-hidden border-b border-orange-50">
                    <img
                        v-if="template.background_url"
                        :src="template.background_url"
                        :alt="template.name"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    />
                    <div v-else class="flex h-full w-full flex-col items-center justify-center text-slate-400 gap-1 bg-slate-50">
                        <Award class="h-8 w-8 opacity-40 text-orange-500" />
                        <span class="text-[11px] font-medium text-slate-400">Tidak ada background</span>
                    </div>

                    <!-- Status Badge -->
                    <div class="absolute top-3 right-3">
                        <span
                            v-if="template.is_active"
                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/90 px-3 py-1 text-xs font-bold text-white shadow-sm backdrop-blur-md"
                        >
                            <CheckCircle2 class="h-3.5 w-3.5" /> Aktif
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center gap-1.5 rounded-full bg-slate-700/90 px-3 py-1 text-xs font-bold text-white shadow-sm backdrop-blur-md"
                        >
                            <XCircle class="h-3.5 w-3.5" /> Nonaktif
                        </span>
                    </div>

                    <!-- Orientation & Resolution Badge -->
                    <div class="absolute bottom-3 left-3 flex gap-2">
                        <span class="rounded-lg bg-black/60 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur-md">
                            {{ template.orientation }}
                        </span>
                        <span class="rounded-lg bg-black/60 px-2.5 py-1 text-[10px] font-bold text-white backdrop-blur-md">
                            {{ template.width }} × {{ template.height }} px
                        </span>
                    </div>
                </div>

                <!-- Body Content -->
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900 group-hover:text-orange-600 transition">
                            {{ template.name }}
                        </h2>
                        <p class="mt-1 text-xs text-slate-400 font-mono">
                            Slug: {{ template.slug }}
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex items-center gap-2 pt-4 border-t border-slate-100">
                        <Link
                            :href="`/certificate-template/${template.id}/edit`"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 hover:border-slate-300"
                        >
                            Edit Template
                        </Link>
                        <button
                            @click="confirmDelete(template.id)"
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-rose-100 bg-rose-50/50 p-2.5 text-rose-600 transition hover:bg-rose-100 hover:text-rose-700"
                            title="Hapus Template"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-[2rem] bg-white p-6 shadow-2xl border border-slate-100 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 mb-4">
                    <AlertTriangle class="h-7 w-7" />
                </div>
                <h3 class="text-lg font-black text-slate-900">Hapus Template Ini?</h3>
                <p class="mt-2 text-sm text-slate-500">
                    Tindakan ini tidak dapat dibatalkan. Template yang dihapus akan hilang secara permanen dari sistem.
                </p>

                <div class="mt-6 flex items-center gap-3">
                    <button
                        @click="cancelDelete"
                        type="button"
                        class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="deleteTemplate"
                        type="button"
                        class="flex-1 rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-rose-600/20 transition hover:bg-rose-700"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>