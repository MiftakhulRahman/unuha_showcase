<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';

interface Tool {
    id: number;
    nama: string;
    slug: string;
    deskripsi?: string;
    icon?: string;
    color?: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    tool: Tool;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Tools', href: '/admin/tools' },
    { title: props.tool.nama, href: `/admin/tools/${props.tool.id}` },
];

const getStatusBadge = (isActive: boolean) => {
    return isActive
        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100';
};
</script>

<template>
    <Head :title="`${tool.nama}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/admin/tools" as="button">
                    <Button variant="outline" size="sm">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-3xl font-bold">{{ tool.nama }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Detail tool/teknologi
                    </p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Informasi Tool</h3>
                        <div class="mt-4 space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama</label>
                                    <p class="mt-1 text-sm">{{ tool.nama }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Slug</label>
                                    <p class="mt-1 text-sm font-mono">{{ tool.slug }}</p>
                                </div>
                                <div v-if="tool.icon">
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Icon</label>
                                    <p class="mt-1 text-sm">{{ tool.icon }}</p>
                                </div>
                                <div v-if="tool.color">
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Warna</label>
                                    <p class="mt-1 text-sm">{{ tool.color }}</p>
                                </div>
                            </div>
                            <div v-if="tool.deskripsi">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Deskripsi</label>
                                <p class="mt-1 text-sm">{{ tool.deskripsi }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Status</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Status Aktif</label>
                                <div class="mt-2">
                                    <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getStatusBadge(tool.is_active)]">
                                        {{ tool.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold">Informasi Sistem</h3>
                        <div class="mt-4 space-y-4 text-sm">
                            <div>
                                <label class="font-medium text-gray-600 dark:text-gray-400">Dibuat</label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ new Date(tool.created_at).toLocaleString('id-ID') }}
                                </p>
                            </div>
                            <div>
                                <label class="font-medium text-gray-600 dark:text-gray-400">Diperbarui</label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ new Date(tool.updated_at).toLocaleString('id-ID') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2">
                        <Link :href="`/admin/tools/${tool.id}/edit`" as="button">
                            <Button class="w-full">Edit</Button>
                        </Link>
                        <Link href="/admin/tools" as="button">
                            <Button variant="outline" class="w-full">Kembali</Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
