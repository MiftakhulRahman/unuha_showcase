<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

interface User {
    id: number;
    name: string;
    email: string;
    username: string;
    role: string;
    is_active: boolean;
}

interface Props {
    user: User;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Admin', href: '/admin' },
    { title: 'Pengguna', href: '/admin/users' },
    { title: `Edit ${props.user.name}`, href: `/admin/users/${props.user.id}/edit` },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    username: props.user.username,
    role: props.user.role,
    is_active: props.user.is_active,
});

const roles = [
    { label: 'Super Admin', value: 'superadmin' },
    { label: 'Dosen', value: 'dosen' },
    { label: 'Mahasiswa', value: 'mahasiswa' },
];

const submit = () => {
    form.put(`/admin/users/${props.user.id}`, {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Edit Pengguna" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 sm:p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold">Edit Pengguna</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Perbarui informasi pengguna
                </p>
            </div>

            <!-- Form -->
            <div class="max-w-2xl rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Name -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Nama Lengkap *</label>
                        <Input
                            v-model="form.name"
                            type="text"
                            placeholder="Masukkan nama lengkap"
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Email *</label>
                        <Input
                            v-model="form.email"
                            type="email"
                            placeholder="Masukkan email"
                            :class="{ 'border-red-500': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <!-- Username -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Username *</label>
                        <Input
                            v-model="form.username"
                            type="text"
                            placeholder="Masukkan username"
                            :class="{ 'border-red-500': form.errors.username }"
                        />
                        <p v-if="form.errors.username" class="text-sm text-red-600">{{ form.errors.username }}</p>
                    </div>

                    <!-- Role -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Role *</label>
                        <select
                            v-model="form.role"
                            class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                            :class="{ 'border-red-500': form.errors.role }"
                        >
                            <option v-for="role in roles" :key="role.value" :value="role.value">
                                {{ role.label }}
                            </option>
                        </select>
                        <p v-if="form.errors.role" class="text-sm text-red-600">{{ form.errors.role }}</p>
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
                    <div class="flex gap-3 pt-4">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </Button>
                        <Button
                            as="a"
                            href="/admin/users"
                            variant="outline"
                        >
                            Batal
                        </Button>
                    </div>
                </form>
            </div>

            <!-- Delete Section -->
            <div class="max-w-2xl rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-900 dark:bg-red-950">
                <h3 class="text-lg font-semibold text-red-900 dark:text-red-100">Zona Berbahaya</h3>
                <p class="mt-2 text-sm text-red-800 dark:text-red-200">
                    Hapus pengguna ini secara permanen dari sistem
                </p>
                <Link :href="`/admin/users/${user.id}`" method="delete" as="button">
                    <Button variant="destructive" class="mt-4">
                        Hapus Pengguna
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
