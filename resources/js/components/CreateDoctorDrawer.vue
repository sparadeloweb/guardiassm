<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
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
    (e: 'created', doctor: any): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const form = ref({
    name: '',
    age: '',
    email: '',
    phone: '',
    address: '',
    is_resident: false,
});

const errors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

const handleCancel = () => {
    form.value = {
        name: '',
        age: '',
        email: '',
        phone: '',
        address: '',
        is_resident: false,
    };
    errors.value = {};
    emit('update:open', false);
};

const handleSubmit = async () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        const response = await axios.post('/api/doctors', form.value, {
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
            console.error('Error creating doctor:', error);
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
                <SheetTitle>Crear Nuevo Doctor</SheetTitle>
                <SheetDescription>
                    Completa los datos del nuevo doctor
                </SheetDescription>
            </SheetHeader>

            <form @submit.prevent="handleSubmit" class="space-y-4 py-4 px-4">
                <div class="grid gap-4">
                    <!-- Nombre -->
                    <div class="grid gap-2">
                        <Label for="doctor_name">Nombre Completo *</Label>
                        <Input
                            id="doctor_name"
                            v-model="form.name"
                            placeholder="Dr. Juan Pérez"
                            required
                        />
                        <InputError :message="errors.name?.[0]" />
                    </div>

                    <!-- Edad -->
                    <div class="grid gap-2">
                        <Label for="doctor_age">Edad *</Label>
                        <Input
                            id="doctor_age"
                            v-model="form.age"
                            type="number"
                            placeholder="35"
                            required
                        />
                        <InputError :message="errors.age?.[0]" />
                    </div>

                    <!-- Email -->
                    <div class="grid gap-2">
                        <Label for="doctor_email">Email *</Label>
                        <Input
                            id="doctor_email"
                            v-model="form.email"
                            type="email"
                            placeholder="doctor@example.com"
                            required
                        />
                        <InputError :message="errors.email?.[0]" />
                    </div>

                    <!-- Teléfono -->
                    <div class="grid gap-2">
                        <Label for="doctor_phone">Teléfono *</Label>
                        <Input
                            id="doctor_phone"
                            v-model="form.phone"
                            type="tel"
                            placeholder="+54 11 1234 5678"
                            required
                        />
                        <InputError :message="errors.phone?.[0]" />
                    </div>

                    <!-- Dirección -->
                    <div class="grid gap-2">
                        <Label for="doctor_address">Dirección *</Label>
                        <Input
                            id="doctor_address"
                            v-model="form.address"
                            placeholder="Calle Principal 123"
                            required
                        />
                        <InputError :message="errors.address?.[0]" />
                    </div>

                    <!-- Es Residente -->
                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="doctor_is_resident"
                            :checked="form.is_resident"
                            @update:checked="(val) => form.is_resident = val"
                        />
                        <Label for="doctor_is_resident" class="cursor-pointer">
                            Es Residente
                        </Label>
                    </div>
                    <InputError :message="errors.is_resident?.[0]" />
                </div>

                <SheetFooter class="flex-col gap-2 pt-4 px-0">
                    <Button
                        type="submit"
                        :disabled="isSubmitting"
                        class="w-full"
                    >
                        <LoaderCircle v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                        <UserPlus v-else class="mr-2 h-4 w-4" />
                        {{ isSubmitting ? 'Creando...' : 'Crear Doctor' }}
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
