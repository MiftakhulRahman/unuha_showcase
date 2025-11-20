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
import { LayoutGrid, BookOpen, Trophy, Settings, Users, FileText } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
            icon: LayoutGrid,
        },
    ];

    if (!user.value) return items;

    // === SUPERADMIN MENU ===
    if (user.value.role === 'superadmin') {
        items.push(
            {
                title: 'Projects',
                href: '/projects',
                icon: BookOpen,
                description: 'View & manage all projects',
            },
            {
                title: 'Challenges',
                href: '/challenges',
                icon: Trophy,
                description: 'Monitor all challenges',
            }
        );
    }
    // === DOSEN MENU ===
    else if (user.value.role === 'dosen') {
        items.push(
            {
                title: 'My Projects',
                href: '/projects',
                icon: FileText,
                description: 'Your portfolio projects',
            },
            {
                title: 'My Challenges',
                href: '/challenges',
                icon: Trophy,
                description: 'Create & manage challenges',
            }
        );
    }
    // === MAHASISWA MENU ===
    else if (user.value.role === 'mahasiswa') {
        items.push(
            {
                title: 'My Projects',
                href: '/projects',
                icon: FileText,
                description: 'Your portfolio projects',
            },
            {
                title: 'Challenges',
                href: '/challenges',
                icon: Trophy,
                description: 'Discover challenges',
            }
        );
    }

    return items;
});

const footerNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    if (!user.value) return items;

    // === SUPERADMIN FOOTER MENU ===
    if (user.value.role === 'superadmin') {
        items.push({
            title: 'Admin Panel',
            href: '/admin/users',
            icon: Users,
            description: 'User & system management',
        });
    }

    // Settings untuk semua user
    items.push({
        title: 'Settings',
        href: '/profile',
        icon: Settings,
        description: 'Profile & account settings',
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
