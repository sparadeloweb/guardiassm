<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  placeholder?: string;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Seleccionar...',
});
</script>

<template>
  <span v-if="$slots.default" class="block truncate">
    <slot />
  </span>
  <span v-else class="block truncate text-muted-foreground">
    {{ placeholder }}
  </span>
</template>
