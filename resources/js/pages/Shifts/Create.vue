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
import { LoaderCircle, Plus, Trash2, Clock, User, Stethoscope, AlertTriangle } from 'lucide-vue-next';
import { ref, computed, watch, onMounted } from 'vue';

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

interface Props {
    doctors: Doctor[];
    shiftTypes: ShiftType[];
    patients: Patient[];
    pathologies: Pathology[];
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Guardias',
        href: ShiftsController.index().url,
    },
    {
        title: 'Crear Guardia',
        href: ShiftsController.create().url,
    },
];

// Estado del formulario usando useForm de Inertia
const form = useForm({
    doctor_id: '',
    shift_type_id: '',
    starts_at: '',
    ends_at: '',
    notes: '',
    attentions: [] as Array<{
        patient_id: string;
        attended_at: string;
        notes: string;
        pathologies: number[];
    }>
});

// Estado de carga
const isSubmitting = ref(false);

// Estado para errores generales
const generalError = ref<string | null>(null);
const pathologySearch = ref<{ [key: number]: string }>({});

// Clave para localStorage
const FORM_STORAGE_KEY = 'shift_create_form';

// Función para guardar el estado del formulario
const saveFormState = () => {
    const formState = {
        doctor_id: form.doctor_id,
        shift_type_id: form.shift_type_id,
        starts_at: form.starts_at,
        ends_at: form.ends_at,
        notes: form.notes,
        attentions: form.attentions
    };
    localStorage.setItem(FORM_STORAGE_KEY, JSON.stringify(formState));
};

// Función para restaurar el estado del formulario
const restoreFormState = () => {
    try {
        const savedState = localStorage.getItem(FORM_STORAGE_KEY);
        if (savedState) {
            const formState = JSON.parse(savedState);

            // Solo restaurar si hay errores (indicando que hubo un problema de validación)
            if (Object.keys(form.errors).length > 0 || generalError.value) {
                form.doctor_id = formState.doctor_id || '';
                form.shift_type_id = formState.shift_type_id || '';
                form.starts_at = formState.starts_at || '';
                form.ends_at = formState.ends_at || '';
                form.notes = formState.notes || '';
                form.attentions = formState.attentions || [];
            }
        }
    } catch (error) {
        console.warn('Error restoring form state:', error);
    }
};

// Función para limpiar el estado guardado
const clearFormState = () => {
    localStorage.removeItem(FORM_STORAGE_KEY);
};

// Computed para el tipo de guardia seleccionado
const selectedShiftType = computed(() => {
    return props.shiftTypes.find(st => st.id === parseInt(form.shift_type_id));
});

// Computed para calcular duración en horas
const totalHours = computed(() => {
    if (!form.starts_at || !form.ends_at) return 0;

    const start = new Date(form.starts_at);
    const end = new Date(form.ends_at);
    const diffMs = end.getTime() - start.getTime();
    const diffHours = diffMs / (1000 * 60 * 60);

    return Math.round(diffHours * 100) / 100; // Redondear a 2 decimales
});

// Computed para calcular monto total estimado
const estimatedTotal = computed(() => {
    if (!selectedShiftType.value || totalHours.value === 0) return 0;

    const hourlyAmount = totalHours.value * selectedShiftType.value.value;
    const patientAmount = form.attentions.length * selectedShiftType.value.patient_value;

    return hourlyAmount + patientAmount;
});

// Función para agregar atención
const addAttention = () => {
    form.attentions.push({
        patient_id: '',
        attended_at: form.starts_at || '',
        notes: '',
        pathologies: []
    });
};

// Función para eliminar atención
const removeAttention = (index: number) => {
    form.attentions.splice(index, 1);
};

// Función para agregar/remover patología
const togglePathology = (attentionIndex: number, pathologyId: number) => {
    const attention = form.attentions[attentionIndex];
    const index = attention.pathologies.indexOf(pathologyId);

    if (index > -1) {
        attention.pathologies.splice(index, 1);
    } else {
        attention.pathologies.push(pathologyId);
    }
};

// Función para verificar si una patología está seleccionada
const isPathologySelected = (attentionIndex: number, pathologyId: number) => {
    return form.attentions[attentionIndex].pathologies.includes(pathologyId);
};

