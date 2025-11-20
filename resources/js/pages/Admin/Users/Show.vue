<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    username: string;
    role: string;
    is_active: boolean;
    registration_completed: boolean;
    avatar?: string;
    bio?: string;
    created_at: string;
    updated_at: string;
    profile_mahasiswa?: {
        nim: string;
        angkatan: number;
        prodi?: {
            nama: string;
        };
    };
    profile_dosen?: {
        nidn: string;
        jabatan: string;
        prodi?: {
            nama: string;
        };
    };
}

interface Props {
    user: User;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Pengguna', href: '/admin/users' },
    { title: props.user.name, href: `/admin/users/${props.user.id}` },
];

const getRoleBadge = (role: string) => {
    const colors: { [key: string]: string } = {
        superadmin: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
        dosen: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100',
        mahasiswa: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100',
    };
    return colors[role] || 'bg-gray-100 text-gray-800';
};

const getStatusBadge = (isActive: boolean) => {
    return isActive
        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100';
};
</script>

<template>
    <Head :title="`Lihat - ${user.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/admin/users" as="button">
                    <Button variant="outline" size="sm">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-3xl font-bold">{{ user.name }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Detail profil pengguna
                    </p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Informasi Dasar</h3>
                        <div class="mt-4 space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama</label>
                                    <p class="mt-1 text-sm">{{ user.name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Username</label>
                                    <p class="mt-1 text-sm">{{ user.username }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</label>
                                    <p class="mt-1 text-sm">{{ user.email }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Role</label>
                                    <span :class="['mt-1 inline-block rounded-full px-3 py-1 text-xs font-semibold', getRoleBadge(user.role)]">
                                        {{ user.role }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Specific Info -->
                    <div v-if="user.profile_mahasiswa" class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Data Mahasiswa</h3>
                        <div class="mt-4 space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">NIM</label>
                                    <p class="mt-1 text-sm">{{ user.profile_mahasiswa.nim }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Angkatan</label>
                                    <p class="mt-1 text-sm">{{ user.profile_mahasiswa.angkatan }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Program Studi</label>
                                    <p class="mt-1 text-sm">{{ user.profile_mahasiswa.prodi?.nama }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="user.profile_dosen" class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Data Dosen</h3>
                        <div class="mt-4 space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">NIDN</label>
                                    <p class="mt-1 text-sm">{{ user.profile_dosen.nidn }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Jabatan</label>
                                    <p class="mt-1 text-sm">{{ user.profile_dosen.jabatan }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Program Studi</label>
                                    <p class="mt-1 text-sm">{{ user.profile_dosen.prodi?.nama }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Status</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Status Akun</label>
                                <div class="mt-2">
                                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getStatusBadge(user.is_active)]">
                                        {{ user.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Status Registrasi</label>
                                <div class="mt-2">
                                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', user.registration_completed ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100']">
                                        {{ user.registration_completed ? 'Selesai' : 'Belum Selesai' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Informasi Sistem</h3>
                        <div class="mt-4 space-y-4 text-sm">
                            <div>
                                <label class="font-medium text-gray-600 dark:text-gray-400">Dibuat</label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ new Date(user.created_at).toLocaleString('id-ID') }}
                                </p>
                            </div>
                            <div>
                                <label class="font-medium text-gray-600 dark:text-gray-400">Diperbarui</label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ new Date(user.updated_at).toLocaleString('id-ID') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2">
                        <Link :href="`/admin/users/${user.id}/edit`" as="button">
                            <Button class="w-full">Edit</Button>
                        </Link>
                        <Link href="/admin/users" as="button">
                            <Button variant="outline" class="w-full">Kembali</Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
