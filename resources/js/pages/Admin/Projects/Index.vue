<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';

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
    { title: 'Project', href: '/admin/projects' },
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
                            <h1 class="text-2xl font-bold tracking-tight">Manajemen Project</h1>
                            <p class="text-sm text-muted-foreground">
                                Kelola semua project yang ada di sistem dengan akses penuh
                            </p>
                        </div>
                    </div>

                    <!-- Filter & Search -->
                    <div class="mt-6">
                        <AdminFilterBar
                            :filters="filterOptions"
                            :current-filters="filters"
                            search-placeholder="Cari judul project, pembuat, atau kategori..."
                        />
                    </div>
                </div>

                <!-- Data Table Card -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
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
                            <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getStatusBadge(item.status)]">
                                {{ item.status.charAt(0).toUpperCase() + item.status.slice(1) }}
                            </span>
                        </template>
                    </AdminDataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

