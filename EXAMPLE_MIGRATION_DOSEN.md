# Migration Example: AdminDataTable → PrimeDataTable

Ini adalah example bagaimana migrate existing Dosen Index page dari AdminDataTable ke PrimeDataTable.

## Current Code (AdminDataTable)

```vue
<script setup lang="ts">
import AdminDataTable from '@/components/AdminDataTable.vue';

const columns = [
    { key: 'name', label: 'Nama' },
    { key: 'email', label: 'Email' },
    { key: 'profile_dosen.nidn', label: 'NIDN' },
    { key: 'profile_dosen.prodi.nama', label: 'Prodi' },
    { key: 'is_active', label: 'Status' },
];

const getStatusBadge = (isActive: boolean) => {
    return isActive
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
};
</script>

<template>
    <AdminDataTable
        title=""
        :columns="columns"
        :data="dosen.data"
        :links="dosen.links"
        bulk-delete-route="/admin/dosen/bulk-delete"
        edit-route="/admin/dosen"
        delete-route="/admin/dosen"
    >
        <template #cell-is_active="{ item }">
            <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getStatusBadge(item.is_active)]">
                {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
            </span>
        </template>
    </AdminDataTable>
</template>
```

## Migrated Code (PrimeDataTable)

### Langkah 1: Update Imports

```vue
<script setup lang="ts">
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Tag from 'primevue/tag';  // NEW: Untuk status badge yang lebih bagus

// Remove: AdminDataTable
// Remove: AdminFilterBar (optional, PrimeDataTable punya built-in search)
</script>
```

### Langkah 2: Update Column Definition

**PrimeDataTable gunakan `field` dan `header` bukannya `key` dan `label`:**

```typescript
// Before
const columns = [
    { key: 'name', label: 'Nama' },
    { key: 'email', label: 'Email' },
    { key: 'profile_dosen.nidn', label: 'NIDN' },
    { key: 'profile_dosen.prodi.nama', label: 'Prodi' },
    { key: 'is_active', label: 'Status' },
];

// After
const columns = [
    { field: 'name', header: 'Nama', sortable: true },
    { field: 'email', header: 'Email', sortable: true },
    { field: 'profile_dosen.nidn', header: 'NIDN', sortable: true },
    { field: 'profile_dosen.prodi.nama', header: 'Prodi', sortable: true },
    { field: 'is_active', header: 'Status', sortable: true },
];
```

### Langkah 3: Update Status Helper

```typescript
// Before (CSS class string)
const getStatusBadge = (isActive: boolean) => {
    return isActive
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
};

// After (PrimeVue severity prop)
const getStatusSeverity = (isActive: boolean) => {
    return isActive ? 'success' : 'danger';
};
```

### Langkah 4: Update Template

```vue
<!-- Before -->
<AdminDataTable
    title=""
    :columns="columns"
    :data="dosen.data"
    :links="dosen.links"
    bulk-delete-route="/admin/dosen/bulk-delete"
    edit-route="/admin/dosen"
    delete-route="/admin/dosen"
>
    <template #cell-is_active="{ item }">
        <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getStatusBadge(item.is_active)]">
            {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
        </span>
    </template>
</AdminDataTable>

<!-- After -->
<PrimeDataTable
    title="Manajemen Dosen"
    description="Kelola data semua dosen dalam sistem"
    :columns="columns"
    :data="dosen.data"
    create-route="/admin/dosen/create"
    edit-route="/admin/dosen"
    delete-route="/admin/dosen"
    bulk-delete-route="/admin/dosen/bulk-delete"
    :exportable="true"
>
    <template #cell-is_active="{ item }">
        <Tag 
            :value="item.is_active ? 'Aktif' : 'Tidak Aktif'" 
            :severity="getStatusSeverity(item.is_active)"
        />
    </template>
</PrimeDataTable>
```

## Complete Migrated File

```vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import Tag from 'primevue/tag';
import { Plus } from 'lucide-vue-next';

interface Dosen {
    id: number;
    name: string;
    email: string;
    username: string;
    is_active: boolean;
    profile_dosen?: {
        nidn: string;
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
            from: number;
            to: number;
            total: number;
        };
    };
    prodis: Prodi[];
    filters: {
        search?: string;
        prodi_id?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dosen', href: '/admin/dosen' },
];

const columns = [
    { field: 'name', header: 'Nama', sortable: true },
    { field: 'email', header: 'Email', sortable: true },
    { field: 'profile_dosen.nidn', header: 'NIDN', sortable: true },
    { field: 'profile_dosen.prodi.nama', header: 'Prodi', sortable: true },
    { field: 'is_active', header: 'Status', sortable: true },
];

const getStatusSeverity = (isActive: boolean) => {
    return isActive ? 'success' : 'danger';
};
</script>

<template>
    <Head title="Manajemen Dosen" />
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-950 dark:to-gray-900">
            <div class="mx-auto max-w-7xl space-y-6 p-6">
                <!-- Breadcrumb -->
                <div class="flex items-center justify-between">
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </div>

                <!-- Header Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="space-y-1.5">
                            <h1 class="text-2xl font-bold tracking-tight">Manajemen Dosen</h1>
                            <p class="text-sm text-muted-foreground">
                                Kelola data semua dosen dalam sistem
                            </p>
                        </div>
                        <Link href="/admin/dosen/create" as="button">
                            <Button size="default" class="gap-2">
                                <Plus class="h-4 w-4" />
                                Tambah Dosen
                            </Button>
                        </Link>
                    </div>
                </div>

                <!-- Data Table Card -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <PrimeDataTable
                        title="Daftar Dosen"
                        description="Kelola data semua dosen dalam sistem"
                        :columns="columns"
                        :data="dosen.data"
                        :totalRecords="dosen.meta?.total"
                        create-route="/admin/dosen/create"
                        edit-route="/admin/dosen"
                        delete-route="/admin/dosen"
                        bulk-delete-route="/admin/dosen/bulk-delete"
                        :exportable="true"
                    >
                        <template #cell-is_active="{ item }">
                            <Tag 
                                :value="item.is_active ? 'Aktif' : 'Tidak Aktif'" 
                                :severity="getStatusSeverity(item.is_active)"
                            />
                        </template>
                    </PrimeDataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
```

## Key Differences

| Aspect | AdminDataTable | PrimeDataTable |
|--------|---|---|
| **Column Props** | `key`, `label` | `field`, `header` |
| **Import** | `import AdminDataTable` | `import PrimeDataTable` |
| **Sorting** | Built-in | Click header + `sortable: true` |
| **Pagination** | Server-side via links | Client-side + configurable |
| **Search** | Via AdminFilterBar | Built-in global search |
| **Status Styling** | CSS classes | PrimeVue Tag + severity |
| **Export** | ❌ | ✅ CSV export |
| **Import** | ❌ | ✅ CSV import |
| **Toolbar** | ❌ | ✅ Full featured |
| **UI** | Custom table | PrimeVue DataTable |

## What You Get

✨ **Better UX:**
- Modern PrimeVue design
- Built-in search/sort/pagination
- Toolbar dengan Create/Delete/Import/Export
- Better mobile responsiveness

✨ **Fewer Code:**
- No need for AdminFilterBar
- Column definition simpler
- Status badge using Tag component
- Less boilerplate

✨ **More Features:**
- CSV export/import
- Bulk delete dengan selection
- Global search in all columns
- Customizable rows per page
- Better accessibility

## Migration Checklist

- [ ] Update imports (PrimeDataTable, remove AdminDataTable)
- [ ] Update columns definition (key→field, label→header)
- [ ] Update template (replace AdminDataTable tag)
- [ ] Update status/badge helpers (use PrimeVue severity)
- [ ] Remove AdminFilterBar (use PrimeDataTable's search)
- [ ] Add routes (create-route untuk create button)
- [ ] Test CRUD operations
- [ ] Test export feature
- [ ] Test bulk delete
- [ ] Test search/sort

## Questions?

Refer ke `PRIME_DATATABLE_GUIDE.md` untuk detailed documentation.
