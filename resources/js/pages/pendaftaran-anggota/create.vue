<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    UserPlus,
    ArrowLeft,
    Loader2,
    Upload,
    FileText,
    X,
} from 'lucide-vue-next';
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
                title: 'Pendaftaran Anggota',
                href: '/pendaftaran-anggota',
            },
            {
                title: 'Tambah Pendaftar',
                href: '/pendaftaran-anggota/create',
            },
        ],
    },
});

const form = useForm({
    nama: '',
    nim_nis: '',
    asal_instansi: '',
    jenjang: '' as '' | 'mahasiswa' | 'sma' | 'smk',
    jurusan_prodi: '',
    angkatan: '',
    email: '',
    no_telepon: '',
    alamat: '',
    alasan_bergabung: '',
    file_cv: null as File | null,
    foto: null as File | null,
});

const fotoPreview = ref<string | null>(null);
const fotoInput = ref<HTMLInputElement | null>(null);
const cvInput = ref<HTMLInputElement | null>(null);

function onFotoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.foto = file;
    fotoPreview.value = file ? URL.createObjectURL(file) : null;
}

function onCvChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.file_cv = file;
}

function removeFoto() {
    form.foto = null;
    fotoPreview.value = null;

    if (fotoInput.value) {
        fotoInput.value.value = '';
    }
}

function removeCv() {
    form.file_cv = null;

    if (cvInput.value) {
        cvInput.value.value = '';
    }
}

function submit() {
    form.post('/pendaftaran-anggota', {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>

    <Head title="Tambah Pendaftar" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <Link href="/pendaftaran-anggota"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border transition-colors hover:bg-muted">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold">Tambah Pendaftar</h1>
                    <p class="text-sm text-muted-foreground">
                        Lengkapi data pendaftaran anggota baru.
                    </p>
                </div>
            </div>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <!-- Data Diri -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                        <UserPlus class="h-4 w-4 text-blue-500" />
                    </div>
                    <div>
                        <h2 class="font-semibold">Data Diri</h2>
                        <p class="text-xs text-muted-foreground">
                            Informasi identitas pendaftar
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Nama Lengkap <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.nama" type="text" placeholder="Masukkan nama lengkap"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.nama }" />
                        <p v-if="form.errors.nama" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">NIM / NIS <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.nim_nis" type="text" placeholder="Nomor induk"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.nim_nis }" />
                        <p v-if="form.errors.nim_nis" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.nim_nis }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Jenjang <span
                                class="text-rose-500">*</span></label>
                        <select v-model="form.jenjang"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.jenjang }">
                            <option value="" disabled>Pilih jenjang</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="sma">SMA</option>
                            <option value="smk">SMK</option>
                        </select>
                        <p v-if="form.errors.jenjang" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.jenjang }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Asal Instansi <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.asal_instansi" type="text" placeholder="Nama kampus / sekolah"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.asal_instansi }" />
                        <p v-if="form.errors.asal_instansi" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.asal_instansi }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Jurusan / Prodi</label>
                        <input v-model="form.jurusan_prodi" type="text" placeholder="Contoh: Teknik Informatika"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.jurusan_prodi }" />
                        <p v-if="form.errors.jurusan_prodi" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.jurusan_prodi }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Angkatan</label>
                        <input v-model="form.angkatan" type="text" placeholder="Contoh: 2023"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.angkatan }" />
                        <p v-if="form.errors.angkatan" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.angkatan }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-semibold">Kontak & Alamat</h2>
                    <p class="text-xs text-muted-foreground">
                        Informasi untuk menghubungi pendaftar
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Email <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.email" type="email" placeholder="nama@email.com"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.email }" />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium">No. Telepon <span
                                class="text-rose-500">*</span></label>
                        <input v-model="form.no_telepon" type="text" placeholder="08xxxxxxxxxx"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.no_telepon }" />
                        <p v-if="form.errors.no_telepon" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.no_telepon }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Alamat</label>
                        <textarea v-model="form.alamat" rows="3" placeholder="Alamat lengkap"
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.alamat }"></textarea>
                        <p v-if="form.errors.alamat" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.alamat }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium">Alasan Bergabung</label>
                        <textarea v-model="form.alasan_bergabung" rows="4"
                            placeholder="Ceritakan alasan ingin bergabung..."
                            class="w-full rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'border-rose-500': form.errors.alasan_bergabung }"></textarea>
                        <p v-if="form.errors.alasan_bergabung" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.alasan_bergabung }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Berkas -->
            <div class="rounded-xl border bg-background p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-semibold">Berkas Pendukung</h2>
                    <p class="text-xs text-muted-foreground">
                        Unggah foto dan CV (opsional)
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <!-- Foto -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Foto</label>
                        <div v-if="fotoPreview"
                            class="relative flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                            <img :src="fotoPreview" alt="Preview foto"
                                class="h-14 w-14 rounded-lg object-cover" />
                            <div class="flex-1 text-sm text-muted-foreground">
                                {{ form.foto?.name }}
                            </div>
                            <button type="button"
                                class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10"
                                @click="removeFoto">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <label v-else
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed px-4 py-6 text-center transition-colors hover:bg-muted/40">
                            <Upload class="h-5 w-5 text-muted-foreground" />
                            <span class="text-sm text-muted-foreground">Klik untuk unggah foto</span>
                            <input ref="fotoInput" type="file" accept="image/*" class="hidden"
                                @change="onFotoChange" />
                        </label>
                        <p v-if="form.errors.foto" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.foto }}
                        </p>
                    </div>

                    <!-- CV -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">File CV</label>
                        <div v-if="form.file_cv"
                            class="relative flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                <FileText class="h-5 w-5 text-blue-500" />
                            </div>
                            <div class="flex-1 truncate text-sm text-muted-foreground">
                                {{ form.file_cv.name }}
                            </div>
                            <button type="button"
                                class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10"
                                @click="removeCv">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <label v-else
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed px-4 py-6 text-center transition-colors hover:bg-muted/40">
                            <Upload class="h-5 w-5 text-muted-foreground" />
                            <span class="text-sm text-muted-foreground">Klik untuk unggah CV (PDF)</span>
                            <input ref="cvInput" type="file" accept=".pdf,.doc,.docx" class="hidden"
                                @change="onCvChange" />
                        </label>
                        <p v-if="form.errors.file_cv" class="mt-1 text-xs text-rose-500">
                            {{ form.errors.file_cv }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <Link href="/pendaftaran-anggota"
                    class="rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-60">
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Simpan Pendaftaran
                </button>
            </div>
        </form>
    </div>
</template>
