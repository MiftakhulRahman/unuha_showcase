<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Select from 'primevue/select';
import Avatar from 'primevue/avatar';
import Divider from 'primevue/divider';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

interface Mahasiswa {
    id: number;
    name: string;
    email: string;
    username: string;
    is_active: boolean;
    profile_mahasiswa?: {
        nim: string;
        angkatan: number;
        semester: number;
        prodi_id: number;
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
const toast = useToast();

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

// Modal & Form Logic
const showDialog = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);
const showDetailDialog = ref(false);
const selectedMahasiswa = ref<Mahasiswa | null>(null);

const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
    is_active: true,
    profile: {
        nim: '',
        prodi_id: null as number | null,
        angkatan: new Date().getFullYear(),
        semester: 1,
    },
});

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

const openNew = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
};

const editMahasiswa = (mhs: Mahasiswa) => {
    isEditing.value = true;
    editingId.value = mhs.id;
    form.name = mhs.name;
    form.email = mhs.email;
    form.username = mhs.username;
    form.password = ''; // Reset password fields
    form.password_confirmation = '';
    form.is_active = !!mhs.is_active;
    
    // Profile data
    if (mhs.profile_mahasiswa) {
        form.profile.nim = mhs.profile_mahasiswa.nim;
        form.profile.prodi_id = mhs.profile_mahasiswa.prodi_id;
        form.profile.angkatan = mhs.profile_mahasiswa.angkatan;
        form.profile.semester = mhs.profile_mahasiswa.semester || 1;
    } else {
        form.profile.nim = '';
        form.profile.prodi_id = null;
        form.profile.angkatan = new Date().getFullYear();
        form.profile.semester = 1;
    }

    form.clearErrors();
    showDialog.value = true;
};

const viewMahasiswa = (mhs: Mahasiswa) => {
    selectedMahasiswa.value = mhs;
    showDetailDialog.value = true;
};

