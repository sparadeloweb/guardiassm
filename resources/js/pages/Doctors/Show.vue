<script setup lang="ts">
import DoctorsController from '@/actions/App/Http/Controllers/Doctors/DoctorsController';
import DeleteDoctorDrawer from '@/components/DeleteDoctorDrawer.vue';
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
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Pencil,
    Trash2,
    Mail,
    Phone,
    MapPin,
    Calendar,
    User,
} from 'lucide-vue-next';

interface Doctor {
    id: number;
    name: string;
    age: number;
    email: string;
    phone: string;
    address: string;
    is_resident: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    doctor: Doctor;
    success?: string;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Doctores',
        href: DoctorsController.index().url,
    },
    {
        title: props.doctor.name,
        href: DoctorsController.show(props.doctor.id).url,
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
    router.delete(DoctorsController.destroy(props.doctor.id).url, {
        onSuccess: () => {
            isDeleteDrawerOpen.value = false;
            isSuccessDialogOpen.value = true;
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
</script>

<template>
    <Head :title="doctor.name" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                :title="doctor.name"
                description="Información completa del doctor"
            />

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Información Principal -->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-2xl">{{
                                    doctor.name
                                }}</CardTitle>
                            </div>
                        </div>
                    </CardHeader>

                    <CardContent class="space-y-6">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <User
                                    class="h-5 w-5 text-muted-foreground mt-0.5"
                                />
                                <div>
                                    <p class="text-sm font-medium">Edad</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ doctor.age }} años
                                    </p>
                                </div>
                            </div>

                            <Separator />

                            <div class="flex items-start gap-3">
                                <Mail
                                    class="h-5 w-5 text-muted-foreground mt-0.5"
                                />
                                <div>
                                    <p class="text-sm font-medium">Email</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ doctor.email }}
                                    </p>
                                </div>
                            </div>

                            <Separator />

                            <div class="flex items-start gap-3">
                                <Phone
                                    class="h-5 w-5 text-muted-foreground mt-0.5"
                                />
                                <div>
                                    <p class="text-sm font-medium">Teléfono</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ doctor.phone }}
                                    </p>
                                </div>
                            </div>

                            <Separator />

                            <div class="flex items-start gap-3">
                                <MapPin
                                    class="h-5 w-5 text-muted-foreground mt-0.5"
                                />
                                <div>
                                    <p class="text-sm font-medium">Dirección</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ doctor.address }}
                                    </p>
                                </div>
                            </div>

                            <Separator />

                            <div class="flex items-start gap-3">
                                <Badge :variant="doctor.is_resident ? 'default' : 'secondary'" class="w-fit">
                                    {{ doctor.is_resident ? 'Residente' : 'No Residente' }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter class="flex gap-2">
                        <Button variant="default" size="sm" as-child>
                            <Link
                                :href="DoctorsController.edit(doctor.id).url"
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
                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg">
                                Información del Registro
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-start gap-3">
                                <Calendar
                                    class="h-5 w-5 text-muted-foreground mt-0.5"
                                />
                                <div>
                                    <p class="text-sm font-medium">
                                        Fecha de Creación
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ formatDate(doctor.created_at) }}
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
                                        Última Actualización
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ formatDate(doctor.updated_at) }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Drawer de confirmación de eliminación -->
            <DeleteDoctorDrawer
                v-model:open="isDeleteDrawerOpen"
                :doctor="doctor"
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

