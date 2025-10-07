<script setup lang="ts">
import ShiftTypesController from '@/actions/App/Http/Controllers/Shifts/ShiftTypesController';
import DeleteShiftTypeDrawer from '@/components/DeleteShiftTypeDrawer.vue';
import EmptyState from '@/components/EmptyState.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import SuccessDialog from '@/components/SuccessDialog.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
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
    Plus,
    Eye,
    Pencil,
    Trash2,
    Search,
    ArrowUpDown,
    ArrowUp,
    ArrowDown,
    Briefcase,
} from 'lucide-vue-next';

interface ShiftType {
    id: number;
    name: string;
    description: string;
    value: number;
    patient_value: number | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    shiftTypes: ShiftType[];
}

const props = defineProps<Props>();
const page = usePage();

const searchQuery = ref('');
const selectedShiftType = ref<ShiftType | null>(null);
const showDeleteDrawer = ref(false);
const showSuccessDialog = ref(false);
const successMessage = ref('');

// Estado de ordenamiento
const sortColumn = ref<keyof ShiftType | null>(null);
const sortDirection = ref<'asc' | 'desc'>('asc');

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Guardias',
        href: '#',
    },
    {
        title: 'Tipos de Guardias',
        href: ShiftTypesController.index().url,
    },
];

const filteredShiftTypes = computed(() => {
    if (!searchQuery.value) return props.shiftTypes;

    const query = searchQuery.value.toLowerCase();
    return props.shiftTypes.filter(
        (shiftType) =>
            shiftType.name.toLowerCase().includes(query) ||
            shiftType.description.toLowerCase().includes(query)
    );
});

// Función para ordenar
const toggleSort = (column: keyof ShiftType) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }
};

// Obtener icono de ordenamiento
const getSortIcon = (column: keyof ShiftType) => {
    if (sortColumn.value !== column) return ArrowUpDown;
    return sortDirection.value === 'asc' ? ArrowUp : ArrowDown;
};

const sortedShiftTypes = computed(() => {
    let result = [...filteredShiftTypes.value];

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

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
        minimumFractionDigits: 2,
    }).format(value);
};

const openDeleteDrawer = (shiftType: ShiftType) => {
    selectedShiftType.value = shiftType;
    showDeleteDrawer.value = true;
};

const closeDeleteDrawer = () => {
    showDeleteDrawer.value = false;
    selectedShiftType.value = null;
};

const confirmDelete = () => {
    if (selectedShiftType.value) {
        router.delete(ShiftTypesController.destroy(selectedShiftType.value.id).url, {
            onSuccess: () => {
                closeDeleteDrawer();
                successMessage.value = 'Tipo de guardia eliminado correctamente';
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
    <Head title="Tipos de Guardias" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Tipos de Guardias"
                    description="Gestiona todos los tipos de guardias del sistema"
                />

                <Button as-child>
                    <Link :href="ShiftTypesController.create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Crear Tipo de Guardia
                    </Link>
                </Button>
            </div>

            <div v-if="shiftTypes.length > 0" class="space-y-4">
                <!-- Barra de búsqueda -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Buscar Tipos de Guardias</CardTitle>
                        <CardDescription>
                            Filtra por nombre o descripción
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
                                placeholder="Buscar tipos de guardias..."
                                class="pl-10"
                            />
                        </div>
                    </CardContent>
                </Card>

                <!-- Tabla de tipos de guardias -->
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
                                            @click="toggleSort('description')"
                                        >
                                            Descripción
                                            <component
                                                :is="getSortIcon('description')"
                                                class="ml-2 h-4 w-4"
                                            />
                                        </Button>
                                    </TableHead>
                                            <TableHead class="text-center">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    class="h-8 data-[state=open]:bg-accent"
                                                    @click="toggleSort('value')"
                                                >
                                                    Valor por hora
                                                    <component
                                                        :is="getSortIcon('value')"
                                                        class="ml-2 h-4 w-4"
                                                    />
                                                </Button>
                                            </TableHead>
                                            <TableHead class="text-center">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    class="h-8 data-[state=open]:bg-accent"
                                                    @click="toggleSort('patient_value')"
                                                >
                                                    Valor paciente
                                                    <component
                                                        :is="getSortIcon('patient_value')"
                                                        class="ml-2 h-4 w-4"
                                                    />
                                                </Button>
                                            </TableHead>
                                            <TableHead class="text-center">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                        <TableRow v-if="sortedShiftTypes.length === 0">
                                            <TableCell colspan="5" class="h-24 text-center">
                                        <EmptyState
                                            :icon="Search"
                                            title="No se encontraron resultados"
                                            description="Intenta con otros términos de búsqueda"
                                        />
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="shiftType in sortedShiftTypes" :key="shiftType.id">
                                    <TableCell class="text-center font-medium text-foreground">
                                        {{ shiftType.name }}
                                    </TableCell>
                                    <TableCell class="text-center text-foreground">
                                        {{ shiftType.description }}
                                    </TableCell>
                                            <TableCell class="text-center">
                                                <Badge variant="default">
                                                    {{ formatCurrency(shiftType.value) }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell class="text-center">
                                                <Badge v-if="shiftType.patient_value" variant="default">
                                                    {{ formatCurrency(shiftType.patient_value) }}
                                                </Badge>
                                                <span v-else class="text-muted-foreground">-</span>
                                            </TableCell>
                                            <TableCell class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <Button variant="ghost" size="icon" as-child>
                                                <Link :href="ShiftTypesController.show(shiftType.id).url">
                                                    <Eye class="h-4 w-4" />
                                                    <span class="sr-only">Ver tipo de guardia</span>
                                                </Link>
                                            </Button>

                                            <Button variant="ghost" size="icon" as-child>
                                                <Link :href="ShiftTypesController.edit(shiftType.id).url">
                                                    <Pencil class="h-4 w-4" />
                                                    <span class="sr-only">Editar tipo de guardia</span>
                                                </Link>
                                            </Button>

                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="openDeleteDrawer(shiftType)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                                <span class="sr-only">Eliminar tipo de guardia</span>
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
                            {{ sortedShiftTypes.length }}
                        </span>
                        de
                        <span class="font-medium text-foreground">
                            {{ shiftTypes.length }}
                        </span>
                        tipos de guardias
                    </p>
                </div>
            </div>

            <EmptyState
                v-else
                :icon="Briefcase"
                title="¡Ups! No se encontraron registros"
                description="No hay tipos de guardias registrados en el sistema. Crea el primer tipo para comenzar."
            >
                <template #action>
                    <Button as-child size="lg">
                        <Link :href="ShiftTypesController.create().url">
                            <Plus class="mr-2 h-4 w-4" />
                            Crear Primer Tipo de Guardia
                        </Link>
                    </Button>
                </template>
            </EmptyState>
        </div>

        <DeleteShiftTypeDrawer
            v-model:open="showDeleteDrawer"
            :shift-type="selectedShiftType"
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
