<script setup lang="ts">
import ShiftsController from '@/actions/App/Http/Controllers/Shifts/ShiftsController';
import DeleteShiftDrawer from '@/components/DeleteShiftDrawer.vue';
import EmptyState from '@/components/EmptyState.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import SuccessDialog from '@/components/SuccessDialog.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    Eye,
    Pencil,
    Trash2,
    Search,
    ArrowUpDown,
    ArrowUp,
    ArrowDown,
    Clock,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Shift {
    id: number;
    doctor_id: number;
    shift_type_id: number;
    hourly_rate_snapshot: number;
    per_patient_rate_snapshot: number;
    starts_at: string;
    ends_at: string;
    total_hours: number;
    patients_count: number;
    total_amount: number;
    notes: string | null;
    paid_at: string | null;
    created_at: string;
    doctor: {
        id: number;
        name: string;
    };
    shift_type: {
        id: number;
        name: string;
    };
}

interface Props {
    shifts: Shift[];
    success?: string;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Guardias',
        href: '#',
    },
    {
        title: 'Guardias',
        href: ShiftsController.index().url,
    },
];

// Estado de búsqueda y ordenamiento
const searchQuery = ref('');
const sortColumn = ref<keyof Shift | null>(null);
const sortDirection = ref<'asc' | 'desc'>('asc');

// Estado para el drawer de eliminación
const isDeleteDrawerOpen = ref(false);
const shiftToDelete = ref<Shift | null>(null);

// Estado para el dialog de éxito
const isSuccessDialogOpen = ref(false);
const successMessage = ref('');

// Detectar mensaje de éxito del controlador
if (props.success) {
    successMessage.value = props.success;
    isSuccessDialogOpen.value = true;
}

// Función para ordenar
const toggleSort = (column: keyof Shift) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }
};

// Obtener icono de ordenamiento
const getSortIcon = (column: keyof Shift) => {
    if (sortColumn.value !== column) return ArrowUpDown;
    return sortDirection.value === 'asc' ? ArrowUp : ArrowDown;
};

// Guardias filtradas y ordenadas
const filteredShifts = computed(() => {
    let result = [...props.shifts];

    // Filtrar por búsqueda
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (shift) =>
                shift.doctor.name.toLowerCase().includes(query) ||
                shift.shift_type.name.toLowerCase().includes(query) ||
                shift.notes?.toLowerCase().includes(query),
        );
    }

    // Ordenar
    if (sortColumn.value) {
        result.sort((a, b) => {
            let aVal = a[sortColumn.value!];
            let bVal = b[sortColumn.value!];

            // Para relaciones anidadas
            if (sortColumn.value === 'doctor') {
                aVal = a.doctor.name;
                bVal = b.doctor.name;
            } else if (sortColumn.value === 'shift_type') {
                aVal = a.shift_type.name;
                bVal = b.shift_type.name;
            }

            let comparison = 0;
            if (typeof aVal === 'string' && typeof bVal === 'string') {
                comparison = aVal.localeCompare(bVal);
            } else if (typeof aVal === 'number' && typeof bVal === 'number') {
                comparison = aVal - bVal;
            }

            return sortDirection.value === 'asc' ? comparison : -comparison;
        });
    }

    return result;
});

const openDeleteDrawer = (shift: Shift) => {
    shiftToDelete.value = shift;
    isDeleteDrawerOpen.value = true;
};

const confirmDelete = () => {
    if (shiftToDelete.value) {
        router.delete(ShiftsController.destroy(shiftToDelete.value.id).url, {
            onSuccess: () => {
                isDeleteDrawerOpen.value = false;
                shiftToDelete.value = null;
                isSuccessDialogOpen.value = true;
            },
        });
    }
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
    }).format(amount);
};