// Función para filtrar patologías por búsqueda
const getFilteredPathologies = (attentionIndex: number) => {
    const searchTerm = pathologySearch.value[attentionIndex]?.toLowerCase() || '';
    if (!searchTerm) return props.pathologies;
    return props.pathologies.filter(pathology =>
        pathology.name.toLowerCase().includes(searchTerm)
    );
};

// Función para obtener el nombre del paciente
const getPatientName = (patientId: string) => {
    const patient = props.patients.find(p => p.id === parseInt(patientId));
    return patient ? `${patient.name} - DNI: ${patient.DNI}` : '';
};

// Función para obtener el nombre de la patología
const getPathologyName = (pathologyId: number) => {
    const pathology = props.pathologies.find(p => p.id === pathologyId);
    return pathology ? pathology.name : '';
};

// Función para formatear fecha y hora para input datetime-local
const formatDateTimeForInput = (dateTime: string) => {
    if (!dateTime) return '';
    const date = new Date(dateTime);
    return date.toISOString().slice(0, 16);
};

// Función para formatear fecha y hora desde input datetime-local
const formatDateTimeFromInput = (dateTimeInput: string) => {
    if (!dateTimeInput) return '';
    // Convertir a formato MySQL compatible (YYYY-MM-DD HH:MM:SS)
    const date = new Date(dateTimeInput);
    return date.getFullYear() + '-' +
           String(date.getMonth() + 1).padStart(2, '0') + '-' +
           String(date.getDate()).padStart(2, '0') + ' ' +
           String(date.getHours()).padStart(2, '0') + ':' +
           String(date.getMinutes()).padStart(2, '0') + ':' +
           String(date.getSeconds()).padStart(2, '0');
};

// Función para validar horario de atención
const validateAttentionTime = (attentionTime: string) => {
    if (!attentionTime || !form.starts_at || !form.ends_at) return true;

    const attention = new Date(attentionTime);
    const start = new Date(form.starts_at);
    const end = new Date(form.ends_at);

    return attention >= start && attention <= end;
};

// Watch para actualizar attended_at cuando cambia starts_at
watch(() => form.starts_at, (newValue) => {
    if (newValue && form.attentions.length === 0) {
        form.attentions.forEach(attention => {
            if (!attention.attended_at) {
                attention.attended_at = newValue;
            }
        });
    }
    // Guardar estado automáticamente
    saveFormState();
});

// Watchers para guardar el estado del formulario automáticamente
watch(() => form.doctor_id, saveFormState);
watch(() => form.shift_type_id, saveFormState);
watch(() => form.ends_at, saveFormState);
watch(() => form.notes, saveFormState);
watch(() => form.attentions, saveFormState, { deep: true });

// Restaurar el estado del formulario al montar el componente
onMounted(() => {
    restoreFormState();
});

// Función para enviar el formulario
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
        patient_id: attention.patient_id.toString(),
        attended_at: attention.attended_at,
        notes: attention.notes,
        pathologies: attention.pathologies
    }));

    form.post(ShiftsController.store().url, {
        onSuccess: () => {
            // Limpiar el estado guardado al enviar exitosamente
            clearFormState();
            // El controlador ya redirige al index, no necesitamos redirigir manualmente
        },
        onError: (errors) => {
            // Manejar errores generales del servidor
            if (errors.error) {
                generalError.value = errors.error;
            }
            // Guardar el estado actual para restaurar después del error
            saveFormState();
        }
    });
};
</script>

