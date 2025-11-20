<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';

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
    { title: 'Manajemen Project', href: '/admin/projects' },
];

const columns = [
    { key: 'title', label: 'Judul Project' },
    { key: 'user.name', label: 'Pembuat' },
    { key: 'kategori.nama', label: 'Kategori' },
    { key: 'status', label: 'Status' },
];

const filterOptions = [
    {
        name: 'status',
        label: 'Status',
        options: [
            { label: 'Draft', value: 'draft' },
            { label: 'Published', value: 'published' },
            { label: 'Archived', value: 'archived' },
        ],
    },
];

const getStatusBadge = (status: string) => {
    const colors: { [key: string]: string } = {
        draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
        published: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
        archived: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
    };
    return colors[status] || colors.draft;
};
</script>

<template>
    <Head title="Manajemen Project" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-bold">Manajemen Project</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Kelola semua project yang ada di sistem dengan akses penuh (edit, hapus, atau tentukan featured)
                    </p>
                </div>
            </div>

            <!-- Filter & Search -->
            <AdminFilterBar
                :filters="filterOptions"
                :current-filters="filters"
                search-placeholder="Cari judul project, pembuat, atau kategori..."
            />

            <!-- Data Table -->
            <AdminDataTable
                title=""
                :columns="columns"
                :data="projects.data"
                :links="projects.links"
                bulk-delete-route="/admin/projects/bulk-delete"
                edit-route="/admin/projects"
                delete-route="/admin/projects"
            >
                <template #cell-status="{ item }">
                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getStatusBadge(item.status)]">
                        {{ item.status.charAt(0).toUpperCase() + item.status.slice(1) }}
                    </span>
                </template>
            </AdminDataTable>
        </div>
    </AppLayout>
</template>
