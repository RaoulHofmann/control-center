<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {type BreadcrumbItem, ModuleInstance, Series} from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, Ref } from 'vue';
import axios from "axios";
import { onMounted } from "vue";
import { DiskSpace } from '@/types';

import LoadingState from '@/components/dashboard/LoadingState.vue';
import ErrorState from '@/components/dashboard/ErrorState.vue';
import ModuleList from '@/components/dashboard/ModuleList.vue';
import DiskUsageModule from '@/components/dashboard/modules/DiskUsageModule.vue';
import NoDataState from '@/components/dashboard/NoDataState.vue';
import AddModuleButton from '@/components/dashboard/AddModuleButton.vue';
import SeriesModule from "@/components/dashboard/modules/SeriesModule.vue";

interface ModuleData {
    instance: ModuleInstance,
    disk_space?: DiskSpace[]
    series?: Series[];
}

const diskUsageData: Ref<ModuleData[]> = ref([]);
const seriesData: Ref<ModuleData[]> = ref([]);

const moduleInstances: Ref<ModuleInstance[]> = ref([]);
const loading = ref(true);
const error = ref<string | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
];

const fetchModuleInstances = async () => {
    try {
        const response = await axios.get('/modules/instances');
        if (response.status === 200) {
            moduleInstances.value = response.data;
        }
    } catch (err) {
        console.error('Failed to fetch module instances:', err);
        error.value = 'Failed to fetch module instances';
    }
};

const fetchDiskUsageData = async () => {
    try {
        const response = await axios.get('/modules/disk-usage/data');
        if (response.status === 200) {
            diskUsageData.value = response.data;
        }
    } catch (err) {
        console.error('Failed to fetch disk usage data:', err);
        error.value = 'Failed to fetch disk usage data';
    }
};

const fetchSeriesData = async () => {
    try {
        const response = await axios.get('/modules/series/data');
        if (response.status === 200) {
            seriesData.value = response.data;
        }
    } catch (err) {
        console.error('Failed to fetch series data:', err);
        error.value = 'Failed to fetch series data';
    }
};

const getDiskSpaceForModule = (moduleInstance: ModuleInstance): DiskSpace[] => {
    if (moduleInstance.module.type === 'disk_usage' && Array.isArray(moduleInstance.cached_data)) {
        return moduleInstance.cached_data;
    }
    return [];
};

const getSeriesForModule = (moduleInstance: ModuleInstance): Series[] => {
    if (moduleInstance.module.type === 'series_list' && Array.isArray(moduleInstance.cached_data)) {
        return moduleInstance.cached_data;
    }
    return [];
};

const addModuleInstance = (moduleInstance: ModuleInstance) => {
    if (!moduleInstances.value.some(mi => mi.id === moduleInstance.id)) {
        moduleInstances.value = [...moduleInstances.value, moduleInstance];
    }

    if (moduleInstance.module.type === 'disk_usage') {
        fetchDiskUsageData();
    } else if (moduleInstance.module.type === 'series_list') {
        fetchSeriesData();
    }
};

onMounted(async () => {
    loading.value = true;
    error.value = null;

    try {
        await fetchModuleInstances();

        const hasDiskUsageModules = moduleInstances.value.some(mi => mi.module.type === 'disk_usage');
        const hasSeriesModules = moduleInstances.value.some(mi => mi.module.type === 'series_list');

        if (hasDiskUsageModules) {
            await fetchDiskUsageData();
        }

        if (hasSeriesModules) {
            await fetchSeriesData();
        }
    } catch (err) {
        console.error('Error initializing dashboard:', err);
        error.value = 'Failed to initialize dashboard';
    } finally {
        loading.value = false;
    }
});

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <LoadingState v-if="loading" />

            <ErrorState v-else-if="error" :error="error" />

            <div v-else-if="moduleInstances.length > 0">
                <div class="mb-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Dashboard Modules</h2>
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-500">
                            Drag modules to customize your dashboard
                        </div>
                        <AddModuleButton :module-instances="moduleInstances" @add-module-instance="addModuleInstance" />
                    </div>
                </div>

                <ModuleList
                    :module-instances="moduleInstances"
                    @update:module-instances="moduleInstances = $event"
                >
                    <template #module-content="{ moduleInstance }">
                        <DiskUsageModule
                            v-if="moduleInstance.module.type === 'disk_usage'"
                            :module-instance="moduleInstance"
                            :disk-space="getDiskSpaceForModule(moduleInstance)"
                        />
                        <SeriesModule v-if="moduleInstance.module.type === 'series_list'" :module-instance="moduleInstance" :series="getSeriesForModule(moduleInstance)" />
                    </template>
                </ModuleList>
            </div>

            <NoDataState v-else-if="!loading && !error && (!moduleInstances || moduleInstances.length === 0)" :module-instances="moduleInstances" @add-module-instance="addModuleInstance" />
        </div>
    </AppLayout>
</template>
