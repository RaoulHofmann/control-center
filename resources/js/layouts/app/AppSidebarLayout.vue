<script setup lang="ts">
import type { BreadcrumbItemType, NavItem } from '@/types';
import { cn } from "@/lib/utils";
import { ref, computed } from 'vue';
import { Home, Settings, Users } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const isOpen = ref(true);
const toggleSidebar = () => {
    isOpen.value = !isOpen.value;
};

const page = usePage();

// Computed property to get navigation items with active state
const navItems = computed(() => {
    const currentUrl = page.url;

    return [
        {
            title: 'Dashboard',
            href: '/',
            icon: Home,
            isActive: currentUrl === '/'
        },
        {
            title: 'Settings',
            href: '/settings',
            icon: Settings,
            isActive: currentUrl === '/settings'
        }
    ] as NavItem[];
});
</script>

<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside
            :class="cn(
                'bg-gray-100 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300',
                isOpen ? 'w-64' : 'w-20'
            )"
        >
            <div class="p-4 flex justify-between items-center">
                <h1 :class="cn('font-bold text-xl transition-opacity', isOpen ? 'opacity-100' : 'opacity-0')">
                    Control Center
                </h1>
                <button
                    @click="toggleSidebar"
                    class="p-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        :class="isOpen ? 'rotate-0' : 'rotate-180'"
                        class="h-5 w-5 transition-transform"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <nav class="mt-6">
                <ul class="space-y-2 px-2">
                    <li v-for="item in navItems" :key="item.title">
                        <a
                            :href="item.href"
                            :class="cn(
                                'flex items-center p-3 rounded-md transition-colors',
                                item.isActive
                                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'
                                    : 'hover:bg-gray-200 dark:hover:bg-gray-800'
                            )"
                        >
                            <component :is="item.icon" class="h-5 w-5" />
                            <span :class="cn('ml-3 transition-opacity', isOpen ? 'opacity-100' : 'opacity-0')">
                                {{ item.title }}
                            </span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main content -->
        <main
            data-slot="sidebar-inset"
            :class="cn(
                'bg-background relative flex w-full flex-1 flex-col',
                'md:peer-data-[variant=inset]:m-2 md:peer-data-[variant=inset]:ml-0 md:peer-data-[variant=inset]:rounded-xl md:peer-data-[variant=inset]:shadow-sm md:peer-data-[variant=inset]:peer-data-[state=collapsed]:ml-2',
            )"
        >
            <slot />
        </main>
    </div>
</template>
