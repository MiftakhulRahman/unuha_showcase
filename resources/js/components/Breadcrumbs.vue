<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import {
    Breadcrumb,
    BreadcrumbItem as BreadcrumbItemComponent,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Home } from 'lucide-vue-next';

interface Props {
    breadcrumbs: BreadcrumbItem[];
}

const props = defineProps<Props>();

// Filter out "Admin" and "Beranda" from breadcrumbs
const filteredBreadcrumbs = props.breadcrumbs.filter(
    item => item.title !== 'Admin' && item.title !== 'Beranda'
);
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList class="text-sm">
            <BreadcrumbItemComponent>
                <BreadcrumbLink href="/dashboard" class="flex items-center gap-1.5 text-muted-foreground hover:text-foreground transition-colors">
                    <Home class="h-3.5 w-3.5" />
                    <span>Dashboard</span>
                </BreadcrumbLink>
            </BreadcrumbItemComponent>
            <template v-if="filteredBreadcrumbs.length > 0">
                <BreadcrumbSeparator class="text-muted-foreground/50" />
                <template v-for="(item, index) in filteredBreadcrumbs" :key="index">
                    <BreadcrumbItemComponent>
                        <BreadcrumbLink
                            v-if="index < filteredBreadcrumbs.length - 1"
                            :href="item.href"
                            class="text-muted-foreground hover:text-foreground transition-colors"
                        >
                            {{ item.title }}
                        </BreadcrumbLink>
                        <BreadcrumbPage v-else class="font-medium text-foreground">
                            {{ item.title }}
                        </BreadcrumbPage>
                    </BreadcrumbItemComponent>
                    <BreadcrumbSeparator v-if="index < filteredBreadcrumbs.length - 1" class="text-muted-foreground/50" />
                </template>
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