<template>
    <Head title="Crear Guardia" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                title="Crear Nueva Guardia"
                description="Completa los datos de la guardia y sus atenciones"
            />

            <Form @submit.prevent="submit" class="space-y-6">
                <!-- Error general -->
                <div v-if="generalError" class="rounded-md bg-destructive/15 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <AlertTriangle class="h-5 w-5 text-destructive" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-destructive">
                                Error al crear la guardia
                            </h3>
                            <div class="mt-2 text-sm text-destructive">
                                {{ generalError }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Información básica de la guardia -->
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
                                <Label for="doctor_id">Doctor *</Label>
                                <select
                                    id="doctor_id"
                                    v-model="form.doctor_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    required
                                >
                                    <option value="">Seleccionar doctor</option>
                                    <option
                                        v-for="doctor in doctors"
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
                                <Label for="shift_type_id">Tipo de Guardia *</Label>
                                <select
                                    id="shift_type_id"
                                    v-model="form.shift_type_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    required
                                >
                                    <option value="">Seleccionar tipo</option>
                                    <option
                                        v-for="shiftType in shiftTypes"
                                        :key="shiftType.id"
                                        :value="shiftType.id"
                                    >
                                        {{ shiftType.name }} - ${{ shiftType.value }}/h
                                    </option>
                                </select>
                                <InputError :message="form.errors.shift_type_id" />
                            </div>

                            <!-- Fecha y hora de inicio -->
                            <div class="grid gap-2">
                                <Label for="starts_at">Fecha y Hora de Inicio *</Label>
                                <Input
                                    id="starts_at"
                                    type="datetime-local"
                                    v-model="form.starts_at"
                                    required
                                />
                                <InputError :message="form.errors.starts_at" />
                            </div>

                            <!-- Fecha y hora de fin -->
                            <div class="grid gap-2">
                                <Label for="ends_at">Fecha y Hora de Fin *</Label>
                                <Input
                                    id="ends_at"
                                    type="datetime-local"
                                    v-model="form.ends_at"
                                    required
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
                                placeholder="Observaciones adicionales sobre la guardia..."
                                rows="3"
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
                            <Button type="button" variant="outline" @click="addAttention" class="w-full md:w-auto">
                                <Plus class="mr-2 h-4 w-4" />
                                Agregar Atención
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="form.attentions.length === 0" class="text-center py-8">
                            <Clock class="mx-auto h-12 w-12 text-muted-foreground" />
                            <h3 class="mt-2 text-sm font-semibold">No hay atenciones</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Agrega atenciones para registrar los pacientes atendidos
                            </p>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="(attention, index) in form.attentions"
                                :key="index"
                                class="rounded-lg border p-4"
                            >
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium">Atención #{{ index + 1 }}</h4>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        @click="removeAttention(index)"
                                    >
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                    </Button>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <!-- Paciente -->
                                    <div class="grid gap-2">
                                        <Label :for="`patient_${index}`">Paciente *</Label>
                                        <select
                                            :id="`patient_${index}`"
                                            v-model="attention.patient_id"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            required
                                        >
                                            <option value="">Seleccionar paciente</option>
                                            <option
                                                v-for="patient in patients"
                                                :key="patient.id"
                                                :value="patient.id"
                                            >
                                                {{ patient.name }} - DNI: {{ patient.DNI }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors[`attentions.${index}.patient_id`]" />
                                    </div>

                                    <!-- Fecha y hora de atención -->
                                    <div class="grid gap-2">
                                        <Label :for="`attended_at_${index}`">Fecha y Hora de Atención *</Label>
                                        <Input
                                            :id="`attended_at_${index}`"
                                            type="datetime-local"
                                            v-model="attention.attended_at"
                                            :class="{
                                                'border-destructive': !validateAttentionTime(attention.attended_at) && attention.attended_at
                                            }"
                                            required
                                        />
                                        <div v-if="!validateAttentionTime(attention.attended_at) && attention.attended_at" class="text-sm text-destructive">
                                            El horario de atención debe estar dentro del rango de la guardia
                                        </div>
                                        <InputError :message="form.errors[`attentions.${index}.attended_at`]" />
                                    </div>

                                    <!-- Notas -->
                                    <div class="grid gap-2">
                                        <Label :for="`notes_${index}`">Notas</Label>
                                        <Textarea
                                            :id="`notes_${index}`"
                                            v-model="attention.notes"
                                            placeholder="Observaciones sobre la atención..."
                                            rows="2"
                                        />
                                        <InputError :message="form.errors[`attentions.${index}.notes`]" />
                                    </div>
                                </div>

                        <!-- Patologías -->
                        <div class="mt-4">
                            <Label class="mb-3 block text-sm font-medium">Patologías <span class="text-muted-foreground">(opcional)</span></Label>

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
                        <div class="col-span-2">
                            <span class="text-muted-foreground">Total estimado:</span>
                            <Badge variant="default" class="ml-2">${{ estimatedTotal.toFixed(2) }}</Badge>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center justify-between gap-4">
                    <Button type="button" variant="outline" @click="clearFormState">
                        Limpiar Formulario
                    </Button>
                    <div class="flex gap-4">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="ShiftsController.index().url">
                                Cancelar
                            </Link>
                        </Button>
                        <Button type="submit" :disabled="isSubmitting">
                            <LoaderCircle v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                            {{ isSubmitting ? 'Creando...' : 'Crear Guardia' }}
                        </Button>
                    </div>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
