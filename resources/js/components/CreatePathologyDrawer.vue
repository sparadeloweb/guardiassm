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
    (e: 'created', pathology: any): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const form = ref({
    name: '',
    description: '',
});

const errors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

const handleCancel = () => {
    form.value = {
        name: '',
        description: '',
    };
    errors.value = {};
    emit('update:open', false);
};

const handleSubmit = async () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        const response = await axios.post('/api/pathologies', form.value, {
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
            console.error('Error creating pathology:', error);
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
                <SheetTitle>Crear Nueva Patología</SheetTitle>
                <SheetDescription>
                    Completa los datos de la nueva patología
                </SheetDescription>
            </SheetHeader>

            <form @submit.prevent="handleSubmit" class="space-y-4 py-4 px-4">
                <div class="grid gap-4">
                    <!-- Nombre -->
                    <div class="grid gap-2">
                        <Label for="pathology_name">Nombre *</Label>
                        <Input
                            id="pathology_name"
                            v-model="form.name"
                            placeholder="Hipertensión"
                            required
                        />
                        <InputError :message="errors.name?.[0]" />
                    </div>

                    <!-- Descripción -->
                    <div class="grid gap-2">
                        <Label for="pathology_description">Descripción</Label>
                        <Textarea
                            id="pathology_description"
                            v-model="form.description"
                            placeholder="Descripción de la patología..."
                            rows="4"
                        />
                        <InputError :message="errors.description?.[0]" />
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
                        {{ isSubmitting ? 'Creando...' : 'Crear Patología' }}
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
