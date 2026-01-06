<template>
  <div
    @click="$emit('click')"
    class="cursor-pointer rounded-2xl p-5 shadow text-white relative overflow-hidden"
    :class="bg"
  >
    <Icon :icon="icon" class="text-3xl opacity-80" />
    <div class="mt-4 text-2xl font-bold">{{ value }}</div>
    <div class="text-sm opacity-90">{{ title }}</div>
    <div class="text-xs opacity-70 mt-1">{{ hint }}</div>

    <span
      v-if="pulse"
      class="absolute top-3 right-3 w-3 h-3 bg-white rounded-full animate-ping"
    />
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: String,
  hint: String,
  color: String,
  pulse: Boolean,
})

const bg = {
  blue: 'bg-gradient-to-br from-blue-500 to-blue-700',
  indigo: 'bg-gradient-to-br from-indigo-500 to-indigo-700',
  red: 'bg-gradient-to-br from-red-500 to-red-700',
  emerald: 'bg-gradient-to-br from-emerald-500 to-emerald-700',
}[props.color]
</script>
