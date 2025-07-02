<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { ModuleConfigSchema } from '@/types';

interface Module {
    id: number;
    name: string;
    type: string;
    description: string;
    is_active: boolean;
    config_schema: ModuleConfigSchema;
    created_at: string;
    updated_at: string;
}

interface ModuleInstance {
    id: number;
    module_id: number;
    name: string;
    is_active: boolean;
    display_order: number;
    config: any;
    cached_data: any;
    last_updated_at: string;
    created_at: string;
    updated_at: string;
    module: Module;
}

interface Props {
    moduleInstances: ModuleInstance[];
}

const props = defineProps<Props>();
const emit = defineEmits(['addModuleInstance']);

const showDropdown = ref(false);
const selectedModuleInstance = ref<ModuleInstance | null>(null);
const dropdownRef = ref<HTMLElement | null>(null);

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

const selectModuleInstance = (moduleInstance: ModuleInstance) => {
    selectedModuleInstance.value = moduleInstance;
    emit('addModuleInstance', moduleInstance);
    showDropdown.value = false;
};

const handleClickOutside = (event: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="relative" v-if="moduleInstances.length > 0" ref="dropdownRef">
        <Button
            variant="outline"
            size="sm"
            class="flex items-center gap-1"
            @click.stop="toggleDropdown"
        >
            <Plus class="h-4 w-4" />
            <span>Add Module</span>
        </Button>

        <!-- Dropdown Menu -->
        <div v-if="showDropdown" class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10">
            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                <div v-for="moduleInstance in moduleInstances" :key="moduleInstance.id"
                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer flex items-center"
                    role="menuitem"
                    @click="selectModuleInstance(moduleInstance)"
                >
                    <span class="mr-2 text-xs opacity-70">[{{ moduleInstance.module.type }}]</span>
                    {{ moduleInstance.name }}
                </div>
            </div>
        </div>
    </div>
</template>
