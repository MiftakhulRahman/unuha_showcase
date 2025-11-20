<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Edit, Trash2, Share2, Heart } from 'lucide-vue-next';

interface Project {
    id: number;
    title: string;
    description: string;
    content: string;
    slug: string;
    status: string;
    kategori: { nama: string };
    tools: any[];
    images: any[];
    view_count: number;
    published_at: string;
    user: { name: string; username: string };
}

interface Props {
    project: Project;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Projects', href: '/projects' },
    { title: props.project.title, href: `/projects/${props.project.id}` },
];
</script>

<template>
    <Head :title="project.title" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6">
            <!-- Header -->
            <div class="mb-8">
                <Link href="/projects" class="mb-4 inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Projects
                </Link>

                <div class="flex items-start justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-3">
                            <h1 class="text-4xl font-bold">{{ project.title }}</h1>
                            <span :class="[
                                'inline-block rounded-full px-3 py-1 text-xs font-semibold',
                                project.status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700' :
                                'bg-green-100 text-green-800 dark:bg-green-700'
                            ]">
                                {{ project.status }}
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            By <span class="font-medium">{{ project.user.name }}</span> • {{ project.kategori.nama }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <Link :href="`/projects/${project.id}/edit`">
                            <Button variant="outline">
                                <Edit class="mr-2 h-4 w-4" />
                                Edit
                            </Button>
                        </Link>
                        <Link :href="`/projects/${project.id}`" method="delete" as="button">
                            <Button variant="outline" class="text-red-600 hover:text-red-700">
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <!-- Description -->
                        <p class="mb-6 text-lg text-gray-700 dark:text-gray-300">
                            {{ project.description }}
                        </p>

                        <!-- Full Content -->
                        <div class="prose dark:prose-invert max-w-none">
                            {{ project.content }}
                        </div>

                        <!-- Tools -->
                        <div v-if="project.tools.length > 0" class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-700">
                            <h3 class="mb-3 font-semibold text-gray-900 dark:text-white">Technologies</h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="tool in project.tools"
                                    :key="tool.id"
                                    class="inline-block rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-100"
                                >
                                    {{ tool.nama }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Stats -->
                    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">Stats</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Views</span>
                                <span class="font-medium">{{ project.view_count }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status</span>
                                <span class="font-medium capitalize">{{ project.status }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">Actions</h3>
                        <div class="space-y-2">
                            <Button class="w-full">
                                <Heart class="mr-2 h-4 w-4" />
                                Like
                            </Button>
                            <Button variant="outline" class="w-full">
                                <Share2 class="mr-2 h-4 w-4" />
                                Share
                            </Button>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">Links</h3>
                        <div class="space-y-2">
                            <a v-if="project.repository_url" :href="project.repository_url" target="_blank" rel="noopener noreferrer" class="block text-blue-600 hover:underline dark:text-blue-400">
                                → Repository
                            </a>
                            <a v-if="project.demo_url" :href="project.demo_url" target="_blank" rel="noopener noreferrer" class="block text-blue-600 hover:underline dark:text-blue-400">
                                → Live Demo
                            </a>
                            <a v-if="project.video_url" :href="project.video_url" target="_blank" rel="noopener noreferrer" class="block text-blue-600 hover:underline dark:text-blue-400">
                                → Video Demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
