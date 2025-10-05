<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Props {
    open: boolean;
    title?: string;
    description?: string;
}

interface Emits {
    (e: 'update:open', value: boolean): void;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Operación exitosa',
    description: 'La operación se completó correctamente.',
});

const emit = defineEmits<Emits>();

const handleClose = () => {
    emit('update:open', false);
};
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="sm:max-w-[450px] gap-6">
            <DialogHeader class="space-y-3">
                <DialogTitle class="text-2xl">{{ title }}</DialogTitle>
                <DialogDescription class="text-base leading-relaxed">
                    {{ description }}
                </DialogDescription>
            </DialogHeader>

            <DialogFooter>
                <Button @click="handleClose" class="w-full" size="lg">
                    Aceptar
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

