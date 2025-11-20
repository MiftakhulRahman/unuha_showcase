<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';

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
        links: any;
    };
    filters: {
        search: string;
        status: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Manajemen Challenge', href: '/admin/challenges' },
];

const columns = [
    { key: 'title', label: 'Judul Challenge' },
    { key: 'user.name', label: 'Pembuat (Dosen)' },
    { key: 'status', label: 'Status' },
    { key: 'start_date', label: 'Tanggal Mulai' },
    { key: 'end_date', label: 'Tanggal Berakhir' },
];

const filterOptions = [
    {
        name: 'status',
        label: 'Status',
        options: [
            { label: 'Draft', value: 'draft' },
            { label: 'Active', value: 'active' },
            { label: 'Finished', value: 'finished' },
        ],
    },
];

const getStatusBadge = (status: string) => {
    const colors: { [key: string]: string } = {
        draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
        active: 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
        finished: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
    };
    return colors[status] || colors.draft;
};
</script>

<template>
    <Head title="Manajemen Challenge" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-bold">Manajemen Challenge</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Pantau, edit, atau batalkan challenge yang dibuat oleh dosen dengan akses penuh
                    </p>
                </div>
            </div>

            <!-- Filter & Search -->
            <AdminFilterBar
                :filters="filterOptions"
                :current-filters="filters"
                search-placeholder="Cari judul challenge atau pembuat (dosen)..."
            />

            <!-- Data Table -->
            <AdminDataTable
                title=""
                :columns="columns"
                :data="challenges.data"
                :links="challenges.links"
                bulk-delete-route="/admin/challenges/bulk-delete"
                edit-route="/admin/challenges"
                delete-route="/admin/challenges"
            >
                <template #cell-status="{ item }">
                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getStatusBadge(item.status)]">
                        {{ item.status.charAt(0).toUpperCase() + item.status.slice(1) }}
                    </span>
                </template>
                <template #cell-start_date="{ item }">
                    {{ new Date(item.start_date).toLocaleDateString('id-ID') }}
                </template>
                <template #cell-end_date="{ item }">
                    {{ new Date(item.end_date).toLocaleDateString('id-ID') }}
                </template>
            </AdminDataTable>
        </div>
    </AppLayout>
</template>
