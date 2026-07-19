<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Wrench,
    ArrowLeft,
    Save,
    Tag,
    FileText,
    Clock,
    DollarSign,
    Image as ImageIcon,
    X,
    ListChecks,
} from '@lucide/vue';
import { ref } from 'vue';
import { dashboard } from '@/routes';

interface Layanan {
    id: number;
    nama: string;
    gambar: string | null;
    gambar_url: string | null;
    kategori: string | null;
    deskripsi: string | null;
    syarat: string[] | null;
    estimasi_waktu: string | null;
    biaya: number | null;
    status: 'aktif' | 'nonaktif';
}

const props = defineProps<{
    layanan: Layanan;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Layanan',
                href: '/layanan',
            },
            {
                title: 'Edit Layanan',
                href: '/layanan',
            },
        ],
    },
});

interface LayananForm {
    nama: string;
    gambar: File | null;
    kategori: string;
    deskripsi: string;
    syarat: string[];
    estimasi_waktu: string;
    biaya: number | null;
    status: 'aktif' | 'nonaktif';
}

const form = useForm<LayananForm>({
    nama: props.layanan.nama ?? '',
    gambar: null,
    kategori: props.layanan.kategori ?? '',
    deskripsi: props.layanan.deskripsi ?? '',
    syarat: props.layanan.syarat ? [...props.layanan.syarat] : [],
    estimasi_waktu: props.layanan.estimasi_waktu ?? '',
    biaya: props.layanan.biaya ?? null,
    status: props.layanan.status ?? 'aktif',
});

// Preview diawal dari gambar yang sudah tersimpan di database (kalau ada)
const preview = ref<string | null>(props.layanan.gambar_url ?? null);

// Tandai apakah user memilih untuk menghapus gambar lama tanpa upload baru
const removeExistingImage = ref(false);

const syaratInput = ref('');

function tambahSyarat() {
    const value = syaratInput.value.trim();

    if (value && !form.syarat.includes(value)) {
        form.syarat.push(value);
    }

    syaratInput.value = '';
}

function hapusSyarat(index: number) {
    form.syarat.splice(index, 1);
}

function onFileChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.gambar = file;
    removeExistingImage.value = false;

    if (preview.value && preview.value.startsWith('blob:')) {
        URL.revokeObjectURL(preview.value);
    }

    preview.value = file ? URL.createObjectURL(file) : (props.layanan.gambar_url ?? null);
}

function removeImage() {
    form.gambar = null;
    removeExistingImage.value = true;

    if (preview.value && preview.value.startsWith('blob:')) {
        URL.revokeObjectURL(preview.value);
    }

    preview.value = null;

    const input = document.getElementById('gambar') as HTMLInputElement | null;

    if (input) {
        input.value = '';
    }
}

