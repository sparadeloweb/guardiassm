<script setup lang="ts">
import PathologiesController from '@/actions/App/Http/Controllers/Patients/PathologiesController';
import ShiftsController from '@/actions/App/Http/Controllers/Shifts/ShiftsController';
import DoctorsController from '@/actions/App/Http/Controllers/Doctors/DoctorsController';
import PatientsController from '@/actions/App/Http/Controllers/Patients/PatientsController';
import DeletePathologyDrawer from '@/components/DeletePathologyDrawer.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import SuccessDialog from '@/components/SuccessDialog.vue';
import { Button } from '@/components/ui/button';
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
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    Calendar,
    Edit,
    FileText,
    Stethoscope,
    Trash2,
    Clock,
    ExternalLink,
    User,
    Heart,
} from 'lucide-vue-next';

interface Patient {
    id: number;
    name: string;
    DNI: string | null;
    phone: string | null;
    address: string | null;
    email: string | null;
    gender: string | null;
    birth_date: string | null;
}

interface Doctor {
    id: number;
    name: string;
    age: number;
    email: string;
    phone: string;
    address: string;
    is_resident: boolean;
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
    notes?: string;
    paid_at?: string;
    created_at: string;
    updated_at: string;
    doctor?: Doctor;
}

interface Attention {
    id: number;
    patient_id: number;
    shift_id: number;
    attended_at: string;
    notes?: string;
    created_at: string;
    updated_at: string;
    shift?: Shift;
    patient?: Patient;
}

interface Pathology {
    id: number;
    name: string;
    description: string;
    created_at: string;
    updated_at: string;
    attentions: Attention[];
}

interface Props {
    pathology: Pathology;
}

const props = defineProps<Props>();
const page = usePage();

const showDeleteDrawer = ref(false);
const showSuccessDialog = ref(false);
const successMessage = ref('');

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Pacientes',
        href: '#',
    },
    {
        title: 'Patologías',
        href: PathologiesController.index().url,
    },
    {
        title: props.pathology.name,
        href: PathologiesController.show(props.pathology.id).url,
    },
];

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-AR');
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

const openDeleteDrawer = () => {
    showDeleteDrawer.value = true;
};

const closeDeleteDrawer = () => {
    showDeleteDrawer.value = false;
};

const confirmDelete = () => {
    router.delete(PathologiesController.destroy(props.pathology.id).url, {
        onSuccess: () => {
            closeDeleteDrawer();
            successMessage.value = 'Patología eliminada correctamente';
            showSuccessDialog.value = true;
        },
    });
};

const closeSuccessDialog = () => {
    showSuccessDialog.value = false;
};

// Check for success message from controller redirect
const flashMessage = computed(() => (page.props as any).flash?.success);
if (flashMessage.value) {
    successMessage.value = flashMessage.value;
    showSuccessDialog.value = true;
}
</script>

