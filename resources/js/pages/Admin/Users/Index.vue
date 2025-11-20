<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Eye, Edit, Trash2, RotateCcw } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    username: string;
    role: string;
    is_active: boolean;
}

interface Props {
    users: {
        data: User[];
        links: any;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin/users' },
    { title: 'Users', href: '/admin/users' },
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
</script>

<template>
    <Head title="User Management" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold">User Management</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Manage all users in the system
                </p>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Email</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Role</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id" class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium">{{ user.name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">@{{ user.username }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm">{{ user.email }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getRoleBadge(user.role)]">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getStatusBadge(user.is_active)]">
                                    {{ user.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <Link :href="`/admin/users/${user.id}`">
                                        <Button variant="outline" size="sm">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Link :href="`/admin/users/${user.id}/edit`">
                                        <Button variant="outline" size="sm">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Link :href="`/admin/users/${user.id}`" method="post" as="button">
                                        <Button variant="outline" size="sm" class="text-yellow-600">
                                            <RotateCcw class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Link :href="`/admin/users/${user.id}`" method="delete" as="button">
                                        <Button variant="outline" size="sm" class="text-red-600">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
