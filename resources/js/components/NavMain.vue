<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavGroup } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { MoreHorizontal, ChevronRight } from 'lucide-vue-next';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';

defineProps<{
    groups: NavGroup[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup v-for="(group, index) in groups" :key="index" class="py-0 px-2 mb-2 last:mb-0">
        <SidebarGroupLabel 
            v-if="group.label"
            class="
                uppercase 
                text-[10px] 
                tracking-wider 
                text-muted-foreground
                group-data-[collapsible=icon]:!opacity-100 
                group-data-[collapsible=icon]:w-full 
                group-data-[collapsible=icon]:justify-center 
                group-data-[collapsible=icon]:items-center
                group-data-[collapsible=icon]:p-0
            "
        >
            <span class="group-data-[collapsible=icon]:hidden">{{ group.label }}</span>
            <MoreHorizontal class="!size-5 hidden group-data-[collapsible=icon]:block pr-[2px]" />
        </SidebarGroupLabel>

        <SidebarMenu>
            <Collapsible
                v-for="item in group.items"
                :key="item.title"
                as-child
                :default-open="false"
                class="group/collapsible"
            >
                <SidebarMenuItem>
                    <template v-if="item.items && item.items.length > 0">
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :tooltip="item.title">
                                <component :is="item.icon" v-if="item.icon" />
                                <span>{{ item.title }}</span>
                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subitem in item.items" :key="subitem.title">
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="urlIsActive(subitem.href, page.url)"
                                    >
                                        <Link :href="subitem.href">
                                            <component :is="subitem.icon" v-if="subitem.icon" />
                                            <span>{{ subitem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </template>
                    
                    <SidebarMenuButton
                        v-else
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
            </Collapsible>
        </SidebarMenu>
    </SidebarGroup>
</template>