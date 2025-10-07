<script setup lang="ts">
import PatientsController from '@/actions/App/Http/Controllers/Patients/PatientsController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Pacientes',
        href: PatientsController.index().url,
    },
    {
        title: props.patient.name,
        href: PatientsController.show(props.patient.id).url,
    },
    {
        title: 'Editar',
        href: PatientsController.edit(props.patient.id).url,
    },
];
</script>

<template>
    <Head title="Editar Paciente" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                :title="`Editar ${patient.name}`"
                description="Modifica los datos del paciente"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Información del Paciente</CardTitle>
                    <CardDescription>
                        Todos los campos son opcionales excepto el nombre y género
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <Form
                        :action="PatientsController.update(patient.id).url"
                        method="put"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">Nombre Completo *</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
                                    :default-value="patient.name"
                                    placeholder="Juan Pérez"
                                    required
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="DNI">DNI</Label>
                                <Input
                                    id="DNI"
                                    type="text"
                                    name="DNI"
                                    :default-value="patient.DNI || ''"
                                    placeholder="12345678"
                                />
                                <InputError :message="errors.DNI" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    :default-value="patient.email || ''"
                                    placeholder="paciente@example.com"
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="phone">Teléfono</Label>
                                <Input
                                    id="phone"
                                    type="tel"
                                    name="phone"
                                    :default-value="patient.phone || ''"
                                    placeholder="+54 11 1234 5678"
                                />
                                <InputError :message="errors.phone" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="gender">Género *</Label>
                                <select
                                    id="gender"
                                    name="gender"
                                    :value="patient.gender"
                                    required
                                    class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">Seleccionar género</option>
                                    <option value="male">Masculino</option>
                                    <option value="female">Femenino</option>
                                </select>
                                <InputError :message="errors.gender" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="birth_date">Fecha de Nacimiento</Label>
                                <Input
                                    id="birth_date"
                                    type="date"
                                    name="birth_date"
                                    :default-value="patient.birth_date || ''"
                                />
                                <InputError :message="errors.birth_date" />
                            </div>

                            <div class="grid gap-2 md:col-span-2">
                                <Label for="address">Dirección</Label>
                                <Input
                                    id="address"
                                    type="text"
                                    name="address"
                                    :default-value="patient.address || ''"
                                    placeholder="Calle Principal 123, Ciudad"
                                />
                                <InputError :message="errors.address" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="processing">
                                <LoaderCircle
                                    v-if="processing"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                Actualizar Paciente
                            </Button>

                            <Button variant="outline" type="button" as-child>
                                <Link :href="PatientsController.show(patient.id).url">
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
