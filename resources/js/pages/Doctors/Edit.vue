<script setup lang="ts">
import DoctorsController from '@/actions/App/Http/Controllers/Doctors/DoctorsController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
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
import { ref } from 'vue';

interface Doctor {
    id: number;
    name: string;
    age: number;
    email: string;
    phone: string;
    address: string;
    is_resident: boolean;
}

interface Props {
    doctor: Doctor;
}

const props = defineProps<Props>();

const isResident = ref(props.doctor.is_resident);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Doctores',
        href: DoctorsController.index().url,
    },
    {
        title: props.doctor.name,
        href: DoctorsController.show(props.doctor.id).url,
    },
    {
        title: 'Editar',
        href: DoctorsController.edit(props.doctor.id).url,
    },
];
</script>

<template>
    <Head :title="`Editar ${doctor.name}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                :title="`Editar ${doctor.name}`"
                description="Actualiza los datos del doctor"
            />

            <Card>
                <CardHeader>
                    <CardTitle>Información del Doctor</CardTitle>
                    <CardDescription>
                        Todos los campos son obligatorios
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <Form
                        v-bind="DoctorsController.update.form(doctor.id)"
                        class="space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="name">Nombre Completo</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
                                    :default-value="doctor.name"
                                    placeholder="Dr. Juan Pérez"
                                    required
                                    autocomplete="name"
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="age">Edad</Label>
                                <Input
                                    id="age"
                                    type="number"
                                    name="age"
                                    :default-value="doctor.age"
                                    placeholder="35"
                                    required
                                    min="18"
                                    max="100"
                                />
                                <InputError :message="errors.age" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    :default-value="doctor.email"
                                    placeholder="doctor@example.com"
                                    required
                                    autocomplete="email"
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="phone">Teléfono</Label>
                                <Input
                                    id="phone"
                                    type="tel"
                                    name="phone"
                                    :default-value="doctor.phone"
                                    placeholder="+34 600 000 000"
                                    required
                                    autocomplete="tel"
                                />
                                <InputError :message="errors.phone" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="address">Dirección</Label>
                                <Input
                                    id="address"
                                    type="text"
                                    name="address"
                                    :default-value="doctor.address"
                                    placeholder="Calle Principal 123, Madrid"
                                    required
                                    autocomplete="street-address"
                                />
                                <InputError :message="errors.address" />
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="is_resident"
                                    v-model:checked="isResident"
                                />
                                <input type="hidden" name="is_resident" :value="isResident ? '1' : '0'" />
                                <Label for="is_resident" class="text-sm font-normal cursor-pointer">
                                    Marcar como residente
                                </Label>
                                <InputError :message="errors.is_resident" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="processing">
                                <LoaderCircle
                                    v-if="processing"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                Actualizar Doctor
                            </Button>

                            <Button variant="outline" type="button" as-child>
                                <Link
                                    :href="
                                        DoctorsController.show(doctor.id).url
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

