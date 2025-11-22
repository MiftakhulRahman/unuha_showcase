<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavGroup } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutGrid,
    Users,
    GraduationCap,
    User,
    Trophy,
    BookOpen,
    Settings,
    Wrench,
    FileText,
    ArrowLeft,
    FolderTree,
    Tags,
    LockKeyhole,
    Palette,
    UserCircle,
    Shield
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';
import { useSidebar } from '@/components/ui/sidebar/utils';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const { setOpenMobile } = useSidebar();

const mainNavGroups = computed<NavGroup[]>(() => {
    const groups: NavGroup[] = [];

    // Dashboard - standalone
    groups.push({
        items: [
            {
                title: 'Dashboard',
                href: dashboard().url,
                icon: LayoutGrid,
            },
        ],
    });

    if (!user.value) return groups;

    // === SUPERADMIN MENU ===
    if (user.value.role === 'superadmin') {
        // Kelompok Pengguna
        groups.push({
            label: 'Pengguna', // Disingkat dari Manajemen Pengguna
            items: [
                {
                    title: 'Pengguna', // Sebelumnya: Manajemen Pengguna
                    href: '/admin/users',
                    icon: Users,
                },
                {
                    title: 'Dosen', // Sebelumnya: Manajemen Dosen
                    href: '/admin/dosen',
                    icon: GraduationCap,
                },
                {
                    title: 'Mahasiswa', // Sebelumnya: Manajemen Mahasiswa
                    href: '/admin/mahasiswa',
                    icon: User,
                },
            ],
        });

        // Data Master
        groups.push({
            label: 'Data Master',
            items: [
                {
                    title: 'Program Studi', // Sebelumnya: Manajemen Program Studi
                    href: '/admin/prodis',
                    icon: FolderTree,
                },
                {
                    title: 'Kategori', // Sebelumnya: Manajemen Kategori
                    href: '/admin/kategoris',
                    icon: Tags,
                },
                {
                    title: 'Teknologi', // Sebelumnya: Manajemen Teknologi
                    href: '/admin/tools',
                    icon: Wrench,
                },
            ],
        });

        // Konten
        groups.push({
            label: 'Konten',
            items: [
                {
                    title: 'Project', // Sebelumnya: Manajemen Project
                    href: '/admin/projects',
                    icon: BookOpen,
                },
                {
                    title: 'Challenge', // Sebelumnya: Manajemen Challenge
                    href: '/admin/challenges',
                    icon: Trophy,
                },
            ],
        });

        // Settings group
        groups.push({
            label: 'Pengaturan',
            items: [
                {
                    title: 'Pengaturan',
                    href: '/settings/profile',
                    icon: Settings,
                    items: [
                        {
                            title: 'Profil',
                            href: '/settings/profile',
                            icon: UserCircle,
                        },
                        {
                            title: 'Password',
                            href: '/settings/password',
                            icon: LockKeyhole,
                        },
                        {
                            title: 'Autentikasi Dua Faktor',
                            href: '/settings/two-factor',
                            icon: Shield,
                        },
                        {
                            title: 'Tampilan',
                            href: '/settings/appearance',
                            icon: Palette,
                        },
                    ],
                },
            ],
        });
    }
    // === DOSEN MENU ===
    else if (user.value.role === 'dosen') {
        groups.push({
            label: 'Konten',
            items: [
                {
                    title: 'Project Saya',
                    href: '/projects',
                    icon: BookOpen,
                },
                {
                    title: 'Challenge', // Sebelumnya: Manajemen Challenge
                    href: '/challenges',
                    icon: Trophy,
                },
                {
                    title: 'Penilaian Challenge',
                    href: '/penilaian',
                    icon: FileText,
                },
            ],
        });

        groups.push({
            items: [
                {
                    title: 'Profil Dosen',
                    href: '/profile/dosen',
                    icon: GraduationCap,
                },
            ],
        });
    }
    // === MAHASISWA MENU ===
    else if (user.value.role === 'mahasiswa') {
        groups.push({
            label: 'Konten',
            items: [
                {
                    title: 'Project Saya',
                    href: '/projects',
                    icon: BookOpen,
                },
                {
                    title: 'Ikuti Challenge',
                    href: '/challenges',
                    icon: Trophy,
                },
                {
                    title: 'Kolaborasi',
                    href: '/kolaborasi',
                    icon: Users,
                },
            ],
        });

        groups.push({
            items: [
                {
                    title: 'Profil Mahasiswa',
                    href: '/profile/mahasiswa',
                    icon: User,
                },
            ],
        });
    }

    return groups;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <div class="flex items-center justify-between w-full">
                        <SidebarMenuButton size="lg" as-child class="flex-1">
                            <Link :href="dashboard()">
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                        <button
                            @click="setOpenMobile(false)"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors md:hidden"
                            aria-label="Close sidebar"
                        >
                            <ArrowLeft class="h-5 w-5" />
                        </button>
                    </div>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :groups="mainNavGroups" />
        </SidebarContent>
    </Sidebar>
    <slot />
</template>