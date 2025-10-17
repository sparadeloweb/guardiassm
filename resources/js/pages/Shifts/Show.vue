<script setup lang="ts">
import ShiftsController from '@/actions/App/Http/Controllers/Shifts/ShiftsController';
import PathologiesController from '@/actions/App/Http/Controllers/Patients/PathologiesController';
import DoctorsController from '@/actions/App/Http/Controllers/Doctors/DoctorsController';
import ShiftTypesController from '@/actions/App/Http/Controllers/Shifts/ShiftTypesController';
import PatientsController from '@/actions/App/Http/Controllers/Patients/PatientsController';
import DeleteShiftDrawer from '@/components/DeleteShiftDrawer.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import SuccessDialog from '@/components/SuccessDialog.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
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
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Pencil,
    Trash2,
    Clock,
    User,
    Briefcase,
    Calendar,
    Stethoscope,
    DollarSign,
    FileText,
    ExternalLink,
    Check,
} from 'lucide-vue-next';

interface Doctor {
    id: number;
    name: string;
}

interface ShiftType {
    id: number;
    name: string;
    value: number;
    patient_value: number;
}

interface Pathology {
    id: number;
    name: string;
}

interface Patient {
    id: number;
    name: string;
    DNI: string;
}

interface Attention {
    id: number;
    patient_id: number;
    attended_at: string;
    notes: string | null;
    patient: Patient;
    pathologies: Pathology[];
}

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
    updated_at: string;
    doctor: Doctor;
    shift_type: ShiftType;
    attentions: Attention[];
}

interface Props {
    shift: Shift;
    success?: string;
}

const props = defineProps<Props>();


const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Guardias',
        href: ShiftsController.index().url,
    },
    {
        title: 'Detalles',
        href: '#',
    },
];

// Estados para los modales
const showDeleteDrawer = ref(false);
const showSuccessDialog = ref(false);
const successMessage = ref('');

// Obtener mensajes flash
const page = usePage();
const flashMessage = computed(() => (page.props as any).flash?.success);

// Mostrar diálogo de éxito si hay mensaje flash
if (flashMessage.value) {
    showSuccessDialog.value = true;
    successMessage.value = flashMessage.value as string;
}

// Función para formatear fecha y hora
const formatDateTime = (dateTime: string) => {
    if (!dateTime) return '';
    const date = new Date(dateTime);
    return date.toLocaleString('es-AR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Función para formatear moneda
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
    }).format(amount);
};

// Función para formatear duración
const formatDuration = (hours: number) => {
    const wholeHours = Math.floor(hours);
    const minutes = Math.round((hours - wholeHours) * 60);
    return `${wholeHours}h ${minutes}m`;
};

// Función para obtener el estado de pago
const getPaymentStatus = (paidAt: string | null) => {
    return paidAt ? 'Pagado' : 'Pendiente';
};

// Función para obtener el color del badge de estado
const getPaymentStatusColor = (paidAt: string | null) => {
    return paidAt ? 'default' : 'destructive';
};

// Función para confirmar eliminación
const confirmDelete = () => {
    showDeleteDrawer.value = false;
    router.delete(ShiftsController.destroy(props.shift.id).url, {
        onSuccess: () => {
            router.visit(ShiftsController.index().url);
        },
    });
};

// Función para obtener el nombre de la patología
const getPathologyName = (pathologyId: number) => {
    const pathology = props.shift.attentions
        .flatMap(attention => attention.pathologies)
        .find(p => p.id === pathologyId);
    return pathology ? pathology.name : '';
};

// Función para marcar como pagada
const markAsPaid = () => {
    router.put(`/shifts/${props.shift.id}/mark-as-paid`, {}, {
        onSuccess: () => {
            successMessage.value = 'Guardia marcada como pagada correctamente';
            showSuccessDialog.value = true;
        },
        onError: (errors) => {
            console.error('Error al marcar como pagada:', errors);
            successMessage.value = 'Error al marcar como pagada';
            showSuccessDialog.value = true;
        }
    });
};

// Función para cerrar el modal de éxito y recargar los datos
const closeSuccessDialog = () => {
    showSuccessDialog.value = false;
    // Recargar solo los datos del shift para actualizar el estado de pago
    router.reload({ only: ['shift'] });
};
</script>

