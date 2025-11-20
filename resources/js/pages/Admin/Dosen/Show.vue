<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';

interface Dosen {
    id: number;
    name: string;
    email: string;
    username: string;
    is_active: boolean;
    registration_completed: boolean;
    created_at: string;
    updated_at: string;
    profile_dosen: {
        nidn: string;
        jabatan: string;
        bidang_keahlian: string;
        prodi?: {
            nama: string;
            kode: string;
        };
    };
}

interface Props {
    dosen: Dosen;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Dosen', href: '/admin/dosen' },
    { title: props.dosen.name, href: `/admin/dosen/${props.dosen.id}` },
];

const getStatusBadge = (isActive: boolean) => {
    return isActive
        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100';
};
</script>

<template>
    <Head :title="`Lihat - ${dosen.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/admin/dosen" as="button">
                    <Button variant="outline" size="sm">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-3xl font-bold">{{ dosen.name }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Detail profil dosen
                    </p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Informasi Akun</h3>
                        <div class="mt-4 space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama</label>
                                    <p class="mt-1 text-sm">{{ dosen.name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Username</label>
                                    <p class="mt-1 text-sm">{{ dosen.username }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</label>
                                    <p class="mt-1 text-sm">{{ dosen.email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dosen Profile Data -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Data Dosen</h3>
                        <div class="mt-4 space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">NIDN</label>
                                    <p class="mt-1 text-sm font-mono">{{ dosen.profile_dosen.nidn }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Program Studi</label>
                                    <p class="mt-1 text-sm">{{ dosen.profile_dosen.prodi?.nama }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Jabatan</label>
                                    <p class="mt-1 text-sm">{{ dosen.profile_dosen.jabatan || '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Bidang Keahlian</label>
                                    <p class="mt-1 text-sm">{{ dosen.profile_dosen.bidang_keahlian || '-' }}</p>
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
                                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getStatusBadge(dosen.is_active)]">
                                        {{ dosen.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Status Registrasi</label>
                                <div class="mt-2">
                                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', dosen.registration_completed ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100']">
                                        {{ dosen.registration_completed ? 'Selesai' : 'Belum Selesai' }}
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
                                    {{ new Date(dosen.created_at).toLocaleString('id-ID') }}
                                </p>
                            </div>
                            <div>
                                <label class="font-medium text-gray-600 dark:text-gray-400">Diperbarui</label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ new Date(dosen.updated_at).toLocaleString('id-ID') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2">
                        <Link :href="`/admin/dosen/${dosen.id}/edit`" as="button">
                            <Button class="w-full">Edit</Button>
                        </Link>
                        <Link href="/admin/dosen" as="button">
                            <Button variant="outline" class="w-full">Kembali</Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
