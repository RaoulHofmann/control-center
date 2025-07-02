<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import {type BreadcrumbItem, DiskUsageConfigSchema} from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, reactive, Ref, onMounted } from 'vue';
import axios from 'axios';
import { Loader2 } from 'lucide-vue-next';
import DiskUsageModuleForm from '@/components/modules/DiskUsageModuleForm.vue';
import { ModuleConfigSchema } from '@/types';


interface ConnectionStatus {
    success: boolean;
    message: string;
}

interface Error {
    url: string;
    apiKey: string;
}

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
    config: any;
    cached_data: any;
    last_updated_at: string;
    created_at: string;
    updated_at: string;
    module: Module;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '/settings',
    },
];

const settingsConfig = useForm({
    url: 'http://192.168.20.20:8989',
    apiKey: 'XXXXXX',
    type: '',
});

const errors = reactive<Error>({
    url: '',
    apiKey: ''
})

const testingConnection = ref(false)
const connectionTested = ref(false)
const connectionStatus: Ref<ConnectionStatus | null> = ref(null)

const validate = () => {
    let valid = true

    if (!settingsConfig.url) {
        errors.url = 'URL is required'
        valid = false
    } else if (!settingsConfig.url.startsWith('http')) {
        errors.url = 'URL must start with http:// or https://'
        valid = false
    } else {
        errors.url = ''
    }

    if (!settingsConfig.apiKey) {
        errors.apiKey = 'API key is required'
        valid = false
    } else {
        errors.apiKey = ''
    }

    return valid
}


const testConnection = async () => {
    if (!validate()) return;

    testingConnection.value = true;
    connectionStatus.value = null;

    console.log(settingsConfig)

    try {
        const response = await axios.post('/settings/test', {
            type: settingsConfig.type?.toLowerCase(),
            url: settingsConfig.url,
            apiKey: settingsConfig.apiKey
        });

        console.log(response.data)

        connectionStatus.value = {
            success: true,
            message: '✓ Connection successful! Setting are valid.'
        };
        connectionTested.value = true;
    } catch (error: any) {
        connectionStatus.value = {
            success: false,
            message: '✗ Connection failed: ' +
                (error?.response?.data?.message || 'Invalid configuration')
        };
    } finally {
        testingConnection.value = false;
    }
};


const saveConnection = () => {
    if (!connectionTested.value) {
        connectionStatus.value = {
            success: false,
            message: 'Please test the connection before saving'
        }
        return
    }

    console.log(settingsConfig)

    settingsConfig.post('/settings/save', {
        onSuccess: () => {
            closeDialog()
            // Optionally show toast notification
        },
        onError: (errors) => {
            connectionStatus.value = {
                success: false,
                message: 'Save failed: ' + Object.values(errors).join(' ')
            }
        }
    })
}

interface DialogState {
    isOpen: boolean;
    selectedType: string | null;
}

const dialogState = ref<DialogState>({
    isOpen: false,
    selectedType: null,
});

const openDialog = (type: string) => {
    dialogState.value.selectedType = type
    settingsConfig.type = type?.toLowerCase()
    dialogState.value.isOpen = true
    connectionTested.value = false
    connectionStatus.value = null
}

const closeDialog = () => {
    dialogState.value.isOpen = false
    settingsConfig.reset()
    connectionTested.value = false
    connectionStatus.value = null
}

// Module state
const modules = ref<Module[]>([]);
const moduleInstances = ref<ModuleInstance[]>([]);
const loadingModules = ref(false);
const moduleError = ref<string | null>(null);

interface ModuleDialogState {
    isOpen: boolean;
    selectedModule: Module | null;
    selectedInstance: ModuleInstance | null;
    mode: 'create' | 'edit';
}

const moduleDialogState = ref<ModuleDialogState>({
    isOpen: false,
    selectedModule: null,
    selectedInstance: null,
    mode: 'create',
});

const moduleForm = useForm({
    module_id: 0,
    name: '',
    is_active: true as boolean,
    config: {} as any,
});

// Fetch modules and module instances
const fetchModules = async () => {
    loadingModules.value = true;
    moduleError.value = null;

    try {
        const response = await axios.get('/modules');
        modules.value = response.data;
    } catch (error: any) {
        moduleError.value = 'Failed to load modules: ' + (error?.response?.data?.message || 'Unknown error');
        console.error('Error fetching modules:', error);
    } finally {
        loadingModules.value = false;
    }
};

