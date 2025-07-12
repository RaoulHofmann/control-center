<script setup lang="ts">
import DiskUsageChart from './DiskUsageChart.vue';
import {Series, ModuleInstance} from '@/types';

interface Props {
    moduleInstance: ModuleInstance;
    series: Series[];
}

const props = defineProps<Props>();
</script>

<template>
    <div>
        <div v-if="series && series.length > 0">
            <div v-for="serie in series" :key="serie.id">
                {{ serie.title }}
            </div>
        </div>
        <div v-else class="text-center p-4 text-gray-500">
            No disk usage data available for this module.
        </div>
    </div>
</template>
