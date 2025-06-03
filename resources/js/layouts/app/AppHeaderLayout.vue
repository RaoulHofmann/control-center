<script setup lang="ts">
import type { BreadcrumbItemType } from '@/types';
import { cn } from "@/lib/utils";
import { ChevronRight, Bell, User } from 'lucide-vue-next';
import FlashMessages from "@/components/ui/flash/FlashMessages.vue";

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Breadcrumbs -->
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li v-for="(crumb, index) in breadcrumbs" :key="crumb.href" class="inline-flex items-center">
                        <a
                            :href="crumb.href"
                            :class="cn(
                                'inline-flex items-center text-sm font-medium',
                                index === breadcrumbs.length - 1
                                    ? 'text-gray-500 dark:text-gray-400'
                                    : 'text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400'
                            )"
                        >
                            <span v-if="index > 0" class="mr-2">
                                <ChevronRight class="w-4 h-4" />
                            </span>
                            {{ crumb.title }}
                        </a>
                    </li>
                </ol>
            </nav>

            <!-- actions -->
            <div class="flex items-center space-x-3">
                <button class="p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <Bell class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                </button>
            </div>
        </div>
        <flash-messages />
    </header>
</template>
