<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

interface Challenge {
    id: number;
    title: string;
    description: string;
    slug: string;
    status: string;
    user: { name: string };
    start_date: string;
    end_date: string;
}

interface Props {
    challenges: {
        data: Challenge[];
        meta?: {
            total: number;
        };
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Challenge', href: '/admin/challenges' },
];

const columns = [
    { field: 'title', header: 'Judul Challenge', sortable: true, filterType: 'text' as const },
    { field: 'user.name', header: 'Pembuat (Dosen)', sortable: true, filterType: 'text' as const },
    { 
        field: 'status', 
        header: 'Status', 
        sortable: true, 
        filterType: 'select' as const,
        filterOptions: [
            { label: 'Draft', value: 'draft' },
            { label: 'Active', value: 'active' },
            { label: 'Finished', value: 'finished' },
        ]
    },
    { field: 'start_date', header: 'Tanggal Mulai', sortable: true },
    { field: 'end_date', header: 'Tanggal Berakhir', sortable: true },
];

const getStatusSeverity = (status: string) => {
    const severities: { [key: string]: string } = {
        draft: 'secondary',
        active: 'success',
        finished: 'info',
    };
    return severities[status] || 'secondary';
};
</script>

<template>
    <Head title="Manajemen Challenge" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="challenges.data"
                    :totalRecords="challenges.meta?.total"
                    title="Manajemen Challenge"
                    description="Pantau, edit, atau batalkan challenge yang dibuat oleh dosen"
                    edit-route="/admin/challenges"
                    delete-route="/admin/challenges"
                    bulk-delete-route="/admin/challenges/bulk-delete"
                    :exportable="true"
                >
                    <template #cell-user.name="{ item }">
                        {{ item.user?.name || '-' }}
                    </template>

                    <template #cell-status="{ item }">
                        <Tag 
                            :value="item.status.charAt(0).toUpperCase() + item.status.slice(1)" 
                            :severity="getStatusSeverity(item.status)" 
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>

                    <template #cell-start_date="{ item }">
                        {{ new Date(item.start_date).toLocaleDateString('id-ID') }}
                    </template>

                    <template #cell-end_date="{ item }">
                        {{ new Date(item.end_date).toLocaleDateString('id-ID') }}
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

