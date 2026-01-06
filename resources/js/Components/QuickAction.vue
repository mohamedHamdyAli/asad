<template>
  <button
    class="flex flex-col items-center justify-center border rounded-lg p-4 hover:bg-gray-50"
  >
    <Icon :icon="icon" class="text-2xl mb-2" />
    <span class="text-sm font-medium">{{ label }}</span>
  </button>
</template>

<script setup>
defineProps({
  label: String,
  icon: String,
})
</script>
