<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';

interface Dosen {
    id: number;
    name: string;
    email: string;
    username: string;
    is_active: boolean;
    profile_dosen?: {
        nidn: string;
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
    dosen: {
        data: Dosen[];
        links: any;
    };
    prodis: Prodi[];
    filters: {
        search?: string;
        prodi_id?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Dosen', href: '/admin/dosen' },
];

const columns = [
    { key: 'name', label: 'Nama' },
    { key: 'email', label: 'Email' },
    { key: 'profile_dosen.nidn', label: 'NIDN' },
    { key: 'profile_dosen.prodi.nama', label: 'Prodi' },
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
    <Head title="Manajemen Dosen" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold">Manajemen Dosen</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Kelola data semua dosen dalam sistem
                </p>
            </div>

            <!-- Filter & Search -->
            <AdminFilterBar
                :filters="filterOptions"
                :current-filters="filters"
                search-placeholder="Cari nama, email, NIDN..."
            />

            <!-- Data Table -->
            <AdminDataTable
                title=""
                :columns="columns"
                :data="dosen.data"
                :links="dosen.links"
                bulk-delete-route="/admin/dosen/bulk-delete"
                edit-route="/admin/dosen"
                delete-route="/admin/dosen"
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
