<template>
  <div class="relative">
    <svg class="w-8 h-8">
      <circle
        cx="16"
        cy="16"
        r="12"
        fill="transparent"
        stroke="#ffffff"
        stroke-width="2"
        stroke-opacity="0.3"
      />
      <circle
        cx="16"
        cy="16"
        r="12"
        fill="transparent"
        stroke="#ffffff"
        stroke-width="2"
        stroke-dasharray="75.4"
        :stroke-dashoffset="dashOffset"
        transform="rotate(-90 16 16)"
      />
    </svg>
    <button type="button" class="group absolute inset-0 flex items-center justify-center" @click="$emit('close')">
      <svg class="block w-2 h-2 fill-white group-hover:fill-gray-200" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  progress: number;
}

const props = defineProps<Props>();

const dashOffset = computed(() => {
  const circumference = 2 * Math.PI * 12;
  return circumference * (1 - props.progress);
});

defineEmits<{
  (e: 'close'): void;
}>();
</script>
