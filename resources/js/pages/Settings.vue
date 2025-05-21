<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, reactive, Ref } from 'vue';
import axios from 'axios';
import { Loader2 } from 'lucide-vue-next';


interface ConnectionStatus {
    success: boolean;
    message: string;
}

interface Error {
    url: string;
    apiKey: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '/settings',
    },
];

const settingsConfig = useForm({
    url: 'http://192.168.20.20:8989',
    apiKey: 'aab8e0ae7f284665a0e22492705741f3',
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

</script>

<template>
    <Head title="Settings" />

    <Dialog :open="dialogState.isOpen" modal :defaultOpen="false" @update:open="closeDialog">
        <DialogContent class="sm:max-w-[525px]">
            <DialogHeader>
                <DialogTitle>Configure {{ dialogState.selectedType }}</DialogTitle>
                <DialogDescription> Enter your connection details </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="saveConnection">
                <div class="grid gap-4 py-4">
                    <!-- URL Field -->
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="url" class="text-right"> Server URL </Label>
                        <div class="col-span-3 space-y-1">
                            <Input id="url" v-model="settingsConfig.url" placeholder="http://localhost:8989" :class="{ 'border-red-500': errors.url }" />
                            <span v-if="errors.url" class="text-xs text-red-500">{{ errors.url }}</span>
                        </div>
                    </div>

                    <!-- API Key Field -->
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

                    <!-- Connection Status -->
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

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex sm:max-h-1xl md:w-3xl flex-1 flex-col gap-3 rounded-xl p-4">
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
    </AppLayout>
</template>
