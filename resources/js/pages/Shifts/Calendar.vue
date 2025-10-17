<script setup lang="ts">
import ShiftsController from '@/actions/App/Http/Controllers/Shifts/ShiftsController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    ChevronLeft,
    ChevronRight,
    Clock,
    Calendar as CalendarIcon,
    Eye,
    User,
    Briefcase,
    DollarSign,
    Users,
    ChevronDown,
    ChevronUp,
    MoreVertical,
    MessageCircle,
} from 'lucide-vue-next';

interface ShiftData {
    id: number;
    doctor: string;
    doctor_phone: string | null;
    shift_type: string;
    starts_at: string;
    ends_at: string;
    total_hours: number;
    patients_count: number;
    total_amount: number;
    paid_at: string | null;
    is_past: boolean;
}

interface Props {
    calendar: Record<string, ShiftData[]>;
    month: number;
    year: number;
    month_name: string;
    days_in_month: number;
    first_day_of_week: number;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Guardias',
        href: ShiftsController.index().url,
    },
    {
        title: 'Calendario',
        href: '#',
    },
];

// Estado para navegación
const currentMonth = ref(props.month);
const currentYear = ref(props.year);

// Estado para expandir días
const expandedDays = ref<Set<string>>(new Set());

// Días de la semana
const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

// Nombres de los meses en español
const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

