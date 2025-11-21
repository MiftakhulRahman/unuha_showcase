<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';

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
    { title: 'Challenge', href: '/admin/challenges' },
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
                            <h1 class="text-2xl font-bold tracking-tight">Manajemen Challenge</h1>
                            <p class="text-sm text-muted-foreground">
                                Pantau, edit, atau batalkan challenge yang dibuat oleh dosen
                            </p>
                        </div>
                    </div>

                    <!-- Filter & Search -->
                    <div class="mt-6">
                        <AdminFilterBar
                            :filters="filterOptions"
                            :current-filters="filters"
                            search-placeholder="Cari judul challenge atau pembuat (dosen)..."
                        />
                    </div>
                </div>

                <!-- Data Table Card -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
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
                            <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getStatusBadge(item.status)]">
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
            </div>
        </div>
    </AppLayout>
</template>

