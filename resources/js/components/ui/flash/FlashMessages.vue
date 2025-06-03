<template>
    <div class="fixed top-0 left-1/2 transform -translate-x-1/2 z-50">
        <flash-success :message="$page.props.flash.success" />
        <flash-error :errors="$page.props.errors" />
    </div>
</template>

<script setup lang="ts">
import FlashSuccess from './FlashSuccess.vue';
import FlashError from './FlashError.vue';
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
