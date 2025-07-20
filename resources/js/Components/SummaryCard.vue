<template>
    <div class="bg-white p-4 rounded-lg shadow text-center">
      <p class="text-sm text-gray-500">{{ label }}</p>
      <p :class="['text-xl font-bold', color]">{{ value }}</p>
    </div>
  </template>
  
  <script setup>
  defineProps({
    label: String,
    value: String,
    color: String
  })
  </script>
  