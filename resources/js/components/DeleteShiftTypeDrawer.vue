<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { Badge } from '@/components/ui/badge';
import { Card } from '@/components/ui/card';
import { AlertTriangle, Briefcase, FileText, DollarSign } from 'lucide-vue-next';

interface ShiftType {
    id: number;
    name: string;
    description: string;
    value: number | string;
}

interface Props {
    open: boolean;
    shiftType: ShiftType | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:open': [value: boolean];
    'confirm': [];
}>();

const closeDrawer = () => {
    emit('update:open', false);
};

const confirmDelete = () => {
    emit('confirm');
};
</script>

<template>
    <Sheet :open="open" @update:open="(value) => emit('update:open', value)">
        <SheetContent side="right" class="sm:max-w-[440px]">
            <SheetHeader>
                <SheetTitle class="flex items-center gap-2 text-destructive">
                    <AlertTriangle class="h-5 w-5" />
                    Confirmar Eliminación
                </SheetTitle>
                <SheetDescription class="mb-2">
                    Esta acción no se puede deshacer. El tipo de guardia será eliminado permanentemente.
                </SheetDescription>
            </SheetHeader>

            <div v-if="shiftType" class="py-4">
                <Card class="mx-4 p-6 bg-muted/50">
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <Briefcase class="h-4 w-4 text-muted-foreground mt-0.5 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-muted-foreground">Tipo de Guardia</p>
                                <p class="text-base font-semibold truncate">{{ shiftType.name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <FileText class="h-4 w-4 text-muted-foreground mt-0.5 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-muted-foreground">Descripción</p>
                                <p class="text-sm text-muted-foreground break-words">{{ shiftType.description }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <DollarSign class="h-4 w-4 text-muted-foreground mt-0.5 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-muted-foreground">Valor por hora</p>
                                <Badge variant="secondary" class="font-mono">
                                    ${{ Number(shiftType.value).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>

            <SheetFooter class="flex flex-col gap-3 mt-6">
                <Button
                    variant="destructive"
                    class="w-full"
                    @click="confirmDelete"
                >
                    Eliminar Tipo de Guardia
                </Button>
                <Button
                    variant="outline"
                    class="w-full"
                    @click="closeDrawer"
                >
                    Cancelar
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>

