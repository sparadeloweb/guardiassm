<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { router } from '@inertiajs/vue3';

interface Props {
    data: {
        labels: string[];
        data: number[];
    };
    isLoading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    isLoading: false,
});

// Combinar labels y data en un array de objetos para ordenar
const pathologiesList = computed(() => {
    return props.data.labels.map((label, index) => ({
        name: label,
        count: props.data.data[index] || 0,
    })).sort((a, b) => b.count - a.count);
});

const totalAttentions = computed(() => {
    return props.data.data.reduce((sum, count) => sum + count, 0);
});

const getPercentage = (count: number) => {
    if (totalAttentions.value === 0) return 0;
    return ((count / totalAttentions.value) * 100).toFixed(1);
};

const getBarWidth = (count: number) => {
    if (totalAttentions.value === 0) return '0%';
    const maxCount = Math.max(...props.data.data);
    return `${(count / maxCount) * 100}%`;
};

const navigateToPathology = (pathologyName: string) => {
    // Buscar el ID de la patología por nombre (esto requeriría datos adicionales del backend)
    // Por ahora, navegamos a la lista de patologías con un filtro
    router.get('/pathologies', { search: pathologyName });
};
</script>

<template>
    <div class="relative h-80 overflow-y-auto">
        <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center">
            <div class="animate-pulse space-y-4 w-full">
                <div v-for="i in 5" :key="i" class="h-12 bg-muted rounded-lg"></div>
            </div>
        </div>
        <div v-else-if="pathologiesList.length > 0" class="space-y-3 p-1">
            <div
                v-for="(pathology, index) in pathologiesList"
                :key="pathology.name"
                class="group relative cursor-pointer hover:bg-muted/20 rounded-lg p-2 transition-colors"
                @click="navigateToPathology(pathology.name)"
            >
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-sm font-semibold">
                            {{ index + 1 }}
                        </div>
                        <span class="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                            {{ pathology.name }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge variant="outline" class="text-xs">
                            {{ getPercentage(pathology.count) }}%
                        </Badge>
                        <span class="text-sm font-semibold text-foreground">
                            {{ pathology.count }}
                        </span>
                    </div>
                </div>
                <div class="relative h-2 bg-muted rounded-full overflow-hidden">
                    <div
                        class="absolute inset-y-0 left-0 bg-gradient-to-r from-primary to-primary/80 rounded-full transition-all duration-500 ease-out"
                        :style="{ width: getBarWidth(pathology.count) }"
                    ></div>
                </div>
            </div>
        </div>
        <div v-else class="flex items-center justify-center h-full text-muted-foreground">
            <div class="text-center">
                <div class="text-4xl mb-2">📊</div>
                <p class="text-sm">No hay datos de patologías</p>
                <p class="text-xs text-muted-foreground/70">Registra atenciones para ver estadísticas</p>
            </div>
        </div>
    </div>
</template>
