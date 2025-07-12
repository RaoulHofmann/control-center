<script setup lang="ts">
import DiskUsageChart from './DiskUsageChart.vue';
import {DiskSpace, ModuleConfigSchema, ModuleInstance} from '@/types';

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
