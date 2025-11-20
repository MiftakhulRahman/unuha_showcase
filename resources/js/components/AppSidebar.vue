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
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (user.value) {
        // Menu untuk semua roles
        items.push({
            title: 'Explore Projects',
            href: '#',
            icon: BookOpen,
        });

        // Menu khusus Mahasiswa & Dosen
        if (user.value.role !== 'superadmin') {
            items.push({
                title: 'My Projects',
                href: '#',
                icon: FileText,
            });
        }

        // Menu khusus Challenges
        items.push({
            title: 'Challenges',
            href: '#',
            icon: Trophy,
        });
    }

    return items;
});

const footerNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    if (user.value) {
        // Admin Menu
        if (user.value.role === 'superadmin') {
            items.push({
                title: 'Admin Panel',
                href: '#',
                icon: Users,
            });
        }

        // Settings
        items.push({
            title: 'Settings',
            href: '#',
            icon: Settings,
        });
    }

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
