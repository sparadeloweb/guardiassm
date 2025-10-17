<script setup lang="ts">
import ShiftsController from '@/actions/App/Http/Controllers/Shifts/ShiftsController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, Link, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Plus, Trash2, Clock, User, Stethoscope, AlertTriangle, PlusCircle } from 'lucide-vue-next';
import { ref, computed, watch, onMounted } from 'vue';
import CreateDoctorDrawer from '@/components/CreateDoctorDrawer.vue';
import CreateShiftTypeDrawer from '@/components/CreateShiftTypeDrawer.vue';
import CreatePatientDrawer from '@/components/CreatePatientDrawer.vue';
import CreatePathologyDrawer from '@/components/CreatePathologyDrawer.vue';

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

interface Patient {
    id: number;
    name: string;
    DNI: string;
}

interface Pathology {
    id: number;
    name: string;
}

interface Attention {
    id?: number;
    patient_id: string;
    attended_at: string;
    notes: string;
    pathologies: number[];
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
    doctors: Doctor[];
    shiftTypes: ShiftType[];
    patients: Patient[];
    pathologies: Pathology[];
}

const props = defineProps<Props>();

// Estados para los drawers
const showDoctorDrawer = ref(false);
const showShiftTypeDrawer = ref(false);
const showPatientDrawer = ref(false);
const showPathologyDrawer = ref(false);

// Listas reactivas
const doctorsList = ref<Doctor[]>([...props.doctors]);
const shiftTypesList = ref<ShiftType[]>([...props.shiftTypes]);
const patientsList = ref<Patient[]>([...props.patients]);
const pathologiesList = ref<Pathology[]>([...props.pathologies]);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Guardias',
        href: ShiftsController.index().url,
    },
    {
        title: 'Editar Guardia',
        href: ShiftsController.edit(props.shift.id).url,
    },
];

// Form state using useForm
const form = useForm({
    doctor_id: props.shift.doctor_id.toString(),
    shift_type_id: props.shift.shift_type_id.toString(),
    starts_at: props.shift.starts_at,
    ends_at: props.shift.ends_at,
    notes: props.shift.notes || '',
    attentions: props.shift.attentions.map(attention => ({
        id: attention.id,
        patient_id: attention.patient_id.toString(),
        attended_at: attention.attended_at,
        notes: attention.notes,
        pathologies: attention.pathologies.map(pathology =>
            typeof pathology === 'object' ? pathology.id : pathology
        )
    }))
});

const generalError = ref<string | null>(null);
const pathologySearch = ref<{ [key: number]: string }>({});

// Funciones para manejar la creación de nuevos elementos
const handleDoctorCreated = (doctor: Doctor) => {
    doctorsList.value.push(doctor);
    form.doctor_id = doctor.id.toString();
};

const handleShiftTypeCreated = (shiftType: ShiftType) => {
    shiftTypesList.value.push(shiftType);
    form.shift_type_id = shiftType.id.toString();
};

const handlePatientCreated = (patient: Patient) => {
    patientsList.value.push(patient);
    // Si hay una atención en proceso sin paciente, asignarle el nuevo paciente
    const emptyAttention = form.attentions.find(att => !att.patient_id);
    if (emptyAttention) {
        emptyAttention.patient_id = patient.id.toString();
    }
};

const handlePathologyCreated = (pathology: Pathology) => {
    pathologiesList.value.push(pathology);
};

// Computed values
const selectedDoctor = computed(() =>
    doctorsList.value.find(d => d.id === parseInt(form.doctor_id)) || null
);

const selectedShiftType = computed(() =>
    shiftTypesList.value.find(st => st.id === parseInt(form.shift_type_id)) || null
);

const totalHours = computed(() => {
    if (!form.starts_at || !form.ends_at) return 0;
    const start = new Date(form.starts_at);
    const end = new Date(form.ends_at);
    return Math.ceil((end.getTime() - start.getTime()) / (1000 * 60 * 60));
});

