<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';

interface Kategori {
    id: number;
    nama: string;
    slug: string;
    icon?: string;
    is_active: boolean;
}

interface Props {
    kategoris: {
        data: Kategori[];
        links: any;
    };
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kategori', href: '/admin/kategoris' },
];

const columns = [
    { key: 'nama', label: 'Nama Kategori' },
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
    <Head title="Manajemen Kategori" />
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-950 dark:to-gray-900">
            <div class="mx-auto max-w-7xl space-y-6 p-6">
                <!-- Breadcrumb -->
                <div class="flex items-center justify-between">
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </div>

                <!-- Header Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="space-y-1.5">
                            <h1 class="text-2xl font-bold tracking-tight">Manajemen Kategori</h1>
                            <p class="text-sm text-muted-foreground">
                                Kelola semua kategori project dalam sistem
                            </p>
                        </div>
                        <Link href="/admin/kategoris/create">
                            <Button size="default" class="gap-2">
                                <Plus class="h-4 w-4" />
                                Tambah Kategori
                            </Button>
                        </Link>
                    </div>

                    <!-- Filter & Search -->
                    <div class="mt-6">
                        <AdminFilterBar
                            :filters="filterOptions"
                            :current-filters="filters"
                            search-placeholder="Cari nama atau slug kategori..."
                        />
                    </div>
                </div>

                <!-- Data Table Card -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <AdminDataTable
                        title=""
                        :columns="columns"
                        :data="kategoris.data"
                        :links="kategoris.links"
                        bulk-delete-route="/admin/kategoris/bulk-delete"
                        edit-route="/admin/kategoris"
                        delete-route="/admin/kategoris"
                    >
                        <template #cell-is_active="{ item }">
                            <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getStatusBadge(item.is_active)]">
                                {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </template>
                    </AdminDataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
