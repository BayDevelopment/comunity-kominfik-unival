<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Pencil,
    Trash2,
    Mail,
    Calendar,
    Shield,
    Send,
    Loader2,
    BadgeCheck,
    BadgeAlert,
} from '@lucide/vue';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { dashboard } from '@/routes';

interface User {
    id: number;
    name: string;
    email: string;
    role: 'academy';
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    user: User;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Pengguna',
                href: '/users',
            },
            {
                title: 'Detail Pengguna',
                href: '#',
            },
        ],
    },
});

const roleConfig = {
    academy: {
        label: 'Pengurus',
        class: 'bg-purple-50 text-purple-700 ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/20',
        icon: Shield,
    },
};

function formatDateTime(date: string | null): string {
    if (!date) {
        return '—';
    }

    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function getInitials(name: string): string {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

function getAvatarColor(name: string): string {
    const colors = [
        'bg-red-500', 'bg-orange-500', 'bg-amber-500', 'bg-yellow-500',
        'bg-lime-500', 'bg-green-500', 'bg-emerald-500', 'bg-teal-500',
        'bg-cyan-500', 'bg-sky-500', 'bg-blue-500', 'bg-indigo-500',
        'bg-violet-500', 'bg-purple-500', 'bg-fuchsia-500', 'bg-pink-500',
        'bg-rose-500',
    ];

    let hash = 0;

    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    return colors[Math.abs(hash) % colors.length];
}

const resending = ref(false);

function resendVerification() {
    resending.value = true;
    router.post(`/users/${props.user.id}/resend-verification`, {}, {
        preserveScroll: true,
        onFinish: () => {
            resending.value = false;
        },
    });
}

const deleting = ref(false);

function hapusUser() {
    Swal.fire({
        title: 'Hapus Pengguna?',
        html: `Pengguna <b>"${props.user.name}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
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
            router.delete(`/users/${props.user.id}`, {
                onFinish: () => {
                    deleting.value = false;
                },
            });
        }
    });
}
</script>

<template>

    <Head :title="user.name" />

    <div class="mx-auto max-w-2xl space-y-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <Link href="/users"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold">Detail Pengguna</h1>
                    <p class="text-sm text-muted-foreground">
                        Informasi lengkap akun pengurus.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Link :href="`/users/${user.id}/edit`"
                    class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-medium transition-colors hover:bg-muted">
                    <Pencil class="h-4 w-4" />
                    Edit
                </Link>
                <button type="button" :disabled="deleting" @click="hapusUser"
                    class="inline-flex items-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 disabled:pointer-events-none disabled:opacity-50 dark:border-rose-500/30 dark:hover:bg-rose-500/10">
                    <Trash2 class="h-4 w-4" />
                    Hapus
                </button>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="rounded-xl border bg-background shadow-sm">
            <div class="flex items-center gap-4 border-b p-6">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl text-xl font-medium text-white"
                    :class="getAvatarColor(user.name)">
                    {{ getInitials(user.name) }}
                </div>
                <div class="min-w-0">
                    <h2 class="truncate text-lg font-semibold">{{ user.name }}</h2>
                    <p class="truncate text-sm text-muted-foreground">{{ user.email }}</p>
                </div>
            </div>

            <div class="divide-y">
                <div class="flex items-center justify-between px-6 py-4">
                    <span class="flex items-center gap-2 text-sm text-muted-foreground">
                        <Shield class="h-4 w-4" />
                        Peran
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                        :class="roleConfig[user.role].class">
                        <component :is="roleConfig[user.role].icon" class="h-3 w-3" />
                        {{ roleConfig[user.role].label }}
                    </span>
                </div>

                <div class="flex items-center justify-between px-6 py-4">
                    <span class="flex items-center gap-2 text-sm text-muted-foreground">
                        <Mail class="h-4 w-4" />
                        Status Email
                    </span>
                    <div class="flex items-center gap-2">
                        <span v-if="user.email_verified_at"
                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                            <BadgeCheck class="h-3 w-3" />
                            Terverifikasi
                        </span>
                        <template v-else>
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">
                                <BadgeAlert class="h-3 w-3" />
                                Belum Verifikasi
                            </span>
                            <button type="button" :disabled="resending" @click="resendVerification"
                                class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-50">
                                <Loader2 v-if="resending" class="h-3 w-3 animate-spin" />
                                <Send v-else class="h-3 w-3" />
                                Kirim Ulang
                            </button>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-between px-6 py-4">
                    <span class="flex items-center gap-2 text-sm text-muted-foreground">
                        <Calendar class="h-4 w-4" />
                        Terdaftar
                    </span>
                    <span class="text-sm">{{ formatDateTime(user.created_at) }}</span>
                </div>

                <div class="flex items-center justify-between px-6 py-4">
                    <span class="flex items-center gap-2 text-sm text-muted-foreground">
                        <Calendar class="h-4 w-4" />
                        Terakhir Diperbarui
                    </span>
                    <span class="text-sm">{{ formatDateTime(user.updated_at) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
