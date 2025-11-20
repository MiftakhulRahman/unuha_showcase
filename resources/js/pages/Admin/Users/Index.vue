<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import AdminFilterBar from '@/components/AdminFilterBar.vue';
import { Button } from '@/components/ui/button';

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
        links: any;
    };
    filters: {
        search?: string;
        role?: string;
        status?: string;
        registration?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Pengguna', href: '/admin/users' },
];

const columns = [
    { key: 'name', label: 'Nama' },
    { key: 'email', label: 'Email' },
    { key: 'username', label: 'Username' },
    { key: 'role', label: 'Role' },
    { key: 'is_active', label: 'Status' },
];

const getRoleBadge = (role: string) => {
    const colors: { [key: string]: string } = {
        superadmin: 'bg-red-100 text-red-800 dark:bg-red-900',
        dosen: 'bg-blue-100 text-blue-800 dark:bg-blue-900',
        mahasiswa: 'bg-green-100 text-green-800 dark:bg-green-900',
    };
    return colors[role] || 'bg-gray-100 text-gray-800';
};

const getStatusBadge = (isActive: boolean) => {
    return isActive
        ? 'bg-green-100 text-green-800 dark:bg-green-900'
        : 'bg-red-100 text-red-800 dark:bg-red-900';
};

const filterOptions = [
    {
        key: 'role',
        label: 'Role',
        type: 'select' as const,
        placeholder: 'Semua Role',
        options: [
            { label: 'Super Admin', value: 'superadmin' },
            { label: 'Dosen', value: 'dosen' },
            { label: 'Mahasiswa', value: 'mahasiswa' },
        ],
    },
    {
        key: 'status',
        label: 'Status',
        type: 'select' as const,
        placeholder: 'Semua Status',
        options: [
            { label: 'Aktif', value: 'active' },
            { label: 'Tidak Aktif', value: 'inactive' },
        ],
    },
    {
        key: 'registration',
        label: 'Status Registrasi',
        type: 'select' as const,
        placeholder: 'Semua Status',
        options: [
            { label: 'Selesai', value: 'completed' },
            { label: 'Belum Selesai', value: 'pending' },
        ],
    },
];
</script>

<template>
    <Head title="Manajemen Pengguna" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-bold">Manajemen Pengguna</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Kelola semua pengguna dalam sistem
                    </p>
                </div>
                <Link href="/admin/users/create" as="button">
                    <Button>+ Tambah Pengguna</Button>
                </Link>
            </div>

            <!-- Filter & Search -->
            <AdminFilterBar
                :filters="filterOptions"
                :current-filters="filters"
                search-placeholder="Cari nama, email, atau username..."
            />

            <!-- Data Table -->
            <AdminDataTable
                title=""
                :columns="columns"
                :data="users.data"
                :links="users.links"
                bulk-delete-route="/admin/users/bulk-delete"
                edit-route="/admin/users"
                delete-route="/admin/users"
            >
                <template #cell-role="{ item }">
                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getRoleBadge(item.role)]">
                        {{ item.role }}
                    </span>
                </template>

                <template #cell-is_active="{ item }">
                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getStatusBadge(item.is_active)]">
                        {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </template>
            </AdminDataTable>
        </div>
    </AppLayout>
</template>

