<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';

interface Prodi {
    id: number;
    nama: string;
    kode: string;
    is_active: boolean;
}

interface Props {
    prodis: {
        data: Prodi[];
        links: any;
    };
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Program Studi', href: '/admin/prodis' },
];

const columns = [
    { key: 'nama', label: 'Nama Program Studi' },
    { key: 'kode', label: 'Kode' },
    { key: 'is_active', label: 'Status' },
];

const getStatusBadge = (isActive: boolean) => {
    return isActive
        ? 'bg-green-100 text-green-800 dark:bg-green-900'
        : 'bg-red-100 text-red-800 dark:bg-red-900';
};

const filterOptions = [
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
    <Head title="Manajemen Program Studi" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-bold">Manajemen Program Studi</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Kelola semua program studi dalam sistem
                    </p>
                </div>
                <Link href="/admin/prodis/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Program Studi
                    </Button>
                </Link>
            </div>

            <!-- Filter & Search -->
            <AdminFilterBar
                :filters="filterOptions"
                :current-filters="filters"
                search-placeholder="Cari nama atau kode program studi..."
            />

            <!-- Data Table -->
            <AdminDataTable
                title=""
                :columns="columns"
                :data="prodis.data"
                :links="prodis.links"
                bulk-delete-route="/admin/prodis/bulk-delete"
                edit-route="/admin/prodis"
                delete-route="/admin/prodis"
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
