<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';

interface User {
    id: number;
    name: string;
    email: string;
    username: string;
    role: string;
    is_active: boolean;
    registration_completed: boolean;
}

interface Props {
    users: {
        data: User[];
        meta?: {
            from: number;
            to: number;
            total: number;
        };
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pengguna', href: '/admin/users' },
];

const columns = [
    { field: 'name', header: 'Nama', sortable: true },
    { field: 'email', header: 'Email', sortable: true },
    { field: 'username', header: 'Username', sortable: true },
    { field: 'role', header: 'Role', sortable: true },
    { field: 'is_active', header: 'Status', sortable: true },
];

const getRoleSeverity = (role: string) => {
    const severities: { [key: string]: string } = {
        superadmin: 'danger',
        dosen: 'info',
        mahasiswa: 'success',
    };
    return severities[role] || 'secondary';
};

const getStatusSeverity = (isActive: boolean) => {
    return isActive ? 'success' : 'danger';
};
</script>

<template>
    <Head title="Manajemen Pengguna" />
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-950 dark:to-gray-900">
            <div class="mx-auto max-w-7xl space-y-6 p-6">
                <!-- Breadcrumb -->
                <Breadcrumbs :breadcrumbs="breadcrumbs" />

                <!-- Data Table with Integrated Header -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <PrimeDataTable
                        :columns="columns"
                        :data="users.data"
                        :totalRecords="users.meta?.total"
                        title="Manajemen Pengguna"
                        description="Kelola semua pengguna dalam sistem"
                        create-route="/admin/users/create"
                        edit-route="/admin/users"
                        delete-route="/admin/users"
                        bulk-delete-route="/admin/users/bulk-delete"
                        :exportable="true"
                    >
                        <template #cell-role="{ item }">
                            <Tag :value="item.role" :severity="getRoleSeverity(item.role)" />
                        </template>

                        <template #cell-is_active="{ item }">
                            <Tag 
                                :value="item.is_active ? 'Aktif' : 'Tidak Aktif'" 
                                :severity="getStatusSeverity(item.is_active)" 
                            />
                        </template>
                    </PrimeDataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
