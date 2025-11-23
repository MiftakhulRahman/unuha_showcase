<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

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
        meta?: {
            total: number;
        };
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kategori', href: '/admin/kategoris' },
];

const columns = [
    { field: 'nama', header: 'Nama Kategori', sortable: true, filterType: 'text' as const },
    { field: 'slug', header: 'Slug', sortable: true, filterType: 'text' as const },
    { 
        field: 'is_active', 
        header: 'Status', 
        sortable: true, 
        filterType: 'boolean' as const,
        filterOptions: [
            { label: 'Aktif', value: true },
            { label: 'Tidak Aktif', value: false },
        ]
    },
];

const getStatusSeverity = (isActive: boolean) => {
    return isActive ? 'success' : 'danger';
};
</script>

<template>
    <Head title="Manajemen Kategori" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="kategoris.data"
                    :totalRecords="kategoris.meta?.total"
                    title="Manajemen Kategori"
                    description="Kelola semua kategori project dalam sistem"
                    create-route="/admin/kategoris/create"
                    edit-route="/admin/kategoris"
                    delete-route="/admin/kategoris"
                    bulk-delete-route="/admin/kategoris/bulk-delete"
                    :exportable="true"
                >
                    <template #cell-is_active="{ item }">
                        <Tag 
                            :value="item.is_active ? 'Aktif' : 'Tidak Aktif'" 
                            :severity="getStatusSeverity(item.is_active)" 
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>
                </PrimeDataTable>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Nuclear option for Badge Font Weight */
:deep(.custom-badge),
:deep(.custom-badge .p-tag-value),
:deep(.p-tag.custom-badge),
:deep(.p-tag.custom-badge span) {
    font-weight: 500 !important;
    font-family: 'Outfit', sans-serif !important;
}
</style>
