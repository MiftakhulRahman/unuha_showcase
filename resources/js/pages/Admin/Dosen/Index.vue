<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AdminDataTable from '@/components/AdminDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Button from 'primevue/button'; // Use PrimeVue button for consistency

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

// Updated Props interface to include meta for pagination
interface Props {
    dosen: {
        data: Dosen[];
        links: any;
        meta: any; // Added for PrimeVue DataTable pagination
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
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'profile_dosen.nidn', label: 'NIDN', sortable: true },
    { key: 'profile_dosen.prodi.nama', label: 'Prodi', sortable: true },
    { key: 'is_active', label: 'Status', sortable: true },
];

</script>

<template>
    <Head title="Manajemen Dosen" />
    <AppLayout>
        <!-- The main content area -->
        <div class="p-6">
            <div class="flex items-center justify-between">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="mt-4">
                <div class="mb-6 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-1.5">
                        <h1 class="text-2xl font-bold tracking-tight">Manajemen Dosen</h1>
                        <p class="text-sm text-muted-foreground">
                            Kelola data semua dosen dalam sistem.
                        </p>
                    </div>
                    <Link href="/admin/dosen/create">
                        <Button label="Tambah Dosen" icon="pi pi-plus" />
                    </Link>
                </div>

                <!-- The new PrimeVue DataTable replaces the old structure -->
                <AdminDataTable
                    :columns="columns"
                    :data="props.dosen.data"
                    :meta="props.dosen.meta"
                    bulk-delete-route="/admin/dosen/bulk-delete"
                    edit-route="/admin/dosen"
                    delete-route="/admin/dosen"
                >
                    <!--
                        You can still use slots if you need custom rendering beyond the default.
                        The 'is_active' status is now handled automatically by AdminDataTable.
                        Example:
                    -->
                    <!-- <template #cell-name="{ item }">
                        <span class="font-bold">{{ item.name }}</span>
                    </template> -->
                </AdminDataTable>
            </div>
        </div>
    </AppLayout>
</template>
