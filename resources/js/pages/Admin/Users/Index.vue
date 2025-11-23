```vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Avatar from 'primevue/avatar';
import Divider from 'primevue/divider';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

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
const toast = useToast();

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

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

// Modal & Form Logic
const showDialog = ref(false);
const showDetailDialog = ref(false);
const editingId = ref<number | null>(null);
const selectedUser = ref<User | null>(null);

const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
    role: 'mahasiswa',
    is_active: true,
});

const createUser = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
};

const editUser = (user: User) => {
    editingId.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.username = user.username;
    form.role = user.role;
    form.is_active = !!user.is_active;
    form.password = '';
    form.password_confirmation = '';
    
    form.clearErrors();
    showDialog.value = true;
};

const viewUser = (user: User) => {
    selectedUser.value = user;
    showDetailDialog.value = true;
};

const saveUser = () => {
    if (editingId.value) {
        form.put(`/admin/users/${editingId.value}`, {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal memperbarui pengguna');
            }
        });
    } else {
        form.post('/admin/users', {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal menambahkan pengguna');
            }
        });
    }
};
</script>

<template>
    <Head title="Manajemen Pengguna" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

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
                    <template #toolbar-start>
                        <Button 
                            label="Tambah" 
                            icon="pi pi-plus" 
                            class="mr-2" 
                            @click="createUser" 
                        />
                    </template>

                    <template #cell-role="{ item }">
                        <Tag 
                            :value="item.role" 
                            :severity="getRoleSeverity(item.role)" 
                            class="custom-badge"
                            style="font-weight: 500 !important;" 
                        />
                    </template>

                    <template #cell-is_active="{ item }">
                        <Tag 
                            :value="item.is_active ? 'Aktif' : 'Tidak Aktif'" 
                            :severity="getStatusSeverity(item.is_active)" 
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>

                    <template #actions="{ item }">
                        <Button 
                            icon="pi pi-eye" 
                            variant="outlined" 
                            rounded 
                            severity="info"
                            class="mr-2" 
                            @click="viewUser(item)" 
                        />
                        <Button 
                            icon="pi pi-pencil" 
                            variant="outlined" 
                            rounded 
                            class="mr-2" 
                            @click="editUser(item)" 
                        />
                    </template>
                </PrimeDataTable>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog 
            v-model:visible="showDialog" 
            :style="{ width: '500px' }" 
            :header="editingId ? 'Edit Pengguna' : 'Tambah Pengguna'" 
            :modal="true" 
            class="p-fluid"
        >
            <div class="field mb-4">
                <label for="name" class="font-medium mb-2 block">Nama Lengkap</label>
                <InputText 
                    id="name" 
                    v-model="form.name" 
                    required="true" 
                    :class="{ 'p-invalid': form.errors.name }" 
                />
                <small class="p-error" v-if="form.errors.name">{{ form.errors.name }}</small>
            </div>

            <div class="field mb-4">
                <label for="email" class="font-medium mb-2 block">Email</label>
                <InputText 
                    id="email" 
                    v-model="form.email" 
                    required="true" 
                    type="email"
                    :class="{ 'p-invalid': form.errors.email }" 
                />
                <small class="p-error" v-if="form.errors.email">{{ form.errors.email }}</small>
            </div>

            <div class="field mb-4">
                <label for="username" class="font-medium mb-2 block">Username</label>
                <InputText 
                    id="username" 
                    v-model="form.username" 
                    required="true" 
                    :class="{ 'p-invalid': form.errors.username }" 
                />
                <small class="p-error" v-if="form.errors.username">{{ form.errors.username }}</small>
            </div>

            <div class="field mb-4">
                <label for="role" class="font-medium mb-2 block">Role</label>
                <Select 
                    id="role"
                    v-model="form.role" 
                    :options="[
                        {label: 'Superadmin', value: 'superadmin'},
                        {label: 'Dosen', value: 'dosen'},
                        {label: 'Mahasiswa', value: 'mahasiswa'}
                    ]" 
                    optionLabel="label" 
                    optionValue="value" 
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.role }"
                />
                <small class="p-error" v-if="form.errors.role">{{ form.errors.role }}</small>
            </div>

            <div class="field mb-4">
                <label for="password" class="font-medium mb-2 block">Password {{ editingId ? '(Opsional)' : '*' }}</label>
                <InputText 
                    id="password" 
                    v-model="form.password" 
                    type="password"
                    :class="{ 'p-invalid': form.errors.password }" 
                />
                <small class="p-error" v-if="form.errors.password">{{ form.errors.password }}</small>
            </div>

            <div class="field mb-4">
                <label for="password_confirmation" class="font-medium mb-2 block">Konfirmasi Password</label>
                <InputText 
                    id="password_confirmation" 
                    v-model="form.password_confirmation" 
                    type="password"
                />
            </div>

            <div class="field mb-4" v-if="editingId">
                <label for="is_active" class="font-medium mb-2 block">Status</label>
                <Select 
                    id="is_active"
                    v-model="form.is_active" 
                    :options="[{label: 'Active', value: true}, {label: 'Inactive', value: false}]" 
                    optionLabel="label" 
                    optionValue="value" 
                    class="w-full"
                />
            </div>

            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="showDialog = false" />
                <Button label="Simpan" icon="pi pi-check" text @click="saveUser" :loading="form.processing" />
            </template>
        </Dialog>

        <!-- Detail Dialog (Premium Design) -->
        <Dialog 
            v-model:visible="showDetailDialog" 
            :style="{ width: '500px' }" 
            :modal="true" 
            :showHeader="false"
            class="p-0 overflow-hidden rounded-2xl"
            :contentStyle="{ padding: '0' }"
        >
            <div v-if="selectedUser" class="bg-white dark:bg-gray-900">
                <!-- Header Section -->
                <div class="relative h-32 bg-gradient-to-r from-gray-700 to-gray-900">
                    <div class="absolute -bottom-12 left-8">
                        <Avatar 
                            :label="getInitials(selectedUser.name)" 
                            size="xlarge" 
                            shape="circle" 
                            class="w-24 h-24 text-3xl font-bold bg-white text-gray-800 border-4 border-white shadow-lg"
                        />
                    </div>
                    <div class="absolute top-4 right-4">
                        <Button 
                            icon="pi pi-times" 
                            rounded 
                            text 
                            class="text-white hover:bg-white/20" 
                            @click="showDetailDialog = false" 
                        />
                    </div>
                </div>

                <!-- Content Section -->
                <div class="pt-16 pb-8 px-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ selectedUser.name }}</h2>
                            <Tag 
                                :value="selectedUser.role" 
                                :severity="getRoleSeverity(selectedUser.role)" 
                                class="mt-2"
                            />
                        </div>
                        <Tag 
                            :value="selectedUser.is_active ? 'Aktif' : 'Tidak Aktif'" 
                            :severity="selectedUser.is_active ? 'success' : 'danger'" 
                            class="px-3 py-1 text-sm"
                        />
                    </div>

                    <Divider />

                    <div class="space-y-4 mt-6">
                        <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                            <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                                <i class="pi pi-envelope"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="font-medium">{{ selectedUser.email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                            <div class="w-8 h-8 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600">
                                <i class="pi pi-user"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Username</p>
                                <p class="font-medium">{{ selectedUser.username }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                            <div class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600">
                                <i class="pi pi-check-circle"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Registrasi Selesai</p>
                                <p class="font-medium">{{ selectedUser.registration_completed ? 'Ya' : 'Belum' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-800/50 px-8 py-4 flex justify-end">
                    <Button label="Tutup" icon="pi pi-times" text @click="showDetailDialog = false" />
                </div>
            </div>
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
    font-weight: 500 !important; /* Try Semibold (500) or Extra Bold (800) - User said 700 system was too thick, Outfit 700 was too thin. Let's try 500 first, if too thin, we go 800. Actually, let's go with a specific weight that might work better. */
    font-family: 'Outfit', sans-serif !important;
}
</style>
```