const saveMahasiswa = () => {
    if (isEditing.value && editingId.value) {
        form.put(`/admin/mahasiswa/${editingId.value}`, {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal memperbarui data mahasiswa');
            }
        });
    } else {
        form.post('/admin/mahasiswa', {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal menambahkan mahasiswa');
            }
        });
    }
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
                    delete-route="/admin/mahasiswa"
                    bulk-delete-route="/admin/mahasiswa/bulk-delete"
                    :exportable="true"
                >
                    <!-- Custom Toolbar for Add Button -->
                    <template #toolbar-start>
                        <Button 
                            label="Tambah" 
                            icon="pi pi-plus" 
                            class="mr-2" 
                            @click="openNew" 
                        />
                    </template>

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
                            :value="item.is_active ? 'Active' : 'Inactive'" 
                            :severity="item.is_active ? 'success' : 'danger'" 
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>

                    <!-- Custom Actions Slot (Edit) -->
                    <template #actions="{ item }">
                        <Button 
                            icon="pi pi-eye" 
                            variant="outlined" 
                            rounded 
                            severity="info"
                            class="mr-2" 
                            @click="viewMahasiswa(item)" 
                        />
                        <Button 
                            icon="pi pi-pencil" 
                            variant="outlined" 
                            rounded 
                            class="mr-2" 
                            @click="editMahasiswa(item)" 
                        />
                    </template>
                </PrimeDataTable>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog 
            v-model:visible="showDialog" 
            :style="{ width: '800px' }" 
            :header="isEditing ? 'Edit Mahasiswa' : 'Tambah Mahasiswa'" 
            :modal="true" 
            class="p-fluid"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Account Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Informasi Akun</h3>
                    
                    <div class="field mb-4">
                        <label for="name" class="font-medium mb-2 block">Nama Lengkap</label>
                        <InputText 
                            id="name" 
                            v-model="form.name" 
                            required="true" 
                            :class="{ 'p-invalid': form.errors.name }" 
                            placeholder="Nama Lengkap"
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
                            placeholder="Email"
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
                            placeholder="Username"
                        />
                        <small class="p-error" v-if="form.errors.username">{{ form.errors.username }}</small>
                    </div>

                    <div class="field mb-4">
                        <label for="password" class="font-medium mb-2 block">Password {{ isEditing ? '(Kosongkan jika tidak diubah)' : '*' }}</label>
                        <InputText 
                            id="password" 
                            v-model="form.password" 
                            type="password"
                            :class="{ 'p-invalid': form.errors.password }" 
                            placeholder="Password"
                        />
                        <small class="p-error" v-if="form.errors.password">{{ form.errors.password }}</small>
                    </div>

                    <div class="field mb-4">
                        <label for="password_confirmation" class="font-medium mb-2 block">Konfirmasi Password</label>
                        <InputText 
                            id="password_confirmation" 
                            v-model="form.password_confirmation" 
                            type="password"
                            placeholder="Konfirmasi Password"
                        />
                    </div>
                </div>

                <!-- Profile Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Data Mahasiswa</h3>

                    <div class="field mb-4">
                        <label for="nim" class="font-medium mb-2 block">NIM</label>
                        <InputText 
                            id="nim" 
                            v-model="form.profile.nim" 
                            required="true" 
                            :class="{ 'p-invalid': form.errors['profile.nim'] }" 
                            placeholder="NIM"
                        />
                        <small class="p-error" v-if="form.errors['profile.nim']">{{ form.errors['profile.nim'] }}</small>
                    </div>

                    <div class="field mb-4">
                        <label for="prodi_id" class="font-medium mb-2 block">Program Studi</label>
                        <Select 
                            id="prodi_id"
                            v-model="form.profile.prodi_id" 
                            :options="prodis" 
                            optionLabel="nama" 
                            optionValue="id" 
                            placeholder="Pilih Program Studi" 
                            class="w-full"
                            :class="{ 'p-invalid': form.errors['profile.prodi_id'] }"
                        />
                        <small class="p-error" v-if="form.errors['profile.prodi_id']">{{ form.errors['profile.prodi_id'] }}</small>
                    </div>

                    <div class="field mb-4">
                        <label for="angkatan" class="font-medium mb-2 block">Angkatan</label>
                        <InputNumber 
                            id="angkatan" 
                            v-model="form.profile.angkatan" 
                            :useGrouping="false"
                            placeholder="Contoh: 2023"
                            class="w-full"
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="semester" class="font-medium mb-2 block">Semester</label>
                        <Select 
                            id="semester"
                            v-model="form.profile.semester" 
                            :options="[1,2,3,4,5,6,7,8]" 
                            placeholder="Pilih Semester" 
                            class="w-full"
                        />
                    </div>

                    <div class="field mb-4" v-if="editingId">
                        <label for="is_active" class="font-medium mb-2 block">Status Akun</label>
                        <Select 
                            id="is_active"
                            v-model="form.is_active" 
                            :options="[{label: 'Active', value: true}, {label: 'Inactive', value: false}]" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-full"
                        />
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="showDialog = false" />
                <Button label="Simpan" icon="pi pi-check" text @click="saveMahasiswa" :loading="form.processing" />
            </template>
        </Dialog>

        <!-- Detail Dialog (Premium Design) -->
        <Dialog 
            v-model:visible="showDetailDialog" 
            :style="{ width: '600px' }" 
            :modal="true" 
            :showHeader="false"
            class="p-0 overflow-hidden rounded-2xl"
            :contentStyle="{ padding: '0' }"
        >
            <div v-if="selectedMahasiswa" class="bg-white dark:bg-gray-900">
                <!-- Header Section -->
                <div class="relative h-32 bg-gradient-to-r from-emerald-600 to-teal-700">
                    <div class="absolute -bottom-12 left-8">
                        <Avatar 
                            :label="getInitials(selectedMahasiswa.name)" 
                            size="xlarge" 
                            shape="circle" 
                            class="w-24 h-24 text-3xl font-bold bg-white text-teal-700 border-4 border-white shadow-lg"
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
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ selectedMahasiswa.name }}</h2>
                            <p class="text-gray-500 dark:text-gray-400">Mahasiswa</p>
                        </div>
                        <Tag 
                            :value="selectedMahasiswa.is_active ? 'Active' : 'Inactive'" 
                            :severity="selectedMahasiswa.is_active ? 'success' : 'danger'" 
                            class="px-3 py-1 text-sm"
                        />
                    </div>

                    <Divider />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Account Details -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Informasi Akun</h3>
                            
                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                                    <i class="pi pi-envelope"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p class="font-medium">{{ selectedMahasiswa.email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600">
                                    <i class="pi pi-user"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Username</p>
                                    <p class="font-medium">{{ selectedMahasiswa.username }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Details -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Mahasiswa</h3>
                            
                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600">
                                    <i class="pi pi-id-card"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">NIM</p>
                                    <p class="font-medium">{{ selectedMahasiswa.profile_mahasiswa?.nim || '-' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600">
                                    <i class="pi pi-building"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Program Studi</p>
                                    <p class="font-medium">{{ selectedMahasiswa.profile_mahasiswa?.prodi?.nama || '-' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-teal-600">
                                    <i class="pi pi-calendar"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Angkatan</p>
                                    <p class="font-medium">{{ selectedMahasiswa.profile_mahasiswa?.angkatan || '-' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-600">
                                    <i class="pi pi-bookmark"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Semester</p>
                                    <p class="font-medium">{{ selectedMahasiswa.profile_mahasiswa?.semester || '-' }}</p>
                                </div>
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
/* Nuclear option for Badge Font Weight */
:deep(.custom-badge),
:deep(.custom-badge .p-tag-value),
:deep(.p-tag.custom-badge),
:deep(.p-tag.custom-badge span) {
    font-weight: 500 !important;
    font-family: 'Outfit', sans-serif !important;
}
</style>
