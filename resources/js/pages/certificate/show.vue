<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Award,
    ArrowLeft,
    Pencil,
    Trash2,
    Ban,
    Download,
    UserRound,
    Mail,
    Hash,
    CalendarDays,
    CalendarX,
    BadgeCheck,
    FileText,
    PenLine,
    Eye,
    CheckCircle,
    X,
    AlertTriangle,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import * as certRoute from '@/routes/certificate';

interface CertificateDetail {
    id: number;
    uuid: string;
    certificate_number: string;
    recipient_name: string;
    recipient_email: string | null;
    event_name: string | null;
    course_name: string | null;
    description: string | null;
    issued_at: string;
    expired_at: string | null;
    status: 'draft' | 'published' | 'revoked';
    signed_by: string | null;
    verification_code: string;
    download_count: number;
    last_downloaded_at: string | null;
    revoke_reason: string | null;
    revoked_at: string | null;
    template: { id: number; name: string } | null;
    program: { id: number; name: string } | null;
    revoked_by: { id: number; name: string } | null;
}

const props = defineProps<{
    certificate: CertificateDetail;
}>();

defineOptions({
    layout: AppSidebarLayout,
});

// Flash Message sukses
const page = usePage();
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);
const showFlash = ref(true);

// State untuk Modal Cabut Sertifikat
const showRevokeModal = ref(false);
const revokeReason = ref('');

// State untuk Modal Hapus Sertifikat
const showDeleteModal = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sertifikat', href: certRoute.index().url },
    {
        title: props.certificate.certificate_number,
        href: certRoute.show(props.certificate.id).url,
    },
];

