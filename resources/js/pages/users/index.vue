<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    Search,
    Pencil,
    Trash2,
    Eye,
    ChevronLeft,
    ChevronRight,
    FolderX,
    SlidersHorizontal,
    Shield,
    Mail,
    Calendar,
    Send,
    Loader2,
} from '@lucide/vue';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';
import { dashboard } from '@/routes';

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
        ],
    },
});

interface User {
    id: number;
    name: string;
    email: string;
    role: 'academy';
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

// Konfigurasi Role
const roleConfig: Record<User['role'], { label: string; class: string; icon: any }> = {
    academy: {
        label: 'Pengurus',
        class: 'bg-purple-50 text-purple-700 ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/20',
        icon: Shield,
    },
};

interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    users?: Paginated<User>;
    filters?: {
        search?: string;
        role?: string;
        status?: string;
    };
}>();

const search = ref(props.filters?.search ?? '');
const role = ref(props.filters?.role ?? '');
const status = ref(props.filters?.status ?? '');

// Konfigurasi Status Verifikasi Email
const statusConfig = {
    verified: {
        label: 'Terverifikasi',
        class: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
    },
    unverified: {
        label: 'Belum Verifikasi',
        class: 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
    },
};

// Format tanggal
function formatDate(date: string | null): string {
    if (!date) {
        return '—';
    }

    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

let debounceTimer: ReturnType<typeof setTimeout>;

function applyFilters() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            '/users',
            {
                search: search.value || undefined,
                role: role.value || undefined,
                status: status.value || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 350);
}

watch([search, role, status], applyFilters);

function goToPage(page: number) {
    router.get(
        '/users',
        {
            search: search.value || undefined,
            role: role.value || undefined,
            status: status.value || undefined,
            page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

const deletingId = ref<number | null>(null);
const resendingId = ref<number | null>(null);

const resendVerification = (user: User) => {
    resendingId.value = user.id;
    router.post(`/users/${user.id}/resend-verification`, {}, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            resendingId.value = null;
        },
    });
};

const hapusUser = (user: User) => {
    Swal.fire({
        title: 'Hapus Pengguna?',
        html: `Pengguna <b>"${user.name}"</b> akan dihapus secara permanen.<br>Tindakan ini tidak dapat dibatalkan.`,
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
            deletingId.value = user.id;
            router.delete(`/users/${user.id}`, {
                preserveScroll: true,
                onFinish: () => {
                    deletingId.value = null;
                },
            });
        }
    });
};

// Helper untuk mendapatkan inisial nama
function getInitials(name: string): string {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

// Warna avatar berdasarkan nama
function getAvatarColor(name: string): string {
    const colors = [
        'bg-red-500',
        'bg-orange-500',
        'bg-amber-500',
        'bg-yellow-500',
        'bg-lime-500',
        'bg-green-500',
        'bg-emerald-500',
        'bg-teal-500',
        'bg-cyan-500',
        'bg-sky-500',
        'bg-blue-500',
        'bg-indigo-500',
        'bg-violet-500',
        'bg-purple-500',
        'bg-fuchsia-500',
        'bg-pink-500',
        'bg-rose-500',
    ];

    let hash = 0;

    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    return colors[Math.abs(hash) % colors.length];
}
</script>

<template>

    <Head title="Pengguna" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Data Pengguna</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola seluruh pengguna yang terdaftar di sistem.
                </p>
            </div>

            <Link href="/users/create"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90">
                <Plus class="h-4 w-4" />
                Tambah Pengguna
            </Link>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col gap-3 rounded-xl border bg-background p-4 shadow-sm sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input v-model="search" type="text" placeholder="Cari nama atau email pengguna..."
                    class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm ring-offset-background transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring" />
            </div>

            <div class="relative sm:w-44">
                <SlidersHorizontal
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <select v-model="status"
                    class="w-full appearance-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring">
                    <option value="">Semua Status</option>
                    <option value="verified">Terverifikasi</option>
                    <option value="unverified">Belum Verifikasi</option>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden rounded-xl border bg-background shadow-sm">
            <div v-if="!users || users.data.length === 0"
                class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <FolderX class="h-6 w-6 text-muted-foreground" />
                </div>
                <div>
                    <p class="font-medium">Belum ada pengguna</p>
                    <p class="text-sm text-muted-foreground">
                        Pengguna yang ditambahkan akan muncul di sini.
                    </p>
                </div>
                <Link href="/users/create"
                    class="mt-2 inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition-colors hover:bg-muted">
                    <Plus class="h-4 w-4" />
                    Tambah Pengguna Pertama
                </Link>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b bg-muted/40 text-left text-xs tracking-wide text-muted-foreground uppercase">
                            <th class="px-6 py-3 font-medium">Pengguna</th>
                            <th class="px-6 py-3 font-medium">Email</th>
                            <th class="px-6 py-3 font-medium">Role</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Bergabung</th>
                            <th class="px-6 py-3 text-right font-medium">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="user in users.data" :key="user.id" class="transition-colors hover:bg-muted/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-white font-medium text-sm"
                                        :class="getAvatarColor(user.name)">
                                        {{ getInitials(user.name) }}
                                    </div>
                                    <span class="font-medium">{{ user.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <Mail class="h-3.5 w-3.5 text-muted-foreground" />
                                    <span class="text-muted-foreground">{{ user.email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                    :class="roleConfig[user.role].class">
                                    <component :is="roleConfig[user.role].icon" class="h-3 w-3" />
                                    {{ roleConfig[user.role].label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="user.email_verified_at ? statusConfig.verified.class : statusConfig.unverified.class">
                                        {{ user.email_verified_at ? statusConfig.verified.label :
                                            statusConfig.unverified.label }}
                                    </span>
                                    <button v-if="!user.email_verified_at" type="button"
                                        :disabled="resendingId === user.id"
                                        class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-amber-50 hover:text-amber-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-amber-500/10"
                                        title="Kirim ulang email verifikasi" @click="resendVerification(user)">
                                        <Loader2 v-if="resendingId === user.id" class="h-3.5 w-3.5 animate-spin" />
                                        <Send v-else class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <Calendar class="h-3.5 w-3.5" />
                                    <span>{{ formatDate(user.created_at) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="`/users/${user.id}`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Lihat detail">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <Link :href="`/users/${user.id}/edit`"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                        title="Edit pengguna">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button type="button" :disabled="deletingId === user.id"
                                        class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 disabled:pointer-events-none disabled:opacity-40 dark:hover:bg-rose-500/10"
                                        title="Hapus pengguna" @click="hapusUser(user)">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users && users.data.length > 0"
                class="flex flex-col gap-3 border-t px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-muted-foreground">
                    Menampilkan {{ users.from }}–{{ users.to }} dari
                    {{ users.total }} pengguna
                </p>

                <div class="flex items-center gap-2">
                    <button type="button" :disabled="users.current_page <= 1"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(users.current_page - 1)">
                        <ChevronLeft class="h-4 w-4" />
                        Sebelumnya
                    </button>

                    <span class="px-2 text-sm text-muted-foreground">
                        {{ users.current_page }} / {{ users.last_page }}
                    </span>

                    <button type="button" :disabled="users.current_page >= users.last_page"
                        class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-40"
                        @click="goToPage(users.current_page + 1)">
                        Selanjutnya
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
