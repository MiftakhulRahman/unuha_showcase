<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import PrimeDataTable from '@/components/PrimeDataTable.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import ToggleSwitch from 'primevue/toggleswitch';
import InputSlug from '@/components/InputSlug.vue';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

interface Project {
    id: number;
    title: string;
    slug: string;
    description: string;
    content: string;
    status: string;
    is_featured: boolean;
    repository_url: string;
    demo_url: string;
    video_url: string;
    user: { name: string };
    kategori: { nama: string };
}

interface Props {
    projects: {
        data: Project[];
        meta?: {
            total: number;
        };
    };
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Project', href: '/admin/projects' },
];

const columns = [
    { field: 'title', header: 'Judul Project', sortable: true, filterType: 'text' as const },
    { field: 'user.name', header: 'Pembuat', sortable: true, filterType: 'text' as const },
    { field: 'kategori.nama', header: 'Kategori', sortable: true, filterType: 'text' as const },
    { 
        field: 'status', 
        header: 'Status', 
        sortable: true, 
        filterType: 'select' as const,
        filterOptions: [
            { label: 'Draft', value: 'draft' },
            { label: 'Published', value: 'published' },
            { label: 'Archived', value: 'archived' },
        ]
    },
    { 
        field: 'is_featured', 
        header: 'Featured', 
        sortable: true, 
        filterType: 'boolean' as const,
        filterOptions: [
            { label: 'Yes', value: true },
            { label: 'No', value: false },
        ]
    },
];

const getStatusSeverity = (status: string) => {
    const severities: { [key: string]: string } = {
        draft: 'secondary',
        published: 'success',
        archived: 'danger',
    };
    return severities[status] || 'secondary';
};

// Modal & Form Logic
const showDialog = ref(false);
const editingId = ref<number | null>(null);
const creatorName = ref('');
const kategoriName = ref('');

const form = useForm({
    title: '',
    slug: '',
    description: '',
    content: '',
    status: 'draft',
    is_featured: false,
    repository_url: '',
    demo_url: '',
    video_url: '',
});

const editProject = (project: Project) => {
    editingId.value = project.id;
    creatorName.value = project.user?.name || '-';
    kategoriName.value = project.kategori?.nama || '-';
    
    form.title = project.title;
    form.slug = project.slug;
    form.description = project.description;
    form.content = project.content || '';
    form.status = project.status;
    form.is_featured = !!project.is_featured;
    form.repository_url = project.repository_url || '';
    form.demo_url = project.demo_url || '';
    form.video_url = project.video_url || '';

    form.clearErrors();
    showDialog.value = true;
};

const saveProject = () => {
    if (editingId.value) {
        form.put(`/admin/projects/${editingId.value}`, {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal memperbarui project');
            }
        });
    }
};
</script>

<template>
    <Head title="Manajemen Project" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="projects.data"
                    :totalRecords="projects.meta?.total"
                    title="Manajemen Project"
                    description="Kelola semua project yang ada di sistem dengan akses penuh"
                    delete-route="/admin/projects"
                    bulk-delete-route="/admin/projects/bulk-delete"
                    :exportable="true"
                >
                    <template #cell-user.name="{ item }">
                        {{ item.user?.name || '-' }}
                    </template>

                    <template #cell-kategori.nama="{ item }">
                        {{ item.kategori?.nama || '-' }}
                    </template>

                    <template #cell-status="{ item }">
                        <Tag 
                            :value="item.status.charAt(0).toUpperCase() + item.status.slice(1)" 
                            :severity="getStatusSeverity(item.status)" 
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>

                    <template #cell-is_featured="{ item }">
                        <Tag 
                            :value="item.is_featured ? 'Yes' : 'No'" 
                            :severity="item.is_featured ? 'warning' : 'secondary'" 
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>

                    <!-- Custom Actions Slot (Edit) -->
                    <template #actions="{ item }">
                        <Button 
                            icon="pi pi-pencil" 
                            variant="outlined" 
                            rounded 
                            class="mr-2" 
                            @click="editProject(item)" 
                        />
                    </template>
                </PrimeDataTable>
            </div>
        </div>

        <!-- Edit Dialog -->
        <Dialog 
            v-model:visible="showDialog" 
            :style="{ width: '800px' }" 
            header="Edit Project" 
            :modal="true" 
            class="p-fluid"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div>
                    <div class="field mb-4">
                        <label class="font-medium mb-2 block">Pembuat</label>
                        <InputText :modelValue="creatorName" disabled class="bg-gray-100" />
                    </div>

                    <div class="field mb-4">
                        <label class="font-medium mb-2 block">Kategori</label>
                        <InputText :modelValue="kategoriName" disabled class="bg-gray-100" />
                    </div>

                    <div class="field mb-4">
                        <label for="title" class="font-medium mb-2 block">Judul Project</label>
                        <InputSlug
                            id="title"
                            v-model="form.title"
                            :target="form.slug"
                            @update:target="form.slug = $event"
                            placeholder="Judul Project"
                            :error="form.errors.title"
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="slug" class="font-medium mb-2 block">Slug</label>
                        <InputText 
                            id="slug" 
                            v-model="form.slug" 
                            required="true" 
                            :class="{ 'p-invalid': form.errors.slug }" 
                        />
                        <small class="p-error" v-if="form.errors.slug">{{ form.errors.slug }}</small>
                    </div>

                    <div class="field mb-4 flex items-center gap-2 mt-8">
                        <ToggleSwitch v-model="form.is_featured" inputId="is_featured" />
                        <label for="is_featured" class="font-medium">Featured Project</label>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div class="field mb-4">
                        <label for="status" class="font-medium mb-2 block">Status</label>
                        <Select 
                            id="status"
                            v-model="form.status" 
                            :options="[
                                { label: 'Draft', value: 'draft' },
                                { label: 'Published', value: 'published' },
                                { label: 'Archived', value: 'archived' }
                            ]" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-full"
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="repository_url" class="font-medium mb-2 block">Repository URL</label>
                        <InputText 
                            id="repository_url" 
                            v-model="form.repository_url" 
                            placeholder="https://github.com/..."
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="demo_url" class="font-medium mb-2 block">Demo URL</label>
                        <InputText 
                            id="demo_url" 
                            v-model="form.demo_url" 
                            placeholder="https://..."
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="video_url" class="font-medium mb-2 block">Video URL</label>
                        <InputText 
                            id="video_url" 
                            v-model="form.video_url" 
                            placeholder="https://youtube.com/..."
                        />
                    </div>
                </div>

                <!-- Full Width -->
                <div class="col-span-1 md:col-span-2">
                    <div class="field mb-4">
                        <label for="description" class="font-medium mb-2 block">Deskripsi Singkat</label>
                        <Textarea 
                            id="description" 
                            v-model="form.description" 
                            rows="3" 
                            class="w-full"
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="content" class="font-medium mb-2 block">Konten Detail</label>
                        <Textarea 
                            id="content" 
                            v-model="form.content" 
                            rows="5" 
                            class="w-full"
                        />
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="showDialog = false" />
                <Button label="Simpan" icon="pi pi-check" text @click="saveProject" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
/* Nuclear option for Badge Font Weight */
:deep(.custom-badge),
:deep(.custom-badge .p-tag-value),
:deep(.p-tag.custom-badge),
:deep(.p-tag.custom-badge span) {
    font-weight: 500 !important;
    font-family: 'Outfit', sans-serif !important;
}
</style>

