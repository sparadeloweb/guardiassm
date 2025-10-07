<script setup lang="ts">
import ShiftTypesController from '@/actions/App/Http/Controllers/Shifts/ShiftTypesController';
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
</script>

<template>
    <Head :title="shiftType.name" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                :title="shiftType.name"
                description="Información completa del tipo de guardia"
            />

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

                    <CardFooter class="flex gap-2">
                        <Button variant="default" size="sm" as-child>
                            <Link
                                :href="ShiftTypesController.edit(shiftType.id).url"
                            >
                                <Pencil class="mr-2 h-4 w-4" />
                                Editar
                            </Link>
                        </Button>
                        <Button
                            variant="destructive"
                            size="sm"
                            @click="openDeleteDrawer"
                        >
                            <Trash2 class="mr-2 h-4 w-4" />
                            Eliminar
                        </Button>
                    </CardFooter>
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

