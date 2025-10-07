<script setup lang="ts">
import PathologiesController from '@/actions/App/Http/Controllers/Patients/PathologiesController';
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
} from 'lucide-vue-next';

interface Pathology {
    id: number;
    name: string;
    description: string;
    created_at: string;
    updated_at: string;
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
                        <h3 class="text-base font-medium">{{ pathology.name }}</h3>
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

                <div class="flex items-center gap-2">
                    <Button
                        v-if="pathology.id !== 1"
                        variant="outline"
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
