<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Save,
    User,
    Mail,
    Lock,
    Shield,
    Eye,
    EyeOff,
    Loader2,
} from '@lucide/vue';
import { ref } from 'vue';
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
            {
                title: 'Tambah Pengguna',
                href: '/users/create',
            },
        ],
    },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

function submit() {
    form.post('/users', {
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>

<template>

    <Head title="Tambah Pengguna" />

    <div class="mx-auto max-w-2xl space-y-6 p-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <Link href="/users"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold">Tambah Pengguna</h1>
                <p class="text-sm text-muted-foreground">
                    Buat akun baru untuk pengurus yang mengelola website.
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <form @submit.prevent="submit" class="space-y-6 rounded-xl border bg-background p-6 shadow-sm">
            <!-- Info role -->
            <div
                class="flex items-start gap-3 rounded-lg bg-purple-50 p-3 text-sm text-purple-700 ring-1 ring-inset ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/20">
                <Shield class="mt-0.5 h-4 w-4 shrink-0" />
                <span>
                    Akun ini akan otomatis diberi peran <b>Pengurus</b> dan bisa login untuk mengelola website.
                </span>
            </div>

            <!-- Nama -->
            <div class="space-y-1.5">
                <label for="name" class="text-sm font-medium">
                    Nama Lengkap
                </label>
                <div class="relative">
                    <User
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <input id="name" v-model="form.name" type="text" placeholder="Masukkan nama lengkap"
                        autocomplete="name"
                        class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                        :class="form.errors.name ? 'border-rose-400 focus:ring-rose-400' : ''" />
                </div>
                <p v-if="form.errors.name" class="text-xs text-rose-600 dark:text-rose-400">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-sm font-medium">
                    Email
                </label>
                <div class="relative">
                    <Mail
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <input id="email" v-model="form.email" type="email" placeholder="nama@contoh.com"
                        autocomplete="email"
                        class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                        :class="form.errors.email ? 'border-rose-400 focus:ring-rose-400' : ''" />
                </div>
                <p v-if="form.errors.email" class="text-xs text-rose-600 dark:text-rose-400">
                    {{ form.errors.email }}
                </p>
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="text-sm font-medium">
                    Kata Sandi
                </label>
                <div class="relative">
                    <Lock
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                        placeholder="Minimal 8 karakter" autocomplete="new-password"
                        class="w-full rounded-lg border bg-background py-2.5 pr-9 pl-9 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                        :class="form.errors.password ? 'border-rose-400 focus:ring-rose-400' : ''" />
                    <button type="button"
                        class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        @click="showPassword = !showPassword">
                        <EyeOff v-if="showPassword" class="h-4 w-4" />
                        <Eye v-else class="h-4 w-4" />
                    </button>
                </div>
                <p v-if="form.errors.password" class="text-xs text-rose-600 dark:text-rose-400">
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Konfirmasi Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="text-sm font-medium">
                    Konfirmasi Kata Sandi
                </label>
                <div class="relative">
                    <Lock
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <input id="password_confirmation" v-model="form.password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'" placeholder="Ulangi kata sandi"
                        autocomplete="new-password"
                        class="w-full rounded-lg border bg-background py-2.5 pr-9 pl-9 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                        :class="form.errors.password_confirmation ? 'border-rose-400 focus:ring-rose-400' : ''" />
                    <button type="button"
                        class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        @click="showPasswordConfirmation = !showPasswordConfirmation">
                        <EyeOff v-if="showPasswordConfirmation" class="h-4 w-4" />
                        <Eye v-else class="h-4 w-4" />
                    </button>
                </div>
                <p v-if="form.errors.password_confirmation" class="text-xs text-rose-600 dark:text-rose-400">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 border-t pt-4">
                <Link href="/users"
                    class="rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-60">
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                    <Save v-else class="h-4 w-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Pengguna' }}
                </button>
            </div>
        </form>
    </div>
</template>
