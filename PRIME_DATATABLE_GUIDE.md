# PrimeDataTable Component Guide

Komponen reusable `PrimeDataTable` yang diupdate dengan fitur lengkap dari PrimeVue DataTable, termasuk pagination, sorting, filtering, toolbar dengan actions, dan import/export.

## Fitur

✅ **Pagination** - Customizable rows per page (5, 10, 25, 50)
✅ **Sorting** - Multi-column sortable headers
✅ **Global Search** - Real-time filtering across all columns
✅ **Toolbar** - Tombol Create, Delete, Import, Export
✅ **Bulk Selection** - Select multiple rows dengan checkbox
✅ **Bulk Delete** - Delete banyak items sekaligus
✅ **Individual Actions** - Edit dan Delete per row
✅ **Custom Slots** - Customize cell content dan actions
✅ **Responsive** - Mobile-friendly design
✅ **Dark Mode** - Full dark mode support
✅ **Import/Export** - CSV file support

## Installation

PrimeVue sudah terinstall di project ini. Component tersedia di:
```
resources/js/components/PrimeDataTable.vue
```

## Basic Usage

### Simple Table dengan Search & Sorting

```vue
<script setup lang="ts">
const columns = [
    { field: 'name', header: 'Nama', sortable: true },
    { field: 'email', header: 'Email', sortable: true },
    { field: 'role', header: 'Role', sortable: true },
];

const users = [
    { id: 1, name: 'John Doe', email: 'john@example.com', role: 'Admin' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'User' },
];
</script>

<template>
    <PrimeDataTable
        :columns="columns"
        :data="users"
        title="Manajemen Pengguna"
        description="Kelola semua pengguna dalam sistem"
    />
</template>
```

### Dengan CRUD Operations

```vue
<PrimeDataTable
    :columns="columns"
    :data="users"
    :totalRecords="usersData.total"
    title="Manajemen Pengguna"
    description="Kelola semua pengguna dalam sistem"
    create-route="/admin/users/create"
    edit-route="/admin/users"
    delete-route="/admin/users"
    bulk-delete-route="/admin/users/bulk-delete"
    :exportable="true"
/>
```

### Dengan Sorting & Pagination Control

```vue
<PrimeDataTable
    :columns="columns"
    :data="products"
    title="Daftar Produk"
    sortField="price"
    :sortOrder="-1"
    :rows="25"
/>
```

### Dengan Import/Export

```vue
<PrimeDataTable
    :columns="columns"
    :data="users"
    import-route="/admin/users/import"
    :exportable="true"
/>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | `string` | `''` | Header title |
| `description` | `string` | `''` | Header subtitle |
| `columns` | `Column[]` | Required | Column definitions |
| `data` | `any[]` | Required | Table data |
| `totalRecords` | `number` | `0` | Total records for pagination |
| `createRoute` | `string` | undefined | Route untuk tombol Tambah |
| `editRoute` | `string` | undefined | Base route untuk edit (/{id}/edit) |
| `deleteRoute` | `string` | undefined | Base route untuk delete (/{id}) |
| `bulkDeleteRoute` | `string` | undefined | Route untuk bulk delete |
| `importRoute` | `string` | undefined | Route untuk import file |
| `exportable` | `boolean` | `true` | Show/hide export button |
| `sortField` | `string` | undefined | Default sort column |
| `sortOrder` | `number` | `1` | Sort order (1 = ASC, -1 = DESC) |
| `rows` | `number` | `10` | Default rows per page |

## Column Definition

```typescript
interface Column {
    field: string;      // Data field name (supports nested: "profile.name")
    header: string;     // Column header
    sortable?: boolean; // Enable sorting (default: true)
    width?: string;     // Column width (e.g., "20%", "200px")
}
```

## Slots

### Cell Content Customization

```vue
<PrimeDataTable
    :columns="columns"
    :data="items"
>
    <!-- Customize specific cell content -->
    <template #cell-price="{ item }">
        {{ formatCurrency(item.price) }}
    </template>

    <template #cell-status="{ item }">
        <Tag 
            :value="item.status" 
            :severity="getStatusSeverity(item.status)" 
        />
    </template>

    <!-- Customize row actions -->
    <template #actions="{ item }">
        <Button 
            label="View" 
            icon="pi pi-eye"
            @click="viewItem(item)"
        />
    </template>
</PrimeDataTable>
```

## Complete Example

```vue
<script setup lang="ts">
import { ref } from 'vue';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Tag from 'primevue/tag';

interface Product {
    id: number;
    code: string;
    name: string;
    price: number;
    category: string;
    rating: number;
    status: string;
    image: string;
}

