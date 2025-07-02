<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, Ref } from 'vue';
import axios from "axios";
import { onMounted } from "vue";
import { ModuleConfigSchema } from '@/types';

// Import components
import LoadingState from '@/components/dashboard/LoadingState.vue';
import ErrorState from '@/components/dashboard/ErrorState.vue';
import ModuleList from '@/components/dashboard/ModuleList.vue';
import DiskUsageModule from '@/components/dashboard/DiskUsageModule.vue';
import NoDataState from '@/components/dashboard/NoDataState.vue';
import AddModuleButton from '@/components/dashboard/AddModuleButton.vue';

// Types
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

interface DiskUsageData {
    instance: ModuleInstance,
    disk_space: DiskSpace[]
}

// State
const diskUsageData: Ref<DiskUsageData[]> = ref([]);
const moduleInstances: Ref<ModuleInstance[]> = ref([]);
const loading = ref(true);
const error = ref<string | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
];

// Function to ensure the disk usage module exists
const ensureDiskUsageModule = async () => {
    try {
        await axios.post('/modules/disk-usage/create');
    } catch (err) {
        console.error('Failed to create disk usage module:', err);
    }
};

// Function to fetch module instances
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

// Function to fetch disk usage data
const fetchDiskUsageData = async () => {
    try {
        const response = await axios.get('/modules/disk-usage/data');
        if (response.status === 200) {
            diskUsageData.value = response.data;

            // Update the cached_data for each module instance
            response.data.forEach((data: DiskUsageData) => {
                const instance = moduleInstances.value.find(mi => mi.id === data.instance.id);
                if (instance) {
                    instance.cached_data = data.disk_space;
                }
            });
        }
    } catch (err) {
        console.error('Failed to fetch disk usage data:', err);
        error.value = 'Failed to fetch disk usage data';
    }
};

// Function to get disk space data for a module instance
const getDiskSpaceForModule = (moduleInstance: ModuleInstance): DiskSpace[] => {
    if (moduleInstance.module.type === 'disk_usage' && Array.isArray(moduleInstance.cached_data)) {
        return moduleInstance.cached_data;
    }
    return [];
};

const addModuleInstance = (moduleInstance: ModuleInstance) => {
    if (!moduleInstances.value.some(mi => mi.id === moduleInstance.id)) {
        moduleInstances.value = [...moduleInstances.value, moduleInstance];
    }

    // If it's a disk usage module, fetch the disk usage data
    if (moduleInstance.module.type === 'disk_usage') {
        fetchDiskUsageData();
    }
};

onMounted(async () => {
    loading.value = true;
    error.value = null;

    try {
        await ensureDiskUsageModule();
        await fetchModuleInstances();

        if (moduleInstances.value.some(mi => mi.module.type === 'disk_usage')) {
            await fetchDiskUsageData();
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
                    </template>
                </ModuleList>
            </div>

            <NoDataState v-else-if="!loading && !error && (!moduleInstances || moduleInstances.length === 0)" :module-instances="moduleInstances" @add-module-instance="addModuleInstance" />
        </div>
    </AppLayout>
</template>
