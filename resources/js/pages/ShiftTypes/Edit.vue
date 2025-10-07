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

interface ShiftType {
    id: number;
    name: string;
    description: string;
    value: number;
    patient_value: number | null;
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
    {
        title: 'Editar',
        href: ShiftTypesController.edit(props.shiftType.id).url,
    },
];
</script>

<template>
    <Head :title="`Editar ${shiftType.name}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                :title="`Editar ${shiftType.name}`"
                description="Actualiza los datos del tipo de guardia"
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
                        v-bind="ShiftTypesController.update.form(shiftType.id)"
                        class="space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="name">Nombre</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
                                    :default-value="shiftType.name"
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
                                    :default-value="shiftType.description"
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
                                    :default-value="shiftType.value"
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
                                    :default-value="shiftType.patient_value || 0"
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
                                Actualizar Tipo
                            </Button>

                            <Button variant="outline" type="button" as-child>
                                <Link
                                    :href="
                                        ShiftTypesController.show(shiftType.id).url
                                    "
                                >
                                    Cancelar
                                </Link>
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="recentlySuccessful"
                                    class="text-sm text-neutral-600"
                                >
                                    Guardado.
                                </p>
                            </Transition>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

