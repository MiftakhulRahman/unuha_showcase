<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Toolbar from 'primevue/toolbar';
import FileUpload from 'primevue/fileupload';
import Select from 'primevue/select';
import SplitButton from 'primevue/splitbutton';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { useToast } from '@/composables/useToast';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import Toast from 'primevue/toast';
import { useToast as usePrimeToast } from 'primevue/usetoast';
import * as XLSX from 'xlsx';

interface Column {
    field: string;
    header: string;
    sortable?: boolean;
    width?: string;
    filterType?: 'text' | 'select' | 'boolean';
    filterOptions?: { label: string; value: any }[];
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
const primeToast = usePrimeToast();
const dt = ref();
const selectedItems = ref<any[]>([]);
const showDeleteDialog = ref(false);
const showBulkDeleteDialog = ref(false);
const itemToDelete = ref<any>(null);
const isRefreshing = ref(false);

// Sorting state
const sortField = ref<string | undefined>(undefined);
const sortOrder = ref<number | undefined>(undefined);

// Filter state
const showFilters = ref(false);
const filters = ref<Record<string, any>>({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const hasFilterableColumns = computed(() => {
    return props.columns.some(col => col.filterType);
});

const hasActiveFilters = computed(() => {
    return Object.keys(filters.value).some(key => {
        if (key === 'global') return false;
        const filter = filters.value[key];
        if (filter.constraints) {
            return filter.constraints.some((c: any) => c.value !== null && c.value !== '');
        }
        return filter.value !== null && filter.value !== '';
    });
});

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

onMounted(() => {
    initializeColumnFilters();
});

const initializeColumnFilters = () => {
    props.columns.forEach((col) => {
        if (!filters.value[col.field]) {
            // Untuk boolean dan select, gunakan EQUALS mode
            if (col.filterType === 'boolean' || col.filterType === 'select') {
                filters.value[col.field] = { value: null, matchMode: FilterMatchMode.EQUALS };
            } else {
                // Untuk text, gunakan STARTS_WITH
                filters.value[col.field] = {
                    operator: FilterOperator.AND,
                    constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }],
                };
            }
        }
    });
};

const onGlobalFilterChange = (event: any) => {
    filters.value.global.value = event.target.value;
};

const clearFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    };
    props.columns.forEach((col) => {
        if (col.filterType === 'boolean' || col.filterType === 'select') {
            filters.value[col.field] = { value: null, matchMode: FilterMatchMode.EQUALS };
        } else {
            filters.value[col.field] = {
                operator: FilterOperator.AND,
                constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }],
            };
        }
    });
    toast.info('Filter telah dihapus');
};

const refreshData = () => {
    isRefreshing.value = true;
    router.reload({
        onFinish: () => {
            isRefreshing.value = false;
            toast.success('Data berhasil dimuat ulang');
        },
    });
};

const handleSort = (event: any) => {
    sortField.value = event.sortField || undefined;
    sortOrder.value = event.sortOrder || undefined;
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
            // toast.deleteSuccess(); // Handled by AppLayout flash watcher
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
            // toast.bulkDeleteSuccess(selectedItems.value.length); // Handled by AppLayout flash watcher
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

const exportExcel = () => {
    const dataToExport = props.data.map(item => {
        const exportItem: any = {};
        props.columns.forEach(col => {
            exportItem[col.header] = item[col.field];
        });
        return exportItem;
    });

    const worksheet = XLSX.utils.json_to_sheet(dataToExport);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Data");
    XLSX.writeFile(workbook, "data_export.xlsx");
};

const exportItems = [
    {
        label: 'CSV',
        icon: 'pi pi-file',
        command: () => {
            exportCSV();
        }
    },
    {
        label: 'Excel',
        icon: 'pi pi-file-excel',
        command: () => {
            exportExcel();
        }
    }
];

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
        <Toast position="top-right" />
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
                    class="responsive-label" 
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
                <SplitButton 
                    v-if="exportable"
                    label="Ekspor" 
                    icon="pi pi-download" 
                    severity="secondary" 
                    :model="exportItems"
                    @click="exportCSV"
                    class="responsive-label"
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
            :sortField="sortField"
            :sortOrder="sortOrder"
            removableSort
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]"
            currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} entri"
            :globalFilterFields="columns.map(c => c.field)"
            tableStyle="min-width: 50rem"
            @sort="handleSort"
        >
            <template #header>
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <div class="flex gap-2 items-center">
                            <Button 
                                icon="pi pi-refresh" 
                                rounded 
                                text 
                                severity="secondary"
                                :loading="isRefreshing"
                                @click="refreshData"
                                v-tooltip="'Muat ulang data'"
                            />
                            <Button 
                                v-if="hasFilterableColumns"
                                :icon="showFilters ? 'pi pi-filter-slash' : 'pi pi-filter'" 
                                rounded 
                                text 
                                :severity="showFilters ? 'primary' : 'secondary'"
                                @click="toggleFilters"
                                v-tooltip="'Toggle Filter'"
                            />
                            <Button 
                                v-if="hasActiveFilters"
                                icon="pi pi-times" 
                                rounded 
                                text 
                                severity="danger"
                                @click="clearFilters"
                                v-tooltip="'Hapus semua filter'"
                            />
                        </div>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Cari..." />
                        </IconField>
                    </div>

                    <!-- Custom Filter Panel -->
                    <div v-if="showFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out">
                        <div v-for="col in columns.filter(c => c.filterType)" :key="col.field" class="flex flex-col gap-2">
                            <label :for="`filter-${col.field}`" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ col.header }}</label>
                            
                            <!-- Text Filter -->
                            <InputText 
                                v-if="col.filterType === 'text'"
                                :id="`filter-${col.field}`"
                                v-model="filters[col.field].constraints[0].value" 
                                type="text" 
                                :placeholder="`Cari ${col.header.toLowerCase()}...`" 
                                class="w-full p-inputtext-sm"
                            />
                            
                            <!-- Select Filter -->
                            <Select 
                                v-else-if="col.filterType === 'select'"
                                :inputId="`filter-${col.field}`"
                                v-model="filters[col.field].value"
                                :options="col.filterOptions || []"
                                optionLabel="label"
                                optionValue="value"
                                :placeholder="`Pilih ${col.header.toLowerCase()}...`"
                                class="w-full p-select-sm"
                                showClear
                            />

                            <!-- Boolean Filter -->
                            <Select 
                                v-else-if="col.filterType === 'boolean'"
                                :inputId="`filter-${col.field}`"
                                v-model="filters[col.field].value"
                                :options="[
                                    { label: 'Aktif', value: true },
                                    { label: 'Tidak Aktif', value: false }
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                :placeholder="`Pilih ${col.header.toLowerCase()}...`"
                                class="w-full p-select-sm"
                                showClear
                            />
                        </div>
                    </div>
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

            <Column v-if="editRoute || deleteRoute || $slots.actions" :exportable="false" style="min-width: 8rem; width: 8rem; text-align: center" header="Aksi" class="text-center">
                <template #body="slotProps">
                    <div class="flex justify-center gap-2">
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
                    </div>
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

<style scoped>
/* Hide label on mobile for SplitButton with responsive-label class */
:deep(.responsive-label .p-button-label) {
    display: none;
}

@media (min-width: 640px) {
    :deep(.responsive-label .p-button-label) {
        display: block;
    }
}
</style>
