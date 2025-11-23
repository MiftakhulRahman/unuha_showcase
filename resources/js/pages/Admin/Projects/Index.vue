<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

interface Project {
    id: number;
    title: string;
    description: string;
    slug: string;
    status: string;
    user: { name: string };
    kategori: { nama: string };
}

interface Props {
    projects: {
        data: Project[];
        meta?: {
            total: number;
        };
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Project', href: '/admin/projects' },
];

const columns = [
    { field: 'title', header: 'Judul Project', sortable: true, filterType: 'text' as const },
    { field: 'user.name', header: 'Pembuat', sortable: true, filterType: 'text' as const },
    { field: 'kategori.nama', header: 'Kategori', sortable: true, filterType: 'text' as const },
    { 
        field: 'status', 
        header: 'Status', 
        sortable: true, 
        filterType: 'select' as const,
        filterOptions: [
            { label: 'Draft', value: 'draft' },
            { label: 'Published', value: 'published' },
            { label: 'Archived', value: 'archived' },
        ]
    },
];

const getStatusSeverity = (status: string) => {
    const severities: { [key: string]: string } = {
        draft: 'secondary',
        published: 'success',
        archived: 'danger',
    };
    return severities[status] || 'secondary';
};
</script>

<template>
    <Head title="Manajemen Project" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="projects.data"
                    :totalRecords="projects.meta?.total"
                    title="Manajemen Project"
                    description="Kelola semua project yang ada di sistem dengan akses penuh"
                    edit-route="/admin/projects"
                    delete-route="/admin/projects"
                    bulk-delete-route="/admin/projects/bulk-delete"
                    :exportable="true"
                >
                    <template #cell-user.name="{ item }">
                        {{ item.user?.name || '-' }}
                    </template>

                    <template #cell-kategori.nama="{ item }">
                        {{ item.kategori?.nama || '-' }}
                    </template>

                    <template #cell-status="{ item }">
                        <Tag 
                            :value="item.status.charAt(0).toUpperCase() + item.status.slice(1)" 
                            :severity="getStatusSeverity(item.status)" 
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