const fetchModuleInstances = async () => {
    loadingModules.value = true;
    moduleError.value = null;

    try {
        const response = await axios.get('/modules/instances');
        moduleInstances.value = response.data;
    } catch (error: any) {
        moduleError.value = 'Failed to load module instances: ' + (error?.response?.data?.message || 'Unknown error');
        console.error('Error fetching module instances:', error);
    } finally {
        loadingModules.value = false;
    }
};


// Open module dialog
const openModuleDialog = (module: Module, instance?: ModuleInstance) => {
    moduleDialogState.value.selectedModule = module;

    if (instance) {
        moduleDialogState.value.selectedInstance = instance;
        moduleDialogState.value.mode = 'edit';
        moduleForm.module_id = instance.module_id;
        moduleForm.name = instance.name;
        moduleForm.is_active = instance.is_active;
        moduleForm.config = instance.config || {};
    } else {
        moduleDialogState.value.selectedInstance = null;
        moduleDialogState.value.mode = 'create';
        moduleForm.module_id = module.id;
        moduleForm.name = '';
        moduleForm.is_active = true;
        moduleForm.config = {};

        // Initialize config with default values from schema
        if (module.config_schema) {
            for (const [key, schema] of Object.entries(module.config_schema)) {
                if (schema.type === 'select' && schema.options && schema.options.length > 0) {
                    moduleForm.config[key] = schema.options[0];
                }
            }
        }

        // Initialize module-specific default config
        if (module.type === 'disk_usage') {
            moduleForm.config.selected_disks = [];
        }
    }

    moduleDialogState.value.isOpen = true;
};

const closeModuleDialog = () => {
    moduleDialogState.value.isOpen = false;
    moduleForm.reset();
};


const saveModuleInstance = () => {
    if (moduleDialogState.value.mode === 'create') {
        moduleForm.post('/modules/instances', {
            onSuccess: () => {
                closeModuleDialog();
                fetchModuleInstances();
            },
            onError: (errors) => {
                console.error('Error creating module instance:', errors);
            }
        });
    } else {
        const instanceId = moduleDialogState.value.selectedInstance?.id;
        if (!instanceId) return;

        moduleForm.put(`/modules/instances/${instanceId}`, {
            onSuccess: () => {
                closeModuleDialog();
                fetchModuleInstances();
            },
            onError: (errors) => {
                console.error('Error updating module instance:', errors);
            }
        });
    }
};

const deleteModuleInstance = async (instance: ModuleInstance) => {
    if (!confirm(`Are you sure you want to delete "${instance.name}"?`)) return;

    try {
        await axios.delete(`/modules/instances/${instance.id}`);
        fetchModuleInstances();
    } catch (error) {
        console.error('Error deleting module instance:', error);
    }
};

onMounted(() => {
    fetchModules();
    fetchModuleInstances();
});

</script>

