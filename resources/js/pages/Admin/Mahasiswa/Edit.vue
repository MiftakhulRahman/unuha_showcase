<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

interface Prodi {
    id: number;
    nama: string;
    kode: string;
}

interface Mahasiswa {
    id: number;
    name: string;
    email: string;
    username: string;
    is_active: boolean;
    profile_mahasiswa: {
        nim: string;
        prodi_id: number;
        angkatan: number;
        semester: number;
    };
}

interface Props {
    mahasiswa: Mahasiswa;
    prodis: Prodi[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Mahasiswa', href: '/admin/mahasiswa' },
    { title: `Edit ${props.mahasiswa.name}`, href: `/admin/mahasiswa/${props.mahasiswa.id}/edit` },
];

const form = useForm({
    name: props.mahasiswa.name,
    email: props.mahasiswa.email,
    username: props.mahasiswa.username,
    is_active: props.mahasiswa.is_active,
    profile: {
        nim: props.mahasiswa.profile_mahasiswa.nim,
        prodi_id: props.mahasiswa.profile_mahasiswa.prodi_id,
        angkatan: props.mahasiswa.profile_mahasiswa.angkatan,
        semester: props.mahasiswa.profile_mahasiswa.semester,
    },
});

const submit = () => {
    form.put(`/admin/mahasiswa/${props.mahasiswa.id}`, {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Edit Mahasiswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold">Edit Mahasiswa</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Perbarui informasi mahasiswa
                </p>
            </div>

            <!-- Form -->
            <div class="max-w-2xl rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Info Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Informasi Akun</h3>
                        <div class="space-y-4">
                            <!-- Name -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Nama Lengkap *</label>
                                <Input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Masukkan nama lengkap"
                                    :class="{ 'border-red-500': form.errors.name }"
                                />
                                <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Email *</label>
                                <Input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="Masukkan email"
                                    :class="{ 'border-red-500': form.errors.email }"
                                />
                                <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
                            </div>

                            <!-- Username -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Username *</label>
                                <Input
                                    v-model="form.username"
                                    type="text"
                                    placeholder="Masukkan username"
                                    :class="{ 'border-red-500': form.errors.username }"
                                />
                                <p v-if="form.errors.username" class="text-sm text-red-600">{{ form.errors.username }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Data Mahasiswa</h3>
                        <div class="space-y-4">
                            <!-- NIM -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">NIM *</label>
                                <Input
                                    v-model="form.profile.nim"
                                    type="text"
                                    placeholder="Masukkan NIM"
                                    :class="{ 'border-red-500': form.errors['profile.nim'] }"
                                />
                                <p v-if="form.errors['profile.nim']" class="text-sm text-red-600">{{ form.errors['profile.nim'] }}</p>
                            </div>

                            <!-- Prodi -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Program Studi *</label>
                                <select
                                    v-model.number="form.profile.prodi_id"
                                    class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                                    :class="{ 'border-red-500': form.errors['profile.prodi_id'] }"
                                >
                                    <option value="">Pilih Program Studi</option>
                                    <option v-for="prodi in prodis" :key="prodi.id" :value="prodi.id">
                                        {{ prodi.nama }} ({{ prodi.kode }})
                                    </option>
                                </select>
                                <p v-if="form.errors['profile.prodi_id']" class="text-sm text-red-600">{{ form.errors['profile.prodi_id'] }}</p>
                            </div>

                            <!-- Angkatan -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Angkatan *</label>
                                <Input
                                    v-model.number="form.profile.angkatan"
                                    type="number"
                                    placeholder="Masukkan angkatan"
                                    :class="{ 'border-red-500': form.errors['profile.angkatan'] }"
                                />
                                <p v-if="form.errors['profile.angkatan']" class="text-sm text-red-600">{{ form.errors['profile.angkatan'] }}</p>
                            </div>

                            <!-- Semester -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Semester *</label>
                                <select
                                    v-model.number="form.profile.semester"
                                    class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                                    :class="{ 'border-red-500': form.errors['profile.semester'] }"
                                >
                                    <option value="">Pilih Semester</option>
                                    <option v-for="i in 8" :key="i" :value="i">
                                        Semester {{ i }}
                                    </option>
                                </select>
                                <p v-if="form.errors['profile.semester']" class="text-sm text-red-600">{{ form.errors['profile.semester'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Status</h3>
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-gray-300"
                            />
                            <span class="text-sm font-medium">Aktif</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </Button>
                        <Button
                            as="a"
                            href="/admin/mahasiswa"
                            variant="outline"
                        >
                            Batal
                        </Button>
                    </div>
                </form>
            </div>

            <!-- Delete Section -->
            <div class="max-w-2xl rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-900 dark:bg-red-950">
                <h3 class="text-lg font-semibold text-red-900 dark:text-red-100">Zona Berbahaya</h3>
                <p class="mt-2 text-sm text-red-800 dark:text-red-200">
                    Hapus mahasiswa ini secara permanen dari sistem
                </p>
                <Link :href="`/admin/mahasiswa/${mahasiswa.id}`" method="delete" as="button">
                    <Button variant="destructive" class="mt-4">
                        Hapus Mahasiswa
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