// Función para navegar al mes anterior
const previousMonth = () => {
    if (currentMonth.value === 1) {
        currentMonth.value = 12;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
    loadCalendar();
};

// Función para navegar al mes siguiente
const nextMonth = () => {
    if (currentMonth.value === 12) {
        currentMonth.value = 1;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
    loadCalendar();
};

// Función para ir al mes actual
const goToCurrentMonth = () => {
    const today = new Date();
    currentMonth.value = today.getMonth() + 1;
    currentYear.value = today.getFullYear();
    loadCalendar();
};

// Función para cargar el calendario
const loadCalendar = () => {
    router.get(
        ShiftsController.calendar().url,
        {
            month: currentMonth.value,
            year: currentYear.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

// Generar días del calendario (incluyendo días vacíos al inicio)
const calendarDays = computed(() => {
    const days: Array<{ date: string; day: number; shifts: ShiftData[]; isCurrentMonth: boolean }> = [];

    // Añadir días vacíos al inicio
    for (let i = 0; i < props.first_day_of_week; i++) {
        days.push({
            date: '',
            day: 0,
            shifts: [],
            isCurrentMonth: false,
        });
    }

    // Añadir días del mes
    for (let day = 1; day <= props.days_in_month; day++) {
        const dateKey = `${props.year}-${String(props.month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        days.push({
            date: dateKey,
            day,
            shifts: props.calendar[dateKey] || [],
            isCurrentMonth: true,
        });
    }

    return days;
});

// Función para formatear moneda
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

// Función para formatear hora
const formatTime = (dateTime: string) => {
    const date = new Date(dateTime);
    return date.toLocaleTimeString('es-AR', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
};

// Función para verificar si es hoy
const isToday = (dateKey: string) => {
    const today = new Date();
    const todayKey = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    return dateKey === todayKey;
};

// Función para obtener clases del día
const getDayClasses = (day: { date: string; day: number; shifts: ShiftData[]; isCurrentMonth: boolean }) => {
    if (!day.isCurrentMonth) return 'bg-muted/30';

    const classes = ['bg-background hover:bg-muted/50 transition-colors'];

    if (isToday(day.date)) {
        classes.push('ring-2 ring-primary ring-inset');
    }

    return classes.join(' ');
};

// Función para alternar expansión de un día
const toggleDayExpansion = (dateKey: string) => {
    if (expandedDays.value.has(dateKey)) {
        expandedDays.value.delete(dateKey);
    } else {
        expandedDays.value.add(dateKey);
    }
};

// Función para verificar si un día está expandido
const isDayExpanded = (dateKey: string) => {
    return expandedDays.value.has(dateKey);
};

// Función para enviar notificación de WhatsApp
const sendWhatsAppNotification = (shift: ShiftData) => {
    if (!shift.doctor_phone) {
        alert('El doctor no tiene un número de teléfono registrado.');
        return;
    }

    // Formatear fecha y hora para el mensaje
    const startDate = new Date(shift.starts_at);
    const formattedDate = startDate.toLocaleDateString('es-AR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
    const formattedTime = startDate.toLocaleTimeString('es-AR', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });

    // Crear mensaje (usando códigos Unicode para emojis para evitar problemas de encoding)
    const waveEmoji = String.fromCodePoint(0x1F44B);
    const calendarEmoji = String.fromCodePoint(0x1F4C5);
    const clockEmoji = String.fromCodePoint(0x23F0);
    const hospitalEmoji = String.fromCodePoint(0x1F3E5);
    const timerEmoji = String.fromCodePoint(0x23F1);

    const message = `Hola ${shift.doctor}! ${waveEmoji}\n\nTe recordamos tu próxima guardia:\n\n${calendarEmoji} Fecha: ${formattedDate}\n${clockEmoji} Hora: ${formattedTime}\n${hospitalEmoji} Tipo: ${shift.shift_type}\n${timerEmoji} Duración: ${shift.total_hours}h\n\n¡Nos vemos pronto!`;

    // Limpiar número de teléfono (quitar espacios, guiones, etc.)
    const cleanPhone = shift.doctor_phone.replace(/[^0-9]/g, '');

    // Crear URL de WhatsApp
    const whatsappUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`;

    // Abrir WhatsApp en una nueva ventana
    window.open(whatsappUrl, '_blank');
};

// Función para verificar si se puede enviar WhatsApp
const canSendWhatsApp = (shift: ShiftData) => {
    return shift.doctor_phone && !shift.is_past;
};

// Estadísticas del mes
const monthStats = computed(() => {
    let totalShifts = 0;
    let totalRevenue = 0;
    let totalPatients = 0;
    let paidShifts = 0;
    let unpaidShifts = 0;

    Object.values(props.calendar).forEach(shifts => {
        shifts.forEach(shift => {
            totalShifts++;
            totalRevenue += Number(shift.total_amount) || 0;
            totalPatients += Number(shift.patients_count) || 0;

            if (shift.paid_at) {
                paidShifts++;
            } else {
                unpaidShifts++;
            }
        });
    });

    return {
        totalShifts,
        totalRevenue,
        totalPatients,
        paidShifts,
        unpaidShifts,
    };
});
</script>

<template>
    <Head title="Calendario de Guardias" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <HeadingSmall
                    title="Calendario de Guardias"
                    :description="`${monthNames[currentMonth - 1]} ${currentYear}`"
                    class="hidden sm:block"
                />

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <!-- Mobile: Month/Year display and navigation -->
                    <div class="flex items-center justify-between gap-2 sm:order-2">
                        <Button variant="outline" size="icon" @click="previousMonth" class="flex-shrink-0">
                            <ChevronLeft class="h-4 w-4" />
                        </Button>
                        <div class="px-4 py-2 bg-muted rounded-md font-medium text-sm sm:min-w-[200px] text-center flex-1 sm:flex-none">
                            {{ monthNames[currentMonth - 1] }} {{ currentYear }}
                        </div>
                        <Button variant="outline" size="icon" @click="nextMonth" class="flex-shrink-0">
                            <ChevronRight class="h-4 w-4" />
                        </Button>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center gap-2 sm:order-1">
                        <Button as-child class="flex-1 sm:flex-none">
                            <Link :href="ShiftsController.create().url">
                                <Clock class="mr-2 h-4 w-4" />
                                <span class="hidden sm:inline">Nueva Guardia</span>
                                <span class="sm:hidden">Nueva</span>
                            </Link>
                        </Button>
                        <Button variant="outline" size="sm" @click="goToCurrentMonth" class="flex-1 sm:flex-none">
                            <CalendarIcon class="mr-2 h-4 w-4" />
                            Hoy
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas del mes -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Total Guardias</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ monthStats.totalShifts }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Pacientes</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ monthStats.totalPatients }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Monto Total</CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(monthStats.totalRevenue) }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Pagadas</CardTitle>
                        <Badge variant="default" class="h-4 px-1.5 text-xs">✓</Badge>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ monthStats.paidShifts }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Pendientes</CardTitle>
                        <Badge variant="secondary" class="h-4 px-1.5 text-xs">⏱</Badge>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ monthStats.unpaidShifts }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Calendario -->
            <Card>
                <CardContent class="p-0">
                    <!-- Desktop: Calendar Grid -->
                    <div class="hidden md:block">
                        <!-- Días de la semana -->
                        <div class="grid grid-cols-7 border-b">
                            <div
                                v-for="day in daysOfWeek"
                                :key="day"
                                class="p-4 text-center font-semibold text-sm border-r last:border-r-0 bg-muted/50"
                            >
                                {{ day }}
                            </div>
                        </div>

                        <!-- Días del mes -->
                        <div class="grid grid-cols-7">
                        <div
                            v-for="(dayData, index) in calendarDays"
                            :key="index"
                            :class="[
                                'min-h-[100px] border-r border-b last:border-r-0',
                                getDayClasses(dayData),
                            ]"
                        >
                            <div v-if="dayData.isCurrentMonth" class="p-2 h-full flex flex-col">
                                <!-- Número del día -->
                                <div class="flex items-center justify-between mb-2">
                                    <span
                                        :class="[
                                            'text-sm font-semibold',
                                            isToday(dayData.date) ? 'bg-primary text-primary-foreground rounded-full w-6 h-6 flex items-center justify-center' : ''
                                        ]"
                                    >
                                        {{ dayData.day }}
                                    </span>
                                    <Badge v-if="dayData.shifts.length > 0" variant="outline" class="text-xs">
                                        {{ dayData.shifts.length }}
                                    </Badge>
                                </div>

                                <!-- Guardias del día -->
                                <div class="space-y-1 overflow-y-auto flex-1">
                                    <template v-if="!isDayExpanded(dayData.date)">
                                        <!-- Vista compacta -->
                                        <DropdownMenu v-for="shift in dayData.shifts" :key="shift.id">
                                            <DropdownMenuTrigger as-child>
                                                <div
                                                    :class="[
                                                        'p-1.5 rounded text-xs border hover:shadow-md transition-all cursor-pointer',
                                                        shift.is_past
                                                            ? 'bg-muted/80 border-border/50 opacity-75'
                                                            : 'bg-primary/10 border-primary/30 hover:bg-primary/20',
                                                        shift.paid_at
                                                            ? 'border-l-4 border-l-green-500'
                                                            : 'border-l-4 border-l-yellow-500'
                                                    ]"
                                                >
                                                    <div class="flex items-center justify-between gap-1">
                                                        <span class="font-semibold truncate text-foreground text-[11px]">
                                                            {{ shift.doctor }}
                                                        </span>
                                                        <span class="text-muted-foreground text-[10px] flex-shrink-0">
                                                            {{ formatTime(shift.starts_at) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="start">
                                                <DropdownMenuItem as-child>
                                                    <Link :href="ShiftsController.show(shift.id).url" class="cursor-pointer flex items-center">
                                                        <Eye class="mr-2 h-4 w-4" />
                                                        Ver Detalles
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator v-if="canSendWhatsApp(shift)" />
                                                <DropdownMenuItem
                                                    v-if="canSendWhatsApp(shift)"
                                                    @click="sendWhatsAppNotification(shift)"
                                                    class="cursor-pointer"
                                                >
                                                    <MessageCircle class="mr-2 h-4 w-4" />
                                                    Notificar por WhatsApp
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </template>

                                    <template v-else>
                                        <!-- Vista expandida -->
                                        <DropdownMenu v-for="shift in dayData.shifts" :key="shift.id">
                                            <DropdownMenuTrigger as-child>
                                                <div
                                                    :class="[
                                                        'p-2 rounded text-xs border hover:shadow-md transition-all cursor-pointer',
                                                        shift.is_past
                                                            ? 'bg-muted/80 border-border/50 opacity-75'
                                                            : 'bg-primary/10 border-primary/30 hover:bg-primary/20',
                                                        shift.paid_at
                                                            ? 'border-l-4 border-l-green-500'
                                                            : 'border-l-4 border-l-yellow-500'
                                                    ]"
                                                >
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <User class="h-3 w-3 text-muted-foreground flex-shrink-0" />
                                                        <span class="font-semibold truncate text-foreground">{{ shift.doctor }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <Briefcase class="h-3 w-3 text-muted-foreground flex-shrink-0" />
                                                        <span class="text-muted-foreground truncate">{{ shift.shift_type }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <Clock class="h-3 w-3 text-muted-foreground flex-shrink-0" />
                                                        <span class="text-muted-foreground">
                                                            {{ formatTime(shift.starts_at) }} - {{ formatTime(shift.ends_at) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center justify-between gap-1">
                                                        <div class="flex items-center gap-1">
                                                            <Users class="h-3 w-3 text-muted-foreground flex-shrink-0" />
                                                            <span class="text-muted-foreground">{{ shift.patients_count }}</span>
                                                        </div>
                                                        <Badge
                                                            :variant="shift.paid_at ? 'default' : 'secondary'"
                                                            class="text-[10px] px-1 h-4"
                                                        >
                                                            {{ formatCurrency(shift.total_amount) }}
                                                        </Badge>
                                                    </div>
                                                </div>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="start">
                                                <DropdownMenuItem as-child>
                                                    <Link :href="ShiftsController.show(shift.id).url" class="cursor-pointer flex items-center">
                                                        <Eye class="mr-2 h-4 w-4" />
                                                        Ver Detalles
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator v-if="canSendWhatsApp(shift)" />
                                                <DropdownMenuItem
                                                    v-if="canSendWhatsApp(shift)"
                                                    @click="sendWhatsAppNotification(shift)"
                                                    class="cursor-pointer"
                                                >
                                                    <MessageCircle class="mr-2 h-4 w-4" />
                                                    Notificar por WhatsApp
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </template>

                                    <!-- Botón para expandir/colapsar si hay guardias -->
                                    <Button
                                        v-if="dayData.shifts.length > 0"
                                        variant="ghost"
                                        size="sm"
                                        class="w-full h-6 text-[10px] mt-1"
                                        @click.prevent="toggleDayExpansion(dayData.date)"
                                    >
                                        <ChevronDown v-if="!isDayExpanded(dayData.date)" class="h-3 w-3 mr-1" />
                                        <ChevronUp v-else class="h-3 w-3 mr-1" />
                                        {{ isDayExpanded(dayData.date) ? 'Compacto' : 'Expandir' }}
                                    </Button>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Mobile: List View -->
                    <div class="md:hidden divide-y">
                        <div
                            v-for="dayData in calendarDays.filter(d => d.isCurrentMonth)"
                            :key="dayData.date"
                            class="p-4"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        :class="[
                                            'text-lg font-bold',
                                            isToday(dayData.date) ? 'bg-primary text-primary-foreground rounded-full w-8 h-8 flex items-center justify-center' : ''
                                        ]"
                                    >
                                        {{ dayData.day }}
                                    </span>
                                    <span class="text-sm text-muted-foreground">
                                        {{ daysOfWeek[new Date(dayData.date).getDay()] }}
                                    </span>
                                </div>
                                <Badge v-if="dayData.shifts.length > 0" variant="outline">
                                    {{ dayData.shifts.length }} {{ dayData.shifts.length === 1 ? 'guardia' : 'guardias' }}
                                </Badge>
                            </div>

                            <!-- Guardias del día (Mobile) -->
                            <div v-if="dayData.shifts.length > 0" class="space-y-2">
                                <DropdownMenu v-for="shift in dayData.shifts" :key="shift.id">
                                    <DropdownMenuTrigger as-child>
                                        <div
                                            :class="[
                                                'p-3 rounded-lg border cursor-pointer',
                                                shift.is_past
                                                    ? 'bg-muted/80 border-border/50'
                                                    : 'bg-primary/10 border-primary/30',
                                                shift.paid_at
                                                    ? 'border-l-4 border-l-green-500'
                                                    : 'border-l-4 border-l-yellow-500'
                                            ]"
                                        >
                                            <div class="flex items-start justify-between gap-2 mb-2">
                                                <div class="flex items-center gap-2">
                                                    <User class="h-4 w-4 text-muted-foreground flex-shrink-0" />
                                                    <span class="font-semibold text-sm">{{ shift.doctor }}</span>
                                                </div>
                                                <Badge :variant="shift.paid_at ? 'default' : 'secondary'" class="text-xs">
                                                    {{ shift.paid_at ? 'Pagada' : 'Pendiente' }}
                                                </Badge>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
                                                <Briefcase class="h-3 w-3" />
                                                <span>{{ shift.shift_type }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
                                                <Clock class="h-3 w-3" />
                                                <span>{{ formatTime(shift.starts_at) }} - {{ formatTime(shift.ends_at) }}</span>
                                            </div>
                                            <div class="flex items-center justify-between text-xs">
                                                <div class="flex items-center gap-2 text-muted-foreground">
                                                    <Users class="h-3 w-3" />
                                                    <span>{{ shift.patients_count }} pacientes</span>
                                                </div>
                                                <span class="font-semibold">{{ formatCurrency(shift.total_amount) }}</span>
                                            </div>
                                        </div>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="start">
                                        <DropdownMenuItem as-child>
                                            <Link :href="ShiftsController.show(shift.id).url" class="cursor-pointer flex items-center">
                                                <Eye class="mr-2 h-4 w-4" />
                                                Ver Detalles
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator v-if="canSendWhatsApp(shift)" />
                                        <DropdownMenuItem
                                            v-if="canSendWhatsApp(shift)"
                                            @click="sendWhatsAppNotification(shift)"
                                            class="cursor-pointer"
                                        >
                                            <MessageCircle class="mr-2 h-4 w-4" />
                                            Notificar por WhatsApp
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                            <div v-else class="text-sm text-muted-foreground text-center py-2">
                                Sin guardias
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Leyenda -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-sm">Leyenda</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 border-l-4 border-l-green-500 bg-muted rounded"></div>
                            <span>Guardia Pagada</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 border-l-4 border-l-yellow-500 bg-muted rounded"></div>
                            <span>Guardia Pendiente</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-primary/10 border border-primary/30 rounded"></div>
                            <span>Guardia Futura</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-muted/80 border border-border/50 rounded"></div>
                            <span>Guardia Pasada</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-xs">15</div>
                            <span>Día Actual</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
