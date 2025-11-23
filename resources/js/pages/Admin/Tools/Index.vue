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
import ToggleSwitch from 'primevue/toggleswitch';
import InputSlug from '@/components/InputSlug.vue';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

interface Tool {
    id: number;
    nama: string;
    slug: string;
    deskripsi?: string;
    icon?: string;
    color?: string;
    kategori_tool?: string;
    is_active: boolean;
}

interface Props {
    tools: {
        data: Tool[];
        meta?: {
            total: number;
        };
    };
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tools/Teknologi', href: '/admin/tools' },
];

const columns = [
    { field: 'nama', header: 'Nama Tool', sortable: true, filterType: 'text' as const },
    { field: 'slug', header: 'Slug', sortable: true, filterType: 'text' as const },
    { 
        field: 'kategori_tool', 
        header: 'Kategori', 
        sortable: true, 
        filterType: 'select' as const,
        filterOptions: [
            { label: 'Language', value: 'language' },
            { label: 'Framework', value: 'framework' },
            { label: 'Library', value: 'library' },
            { label: 'Database', value: 'database' },
            { label: 'Platform', value: 'platform' },
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

const kategoriToolOptions = [
    { label: 'Language', value: 'language' },
    { label: 'Framework', value: 'framework' },
    { label: 'Library', value: 'library' },
    { label: 'Database', value: 'database' },
    { label: 'Platform', value: 'platform' },
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
    color: '',
    kategori_tool: null as string | null,
    is_active: true,
});

const openNew = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
};

const editTool = (tool: Tool) => {
    isEditing.value = true;
    editingId.value = tool.id;
    form.nama = tool.nama;
    form.slug = tool.slug;
    form.deskripsi = tool.deskripsi || '';
    form.icon = tool.icon || '';
    form.color = tool.color || '';
    form.kategori_tool = tool.kategori_tool || null;
    form.is_active = !!tool.is_active;
    form.clearErrors();
    showDialog.value = true;
};

const saveTool = () => {
    if (isEditing.value && editingId.value) {
        form.put(`/admin/tools/${editingId.value}`, {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal memperbarui tool');
            }
        });
    } else {
        form.post('/admin/tools', {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal menambahkan tool');
            }
        });
    }
};
</script>

<template>
    <Head title="Manajemen Tools/Teknologi" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="tools.data"
                    :totalRecords="tools.meta?.total"
                    title="Manajemen Tools/Teknologi"
                    description="Kelola semua tools dan teknologi dalam sistem"
                    delete-route="/admin/tools"
                    bulk-delete-route="/admin/tools/bulk-delete"
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

                    <!-- Kategori Slot -->
                    <template #cell-kategori_tool="{ item }">
                        <span v-if="item.kategori_tool" class="capitalize">{{ item.kategori_tool }}</span>
                        <span v-else class="text-gray-400">-</span>
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
                            @click="editTool(item)" 
                        />
                    </template>
                </PrimeDataTable>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog 
            v-model:visible="showDialog" 
            :style="{ width: '500px' }" 
            :header="isEditing ? 'Edit Tool' : 'Tambah Tool'" 
            :modal="true" 
            class="p-fluid"
        >
            <div class="field mb-4">
                <label for="nama" class="font-medium mb-2 block">Nama Tool</label>
                <InputText 
                    id="nama" 
                    v-model="form.nama" 
                    required="true" 
                    autofocus 
                    :class="{ 'p-invalid': form.errors.nama }" 
                    placeholder="Contoh: React"
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
                <label for="kategori_tool" class="font-medium mb-2 block">Kategori Tool</label>
                <Select 
                    id="kategori_tool"
                    v-model="form.kategori_tool" 
                    :options="kategoriToolOptions" 
                    optionLabel="label" 
                    optionValue="value" 
                    placeholder="Pilih Kategori" 
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.kategori_tool }"
                />
                <small class="p-error" v-if="form.errors.kategori_tool">{{ form.errors.kategori_tool }}</small>
            </div>

            <div class="field mb-4">
                <label for="deskripsi" class="font-medium mb-2 block">Deskripsi</label>
                <Textarea 
                    id="deskripsi" 
                    v-model="form.deskripsi" 
                    rows="3" 
                    cols="20" 
                    placeholder="Deskripsi singkat tool"
                />
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="field">
                    <label for="icon" class="font-medium mb-2 block">Icon (Emoji/Class)</label>
                    <InputText 
                        id="icon" 
                        v-model="form.icon" 
                        placeholder="Contoh: ⚛️"
                    />
                </div>
                <div class="field">
                    <label for="color" class="font-medium mb-2 block">Warna (Hex/Class)</label>
                    <InputText 
                        id="color" 
                        v-model="form.color" 
                        placeholder="Contoh: #61dafb"
                    />
                </div>
            </div>

            <div class="field mb-4 flex items-center gap-2">
                <ToggleSwitch v-model="form.is_active" inputId="is_active" />
                <label for="is_active" class="font-medium">Status Aktif</label>
            </div>

            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="showDialog = false" />
                <Button label="Simpan" icon="pi pi-check" text @click="saveTool" :loading="form.processing" />
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

