<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';

interface Mahasiswa {
    id: number;
    name: string;
    email: string;
    username: string;
    is_active: boolean;
    profile_mahasiswa?: {
        nim: string;
        angkatan: number;
        prodi: {
            id: number;
            nama: string;
            kode: string;
        };
    };
}

interface Prodi {
    id: number;
    nama: string;
    kode: string;
}

interface Props {
    mahasiswa: {
        data: Mahasiswa[];
        links: any;
    };
    prodis: Prodi[];
    filters: {
        search?: string;
        prodi_id?: string;
        angkatan?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Mahasiswa', href: '/admin/mahasiswa' },
];

const columns = [
    { key: 'name', label: 'Nama' },
    { key: 'email', label: 'Email' },
    { key: 'profile_mahasiswa.nim', label: 'NIM' },
    { key: 'profile_mahasiswa.prodi.nama', label: 'Prodi' },
    { key: 'profile_mahasiswa.angkatan', label: 'Angkatan' },
    { key: 'is_active', label: 'Status' },
];

const getStatusBadge = (isActive: boolean) => {
    return isActive
        ? 'bg-green-100 text-green-800 dark:bg-green-900'
        : 'bg-red-100 text-red-800 dark:bg-red-900';
};

const filterOptions = [
    {
        key: 'prodi_id',
        label: 'Program Studi',
        type: 'select' as const,
        placeholder: 'Semua Prodi',
        options: props.prodis.map((p) => ({ label: p.nama, value: p.id })),
    },
    {
        key: 'angkatan',
        label: 'Angkatan',
        type: 'text' as const,
        placeholder: 'Cari angkatan...',
    },
    {
        key: 'status',
        label: 'Status',
        type: 'select' as const,
        placeholder: 'Semua Status',
        options: [
            { label: 'Aktif', value: 'active' },
            { label: 'Tidak Aktif', value: 'inactive' },
        ],
    },
];
</script>

<template>
    <Head title="Manajemen Mahasiswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-bold">Manajemen Mahasiswa</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Kelola data semua mahasiswa dalam sistem
                    </p>
                </div>
                <Link href="/admin/mahasiswa/create" as="button">
                    <Button>+ Tambah Mahasiswa</Button>
                </Link>
            </div>

            <!-- Filter & Search -->
            <AdminFilterBar
                :filters="filterOptions"
                :current-filters="filters"
                search-placeholder="Cari nama, email, NIM..."
            />

            <!-- Data Table -->
            <AdminDataTable
                title=""
                :columns="columns"
                :data="mahasiswa.data"
                :links="mahasiswa.links"
                bulk-delete-route="/admin/mahasiswa/bulk-delete"
                edit-route="/admin/mahasiswa"
                delete-route="/admin/mahasiswa"
            >
                <template #cell-is_active="{ item }">
                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getStatusBadge(item.is_active)]">
                        {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </template>
            </AdminDataTable>
        </div>
    </AppLayout>
</template>
