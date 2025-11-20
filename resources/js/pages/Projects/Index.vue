<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Plus, Eye, Edit, Trash2 } from 'lucide-vue-next';

interface Project {
    id: number;
    title: string;
    description: string;
    slug: string;
    status: string;
    kategori: { nama: string };
    tools: any[];
}

interface Props {
    projects: {
        data: Project[];
        links: any;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dasbor', href: '/dashboard' },
    { title: 'Project Saya', href: '/projects' },
];

const getStatusColor = (status: string) => {
    const colors: { [key: string]: string } = {
        draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
        published: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
        archived: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
    };
    return colors[status] || colors.draft;
};
</script>

<template>
    <Head title="Project Saya" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Project Saya</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Kelola semua project Anda di sini
                    </p>
                </div>
                <Link href="/projects/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Project Baru
                    </Button>
                </Link>
            </div>

            <!-- Projects Grid -->
            <div v-if="projects.data.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="project in projects.data"
                    :key="project.id"
                    class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900"
                >
                    <!-- Card Body -->
                    <div class="p-6">
                        <!-- Status Badge -->
                        <div class="mb-3 flex items-start justify-between">
                            <span :class="['inline-block rounded-full px-3 py-1 text-xs font-semibold', getStatusColor(project.status)]">
                                {{ project.status }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ project.kategori.nama }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">
                            {{ project.title }}
                        </h3>

                        <!-- Description -->
                        <p class="mb-4 line-clamp-2 text-gray-600 dark:text-gray-400">
                            {{ project.description }}
                        </p>

                        <!-- Tools -->
                        <div v-if="project.tools.length > 0" class="mb-4 flex flex-wrap gap-2">
                            <span
                                v-for="tool in project.tools.slice(0, 3)"
                                :key="tool.id"
                                class="inline-block rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-100"
                            >
                                {{ tool.nama }}
                            </span>
                            <span v-if="project.tools.length > 3" class="text-xs text-gray-500 dark:text-gray-400">
                                +{{ project.tools.length - 3 }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                            <Link :href="`/projects/${project.id}`" class="flex-1">
                                <Button variant="outline" class="w-full">
                                    <Eye class="h-4 w-4" />
                                </Button>
                            </Link>
                            <Link :href="`/projects/${project.id}/edit`" class="flex-1">
                                <Button variant="outline" class="w-full">
                                    <Edit class="h-4 w-4" />
                                </Button>
                            </Link>
                            <Link :href="`/projects/${project.id}`" method="delete" as="button" class="flex-1">
                                <Button variant="outline" class="w-full text-red-600 hover:text-red-700 dark:text-red-400">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-600">
                <Plus class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                    Belum ada project
                </h3>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Mulailah dengan membuat project pertama Anda
                </p>
                <Link href="/projects/create" class="mt-6 inline-block">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Buat Project Pertama
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