const products = ref<Product[]>([
    { 
        id: 1, 
        code: 'P001', 
        name: 'Laptop', 
        price: 999.99,
        category: 'Electronics',
        rating: 4.5,
        status: 'INSTOCK',
        image: 'laptop.jpg'
    },
    // ... more products
]);

const columns = [
    { field: 'code', header: 'Code', sortable: true, width: '15%' },
    { field: 'name', header: 'Name', sortable: true, width: '25%' },
    { field: 'category', header: 'Category', sortable: true, width: '20%' },
    { field: 'price', header: 'Price', sortable: true, width: '15%' },
    { field: 'rating', header: 'Rating', sortable: true, width: '12%' },
    { field: 'status', header: 'Status', sortable: true, width: '13%' },
];

const getStatusSeverity = (status: string) => {
    const severities: { [key: string]: string } = {
        'INSTOCK': 'success',
        'LOWSTOCK': 'warning',
        'OUTOFSTOCK': 'danger',
    };
    return severities[status] || 'secondary';
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR' 
    }).format(value);
};
</script>

<template>
    <PrimeDataTable
        :columns="columns"
        :data="products"
        :totalRecords="products.length"
        title="Manajemen Produk"
        description="Kelola katalog produk dalam sistem"
        create-route="/admin/products/create"
        edit-route="/admin/products"
        delete-route="/admin/products"
        bulk-delete-route="/admin/products/bulk-delete"
        import-route="/admin/products/import"
        sort-field="name"
        :sort-order="1"
        :rows="10"
        :exportable="true"
    >
        <!-- Customize image cell -->
        <template #cell-image="{ item }">
            <img 
                :src="`/images/products/${item.image}`" 
                :alt="item.name"
                class="h-12 w-12 rounded object-cover"
            />
        </template>

        <!-- Customize price cell -->
        <template #cell-price="{ item }">
            <span class="font-semibold">{{ formatCurrency(item.price) }}</span>
        </template>

        <!-- Customize rating cell -->
        <template #cell-rating="{ item }">
            <div class="flex items-center gap-2">
                <span>{{ item.rating }}</span>
                <i class="pi pi-star-fill text-yellow-400" />
            </div>
        </template>

        <!-- Customize status cell -->
        <template #cell-status="{ item }">
            <Tag 
                :value="item.status" 
                :severity="getStatusSeverity(item.status)"
            />
        </template>

        <!-- Custom actions -->
        <template #actions="{ item }">
            <Button 
                icon="pi pi-eye" 
                rounded 
                text
                severity="info"
                @click="viewProduct(item)"
            />
        </template>
    </PrimeDataTable>
</template>
```

## Toolbar Layout

Toolbar terdiri dari 3 bagian:

```
┌─────────────────────────────────────────────────────────────┐
│ [+ Tambah] [🗑️ Hapus Terpilih]     [📥 Import] [📤 Export] │
└─────────────────────────────────────────────────────────────┘
```

### Customization

- **Create Button**: Klik akan navigate ke `createRoute`
- **Delete Button**: Klik akan konfirmasi dan mengirim POST ke `bulkDeleteRoute`
- **Import Button**: Upload file CSV/XLSX ke `importRoute`
- **Export Button**: Download table sebagai CSV

## Search & Filtering

Global search otomatis mencari di semua columns yang didefined. Cukup ketik di search box akan langsung filter hasil.

```vue
<!-- Search box muncul di header -->
<template #header>
    <div class="flex items-center justify-between gap-2">
        <h4>Manajemen Produk</h4>
        <IconField>
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText placeholder="Cari..." />
        </IconField>
    </div>
</template>
```

## Sorting

Klik di header column yang ada `sortable: true` untuk sort ascending/descending.

Default sort:
```vue
<PrimeDataTable
    sort-field="price"  <!-- Column to sort -->
    :sort-order="-1"    <!-- -1 = DESC, 1 = ASC -->
    :data="products"
/>
```

## Pagination

Default options: 5, 10, 25, 50 rows per page

```vue
<PrimeDataTable
    :rows="25"  <!-- Default 25 rows per page -->
    :data="products"
/>
```

Current page info ditampilkan: "Menampilkan 1 - 25 dari 100 data"

## Delete Operations

### Single Delete
1. Klik trash icon di row
2. Confirm dialog muncul
3. Klik "Ya, Hapus" akan POST ke `deleteRoute/{id}`

### Bulk Delete
1. Select multiple rows dengan checkbox
2. Klik tombol "Hapus Terpilih" di toolbar
3. Confirm dialog muncul
4. Klik "Ya, Hapus Semua" akan POST ke `bulkDeleteRoute` dengan `{ ids: [1,2,3] }`

## Import/Export

### Export
```vue
<PrimeDataTable
    :data="products"
    :exportable="true"  <!-- Enable export button -->
