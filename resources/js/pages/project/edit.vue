<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    FolderKanban,
    ArrowLeft,
    Save,
    Building2,
    User,
    CalendarDays,
    FileText,
    Image as ImageIcon,
    Code2,
    Gauge,
    X,
} from '@lucide/vue';
import { ref, onBeforeUnmount } from 'vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Project',
                href: '/project',
            },
            {
                title: 'Edit Project',
                href: '/project/edit',
            },
        ],
    },
});

interface Project {
    id: number;
    nama: string;
    gambar: string | null;
    deskripsi: string | null;
    klien: string | null;
    pic: string | null;
    teknologi: string | null;
    status: 'aktif' | 'selesai' | 'ditunda' | 'dibatalkan';
    progress: number;
    mulai: string | null;
    selesai: string | null;
}

const props = defineProps<{
    project: Project;
}>();

interface ProjectForm {
    nama: string;
    gambar: File | null;
    deskripsi: string;
    klien: string;
    pic: string;
    teknologi: string;
    status: 'aktif' | 'selesai' | 'ditunda' | 'dibatalkan';

    // Hindari konflik dengan useForm().progress
    project_progress: number;

    mulai: string;
    selesai: string;
    hapus_gambar: boolean;
}

function toDateInput(value: string | null): string {
    return value ? value.slice(0, 10) : '';
}

const form = useForm<ProjectForm>({
    nama: props.project.nama ?? '',
    gambar: null,
    deskripsi: props.project.deskripsi ?? '',
    klien: props.project.klien ?? '',
    pic: props.project.pic ?? '',
    teknologi: props.project.teknologi ?? '',
    status: props.project.status ?? 'aktif',

    project_progress: props.project.progress ?? 0,

    mulai: toDateInput(props.project.mulai),
    selesai: toDateInput(props.project.selesai),
    hapus_gambar: false,
});

const preview = ref<string | null>(props.project.gambar ?? null);

function isBlobPreview(url: string | null): boolean {
    return !!url && url.startsWith('blob:');
}

function onFileChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;

    form.gambar = file;

    if (isBlobPreview(preview.value) && preview.value !== null) {
        URL.revokeObjectURL(preview.value);
    }

    preview.value = file ? URL.createObjectURL(file) : props.project.gambar;

    if (file) {
        form.hapus_gambar = false;
    }
}

function removeImage() {
    const hadServerImage = !isBlobPreview(preview.value) && !!preview.value;

    form.gambar = null;

    if (isBlobPreview(preview.value) && preview.value) {
        URL.revokeObjectURL(preview.value);
    }

    preview.value = null;

    if (hadServerImage) {
        form.hapus_gambar = true;
    }

    const input = document.getElementById('gambar') as HTMLInputElement | null;

    if (input) {
        input.value = '';
    }
}

onBeforeUnmount(() => {
    if (isBlobPreview(preview.value) && preview.value !== null) {
        URL.revokeObjectURL(preview.value);
    }
});

function submit() {
    form
        .transform((data) => ({
            nama: data.nama,
            gambar: data.gambar,
            deskripsi: data.deskripsi,
            klien: data.klien,
            pic: data.pic,
            teknologi: data.teknologi,
            status: data.status,
            progress: data.project_progress,
            mulai: data.mulai,
            selesai: data.selesai,
            hapus_gambar: data.hapus_gambar,
            _method: 'put',
        }))
        .post(`/project/${props.project.id}`, {
            preserveScroll: true,
            forceFormData: true,
        });
}
</script>