// Función para ajustar horarios de atención cuando cambia el rango de la guardia
const adjustAttentionTimesToShiftRange = (newStartTime: string, newEndTime: string) => {
    const shiftStart = new Date(newStartTime);
    const shiftEnd = new Date(newEndTime);

    form.attentions.forEach((attention, index) => {
        if (attention.attended_at) {
            const attentionTime = new Date(attention.attended_at);

            // Solo ajustar si la atención está fuera del nuevo rango
            if (attentionTime < shiftStart || attentionTime > shiftEnd) {
                // Si está antes del inicio, moverla al inicio
                if (attentionTime < shiftStart) {
                    attention.attended_at = formatDateTimeToInput(shiftStart.toISOString());
                }
                // Si está después del final, moverla al final
                else if (attentionTime > shiftEnd) {
                    attention.attended_at = formatDateTimeToInput(shiftEnd.toISOString());
                }
            }
        }
    });
};

// Función para verificar si una patología está seleccionada
const isPathologySelected = (attentionIndex: number, pathologyId: number) => {
    return form.attentions[attentionIndex].pathologies.includes(pathologyId);
};

// Función para filtrar patologías por búsqueda
const getFilteredPathologies = (attentionIndex: number) => {
    const searchTerm = pathologySearch.value[attentionIndex]?.toLowerCase() || '';
    if (!searchTerm) return pathologiesList.value;
    return pathologiesList.value.filter(pathology =>
        pathology.name.toLowerCase().includes(searchTerm)
    );
};

// Helper functions
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
    }).format(amount);
};

const formatDateTimeFromInput = (dateTime: string) => {
    // Convert from input format (YYYY-MM-DDTHH:MM) to MySQL format (YYYY-MM-DD HH:MM:SS)
    if (!dateTime) return '';

    // If already in MySQL format, return as is
    if (dateTime.includes(' ') && !dateTime.includes('T')) {
        return dateTime;
    }

    // Convert directly without timezone conversion to avoid the 3-hour shift
    // Extract date and time components manually
    const [datePart, timePart] = dateTime.split('T');
    if (!datePart || !timePart) return dateTime;

    // Ensure we have seconds
    const timeWithSeconds = timePart.split(':').length === 2 ? timePart + ':00' : timePart;

    // Return in MySQL format without timezone conversion
    return `${datePart} ${timeWithSeconds}`;
};

const formatDateTimeToInput = (dateTime: string) => {
    // Convert from MySQL format (YYYY-MM-DD HH:MM:SS) to input format (YYYY-MM-DDTHH:MM)
    if (!dateTime) return '';

    // If already in input format, return as is
    if (dateTime.includes('T')) {
        return dateTime.slice(0, 16);
    }

    // Convert from MySQL format to input format without timezone conversion
    return dateTime.replace(' ', 'T').slice(0, 16);
};

const getPatientName = (patientId: string) => {
    const patient = patientsList.value.find(p => p.id === parseInt(patientId));
    return patient ? `${patient.name} - DNI: ${patient.DNI}` : 'Paciente no encontrado';
};

const getPathologyName = (pathologyId: number) => {
    const pathology = pathologiesList.value.find(p => p.id === pathologyId);
    return pathology ? pathology.name : 'Patología no encontrada';
};

const validateAttentionTime = (attendedAt: string) => {
    if (!attendedAt || !form.starts_at || !form.ends_at) return true;

    const attentionTime = new Date(attendedAt);
    const startTime = new Date(form.starts_at);
    const endTime = new Date(form.ends_at);

    return attentionTime >= startTime && attentionTime <= endTime;
};

// Attention management
const addAttention = () => {
    form.attentions.push({
        patient_id: '',
        attended_at: '',
        notes: '',
        pathologies: []
    });
};

const removeAttention = (index: number) => {
    form.attentions.splice(index, 1);
};

const togglePathology = (attentionIndex: number, pathologyId: number) => {
    const attention = form.attentions[attentionIndex];
    const index = attention.pathologies.indexOf(pathologyId);

    if (index > -1) {
        attention.pathologies.splice(index, 1);
    } else {
        attention.pathologies.push(pathologyId);
    }
};

