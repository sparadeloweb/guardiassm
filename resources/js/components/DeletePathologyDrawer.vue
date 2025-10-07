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
import { AlertTriangle, Stethoscope, FileText } from 'lucide-vue-next';

interface Pathology {
    id: number;
    name: string;
    description: string;
}

interface Props {
    open: boolean;
    pathology: Pathology | null;
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
                    Esta acción no se puede deshacer. La patología será eliminada permanentemente.
                </SheetDescription>
            </SheetHeader>

            <div v-if="pathology" class="py-4">
                <Card class="mx-4 p-6 bg-muted/50">
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <Stethoscope class="h-4 w-4 text-muted-foreground mt-0.5 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-muted-foreground">Patología</p>
                                <p class="text-base font-semibold truncate">{{ pathology.name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <FileText class="h-4 w-4 text-muted-foreground mt-0.5 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-muted-foreground">Descripción</p>
                                <p class="text-sm text-muted-foreground break-words">{{ pathology.description }}</p>
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
                    Eliminar Patología
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
