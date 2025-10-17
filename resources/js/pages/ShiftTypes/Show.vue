<script setup lang="ts">
import ShiftTypesController from '@/actions/App/Http/Controllers/Shifts/ShiftTypesController';
import ShiftsController from '@/actions/App/Http/Controllers/Shifts/ShiftsController';
import DoctorsController from '@/actions/App/Http/Controllers/Doctors/DoctorsController';
import DeleteShiftTypeDrawer from '@/components/DeleteShiftTypeDrawer.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import SuccessDialog from '@/components/SuccessDialog.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Pencil,
    Trash2,
    Briefcase,
    FileText,
    DollarSign,
    Calendar,
    Clock,
    Euro,
    Users,
    ExternalLink,
    User,
} from 'lucide-vue-next';

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
    notes?: string;
    paid_at?: string;
    created_at: string;
    updated_at: string;
    doctor?: {
        id: number;
        name: string;
        age: number;
        email: string;
        phone: string;
        address: string;
        is_resident: boolean;
    };
}

interface ShiftType {
    id: number;
    name: string;
    description: string;
    value: number;
    patient_value: number | null;
    created_at: string;
    updated_at: string;
    shifts: Shift[];
}

interface Props {
    shiftType: ShiftType;
    success?: string;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Tipos de Guardias',
        href: ShiftTypesController.index().url,
    },
    {
        title: props.shiftType.name,
        href: ShiftTypesController.show(props.shiftType.id).url,
    },
];

// Estado para el drawer de eliminación
const isDeleteDrawerOpen = ref(false);

// Estado para el dialog de éxito
const isSuccessDialogOpen = ref(false);
const successMessage = ref('');

// Detectar mensaje de éxito del controlador
if (props.success) {
    successMessage.value = props.success;
    isSuccessDialogOpen.value = true;
}

const openDeleteDrawer = () => {
    isDeleteDrawerOpen.value = true;
};

const confirmDelete = () => {
    router.delete(ShiftTypesController.destroy(props.shiftType.id).url, {
        onSuccess: () => {
            router.visit(ShiftTypesController.index().url);
        },
    });
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
        minimumFractionDigits: 2,
    }).format(value);
};

