<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import ShiftsController from '@/actions/App/Http/Controllers/Shifts/ShiftsController';
import { router } from '@inertiajs/vue3';
import { Trash2, AlertTriangle } from 'lucide-vue-next';
import { ref } from 'vue';

interface Shift {
    id: number;
    doctor_id: number;
    shift_type_id: number;
    hourly_rate_snapshot: number;
    per_patient_rate_snapshot: number;
    starts_at: string;
    ends_at: string;
    total_hours: number;
    patients_count: number;
    total_amount: number;
    notes: string | null;
    paid_at: string | null;
    created_at: string;
    doctor: {
        id: number;
        name: string;
    };
    shift_type: {
        id: number;
        name: string;
    };
}

interface Props {
    shift: Shift;
    open: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    close: [];
}>();

const isDeleting = ref(false);

const handleDelete = async () => {
    isDeleting.value = true;

    try {
        router.delete(ShiftsController.destroy(props.shift.id).url, {
            onSuccess: () => {
                emit('close');
            },
            onFinish: () => {
                isDeleting.value = false;
            }
        });
    } catch (error) {
        isDeleting.value = false;
    }
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
    }).format(amount);
};

const formatDateTime = (dateTime: string) => {
    return new Intl.DateTimeFormat('es-AR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateTime));
};
</script>

<template>
    <Sheet :open="open" @update:open="(value) => !value && emit('close')">
        <SheetContent class="w-full max-w-md">
            <SheetHeader class="text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-destructive/10">
                    <AlertTriangle class="h-6 w-6 text-destructive" />
                </div>
                <SheetTitle>¿Eliminar guardia?</SheetTitle>
                <SheetDescription class="text-center">
                    Esta acción no se puede deshacer. Se eliminará permanentemente la guardia y toda su información asociada.
                </SheetDescription>
            </SheetHeader>

            <!-- Información de la guardia -->
            <div class="mt-6">
                <div class="rounded-lg border bg-card p-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-muted-foreground">Doctor:</span>
                            <span class="text-sm font-medium">{{ shift.doctor.name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-muted-foreground">Tipo de Guardia:</span>
                            <span class="text-sm font-medium">{{ shift.shift_type.name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-muted-foreground">Fecha:</span>
                            <span class="text-sm font-medium">{{ formatDateTime(shift.starts_at) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-muted-foreground">Duración:</span>
                            <span class="text-sm font-medium">{{ shift.total_hours }}h</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-muted-foreground">Pacientes:</span>
                            <span class="text-sm font-medium">{{ shift.patients_count }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-muted-foreground">Monto Total:</span>
                            <span class="text-sm font-medium">{{ formatCurrency(shift.total_amount) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <SheetFooter class="mt-6 flex-col gap-2">
                <Button
                    variant="destructive"
                    @click="handleDelete"
                    :disabled="isDeleting"
                    class="w-full"
                >
                    <Trash2 class="mr-2 h-4 w-4" />
                    {{ isDeleting ? 'Eliminando...' : 'Eliminar Guardia' }}
                </Button>
                <SheetClose as-child>
                    <Button variant="outline" class="w-full">
                        Cancelar
                    </Button>
                </SheetClose>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>
