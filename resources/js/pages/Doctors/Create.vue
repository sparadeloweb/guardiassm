<script setup lang="ts">
import DoctorsController from '@/actions/App/Http/Controllers/Doctors/DoctorsController';
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

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Doctores',
        href: DoctorsController.index().url,
    },
    {
        title: 'Crear Doctor',
        href: DoctorsController.create().url,
    },
];
</script>

<template>
    <Head title="Crear Doctor" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <HeadingSmall
                title="Crear Nuevo Doctor"
                description="Completa los datos del doctor"
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
                        v-bind="DoctorsController.store.form()"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="name">Nombre Completo</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
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
                                    placeholder="Calle Principal 123, Madrid"
                                    required
                                    autocomplete="street-address"
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
                                Crear Doctor
                            </Button>

                            <Button variant="outline" type="button" as-child>
                                <Link :href="DoctorsController.index().url">
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