const formatDateTime = (date: string) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="shiftType.name" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <div class="flex items-center justify-between">
                <HeadingSmall
                    :title="shiftType.name"
                    description="Información completa del tipo de guardia"
                />

                <div class="flex gap-2">
                    <Button as-child>
                        <Link
                            :href="ShiftTypesController.edit(shiftType.id).url"
                        >
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
                    <Button variant="destructive" @click="openDeleteDrawer">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Información Principal -->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-2xl">{{
                                    shiftType.name
                                }}</CardTitle>
                            </div>
                        </div>
                    </CardHeader>

                    <CardContent class="space-y-6">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <Briefcase
                                    class="h-5 w-5 text-muted-foreground mt-0.5"
                                />
                                <div>
                                    <p class="text-sm font-medium">Nombre</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ shiftType.name }}
                                    </p>
                                </div>
                            </div>

                            <Separator />

                            <div class="flex items-start gap-3">
                                <FileText
                                    class="h-5 w-5 text-muted-foreground mt-0.5"
                                />
                                <div>
                                    <p class="text-sm font-medium">Descripción</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ shiftType.description }}
                                    </p>
                                </div>
                            </div>

                            <Separator />

                            <div class="flex items-start gap-3">
                                <DollarSign
                                    class="h-5 w-5 text-muted-foreground mt-0.5"
                                />
                                <div>
                                    <p class="text-sm font-medium">Valor por hora</p>
                                    <p class="text-sm text-muted-foreground font-mono">
                                        {{ formatCurrency(shiftType.value) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <DollarSign
                                    class="h-5 w-5 text-muted-foreground mt-0.5"
                                />
                                <div>
                                    <p class="text-sm font-medium">Valor paciente</p>
                                    <p class="text-sm text-muted-foreground font-mono">
                                        {{ shiftType.patient_value ? formatCurrency(shiftType.patient_value) : 'No especificado' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>

                </Card>

                <!-- Información Adicional -->
                <Card>
                    <CardHeader>
                        <CardTitle>Información Adicional</CardTitle>
                        <CardDescription>
                            Datos del registro
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <Calendar
                                class="h-5 w-5 text-muted-foreground mt-0.5"
                            />
                            <div>
                                <p class="text-sm font-medium">Creado</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ formatDate(shiftType.created_at) }}
                                </p>
                            </div>
                        </div>

                        <Separator />

                        <div class="flex items-start gap-3">
                            <Calendar
                                class="h-5 w-5 text-muted-foreground mt-0.5"
                            />
                            <div>
                                <p class="text-sm font-medium">
                                    Última actualización
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ formatDate(shiftType.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sección de Guardias -->
            <Card v-if="shiftType.shifts && shiftType.shifts.length > 0">
                <CardHeader>
                    <CardTitle class="text-lg">Guardias Realizadas</CardTitle>
                    <CardDescription>
                        Historial de todas las guardias que han usado este tipo de turno
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <Link
                            v-for="shift in shiftType.shifts"
                            :key="shift.id"
                            :href="ShiftsController.show(shift.id).url"
                            class="block border rounded-lg p-4 hover:bg-muted/50 transition-colors cursor-pointer relative"
                        >
                            <div class="absolute top-3 right-3">
                                <ExternalLink class="h-4 w-4 text-muted-foreground opacity-60 hover:opacity-100 transition-opacity" />
                            </div>

                            <!-- Header de la guardia -->
                            <div class="flex items-center justify-between mb-4 pr-8">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <Clock class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm font-medium text-muted-foreground">Guardia #{{ shift.id }}</span>
                                    </div>
                                    <div class="h-4 w-px bg-border"></div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ formatDateTime(shift.starts_at) }} - {{ formatDateTime(shift.ends_at) }}
                                        <span class="ml-2 font-medium">({{ shift.total_hours }}h)</span>
                                    </div>
                                </div>
                                <div class="text-lg font-semibold">
                                    {{ formatCurrency(shift.total_amount) }}
                                </div>
                            </div>

                            <!-- Información principal -->
                            <div class="grid gap-4 md:grid-cols-3">
                                <!-- Doctor -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <User class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm font-medium">Doctor</span>
                                    </div>
                                    <div class="text-sm">
                                        <Link
                                            v-if="shift.doctor"
                                            :href="DoctorsController.show(shift.doctor.id).url"
                                            class="font-medium text-primary hover:text-primary/80 underline decoration-dotted underline-offset-2 flex items-center gap-1"
                                        >
                                            {{ shift.doctor.name }}
                                            <ExternalLink class="h-3 w-3" />
                                        </Link>
                                        <span v-else class="text-muted-foreground">Sin especificar</span>
                                    </div>
                                </div>

                                <!-- Información de pacientes -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <Users class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm font-medium">Pacientes Atendidos</span>
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ shift.patients_count }} pacientes
                                    </div>
                                </div>

                                <!-- Estado de pago -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <Euro class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm font-medium">Estado de Pago</span>
                                    </div>
                                    <div class="text-sm">
                                        <div v-if="shift.paid_at" class="text-green-600 font-medium">
                                            Pagado
                                        </div>
                                        <div v-else class="text-orange-600 font-medium">
                                            Pendiente
                                        </div>
                                        <div v-if="shift.paid_at" class="text-xs text-muted-foreground">
                                            {{ formatDateTime(shift.paid_at) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notas del turno -->
                            <div v-if="shift.notes" class="mt-4 pt-4 border-t">
                                <div class="flex items-start gap-2">
                                    <FileText class="h-4 w-4 text-muted-foreground mt-0.5" />
                                    <div>
                                        <span class="text-sm font-medium">Notas:</span>
                                        <p class="text-sm text-muted-foreground mt-1">
                                            {{ shift.notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <!-- Mensaje cuando no hay guardias -->
            <Card v-else>
                <CardContent class="flex flex-col items-center justify-center py-8">
                    <Clock class="h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-medium mb-2">Sin guardias registradas</h3>
                    <p class="text-sm text-muted-foreground text-center">
                        Este tipo de turno aún no ha sido utilizado en ninguna guardia.
                    </p>
                </CardContent>
            </Card>

            <!-- Drawer de confirmación de eliminación -->
            <DeleteShiftTypeDrawer
                v-model:open="isDeleteDrawerOpen"
                :shift-type="shiftType"
                @confirm="confirmDelete"
            />

            <!-- Dialog de éxito -->
            <SuccessDialog
                v-model:open="isSuccessDialogOpen"
                :title="successMessage || 'Operación exitosa'"
                :description="successMessage ? '' : 'La operación se completó correctamente.'"
            />
        </div>
    </AppLayout>
</template>