<template>

    <Head title="Edit Project" />

    <div class="container mx-auto max-w-4xl py-6">
        <div class="mb-6 flex items-center gap-4">
            <Link href="/project"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Kembali
            </Link>
            <h1 class="text-2xl font-bold">Edit Project</h1>
        </div>

        <!-- Form Card -->
        <form class="overflow-hidden rounded-xl border bg-background shadow-sm" @submit.prevent="submit"
            enctype="multipart/form-data">
            <div class="flex items-center gap-3 border-b bg-muted/40 px-6 py-4">
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10">
                    <FolderKanban class="h-4 w-4 text-blue-500" />
                </div>
                <div>
                    <p class="font-medium">Informasi Project</p>
                    <p class="text-xs text-muted-foreground">
                        Semua field wajib diisi kecuali dinyatakan opsional.
                    </p>
                </div>
            </div>

            <div class="grid gap-5 px-6 py-6 sm:grid-cols-2">
                <!-- Gambar Project -->
                <div class="sm:col-span-2">
                    <label for="gambar" class="mb-1.5 block text-sm font-medium">
                        Gambar Project <span class="text-muted-foreground">(opsional)</span>
                    </label>

                    <div v-if="preview" class="relative mb-3 inline-block">
                        <img :src="preview" alt="Preview gambar project"
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
                            Klik untuk memilih gambar (JPG, PNG, WEBP, maks 2MB)
                        </span>
                    </label>

                    <input id="gambar" type="file" accept="image/png,image/jpeg,image/webp" class="hidden"
                        @change="onFileChange" />

                    <p v-if="form.errors.gambar" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.gambar }}
                    </p>
                </div>

                <!-- Nama Project -->
                <div class="sm:col-span-2">
                    <label for="nama" class="mb-1.5 block text-sm font-medium">
                        Nama Project
                    </label>
                    <div class="relative">
                        <FolderKanban
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="nama" v-model="form.nama" type="text"
                            placeholder="Contoh: Pengembangan Sistem Informasi"
                            class="w-full rounded-lg border bg-background py-2.5 pl-9 pr-3 text-sm outline-none transition-shadow placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{ 'ring-2 ring-rose-500/40 border-rose-500/50': form.errors.nama }" />
                    </div>
                    <p v-if="form.errors.nama" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.nama }}
                    </p>
                </div>

                <!-- Klien -->
                <div>
                    <label for="klien" class="mb-1.5 block text-sm font-medium">
                        Klien / Instansi
                    </label>
                    <div class="relative">
                        <Building2
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="klien" v-model="form.klien" type="text" placeholder="Nama klien atau instansi"
                            class="w-full rounded-lg border bg-background py-2.5 pl-9 pr-3 text-sm outline-none transition-shadow placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{ 'ring-2 ring-rose-500/40 border-rose-500/50': form.errors.klien }" />
                    </div>
                    <p v-if="form.errors.klien" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.klien }}
                    </p>
                </div>

                <!-- PIC -->
                <div>
                    <label for="pic" class="mb-1.5 block text-sm font-medium">
                        PIC (Penanggung Jawab)
                    </label>
                    <div class="relative">
                        <User
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="pic" v-model="form.pic" type="text" placeholder="Nama PIC"
                            class="w-full rounded-lg border bg-background py-2.5 pl-9 pr-3 text-sm outline-none transition-shadow placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{ 'ring-2 ring-rose-500/40 border-rose-500/50': form.errors.pic }" />
                    </div>
                    <p v-if="form.errors.pic" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.pic }}
                    </p>
                </div>

                <!-- Teknologi -->
                <div class="sm:col-span-2">
                    <label for="teknologi" class="mb-1.5 block text-sm font-medium">
                        Teknologi <span class="text-muted-foreground">(opsional, pisahkan dengan koma)</span>
                    </label>
                    <div class="relative">
                        <Code2
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="teknologi" v-model="form.teknologi" type="text"
                            placeholder="Contoh: Laravel, Vue.js, MySQL"
                            class="w-full rounded-lg border bg-background py-2.5 pl-9 pr-3 text-sm outline-none transition-shadow placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{ 'ring-2 ring-rose-500/40 border-rose-500/50': form.errors.teknologi }" />
                    </div>
                    <p v-if="form.errors.teknologi" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.teknologi }}
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium">
                        Status
                    </label>
                    <select id="status" v-model="form.status"
                        class="w-full appearance-none rounded-lg border bg-background px-3 py-2.5 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                        :class="{ 'ring-2 ring-rose-500/40 border-rose-500/50': form.errors.status }">
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="ditunda">Ditunda</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                    <p v-if="form.errors.status" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.status }}
                    </p>
                </div>

                <!-- Progress -->
                <div>
                    <label for="project_progress" class="mb-1.5 flex items-center justify-between text-sm font-medium">
                        <span class="flex items-center gap-1.5">
                            <Gauge class="h-4 w-4 text-muted-foreground" />
                            Progress
                        </span>

                        <span class="text-muted-foreground">
                            {{ form.project_progress }}%
                        </span>
                    </label>

                    <input id="project_progress" v-model.number="form.project_progress" type="range" min="0" max="100"
                        step="5" class="w-full accent-primary" />

                    <p v-if="form.errors.project_progress" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.project_progress }}
                    </p>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label for="mulai" class="mb-1.5 block text-sm font-medium">
                        Tanggal Mulai
                    </label>
                    <div class="relative">
                        <CalendarDays
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="mulai" v-model="form.mulai" type="date"
                            class="w-full rounded-lg border bg-background py-2.5 pl-9 pr-3 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'ring-2 ring-rose-500/40 border-rose-500/50': form.errors.mulai }" />
                    </div>
                    <p v-if="form.errors.mulai" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.mulai }}
                    </p>
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label for="selesai" class="mb-1.5 block text-sm font-medium">
                        Tanggal Selesai <span class="text-muted-foreground">(opsional)</span>
                    </label>
                    <div class="relative">
                        <CalendarDays
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <input id="selesai" v-model="form.selesai" type="date"
                            class="w-full rounded-lg border bg-background py-2.5 pl-9 pr-3 text-sm outline-none transition-shadow focus:ring-2 focus:ring-ring"
                            :class="{ 'ring-2 ring-rose-500/40 border-rose-500/50': form.errors.selesai }" />
                    </div>
                    <p v-if="form.errors.selesai" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.selesai }}
                    </p>
                </div>

                <!-- Deskripsi -->
                <div class="sm:col-span-2">
                    <label for="deskripsi" class="mb-1.5 block text-sm font-medium">
                        Deskripsi <span class="text-muted-foreground">(opsional)</span>
                    </label>
                    <div class="relative">
                        <FileText class="pointer-events-none absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                        <textarea id="deskripsi" v-model="form.deskripsi" rows="4"
                            placeholder="Catatan atau ringkasan singkat mengenai project"
                            class="w-full resize-none rounded-lg border bg-background py-2.5 pl-9 pr-3 text-sm outline-none transition-shadow placeholder:text-muted-foreground focus:ring-2 focus:ring-ring"
                            :class="{ 'ring-2 ring-rose-500/40 border-rose-500/50': form.errors.deskripsi }" />
                    </div>
                    <p v-if="form.errors.deskripsi" class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">
                        {{ form.errors.deskripsi }}
                    </p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex flex-col-reverse gap-3 border-t bg-muted/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                <Link href="/project"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors hover:bg-muted">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-60">
                    <Save class="h-4 w-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Update Project' }}
                </button>
            </div>
        </form>
    </div>
</template>
