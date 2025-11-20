<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';

interface Tool {
    id: number;
    nama: string;
    slug: string;
    icon?: string;
    is_active: boolean;
}

interface Props {
    tools: {
        data: Tool[];
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
    { title: 'Tools/Teknologi', href: '/admin/tools' },
];

const columns = [
    { key: 'nama', label: 'Nama Tool' },
    { key: 'slug', label: 'Slug' },
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
    <Head title="Manajemen Tools/Teknologi" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-bold">Manajemen Tools/Teknologi</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Kelola semua tools dan teknologi dalam sistem
                    </p>
                </div>
                <Link href="/admin/tools/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Tool
                    </Button>
                </Link>
            </div>

            <!-- Filter & Search -->
            <AdminFilterBar
                :filters="filterOptions"
                :current-filters="filters"
                search-placeholder="Cari nama atau slug tool..."
            />

            <!-- Data Table -->
            <AdminDataTable
                title=""
                :columns="columns"
                :data="tools.data"
                :links="tools.links"
                bulk-delete-route="/admin/tools/bulk-delete"
                edit-route="/admin/tools"
                delete-route="/admin/tools"
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
