<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Kategori {
    id: number;
    nama: string;
}

interface Tool {
    id: number;
    nama: string;
}

interface Props {
    kategoris: Kategori[];
    tools: Tool[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Projects', href: '/projects' },
    { title: 'Create', href: '/projects/create' },
];

const form = useForm({
    kategori_id: '',
    title: '',
    description: '',
    content: '',
    repository_url: '',
    demo_url: '',
    video_url: '',
    tool_ids: [] as number[],
});

const submit = () => {
    form.post('/projects');
};
</script>

<template>
    <Head title="Create Project" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl p-4 sm:p-6">
            <div class="mb-8">
                <h1 class="text-3xl font-bold">Create New Project</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Share your awesome project with the community
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Category -->
                <div>
                    <Label for="kategori_id">Category *</Label>
                    <select
                        v-model="form.kategori_id"
                        id="kategori_id"
                        class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                    >
                        <option value="">Select a category</option>
                        <option v-for="kat in kategoris" :key="kat.id" :value="kat.id">
                            {{ kat.nama }}
                        </option>
                    </select>
                    <div v-if="form.errors.kategori_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.kategori_id }}
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <Label for="title">Project Title *</Label>
                    <Input
                        v-model="form.title"
                        id="title"
                        type="text"
                        placeholder="My Awesome Project"
                        class="mt-2"
                    />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                        {{ form.errors.title }}
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <Label for="description">Description *</Label>
                    <textarea
                        v-model="form.description"
                        id="description"
                        placeholder="Brief description of your project"
                        class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                    ></textarea>
                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                        {{ form.errors.description }}
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <Label for="content">Detailed Content</Label>
                    <textarea
                        v-model="form.content"
                        id="content"
                        placeholder="Write more details about your project"
                        rows="6"
                        class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                    ></textarea>
                </div>

                <!-- Tools/Technologies -->
                <div>
                    <Label>Technologies Used</Label>
                    <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <label v-for="tool in tools" :key="tool.id" class="flex items-center">
                            <input
                                type="checkbox"
                                :value="tool.id"
                                v-model.number="form.tool_ids"
                                class="h-4 w-4 rounded border-gray-300"
                            />
                            <span class="ml-2 text-sm">{{ tool.nama }}</span>
                        </label>
                    </div>
                </div>

                <!-- Repository URL -->
                <div>
                    <Label for="repository_url">Repository URL</Label>
                    <Input
                        v-model="form.repository_url"
                        id="repository_url"
                        type="url"
                        placeholder="https://github.com/..."
                        class="mt-2"
                    />
                </div>

                <!-- Demo URL -->
                <div>
                    <Label for="demo_url">Live Demo URL</Label>
                    <Input
                        v-model="form.demo_url"
                        id="demo_url"
                        type="url"
                        placeholder="https://demo.example.com"
                        class="mt-2"
                    />
                </div>

                <!-- Video URL -->
                <div>
                    <Label for="video_url">Video Demo URL</Label>
                    <Input
                        v-model="form.video_url"
                        id="video_url"
                        type="url"
                        placeholder="https://youtube.com/..."
                        class="mt-2"
                    />
                </div>

                <!-- Submit -->
                <div class="flex gap-4 pt-6">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create Project' }}
                    </Button>
                    <Button variant="outline" @click="$inertia.get('/projects')">
                        Cancel
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
