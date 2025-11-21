<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const emit = defineEmits(['close', 'success']);

const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
    role: 'mahasiswa',
    is_active: true,
});

const roles = [
    { label: 'Super Admin', value: 'superadmin' },
    { label: 'Dosen', value: 'dosen' },
    { label: 'Mahasiswa', value: 'mahasiswa' },
];

const submit = () => {
    form.post('/admin/users', {
        onSuccess: () => {
            form.reset();
            emit('success');
            emit('close');
        },
    });
};
</script>

<template>
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

        <!-- Password -->
        <div class="space-y-2">
            <label class="block text-sm font-medium">Password *</label>
            <Input
                v-model="form.password"
                type="password"
                placeholder="Masukkan password"
                :class="{ 'border-red-500': form.errors.password }"
            />
            <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
        </div>

        <!-- Password Confirmation -->
        <div class="space-y-2">
            <label class="block text-sm font-medium">Konfirmasi Password *</label>
            <Input
                v-model="form.password_confirmation"
                type="password"
                placeholder="Konfirmasi password"
            />
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
        <div class="flex justify-end gap-3 pt-4">
            <Button
                type="button"
                variant="outline"
                @click="$emit('close')"
            >
                Batal
            </Button>
            <Button
                type="submit"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </Button>
        </div>
    </form>
</template>
