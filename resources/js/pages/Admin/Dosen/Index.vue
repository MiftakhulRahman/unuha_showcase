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
import Select from 'primevue/select';
import Avatar from 'primevue/avatar';
import Divider from 'primevue/divider';
import ToggleSwitch from 'primevue/toggleswitch';
import { ref, computed } from 'vue';
import { useToast } from '@/composables/useToast';

interface Dosen {
    id: number;
    name: string;
    email: string;
    username: string;
    is_active: boolean;
    profile_dosen: { // Changed to required
        nidn: string;
        prodi_id: number;
        jabatan: string; // Changed to required
        bidang_keahlian: string; // Changed to required
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
    dosen: {
        data: Dosen[];
        meta?: {
            total: number;
        };
    };
    prodis: Prodi[];
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dosen', href: '/admin/dosen' },
];

const columns = [
    { field: 'name', header: 'Nama', sortable: true, filterType: 'text' as const },
    { field: 'email', header: 'Email', sortable: true, filterType: 'text' as const },
    { field: 'profile_dosen.nidn', header: 'NIDN', sortable: true, filterType: 'text' as const },
    { 
        field: 'profile_dosen.prodi.nama', 
        header: 'Prodi', 
        sortable: true, 
        filterType: 'select' as const,
        filterOptions: props.prodis.map(p => ({ label: p.nama, value: p.id }))
    },
    { 
        field: 'is_active', 
        header: 'Status', 
        sortable: true, 
        filterType: 'boolean' as const,
        filterOptions: [
            { label: 'Active', value: true },
            { label: 'Inactive', value: false },
        ]
    },
];

// Removed getStatusSeverity as it's no longer used in the template

// Modal & Form Logic
const showDialog = ref(false);
const showDetailDialog = ref(false); // Added
const editingId = ref<number | null>(null);
const selectedDosen = ref<Dosen | null>(null); // Added

const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
    is_active: true,
    profile: {
        nidn: '',
        prodi_id: null as number | null,
        jabatan: '',
        bidang_keahlian: '',
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

const createDosen = () => { // Renamed from openNew
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
};

const editDosen = (dosen: Dosen) => {
    editingId.value = dosen.id;
    form.name = dosen.name;
    form.email = dosen.email;
    form.username = dosen.username;
    form.password = ''; // Reset password fields
    form.password_confirmation = '';
    form.is_active = !!dosen.is_active;
    
    // Profile data
    // Simplified profile data assignment as profile_dosen is now required in interface
    form.profile.nidn = dosen.profile_dosen.nidn || '';
    form.profile.prodi_id = dosen.profile_dosen.prodi_id || null;
    form.profile.jabatan = dosen.profile_dosen.jabatan || '';
    form.profile.bidang_keahlian = dosen.profile_dosen.bidang_keahlian || '';

    form.clearErrors();
    showDialog.value = true;
};

const viewDosen = (dosen: Dosen) => { // Added
    selectedDosen.value = dosen;
    showDetailDialog.value = true;
};

const saveDosen = () => {
    if (editingId.value) { // Changed condition from isEditing.value && editingId.value
        form.put(`/admin/dosen/${editingId.value}`, {
            onSuccess: () => {
                showDialog.value = false;
                // toast.success('Data dosen berhasil diperbarui'); // Removed
                form.reset();
            },
            onError: () => {
                toast.error('Gagal memperbarui dosen'); // Changed message
            }
        });
    } else {
        form.post('/admin/dosen', {
            onSuccess: () => {
                showDialog.value = false;
                // toast.success('Dosen berhasil ditambahkan'); // Removed
                form.reset();
            },
            onError: () => {
                toast.error('Gagal menambahkan dosen'); // Changed message
            }
        });
    }
};
</script>

<template>
    <Head title="Manajemen Dosen" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="dosen.data"
                    :totalRecords="dosen.meta?.total"
                    title="Manajemen Dosen"
                    description="Kelola data dosen, termasuk akun pengguna dan profil akademik"
                    delete-route="/admin/dosen"
                    bulk-delete-route="/admin/dosen/bulk-delete"
                    :exportable="true"
                >
                    <!-- Custom Toolbar for Add Button -->
                    <template #toolbar-start>
                        <Button 
                            label="Tambah" 
                            icon="pi pi-plus" 
                            class="mr-2" 
                            @click="createDosen" 
                        />
                    </template>

                    <!-- Custom Slots for Nested Fields -->
                    <template #cell-profile_dosen.nidn="{ item }">
                        {{ item.profile_dosen?.nidn || '-' }}
                    </template>

                    <template #cell-profile_dosen.prodi.nama="{ item }">
                        {{ item.profile_dosen?.prodi?.nama || '-' }}
                    </template>

                    <template #cell-is_active="{ item }">
                        <Tag 
                            :value="item.is_active ? 'Active' : 'Inactive'" 
                            :severity="item.is_active ? 'success' : 'danger'" 
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>

                    <!-- Custom Actions Slot (Edit & Show) --> <!-- Changed comment -->
                    <template #actions="{ item }">
                        <Button 
                            icon="pi pi-eye" 
                            variant="outlined" 
                            rounded 
                            severity="info"
                            class="mr-2" 
                            @click="viewDosen(item)" 
                        />
                        <Button 
                            icon="pi pi-pencil" 
                            variant="outlined" 
                            rounded 
                            class="mr-2" 
                            @click="editDosen(item)" 
                        />
                    </template>
                </PrimeDataTable>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog 
            v-model:visible="showDialog" 
            :style="{ width: '800px' }" 
            :header="editingId ? 'Edit Dosen' : 'Tambah Dosen'" 
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
                        <label for="password" class="font-medium mb-2 block">Password {{ editingId ? '(Opsional)' : '*' }}</label>
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

                <!-- Profile Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Profil Akademik</h3> <!-- Changed header -->

                    <div class="field mb-4">
                        <label for="nidn" class="font-medium mb-2 block">NIDN</label>
                        <InputText 
                            id="nidn" 
                            v-model="form.profile.nidn" 
                            required="true" 
                            :class="{ 'p-invalid': form.errors['profile.nidn'] }" 
                            placeholder="NIDN"
                        />
                        <small class="p-error" v-if="form.errors['profile.nidn']">{{ form.errors['profile.nidn'] }}</small>
                    </div>

                    <div class="field mb-4">
                        <label for="prodi" class="font-medium mb-2 block">Program Studi</label> <!-- Changed label -->
                        <Select 
                            id="prodi"
                            v-model="form.profile.prodi_id" 
                            :options="prodis" 
                            optionLabel="nama" 
                            optionValue="id" 
                            placeholder="Pilih Prodi"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors['profile.prodi_id'] }"
                        />
                        <small class="p-error" v-if="form.errors['profile.prodi_id']">{{ form.errors['profile.prodi_id'] }}</small>
                    </div>

