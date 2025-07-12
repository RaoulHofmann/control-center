<script setup lang="ts">
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';
import axios from 'axios';
import DiskUsageChart from './modules/DiskUsageChart.vue';
import {DiskSpace, ModuleInstance} from '@/types';

interface DiskUsageData {
    instance: ModuleInstance,
    disk_space: DiskSpace[]
}

interface Props {
    diskUsageData: DiskUsageData[];
}

const props = defineProps<Props>();
const emit = defineEmits(['update:diskUsageData']);

const isDragging = ref(false);

// Computed property to sort diskUsageData by display_order
const sortedDiskUsageData = computed(() => {
    return [...props.diskUsageData].sort((a, b) => {
        return (a.instance.display_order || 0) - (b.instance.display_order || 0);
    });
});

// Function to handle the end of drag event
const onDragEnd = async () => {
    isDragging.value = false;

    // Update the display_order of each module instance
    const instances = props.diskUsageData.map((data, index) => {
        return {
            id: data.instance.id,
            display_order: index
        };
    });

    try {
        await axios.post('/modules/instances/order', { instances });
        emit('update:diskUsageData', props.diskUsageData);
    } catch (err) {
        console.error('Failed to update module order:', err);
    }
};

// Function to handle the start of drag event
const onDragStart = () => {
    isDragging.value = true;
};

console.log(props.diskUsageData[0])

</script>

<template>
    <div>
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Dashboard Modules</h2>
            <div class="text-sm text-gray-500">
                <span v-if="isDragging" class="text-blue-500">Drag modules to reorder them</span>
                <span v-else>Drag modules to customize your dashboard</span>
            </div>
        </div>

        <draggable
            :list="diskUsageData"
            class="grid auto-rows-min gap-4 md:grid-cols-3"
            item-key="instance.id"
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
                    <div v-for="disk in element.disk_space" :key="disk.path">
                        <DiskUsageChart :disk="disk" :title="element.instance.name" />
                    </div>
                </div>
            </template>
        </draggable>
    </div>
</template>
