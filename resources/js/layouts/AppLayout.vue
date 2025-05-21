<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppSidebarLayout>
        <div class="flex flex-col h-full">
            <AppHeaderLayout :breadcrumbs="breadcrumbs" />
            <div class="flex-1 p-4 overflow-auto">
                <slot />
            </div>
        </div>
    </AppSidebarLayout>
</template>
