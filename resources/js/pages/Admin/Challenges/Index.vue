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
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import InputSlug from '@/components/InputSlug.vue';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

interface Challenge {
    id: number;
    title: string;
    slug: string;
    description: string;
    content: string;
    status: string;
    level: string;
    points: number;
    user: { name: string };
    start_date: string;
    end_date: string;
}

interface Props {
    challenges: {
        data: Challenge[];
        meta?: {
            total: number;
        };
    };
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Challenge', href: '/admin/challenges' },
];

const columns = [
    { field: 'title', header: 'Judul Challenge', sortable: true, filterType: 'text' as const },
    { field: 'user.name', header: 'Pembuat (Dosen)', sortable: true, filterType: 'text' as const },
    { 
        field: 'status', 
        header: 'Status', 
        sortable: true, 
        filterType: 'select' as const,
        filterOptions: [
            { label: 'Draft', value: 'draft' },
            { label: 'Active', value: 'active' },
            { label: 'Finished', value: 'finished' },
        ]
    },
    { field: 'start_date', header: 'Tanggal Mulai', sortable: true },
    { field: 'end_date', header: 'Tanggal Berakhir', sortable: true },
];

const getStatusSeverity = (status: string) => {
    const severities: { [key: string]: string } = {
        draft: 'secondary',
        active: 'success',
        finished: 'info',
    };
    return severities[status] || 'secondary';
};

// Modal & Form Logic
const showDialog = ref(false);
const editingId = ref<number | null>(null);
const creatorName = ref('');

const form = useForm({
    title: '',
    slug: '',
    description: '',
    content: '',
    level: 'beginner',
    points: 0,
    status: 'draft',
    start_date: '',
    end_date: '',
});

const editChallenge = (challenge: Challenge) => {
    editingId.value = challenge.id;
    creatorName.value = challenge.user?.name || '-';
    
    form.title = challenge.title;
    form.slug = challenge.slug;
    form.description = challenge.description;
    form.content = challenge.content || '';
    form.level = challenge.level;
    form.points = challenge.points;
    form.status = challenge.status;
    
    // Format dates for datetime-local input (YYYY-MM-DDTHH:mm)
    const formatDate = (dateString: string) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toISOString().slice(0, 16);
    };

    form.start_date = formatDate(challenge.start_date);
    form.end_date = formatDate(challenge.end_date);

    form.clearErrors();
    showDialog.value = true;
};

const saveChallenge = () => {
    if (editingId.value) {
        form.put(`/admin/challenges/${editingId.value}`, {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal memperbarui challenge');
            }
        });
    }
};
</script>

<template>
    <Head title="Manajemen Challenge" />
    <AppLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <PrimeDataTable
                    :columns="columns"
                    :data="challenges.data"
                    :totalRecords="challenges.meta?.total"
                    title="Manajemen Challenge"
                    description="Pantau, edit, atau batalkan challenge yang dibuat oleh dosen"
                    delete-route="/admin/challenges"
                    bulk-delete-route="/admin/challenges/bulk-delete"
                    :exportable="true"
                >
                    <template #cell-user.name="{ item }">
                        {{ item.user?.name || '-' }}
                    </template>

                    <template #cell-status="{ item }">
                        <Tag 
                            :value="item.status.charAt(0).toUpperCase() + item.status.slice(1)" 
                            :severity="getStatusSeverity(item.status)" 
                            class="custom-badge"
                            style="font-weight: 500 !important;"
                        />
                    </template>

                    <template #cell-start_date="{ item }">
                        {{ new Date(item.start_date).toLocaleDateString('id-ID') }}
                    </template>

                    <template #cell-end_date="{ item }">
                        {{ new Date(item.end_date).toLocaleDateString('id-ID') }}
                    </template>

                    <!-- Custom Actions Slot (Edit) -->
                    <template #actions="{ item }">
                        <Button 
                            icon="pi pi-pencil" 
                            variant="outlined" 
                            rounded 
                            class="mr-2" 
                            @click="editChallenge(item)" 
                        />
                    </template>
                </PrimeDataTable>
            </div>
        </div>

        <!-- Edit Dialog -->
        <Dialog 
            v-model:visible="showDialog" 
            :style="{ width: '800px' }" 
            header="Edit Challenge" 
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
                        <label for="title" class="font-medium mb-2 block">Judul Challenge</label>
                        <InputSlug
                            id="title"
                            v-model="form.title"
                            :target="form.slug"
                            @update:target="form.slug = $event"
                            placeholder="Judul Challenge"
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

                    <div class="field mb-4">
                        <label for="level" class="font-medium mb-2 block">Level</label>
                        <Select 
                            id="level"
                            v-model="form.level" 
                            :options="[
                                { label: 'Beginner', value: 'beginner' },
                                { label: 'Intermediate', value: 'intermediate' },
                                { label: 'Advanced', value: 'advanced' }
                            ]" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-full"
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="points" class="font-medium mb-2 block">Poin</label>
                        <InputNumber 
                            id="points" 
                            v-model="form.points" 
                            :useGrouping="false"
                        />
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
                                { label: 'Active', value: 'active' },
                                { label: 'Finished', value: 'finished' }
                            ]" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-full"
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="start_date" class="font-medium mb-2 block">Tanggal Mulai</label>
                        <InputText 
                            id="start_date" 
                            v-model="form.start_date" 
                            type="datetime-local"
                        />
                    </div>

                    <div class="field mb-4">
                        <label for="end_date" class="font-medium mb-2 block">Tanggal Berakhir</label>
                        <InputText 
                            id="end_date" 
                            v-model="form.end_date" 
                            type="datetime-local"
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
                <Button label="Simpan" icon="pi pi-check" text @click="saveChallenge" :loading="form.processing" />
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

