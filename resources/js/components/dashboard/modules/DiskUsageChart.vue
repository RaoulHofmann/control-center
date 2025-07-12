<script setup lang="ts">
import { computed } from 'vue';
import {DiskSpace} from "@/types";

interface Props {
    disk: DiskSpace;
    title?: string;
}

const props = defineProps<Props>();

// Function to format bytes to a human-readable format
const formatBytes = (bytes: number, decimals = 2) => {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
};

// Computed chart options
const chartOptions = computed(() => {
    const disk = props.disk;
    const title = props.title;
    const usedSpace = disk.totalSpace - disk.freeSpace;
    const usedPercentage = (usedSpace / disk.totalSpace) * 100;

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
            text: title || `Disk Usage: ${disk.path}`,
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
});

// Computed chart series
const chartSeries = computed(() => {
    const disk = props.disk;
    return [disk.totalSpace - disk.freeSpace, disk.freeSpace];
});
</script>

<template>
    <apexchart
        type="donut"
        height="300"
        :options="chartOptions"
        :series="chartSeries"
    ></apexchart>
</template>
