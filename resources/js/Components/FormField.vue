<template>
  <div>
    <label v-if="label" class="block text-xs text-gray-500 mb-1">{{ label }}</label>

    <div>
      <slot></slot>
    </div>

    <p v-if="error" class="text-red-600 text-xs mt-1">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  error: String
})
</script>