function submit() {
    form.transform((data) => ({
        nama: data.nama,
        gambar: data.gambar,
        hapus_gambar: removeExistingImage.value && !data.gambar,
        kategori: data.kategori,
        deskripsi: data.deskripsi,
        syarat: data.syarat,
        estimasi_waktu: data.estimasi_waktu || null,
        biaya: data.biaya,
        status: data.status,
    })).put(`/layanan/${props.layanan.id}`, {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>

    <Head title="Edit Layanan" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Edit Layanan</h1>

                <p class="text-sm text-muted-foreground">
                    Perbarui data layanan "{{ layanan.nama }}" di bawah ini.
                </p>
            </div>

            <Link href="/layanan"
                class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                <ArrowLeft class="h-4 w-4" />
                Kembali
            </Link>
        </div>

        <!-- Form Card -->
        <form class="overflow-hidden rounded-xl border bg-background shadow-sm" @submit.prevent="submit"
            enctype="multipart/form-data">
            <div class="flex items-center gap-3 border-b bg-muted/40 px-6 py-4">
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                    <Wrench class="h-4 w-4 text-blue-500" />
                </div>
                <div>
                    <p class="font-medium">Data Layanan</p>
                    <p class="text-xs text-muted-foreground">
                        Semua field wajib diisi kecuali dinyatakan opsional.
                    </p>
                </div>
            </div>

            <div class="grid gap-5 px-6 py-6 sm:grid-cols-2">
                <!-- Gambar Layanan -->
                <div class="sm:col-span-2">
                    <label for="gambar" class="mb-1.5 block text-sm font-medium">
                        Gambar Layanan
                        <span class="text-muted-foreground">(opsional)</span>
                    </label>

                    <div v-if="preview" class="relative mb-3 inline-block">
                        <img :src="preview" alt="Preview gambar layanan"
                            class="h-40 w-full max-w-sm rounded-lg border object-cover" />
                        <button type="button"
                            class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow-sm transition-colors hover:bg-rose-700"
                            title="Hapus gambar" @click="removeImage">
                            <X class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    <label v-else for="gambar"
                        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed bg-muted/20 px-6 py-8 text-center transition-colors hover:bg-muted/40"
                        :class="{ 'border-rose-500/50': form.errors.gambar }">
                        <ImageIcon class="h-6 w-6 text-muted-foreground" />
                        <span class="text-sm text-muted-foreground">
                            Klik untuk memilih gambar (JPG, PNG, maks 1MB)
                        </span>
                    </label>

                    <input id="gambar" type="file" accept="image/png,image/jpeg,image/webp" class="hidden"
                        @change="onFileChange" />

                    <p v-if="removeExistingImage && !form.gambar" class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                        Gambar lama akan dihapus setelah disimpan.
                    </p>

                    <p v-if="form.errors.gambar" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.gambar }}
                    </p>
                </div>

                <!-- Nama Layanan -->
                <div class="sm:col-span-2">
                    <label for="nama" class="mb-1.5 block text-sm font-medium">
                        Nama Layanan
                    </label>
                    <div class="relative">
                        <Wrench
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="nama" v-model="form.nama" type="text" placeholder="Contoh: Pengembangan Website"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.nama,
                            }" />
                    </div>
                    <p v-if="form.errors.nama" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.nama }}
                    </p>
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="mb-1.5 block text-sm font-medium">
                        Kategori
                    </label>
                    <div class="relative">
                        <Tag
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="kategori" v-model="form.kategori" type="text" placeholder="Contoh: Web Development"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.kategori,
                            }" />
                    </div>
                    <p v-if="form.errors.kategori" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.kategori }}
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium">
                        Status
                    </label>
                    <select id="status" v-model="form.status"
                        class="w-full appearance-none rounded-lg border bg-background px-3 py-2.5 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring"
                        :class="{
                            'border-rose-500/50 ring-2 ring-rose-500/40':
                                form.errors.status,
                        }">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                    <p v-if="form.errors.status" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.status }}
                    </p>
                </div>

                <!-- Biaya -->
                <div>
                    <label for="biaya" class="mb-1.5 block text-sm font-medium">
                        Biaya
                        <span class="text-muted-foreground">(opsional)</span>
                    </label>
                    <div class="relative">
                        <DollarSign
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="biaya" v-model="form.biaya" type="number" min="0" step="1000"
                            placeholder="Contoh: 1500000"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.biaya,
                            }" />
                    </div>
                    <p v-if="form.errors.biaya" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.biaya }}
                    </p>
                </div>

                <!-- Estimasi Waktu -->
                <div>
                    <label for="estimasi_waktu" class="mb-1.5 block text-sm font-medium">
                        Estimasi Waktu
                        <span class="text-muted-foreground">(opsional)</span>
                    </label>
                    <div class="relative">
                        <Clock
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="estimasi_waktu" v-model="form.estimasi_waktu" type="text"
                            placeholder="Contoh: 2-3 Minggu"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.estimasi_waktu,
                            }" />
                    </div>
                    <p v-if="form.errors.estimasi_waktu" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.estimasi_waktu }}
                    </p>
                </div>

                <!-- Deskripsi -->
                <div class="sm:col-span-2">
                    <label for="deskripsi" class="mb-1.5 block text-sm font-medium">
                        Deskripsi
                        <span class="text-muted-foreground">(opsional)</span>
                    </label>
                    <div class="relative">
                        <FileText class="pointer-events-none absolute top-3 left-3 h-4 w-4 text-muted-foreground" />
                        <textarea id="deskripsi" v-model="form.deskripsi" rows="4"
                            placeholder="Deskripsi lengkap tentang layanan"
                            class="w-full resize-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.deskripsi,
                            }" />
                    </div>
                    <p v-if="form.errors.deskripsi" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.deskripsi }}
                    </p>
                </div>

                <!-- Syarat -->
                <div class="sm:col-span-2">
                    <label for="syarat" class="mb-1.5 block text-sm font-medium">
                        Syarat & Ketentuan
                        <span class="text-muted-foreground">(opsional)</span>
                    </label>

                    <div class="flex flex-wrap items-center gap-2 rounded-lg border bg-background px-3 py-2.5 transition-shadow focus-within:ring-2 focus-within:ring-ring"
                        :class="{
                            'border-rose-500/50 ring-2 ring-rose-500/40': form.errors.syarat,
                        }">
                        <ListChecks class="h-4 w-4 shrink-0 text-muted-foreground" />

                        <span v-for="(item, i) in form.syarat" :key="i"
                            class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                            {{ item }}
                            <button type="button" @click="hapusSyarat(i)">
                                <X class="h-3 w-3" />
                            </button>
                        </span>

                        <input id="syarat" v-model="syaratInput" type="text" placeholder="Ketik lalu tekan Enter..."
                            class="min-w-[140px] flex-1 border-none bg-transparent text-sm outline-none placeholder:text-muted-foreground"
                            @keydown.enter.prevent="tambahSyarat" />
                    </div>

                    <p v-if="form.errors.syarat" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.syarat }}
                    </p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex flex-col-reverse gap-3 border-t bg-muted/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                <Link href="/layanan"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-60">
                    <Save class="h-4 w-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
            </div>
        </form>
    </div>
</template>
