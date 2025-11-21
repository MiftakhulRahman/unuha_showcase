<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Toolbar from 'primevue/toolbar';
import FileUpload from 'primevue/fileupload';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from '@/composables/useToast';
import ConfirmDialog from '@/components/ConfirmDialog.vue';

interface Column {
    field: string;
    header: string;
    sortable?: boolean;
    width?: string;
}

interface Props {
    title?: string;
    description?: string;
    columns: Column[];
    data: any[];
    totalRecords?: number;
    createRoute?: string;
    editRoute?: string;
    deleteRoute?: string;
    bulkDeleteRoute?: string;
    importRoute?: string;
    importAccept?: string;
    exportable?: boolean;
    sortField?: string;
    sortOrder?: number;
    rows?: number;
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    description: '',
    totalRecords: 0,
    importAccept: '.csv,.xlsx',
    exportable: true,
    sortOrder: 1,
    rows: 10,
});

const toast = useToast();
const dt = ref();
const selectedItems = ref<any[]>([]);
const showDeleteDialog = ref(false);
const showBulkDeleteDialog = ref(false);
const itemToDelete = ref<any>(null);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const onGlobalFilterChange = (event: any) => {
    filters.value.global.value = event.target.value;
};

const confirmDelete = (item: any) => {
    itemToDelete.value = item;
    showDeleteDialog.value = true;
};

const handleDelete = () => {
    if (!props.deleteRoute || !itemToDelete.value) return;
    
    router.delete(`${props.deleteRoute}/${itemToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.deleteSuccess();
            itemToDelete.value = null;
            showDeleteDialog.value = false;
        },
        onError: () => {
            toast.operationFailed('Gagal menghapus data');
        },
    });
};

const confirmBulkDelete = () => {
    if (selectedItems.value.length === 0) return;
    showBulkDeleteDialog.value = true;
};

const handleBulkDelete = () => {
    if (!props.bulkDeleteRoute || selectedItems.value.length === 0) return;
    
    const ids = selectedItems.value.map(item => item.id);
    
    router.post(props.bulkDeleteRoute, { ids }, {
        preserveScroll: true,
        onSuccess: () => {
            toast.bulkDeleteSuccess(selectedItems.value.length);
            selectedItems.value = [];
            showBulkDeleteDialog.value = false;
        },
        onError: () => {
            toast.operationFailed('Gagal menghapus data');
        },
    });
};

const exportCSV = () => {
    dt.value.exportCSV();
};

const handleImport = async (event: any) => {
    const file = event.files[0];
    if (!file || !props.importRoute) return;
    
    const formData = new FormData();
    formData.append('file', file);
    
    router.post(props.importRoute, formData, {
        preserveScroll: true,
        onSuccess: () => {
            toast.createSuccess('File diimpor');
        },
        onError: () => {
            toast.operationFailed('Gagal mengimpor file');
        },
    });
};

const openNew = () => {
    if (props.createRoute) {
        router.visit(props.createRoute);
    }
};
</script>

<template>
    <div class="card p-4">
        <!-- Title and Description moved above Toolbar -->
        <div v-if="title || description" class="mb-4">
            <h4 v-if="title" class="m-0 text-xl font-semibold">{{ title }}</h4>
            <p v-if="description" class="m-0 mt-1 text-sm text-gray-600 dark:text-gray-400">{{ description }}</p>
        </div>

        <Toolbar class="mb-6">
            <template #start>
                <slot name="toolbar-start">
                    <Button 
                        v-if="createRoute"
                        label="Tambah" 
                        icon="pi pi-plus" 
                        class="mr-2" 
                        @click="openNew" 
                    />
                </slot>
                <Button 
                    v-if="bulkDeleteRoute"
                    label="Hapus" 
                    icon="pi pi-trash" 
                    severity="danger" 
                    variant="outlined" 
                    @click="confirmBulkDelete" 
                    :disabled="!selectedItems || !selectedItems.length" 
                />
            </template>

            <template #end>
                <FileUpload 
                    v-if="importRoute"
                    mode="basic" 
                    :accept="importAccept" 
                    :maxFileSize="1000000" 
                    label="Impor" 
                    customUpload 
                    chooseLabel="Impor" 
                    class="mr-2" 
                    auto 
                    :chooseButtonProps="{ severity: 'secondary' }" 
                    @uploader="handleImport"
                />
                <Button 
                    v-if="exportable"
                    label="Ekspor" 
                    icon="pi pi-upload" 
                    severity="secondary" 
                    @click="exportCSV" 
                />
            </template>
        </Toolbar>

        <DataTable
            ref="dt"
            v-model:selection="selectedItems"
            :value="data"
            dataKey="id"
            :paginator="true"
            :rows="props.rows"
            :filters="filters"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]"
            currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} entri"
            :globalFilterFields="columns.map(c => c.field)"
            tableStyle="min-width: 50rem"
        >
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-end">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Cari..." />
                    </IconField>
                </div>
            </template>

            <Column v-if="bulkDeleteRoute" selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
            
            <Column 
                v-for="col in columns" 
                :key="col.field"
                :field="col.field" 
                :header="col.header" 
                :sortable="col.sortable !== false"
                :style="col.width ? `min-width: ${col.width}` : 'min-width: 12rem'"
            >
                <template #body="slotProps">
                    <slot :name="`cell-${col.field}`" :item="slotProps.data">
                        {{ slotProps.data[col.field] }}
                    </slot>
                </template>
            </Column>

            <Column v-if="editRoute || deleteRoute || $slots.actions" :exportable="false" style="min-width: 12rem" header="Aksi">
                <template #body="slotProps">
                    <Button 
                        v-if="editRoute"
                        icon="pi pi-pencil" 
                        variant="outlined" 
                        rounded 
                        class="mr-2" 
                        @click="router.visit(`${editRoute}/${slotProps.data.id}/edit`)" 
                    />
                    <slot name="actions" :item="slotProps.data" />
                    <Button 
                        v-if="deleteRoute"
                        icon="pi pi-trash" 
                        variant="outlined" 
                        rounded 
                        severity="danger" 
                        @click="confirmDelete(slotProps.data)" 
                    />
                </template>
            </Column>
        </DataTable>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            v-model:open="showDeleteDialog"
            title="Hapus Data"
            description="Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan."
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            variant="destructive"
            icon="delete"
            @confirm="handleDelete"
        />

        <!-- Bulk Delete Confirmation Dialog -->
        <ConfirmDialog
            v-model:open="showBulkDeleteDialog"
            title="Hapus Data Terpilih"
            :description="`Apakah Anda yakin ingin menghapus ${selectedItems.length} data yang dipilih? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Ya, Hapus Semua"
            cancel-text="Batal"
            variant="destructive"
            icon="delete"
            @confirm="handleBulkDelete"
        />
    </div>
</template>
