<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import PeakHoursChart from '@/components/charts/PeakHoursChart.vue';
import PathologiesChart from '@/components/charts/PathologiesChart.vue';
import DoctorsChart from '@/components/charts/DoctorsChart.vue';
import MonthlyEvolutionChart from '@/components/charts/MonthlyEvolutionChart.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import {
    Users,
    UserCheck,
    Stethoscope,
    Clock,
    DollarSign,
    TrendingUp,
    Calendar,
    Heart,
    Activity,
} from 'lucide-vue-next';

interface Stats {
    doctors_count: number;
    patients_count: number;
    pathologies_count: number;
    shifts_count: number;
    attentions_count: number;
    total_revenue: number;
    unpaid_shifts: number;
    paid_shifts: number;
}

interface ChartData {
    peak_hours: {
        labels: string[];
        data: number[];
    };
    top_pathologies: {
        labels: string[];
        data: number[];
    };
    top_doctors: {
        labels: string[];
        shifts_data: number[];
        hours_data: number[];
    };
    monthly_evolution: {
        labels: string[];
        data: number[];
    };
}

interface Props {
    stats: Stats;
    chartData: ChartData;
    period: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inicio',
        href: dashboard().url,
    },
];

// Filtro de período
const selectedPeriod = ref(props.period);
const isLoading = ref(false);

const periods = [
    { value: 'day', label: 'Hoy' },
    { value: 'week', label: 'Esta Semana' },
    { value: 'month', label: 'Este Mes' },
    { value: 'year', label: 'Este Año' },
    { value: 'total', label: 'Total' },
];

const updatePeriod = (period: string) => {
    selectedPeriod.value = period;
    isLoading.value = true;
    console.log('Cambiando período a:', period);
    router.get('/dashboard', { period }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

// Función para formatear moneda
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
    }).format(amount);
};

// Función para formatear números
const formatNumber = (number: number) => {
    return new Intl.NumberFormat('es-AR').format(number);
};
</script>

