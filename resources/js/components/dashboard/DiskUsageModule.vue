<script setup lang="ts">
import DiskUsageChart from './DiskUsageChart.vue';
import { ModuleConfigSchema } from '@/types';

interface DiskSpace {
    path: string,
    freeSpace: number,
    totalSpace: number
}

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
    moduleInstance: ModuleInstance;
    diskSpace: DiskSpace[];
}

const props = defineProps<Props>();
</script>

<template>
    <div>
        <div v-if="diskSpace && diskSpace.length > 0">
            <div v-for="disk in diskSpace" :key="disk.path">
                <DiskUsageChart :disk="disk" :title="moduleInstance?.name || 'Disk Usage'" />
            </div>
        </div>
        <div v-else class="text-center p-4 text-gray-500">
            No disk usage data available for this module.
        </div>
    </div>
</template>
