<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const roleTitle = computed(() => {
    const roleLabels: { [key: string]: string } = {
        superadmin: 'Administrator',
        dosen: 'Dosen',
        mahasiswa: 'Mahasiswa',
    };
    return roleLabels[user.value?.role || 'mahasiswa'];
});

</script>

<template>
    <Head :title="`Dashboard - ${roleTitle}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Welcome Section -->
            <div class="rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
                <h1 class="text-3xl font-bold">Selamat Datang, {{ user?.name }}! 👋</h1>
                <p class="mt-2 text-blue-100">Anda masuk sebagai <span class="font-semibold">{{ roleTitle }}</span></p>
            </div>

            <!-- Role-based Stats -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Total Project</h3>
                    <p class="mt-2 text-3xl font-bold">0</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Challenge</h3>
                    <p class="mt-2 text-3xl font-bold">0</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Interaksi</h3>
                    <p class="mt-2 text-3xl font-bold">0</p>
                </div>
            </div>

            <!-- Role-specific Content -->
            <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <!-- Mahasiswa Dashboard -->
                <template v-if="user?.role === 'mahasiswa'">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold">Dashboard Mahasiswa</h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Kelola project, ikuti challenge, dan tampilkan keterampilan Anda di sini.
                        </p>
                        <!-- Quick Actions -->
                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            <button class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                                + Unggah Project Baru
                            </button>
                            <button class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                                Lihat Challenge
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Dosen Dashboard -->
                <template v-else-if="user?.role === 'dosen'">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold">Dashboard Dosen</h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Kelola course, buat challenge, dan monitor progress mahasiswa.
                        </p>
                        <!-- Quick Actions -->
                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            <button class="rounded-lg bg-green-500 px-4 py-2 text-white hover:bg-green-600">
                                + Buat Challenge Baru
                            </button>
                            <button class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                                Lihat Submissions
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Admin Dashboard -->
                <template v-else-if="user?.role === 'superadmin'">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold">Dashboard Admin</h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Kelola pengguna, persetujuan konten, dan monitor aktivitas platform.
                        </p>
                        <!-- Quick Actions -->
                        <div class="mt-6 grid gap-4 md:grid-cols-3">
                            <button class="rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600">
                                Kelola Pengguna
                            </button>
                            <button class="rounded-lg bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600">
                                Moderasi Konten
                            </button>
                            <button class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                                Lihat Analitik
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Recent Activity -->
            <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <div class="border-b border-gray-200 p-6 dark:border-gray-700">
                    <h2 class="text-xl font-bold">Aktivitas Terbaru</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400">Belum ada aktivitas terbaru</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
