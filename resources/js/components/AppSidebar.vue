<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
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
    Database
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dasbor',
            href: dashboard().url,
            icon: LayoutGrid,
        },
    ];

    if (!user.value) return items;

    // === SUPERADMIN MENU ===
    if (user.value.role === 'superadmin') {
        items.push(
            // Manajemen User (Dosen & Mahasiswa)
            {
                title: 'Manajemen Pengguna',
                href: '/admin/users',
                icon: Users,
                description: 'Kelola akun dosen dan mahasiswa',
            },
            {
                title: 'Manajemen Dosen',
                href: '/admin/dosen',
                icon: GraduationCap,
                description: 'Kelola data dosen',
            },
            {
                title: 'Manajemen Mahasiswa',
                href: '/admin/mahasiswa',
                icon: User,
                description: 'Kelola data mahasiswa',
            },
            // Manajemen Master Data
            {
                title: 'Manajemen Program Studi',
                href: '/admin/prodis',
                icon: Database,
                description: 'Kelola data program studi',
            },
            {
                title: 'Manajemen Kategori',
                href: '/admin/kategoris',
                icon: Database,
                description: 'Kelola kategori project',
            },
            {
                title: 'Manajemen Teknologi',
                href: '/admin/tools',
                icon: Wrench,
                description: 'Kelola teknologi yang digunakan',
            },
            // Manajemen Project
            {
                title: 'Manajemen Project',
                href: '/admin/projects',
                icon: BookOpen,
                description: 'Kelola & moderasi semua project',
            },
            // Manajemen Challenge
            {
                title: 'Manajemen Challenge',
                href: '/admin/challenges',
                icon: Trophy,
                description: 'Monitor & kelola semua challenge',
            }
        );
    }
    // === DOSEN MENU ===
    else if (user.value.role === 'dosen') {
        items.push(
            {
                title: 'Project Saya',
                href: '/projects',
                icon: BookOpen,
                description: 'Portfolio project penelitian/pengabdian',
            },
            {
                title: 'Manajemen Challenge',
                href: '/challenges',
                icon: Trophy,
                description: 'Buat dan kelola kompetisi',
            },
            {
                title: 'Penilaian Challenge',
                href: '/penilaian',
                icon: FileText,
                description: 'Nilai submission mahasiswa',
            },
            {
                title: 'Profil Dosen',
                href: '/profile/dosen',
                icon: GraduationCap,
                description: 'Kelola profil akademik',
            }
        );
    }
    // === MAHASISWA MENU ===
    else if (user.value.role === 'mahasiswa') {
        items.push(
            {
                title: 'Project Saya',
                href: '/projects',
                icon: BookOpen,
                description: 'Portfolio karya mahasiswa',
            },
            {
                title: 'Ikuti Challenge',
                href: '/challenges',
                icon: Trophy,
                description: 'Daftar dan kirim project ke challenge',
            },
            {
                title: 'Kolaborasi',
                href: '/kolaborasi',
                icon: Users,
                description: 'Kelola tim proyek',
            },
            {
                title: 'Profil Mahasiswa',
                href: '/profile/mahasiswa',
                icon: User,
                description: 'Kelola biodata dan skill',
            }
        );
    }

    return items;
});

const footerNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    if (!user.value) return items;

    // Settings untuk semua user
    items.push({
        title: 'Pengaturan',
        href: '/settings/profile',
        icon: Settings,
        description: 'Profil & pengaturan akun',
    });

    return items;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
