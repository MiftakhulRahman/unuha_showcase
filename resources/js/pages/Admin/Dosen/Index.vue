<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

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
        meta?: {
            total: number;
        };
    };
    prodis: Prodi[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dosen', href: '/admin/dosen' },
];

const columns = [
    { field: 'name', header: 'Nama', sortable: true, filterType: 'text' as const },
    { field: 'email', header: 'Email', sortable: true, filterType: 'text' as const },
    { field: 'profile_dosen.nidn', header: 'NIDN', sortable: true, filterType: 'text' as const },
    { 
        field: 'profile_dosen.prodi.nama', 
        header: 'Prodi', 
        sortable: true, 
        filterType: 'select' as const,
        filterOptions: props.prodis.map(p => ({ label: p.nama, value: p.nama })) // Note: PrimeDataTable filters by value match, so we use name here if the column field is name.
    },
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
    <Head title="Manajemen Dosen" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="dosen.data"
                    :totalRecords="dosen.meta?.total"
                    title="Manajemen Dosen"
                    description="Kelola data semua dosen dalam sistem"
                    create-route="/admin/dosen/create"
                    edit-route="/admin/dosen"
                    delete-route="/admin/dosen"
                    bulk-delete-route="/admin/dosen/bulk-delete"
                    :exportable="true"
                >
                    <!-- Custom Slots for Nested Fields -->
                    <template #cell-profile_dosen.nidn="{ item }">
                        {{ item.profile_dosen?.nidn || '-' }}
                    </template>

                    <template #cell-profile_dosen.prodi.nama="{ item }">
                        {{ item.profile_dosen?.prodi?.nama || '-' }}
                    </template>

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
