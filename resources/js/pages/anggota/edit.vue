<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Users,
    ArrowLeft,
    Save,
    User,
    Mail,
    Phone,
    Briefcase,
    Building2,
    MapPin,
    CalendarDays,
    Image as ImageIcon,
    X,
} from '@lucide/vue';
import { ref } from 'vue';
import { dashboard } from '@/routes';

interface Anggota {
    id: number;
    nama: string;
    foto: string | null;
    foto_url: string | null;
    email: string;
    no_telepon: string;
    jabatan: string;
    divisi: string;
    alamat: string | null;
    tanggal_bergabung: string;
    status: 'aktif' | 'tidak_aktif' | 'cuti';
}

const props = defineProps<{
    anggota: Anggota;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Anggota',
                href: '/anggota',
            },
            {
                title: 'Edit Anggota',
                href: '#',
            },
        ],
    },
});

interface AnggotaForm {
    nama: string;
    foto: File | null;
    email: string;
    no_telepon: string;
    jabatan: string;
    divisi: string;
    alamat: string;
    tanggal_bergabung: string;
    status: 'aktif' | 'tidak_aktif' | 'cuti';
}

function toDateInputValue(value: string | null): string {
    if (!value) {
        return '';
    }

    return value.slice(0, 10);
}

const form = useForm<AnggotaForm>({
    nama: props.anggota.nama,
    foto: null,
    email: props.anggota.email,
    no_telepon: props.anggota.no_telepon,
    jabatan: props.anggota.jabatan,
    divisi: props.anggota.divisi,
    alamat: props.anggota.alamat ?? '',
    tanggal_bergabung: toDateInputValue(props.anggota.tanggal_bergabung),
    status: props.anggota.status,
});

const preview = ref<string | null>(props.anggota.foto_url);
const fotoRemoved = ref(false);

function onFileChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.foto = file;
    fotoRemoved.value = false;

    if (preview.value && file) {
        URL.revokeObjectURL(preview.value);
    }

    preview.value = file ? URL.createObjectURL(file) : props.anggota.foto_url;
}

function removeImage() {
    form.foto = null;
    fotoRemoved.value = true;
    preview.value = null;

    const input = document.getElementById('foto') as HTMLInputElement | null;

    if (input) {
        input.value = '';
    }
}

