<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

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
        meta?: {
            total: number;
        };
    };
    prodis: Prodi[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Mahasiswa', href: '/admin/mahasiswa' },
];

const columns = [
    { field: 'name', header: 'Nama', sortable: true, filterType: 'text' as const },
    { field: 'email', header: 'Email', sortable: true, filterType: 'text' as const },
    { field: 'profile_mahasiswa.nim', header: 'NIM', sortable: true, filterType: 'text' as const },
    { 
        field: 'profile_mahasiswa.prodi.nama', 
        header: 'Prodi', 
        sortable: true, 
        filterType: 'select' as const,
        filterOptions: props.prodis.map(p => ({ label: p.nama, value: p.nama }))
    },
    { field: 'profile_mahasiswa.angkatan', header: 'Angkatan', sortable: true, filterType: 'text' as const },
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
    <Head title="Manajemen Mahasiswa" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="mahasiswa.data"
                    :totalRecords="mahasiswa.meta?.total"
                    title="Manajemen Mahasiswa"
                    description="Kelola data semua mahasiswa dalam sistem"
                    create-route="/admin/mahasiswa/create"
                    edit-route="/admin/mahasiswa"
                    delete-route="/admin/mahasiswa"
                    bulk-delete-route="/admin/mahasiswa/bulk-delete"
                    :exportable="true"
                >
                    <!-- Custom Slots for Nested Fields -->
                    <template #cell-profile_mahasiswa.nim="{ item }">
                        {{ item.profile_mahasiswa?.nim || '-' }}
                    </template>

                    <template #cell-profile_mahasiswa.prodi.nama="{ item }">
                        {{ item.profile_mahasiswa?.prodi?.nama || '-' }}
                    </template>

                    <template #cell-profile_mahasiswa.angkatan="{ item }">
                        {{ item.profile_mahasiswa?.angkatan || '-' }}
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