function formatDate(date: string | null): string {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function formatDateTime(date: string | null): string {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function statusStyle(status: CertificateDetail['status']) {
    return {
        published: 'bg-emerald-50 text-emerald-700 border-emerald-100',
        draft: 'bg-slate-100 text-slate-600 border-slate-200',
        revoked: 'bg-rose-50 text-rose-700 border-rose-100',
    }[status];
}

function statusLabel(status: CertificateDetail['status']) {
    return {
        published: 'Terbit',
        draft: 'Draf',
        revoked: 'Dicabut',
    }[status];
}

// Eksekusi Cabut Sertifikat via Modal
function submitRevoke() {
    if (!revokeReason.value.trim()) return;

    router.patch(
        certRoute.revoke(props.certificate.id).url,
        { revoke_reason: revokeReason.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                showRevokeModal.value = false;
                revokeReason.value = '';
            },
        },
    );
}

// Eksekusi Hapus Sertifikat via Modal
function submitDestroy() {
    router.delete(certRoute.destroy(props.certificate.id).url, {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
}
</script>

<template>
    <div class="mx-auto max-w-3xl px-6 py-8">
        <!-- Flash Alert / Notifikasi Sukses Custom -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform -translate-y-2 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform -translate-y-2 opacity-0"
        >
            <div
                v-if="flashSuccess && showFlash"
                class="mb-6 flex items-center justify-between rounded-2xl border border-emerald-100 bg-emerald-50/80 px-5 py-4 text-emerald-900 shadow-sm backdrop-blur-md"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-md shadow-emerald-500/20">
                        <CheckCircle class="h-5 w-5" />
                    </div>
                    <div>
                        <h4 class="text-sm font-black">Berhasil!</h4>
                        <p class="text-xs text-emerald-700 font-medium">{{ flashSuccess }}</p>
                    </div>
                </div>
                <button
                    @click="showFlash = false"
                    type="button"
                    class="rounded-xl p-2 text-emerald-600 transition hover:bg-emerald-100/60"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </transition>

        <div class="flex items-center justify-between">
            <Link
                :href="certRoute.index().url"
                class="inline-flex items-center gap-2 text-sm font-bold text-orange-700 hover:text-orange-800"
            >
                <ArrowLeft class="h-4 w-4" />
                Kembali ke daftar sertifikat
            </Link>

            <span
                class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold"
                :class="statusStyle(certificate.status)"
            >
                {{ statusLabel(certificate.status) }}
            </span>
        </div>

        <!-- Alasan pencabutan -->
        <div
            v-if="certificate.status === 'revoked'"
            class="mt-6 rounded-2xl border border-rose-100 bg-rose-50/60 p-5"
        >
            <p class="text-sm font-black text-rose-800">
                Sertifikat ini telah dicabut
            </p>
            <p class="mt-1 text-sm text-rose-700">
                {{ certificate.revoke_reason }}
            </p>
            <p class="mt-2 text-xs text-rose-500">
                Dicabut pada {{ formatDateTime(certificate.revoked_at) }}
                <template v-if="certificate.revoked_by">
                    oleh {{ certificate.revoked_by.name }}</template
                >
            </p>
        </div>

        <!-- Card utama -->
        <div
            class="mt-6 rounded-[2rem] border border-orange-100 bg-white p-8 shadow-sm shadow-orange-100/50 md:p-10"
        >
            <div
                class="flex items-center gap-4 border-b border-orange-100 pb-6"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-lg shadow-orange-500/20"
                >
                    <Award class="h-6 w-6" stroke-width="2.4" />
                </div>
                <div>
                    <p
                        class="text-xs font-black tracking-widest text-orange-600 uppercase"
                    >
                        Sertifikat
                    </p>
                    <h1 class="text-xl font-black text-slate-950">
                        {{ certificate.recipient_name }}
                    </h1>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="flex items-start gap-3">
                    <UserRound class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                    <div>
                        <p
                            class="text-xs font-bold tracking-wide text-slate-400 uppercase"
                        >
                            Nama Penerima
                        </p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ certificate.recipient_name }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <Mail class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                    <div>
                        <p
                            class="text-xs font-bold tracking-wide text-slate-400 uppercase"
                        >
                            Email
                        </p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ certificate.recipient_email ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <Hash class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                    <div>
                        <p
                            class="text-xs font-bold tracking-wide text-slate-400 uppercase"
                        >
                            Nomor Sertifikat
                        </p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ certificate.certificate_number }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <BadgeCheck
                        class="mt-0.5 h-4 w-4 shrink-0 text-slate-400"
                    />
                    <div>
                        <p
                            class="text-xs font-bold tracking-wide text-slate-400 uppercase"
                        >
                            Program
                        </p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ certificate.program?.name ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <FileText class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                    <div>
                        <p
                            class="text-xs font-bold tracking-wide text-slate-400 uppercase"
                        >
                            Template
                        </p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ certificate.template?.name ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <PenLine class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                    <div>
                        <p
                            class="text-xs font-bold tracking-wide text-slate-400 uppercase"
                        >
                            Ditandatangani oleh
                        </p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ certificate.signed_by ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <CalendarDays
                        class="mt-0.5 h-4 w-4 shrink-0 text-slate-400"
                    />
                    <div>
                        <p
                            class="text-xs font-bold tracking-wide text-slate-400 uppercase"
                        >
                            Diterbitkan
                        </p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ formatDate(certificate.issued_at) }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <CalendarX class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                    <div>
                        <p
                            class="text-xs font-bold tracking-wide text-slate-400 uppercase"
                        >
                            Berlaku Sampai
                        </p>
                        <p class="text-sm font-bold text-slate-900">
                            {{ formatDate(certificate.expired_at) }}
                        </p>
                    </div>
                </div>

                <div v-if="certificate.description" class="md:col-span-2">
                    <p
                        class="text-xs font-bold tracking-wide text-slate-400 uppercase"
                    >
                        Keterangan
                    </p>
                    <p class="mt-1 text-sm leading-6 text-slate-700">
                        {{ certificate.description }}
                    </p>
                </div>
            </div>

            <!-- Info verifikasi & tracking -->
            <div
                class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 rounded-2xl bg-slate-50 px-5 py-4 text-xs text-slate-500"
            >
                <span class="flex items-center gap-1.5">
                    <Eye class="h-3.5 w-3.5" />
                    Diunduh {{ certificate.download_count }}x
                </span>
                <span v-if="certificate.last_downloaded_at">
                    Terakhir:
                    {{ formatDateTime(certificate.last_downloaded_at) }}
                </span>
                <span class="font-mono"
                    >Kode verifikasi: {{ certificate.verification_code }}</span
                >
            </div>

            <!-- Aksi -->
            <div class="mt-8 flex flex-wrap gap-3">
                <a
                    :href="`/certificate/${certificate.id}/download`"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-5 py-3 text-sm font-black text-white shadow-lg shadow-orange-500/25 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-500/30"
                >
                    <Download class="h-4 w-4" />
                    Unduh PDF
                </a>

                <Link
                    :href="certRoute.edit(certificate.id).url"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                >
                    <Pencil class="h-4 w-4" />
                    Edit
                </Link>

                <button
                    v-if="certificate.status !== 'revoked'"
                    @click="showRevokeModal = true"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-rose-100 bg-rose-50 px-5 py-3 text-sm font-bold text-rose-700 transition hover:bg-rose-100"
                >
                    <Ban class="h-4 w-4" />
                    Cabut Sertifikat
                </button>

                <button
                    @click="showDeleteModal = true"
                    class="ml-auto inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold text-slate-400 transition hover:bg-slate-50 hover:text-rose-600"
                >
                    <Trash2 class="h-4 w-4" />
                    Hapus
                </button>
            </div>
        </div>

        <!-- Custom Modal: Cabut Sertifikat (Pengganti window.prompt) -->
        <div v-if="showRevokeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-[2rem] bg-white p-6 shadow-2xl border border-slate-100 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 mb-4">
                    <AlertTriangle class="h-7 w-7" />
                </div>
                <h3 class="text-lg font-black text-slate-900">Cabut Sertifikat Ini?</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Masukkan alasan mengapa sertifikat <span class="font-semibold text-slate-700">{{ certificate.certificate_number }}</span> ini dicabut:
                </p>

                <div class="mt-4 text-left">
                    <textarea
                        v-model="revokeReason"
                        rows="3"
                        placeholder="Contoh: Kesalahan penulisan nama / pembatalan kelulusan..."
                        class="w-full rounded-2xl border border-slate-200 p-3 text-sm text-slate-800 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20"
                    ></textarea>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button
                        @click="showRevokeModal = false; revokeReason = ''"
                        type="button"
                        class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="submitRevoke"
                        type="button"
                        :disabled="!revokeReason.trim()"
                        class="flex-1 rounded-xl bg-amber-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-amber-600/20 transition hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Ya, Cabut
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Modal: Hapus Sertifikat (Pengganti window.confirm) -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-[2rem] bg-white p-6 shadow-2xl border border-slate-100 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 mb-4">
                    <AlertTriangle class="h-7 w-7" />
                </div>
                <h3 class="text-lg font-black text-slate-900">Hapus Sertifikat Ini?</h3>
                <p class="mt-2 text-sm text-slate-500">
                    Tindakan ini akan menghapus sertifikat <span class="font-semibold text-slate-700">{{ certificate.certificate_number }}</span>. Data masih bisa dipulihkan melalui database jika diperlukan.
                </p>

                <div class="mt-6 flex items-center gap-3">
                    <button
                        @click="showDeleteModal = false"
                        type="button"
                        class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="submitDestroy"
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