/>
```

Klik tombol "Export" akan download data sebagai CSV file.

### Import
```vue
<PrimeDataTable
    :data="products"
    import-route="/admin/products/import"
/>
```

Klik tombol "Import" untuk upload file CSV. Server akan process file dan return updated data.

## Styling

Component menggunakan Tailwind CSS + PrimeVue theme. Styling fully responsive dengan dark mode support.

### Dark Mode
Otomatis mengikuti system preference atau user preference. Tidak perlu konfigurasi tambahan.

### Custom Styling

Jika ingin customize styling, edit class di component atau override dengan Tailwind utilities:

```vue
<div class="rounded-xl border border-gray-200 dark:border-gray-800">
    <PrimeDataTable :data="items" />
</div>
```

## Common Patterns

### Pattern 1: Master-Detail dengan Custom Actions
```vue
<PrimeDataTable
    :columns="columns"
    :data="users"
    edit-route="/admin/users"
    delete-route="/admin/users"
>
    <template #actions="{ item }">
        <Button 
            icon="pi pi-list"
            rounded
            text
            @click="viewDetails(item)"
        />
    </template>
</PrimeDataTable>
```

### Pattern 2: Formatted Cells
```vue
<PrimeDataTable :columns="columns" :data="orders">
    <template #cell-total="{ item }">
        {{ formatCurrency(item.total) }}
    </template>
    <template #cell-created_at="{ item }">
        {{ formatDate(item.created_at) }}
    </template>
</PrimeDataTable>
```

### Pattern 3: Conditional Rendering
```vue
<PrimeDataTable :columns="columns" :data="users">
    <template #cell-actions="{ item }">
        <Button 
            v-if="canEdit(item)"
            icon="pi pi-pencil"
            @click="editItem(item)"
        />
    </template>
</PrimeDataTable>
```

## Integration dengan Server

### Backend Requirements

#### Delete Single
```
DELETE /api/resource/{id}
Response: { success: true, message: "Deleted successfully" }
```

#### Bulk Delete
```
POST /api/resource/bulk-delete
Body: { ids: [1, 2, 3] }
Response: { success: true, message: "3 items deleted" }
```

#### Import File
```
POST /api/resource/import
Body: FormData { file: File }
Response: { success: true, imported: 10, errors: 0 }
```

## Error Handling

Component otomatis menampilkan error toast jika operasi gagal:

```typescript
// Jika delete gagal
toast.operationFailed('Gagal menghapus data');

// Jika bulk delete gagal
toast.operationFailed('Gagal menghapus data');

// Jika import gagal
toast.operationFailed('Gagal mengimport file');
```

## Performance Tips

1. **Lazy Load**: Untuk dataset besar, gunakan server-side pagination
2. **Memoize Columns**: Define columns di parent component, jangan di loop
3. **Custom Slots**: Gunakan untuk complex rendering, avoid inline logic
4. **Debounce Search**: Jika ingin custom search dengan API call, debounce query

## Migration dari Old Component

Jika migrasi dari AdminDataTable ke PrimeDataTable:

```vue
<!-- Old: AdminDataTable -->
<AdminDataTable
    title="Users"
    :columns="columns"
    :data="users.data"
    :links="users.links"
    :meta="users.meta"
    edit-route="/admin/users"
    delete-route="/admin/users"
/>

<!-- New: PrimeDataTable -->
<PrimeDataTable
    title="Users"
    description="Kelola semua pengguna"
    :columns="columns"
    :data="users.data"
    :totalRecords="users.meta.total"
    create-route="/admin/users/create"
    edit-route="/admin/users"
    delete-route="/admin/users"
    bulk-delete-route="/admin/users/bulk-delete"
/>
```

## Troubleshooting

### Table tidak muncul
- Check props `columns` dan `data` sudah diberikan
- Pastikan component sudah diimport

### Search tidak bekerja
- Pastikan `columns` di-define dengan `field` yang benar
- `globalFilterFields` otomatis di-generate dari columns

### Sorting tidak bekerja
- Set `sortable: true` di column definition
- Atau `sortable` default `true` jika tidak dispesifikasi

### Delete tidak bekerja
- Check route di `deleteRoute` benar
- Pastikan backend support DELETE method
- Check CSRF token sudah di-setup di Inertia

### Import tidak bekerja
- Check `importRoute` sudah dispesifikasi
- Pastikan server handle multipart/form-data
- File harus CSV atau XLSX format

## API Reference

Lihat props dan slots di atas untuk dokumentasi lengkap.

---

**Last Updated**: 2024
**Component**: `resources/js/components/PrimeDataTable.vue`
**Dependencies**: PrimeVue 4.4.1+, Vue 3.5+, Inertia Vue 3
