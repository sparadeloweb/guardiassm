<script setup lang="ts">
import { type Component } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    icon?: Component;
    title?: string;
    description?: string;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: '¡Uups! No se encontraron registros',
    description: 'No hay datos asociados para mostrar en este momento',
});
</script>

<template>
    <div
        :class="
            cn(
                'flex min-h-[400px] w-full flex-col items-center justify-center rounded-xl border-2 border-dashed border-muted-foreground/25 bg-muted/30 p-12 text-center transition-colors hover:border-muted-foreground/50 hover:bg-muted/50',
                props.class,
            )
        "
    >
        <div
            class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-muted-foreground/10"
        >
            <component
                :is="icon"
                v-if="icon"
                class="h-10 w-10 text-muted-foreground/70"
            />
            <svg
                v-else
                class="h-10 w-10 text-muted-foreground/70"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                />
            </svg>
        </div>

        <h3 class="mb-2 text-2xl font-semibold text-foreground">
            {{ title }}
        </h3>

        <p class="max-w-md text-base text-muted-foreground">
            {{ description }}
        </p>

        <div v-if="$slots.action" class="mt-6">
            <slot name="action" />
        </div>
    </div>
</template>

