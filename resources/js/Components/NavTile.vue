<template>
  <button
    class="w-full text-left px-4 py-3 border rounded-lg hover:bg-gray-50 font-medium"
  >
    {{ label }}
  </button>
</template>

<script setup>
defineProps({ label: String })
</script>
