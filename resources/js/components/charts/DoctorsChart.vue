<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Clock, UserCheck } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

interface Props {
    data: {
        labels: string[];
        shifts_data: number[];
        hours_data: number[];
    };
    isLoading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    isLoading: false,
});

// Combinar datos en un array de objetos para ordenar por horas
const doctorsList = computed(() => {
    return props.data.labels.map((name, index) => ({
        name,
        shifts: props.data.shifts_data[index] || 0,
        hours: props.data.hours_data[index] || 0,
    })).sort((a, b) => b.hours - a.hours);
});

const maxHours = computed(() => {
    return Math.max(...props.data.hours_data);
});

const getHoursPercentage = (hours: number) => {
    if (maxHours.value === 0) return '0%';
    return `${(hours / maxHours.value) * 100}%`;
};

const formatHours = (hours: number) => {
    if (hours < 1) return `${(hours * 60).toFixed(0)}min`;
    return `${hours.toFixed(1)}h`;
};

const navigateToDoctor = (doctorName: string) => {
    // Buscar el ID del doctor por nombre (esto requeriría datos adicionales del backend)
    // Por ahora, navegamos a la lista de doctores con un filtro
    router.get('/doctors', { search: doctorName });
};
</script>

<template>
    <div class="relative h-80 overflow-y-auto">
        <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center">
            <div class="animate-pulse space-y-4 w-full">
                <div v-for="i in 5" :key="i" class="h-16 bg-muted rounded-lg"></div>
            </div>
        </div>
        <div v-else-if="doctorsList.length > 0" class="space-y-4 p-1">
            <div
                v-for="(doctor, index) in doctorsList"
                :key="doctor.name"
                class="group relative p-4 rounded-lg bg-gradient-to-r from-background to-muted/20 border border-border/50 hover:border-primary/20 transition-all duration-300 cursor-pointer"
                @click="navigateToDoctor(doctor.name)"
            >
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-7 h-7 rounded-full bg-primary/10 text-primary text-sm font-bold">
                            {{ index + 1 }}
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-foreground group-hover:text-primary transition-colors">
                                {{ doctor.name }}
                            </span>
                            <div class="flex items-center gap-3 mt-1">
                                <div class="flex items-center gap-1 text-xs text-muted-foreground">
                                    <UserCheck class="h-3 w-3" />
                                    <span>{{ doctor.shifts }} guardias</span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-muted-foreground">
                                    <Clock class="h-3 w-3" />
                                    <span>{{ formatHours(doctor.hours) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <Badge variant="outline" class="text-xs font-medium">
                        {{ formatHours(doctor.hours) }} total
                    </Badge>
                </div>

                <!-- Barra de progreso para horas -->
                <div class="relative h-2 bg-muted rounded-full overflow-hidden">
                    <div
                        class="absolute inset-y-0 left-0 bg-gradient-to-r from-primary to-primary/80 rounded-full transition-all duration-700 ease-out"
                        :style="{ width: getHoursPercentage(doctor.hours) }"
                    ></div>
                </div>
            </div>
        </div>
        <div v-else class="flex items-center justify-center h-full text-muted-foreground">
            <div class="text-center">
                <div class="text-4xl mb-2">👨‍⚕️</div>
                <p class="text-sm">No hay datos de doctores</p>
                <p class="text-xs text-muted-foreground/70">Registra guardias para ver estadísticas</p>
            </div>
        </div>
    </div>
</template>
