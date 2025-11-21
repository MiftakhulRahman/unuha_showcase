<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { useToast } from '@/composables/useToast';
import { ArrowLeft, Save } from 'lucide-vue-next';

interface Prodi {
    id: number;
    nama: string;
    kode: string;
}

interface Props {
    prodis: Prodi[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dosen', href: '/admin/dosen' },
    { title: 'Tambah Dosen', href: '/admin/dosen/create' },
];

const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
    is_active: true,
    profile: {
        nidn: '',
        prodi_id: '',
        jabatan: '',
        bidang_keahlian: '',
    },
});

const toast = useToast();

const submit = () => {
    form.post('/admin/dosen', {
        onSuccess: () => {
            toast.createSuccess('Dosen');
            form.reset();
        },
        onError: () => {
            toast.operationFailed('Gagal menambahkan dosen. Periksa kembali data yang dimasukkan.');
        },
    });
};
</script>

<template>
    <Head title="Tambah Dosen" />
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-950 dark:to-gray-900">
            <div class="mx-auto max-w-4xl space-y-6 p-6">
                <!-- Breadcrumb with Back Arrow -->
                <div class="flex items-center gap-4">
                    <Link href="/admin/dosen" class="text-muted-foreground transition-colors hover:text-foreground">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </div>

                <!-- Header Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h1 class="text-2xl font-bold tracking-tight">Tambah Dosen Baru</h1>
                    <p class="mt-1.5 text-sm text-muted-foreground">
                        Daftarkan dosen baru ke dalam sistem
                    </p>
                </div>

                <!-- Form Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Basic Info Section -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Informasi Akun</h3>
                            <div class="space-y-4">
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
                            </div>
                        </div>

                        <!-- Profile Section -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Data Dosen</h3>
                            <div class="space-y-4">
                                <!-- NIDN -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium">NIDN *</label>
                                    <Input
                                        v-model="form.profile.nidn"
                                        type="text"
                                        placeholder="Masukkan NIDN"
                                        :class="{ 'border-red-500': form.errors['profile.nidn'] }"
                                    />
                                    <p v-if="form.errors['profile.nidn']" class="text-sm text-red-600">{{ form.errors['profile.nidn'] }}</p>
                                </div>

                                <!-- Prodi -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium">Program Studi *</label>
                                    <select
                                        v-model="form.profile.prodi_id"
                                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                                        :class="{ 'border-red-500': form.errors['profile.prodi_id'] }"
                                    >
                                        <option value="">Pilih Program Studi</option>
                                        <option v-for="prodi in prodis" :key="prodi.id" :value="prodi.id">
                                            {{ prodi.nama }} ({{ prodi.kode }})
                                        </option>
                                    </select>
                                    <p v-if="form.errors['profile.prodi_id']" class="text-sm text-red-600">{{ form.errors['profile.prodi_id'] }}</p>
                                </div>

                                <!-- Jabatan -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium">Jabatan</label>
                                    <Input
                                        v-model="form.profile.jabatan"
                                        type="text"
                                        placeholder="Contoh: Lektor Kepala, Asisten Ahli"
                                    />
                                    <p v-if="form.errors['profile.jabatan']" class="text-sm text-red-600">{{ form.errors['profile.jabatan'] }}</p>
                                </div>

                                <!-- Bidang Keahlian -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium">Bidang Keahlian</label>
                                    <Input
                                        v-model="form.profile.bidang_keahlian"
                                        type="text"
                                        placeholder="Contoh: Web Development, Database Design"
                                    />
                                    <p v-if="form.errors['profile.bidang_keahlian']" class="text-sm text-red-600">{{ form.errors['profile.bidang_keahlian'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Status</h3>
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
                                href="/admin/dosen"
                                variant="outline"
                            >
                                Batal
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
