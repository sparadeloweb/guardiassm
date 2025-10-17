<script setup lang="ts">
import PatientsController from '@/actions/App/Http/Controllers/Patients/PatientsController';
import ShiftsController from '@/actions/App/Http/Controllers/Shifts/ShiftsController';
import DoctorsController from '@/actions/App/Http/Controllers/Doctors/DoctorsController';
import PathologiesController from '@/actions/App/Http/Controllers/Patients/PathologiesController';
import DeletePatientDrawer from '@/components/DeletePatientDrawer.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import SuccessDialog from '@/components/SuccessDialog.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
    CreditCard,
    Edit,
    Heart,
    Mail,
    MapPin,
    Phone,
    Trash2,
    User,
    Clock,
    Stethoscope,
    ExternalLink,
    FileText,
    Briefcase,
    Shield,
} from 'lucide-vue-next';

interface Pathology {
    id: number;
    name: string;
    description: string;
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
    pathologies?: Pathology[];
}

interface Patient {
    id: number;
    name: string;
    DNI: string | null;
    phone: string | null;
    address: string | null;
    health_insurance: string | null;
    email: string | null;
    gender: string | null;
    birth_date: string | null;
    created_at: string;
    updated_at: string;
    attentions: Attention[];
}

interface Props {
    patient: Patient;
}

const props = defineProps<Props>();
const page = usePage();

const showDeleteDrawer = ref(false);
const showSuccessDialog = ref(false);
const successMessage = ref('');

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Pacientes',
        href: PatientsController.index().url,
    },
    {
        title: props.patient.name,
        href: PatientsController.show(props.patient.id).url,
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

const formatGender = (gender: string | null) => {
    if (!gender) return 'No especificado';
    return gender === 'male' ? 'Masculino' : 'Femenino';
};

const openDeleteDrawer = () => {
    showDeleteDrawer.value = true;
};

const closeDeleteDrawer = () => {
    showDeleteDrawer.value = false;
};

const confirmDelete = () => {
    router.delete(PatientsController.destroy(props.patient.id).url, {
        onSuccess: () => {
            closeDeleteDrawer();
            successMessage.value = 'Paciente eliminado correctamente';
            showSuccessDialog.value = true;
        },
    });
};

const closeSuccessDialog = () => {
    showSuccessDialog.value = false;
};
</script>

<template>
    <Head :title="patient.name" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <div class="flex items-center justify-between">
                <HeadingSmall
                    :title="patient.name"
                    description="Información detallada del paciente"
                />

                <div class="flex gap-2">
                    <Button as-child>
                        <Link :href="PatientsController.edit(patient.id).url">
                            <Edit class="mr-2 h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
                    <Button variant="destructive" @click="openDeleteDrawer">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <User class="h-5 w-5" />
                            Información Personal
                        </CardTitle>
                        <CardDescription>
                            Datos básicos del paciente
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <User class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Nombre Completo</p>
                                <p class="text-sm text-muted-foreground">{{ patient.name }}</p>
                            </div>
                        </div>

                        <div v-if="patient.DNI" class="flex items-start gap-3">
                            <CreditCard class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">DNI</p>
                                <p class="text-sm text-muted-foreground">{{ patient.DNI }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <Heart class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Género</p>
                                <p class="text-sm text-muted-foreground">{{ formatGender(patient.gender) }}</p>
                            </div>
                        </div>

                        <div v-if="patient.birth_date" class="flex items-start gap-3">
                            <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Fecha de Nacimiento</p>
                                <p class="text-sm text-muted-foreground">{{ formatDate(patient.birth_date) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Phone class="h-5 w-5" />
                            Información de Contacto
                        </CardTitle>
                        <CardDescription>
                            Datos para contactar al paciente
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="patient.email" class="flex items-start gap-3">
                            <Mail class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Email</p>
                                <p class="text-sm text-muted-foreground">{{ patient.email }}</p>
                            </div>
                        </div>

                        <div v-if="patient.phone" class="flex items-start gap-3">
                            <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Teléfono</p>
                                <p class="text-sm text-muted-foreground">{{ patient.phone }}</p>
                            </div>
                        </div>

                        <div v-if="patient.address" class="flex items-start gap-3">
                            <MapPin class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Dirección</p>
                                <p class="text-sm text-muted-foreground">{{ patient.address }}</p>
                            </div>
                        </div>

                        <div v-if="patient.health_insurance" class="flex items-start gap-3">
                            <Shield class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Obra Social</p>
                                <p class="text-sm text-muted-foreground">{{ patient.health_insurance }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Información del Sistema</CardTitle>
                    <CardDescription>
                        Fechas de creación y última actualización
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4 md:grid-cols-2">
                    <div class="flex items-start gap-3">
                        <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                        <div>
                            <p class="text-sm font-medium">Fecha de Creación</p>
                            <p class="text-sm text-muted-foreground">{{ formatDate(patient.created_at) }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                        <div>
                            <p class="text-sm font-medium">Última Actualización</p>
                            <p class="text-sm text-muted-foreground">{{ formatDate(patient.updated_at) }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Sección de Atenciones -->
            <Card v-if="patient.attentions && patient.attentions.length > 0">
                <CardHeader>
                    <CardTitle class="text-lg">Historial de Atenciones</CardTitle>
                    <CardDescription>
                        Todas las atenciones médicas que ha recibido este paciente
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <Link
                            v-for="attention in patient.attentions"
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

                                <!-- Patologías -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <Briefcase class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm font-medium">Patologías</span>
                                    </div>
                                    <div class="text-sm">
                                        <div v-if="attention.pathologies && attention.pathologies.length > 0" class="flex flex-wrap gap-1">
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
                                                    {{ pathology.name }}
                                                    <ExternalLink class="h-3 w-3" />
                                                </Badge>
                                            </Link>
                                        </div>
                                        <span v-else class="text-muted-foreground">Sin patologías registradas</span>
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
                        Este paciente aún no ha recibido atenciones médicas registradas en el sistema.
                    </p>
                </CardContent>
            </Card>
        </div>

        <DeletePatientDrawer
            v-model:open="showDeleteDrawer"
            :patient="patient"
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
