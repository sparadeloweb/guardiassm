<script setup lang="ts">
import ShiftTypesController from '@/actions/App/Http/Controllers/Shifts/ShiftTypesController';
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

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Tipos de Guardias',
        href: ShiftTypesController.index().url,
    },
    {
        title: 'Crear Tipo',
        href: ShiftTypesController.create().url,
    },
];
</script>

<template>
    <Head title="Crear Tipo de Guardia" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                title="Crear Nuevo Tipo de Guardia"
                description="Completa los datos del tipo de guardia"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Información del Tipo</CardTitle>
                    <CardDescription>
                        Todos los campos son obligatorios
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <Form
                        v-bind="ShiftTypesController.store.form()"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="name">Nombre</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
                                    placeholder="Ej: Guardia 12hs Diurna"
                                    required
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="description">Descripción</Label>
                                <Textarea
                                    id="description"
                                    name="description"
                                    placeholder="Describe las características del tipo de guardia"
                                    required
                                    rows="3"
                                />
                                <InputError :message="errors.description" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="value">Valor por hora ($)</Label>
                                <Input
                                    id="value"
                                    type="number"
                                    name="value"
                                    placeholder="1500.00"
                                    required
                                    step="0.01"
                                    min="0"
                                />
                                <InputError :message="errors.value" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="patient_value">Valor paciente ($)</Label>
                                <Input
                                    id="patient_value"
                                    type="number"
                                    name="patient_value"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    required
                                />
                                <InputError :message="errors.patient_value" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="processing">
                                <LoaderCircle
                                    v-if="processing"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                Crear Tipo
                            </Button>

                            <Button variant="outline" type="button" as-child>
                                <Link :href="ShiftTypesController.index().url">
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