<template>
    <Head title="Inicio" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 overflow-x-auto p-6 bg-gradient-to-br from-background via-background to-muted/30">
            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Inicio"
                    description="Resumen general del sistema de guardias médicas"
                />

                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium">Período:</span>
                    <div class="flex gap-1">
                        <Button
                            v-for="period in periods"
                            :key="period.value"
                            :variant="selectedPeriod === period.value ? 'default' : 'outline'"
                            size="sm"
                            @click="updatePeriod(period.value)"
                            class="text-xs cursor-pointer"
                        >
                            {{ period.label }}
                        </Button>
                </div>
                </div>
            </div>

            <!-- Acciones rápidas simplificadas -->
            <div class="flex items-center gap-2 flex-wrap">
                <Link href="/shifts/create">
                    <Button variant="outline" size="sm" class="cursor-pointer">
                        <Clock class="mr-2 h-4 w-4" />
                        Nueva Guardia
                    </Button>
                </Link>
                <Link href="/doctors/create">
                    <Button variant="outline" size="sm" class="cursor-pointer">
                        <Users class="mr-2 h-4 w-4" />
                        Nuevo Doctor
                    </Button>
                </Link>
                <Link href="/patients/create">
                    <Button variant="outline" size="sm" class="cursor-pointer">
                        <Heart class="mr-2 h-4 w-4" />
                        Nuevo Paciente
                    </Button>
                </Link>
                <Link href="/shifts">
                    <Button variant="outline" size="sm" class="cursor-pointer">
                        <Calendar class="mr-2 h-4 w-4" />
                        Ver Guardias
                    </Button>
                </Link>
            </div>

            <!-- Cards de estadísticas básicas -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Doctores -->
                <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Doctores</CardTitle>
                        <div class="p-2 rounded-full bg-primary/10">
                            <Users class="h-4 w-4 text-primary" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold text-foreground">{{ formatNumber(stats.doctors_count) }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Profesionales registrados
                        </p>
                    </CardContent>
                </Card>

                <!-- Pacientes -->
                <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Pacientes</CardTitle>
                        <div class="p-2 rounded-full bg-primary/10">
                            <UserCheck class="h-4 w-4 text-primary" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold text-foreground">{{ formatNumber(stats.patients_count) }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Pacientes registrados
                        </p>
                    </CardContent>
                </Card>

                <!-- Patologías -->
                <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Patologías</CardTitle>
                        <div class="p-2 rounded-full bg-primary/10">
                            <Stethoscope class="h-4 w-4 text-primary" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold text-foreground">{{ formatNumber(stats.pathologies_count) }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Patologías catalogadas
                        </p>
                    </CardContent>
                </Card>

                <!-- Monto Total a Pagar -->
                <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Monto Total a Pagar</CardTitle>
                        <div class="p-2 rounded-full bg-primary/10">
                            <DollarSign class="h-4 w-4 text-primary" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div v-if="isLoading" class="space-y-2">
                            <Skeleton class="h-8 w-24" />
                            <Skeleton class="h-4 w-40" />
                        </div>
                        <div v-else>
                            <div class="text-3xl font-bold text-foreground">{{ formatCurrency(stats.total_revenue) }}</div>
                            <p class="text-xs text-muted-foreground mt-1">
                                Total de guardias registradas
                                <Badge variant="outline" class="ml-1 text-xs">
                                    {{ periods.find(p => p.value === selectedPeriod)?.label }}
                                </Badge>
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Cards de actividad (con filtros) -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Guardias Registradas -->
                <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Guardias</CardTitle>
                        <div class="p-2 rounded-full bg-primary/10">
                            <Clock class="h-4 w-4 text-primary" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div v-if="isLoading" class="space-y-2">
                            <Skeleton class="h-8 w-16" />
                            <Skeleton class="h-4 w-32" />
                        </div>
                        <div v-else>
                            <div class="text-3xl font-bold text-foreground">{{ formatNumber(stats.shifts_count) }}</div>
                            <p class="text-xs text-muted-foreground mt-1">
                                Guardias registradas
                                <Badge variant="outline" class="ml-1 text-xs">
                                    {{ periods.find(p => p.value === selectedPeriod)?.label }}
                                </Badge>
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Atenciones Registradas -->
                <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Atenciones</CardTitle>
                        <div class="p-2 rounded-full bg-primary/10">
                            <Activity class="h-4 w-4 text-primary" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div v-if="isLoading" class="space-y-2">
                            <Skeleton class="h-8 w-16" />
                            <Skeleton class="h-4 w-32" />
                        </div>
                        <div v-else>
                            <div class="text-3xl font-bold text-foreground">{{ formatNumber(stats.attentions_count) }}</div>
                            <p class="text-xs text-muted-foreground mt-1">
                                Atenciones realizadas
                                <Badge variant="outline" class="ml-1 text-xs">
                                    {{ periods.find(p => p.value === selectedPeriod)?.label }}
                                </Badge>
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Guardias Pagadas -->
                <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Guardias Pagadas</CardTitle>
                        <div class="p-2 rounded-full bg-primary/10">
                            <TrendingUp class="h-4 w-4 text-primary" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div v-if="isLoading" class="space-y-2">
                            <Skeleton class="h-8 w-16" />
                            <Skeleton class="h-4 w-32" />
                        </div>
                        <div v-else>
                            <div class="text-3xl font-bold text-foreground">{{ formatNumber(stats.paid_shifts) }}</div>
                            <p class="text-xs text-muted-foreground mt-1">
                                Guardias liquidadas
                                <Badge variant="outline" class="ml-1 text-xs">
                                    {{ periods.find(p => p.value === selectedPeriod)?.label }}
                                </Badge>
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Guardias Pendientes -->
                <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Guardias Pendientes</CardTitle>
                        <div class="p-2 rounded-full bg-primary/10">
                            <Calendar class="h-4 w-4 text-primary" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div v-if="isLoading" class="space-y-2">
                            <Skeleton class="h-8 w-16" />
                            <Skeleton class="h-4 w-32" />
                        </div>
                        <div v-else>
                            <div class="text-3xl font-bold text-foreground">{{ formatNumber(stats.unpaid_shifts) }}</div>
                            <p class="text-xs text-muted-foreground mt-1">
                                Guardias por liquidar
                                <Badge variant="outline" class="ml-1 text-xs">
                                    {{ periods.find(p => p.value === selectedPeriod)?.label }}
                                </Badge>
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sección de Gráficos -->
            <div class="space-y-6">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-1 bg-primary rounded-full"></div>
                    <h2 class="text-xl font-semibold">Análisis y Estadísticas</h2>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Gráfico de Horarios Pico -->
                    <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/3 to-transparent"></div>
                        <CardHeader class="relative z-10">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 rounded-full bg-primary/10">
                                    <Clock class="h-5 w-5 text-primary" />
                                </div>
                                Horarios Pico de Atención
                            </CardTitle>
                            <CardDescription>
                                Distribución de atenciones por hora del día
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="relative z-10">
                            <PeakHoursChart
                                :data="chartData.peak_hours"
                                :is-loading="isLoading"
                            />
                        </CardContent>
                    </Card>

                    <!-- Gráfico de Patologías -->
                    <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/3 to-transparent"></div>
                        <CardHeader class="relative z-10">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 rounded-full bg-primary/10">
                                    <Stethoscope class="h-5 w-5 text-primary" />
                                </div>
                                Patologías Más Frecuentes
                            </CardTitle>
                            <CardDescription>
                                Top 5 patologías más diagnosticadas
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="relative z-10">
                            <PathologiesChart
                                :data="chartData.top_pathologies"
                                :is-loading="isLoading"
                            />
                        </CardContent>
                    </Card>

                    <!-- Gráfico de Doctores -->
                    <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/3 to-transparent"></div>
                        <CardHeader class="relative z-10">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 rounded-full bg-primary/10">
                                    <Users class="h-5 w-5 text-primary" />
                                </div>
                                Doctores Más Activos
                            </CardTitle>
                            <CardDescription>
                                Doctores con más guardias y horas trabajadas
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="relative z-10">
                            <DoctorsChart
                                :data="chartData.top_doctors"
                                :is-loading="isLoading"
                            />
                        </CardContent>
                    </Card>

                    <!-- Gráfico de Evolución Mensual -->
                    <Card class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 border-border/50 hover:shadow-lg transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/3 to-transparent"></div>
                        <CardHeader class="relative z-10">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 rounded-full bg-primary/10">
                                    <Activity class="h-5 w-5 text-primary" />
                                </div>
                                Evolución de Atenciones
                            </CardTitle>
                            <CardDescription>
                                Evolución de atenciones según el período seleccionado
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="relative z-10">
                            <MonthlyEvolutionChart
                                :data="chartData.monthly_evolution"
                                :is-loading="isLoading"
                            />
                        </CardContent>
                    </Card>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
