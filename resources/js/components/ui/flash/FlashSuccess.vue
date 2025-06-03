<template>
  <transition name="slide-down">
    <div v-if="message && show" class="mb-2 flex items-center justify-between bg-green-700 rounded max-w-lg shadow-lg">
      <div class="flex items-center">
        <svg class="ml-4 mr-2 flex-no-shrink w-4 h-4 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><polygon points="0 11 2 9 7 14 18 3 20 5 7 18" /></svg>
        <div class="py-4 text-white text-sm font-medium">{{ message }}</div>
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
  message?: string;
}

const props = withDefaults(defineProps<Props>(), {
  message: ''
});

const show = ref(false);
let timer: number | null = null;
const duration = 5000;
const startTime = ref(0);
const currentTime = ref(0);

const progress = computed(() => {
  const elapsed = currentTime.value - startTime.value;
  return Math.min(elapsed / duration, 1);
});

watch(() => props.message, (newVal) => {
  if (newVal) {
    show.value = true;
    startTimer();
  }
});

onMounted(() => {
  if (props.message) {
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
