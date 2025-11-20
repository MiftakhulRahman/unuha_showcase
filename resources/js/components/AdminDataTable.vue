<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Eye, Edit, Trash2, RotateCcw, ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    width?: string;
}

interface Props {
    title: string;
    description?: string;
    columns: Column[];
    data: any[];
    links?: any;
    onEdit?: (item: any) => void;
    onDelete?: (item: any) => void;
    onResetPassword?: (item: any) => void;
    onToggleStatus?: (item: any) => void;
    showCheckbox?: boolean;
    bulkDeleteRoute?: string;
    editRoute?: string;
    deleteRoute?: string;
    resetPasswordRoute?: string;
    toggleStatusRoute?: string;
    customActions?: Array<{
        label: string;
        icon?: any;
        onClick: (item: any) => void;
        color?: string;
    }>;
}

const props = withDefaults(defineProps<Props>(), {
    showCheckbox: true,
    description: '',
});

const selectedIds = ref<number[]>([]);
const allSelected = ref(false);

const canDeleteSelected = computed(() => selectedIds.value.length > 0);

const toggleSelectAll = (checked: boolean) => {
    allSelected.value = checked;
    if (checked) {
        selectedIds.value = props.data.map((item) => item.id);
    } else {
        selectedIds.value = [];
    }
};

const toggleSelect = (id: number) => {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(id);
    }
    allSelected.value = selectedIds.value.length === props.data.length;
};

const isSelected = (id: number) => selectedIds.value.includes(id);

const handleBulkDelete = () => {
    if (!props.bulkDeleteRoute || selectedIds.value.length === 0) return;
    
    if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.value.length} item?`)) {
        router.post(props.bulkDeleteRoute, { ids: selectedIds.value });
    }
};

const renderCellValue = (item: any, key: string) => {
    const keys = key.split('.');
    let value = item;
    for (const k of keys) {
        value = value?.[k];
    }
    return value;
};
</script>

<template>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-2xl font-bold">{{ title }}</h2>
                <p v-if="description" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ description }}
                </p>
            </div>
            <div v-if="canDeleteSelected" class="flex gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ selectedIds.length }} dipilih
                </span>
                <Button
                    variant="destructive"
                    size="sm"
                    @click="handleBulkDelete"
                >
                    <Trash2 class="mr-2 h-4 w-4" />
                    Hapus Pilihan
                </Button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
            <table class="w-full">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                    <tr>
                        <th v-if="showCheckbox" class="px-6 py-3">
                            <Checkbox
                                :checked="allSelected"
                                @update:checked="toggleSelectAll"
                            />
                        </th>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            class="px-6 py-3 text-left text-sm font-semibold"
                            :style="{ width: column.width }"
                        >
                            {{ column.label }}
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in data"
                        :key="item.id"
                        class="border-t border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                    >
                        <td v-if="showCheckbox" class="px-6 py-4">
                            <Checkbox
                                :checked="isSelected(item.id)"
                                @update:checked="toggleSelect(item.id)"
                            />
                        </td>
                        <td
                            v-for="column in columns"
                            :key="`${item.id}-${column.key}`"
                            class="px-6 py-4"
                        >
                            <slot :name="`cell-${column.key}`" :item="item">
                                {{ renderCellValue(item, column.key) }}
                            </slot>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                <slot name="actions" :item="item">
                                    <Link
                                        v-if="editRoute"
                                        :href="`${editRoute}/${item.id}`"
                                        as="button"
                                    >
                                        <Button variant="outline" size="sm">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Link
                                        v-if="editRoute"
                                        :href="`${editRoute}/${item.id}/edit`"
                                        as="button"
                                    >
                                        <Button variant="outline" size="sm">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Link
                                        v-if="resetPasswordRoute"
                                        :href="resetPasswordRoute.replace('{id}', item.id)"
                                        method="post"
                                        as="button"
                                    >
                                        <Button variant="outline" size="sm" class="text-yellow-600">
                                            <RotateCcw class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Link
                                        v-if="deleteRoute"
                                        :href="`${deleteRoute}/${item.id}`"
                                        method="delete"
                                        as="button"
                                    >
                                        <Button variant="outline" size="sm" class="text-red-600">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <template v-for="action in customActions" :key="action.label">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            :class="{ [action.color || '']: true }"
                                            @click="action.onClick(item)"
                                        >
                                            <component v-if="action.icon" :is="action.icon" class="h-4 w-4" />
                                            <span v-else>{{ action.label }}</span>
                                        </Button>
                                    </template>
                                </slot>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="data.length === 0">
                        <td :colspan="columns.length + (showCheckbox ? 2 : 1)" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="links && links.length > 1" class="flex items-center justify-between gap-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan data
            </div>
            <div class="flex gap-1">
                <template v-for="link in links" :key="link.label">
                    <Link
                        v-if="!link.active && link.url"
                        :href="link.url"
                        :class="[
                            'rounded border border-gray-200 px-3 py-2 text-sm transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800',
                            link.label.includes('Next') ? 'ml-auto' : '',
                        ]"
                    >
                        <template v-if="link.label.includes('Previous')">
                            <ChevronLeft class="h-4 w-4" />
                        </template>
                        <template v-else-if="link.label.includes('Next')">
                            <ChevronRight class="h-4 w-4" />
                        </template>
                        <template v-else>
                            {{ link.label }}
                        </template>
                    </Link>
                    <button
                        v-else
                        :class="[
                            'rounded border px-3 py-2 text-sm',
                            link.active
                                ? 'border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-900 dark:text-blue-100'
                                : 'border-gray-200 text-gray-400 dark:border-gray-700',
                        ]"
                        disabled
                    >
                        <template v-if="link.label.includes('Previous')">
                            <ChevronLeft class="h-4 w-4" />
                        </template>
                        <template v-else-if="link.label.includes('Next')">
                            <ChevronRight class="h-4 w-4" />
                        </template>
                        <template v-else>
                            {{ link.label }}
                        </template>
                    </button>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Empty for now - using Tailwind CSS */
</style>
