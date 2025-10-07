<script setup lang="ts">
import DoctorsController from '@/actions/App/Http/Controllers/Doctors/DoctorsController';
import DeleteDoctorDrawer from '@/components/DeleteDoctorDrawer.vue';
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
    User,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Doctor {
    id: number;
    name: string;
    age: number;
    email: string;
    phone: string;
    address: string;
    is_resident: boolean;
    created_at: string;
}

interface Props {
    doctors: Doctor[];
    success?: string;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Doctores',
        href: DoctorsController.index().url,
    },
];

// Estado de búsqueda y ordenamiento
const searchQuery = ref('');
const sortColumn = ref<keyof Doctor | null>(null);
const sortDirection = ref<'asc' | 'desc'>('asc');

// Estado para el drawer de eliminación
const isDeleteDrawerOpen = ref(false);
const doctorToDelete = ref<Doctor | null>(null);

// Estado para el dialog de éxito
const isSuccessDialogOpen = ref(false);
const successMessage = ref('');

// Detectar mensaje de éxito del controlador
if (props.success) {
    successMessage.value = props.success;
    isSuccessDialogOpen.value = true;
}

// Función para ordenar
const toggleSort = (column: keyof Doctor) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }
};

// Obtener icono de ordenamiento
const getSortIcon = (column: keyof Doctor) => {
    if (sortColumn.value !== column) return ArrowUpDown;
    return sortDirection.value === 'asc' ? ArrowUp : ArrowDown;
};

// Doctores filtrados y ordenados
const filteredDoctors = computed(() => {
    let result = [...props.doctors];

    // Filtrar por búsqueda
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (doctor) =>
                doctor.name.toLowerCase().includes(query) ||
                doctor.email.toLowerCase().includes(query) ||
                doctor.phone.toLowerCase().includes(query) ||
                doctor.address.toLowerCase().includes(query),
        );
    }

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
    }

    return result;
});

const openDeleteDrawer = (doctor: Doctor) => {
    doctorToDelete.value = doctor;
    isDeleteDrawerOpen.value = true;
};

const confirmDelete = () => {
    if (doctorToDelete.value) {
        router.delete(DoctorsController.destroy(doctorToDelete.value.id).url, {
            onSuccess: () => {
                isDeleteDrawerOpen.value = false;
                doctorToDelete.value = null;
                isSuccessDialogOpen.value = true;
            },
        });
    }
};
</script>

<template>
    <Head title="Doctores" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Doctores"
                    description="Administra la información de los doctores del sistema"
                />
                <Button as-child>
                    <Link :href="DoctorsController.create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Crear Doctor
                    </Link>
                </Button>
            </div>

            <EmptyState
                v-if="doctors.length === 0"
                :icon="User"
                title="¡Uups! No se encontraron registros"
                description="No hay doctores registrados en el sistema. Comienza creando tu primer doctor."
            >
                <template #action>
                    <Button as-child size="lg">
                        <Link :href="DoctorsController.create().url">
                            <Plus class="mr-2 h-4 w-4" />
                            Crear Primer Doctor
                        </Link>
                    </Button>
                </template>
            </EmptyState>

            <div v-else class="space-y-4">
                <!-- Barra de búsqueda -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Buscar Doctores</CardTitle>
                        <CardDescription>
                            Filtra por nombre, email, teléfono o dirección
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
                                placeholder="Buscar doctores..."
                                class="pl-10"
                            />
                        </div>
                    </CardContent>
                </Card>

                <!-- Tabla de doctores -->
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
                                            @click="toggleSort('age')"
                                        >
                                            Edad
                                            <component
                                                :is="getSortIcon('age')"
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
                                      <TableHead class="text-center">Teléfono</TableHead>
                                      <TableHead class="text-center">Dirección</TableHead>
                                      <TableHead class="text-center">Rol</TableHead>
                                      <TableHead class="text-center">
                                          Acciones
                                      </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-if="filteredDoctors.length === 0"
                                >
                                    <TableCell
                                        colspan="7"
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
                                    v-for="doctor in filteredDoctors"
                                    :key="doctor.id"
                                >
                                    <TableCell class="font-medium text-center text-foreground">
                                        {{ doctor.name }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ doctor.age }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ doctor.email }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ doctor.phone }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground max-w-xs truncate">
                                        {{ doctor.address }}
                                    </TableCell>
                                     <TableCell class="text-center">
                                         <Badge :variant="doctor.is_resident ? 'default' : 'secondary'">
                                             {{ doctor.is_resident ? 'Residente' : 'Doctor' }}
                                         </Badge>
                                     </TableCell>
                                    <TableCell class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                as-child
                                            >
                                                <Link
                                                    :href="
                                                        DoctorsController.show(
                                                            doctor.id,
                                                        ).url
                                                    "
                                                >
                                                    <Eye class="h-4 w-4" />
                                                    <span class="sr-only">
                                                        Ver doctor
                                                    </span>
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                as-child
                                            >
                                                <Link
                                                    :href="
                                                        DoctorsController.edit(
                                                            doctor.id,
                                                        ).url
                                                    "
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                    <span class="sr-only">
                                                        Editar doctor
                                                    </span>
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="openDeleteDrawer(doctor)"
                                            >
                                                <Trash2
                                                    class="h-4 w-4 text-destructive"
                                                />
                                                <span class="sr-only">
                                                    Eliminar doctor
                                                </span>
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
                            {{ filteredDoctors.length }}
                        </span>
                        de
                        <span class="font-medium text-foreground">
                            {{ doctors.length }}
                        </span>
                        doctores
                    </p>
                </div>
            </div>

            <!-- Drawer de confirmación de eliminación -->
            <DeleteDoctorDrawer
                v-model:open="isDeleteDrawerOpen"
                :doctor="doctorToDelete"
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
