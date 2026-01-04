<template>
  <transition name="slide-down">
    <div
      v-if="error"
      class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999]
             bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg
             flex items-start gap-3 max-w-xl w-full"
    >
      <div class="font-semibold">
        {{ error.title }}
      </div>

      <div class="flex-1 text-sm">
        {{ error.message }}
      </div>

      <button
        class="ml-2 text-white/80 hover:text-white"
        @click="clear"
      >
        ✕
      </button>
    </div>
  </transition>
</template>

<script setup>
import { useServerError } from '@/composables/useServerError'

const { error, clear } = useServerError()
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.25s ease;
}
.slide-down-enter-from {
  opacity: 0;
  transform: translate(-50%, -20px);
}
.slide-down-leave-to {
  opacity: 0;
  transform: translate(-50%, -20px);
}
</style>
