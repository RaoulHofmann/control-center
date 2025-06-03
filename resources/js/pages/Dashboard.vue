<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, Ref, computed } from 'vue';
import axios from "axios";
import { onMounted } from "vue";

interface diskSpace {
    path: string,
    freeSpace: number,
    totalSpace: number
}

const diskSpace: Ref<diskSpace[]> = ref([]);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
];

// Function to format bytes to a human-readable format
const formatBytes = (bytes: number, decimals = 2) => {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
};

const getChartOptions = (disk: diskSpace) => {
    const usedSpace = disk.totalSpace - disk.freeSpace;
    const usedPercentage = (usedSpace / disk.totalSpace) * 100;

    console.log(`${usedPercentage.toFixed(1)}%`)

    return {
        chart: {
            type: 'donut',
            height: 200,
        },
        labels: ['Used Space', 'Free Space'],
        series: [usedSpace, disk.freeSpace],
        colors: ['#FF4560', '#008FFB'],
        legend: {
            position: 'bottom',
            formatter: function(seriesName: string, opts: any) {
                const value = opts.w.globals.series[opts.seriesIndex];
                const percent = opts.w.globals.seriesPercent[opts.seriesIndex][0];
                const percentText = typeof percent === 'number' ? `${percent.toFixed(1)}%` : '0%';
                return `${seriesName}: ${formatBytes(value)} (${percentText})`;
            }
        },
        tooltip: {
            y: {
                formatter: function(value: number) {
                    return formatBytes(value);
                }
            }
        },
        title: {
            text: `Disk Usage: ${disk.path}`,
            align: 'center',
            style: {
                fontSize: '14px',
            }
        },
        subtitle: {
            text: `Total: ${formatBytes(disk.totalSpace)}`,
            align: 'center',
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Used',
                            formatter: function() {
                                return `${usedPercentage.toFixed(1)}%`;
                            }
                        }
                    }
                }
            }
        }
    };
};

onMounted(async () => {
    const diskSpaceRequest = await axios.get('/sonarr/disk_space')

    console.log(diskSpaceRequest)

    if (diskSpaceRequest.status === 200) {
        const diskData = diskSpaceRequest.data as diskSpace[];
        // For now just assume we are looking at the root dir
        const root = diskData.find(disk => disk.path === '/') || diskData
        diskSpace.value = Array.isArray(root) ? root : [root]
    }
})

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    v-if="diskSpace.length > 0"
                    v-for="disk of diskSpace"
                    class="relative p-4 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white dark:bg-sidebar-background"
                >
                    <apexchart
                        type="donut"
                        height="300"
                        :options="getChartOptions(disk)"
                        :series="[disk.totalSpace - disk.freeSpace, disk.freeSpace]"
                    ></apexchart>
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <!-- Additional content can be added here -->
            </div>
        </div>
    </AppLayout>
</template>
