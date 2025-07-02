<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
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

const addModuleInstance = () => {
    const diskUsageModuleInstance = props.moduleInstances.find(mi => mi.module.type === 'disk_usage');
    if (diskUsageModuleInstance) {
        emit('addModuleInstance', diskUsageModuleInstance);
    }
};
</script>

<template>
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
        <div class="flex justify-between items-center">
            <div>
                <strong class="font-bold">No data available!</strong>
                <span class="block sm:inline"> Add a module instance to get started.</span>
            </div>
            <div v-if="moduleInstances.length > 0">
                <Button
                    variant="outline"
                    size="sm"
                    class="flex items-center gap-1"
                    @click="addModuleInstance"
                >
                    <Plus class="h-4 w-4" />
                    <span>Add Module</span>
                </Button>
            </div>
        </div>
    </div>
</template>