<template>
    <Head title="Settings" />

    <!-- Connection Dialog -->
    <Dialog :open="dialogState.isOpen" modal :defaultOpen="false" @update:open="closeDialog">
        <DialogContent class="sm:max-w-[525px]">
            <DialogHeader>
                <DialogTitle>Configure {{ dialogState.selectedType }}</DialogTitle>
                <DialogDescription> Enter your connection details </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="saveConnection">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="url" class="text-right"> Server URL </Label>
                        <div class="col-span-3 space-y-1">
                            <Input id="url" v-model="settingsConfig.url" placeholder="http://localhost:8989" :class="{ 'border-red-500': errors.url }" />
                            <span v-if="errors.url" class="text-xs text-red-500">{{ errors.url }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="apiKey" class="text-right"> API Key </Label>
                        <div class="col-span-3 space-y-1">
                            <Input
                                id="apiKey"
                                v-model="settingsConfig.apiKey"
                                placeholder="Enter your API key"
                                :class="{ 'border-red-500': errors.apiKey }"
                            />
                            <span v-if="errors.apiKey" class="text-xs text-red-500">{{ errors.apiKey }}</span>
                        </div>
                    </div>

                    <div
                        v-if="connectionStatus"
                        class="col-span-4 rounded p-2 text-center"
                        :class="connectionStatus.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                        {{ connectionStatus.message }}
                    </div>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="closeDialog"> Cancel </Button>
                    <Button type="button" variant="secondary" @click="testConnection" :disabled="testingConnection">
                        <span v-if="testingConnection">
                            <Loader2 class="mr-2 h-4 w-4 animate-spin" />
                            Testing...
                        </span>
                        <span v-else>Test Connection</span>
                    </Button>
                    <Button type="submit" :disabled="testingConnection || !connectionTested"> Save Connection </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Module Dialog -->
    <Dialog :open="moduleDialogState.isOpen" modal :defaultOpen="false" @update:open="closeModuleDialog">
        <DialogContent class="sm:max-w-[525px]">
            <DialogHeader>
                <DialogTitle>
                    {{ moduleDialogState.mode === 'create' ? 'Create' : 'Edit' }}
                    {{ moduleDialogState.selectedModule?.name }} Instance
                </DialogTitle>
                <DialogDescription>
                    {{ moduleDialogState.selectedModule?.description }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="saveModuleInstance">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="name" class="text-right"> Name </Label>
                        <div class="col-span-3">
                            <Input id="name" v-model="moduleForm.name" placeholder="Enter a name for this instance" />
                        </div>
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="is_active" class="text-right"> Active </Label>
                        <div class="col-span-3">
                            <input type="checkbox" id="is_active" v-model="moduleForm.is_active" />
                        </div>
                    </div>

                    <!-- Disk Usage Module Form -->
                    <DiskUsageModuleForm
                        v-if="moduleDialogState.selectedModule?.type === 'disk_usage'"
                        :module="moduleDialogState.selectedModule"
                        :config="moduleForm.config"
                        @update:config="moduleForm.config = $event"
                    />

                    <!-- Generic Module Form Fields -->
                    <template v-else-if="moduleDialogState.selectedModule?.config_schema">
                        <div v-for="(schema, key) in moduleDialogState.selectedModule.config_schema" :key="key" class="grid grid-cols-4 items-center gap-4">
                            <Label :for="key as string" class="text-right"> {{ schema.label }} </Label>
                            <div class="col-span-3">
                                <select
                                    v-if="schema.type === 'select'"
                                    :id="key as string"
                                    v-model="moduleForm.config[key]"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2"
                                >
                                    <option v-for="option in schema.options" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                </select>

                                <Input v-else :id="key" v-model="moduleForm.config[key]" :placeholder="schema.description" />

                                <p v-if="schema.description" class="text-xs text-gray-500 mt-1">
                                    {{ schema.description }}
                                </p>
                            </div>
                        </div>
                    </template>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="closeModuleDialog"> Cancel </Button>
                    <Button type="submit">
                        {{ moduleDialogState.mode === 'create' ? 'Create' : 'Save' }} Instance
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex sm:max-h-1xl md:w-3xl flex-1 flex-col gap-6 rounded-xl p-4">
            <div>
                <h2 class="text-2xl font-bold mb-3">Connections</h2>
                <div class="grid auto-rows-min gap-3 md:grid-cols-3">
                    <div
                        class="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border bg-violet-500 hover:bg-violet-700"
                        @click="openDialog('Sonarr')"
                    >
                        <div class="flex flex-col items-center">
                            <HeadingSmall title="Sonarr" class="pt-2" />
                            <img src="icon/sonarr-icon.png" class="w-12 pt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold mb-3">Modules</h2>

                <div v-if="loadingModules" class="flex justify-center items-center p-4">
                    <Loader2 class="h-8 w-8 animate-spin text-primary" />
                </div>

                <div v-else-if="moduleError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"> {{ moduleError }}</span>
                </div>

                <div v-else-if="modules.length === 0" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                    <strong class="font-bold">No modules available!</strong>
                    <span class="block sm:inline"> Please check your installation.</span>
                </div>

                <div v-else class="space-y-6">
                    <div v-for="module in modules" :key="module.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-xl font-semibold">{{ module.name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ module.description }}</p>
                            </div>
                            <Button @click="openModuleDialog(module)" variant="outline">Add Instance</Button>
                        </div>

                        <div v-if="moduleInstances.filter(i => i.module_id === module.id).length > 0" class="mt-4">
                            <h4 class="text-lg font-medium mb-2">Instances</h4>
                            <div class="space-y-2">
                                <div
                                    v-for="instance in moduleInstances.filter(i => i.module_id === module.id)"
                                    :key="instance.id"
                                    class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded"
                                >
                                    <div>
                                        <div class="font-medium">{{ instance.name }}</div>
                                        <div class="text-sm text-gray-500">
                                            <span v-if="instance.is_active" class="text-green-500">Active</span>
                                            <span v-else class="text-red-500">Inactive</span>
                                            <span v-if="instance.last_updated_at"> · Last updated: {{ new Date(instance.last_updated_at).toLocaleString() }}</span>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <Button @click="openModuleDialog(module, instance)" variant="outline" size="sm">Edit</Button>
                                        <Button @click="deleteModuleInstance(instance)" variant="destructive" size="sm">Delete</Button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="mt-4 text-gray-500 italic">
                            No instances configured. Click "Add Instance" to create one.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