// Form submission
const submit = () => {
    // Validar horarios de atención
    const invalidAttentions = form.attentions.filter(attention =>
        attention.attended_at && !validateAttentionTime(attention.attended_at)
    );

    if (invalidAttentions.length > 0) {
        generalError.value = 'Todos los horarios de atención deben estar dentro del rango de la guardia';
        return;
    }

    // Preparar datos para enviar
    const formData = {
        doctor_id: parseInt(form.doctor_id),
        shift_type_id: parseInt(form.shift_type_id),
        starts_at: formatDateTimeFromInput(form.starts_at),
        ends_at: formatDateTimeFromInput(form.ends_at),
        notes: form.notes,
        attentions: form.attentions.map(attention => ({
            id: attention.id,
            patient_id: attention.patient_id,
            attended_at: formatDateTimeFromInput(attention.attended_at),
            notes: attention.notes,
            pathologies: attention.pathologies.filter(p => typeof p === 'number')
        }))
    };

    // Enviar formulario usando useForm
    form.clearErrors();
    generalError.value = null;

    // Actualizar los datos del formulario
    form.doctor_id = formData.doctor_id.toString();
    form.shift_type_id = formData.shift_type_id.toString();
    form.starts_at = formData.starts_at;
    form.ends_at = formData.ends_at;
    form.notes = formData.notes;
    form.attentions = formData.attentions.map(attention => ({
        id: attention.id,
        patient_id: attention.patient_id.toString(),
        attended_at: attention.attended_at,
        notes: attention.notes,
        pathologies: attention.pathologies
    }));

    form.put(ShiftsController.update(props.shift.id).url, {
        onSuccess: () => {
            // El controlador ya redirige al show, no necesitamos redirigir manualmente
        },
        onError: (errors) => {
            // Manejar errores generales del servidor
            if (errors.error) {
                generalError.value = errors.error;
            }
        }
    });
};

// Watch for changes in shift times and adjust attention times accordingly
watch(() => form.starts_at, (newStartTime) => {
    if (newStartTime && form.ends_at) {
        adjustAttentionTimesToShiftRange(newStartTime, form.ends_at);
    }
});

watch(() => form.ends_at, (newEndTime) => {
    if (newEndTime && form.starts_at) {
        adjustAttentionTimesToShiftRange(form.starts_at, newEndTime);
    }
});

// Convert datetime fields to input format only if they're not already in the correct format
onMounted(() => {
    // Only convert if the datetime doesn't already have the 'T' separator (input format)
    if (form.starts_at && !form.starts_at.includes('T')) {
        form.starts_at = formatDateTimeToInput(form.starts_at);
    }
    if (form.ends_at && !form.ends_at.includes('T')) {
        form.ends_at = formatDateTimeToInput(form.ends_at);
    }
    form.attentions.forEach(attention => {
        if (attention.attended_at && !attention.attended_at.includes('T')) {
            attention.attended_at = formatDateTimeToInput(attention.attended_at);
        }
    });
});
</script>

