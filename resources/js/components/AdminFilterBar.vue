<script setup lang="ts">
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search, X, ChevronDown } from 'lucide-vue-next';

interface FilterOption {
    key: string;
    label: string;
    type: 'text' | 'select' | 'checkbox';
    placeholder?: string;
    options?: { label: string; value: string | number }[];
}

interface Props {
    filters: FilterOption[];
    currentFilters: Record<string, any>;
    searchPlaceholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    searchPlaceholder: 'Cari...',
});

const page = usePage();
const searchQuery = ref(props.currentFilters.search || '');
const filterValues = ref<Record<string, any>>(
    props.currentFilters || {}
);
const showFilters = ref(false);

const handleSearch = () => {
    const params = {
        search: searchQuery.value || undefined,
        ...Object.fromEntries(
            Object.entries(filterValues.value).filter(([_, v]) => v)
        ),
    };
    
    router.get(page.url, params, { preserveState: true });
};

const handleFilterChange = (key: string, value: any) => {
    filterValues.value[key] = value;
    handleSearch();
};

const resetFilters = () => {
    searchQuery.value = '';
    filterValues.value = {};
    router.get(page.url, {}, { preserveState: true });
};

const hasActiveFilters = () => {
    return (
        searchQuery.value ||
        Object.values(filterValues.value).some((v) => v)
    );
};
</script>

<template>
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="flex flex-1 gap-2">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <Input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="searchPlaceholder"
                        class="pl-10"
                        @keyup.enter="handleSearch"
                    />
                </div>
                <Button @click="handleSearch">
                    <Search class="h-4 w-4" />
                </Button>
            </div>
            <Button
                variant="outline"
                class="gap-2"
                @click="showFilters = !showFilters"
            >
                <ChevronDown
                    class="h-4 w-4 transition-transform"
                    :class="{ 'rotate-180': showFilters }"
                />
                Filter
            </Button>
            <Button
                v-if="hasActiveFilters()"
                variant="outline"
                class="text-red-600"
                @click="resetFilters"
            >
                <X class="h-4 w-4" />
                Reset
            </Button>
        </div>

        <!-- Filter Panel -->
        <div v-if="showFilters" class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="filter in filters" :key="filter.key" class="space-y-2">
                    <label class="text-sm font-medium">{{ filter.label }}</label>
                    
                    <template v-if="filter.type === 'select'">
                        <select
                            :value="filterValues[filter.key] || ''"
                            @change="
                                handleFilterChange(filter.key, ($event.target as HTMLSelectElement).value)
                            "
                            class="w-full rounded border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                        >
                            <option value="">{{ filter.placeholder || 'Pilih...' }}</option>
                            <option
                                v-for="option in filter.options"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </template>

                    <template v-else-if="filter.type === 'checkbox'">
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                :checked="filterValues[filter.key]"
                                @change="
                                    handleFilterChange(filter.key, ($event.target as HTMLInputElement).checked)
                                "
                                class="h-4 w-4 rounded"
                            />
                            <span class="text-sm">{{ filter.placeholder }}</span>
                        </label>
                    </template>

                    <template v-else>
                        <Input
                            :value="filterValues[filter.key] || ''"
                            type="text"
                            :placeholder="filter.placeholder"
                            @input="
                                handleFilterChange(filter.key, ($event.target as HTMLInputElement).value)
                            "
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Empty for now - using Tailwind CSS */
</style>
