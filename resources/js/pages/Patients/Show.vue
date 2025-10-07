<script setup lang="ts">
import PatientsController from '@/actions/App/Http/Controllers/Patients/PatientsController';
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
    created_at: string;
    updated_at: string;
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

// Check for success message from controller redirect
const flashMessage = computed(() => page.props.flash?.success);
if (flashMessage.value) {
    successMessage.value = flashMessage.value;
    showSuccessDialog.value = true;
}
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

                <div class="flex items-center gap-2">
                    <Button variant="outline" as-child>
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