<template>
    <Head :title="pathology.name" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <div class="flex items-center justify-between">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-semibold">{{ pathology.name }}</h3>
                        <Badge
                            v-if="pathology.id === 1"
                            variant="secondary"
                        >
                            Patología por defecto
                        </Badge>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        Información detallada de la patología
                    </p>
                </div>

                <div class="flex gap-2">
                    <Button
                        v-if="pathology.id !== 1"
                        as-child
                    >
                        <Link :href="PathologiesController.edit(pathology.id).url">
                            <Edit class="mr-2 h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
                    <Button
                        v-if="pathology.id !== 1"
                        variant="destructive"
                        @click="openDeleteDrawer"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Stethoscope class="h-5 w-5" />
                            Información de la Patología
                        </CardTitle>
                        <CardDescription>
                            Datos básicos de la patología
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <Stethoscope class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Nombre</p>
                                <p class="text-sm text-muted-foreground">{{ pathology.name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <FileText class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Descripción</p>
                                <p class="text-sm text-muted-foreground">{{ pathology.description }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Información del Sistema
                        </CardTitle>
                        <CardDescription>
                            Fechas de creación y última actualización
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Fecha de Creación</p>
                                <p class="text-sm text-muted-foreground">{{ formatDate(pathology.created_at) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Última Actualización</p>
                                <p class="text-sm text-muted-foreground">{{ formatDate(pathology.updated_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sección de Atenciones -->
            <Card v-if="pathology.attentions && pathology.attentions.length > 0">
                <CardHeader>
                    <CardTitle class="text-lg">Atenciones con esta Patología</CardTitle>
                    <CardDescription>
                        Todas las atenciones médicas donde se ha diagnosticado esta patología
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <Link
                            v-for="attention in pathology.attentions"
                            :key="attention.id"
                            :href="ShiftsController.show(attention.shift_id).url"
                            class="block border rounded-lg p-4 hover:bg-muted/50 transition-colors cursor-pointer relative"
                        >
                            <div class="absolute top-3 right-3">
                                <ExternalLink class="h-4 w-4 text-muted-foreground opacity-60 hover:opacity-100 transition-opacity" />
                            </div>

                            <!-- Header de la atención -->
                            <div class="flex items-center justify-between mb-4 pr-8">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <Stethoscope class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm font-medium text-muted-foreground">Atención #{{ attention.id }}</span>
                                    </div>
                                    <div class="h-4 w-px bg-border"></div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ formatDateTime(attention.attended_at) }}
                                    </div>
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    Guardia #{{ attention.shift_id }}
                                </div>
                            </div>

                            <!-- Información principal -->
                            <div class="grid gap-4 md:grid-cols-3">
                                <!-- Paciente -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <Heart class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm font-medium">Paciente</span>
                                    </div>
                                    <div class="text-sm">
                                        <Link
                                            v-if="attention.patient"
                                            :href="PatientsController.show(attention.patient.id).url"
                                            class="font-medium text-primary hover:text-primary/80 underline decoration-dotted underline-offset-2 flex items-center gap-1"
                                        >
                                            {{ attention.patient.name }}
                                            <ExternalLink class="h-3 w-3" />
                                        </Link>
                                        <span v-else class="text-muted-foreground">Sin especificar</span>
                                    </div>
                                </div>

                                <!-- Doctor -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <User class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm font-medium">Doctor</span>
                                    </div>
                                    <div class="text-sm">
                                        <Link
                                            v-if="attention.shift?.doctor"
                                            :href="DoctorsController.show(attention.shift.doctor.id).url"
                                            class="font-medium text-primary hover:text-primary/80 underline decoration-dotted underline-offset-2 flex items-center gap-1"
                                        >
                                            {{ attention.shift.doctor.name }}
                                            <ExternalLink class="h-3 w-3" />
                                        </Link>
                                        <span v-else class="text-muted-foreground">Sin especificar</span>
                                    </div>
                                </div>

                                <!-- Fecha de la guardia -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <Clock class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm font-medium">Guardia</span>
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        <div v-if="attention.shift">
                                            {{ formatDateTime(attention.shift.starts_at) }}
                                        </div>
                                        <div v-else>Sin información</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notas de la atención -->
                            <div v-if="attention.notes" class="mt-4 pt-4 border-t">
                                <div class="flex items-start gap-2">
                                    <FileText class="h-4 w-4 text-muted-foreground mt-0.5" />
                                    <div>
                                        <span class="text-sm font-medium">Notas de la atención:</span>
                                        <p class="text-sm text-muted-foreground mt-1">
                                            {{ attention.notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <!-- Mensaje cuando no hay atenciones -->
            <Card v-else>
                <CardContent class="flex flex-col items-center justify-center py-8">
                    <Stethoscope class="h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-medium mb-2">Sin atenciones registradas</h3>
                    <p class="text-sm text-muted-foreground text-center">
                        Esta patología aún no ha sido diagnosticada en ninguna atención médica.
                    </p>
                </CardContent>
            </Card>
        </div>

        <DeletePathologyDrawer
            v-model:open="showDeleteDrawer"
            :pathology="pathology"
            @confirm="confirmDelete"
        />

        <SuccessDialog
            v-model:open="showSuccessDialog"
            title="¡Éxito!"
            :description="successMessage"
            @close="closeSuccessDialog"
        />
    </AppLayout>
</template>
