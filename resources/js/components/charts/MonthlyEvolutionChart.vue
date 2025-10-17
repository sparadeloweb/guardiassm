<script setup lang="ts">
import { ref, watch } from 'vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';
import { Line } from 'vue-chartjs';
import { Skeleton } from '@/components/ui/skeleton';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend,
    Filler
);

interface MonthlyEvolutionData {
    labels: string[];
    data: number[];
}

const props = withDefaults(defineProps<{
    data: MonthlyEvolutionData;
    isLoading?: boolean;
}>(), {
    isLoading: false,
});

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            backgroundColor: '#f8fafc',
            titleColor: '#1e293b',
            bodyColor: '#475569',
            borderColor: '#e2e8f0',
            borderWidth: 1,
            cornerRadius: 6,
            padding: 8,
            displayColors: false,
            callbacks: {
                title: function(context: any) {
                    return context[0].label;
                },
                label: function(context: any) {
                    return `Atenciones: ${context.parsed.y}`;
                },
            },
        },
    },
    scales: {
        x: {
            grid: {
                display: false,
            },
            ticks: {
                color: '#64748b',
                font: {
                    size: 10,
                    weight: '500',
                },
            },
        },
        y: {
            beginAtZero: true,
            grid: {
                color: '#f1f5f9',
                drawBorder: false,
                lineWidth: 1,
            },
            ticks: {
                color: '#64748b',
                font: {
                    size: 10,
                    weight: '500',
                },
                callback: function(value: any) {
                    return Number.isInteger(value) ? value : '';
                },
            },
        },
    },
    elements: {
        point: {
            radius: 4,
            hoverRadius: 6,
            backgroundColor: '#10b981',
            borderColor: '#ffffff',
            borderWidth: 2,
        },
        line: {
            tension: 0.3,
            borderWidth: 2,
            borderCapStyle: 'round',
        },
    },
    interaction: {
        intersect: false,
        mode: 'index',
    },
});

const chartData = ref({
    labels: props.data.labels,
    datasets: [
        {
            label: 'Atenciones',
            data: props.data.data,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointHoverBackgroundColor: '#ffffff',
            pointHoverBorderColor: '#10b981',
            pointHoverBorderWidth: 2,
            tension: 0.3,
            borderWidth: 2,
            borderCapStyle: 'round',
            pointRadius: 4,
            pointHoverRadius: 6,
        },
    ],
});

watch(() => props.data, (newData) => {
    chartData.value = {
        labels: newData.labels,
        datasets: [
            {
                label: 'Atenciones',
                data: newData.data,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: '#10b981',
                pointHoverBorderWidth: 2,
                tension: 0.3,
                borderWidth: 2,
                borderCapStyle: 'round',
                pointRadius: 4,
                pointHoverRadius: 6,
            },
        ],
    };
}, { deep: true });
</script>

<template>
    <div class="relative h-80">
        <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center">
            <div class="animate-pulse">
                <div class="h-80 w-full bg-muted rounded"></div>
            </div>
        </div>
        <Line v-else :data="chartData" :options="chartOptions" />
    </div>
</template>
