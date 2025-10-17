<script setup lang="ts">
import { computed, inject, onMounted, ref } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
  class?: string;
  position?: 'item-aligned' | 'popper';
  side?: 'top' | 'right' | 'bottom' | 'left';
  sideOffset?: number;
  align?: 'start' | 'center' | 'end';
  alignOffset?: number;
  avoidCollisions?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  position: 'popper',
  side: 'bottom',
  sideOffset: 4,
  align: 'center',
  alignOffset: 0,
  avoidCollisions: true,
});

const selectContext = inject('select-context', null);
const contentRef = ref<HTMLDivElement>();

const positionClasses = computed(() => {
  if (props.position === 'item-aligned') {
    return '';
  }
  return 'w-[--radix-select-trigger-width] min-w-[--radix-select-trigger-width]';
});
</script>

<template>
  <div
    ref="contentRef"
    :class="cn(
      'relative z-50 max-h-96 min-w-[8rem] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
      positionClasses,
      props.class
    )"
  >
    <slot />
  </div>
</template>
