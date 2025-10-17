<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
  value: string;
  disabled?: boolean;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
});

const emit = defineEmits<{
  click: [value: string];
}>();

const handleClick = () => {
  if (!props.disabled) {
    emit('click', props.value);
  }
};
</script>

<template>
  <div
    :class="cn(
      'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
      props.class
    )"
    :data-disabled="disabled"
    @click="handleClick"
  >
    <slot />
  </div>
</template>