function submit() {
    form.transform((data) => ({
        ...data,
        nama: data.nama,
        foto: data.foto,
        email: data.email,
        no_telepon: data.no_telepon,
        jabatan: data.jabatan,
        divisi: data.divisi,
        alamat: data.alamat,
        tanggal_bergabung: data.tanggal_bergabung || null,
        status: data.status,
        hapus_foto: fotoRemoved.value ? 1 : 0,
        _method: 'put',
    })).post(`/anggota/${props.anggota.id}`, {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>

    <Head :title="`Edit Anggota - ${anggota.nama}`" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Edit Anggota</h1>
                <p class="text-sm text-muted-foreground">
                    Perbarui data anggota di bawah ini.
                </p>
            </div>

            <Link :href="`/anggota/${anggota.id}`"
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
                    <Users class="h-4 w-4 text-blue-500" />
                </div>
                <div>
                    <p class="font-medium">Data Anggota</p>
                    <p class="text-xs text-muted-foreground">
                        Semua field wajib diisi kecuali dinyatakan opsional.
                    </p>
                </div>
            </div>

            <div class="grid gap-5 px-6 py-6 sm:grid-cols-2">
                <!-- Foto Anggota -->
                <div class="sm:col-span-2">
                    <label for="foto" class="mb-1.5 block text-sm font-medium">
                        Foto Anggota
                        <span class="text-muted-foreground">(opsional)</span>
                    </label>

                    <div v-if="preview" class="relative mb-3 inline-block">
                        <img :src="preview" alt="Preview foto anggota"
                            class="h-40 w-40 rounded-full border object-cover" />
                        <button type="button"
                            class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow-sm transition-colors hover:bg-rose-700"
                            title="Hapus foto" @click="removeImage">
                            <X class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    <label v-else for="foto"
                        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed bg-muted/20 px-6 py-8 text-center transition-colors hover:bg-muted/40"
                        :class="{ 'border-rose-500/50': form.errors.foto }">
                        <ImageIcon class="h-6 w-6 text-muted-foreground" />
                        <span class="text-sm text-muted-foreground">
                            Klik untuk memilih foto (JPG, PNG, maks 1MB)
                        </span>
                    </label>

                    <input id="foto" type="file" accept="image/png,image/jpeg,image/webp" class="hidden"
                        @change="onFileChange" />

                    <p v-if="form.errors.foto" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.foto }}
                    </p>
                </div>

                <!-- Nama -->
                <div class="sm:col-span-2">
                    <label for="nama" class="mb-1.5 block text-sm font-medium">
                        Nama Lengkap
                    </label>
                    <div class="relative">
                        <User
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="nama" v-model="form.nama" type="text" placeholder="Contoh: Ahmad Fauzi"
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

                <!-- Email -->
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium">
                        Email
                    </label>
                    <div class="relative">
                        <Mail
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="email" v-model="form.email" type="email" placeholder="email@domain.com"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.email,
                            }" />
                    </div>
                    <p v-if="form.errors.email" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- No Telepon -->
                <div>
                    <label for="no_telepon" class="mb-1.5 block text-sm font-medium">
                        No Telepon
                    </label>
                    <div class="relative">
                        <Phone
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="no_telepon" v-model="form.no_telepon" type="text" placeholder="0812-3456-7890"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.no_telepon,
                            }" />
                    </div>
                    <p v-if="form.errors.no_telepon" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.no_telepon }}
                    </p>
                </div>

                <!-- Jabatan -->
                <div>
                    <label for="jabatan" class="mb-1.5 block text-sm font-medium">
                        Jabatan
                    </label>
                    <div class="relative">
                        <Briefcase
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="jabatan" v-model="form.jabatan" type="text" placeholder="Contoh: Project Manager"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.jabatan,
                            }" />
                    </div>
                    <p v-if="form.errors.jabatan" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.jabatan }}
                    </p>
                </div>

                <!-- Divisi -->
                <div>
                    <label for="divisi" class="mb-1.5 block text-sm font-medium">
                        Divisi
                    </label>
                    <div class="relative">
                        <Building2
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="divisi" v-model="form.divisi" type="text" placeholder="Contoh: IT Development"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.divisi,
                            }" />
                    </div>
                    <p v-if="form.errors.divisi" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.divisi }}
                    </p>
                </div>

                <!-- Alamat -->
                <div class="sm:col-span-2">
                    <label for="alamat" class="mb-1.5 block text-sm font-medium">
                        Alamat
                        <span class="text-muted-foreground">(opsional)</span>
                    </label>
                    <div class="relative">
                        <MapPin class="pointer-events-none absolute top-3 left-3 h-4 w-4 text-muted-foreground" />
                        <textarea id="alamat" v-model="form.alamat" rows="3" placeholder="Alamat lengkap anggota"
                            class="w-full resize-none rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.alamat,
                            }" />
                    </div>
                    <p v-if="form.errors.alamat" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.alamat }}
                    </p>
                </div>

                <!-- Tanggal Bergabung -->
                <div>
                    <label for="tanggal_bergabung" class="mb-1.5 block text-sm font-medium">
                        Tanggal Bergabung
                    </label>
                    <div class="relative">
                        <CalendarDays
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="tanggal_bergabung" v-model="form.tanggal_bergabung" type="date"
                            class="w-full rounded-lg border bg-background py-2.5 pr-3 pl-9 text-sm transition-shadow outline-none focus:ring-2 focus:ring-ring"
                            :class="{
                                'border-rose-500/50 ring-2 ring-rose-500/40':
                                    form.errors.tanggal_bergabung,
                            }" />
                    </div>
                    <p v-if="form.errors.tanggal_bergabung" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.tanggal_bergabung }}
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
                        <option value="tidak_aktif">Tidak Aktif</option>
                        <option value="cuti">Cuti</option>
                    </select>
                    <p v-if="form.errors.status" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.status }}
                    </p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex flex-col-reverse gap-3 border-t bg-muted/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                <Link :href="`/anggota/${anggota.id}`"
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