<template>
    <Head title="Detalles de Guardia" />
    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Detalles de Guardia"
                    :description="`Guardia #${shift.id} - ${shift.doctor?.name || 'N/A'}`"
                />
                <div class="flex gap-2">
                    <Button as-child>
                        <Link :href="ShiftsController.edit(shift.id).url">
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
            <Button
                v-if="!shift.paid_at"
                variant="default"
                @click="markAsPaid"
            >
                <Check class="mr-2 h-4 w-4" />
                Marcar como Pagada
            </Button>
                    <Button variant="destructive" @click="showDeleteDrawer = true">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Información General -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Briefcase class="h-5 w-5" />
                            Información General
                        </CardTitle>
                        <CardDescription>
                            Detalles básicos de la guardia
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <User class="h-4 w-4" />
                                    Doctor
                                </div>
                                <Link
                                    v-if="shift.doctor"
                                    :href="DoctorsController.show(shift.doctor.id).url"
                                    class="font-medium text-primary hover:text-primary/80 underline decoration-dotted underline-offset-2 flex items-center gap-1"
                                >
                                    {{ shift.doctor.name }}
                                    <ExternalLink class="h-3 w-3" />
                                </Link>
                                <span v-else class="font-medium">N/A</span>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Briefcase class="h-4 w-4" />
                                    Tipo de Guardia
                                </div>
                                <Link
                                    v-if="shift.shift_type"
                                    :href="ShiftTypesController.show(shift.shift_type.id).url"
                                    class="font-medium text-primary hover:text-primary/80 underline decoration-dotted underline-offset-2 flex items-center gap-1"
                                >
                                    {{ shift.shift_type.name }}
                                    <ExternalLink class="h-3 w-3" />
                                </Link>
                                <span v-else class="font-medium">N/A</span>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Clock class="h-4 w-4" />
                                    Duración
                                </div>
                                <span class="font-medium">{{ formatDuration(shift.total_hours) }}</span>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Stethoscope class="h-4 w-4" />
                                    Pacientes Atendidos
                                </div>
                                <span class="font-medium">{{ shift.patients_count }}</span>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <DollarSign class="h-4 w-4" />
                                    Total
                                </div>
                                <Badge variant="default" class="text-sm">
                                    {{ formatCurrency(shift.total_amount) }}
                                </Badge>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Calendar class="h-4 w-4" />
                                    Estado de Pago
                                </div>
                                <div class="text-right">
                                    <Badge :variant="getPaymentStatusColor(shift.paid_at)">
                                        {{ getPaymentStatus(shift.paid_at) }}
                                    </Badge>
                                    <div v-if="shift.paid_at" class="text-xs text-muted-foreground mt-1">
                                        Pagado el {{ formatDateTime(shift.paid_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Horarios -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Horarios
                        </CardTitle>
                        <CardDescription>
                            Fechas y horas de la guardia
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Clock class="h-4 w-4" />
                                    Inicio
                                </div>
                                <span class="font-medium">{{ formatDateTime(shift.starts_at) }}</span>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Clock class="h-4 w-4" />
                                    Fin
                                </div>
                                <span class="font-medium">{{ formatDateTime(shift.ends_at) }}</span>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <DollarSign class="h-4 w-4" />
                                    Tarifa por Hora
                                </div>
                                <span class="font-medium">{{ formatCurrency(shift.hourly_rate_snapshot) }}</span>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Stethoscope class="h-4 w-4" />
                                    Tarifa por Paciente
                                </div>
                                <span class="font-medium">{{ formatCurrency(shift.per_patient_rate_snapshot) }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Notas -->
            <Card v-if="shift.notes">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <FileText class="h-5 w-5" />
                        Notas
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-muted-foreground">{{ shift.notes }}</p>
                </CardContent>
            </Card>

            <!-- Atenciones -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Stethoscope class="h-5 w-5" />
                        Atenciones Realizadas
                    </CardTitle>
                    <CardDescription>
                        Lista de pacientes atendidos durante la guardia
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="shift.attentions.length === 0" class="text-center py-8">
                        <Stethoscope class="mx-auto h-12 w-12 text-muted-foreground" />
                        <h3 class="mt-2 text-sm font-semibold">No hay atenciones</h3>
                        <p class="mt-1 text-sm text-muted-foreground">
                            No se registraron atenciones para esta guardia
                        </p>
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="attention in shift.attentions"
                            :key="attention.id"
                            class="rounded-lg border p-4"
                        >
                            <div class="flex items-start justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <User class="h-4 w-4 text-muted-foreground" />
                                        <Link
                                            :href="PatientsController.show(attention.patient.id).url"
                                            class="font-medium text-primary hover:text-primary/80 underline decoration-dotted underline-offset-2 flex items-center gap-1"
                                        >
                                            {{ attention.patient.name }} - DNI: {{ attention.patient.DNI }}
                                            <ExternalLink class="h-3 w-3" />
                                        </Link>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                        <Clock class="h-4 w-4" />
                                        {{ formatDateTime(attention.attended_at) }}
                                    </div>
                                    <div v-if="attention.notes" class="flex items-start gap-2 text-sm text-muted-foreground">
                                        <FileText class="h-4 w-4 mt-0.5" />
                                        {{ attention.notes }}
                                    </div>
                                    <div v-if="attention.pathologies.length > 0" class="flex flex-wrap gap-1">
                                        <Link
                                            v-for="pathology in attention.pathologies"
                                            :key="pathology.id"
                                            :href="PathologiesController.show(pathology.id).url"
                                            class="inline-block"
                                        >
                                            <Badge
                                                variant="secondary"
                                                class="text-xs hover:bg-secondary/80 transition-colors cursor-pointer font-medium text-primary hover:text-primary/80 underline decoration-dotted underline-offset-2 flex items-center gap-1"
                                            >
                                                <Stethoscope class="h-3 w-3" />
                                                {{ pathology.name }}
                                                <ExternalLink class="h-3 w-3" />
                                            </Badge>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>

    <DeleteShiftDrawer
        :open="showDeleteDrawer"
        :shift="shift"
        @close="showDeleteDrawer = false"
        @confirm="confirmDelete"
    />

    <SuccessDialog
        :open="showSuccessDialog"
        :message="successMessage"
        @close="closeSuccessDialog"
    />
</template>