                    <div class="field mb-4">
                        <label for="jabatan" class="font-medium mb-2 block">Jabatan</label>
                        <InputText 
                            id="jabatan" 
                            v-model="form.profile.jabatan" 
                            placeholder="Contoh: Lektor Kepala"
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="bidang_keahlian" class="font-medium mb-2 block">Bidang Keahlian</label>
                        <InputText 
                            id="bidang_keahlian" 
                            v-model="form.profile.bidang_keahlian" 
                            placeholder="Contoh: Web Development"
                        />
                    </div>

                    <!-- Removed ToggleSwitch as it's replaced by Select in Account Info -->
                </div>
            </div>

            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="showDialog = false" />
                <Button label="Simpan" icon="pi pi-check" text @click="saveDosen" :loading="form.processing" />
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
            <div v-if="selectedDosen" class="bg-white dark:bg-gray-900">
                <!-- Header Section -->
                <div class="relative h-32 bg-gradient-to-r from-blue-600 to-indigo-700">
                    <div class="absolute -bottom-12 left-8">
                        <Avatar 
                            :label="getInitials(selectedDosen.name)" 
                            size="xlarge" 
                            shape="circle" 
                            class="w-24 h-24 text-3xl font-bold bg-white text-indigo-700 border-4 border-white shadow-lg"
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
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ selectedDosen.name }}</h2>
                            <p class="text-gray-500 dark:text-gray-400">{{ selectedDosen.profile_dosen?.jabatan || 'Dosen' }}</p>
                        </div>
                        <Tag 
                            :value="selectedDosen.is_active ? 'Active' : 'Inactive'" 
                            :severity="selectedDosen.is_active ? 'success' : 'danger'" 
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
                                    <p class="font-medium">{{ selectedDosen.email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600">
                                    <i class="pi pi-user"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Username</p>
                                    <p class="font-medium">{{ selectedDosen.username }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Details -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Profil Akademik</h3>
                            
                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600">
                                    <i class="pi pi-id-card"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">NIDN</p>
                                    <p class="font-medium">{{ selectedDosen.profile_dosen?.nidn || '-' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600">
                                    <i class="pi pi-building"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Program Studi</p>
                                    <p class="font-medium">{{ selectedDosen.profile_dosen?.prodi?.nama || '-' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                <div class="w-8 h-8 rounded-full bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-teal-600">
                                    <i class="pi pi-book"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Bidang Keahlian</p>
                                    <p class="font-medium">{{ selectedDosen.profile_dosen?.bidang_keahlian || '-' }}</p>
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