<template>
    <Head title="Editar Guardia" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                title="Editar Guardia"
                description="Modifica los datos de la guardia y sus atenciones"
            />

            <!-- General Error -->
            <div v-if="generalError" class="rounded-lg border border-destructive/50 bg-destructive/10 p-4">
                <div class="flex items-center gap-2 text-destructive">
                    <AlertTriangle class="h-4 w-4" />
                    <span class="text-sm font-medium">{{ generalError }}</span>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Información Básica -->
                <Card>
                    <CardHeader>
                        <CardTitle>Información de la Guardia</CardTitle>
                        <CardDescription>
                            Configura los datos básicos del turno
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Doctor -->
                            <div class="grid gap-2">
                                <div class="flex items-center justify-between">
                                    <Label for="doctor_id">Doctor *</Label>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="h-6 w-6"
                                        @click="showDoctorDrawer = true"
                                    >
                                        <PlusCircle class="h-4 w-4" />
                                    </Button>
                                </div>
                                <select
                                    id="doctor_id"
                                    v-model="form.doctor_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-destructive': form.errors.doctor_id }"
                                >
                                    <option value="">Seleccionar doctor...</option>
                                    <option
                                        v-for="doctor in doctorsList"
                                        :key="doctor.id"
                                        :value="doctor.id"
                                    >
                                        {{ doctor.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.doctor_id" />
                            </div>

                            <!-- Tipo de Guardia -->
                            <div class="grid gap-2">
                                <div class="flex items-center justify-between">
                                    <Label for="shift_type_id">Tipo de Guardia *</Label>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="h-6 w-6"
                                        @click="showShiftTypeDrawer = true"
                                    >
                                        <PlusCircle class="h-4 w-4" />
                                    </Button>
                                </div>
                                <select
                                    id="shift_type_id"
                                    v-model="form.shift_type_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-destructive': form.errors.shift_type_id }"
                                >
                                    <option value="">Seleccionar tipo...</option>
                                    <option
                                        v-for="shiftType in shiftTypesList"
                                        :key="shiftType.id"
                                        :value="shiftType.id"
                                    >
                                        {{ shiftType.name }} - {{ formatCurrency(shiftType.value) }}/h
                                    </option>
                                </select>
                                <InputError :message="form.errors.shift_type_id" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Fecha y Hora de Inicio -->
                            <div class="grid gap-2">
                                <Label for="starts_at">Fecha y Hora de Inicio *</Label>
                                <Input
                                    id="starts_at"
                                    type="datetime-local"
                                    v-model="form.starts_at"
                                    :class="{ 'border-destructive': form.errors.starts_at }"
                                />
                                <InputError :message="form.errors.starts_at" />
                            </div>

                            <!-- Fecha y Hora de Fin -->
                            <div class="grid gap-2">
                                <Label for="ends_at">Fecha y Hora de Fin *</Label>
                                <Input
                                    id="ends_at"
                                    type="datetime-local"
                                    v-model="form.ends_at"
                                    :class="{ 'border-destructive': form.errors.ends_at }"
                                />
                                <InputError :message="form.errors.ends_at" />
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="grid gap-2">
                            <Label for="notes">Notas</Label>
                            <Textarea
                                id="notes"
                                v-model="form.notes"
                                placeholder="Notas adicionales sobre la guardia..."
                                :class="{ 'border-destructive': form.errors.notes }"
                            />
                            <InputError :message="form.errors.notes" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Atenciones -->
                <Card>
                    <CardHeader>
                        <div class="flex flex-col space-y-4 md:flex-row md:items-center md:justify-between md:space-y-0">
                            <div>
                                <CardTitle>Atenciones</CardTitle>
                                <CardDescription>
                                    Registra las atenciones realizadas durante la guardia
                                </CardDescription>
                            </div>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="addAttention"
                                class="flex items-center gap-2"
                            >
                                <Plus class="h-4 w-4" />
                                Agregar Atención
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="form.attentions.length === 0" class="text-center py-8 text-muted-foreground">
                            <User class="h-12 w-12 mx-auto mb-4 opacity-50" />
                            <p>No hay atenciones registradas</p>
                            <p class="text-sm">Agrega pacientes atendidos durante esta guardia</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="(attention, index) in form.attentions"
                                :key="index"
                                class="rounded-lg border p-4 space-y-4"
                            >
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium">Atención {{ index + 1 }}</h4>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        @click="removeAttention(index)"
                                        class="text-destructive hover:text-destructive"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <!-- Paciente -->
                                    <div class="grid gap-2">
                                        <div class="flex items-center justify-between">
                                            <Label>Paciente *</Label>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                class="h-6 w-6"
                                                @click="showPatientDrawer = true"
                                            >
                                                <PlusCircle class="h-4 w-4" />
                                            </Button>
                                        </div>
                                        <select
                                            v-model="attention.patient_id"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            :class="{ 'border-destructive': form.errors[`attentions.${index}.patient_id`] }"
                                        >
                                            <option value="">Seleccionar paciente...</option>
                                            <option
                                                v-for="patient in patientsList"
                                                :key="patient.id"
                                                :value="patient.id"
                                            >
                                                {{ patient.name }} - DNI: {{ patient.DNI }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors[`attentions.${index}.patient_id`]" />
                                    </div>

                                    <!-- Horario de Atención -->
                                    <div class="grid gap-2">
                                        <Label>Horario de Atención *</Label>
                                        <Input
                                            type="datetime-local"
                                            v-model="attention.attended_at"
                                            :class="{
                                                'border-destructive': form.errors[`attentions.${index}.attended_at`] ||
                                                (attention.attended_at && !validateAttentionTime(attention.attended_at))
                                            }"
                                        />
                                        <InputError :message="form.errors[`attentions.${index}.attended_at`]" />
                                        <p v-if="attention.attended_at && !validateAttentionTime(attention.attended_at)" class="text-sm text-destructive">
                                            El horario debe estar dentro del rango de la guardia
                                        </p>
                                    </div>
                                </div>

                                <!-- Notas -->
                                <div class="grid gap-2">
                                    <Label>Notas</Label>
                                    <Textarea
                                        v-model="attention.notes"
                                        placeholder="Notas sobre la atención..."
                                        :class="{ 'border-destructive': form.errors[`attentions.${index}.notes`] }"
                                    />
                                    <InputError :message="form.errors[`attentions.${index}.notes`]" />
                                </div>

                                <!-- Patologías -->
                                <div class="mt-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <Label class="text-sm font-medium">Patologías <span class="text-muted-foreground">(opcional)</span></Label>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            class="h-6 w-6"
                                            @click="showPathologyDrawer = true"
                                        >
                                            <PlusCircle class="h-4 w-4" />
                                        </Button>
                                    </div>

                                    <!-- Búsqueda de patologías -->
                                    <div class="mb-3">
                                        <Input
                                            v-model="pathologySearch[index]"
                                            placeholder="Buscar patología..."
                                            class="h-8 text-sm"
                                        />
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="pathology in getFilteredPathologies(index)"
                                            :key="pathology.id"
                                            type="button"
                                            @click="togglePathology(index, pathology.id)"
                                            :class="[
                                                'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
                                                isPathologySelected(index, pathology.id)
                                                    ? 'bg-primary text-primary-foreground hover:bg-primary/90'
                                                    : 'bg-secondary text-secondary-foreground hover:bg-secondary/80 border border-input'
                                            ]"
                                        >
                                            <Stethoscope class="mr-1 h-3 w-3" />
                                            {{ pathology.name }}
                                        </button>
                                    </div>
                                    <p class="mt-2 text-xs text-muted-foreground">
                                        Haz clic en las patologías para seleccionarlas. Si no seleccionas ninguna, se asignará automáticamente "Patología no especificada".
                                    </p>
                                    <InputError :message="form.errors[`attentions.${index}.pathologies`]" />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Resumen calculado -->
                <div v-if="selectedShiftType && totalHours > 0" class="rounded-lg border bg-muted/50 p-4">
                    <h4 class="mb-2 font-medium">Resumen Calculado</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-muted-foreground">Duración:</span>
                            <span class="ml-2 font-medium">{{ totalHours }}h</span>
                        </div>
                        <div>
                            <span class="text-muted-foreground">Tarifa por hora:</span>
                            <span class="ml-2 font-medium">${{ selectedShiftType.value }}</span>
                        </div>
                        <div>
                            <span class="text-muted-foreground">Tarifa por paciente:</span>
                            <span class="ml-2 font-medium">${{ selectedShiftType.patient_value }}</span>
                        </div>
                        <div>
                            <span class="text-muted-foreground">Total de pacientes:</span>
                            <span class="ml-2 font-medium">{{ form.attentions.length }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="ShiftsController.index().url"
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
                    >
                        Cancelar
                    </Link>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="flex items-center gap-2"
                    >
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        {{ form.processing ? 'Guardando...' : 'Actualizar Guardia' }}
                    </Button>
                </div>
            </form>
        </div>

        <!-- Drawers para crear nuevos registros -->
        <CreateDoctorDrawer
            v-model:open="showDoctorDrawer"
            @created="handleDoctorCreated"
        />
        <CreateShiftTypeDrawer
            v-model:open="showShiftTypeDrawer"
            @created="handleShiftTypeCreated"
        />
        <CreatePatientDrawer
            v-model:open="showPatientDrawer"
            @created="handlePatientCreated"
        />
        <CreatePathologyDrawer
            v-model:open="showPathologyDrawer"
            @created="handlePathologyCreated"
        />
    </AppLayout>
</template>
