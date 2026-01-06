<template>
  <button
    @click="$emit('click')"
    class="group w-full h-24 rounded-xl border border-gray-200 bg-white
           flex flex-col items-center justify-center gap-2
           hover:bg-gray-50 hover:shadow transition"
  >
    <Icon
      :icon="icon"
      class="text-2xl text-gray-700 group-hover:text-black transition"
    />

    <span class="text-sm font-medium text-gray-700 group-hover:text-black">
      {{ label }}
    </span>
  </button>
</template>

<script setup>
import { Icon } from '@iconify/vue'

defineProps({
  icon: { type: String, required: true },
  label: { type: String, required: true },
})
</script>
