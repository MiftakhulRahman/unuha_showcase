<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { MoreHorizontal } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel 
            class="
                group-data-[collapsible=icon]:!opacity-100 
                group-data-[collapsible=icon]:w-full 
                group-data-[collapsible=icon]:justify-center 
                group-data-[collapsible=icon]:items-center
                group-data-[collapsible=icon]:p-0
            "
        >
            <span class="group-data-[collapsible=icon]:hidden">Platform</span>
            <MoreHorizontal class="!size-5 hidden group-data-[collapsible=icon]:block pr-[2px]" />
        </SidebarGroupLabel>

        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(item.href, page.url)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>