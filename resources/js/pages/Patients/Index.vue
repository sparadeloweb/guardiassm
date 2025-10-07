<script setup lang="ts">
import PatientsController from '@/actions/App/Http/Controllers/Patients/PatientsController';
import DeletePatientDrawer from '@/components/DeletePatientDrawer.vue';
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
    Edit,
    Eye,
    Heart,
    MoreHorizontal,
    Search,
    Trash2,
    UserPlus,
    ArrowUpDown,
    ArrowUp,
    ArrowDown,
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
    patients: Patient[];
}

const props = defineProps<Props>();
const page = usePage();

const searchQuery = ref('');
const selectedPatient = ref<Patient | null>(null);
const showDeleteDrawer = ref(false);
const showSuccessDialog = ref(false);
const successMessage = ref('');

// Estado de ordenamiento
const sortColumn = ref<keyof Patient | null>(null);
const sortDirection = ref<'asc' | 'desc'>('asc');

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Pacientes',
        href: '#',
    },
    {
        title: 'Pacientes',
        href: PatientsController.index().url,
    },
];

const filteredPatients = computed(() => {
    if (!searchQuery.value) return props.patients;

    const query = searchQuery.value.toLowerCase();
    return props.patients.filter(
        (patient) =>
            patient.name.toLowerCase().includes(query) ||
            (patient.DNI && patient.DNI.toLowerCase().includes(query)) ||
            (patient.email && patient.email.toLowerCase().includes(query))
    );
});

// Función para ordenar
const toggleSort = (column: keyof Patient) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }
};

// Obtener icono de ordenamiento
const getSortIcon = (column: keyof Patient) => {
    if (sortColumn.value !== column) return ArrowUpDown;
    return sortDirection.value === 'asc' ? ArrowUp : ArrowDown;
};

const sortedPatients = computed(() => {
    let result = [...filteredPatients.value];

    // Ordenar
    if (sortColumn.value) {
        result.sort((a, b) => {
            const aVal = a[sortColumn.value!];
            const bVal = b[sortColumn.value!];

            let comparison = 0;
            if (typeof aVal === 'string' && typeof bVal === 'string') {
                comparison = aVal.localeCompare(bVal);
            } else if (typeof aVal === 'number' && typeof bVal === 'number') {
                comparison = aVal - bVal;
            }

            return sortDirection.value === 'asc' ? comparison : -comparison;
        });
    } else {
        // Ordenamiento por defecto por nombre
        result.sort((a, b) => a.name.localeCompare(b.name));
    }

    return result;
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-AR');
};

const formatGender = (gender: string | null) => {
    if (!gender) return 'No especificado';
    return gender === 'male' ? 'Masculino' : 'Femenino';
};

const openDeleteDrawer = (patient: Patient) => {
    selectedPatient.value = patient;
    showDeleteDrawer.value = true;
};

const closeDeleteDrawer = () => {
    showDeleteDrawer.value = false;
    selectedPatient.value = null;
};

const confirmDelete = () => {
    if (selectedPatient.value) {
        router.delete(PatientsController.destroy(selectedPatient.value.id).url, {
            onSuccess: () => {
                closeDeleteDrawer();
                successMessage.value = 'Paciente eliminado correctamente';
                showSuccessDialog.value = true;
            },
        });
    }
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
    <Head title="Pacientes" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Pacientes"
                    description="Gestiona todos los pacientes del sistema"
                />

                <Button as-child>
                    <Link :href="PatientsController.create().url">
                        <UserPlus class="mr-2 h-4 w-4" />
                        Crear Paciente
                    </Link>
                </Button>
            </div>

            <div v-if="patients.length > 0" class="space-y-4">
                <!-- Barra de búsqueda -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Buscar Pacientes</CardTitle>
                        <CardDescription>
                            Filtra por nombre, DNI o email
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
                                placeholder="Buscar pacientes..."
                                class="pl-10"
                            />
                        </div>
                    </CardContent>
                </Card>

                <!-- Tabla de pacientes -->
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
                                            @click="toggleSort('name')"
                                        >
                                            Nombre
                                            <component
                                                :is="getSortIcon('name')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('DNI')"
                                        >
                                            DNI
                                            <component
                                                :is="getSortIcon('DNI')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('email')"
                                        >
                                            Email
                                            <component
                                                :is="getSortIcon('email')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('phone')"
                                        >
                                            Teléfono
                                            <component
                                                :is="getSortIcon('phone')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('gender')"
                                        >
                                            Género
                                            <component
                                                :is="getSortIcon('gender')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 data-[state=open]:bg-accent"
                                            @click="toggleSort('birth_date')"
                                        >
                                            Fecha de Nacimiento
                                            <component
                                                :is="getSortIcon('birth_date')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                    <TableHead class="text-center">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="sortedPatients.length === 0">
                                    <TableCell colspan="7" class="h-24 text-center">
                                        <EmptyState
                                            :icon="Search"
                                            title="No se encontraron resultados"
                                            description="Intenta con otros términos de búsqueda"
                                        />
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="patient in sortedPatients" :key="patient.id">
                                    <TableCell class="text-center font-medium text-foreground">
                                        {{ patient.name }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ patient.DNI || '-' }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ patient.email || '-' }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ patient.phone || '-' }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ formatGender(patient.gender) }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ patient.birth_date ? formatDate(patient.birth_date) : '-' }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <Button variant="ghost" size="icon" as-child>
                                                <Link :href="PatientsController.show(patient.id).url">
                                                    <Eye class="h-4 w-4" />
                                                    <span class="sr-only">Ver paciente</span>
                                                </Link>
                                            </Button>

                                            <Button variant="ghost" size="icon" as-child>
                                                <Link :href="PatientsController.edit(patient.id).url">
                                                    <Edit class="h-4 w-4" />
                                                    <span class="sr-only">Editar paciente</span>
                                                </Link>
                                            </Button>

                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="openDeleteDrawer(patient)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                                <span class="sr-only">Eliminar paciente</span>
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Resumen -->
                <div class="flex items-center justify-between text-sm text-muted-foreground">
                    <p>
                        Mostrando
                        <span class="font-medium text-foreground">
                            {{ sortedPatients.length }}
                        </span>
                        de
                        <span class="font-medium text-foreground">
                            {{ patients.length }}
                        </span>
                        pacientes
                    </p>
                </div>
            </div>

            <EmptyState
                v-else
                :icon="Heart"
                title="¡Ups! No se encontraron registros"
                description="No hay pacientes registrados en el sistema. Crea el primer paciente para comenzar."
            >
                <template #action>
                    <Button as-child size="lg">
                        <Link :href="PatientsController.create().url">
                            <UserPlus class="mr-2 h-4 w-4" />
                            Crear Primer Paciente
                        </Link>
                    </Button>
                </template>
            </EmptyState>
        </div>

        <DeletePatientDrawer
            v-model:open="showDeleteDrawer"
            :patient="selectedPatient"
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
