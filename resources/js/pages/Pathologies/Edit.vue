<script setup lang="ts">
import PathologiesController from '@/actions/App/Http/Controllers/Patients/PathologiesController';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface Pathology {
    id: number;
    name: string;
    description: string;
}

interface Props {
    pathology: Pathology;
}

const props = defineProps<Props>();

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
    {
        title: 'Editar',
        href: PathologiesController.edit(props.pathology.id).url,
    },
];
</script>

<template>
    <Head title="Editar Patología" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                title="Editar Patología"
                description="Modifica los datos de la patología"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Información de la Patología</CardTitle>
                    <CardDescription>
                        Todos los campos son obligatorios
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <Form
                        v-bind="PathologiesController.update(pathology.id).form()"
                        method="put"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">Nombre *</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
                                    :default-value="pathology.name"
                                    placeholder="Diabetes"
                                    required
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2 md:col-span-2">
                                <Label for="description">Descripción *</Label>
                                <Textarea
                                    id="description"
                                    name="description"
                                    :default-value="pathology.description"
                                    placeholder="Descripción detallada de la patología..."
                                    rows="4"
                                    required
                                />
                                <InputError :message="errors.description" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="processing">
                                <LoaderCircle
                                    v-if="processing"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                Actualizar Patología
                            </Button>

                            <Button variant="outline" type="button" as-child>
                                <Link :href="PathologiesController.show(pathology.id).url">
                                    Cancelar
                                </Link>
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
