<template>
  <transition name="slide-down">
    <div v-if="hasErrors && show" class="mb-2 flex items-center justify-between bg-red-700 rounded max-w-lg shadow-lg">
      <div class="flex items-center">
        <svg class="ml-4 mr-2 flex-no-shrink w-4 h-4 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z" /></svg>
        <div class="py-4 text-white text-sm font-medium">
          <span v-if="errorCount === 1">There is one form error.</span>
          <span v-else>There are {{ errorCount }} form errors.</span>
        </div>
      </div>
      <div class="mr-2 ml-2">
        <flash-circle-timer :progress="progress" @close="closeMessage" />
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import FlashCircleTimer from './FlashCircleTimer.vue';

interface Props {
  errors?: Record<string, any>;
}

const props = withDefaults(defineProps<Props>(), {
  errors: () => ({})
});

const show = ref(false);
let timer: number | null = null;
const duration = 5000;
const startTime = ref(0);
const currentTime = ref(0);

const hasErrors = computed(() => {
  return Object.keys(props.errors).length > 0;
});

const errorCount = computed(() => {
  return Object.keys(props.errors).length;
});

const progress = computed(() => {
  const elapsed = currentTime.value - startTime.value;
  return Math.min(elapsed / duration, 1);
});

watch(() => props.errors, (newVal) => {
  if (Object.keys(newVal).length > 0) {
    show.value = true;
    startTimer();
  }
}, { deep: true });

onMounted(() => {
  if (hasErrors.value) {
    show.value = true;
    startTimer();
  }
});

onBeforeUnmount(() => {
  clearTimer();
});

function startTimer() {
  clearTimer();
  startTime.value = Date.now();
  currentTime.value = startTime.value;

  timer = window.setInterval(() => {
    currentTime.value = Date.now();

    if (currentTime.value - startTime.value >= duration) {
      show.value = false;
      clearTimer();
    }
  }, 50);
}

function clearTimer() {
  if (timer !== null) {
    clearInterval(timer);
    timer = null;
  }
}

function closeMessage() {
  show.value = false;
  clearTimer();
}
</script>

<style scoped>
.slide-down-enter-active {
    transition: all 0.3s ease-out;
}
.slide-down-leave-active {
    transition: all 0.3s ease-in;
}
.slide-down-enter-from {
    transform: translateY(-20px);
    opacity: 0;
}
.slide-down-leave-to {
    transform: translateY(-20px);
    opacity: 0;
}
</style>
