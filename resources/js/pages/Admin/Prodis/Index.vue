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
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

interface Prodi {
    id: number;
    nama: string;
    kode: string;
    deskripsi: string;
    is_active: boolean;
}

interface Props {
    prodis: {
        data: Prodi[];
        meta?: {
            total: number;
        };
    };
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Program Studi', href: '/admin/prodis' },
];

const columns = [
    { field: 'nama', header: 'Nama Prodi', sortable: true, filterType: 'text' as const },
    { field: 'kode', header: 'Kode', sortable: true, filterType: 'text' as const },
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

// Modal & Form Logic
const showDialog = ref(false);
const showDetailDialog = ref(false);
const editingId = ref<number | null>(null);
const selectedProdi = ref<Prodi | null>(null);

const form = useForm({
    nama: '',
    kode: '',
    deskripsi: '',
    is_active: true,
});

const createProdi = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
};

const editProdi = (prodi: Prodi) => {
    editingId.value = prodi.id;
    form.nama = prodi.nama;
    form.kode = prodi.kode;
    form.deskripsi = prodi.deskripsi || '';
    form.is_active = !!prodi.is_active;
    form.clearErrors();
    showDialog.value = true;
};

const viewProdi = (prodi: Prodi) => {
    selectedProdi.value = prodi;
    showDetailDialog.value = true;
};

const saveProdi = () => {
    if (editingId.value) {
        form.put(`/admin/prodis/${editingId.value}`, {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal memperbarui program studi');
            }
        });
    } else {
        form.post('/admin/prodis', {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal menambahkan program studi');
            }
        });
    }
};
</script>

<template>
    <Head title="Manajemen Program Studi" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="prodis.data"
                    :totalRecords="prodis.meta?.total"
                    title="Manajemen Program Studi"
                    description="Kelola data program studi yang tersedia di universitas"
                    delete-route="/admin/prodis"
                    bulk-delete-route="/admin/prodis/bulk-delete"
                    :exportable="true"
                >
                    <!-- Custom Toolbar for Add Button -->
                    <template #toolbar-start>
                        <Button 
                            label="Tambah" 
                            icon="pi pi-plus" 
                            class="mr-2" 
                            @click="createProdi" 
                        />
                    </template>
                    <!-- Status Slot -->
                    <template #cell-is_active="{ item }">
                        <Tag
                            :value="item.is_active ? 'Active' : 'Inactive'"
                            :severity="item.is_active ? 'success' : 'danger'"
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>

                    <!-- Custom Actions Slot (Edit & Show) -->
                    <template #actions="{ item }">
                        <Button
                            icon="pi pi-eye"
                            variant="outlined"
                            rounded
                            severity="info"
                            class="mr-2"
                            @click="viewProdi(item)"
                        />
                        <Button
                            icon="pi pi-pencil"
                            variant="outlined"
                            rounded
                            class="mr-2"
                            @click="editProdi(item)"
                        />
                    </template>
                </PrimeDataTable>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog
            v-model:visible="showDialog"
            :style="{ width: '500px' }"
            :header="editingId ? 'Edit Program Studi' : 'Tambah Program Studi'"
            :modal="true"
            class="p-fluid"
        >
            <div class="field mb-4">
                <label for="nama" class="font-medium mb-2 block">Nama Program Studi</label>
                <InputText
                    id="nama"
                    v-model="form.nama"
                    required="true"
                    autofocus
                    :class="{ 'p-invalid': form.errors.nama }"
                    placeholder="Contoh: Informatika"
                />
                <small class="p-error" v-if="form.errors.nama">{{ form.errors.nama }}</small>
            </div>

            <div class="field mb-4">
                <label for="kode" class="font-medium mb-2 block">Kode Prodi</label>
                <InputText
                    id="kode"
                    v-model="form.kode"
                    required="true"
                    :class="{ 'p-invalid': form.errors.kode }"
                    placeholder="Contoh: IF"
                />
                <small class="p-error" v-if="form.errors.kode">{{ form.errors.kode }}</small>
            </div>

            <div class="field mb-4">
                <label for="deskripsi" class="font-medium mb-2 block">Deskripsi</label>
                <Textarea
                    id="deskripsi"
                    v-model="form.deskripsi"
                    rows="3"
                    cols="20"
                    placeholder="Deskripsi program studi"
                    class="w-full"
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
                <Button label="Simpan" icon="pi pi-check" text @click="saveProdi" :loading="form.processing" />
            </template>
        </Dialog>

        <!-- Detail Dialog -->
        <Dialog
            v-model:visible="showDetailDialog"
            :style="{ width: '500px' }"
            header="Detail Program Studi"
            :modal="true"
            class="p-fluid"
        >
            <div v-if="selectedProdi" class="grid grid-cols-1 gap-4">
                <div class="flex justify-between border-b pb-2">
                    <span class="font-bold">Nama:</span>
                    <span>{{ selectedProdi.nama }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="font-bold">Kode:</span>
                    <span>{{ selectedProdi.kode }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="font-bold">Status:</span>
                    <Tag :value="selectedProdi.is_active ? 'Active' : 'Inactive'" :severity="selectedProdi.is_active ? 'success' : 'danger'" />
                </div>
                <div class="flex flex-col border-b pb-2">
                    <span class="font-bold mb-1">Deskripsi:</span>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ selectedProdi.deskripsi || '-' }}</p>
                </div>
            </div>
            <template #footer>
                <Button label="Tutup" icon="pi pi-times" text @click="showDetailDialog = false" />
            </template>
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
