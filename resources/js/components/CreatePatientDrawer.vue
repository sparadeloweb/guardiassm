<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { UserPlus, LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import axios from 'axios';

interface Props {
    open: boolean;
}

interface Emits {
    (e: 'update:open', value: boolean): void;
    (e: 'created', patient: any): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const form = ref({
    name: '',
    DNI: '',
    phone: '',
    address: '',
    health_insurance: '',
    email: '',
    gender: '',
    birth_date: '',
});

const errors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

const handleCancel = () => {
    form.value = {
        name: '',
        DNI: '',
        phone: '',
        address: '',
        health_insurance: '',
        email: '',
        gender: '',
        birth_date: '',
    };
    errors.value = {};
    emit('update:open', false);
};

const handleSubmit = async () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        const response = await axios.post('/api/patients', form.value, {
            headers: {
                'Accept': 'application/json',
            }
        });

        if (response.data.success) {
            emit('created', response.data.data);
            handleCancel();
        }
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {};
        } else {
            console.error('Error creating patient:', error);
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Sheet :open="open" @update:open="(val) => emit('update:open', val)">
        <SheetContent class="sm:max-w-[540px] overflow-y-auto">
            <SheetHeader>
                <SheetTitle>Crear Nuevo Paciente</SheetTitle>
                <SheetDescription>
                    Completa los datos del nuevo paciente
                </SheetDescription>
            </SheetHeader>

            <form @submit.prevent="handleSubmit" class="space-y-4 py-4 px-4">
                <div class="grid gap-4">
                    <!-- Nombre -->
                    <div class="grid gap-2">
                        <Label for="patient_name">Nombre Completo *</Label>
                        <Input
                            id="patient_name"
                            v-model="form.name"
                            placeholder="Juan Pérez"
                            required
                        />
                        <InputError :message="errors.name?.[0]" />
                    </div>

                    <!-- DNI -->
                    <div class="grid gap-2">
                        <Label for="patient_dni">DNI</Label>
                        <Input
                            id="patient_dni"
                            v-model="form.DNI"
                            placeholder="12345678"
                        />
                        <InputError :message="errors.DNI?.[0]" />
                    </div>

                    <!-- Género -->
                    <div class="grid gap-2">
                        <Label for="patient_gender">Género *</Label>
                        <select
                            id="patient_gender"
                            v-model="form.gender"
                            required
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Seleccionar género</option>
                            <option value="male">Masculino</option>
                            <option value="female">Femenino</option>
                        </select>
                        <InputError :message="errors.gender?.[0]" />
                    </div>

                    <!-- Email -->
                    <div class="grid gap-2">
                        <Label for="patient_email">Email</Label>
                        <Input
                            id="patient_email"
                            v-model="form.email"
                            type="email"
                            placeholder="paciente@example.com"
                        />
                        <InputError :message="errors.email?.[0]" />
                    </div>

                    <!-- Teléfono -->
                    <div class="grid gap-2">
                        <Label for="patient_phone">Teléfono</Label>
                        <Input
                            id="patient_phone"
                            v-model="form.phone"
                            type="tel"
                            placeholder="+54 11 1234 5678"
                        />
                        <InputError :message="errors.phone?.[0]" />
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="grid gap-2">
                        <Label for="patient_birth_date">Fecha de Nacimiento</Label>
                        <Input
                            id="patient_birth_date"
                            v-model="form.birth_date"
                            type="date"
                        />
                        <InputError :message="errors.birth_date?.[0]" />
                    </div>

                    <!-- Dirección -->
                    <div class="grid gap-2">
                        <Label for="patient_address">Dirección</Label>
                        <Input
                            id="patient_address"
                            v-model="form.address"
                            placeholder="Calle Principal 123"
                        />
                        <InputError :message="errors.address?.[0]" />
                    </div>

                    <!-- Obra Social -->
                    <div class="grid gap-2">
                        <Label for="patient_health_insurance">Obra Social</Label>
                        <Input
                            id="patient_health_insurance"
                            v-model="form.health_insurance"
                            placeholder="OSDE, Swiss Medical, etc."
                        />
                        <InputError :message="errors.health_insurance?.[0]" />
                    </div>
                </div>

                <SheetFooter class="flex-col gap-2 pt-4 px-0">
                    <Button
                        type="submit"
                        :disabled="isSubmitting"
                        class="w-full"
                    >
                        <LoaderCircle v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                        <UserPlus v-else class="mr-2 h-4 w-4" />
                        {{ isSubmitting ? 'Creando...' : 'Crear Paciente' }}
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        @click="handleCancel"
                        :disabled="isSubmitting"
                        class="w-full"
                    >
                        Cancelar
                    </Button>
                </SheetFooter>
            </form>
        </SheetContent>
    </Sheet>
</template>
