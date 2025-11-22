<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { ref } from 'vue';
import CreateUserForm from './Partials/CreateUserForm.vue';
import EditUserForm from './Partials/EditUserForm.vue';

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
    { field: 'name', header: 'Nama', sortable: true, filterType: 'text' as const },
    { field: 'email', header: 'Email', sortable: true, filterType: 'text' as const },
    { field: 'username', header: 'Username', sortable: true, filterType: 'text' as const },
    { 
        field: 'role', 
        header: 'Role', 
        sortable: true, 
        filterType: 'select' as const,
        filterOptions: [
            { label: 'Superadmin', value: 'superadmin' },
            { label: 'Dosen', value: 'dosen' },
            { label: 'Mahasiswa', value: 'mahasiswa' },
        ]
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

// Modal State
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref<User | null>(null);

const openCreateModal = () => {
    showCreateModal.value = true;
};

const openEditModal = (user: User) => {
    selectedUser.value = user;
    showEditModal.value = true;
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    selectedUser.value = null;
};
</script>

<template>
    <Head title="Manajemen Pengguna" />
    <AppLayout>
        <div class="min-h-screen">
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
                        delete-route="/admin/users"
                        bulk-delete-route="/admin/users/bulk-delete"
                        :exportable="true"
                    >
                        <!-- Custom Toolbar Actions -->
                        <template #toolbar-start>
                            <Button 
                                label="Tambah"
                                icon="pi pi-plus" 
                                class="mr-2 responsive-label" 
                                @click="openCreateModal" 
                            />
                        </template>

                        <template #cell-role="{ item }">
                            <Tag 
                                :value="item.role" 
                                :severity="getRoleSeverity(item.role)" 
                                class="custom-badge"
                                style="font-weight: 600 !important;" 
                            />
                        </template>

                        <template #cell-is_active="{ item }">
                            <Tag 
                                :value="item.is_active ? 'Aktif' : 'Tidak Aktif'" 
                                :severity="getStatusSeverity(item.is_active)" 
                                class="custom-badge"
                                style="font-weight: 600 !important;"
                            />
                        </template>

                        <template #actions="{ item }">
                            <Button 
                                icon="pi pi-pencil" 
                                variant="outlined" 
                                rounded 
                                class="mr-2" 
                                @click="openEditModal(item)" 
                            />
                        </template>
                    </PrimeDataTable>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <Dialog 
            v-model:visible="showCreateModal" 
            modal 
            header="Tambah Pengguna Baru" 
            :style="{ width: '30rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <CreateUserForm @close="closeModals" />
        </Dialog>

        <!-- Edit Modal -->
        <Dialog 
            v-model:visible="showEditModal" 
            modal 
            header="Edit Pengguna" 
            :style="{ width: '30rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <EditUserForm 
                v-if="selectedUser" 
                :user="selectedUser" 
                @close="closeModals" 
            />
        </Dialog>
    </AppLayout>
</template>

<style scoped>
/* Hide label on mobile for Button with responsive-label class */
:deep(.responsive-label .p-button-label) {
    display: none;
}

@media (min-width: 640px) {
    :deep(.responsive-label .p-button-label) {
        display: block;
    }
}

/* Nuclear option for Badge Font Weight */
:deep(.custom-badge),
:deep(.custom-badge .p-tag-value),
:deep(.p-tag.custom-badge),
:deep(.p-tag.custom-badge span) {
    font-weight: 600 !important; /* Try Semibold (600) or Extra Bold (800) - User said 700 system was too thick, Outfit 700 was too thin. Let's try 600 first, if too thin, we go 800. Actually, let's go with a specific weight that might work better. */
    font-family: 'Outfit', sans-serif !important;
}
</style>
