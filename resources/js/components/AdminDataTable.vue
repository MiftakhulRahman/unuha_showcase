<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import { FilterMatchMode } from '@primevue/core/api';

// PrimeVue Components
import DataTable, { type DataTablePageEvent } from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Tag from 'primevue/tag';

// Your existing custom dialog
import ConfirmDialog from '@/components/ConfirmDialog.vue';

// Props definition remains similar
interface ColumnDef {
    key: string;
    label: string;
    sortable?: boolean;
    width?: string;
}

interface Props {
    columns: ColumnDef[];
    data: any[];
    links?: any; // For pagination if needed, though PrimeVue handles it
    meta?: {
        current_page: number;
        from: number;
        to: number;
        total: number;
        per_page: number;
    };
    bulkDeleteRoute?: string;
    editRoute?: string;
    deleteRoute?: string;
}

const props = defineProps<Props>();

const toast = useToast();

// --- State Management ---
const dt = ref();
const selectedItems = ref<any[]>([]);
const showDeleteDialog = ref(false);
const showBulkDeleteDialog = ref(false);
const itemToDelete = ref<any>(null);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// --- Computed Properties ---
const canDeleteSelected = computed(() => selectedItems.value.length > 0);

// --- Methods for Actions ---

// Edit action
const handleEdit = (item: any) => {
    if (!props.editRoute) return;
    router.get(`${props.editRoute}/${item.id}/edit`);
};

// Single item deletion
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
            selectedItems.value = []; // Clear selection
        },
        onError: () => {
            toast.operationFailed('Gagal menghapus data');
        },
    });
};

// Bulk deletion
const confirmBulkDelete = () => {
    showBulkDeleteDialog.value = true;
};

const handleBulkDelete = () => {
    if (!props.bulkDeleteRoute || !canDeleteSelected.value) return;

    const ids = selectedItems.value.map((item) => item.id);
    router.post(props.bulkDeleteRoute, { ids }, {
        preserveScroll: true,
        onSuccess: () => {
            toast.bulkDeleteSuccess(ids.length);
            selectedItems.value = [];
        },
        onError: () => {
            toast.operationFailed('Gagal menghapus data');
        },
    });
};

// --- Helper Functions ---

// For deeply nested properties in columns
const renderCellValue = (item: any, key: string) => {
    return key.split('.').reduce((acc, part) => acc && acc[part], item);
};

// For status tag styling
const getStatusSeverity = (isActive: boolean) => {
    return isActive ? 'success' : 'danger';
};

// For pagination handling with Inertia
const onPage = (event: DataTablePageEvent) => {
    const page = event.page + 1;
    router.get(window.location.pathname, { page: page, per_page: event.rows }, { preserveState: true });
}

</script>

<template>
    <div class="card">
        <Toolbar class="mb-4">
            <template #start>
                <!-- Add New button is handled in the parent page -->
                 <Button
                    label="Hapus Terpilih"
                    icon="pi pi-trash"
                    severity="danger"
                    @click="confirmBulkDelete"
                    :disabled="!canDeleteSelected"
                />
            </template>
        </Toolbar>

        <DataTable
            ref="dt"
            v-model:selection="selectedItems"
            v-model:filters="filters"
            :value="data"
            dataKey="id"
            :paginator="meta ? meta.total > (meta.per_page ?? 10) : false"
            :rows="meta?.per_page ?? 10"
            :totalRecords="meta?.total"
            :lazy="true"
            @page="onPage"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[10, 25, 50]"
            currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} data"
            :globalFilterFields="columns.map(c => c.key)"
            tableClass="min-w-full"
        >
            <template #header>
                <div class="flex justify-end">
                    <IconField iconPosition="left">
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Cari..." />
                    </IconField>
                </div>
            </template>

            <template #empty>
                <div class="text-center py-8">
                    Tidak ada data ditemukan.
                </div>
            </template>

            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>

            <!-- Dynamic Columns -->
            <Column
                v-for="col in columns"
                :key="col.key"
                :field="col.key"
                :header="col.label"
                :sortable="col.sortable !== false"
                :style="col.width ? `width: ${col.width}` : ''"
            >
                <template #body="{ data: item }">
                     <!-- Special handling for status column -->
                    <template v-if="col.key === 'is_active'">
                         <slot :name="`cell-${col.key}`" :item="item">
                            <Tag :value="item.is_active ? 'Aktif' : 'Tidak Aktif'" :severity="getStatusSeverity(item.is_active)" />
                        </slot>
                    </template>
                     <!-- Default cell render -->
                    <template v-else>
                        <slot :name="`cell-${col.key}`" :item="item">
                            {{ renderCellValue(item, col.key) }}
                        </slot>
                    </template>
                </template>
            </Column>

            <!-- Actions Column -->
            <Column header="Aksi" headerStyle="width: 10rem; text-align: center" bodyStyle="text-align: center; overflow: visible">
                <template #body="{ data: item }">
                    <div class="flex gap-2 justify-center">
                         <Button
                            v-if="editRoute"
                            icon="pi pi-pencil"
                            rounded
                            text
                            severity="info"
                            @click="handleEdit(item)"
                            v-tooltip.top="'Edit'"
                        />
                        <Button
                            v-if="deleteRoute"
                            icon="pi pi-trash"
                            rounded
                            text
                            severity="danger"
                            @click="confirmDelete(item)"
                            v-tooltip.top="'Hapus'"
                        />
                    </div>
                </template>
            </Column>
        </DataTable>

         <!-- Confirmation Dialogs using your existing component -->
        <ConfirmDialog
            v-model:open="showDeleteDialog"
            title="Hapus Data"
            description="Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan."
            confirm-text="Ya, Hapus"
            @confirm="handleDelete"
            variant="destructive"
        />

        <ConfirmDialog
            v-model:open="showBulkDeleteDialog"
            title="Hapus Data Terpilih"
            :description="`Apakah Anda yakin ingin menghapus ${selectedItems.length} data terpilih?`"
            confirm-text="Ya, Hapus Semua"
            @confirm="handleBulkDelete"
            variant="destructive"
        />
    </div>
</template>