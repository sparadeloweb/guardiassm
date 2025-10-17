<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
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
import { Plus, LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import axios from 'axios';

interface Props {
    open: boolean;
}

interface Emits {
    (e: 'update:open', value: boolean): void;
    (e: 'created', shiftType: any): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const form = ref({
    name: '',
    description: '',
    value: '',
    patient_value: '',
});

const errors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

const handleCancel = () => {
    form.value = {
        name: '',
        description: '',
        value: '',
        patient_value: '',
    };
    errors.value = {};
    emit('update:open', false);
};

const handleSubmit = async () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        const response = await axios.post('/api/shift-types', form.value, {
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
            console.error('Error creating shift type:', error);
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
                <SheetTitle>Crear Nuevo Tipo de Guardia</SheetTitle>
                <SheetDescription>
                    Completa los datos del nuevo tipo de guardia
                </SheetDescription>
            </SheetHeader>

            <form @submit.prevent="handleSubmit" class="space-y-4 py-4 px-4">
                <div class="grid gap-4">
                    <!-- Nombre -->
                    <div class="grid gap-2">
                        <Label for="shift_type_name">Nombre *</Label>
                        <Input
                            id="shift_type_name"
                            v-model="form.name"
                            placeholder="Guardia Nocturna"
                            required
                        />
                        <InputError :message="errors.name?.[0]" />
                    </div>

                    <!-- Descripción -->
                    <div class="grid gap-2">
                        <Label for="shift_type_description">Descripción</Label>
                        <Textarea
                            id="shift_type_description"
                            v-model="form.description"
                            placeholder="Descripción del tipo de guardia..."
                            rows="3"
                        />
                        <InputError :message="errors.description?.[0]" />
                    </div>

                    <!-- Valor por Hora -->
                    <div class="grid gap-2">
                        <Label for="shift_type_value">Valor por Hora *</Label>
                        <Input
                            id="shift_type_value"
                            v-model="form.value"
                            type="number"
                            step="0.01"
                            placeholder="1500.00"
                            required
                        />
                        <InputError :message="errors.value?.[0]" />
                    </div>

                    <!-- Valor por Paciente -->
                    <div class="grid gap-2">
                        <Label for="shift_type_patient_value">Valor por Paciente *</Label>
                        <Input
                            id="shift_type_patient_value"
                            v-model="form.patient_value"
                            type="number"
                            step="0.01"
                            placeholder="500.00"
                            required
                        />
                        <InputError :message="errors.patient_value?.[0]" />
                    </div>
                </div>

                <SheetFooter class="flex-col gap-2 pt-4 px-0">
                    <Button
                        type="submit"
                        :disabled="isSubmitting"
                        class="w-full"
                    >
                        <LoaderCircle v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                        <Plus v-else class="mr-2 h-4 w-4" />
                        {{ isSubmitting ? 'Creando...' : 'Crear Tipo de Guardia' }}
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
