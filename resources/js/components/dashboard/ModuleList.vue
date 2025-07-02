<script setup lang="ts">
import { ref } from 'vue';
import draggable from 'vuedraggable';
import axios from 'axios';
import { ModuleConfigSchema } from '@/types';

interface Module {
    id: number,
    name: string,
    type: string,
    description: string,
    is_active: boolean,
    config_schema: ModuleConfigSchema,
    created_at: string,
    updated_at: string
}

interface ModuleInstance {
    id: number,
    module_id: number,
    name: string,
    is_active: boolean,
    display_order: number,
    config: any,
    cached_data: any,
    last_updated_at: string,
    created_at: string,
    updated_at: string,
    module: Module
}

interface Props {
    moduleInstances: ModuleInstance[];
}

const props = defineProps<Props>();
const emit = defineEmits(['update:moduleInstances']);

const isDragging = ref(false);

const onDragEnd = async () => {
    isDragging.value = false;

    // Update the display_order of each module instance
    const instances = props.moduleInstances.map((instance, index) => {
        return {
            id: instance.id,
            display_order: index
        };
    });

    try {
        await axios.post('/modules/instances/order', { instances });
        emit('update:moduleInstances', props.moduleInstances);
    } catch (err) {
        console.error('Failed to update module order:', err);
    }
};

const onDragStart = () => {
    isDragging.value = true;
};
</script>

<template>
    <div>
        <draggable
            v-if="moduleInstances && moduleInstances.length > 0"
            :list="moduleInstances"
            class="grid auto-rows-min gap-4 md:grid-cols-3"
            item-key="id"
            handle=".drag-handle"
            @start="onDragStart"
            @end="onDragEnd"
            :animation="200"
        >
            <template #item="{element}">
                <div
                    class="relative p-4 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white dark:bg-sidebar-background"
                >
                    <div class="drag-handle cursor-move absolute top-2 right-2 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="5" r="1"></circle>
                            <circle cx="9" cy="12" r="1"></circle>
                            <circle cx="9" cy="19" r="1"></circle>
                            <circle cx="15" cy="5" r="1"></circle>
                            <circle cx="15" cy="12" r="1"></circle>
                            <circle cx="15" cy="19" r="1"></circle>
                        </svg>
                    </div>
                    <slot name="module-content" :module-instance="element"></slot>
                </div>
            </template>
        </draggable>
    </div>
</template>
