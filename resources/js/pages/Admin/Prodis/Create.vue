<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

interface Props {
    //
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Program Studi', href: '/admin/prodis' },
    { title: 'Tambah', href: '/admin/prodis/create' },
];

const form = useForm({
    nama: '',
    kode: '',
    deskripsi: '',
    is_active: true,
});

const submit = () => {
    form.post('/admin/prodis', {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Tambah Program Studi" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold">Tambah Program Studi</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Tambahkan program studi baru ke dalam sistem
                </p>
            </div>

            <!-- Form -->
            <div class="max-w-2xl rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nama -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Nama Program Studi *</label>
                        <Input
                            v-model="form.nama"
                            type="text"
                            placeholder="Contoh: Informatika"
                            :class="{ 'border-red-500': form.errors.nama }"
                        />
                        <p v-if="form.errors.nama" class="text-sm text-red-600">{{ form.errors.nama }}</p>
                    </div>

                    <!-- Kode -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Kode Prodi *</label>
                        <Input
                            v-model="form.kode"
                            type="text"
                            placeholder="Contoh: IF"
                            :class="{ 'border-red-500': form.errors.kode }"
                        />
                        <p v-if="form.errors.kode" class="text-sm text-red-600">{{ form.errors.kode }}</p>
                    </div>

                    <!-- Deskripsi -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Deskripsi</label>
                        <textarea
                            v-model="form.deskripsi"
                            placeholder="Deskripsi program studi"
                            rows="4"
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                            :class="{ 'border-red-500': form.errors.deskripsi }"
                        ></textarea>
                        <p v-if="form.errors.deskripsi" class="text-sm text-red-600">{{ form.errors.deskripsi }}</p>
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-gray-300"
                            />
                            <span class="text-sm font-medium">Aktif</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </Button>
                        <Button
                            as="a"
                            href="/admin/prodis"
                            variant="outline"
                        >
                            Batal
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