const formatDateTime = (dateTime: string) => {
    return new Intl.DateTimeFormat('es-AR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateTime));
};

const formatDate = (dateTime: string) => {
    return new Intl.DateTimeFormat('es-AR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    }).format(new Date(dateTime));
};

const formatTime = (dateTime: string) => {
    return new Intl.DateTimeFormat('es-AR', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).format(new Date(dateTime));
};

const getStatusBadge = (paidAt: string | null) => {
    return paidAt ? 'Pagado' : 'Pendiente';
};

const getStatusVariant = (paidAt: string | null) => {
    return paidAt ? 'default' : 'secondary';
};
</script>

<template>
    <Head title="Guardias" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Guardias"
                    description="Administra todas las guardias registradas en el sistema"
                />
                <Button as-child>
                    <Link :href="ShiftsController.create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Nueva Guardia
                    </Link>
                </Button>
            </div>

            <EmptyState
                v-if="shifts.length === 0"
                :icon="Clock"
                title="¡Uups! No se encontraron registros"
                description="No hay guardias registradas en el sistema. Comienza creando tu primera guardia."
            >
                <template #action>
                    <Button as-child size="lg">
                        <Link :href="ShiftsController.create().url">
                            <Plus class="mr-2 h-4 w-4" />
                            Crear Primera Guardia
                        </Link>
                    </Button>
                </template>
            </EmptyState>

            <div v-else class="space-y-4">
                <!-- Barra de búsqueda -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Buscar Guardias</CardTitle>
                        <CardDescription>
                            Filtra por doctor, tipo de guardia o notas
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="relative">
                            <Search
                                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                v-model="searchQuery"
                                type="search"
                                placeholder="Buscar guardias..."
                                class="pl-10"
                            />
                        </div>
                    </CardContent>
                </Card>

                <!-- Tabla de guardias -->
                <Card>
                    <CardContent class="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('doctor')"
                                        >
                                            Doctor
                                            <component
                                                :is="getSortIcon('doctor')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('shift_type')"
                                        >
                                            Tipo de Guardia
                                            <component
                                                :is="getSortIcon('shift_type')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('starts_at')"
                                        >
                                            Fecha y Hora
                                            <component
                                                :is="getSortIcon('starts_at')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('total_hours')"
                                        >
                                            Duración
                                            <component
                                                :is="getSortIcon('total_hours')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('patients_count')"
                                        >
                                            Pacientes
                                            <component
                                                :is="getSortIcon('patients_count')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('total_amount')"
                                        >
                                            Monto Total
                                            <component
                                                :is="getSortIcon('total_amount')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('paid_at')"
                                        >
                                            Estado
                                            <component
                                                :is="getSortIcon('paid_at')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-if="filteredShifts.length === 0"
                                >
                                    <TableCell
                                        colspan="8"
                                        class="h-24 text-center"
                                    >
                                        <EmptyState
                                            :icon="Search"
                                            title="No se encontraron resultados"
                                            description="Intenta con otros términos de búsqueda"
                                        />
                                    </TableCell>
                                </TableRow>
                                <TableRow
                                    v-for="shift in filteredShifts"
                                    :key="shift.id"
                                >
                                    <TableCell class="font-medium text-center text-foreground">
                                        {{ shift.doctor.name }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ shift.shift_type.name }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium">{{ formatDate(shift.starts_at) }}</span>
                                            <span class="text-xs text-muted-foreground">
                                                {{ formatTime(shift.starts_at) }} - {{ formatTime(shift.ends_at) }}
                                            </span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ shift.total_hours }}h
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="outline">
                                            {{ shift.patients_count }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="default">
                                            {{ formatCurrency(shift.total_amount) }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="getStatusVariant(shift.paid_at)">
                                            {{ getStatusBadge(shift.paid_at) }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <Button variant="ghost" size="icon" as-child>
                                                <Link :href="ShiftsController.show(shift.id).url">
                                                    <Eye class="h-4 w-4" />
                                                    <span class="sr-only">Ver guardia</span>
                                                </Link>
                                            </Button>

                                            <Button variant="ghost" size="icon" as-child>
                                                <Link :href="ShiftsController.edit(shift.id).url">
                                                    <Pencil class="h-4 w-4" />
                                                    <span class="sr-only">Editar guardia</span>
                                                </Link>
                                            </Button>

                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="openDeleteDrawer(shift)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                                <span class="sr-only">Eliminar guardia</span>
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Delete Drawer -->
        <DeleteShiftDrawer
            v-if="shiftToDelete"
            :shift="shiftToDelete"
            :open="isDeleteDrawerOpen"
            @close="isDeleteDrawerOpen = false"
        />

        <!-- Success Dialog -->
        <SuccessDialog
            :open="isSuccessDialogOpen"
            :message="successMessage"
            @close="isSuccessDialogOpen = false"
        />
    </AppLayout>
</template>
