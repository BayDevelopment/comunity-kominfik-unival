<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => {
    const auth = page.props.auth as { user?: { name: string; email?: string } | null } | undefined;

    return auth?.user ?? null;
});
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-gray-100 bg-white/90 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <Link href="/" class="text-xl font-extrabold text-blue-600">
                KOMINFIK
            </Link>

            <div class="hidden items-center gap-7 text-sm font-medium text-gray-700 md:flex">
                <Link href="/" class="hover:text-blue-600">Beranda</Link>
                <Link href="/project" class="hover:text-blue-600">Project</Link>
                <Link href="/anggota" class="hover:text-blue-600">Anggota</Link>
                <Link href="/layanan" class="hover:text-blue-600">Layanan</Link>
                <Link href="/kerjasama" class="hover:text-blue-600">Kerjasama</Link>
            </div>

            <div class="flex items-center gap-3">
                <template v-if="user">
                    <Link
                        href="/dashboard"
                        class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100"
                    >
                        Dashboard
                    </Link>
                </template>

                <template v-else>
                    <Link
                        href="/login"
                        class="hidden text-sm font-semibold text-gray-700 hover:text-blue-600 sm:inline"
                    >
                        Login
                    </Link>

                    <Link
                        href="/join"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Join
                    </Link>
                </template>
            </div>
        </nav>
    </header>
</template>
