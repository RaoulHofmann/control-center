<script setup lang="ts">
import { ref, Ref, watch } from 'vue';
import axios from 'axios';
import { Loader2 } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { ModuleConfigSchema, FieldSchema } from '@/types';

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

interface Props {
    module: Module | null;
    config: any;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:config', 'source-type-change']);

const availableDiskPaths = ref<string[]>([]);
const loadingDiskPaths = ref(false);
const fetchAvailableDiskPaths = async (sourceType: string) => {
    loadingDiskPaths.value = true;
    availableDiskPaths.value = [];

    try {
        const response = await axios.get('/modules/disk-usage/available-paths', {
            params: { source_type: sourceType }
        });

        if (response.status === 200) {
            availableDiskPaths.value = response.data;

            if (props.module?.config_schema?.selected_disks) {
                props.module.config_schema.selected_disks.options = response.data;
            }
        }
    } catch (error) {
        console.error('Failed to fetch available disk paths:', error);
    } finally {
        loadingDiskPaths.value = false;
    }
};

const handleSourceTypeChange = (sourceType: string) => {
    fetchAvailableDiskPaths(sourceType);
    emit('source-type-change', sourceType);
};

watch(() => props.config?.source_type, (newSourceType) => {
    if (newSourceType && props.module?.type === 'disk_usage') {
        fetchAvailableDiskPaths(newSourceType);
    }
}, { immediate: true });

const updateConfig = (key: string, value: any) => {
    const updatedConfig = { ...props.config, [key]: value };
    emit('update:config', updatedConfig);
};
</script>

<template>
    <div v-if="module?.type === 'disk_usage' && module?.config_schema">
        <div v-for="(schema, key) in module.config_schema" :key="key" class="grid grid-cols-4 items-center gap-4">
            <Label :for="key as string" class="text-right"> {{ schema.label }} </Label>
            <div class="col-span-3">
                <select
                    v-if="schema.type === 'select'"
                    :id="key as string"
                    :value="config[key]"
                    class="w-full rounded-md border border-input bg-background px-3 py-2"
                    @change="(e: Event) => {
                        const target = e.target as HTMLInputElement
                        updateConfig(key as string, target?.value);
                        if (key === 'source_type') handleSourceTypeChange(target?.value);
                    }"
                >
                    <option v-for="option in schema.options" :key="option" :value="option">
                        {{ option }}
                    </option>
                </select>

                <div v-else-if="schema.type === 'multiselect'" class="space-y-2">
                    <div v-if="loadingDiskPaths" class="flex items-center">
                        <Loader2 class="mr-2 h-4 w-4 animate-spin" />
                        Loading available options...
                    </div>
                    <div v-else>
                        <div v-if="schema.options && schema.options.length > 0" class="max-h-40 overflow-y-auto border border-input rounded-md p-2">
                            <div v-for="option in schema.options" :key="option" class="flex items-center space-x-2 py-1">
                                <input
                                    type="checkbox"
                                    :id="`${key}-${option}`"
                                    :value="option"
                                    :checked="config[key]?.includes(option)"
                                    @change="(e: Event) => {
                                        const target = e.target as HTMLInputElement
                                        const selected = config[key] || [];
                                        const updatedSelection = target?.checked
                                            ? [...selected, option]
                                            : selected.filter((item: string) => item !== option);
                                        updateConfig(key as string, updatedSelection);
                                    }"
                                    class="rounded border-gray-300"
                                />
                                <label :for="`${key}-${option}`" class="text-sm">{{ option }}</label>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-500">
                            No options available. Please select a source type first.
                        </div>
                    </div>
                </div>

                <Input
                    v-else
                    :id="key"
                    :value="config[key]"
                    :placeholder="(schema as FieldSchema)?.description || ''"
                    @input="(e: Event) => updateConfig(key as string, (e.target as HTMLInputElement)?.value)"
                />

                <p v-if="schema.description" class="text-xs text-gray-500 mt-1">
                    {{ schema.description }}
                </p>
            </div>
        </div>
    </div>
</template>
