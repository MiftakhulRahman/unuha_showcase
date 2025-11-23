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
import ToggleSwitch from 'primevue/toggleswitch';
import InputSlug from '@/components/InputSlug.vue';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

interface Kategori {
    id: number;
    nama: string;
    slug: string;
    deskripsi?: string;
    icon?: string;
    is_active: boolean;
}

interface Props {
    kategoris: {
        data: Kategori[];
        meta?: {
            total: number;
        };
    };
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kategori', href: '/admin/kategoris' },
];

const columns = [
    { field: 'nama', header: 'Nama Kategori', sortable: true, filterType: 'text' as const },
    { field: 'slug', header: 'Slug', sortable: true, filterType: 'text' as const },
    { field: 'deskripsi', header: 'Deskripsi', sortable: false, filterType: 'text' as const },
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

const getStatusSeverity = (isActive: boolean) => {
    return isActive ? 'success' : 'danger';
};

// Modal & Form Logic
const showDialog = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    nama: '',
    slug: '',
    deskripsi: '',
    icon: '',
    is_active: true,
});

const openNew = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
};

const editKategori = (kategori: Kategori) => {
    isEditing.value = true;
    editingId.value = kategori.id;
    form.nama = kategori.nama;
    form.slug = kategori.slug;
    form.deskripsi = kategori.deskripsi || '';
    form.icon = kategori.icon || '';
    form.is_active = !!kategori.is_active; // Ensure boolean
    form.clearErrors();
    showDialog.value = true;
};

const saveKategori = () => {
    if (isEditing.value && editingId.value) {
        form.put(`/admin/kategoris/${editingId.value}`, {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal memperbarui kategori');
            }
        });
    } else {
        form.post('/admin/kategoris', {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal menambahkan kategori');
            }
        });
    }
};
</script>

<template>
    <Head title="Manajemen Kategori" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="kategoris.data"
                    :totalRecords="kategoris.meta?.total"
                    title="Manajemen Kategori"
                    description="Kelola kategori project (Skripsi, PKM, dll)"
                    delete-route="/admin/kategoris"
                    bulk-delete-route="/admin/kategoris/bulk-delete"
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

                    <!-- Status Slot -->
                    <template #cell-is_active="{ item }">
                        <Tag 
                            :value="item.is_active ? 'Aktif' : 'Tidak Aktif'" 
                            :severity="getStatusSeverity(item.is_active)" 
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>

                    <!-- Custom Actions Slot (Edit) -->
                    <template #actions="{ item }">
                        <Button 
                            icon="pi pi-pencil" 
                            variant="outlined" 
                            rounded 
                            class="mr-2" 
                            @click="editKategori(item)" 
                        />
                    </template>
                </PrimeDataTable>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog 
            v-model:visible="showDialog" 
            :style="{ width: '450px' }" 
            :header="isEditing ? 'Edit Kategori' : 'Tambah Kategori'" 
            :modal="true" 
            class="p-fluid"
        >
            <div class="field mb-4">
                <label for="nama" class="font-medium mb-2 block">Nama Kategori</label>
                <InputText 
                    id="nama" 
                    v-model="form.nama" 
                    required="true" 
                    autofocus 
                    :class="{ 'p-invalid': form.errors.nama }" 
                    placeholder="Contoh: Web Development"
                />
                <small class="p-error" v-if="form.errors.nama">{{ form.errors.nama }}</small>
            </div>

            <div class="field mb-4">
                <label for="slug" class="font-medium mb-2 block">Slug</label>
                <InputSlug 
                    id="slug"
                    v-model="form.slug"
                    :source="form.nama"
                    :class="{ 'p-invalid': form.errors.slug }"
                />
                <small class="p-error" v-if="form.errors.slug">{{ form.errors.slug }}</small>
            </div>

            <div class="field mb-4">
                <label for="deskripsi" class="font-medium mb-2 block">Deskripsi</label>
                <Textarea 
                    id="deskripsi" 
                    v-model="form.deskripsi" 
                    rows="3" 
                    cols="20" 
                    placeholder="Deskripsi singkat kategori"
                />
            </div>

            <div class="field mb-4">
                <label for="icon" class="font-medium mb-2 block">Icon (Emoji/Class)</label>
                <InputText 
                    id="icon" 
                    v-model="form.icon" 
                    placeholder="Contoh: 🌐 atau pi pi-globe"
                />
            </div>

            <div class="field mb-4 flex items-center gap-2">
                <ToggleSwitch v-model="form.is_active" inputId="is_active" />
                <label for="is_active" class="font-medium">Status Aktif</label>
            </div>

            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="showDialog = false" />
                <Button label="Simpan" icon="pi pi-check" text @click="saveKategori" :loading="form.processing" />
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
