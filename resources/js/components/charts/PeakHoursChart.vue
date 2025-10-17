<script setup lang="ts">
import { computed } from 'vue';
import { Skeleton } from '@/components/ui/skeleton';

interface PeakHoursData {
    labels: string[];
    data: number[];
}

const props = withDefaults(defineProps<{
    data: PeakHoursData;
    isLoading?: boolean;
}>(), {
    isLoading: false,
});

// Obtener los top 5 horarios con más actividad
const topHours = computed(() => {
    return props.data.labels
        .map((label, index) => ({
            hour: label,
            count: props.data.data[index] || 0,
        }))
        .filter(item => item.count > 0)
        .sort((a, b) => b.count - a.count)
        .slice(0, 5);
});

// Calcular el máximo para las barras de progreso
const maxCount = computed(() => {
    return Math.max(...props.data.data, 1);
});

// Función para calcular el porcentaje de la barra
const getBarWidth = (count: number) => {
    return (count / maxCount.value) * 100;
};

</script>

<template>
    <div v-if="isLoading" class="space-y-2">
        <Skeleton class="h-12 w-full" />
        <Skeleton class="h-12 w-full" />
        <Skeleton class="h-12 w-full" />
        <Skeleton class="h-12 w-full" />
        <Skeleton class="h-12 w-full" />
    </div>

    <div v-else-if="topHours.length > 0" class="space-y-2">
        <div
            v-for="(item, index) in topHours"
            :key="item.hour"
            class="flex items-center justify-between p-3 rounded-md border bg-card hover:bg-muted/20 transition-colors"
        >
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary font-semibold text-xs">
                    {{ index + 1 }}
                </div>
                <div>
                    <div class="font-medium text-sm text-foreground">{{ item.hour }}</div>
                    <div class="text-xs text-muted-foreground">{{ item.count }} atenciones</div>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <div class="font-semibold text-sm text-foreground">{{ item.count }}</div>
                    <div class="text-xs text-muted-foreground">{{ Math.round(getBarWidth(item.count)) }}%</div>
                </div>

                <!-- Barra de progreso horizontal -->
                <div class="w-16 h-2 bg-muted rounded-full overflow-hidden">
                    <div
                        class="h-full bg-gradient-to-r from-primary to-primary/80 rounded-full transition-all duration-500"
                        :style="{ width: `${getBarWidth(item.count)}%` }"
                    ></div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="flex flex-col items-center justify-center py-8 text-center">
        <h3 class="text-sm font-medium text-foreground mb-1">No hay datos de horarios</h3>
        <p class="text-xs text-muted-foreground">No se encontraron atenciones en este período</p>
    </div>
</template>
