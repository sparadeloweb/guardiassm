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
import { Trash2, User } from 'lucide-vue-next';

interface Doctor {
    id: number;
    name: string;
    age: number;
    email: string;
    phone: string;
    address: string;
}

interface Props {
    open: boolean;
    doctor: Doctor | null;
}

interface Emits {
    (e: 'update:open', value: boolean): void;
    (e: 'confirm'): void;
}

defineProps<Props>();
const emit = defineEmits<Emits>();

const handleCancel = () => {
    emit('update:open', false);
};

const handleConfirm = () => {
    emit('confirm');
};
</script>

<template>
    <Sheet :open="open" @update:open="(val) => emit('update:open', val)">
        <SheetContent class="sm:max-w-[440px]">
            <SheetHeader>
                <SheetTitle>Confirmar Eliminación</SheetTitle>
                <SheetDescription class="mb-2">
                    Esta acción no se puede deshacer. ¿Estás seguro de que
                    deseas eliminar este doctor?
                </SheetDescription>
            </SheetHeader>

            <div
                v-if="doctor"
                class="my-6 mx-4 space-y-4 rounded-lg border border-destructive/50 bg-destructive/10 p-6"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-destructive/20"
                    >
                        <User class="h-6 w-6 text-destructive" />
                    </div>
                    <div>
                        <p class="font-semibold text-foreground">
                            {{ doctor.name }}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            {{ doctor.email }}
                        </p>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div
                        class="flex items-center justify-between border-t pt-2"
                    >
                        <span class="text-muted-foreground">Edad:</span>
                        <span class="font-medium">{{ doctor.age }} años</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Teléfono:</span>
                        <span class="font-medium">{{ doctor.phone }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-muted-foreground">Dirección:</span>
                        <span class="font-medium">{{ doctor.address }}</span>
                    </div>
                </div>
            </div>

            <SheetFooter class="flex-col gap-3">
                <Button
                    variant="destructive"
                    @click="handleConfirm"
                    class="w-full"
                >
                    <Trash2 class="mr-2 h-4 w-4" />
                    Eliminar Doctor
                </Button>
                <SheetClose as-child>
                    <Button
                        variant="outline"
                        @click="handleCancel"
                        class="w-full"
                    >
                        Cancelar
                    </Button>
                </SheetClose>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>

